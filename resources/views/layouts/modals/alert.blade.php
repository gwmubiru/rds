<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    $(document).ready(function(){
        $("#myModal").modal('show');
    });
</script>
</head>
<body>
<div id="myModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
              <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
                <h2 class="modal-title; glyphicon glyphicon-alert" style="color:red"><b>NOTICE</b></h2>
            </div>
            <div class="modal-body">
                <p style="font-size:20px;">Effective today February 02, 2022, 9:00 PM, the CSV upload feature will be turned off for all Laboratories testing TRAVELERS.
                  <br><br>All results coming into RDS should be directly from your Laboratory Information Management Systems (LIMS).
                  <br><br>Travelers will be validated using RDS, as before.
                  <br><br>You will still be able login and download results for your clients.

                </p>

                <!-- <form>
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Name">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="Password">
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" placeholder="Email Address">
                    </div>

                    <button type="submit" class="btn btn-primary">Check my email</button>
                    <button type="submit" class="btn btn-success">Update Email</button>
                </form> -->
            </div>
        </div>
    </div>
</div>
</body>
</html>
