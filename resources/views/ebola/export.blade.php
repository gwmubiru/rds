@extends('layouts/layout')

@section('content')
<div class="panel-body">
    <br><br>
    <div class="container" style="margin-top:80px;">
        <h2 align="center">Export Results</h2>
        <div class="row">
             <div class='col-lg-12'>@if(App\Closet\MyHTML::is_evd_user() || App\Closet\MyHTML::is_classified_user() || App\Closet\MyHTML::isSpecialUser())
                   {!! Form::open(array('url'=>'/evd/export_data','id'=>'export_to_csv')) !!}
                   <div class="well firstrow list">

                      <div class="row">
                        <div class="col-md-12">
                         <div class="form-inline" style="margin-top: 10px; margin-bottom: 10px;">
                              <label for="exp_fro">Tested between:</label>
                              <input style="margin-left: 17px;" class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="exp_fro" type="text" id="exp_fro">
                              <label for="exp_to">and </label>
                              <input class="form-control input-sm standard-datepicker standard-datepicker-nofuture" name="exp_to" type="text" id="exp_to">


                              <input type="submit" value="Export to CSV" class="btn btn-primary btn-sm" style="margin-top: 5px;">

                          </div>
                          </div>
                      </div>
                    </div>

                    {!! Form::close() !!}
                @endif
             </div>
          </div>
        </div>
    </div>
