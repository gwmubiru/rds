<div id="the_modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" style="color:red">Worksheet Details</h4>
      </div>
      
        {!! Form::open(array('url'=>'/new/worksheet','id'=>'worksheet_detail', 'name'=>'worksheet_d','class'=>'form-horizontal')) !!}
      <div class="modal-body">
        <div class="form-group">
          {!! Form::label('machine_type', 'Machine Type:', array('class' =>'col-md-4 ')) !!}
          <div class="col-md-6">
            {!! Form::select("machine_type",[""=>"Select One"]+MyHTML::machineTypes(),'',['class'=>'form-control', 'id'=>'machine_type'])  !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('pool_type', 'Pool Type:', array('class' =>'col-md-4 ')) !!}
          <div class="col-md-6">
            {!! Form::select("pool_type",[""=>"Select One"]+MyHTML::worrksheetTemplates(),'',['class'=>'form-control', 'id'=>'pool_type'])  !!}
          </div>
        </div>
        <div class="form-group">
          {!! Form::label('number_of_tubes', 'Number of Tubes:', array('class' =>'col-md-4 ')) !!}
          <div class="col-md-6">
            {!! Form::text('number_of_tubes', 40, array('class' => 'form-control text-line')) !!}
          </div>
        </div>

        
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Submit</button>
        <button type="button" class="btn btn-danger"  data-dismiss="modal">Cancel</button>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
