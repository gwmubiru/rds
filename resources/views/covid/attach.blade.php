<div class="form-group">

<label for="heading">Full Name</label>

<input class="form-control" value='{{$value->patient_surname}}' name="patient_surname" id = "patient_surname" required>

<br>

<br>

<label for="heading">Sex</label>

<input class="form-control" value='{{$value->sex}}' name="sex" id = "sex" required>

<br>

<br>

<label for="heading">Age</label>

<input class="form-control" value='{{$value->age}}' name="age" id ="age" required>

<br>

<br>

<label for="heading">Nationality</label>

<input class="form-control" value='{{$value->nationality}}' name="nationality" id ="nationality" required>

<br>

<br>

<label for="heading">Sample ULIN</label>

	{!! Form::text('specimen_ulin', old('specimen_ulin'), array('class' => 'form-control', 'rows' => '2', 'required')) !!}

	<br>

	<br>

<label for="heading">Sample Type</label>

<textarea class="form-control comment" value='' name="comment" required></textarea>

<br>

<!-- <br>

{!! Form::label('start_date', 'Dispatch Date:') !!}

{!! Form::text('dispatch_date', old('dispatch_date'),array('class' => 'form-control standard-datepicker-nofuture','required'=>'required','id'=>'start_date')) !!}

<br> -->

</div>



<script>



$('#results').on('show.bs.modal', function (event) {



    var button = $(event.relatedTarget)



    var pname = button.data('patient_surname')

    var s = button.data('sex')

    var a = button.data('age')

    var n = button.data('nationality')

		var dis_id = button.data('did')



    var modal = $(this)



    modal.find('.modal-body #patient_surname').val(pname);

    modal.find('.modal-body #sex').val(s);

    modal.find('.modal-body #age').val(a);

    modal.find('.modal-body #nationality').val(n);

		modal.find('.modal-body #dis_id').val(dis_id);

})

</script>


