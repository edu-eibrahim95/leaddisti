@extends('voyager::master')

@section('page_title', "Reports")
@section('css')
    <link href="https://cdn.datatables.net/buttons/1.5.6/css/buttons.dataTables.min.css" rel="stylesheet">
    <style>
        .lead-table td{
            border-right: 1px solid gray;
        }
    </style>
@endsection
@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-receipt"></i> Reports
        </h1>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">

                        <div class="table-responsive">
                            <table class="table table-hover lead-table">
                                <tbody>
                                <tr>
                                    <th><b style="font-weight: bold">Lead Ref :</b></th><td><b style="font-weight: bold">{{$lead->refernce}}</b></td>
                                    <th><b style="font-weight: bold">Created On :</b></th><td><b style="font-weight: bold">{{$lead->created_at}}</b></td>
                                    <th><b style="font-weight: bold">Last Updated On :</b></th><td><b style="font-weight: bold">{{$lead->updated_at}}</b></td>
                                </tr>
                                <tr>
                                    <th>Project : </th><td>{{$lead->project}}</td>
                                    <th>Location : </th><td>{{$lead->location}}</td>
                                    <th>Sector : </th><td>{{$lead->sector}}</td>
                                </tr>
                                <tr>
                                    <th>Size : </th><td>{{$lead->size}}</td>
                                    <th>Budget : </th><td>{{$lead->budget}}</td>
                                    <th>Time Scales : </th><td>{{$lead->time_scales}}</td>
                                </tr>
                                <tr>
                                    <th>Pdf File : </th><td>
                                        @foreach(json_decode($lead->pdf_file) as $file)
                                            <a href="{{ Storage::disk(config('voyager.storage.disk'))->url($file->download_link) ?: '' }}" target="_blank">
                                                {{ $file->original_name ?: '' }}
                                            </a>
                                            <br/>
                                        @endforeach
                                    </td>
                                    <th>Pdf Url : </th><td>{{$lead->pdf_url}}</td>
                                    <th>Accepting Till : </th><td>{{$lead->accepting_till}}</td>
                                </tr>
                                <tr>
                                    <th>Company Name : </th><td>{{$lead->company_name}}</td>
                                    <th>Full Name : </th><td>{{$lead->full_name}}</td>
                                    <th>Phone Number : </th><td>{{$lead->phone_number}}</td>
                                </tr>
                                <tr>
                                    <th>Email : </th><td>{{$lead->email}}</td>
                                    <th>Address Line : </th><td>{{$lead->address_line}}</td>
                                    <th>Address Line 2 : </th><td>{{$lead->address_line2}}</td>
                                </tr>
                                <tr>
                                    <th>Postal Code : </th><td>{{$lead->postal_code}}</td>
                                    <th>City : </th><td>{{$lead->city}}</td>
                                    <th>region : </th><td>@if($lead->region){{$lead->region->name}}@endif</td>
                                </tr>
                                <tr>
                                    <th>Specialisms: </th><td>@foreach($lead->specialisms as $specialism) {{ $specialism->name }},  @endforeach</td>
                                    <th>Turnovers: </th><td>@foreach($lead->turnovers as $turnover) {{$turnover->name}},  @endforeach</td>
                                    <th>Employee Bands: </th><td>@foreach($lead->employee_bands as $employee_band) {{ $employee_band->name }} @endforeach</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <div class="table-responsive">
                            <table id="dataTable" class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Account Manager</th>
                                    <th>No. of Matched Partners</th>
                                    <th>Matched Partners</th>
                                    <th>EMail Group</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($result as $account_manager)
                                    <tr>
                                        <td>{{$account_manager->get('name')}}</td>
                                        <td>{{$account_manager->get('count')}}</td>
                                        <td>
                                            @foreach($account_manager->get('data') as $partner)
                                                {{$partner->name}},
                                            @endforeach
                                        </td>
                                        <td>
                                            <form method="POST" action="{{route('lead_report_email', ['lead_id'=>$lead->id])}}" data-content="{{$account_manager->get('name')}}">
                                                {!! @csrf_field() !!}
                                                <input name="account_manager" type="hidden" value="{{$account_manager->get('name')}}">
                                                <select class="form-control select2" name="email_group_id">
                                                    @foreach($email_groups as $email_group)
                                                        <option value="{{$email_group->id}}">{{$email_group->name}}</option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="#" title="Report" class="btn btn-sm btn-primary pull-right edit"
                                               onclick="$('form[data-content=\'{{$account_manager->get("name")}}\']').submit();">
                                                <i class="voyager-mail"></i> <span class="hidden-xs hidden-sm">Email</span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <thead>
                                <tr>
                                    <th>Overall</th>
                                    <th>{{$overall}}</th>
                                    <th>Real Overall is <red style="color:red">({{$real_overall}})</red> which means there are <red style="color:red">({{$real_overall - $overall}})</red> partners matched that don't have account manager.</th>
                                    <th>
                                        <red style="color:red">choose email groups first ^</red>
                                        <form method="POST" action="{{route('lead_report_email', ['lead_id'=>$lead->id])}}" id="send-all">
                                            {!! @csrf_field() !!}
                                            <input name="account_manager" type="hidden" value="all">
                                            @foreach($result as $account_manager)
                                                <input type="hidden" name="email_group_ids[{{$account_manager->get('name')}}]" value="-1" class="all-email-group-id" data-content="{{$account_manager->get('name')}}">
                                            @endforeach
                                        </form>
                                    </th>
                                    <th>
                                        <a href="#" title="Report" class="btn btn-sm btn-dark pull-right edit" onclick="$('#send-all').submit()">
                                            <i class="voyager-mail"></i> <span class="hidden-xs hidden-sm">Email All</span>
                                        </a>
                                    </th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="pull-left">

                        </div>
                        <div class="text-center">

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('javascript')
    <script>
        function updateValues(){
            $('.all-email-group-id').each(function () {
                    let account_manager = $(this).attr('data-content');
                    let value = $('form[data-content="'+account_manager+'"]').find("select[name=email_group_id]").val();
                    $(this).val(value);
                }
            )
        }
        $(document).ready(function() {
            updateValues();
            $("select[name=email_group_id]").on('change', function () {updateValues();});
        });
    </script>
@endsection
