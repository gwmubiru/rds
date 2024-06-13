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
<b style="font-size:30px">{{$token}}</b>
<p style="font-size:15px; color:gray">Token expires in 2hours ({{$expiryTime}})</p>
<p style="font-family:'Courier New';color:red;font-size:15px"><i>Replies to this message are routed to an unmonitored mailbox. <br>
If you have any questions or inquiries, please contact CPHL/MOH toll free 0800-221-100
</i></p>
<br>
</body>
</html>
