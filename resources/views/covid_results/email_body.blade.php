<!DOCTYPE html>
<html>
<head>
<style>
table {
  border-collapse: collapse;
  width: 25%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
</head>
<body>
  <p>Summary of SARS Covid19 tests performed on <?php echo date(strtotime,"y-m-d");?></p>
  <br>

<table>
  <tr>
    <td width="20%"><b>Total Tested</b></td>

  </tr>
  <?php
  $total_query = "SELECT count(id) as `total` from results WHERE date(test_date) = date (NOW())";

  $total = \DB::select($total_query);

  foreach ($total as $key => $value): ?>

  <?php endforeach; ?>
  <tr>
    <td class="print-vald" width="15%">{{$value->total}}</td>

  </tr>
  </table>

  <table >

  <tr>
    <td><b>Summary of Results</b></td>
    <td><b>Result</b></td>

  </tr>

  <?php
  $grouped_result_query = "SELECT result, count(id) as summary from results WHERE date(test_date) = date (NOW())  group by result";
  $grouped_result = \DB::select($grouped_result_query);

  foreach ($grouped_result as $value): ?>

  <?php endforeach; ?>
  <tr>
    @if (!empty($value->summary))
    <td>{{$value->summary}}</td>
    @endif
    @if (!empty($value->summary))
    <td>{{$value->result}}</td>
    @endif
  </tr>
</table>

<p style="color:#337ab7"><i>Replies to this message are routed to an unmonitored mailbox. <br>
If you have any questions or inquiries, please contact MOH toll free 0800-203-033
</i></p>
<br>
<i>Thank you</i>


</body>
</html>
