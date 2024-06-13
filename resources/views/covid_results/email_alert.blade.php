<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  margin-top: 0.1cm;
}


</style>




<!DOCTYPE html>
<html>
<style>
table th, td {



  font-family: arial, sans-serif;
  width: Auto;
  font-size: 13.0px;

  border: 1.0px solid gray;
  text-align: left;
  padding: 2px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}

  @media print {
  body { margin-top: 2.01cm; }
  body { margin-right: 1.01cm;}
  body { margin-bottom:4.6cm; }
}
}
</style>
<body>

  <table class="table" >
    <thead>
      <th>#</th>
      <th>Lab ID</th>
      <th>Barcode</th>
      <th>Full Names</th>
      <th>Sex</th>
      <th>Age</th>
      <th>Mob Contact</th>
      <th>Sentinel Site</th>
      <th>Swabing District</th>
      <th>Result</th>
      <th>Test date</th>
      <th>Testing Laboratory</th>
    </tr>
  </thead>

  <tbody class="tbody">
    <?php $row=1;
    $get_positives = "select case_name,result,sentinel_site,district,date_of_collection, age_years,sex,case_contact,result,test_date,ref_lab_name,case_id,serial_number_batch,patient_id
                      from results where email_sent = 0 AND result = 'POSITIVE' AND is_classified = 0  AND sentinel_site NOT LIKE '%cabinet%' AND sentinel_site NOT LIKE '%Cabinet%' LIMIT 10";

    $data = \DB::select($get_positives);
    ?>
    @foreach($data as $value)
    <tr>
      <td>{!! $row !!}</td>

      @if($value->case_id == "")
      <td>{!! $value->patient_id !!}</td>
      @else
      <td>{!! $value->case_id !!}</td>
      @endif

      <td>{!! $value->serial_number_batch !!}</td>
      <td>{!! $value->case_name !!}</td>
      <td>{!! $value->sex !!}</td>
      <td>{!! $value->age_years !!}</td>
      <td>{!! $value->case_contact !!}</td>
      <td>{!! $value->sentinel_site !!}</td>
      <td>{!! $value->district !!}</td>
      <td>{!! $value->result !!}</td>
      <td>{!! $value->test_date !!}</td>
      <td>{!! $value->ref_lab_name !!}</td>
    </tr>
    <?php $row++; ?>
    @endforeach
  </tbody>
  </table>
</body>
</html>
