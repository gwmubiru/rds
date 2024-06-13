@extends('layouts/layout')
@section('content')

<div class="panel panel-default">
    <div class="panel-heading">RESULTS PRINTING MONITORING</div>
    <div class="panel-body">
        <ul id="tabs_a" class="nav nav-tabs">
            <li @if($tab=='h') class='active' @endif >
                <a href="?tab=h">Hub Accounts</a>
            </li>
            <li @if($tab=='f') class='active' @endif >
                <a href="?tab=f">Facilities Accounts</a>
            </li>
        </ul>
        <div class="tab-content">
            <table id="results-table" class="table table-condensed table-bordered  tab-pane active">
                <thead>
                    <tr>
                        <th><?=$tab=='f'?'Facility':'Hub' ?></th> 
                        <th>IP</th>
                        <th>Total Pending</th> 
                        <th>Oldest Pending (days)</th>
                        <th>Last printed</th>
                    </tr>
                </thead>
                @if($tab=='f') 
                    <tbody>
                        @foreach($facilities AS $f)
                         <tr>
                            <td class="hover" title="{{ $f->contactPerson }} - {{ $f->phone  }}">{{ $f->facility }}</td>
                            <td class="hover" title="{{ $f->focal_person }} - {{ $f->focal_person_contact }}">{{ $f->ip }}</td>
                            <td>{{ $f->pending }}</td>
                            <td>{{ \MyHTML::dateDiff(date('Y-m-d'), $f->oldest_pending) }}</td>
                            <td>{{ $f->last_dispatch_date }}</td>
                        </tr>
                        @endforeach
                    </tbody>       
                   
                @else
                    <tbody>
                        @foreach($hubs AS $hub)
                         <tr>
                            <td class="hover" title="{{ $hub->coordinator }} - {{ $hub->coordinator_contact  }}">{{ $hub->hub }}</td>
                            <td class="hover" title="{{ $hub->focal_person }} - {{ $hub->focal_person_contact }}">{{ $hub->ip }}</td>
                            <td>{{ $hub->pending }}</td>
                            <td>{{ \MyHTML::dateDiff(date('Y-m-d'), $hub->oldest_pending) }}</td>
                            <td>{{ $hub->last_printed }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    
                @endif
            </table>
        </div>
    </div>
</div>

<style type="text/css">
    .hover{
        cursor: pointer;
    }

    .tab-content {
        border-left: 1px solid #ddd;
        border-right: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
        padding: 10px;
    }

    .nav-tabs {
        margin-bottom: 0;
    }

</style>
<script type="text/javascript">
$('#l6').addClass('active');
$(function() { $('#results-table').DataTable({"order": [[ 3, "desc" ]],}); });
</script>
@endsection