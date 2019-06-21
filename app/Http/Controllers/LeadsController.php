<?php

namespace App\Http\Controllers;

use App\EmailQueue;
use App\Lead;
use App\Partner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use TCG\Voyager\Events\BreadDataAdded;
use TCG\Voyager\Events\BreadDataUpdated;
use TCG\Voyager\Facades\Voyager;
use TCG\Voyager\Http\Controllers\VoyagerBaseController;

class LeadsController extends VoyagerBaseController
{
    public function store(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->addRows)->validate();
        $data = $this->insertUpdateData($request, $slug, $dataType->addRows, new $dataType->model_name());

        event(new BreadDataAdded($dataType, $data));

        $this->processData($data);
        return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => __('voyager::generic.successfully_added_new')." {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }
    // POST BR(E)AD
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses($model))) {
            $data = $model->withTrashed()->findOrFail($id);
        } else {
            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
        }

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();
        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        event(new BreadDataUpdated($dataType, $data));
        $this->processData($data);

        return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => __('voyager::generic.successfully_updated')." {$dataType->display_name_singular}",
                'alert-type' => 'success',
            ]);
    }

    public function processData($data){
        EmailQueue::where(['lead_id'=>$data->id])->delete();
        $region = $data->region()->first();
        $turnovers = $data->turnovers()->get();
        $specialisms = $data->specialisms()->get();
        $employee_bands = $data->employee_bands()->get();
        $partners =  Partner::with('turnovers', 'specialisms', 'employee_bands', 'regions');
        if ($region){
            $partners = $partners->whereHas('regions', function($query) use ($region) {
                $query->where('categories.id', '=', $region->id);
            });
        }
        $partners = $partners->where(function($query) use($turnovers, $specialisms, $employee_bands){
            $query->whereHas('turnovers', function($query) use ($turnovers) {
                $query->whereIn('categories.id', $turnovers->pluck('id'));
            })->orWhereHas('specialisms', function($query) use ($specialisms) {
                $query->whereIn('categories.id', $specialisms->pluck('id'));
            })->orWhereHas('employee_bands', function($query) use ($employee_bands) {
                $query->whereIn('categories.id', $employee_bands->pluck('id'));
            });
        });
        $partners = $partners->get();
        foreach ($partners as $partner){
            EmailQueue::create(['lead_id'=>$data->id, 'partner_id'=>$partner->id]);
        }
    }

}
