<b>Hi All</b>
<p>Hope you are well</p>
<p>Today we have engaged with the following project which we feel is a good fit for your business, please see summary below and PDF download for full project details.</p>
<b>Software Advisory will be accepting requests for the lead below until <u>{{$lead->accepting_till}}</u></b>
<br>
<br>
<table style="border:1px solid black;padding:0; margin: 0;border-collapse: collapse">
    <tr style="padding:0; margin: 0">
        <th style="border: 1px solid black; background: #ffc000; padding: 5px 10px; margin: 5px 10px">  Lead Ref</th>
        <td style="border: 1px solid black; padding: 5px 10px">{{$lead->id}}</td>
    </tr>
    <tr style="padding:0; margin: 0">
        <th style="border: 1px solid black; background: #ffc000; padding: 5px 10px; margin: 5px 10px">  Project</th>
        <td style="border: 1px solid black; padding: 5px 10px">{{$lead->project}}</td>
    </tr>
    <tr style="padding:0; margin: 0">
        <th style="border: 1px solid black; background: #ffc000; padding: 5px 10px; margin: 5px 10px">  Region</th>
        <td style="border: 1px solid black; padding: 5px 10px">{{$lead->region->name}}</td>
    </tr>
    <tr style="padding:0; margin: 0">
        <th style="border: 1px solid black; background: #ffc000; padding: 5px 10px; margin: 5px 10px">  Sector</th>
        <td style="border: 1px solid black; padding: 5px 10px">{{$lead->sector}}</td>
    </tr>
    <tr style="padding:0; margin: 0">
        <th style="border: 1px solid black; background: #ffc000; padding: 5px 10px; margin: 5px 10px">  Size</th>
        <td style="border: 1px solid black; padding: 5px 10px">{{$lead->size}}</td>
    </tr>
    <tr style="padding:0; margin: 0">
        <th style="border: 1px solid black; background: #ffc000; padding: 5px 10px; margin: 5px 10px">  Budget</th>
        <td style="border: 1px solid black; padding: 5px 10px">{{$lead->budget}}</td>
    </tr>
    <tr style="padding:0; margin: 0">
        <th style="border: 1px solid black; background: #ffc000; padding: 5px 10px; margin: 5px 10px">  Time Scales</th>
        <td style="border: 1px solid black; padding: 5px 10px">{{$lead->time_scales}}</td>
    </tr>
    <tr style="padding:0; margin: 0">
        <th style="border: 1px solid black; background: #ffc000; padding: 5px 10px; margin: 5px 10px">  PDF file</th>
        <td style="border: 1px solid black; padding: 5px 10px">
            @if(! empty($lead->pdf_url))
                <a href="{{$lead->pdf_url}}">{{$lead->refence}}</a>
            @endif
            @if(! empty($lead->pdf_file))
                @if($lead->pdf_url)
                    Or
                @endif
                <a href="{{Storage::disk(config('voyager.storage.disk'))->url(json_decode($lead->pdf_file)[0]->download_link)}}">{{$lead->refence}}</a>
            @endif
        </td>
    </tr>

</table>
<p>Can you review and let us know if you are keen to engage with the prospect ?</p>
<p><b>Kind Regards,</b></p>
<p><b>Niamh McKenna | Software Advisory Service</b></p>
<p><img src="{{url('public/logo.jpg')}}"></p>
<p><i>&#9742; </i>020 3640 8095 <i>&#9755; </i>Niamh.McKenna@softwareadvisoryservice.com</p>
<p><i>&#x261D; </i><a href="www.softwareadvisoryservice.com">www.softwareadvisoryservice.com</a></p>