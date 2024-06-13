@extends('layouts/layout')

@section('content')

<h1>Batches to be Released from Lab</h1>


@if( true || Request::has('q') )
    <?php



            function show_approval_status($this_sample)
            {
                if($this_sample->sample_rejected == 'NOT_YET_CHECKED')
                    return 'NOT_YET_CHECKED';

                if($this_sample->sample_rejected == 'YES')
                    return 'NO';
                else
                    return 'YES';
            }

            function show_worksheet_number($this_sample)
            {
                if($this_sample->worksheet_number == null) 
                    return "NOT in Worksheet";
                else
                    return $this_sample->worksheet_number;
            }

            function show_EID_test($this_sample)
            {
                if($this_sample->PCR_test_requested == "YES")
                    return "YES";
                else
                    return "<b style='color:red; background-color:yellow'>NO</b>";
            }

            function show_rejects($data)
            {
                if($data->rejected)
                    return "(Rejected = " . $data->rejected . ")";
                else
                    return ""; 
            }

            function show_turnAroundTime($data)
            {
                $datetime1 = date_create('now');
                $datetime2 = date_create($data->date_rcvd_by_cphl);
                $interval = date_diff($datetime1, $datetime2);
                
                return $interval->format('%a days ago');
            }


    ?>
    <?php   $sample = Request::get('q'); 

            $sql = "select  count(case accepted_result  when 'POSITIVE' then 1 else null end)  as nPositives, 
                            count(case accepted_result  when 'NEGATIVE' then 1 else null end)  as nNegatives, 
                            count(case accepted_result  when 'INVALID' then 1 else null end)  as nInvalids, 
                            count(case sample_rejected when 'YES' then 1 else null end)  as rejected, 
                            batches.id, batch_number, count(dbs_samples.id) as nSamples, facility_id, 
                            facilities.facility, hubs.hub AS hubname ,
                            date_rcvd_by_cphl

                        FROM    batches, dbs_samples, facilities, hubs 
                        WHERE   batches.all_samples_tested = 'YES' 
                        AND     batches.id = dbs_samples.batch_id 
                        AND     batches.facility_id = facilities.id 
                        AND     facilities.hubID = hubs.id 

                        GROUP BY    batch_number 
                        ORDER BY    hubname, facility, date_dispatched_from_facility DESC
                        LIMIT 0, 1000 ";
                     

            $rows = DB::select($sql);
            $nResults = count($rows);

    ?>

    <table border="1">
    <style type="text/css">
        td {
            padding: 5px;
            text-align: center;
        }
    </style>

    @if($nResults == 0)
        <h5 style="color:red">That sample ({{ $sample }}) was not found</h5>
    @else
        <tr>
            <th>Hub</th>
            <th>Facility</th>
            <th>Batch Number</th>
            <th>Samples</th>            
            <th>Positives</th>
            <th>Negatives</th>
            <th>Invalids</th>
            <th>Received at CPHL</th>
        </tr>

    @endif

    @foreach($rows as $this_row)
        <tr>
            <td>{{ $this_row->hubname}}</td>
            <td>{{ $this_row->facility}}</td>
            <td>{{ $this_row->batch_number}}</td>
            <td>{{ $this_row->nSamples}}</td>
            <td>{{ $this_row->nPositives }}</td>
            <td>{{ $this_row->nNegatives }}</td>
            <td>{{ $this_row->nInvalids }} {{ show_rejects($this_row) }}</td>
            <td>{{ show_turnAroundTime($this_row) }}</td>
        </tr>
    @endforeach
    </table>

@endif

@stop