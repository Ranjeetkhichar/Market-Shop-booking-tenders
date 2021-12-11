<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();
require("./../db.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false) {
    header("location: ./../login.php");
}

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'])
{
    if($_SESSION['username'] != 'msadmin@iitp.ac.in')
    {
        header("location: ./../welcome.php");
        exit;
    }
}

$emptyerror = false;
$success = false;

if($_SERVER["REQUEST_METHOD"] == "POST")
{
  $contractid = $_POST["contractid"];
  $month = $_POST["month"];
  $electricitybillid = $_POST["electricitybillid"];
  $electricitybill = $_POST["electricitybill"];

  if(empty($contractid) || empty($month) || empty($electricitybill) || empty($electricitybillid))
  {
    $emptyerror = true;
  }
  else
  {
    $month = $month.'-01';
    $query = "UPDATE payment SET electricitybillid = '$electricitybillid' WHERE contractid = $contractid AND payment.month = '$month'";
    mysqli_query($db,$query) or die(mysqli_error($db));

    $query = "UPDATE payment SET electricitybill = $electricitybill WHERE contractid = $contractid AND payment.month = '$month'";
    mysqli_query($db,$query) or die(mysqli_error($db));

    $success = true;
  }
}
?>

<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

  <link rel="stylesheet" href="./../style.css">
  <title>Market Shop</title>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Market Shop</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
      <li class="nav-item active">
          <a class="nav-link" href="contractdetails.php">Home</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="requestdetails.php">Request Details</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="addbills.php">Add Bills</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="updatebills.php">Update Bills</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="extensionrequest.php">Extension Request</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="./../logout.php">Logout</a>
        </li>
      </ul>
      <div class="navbar-collapse collapse">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active">
            <a class="nav-link" href="#"> <img src="https://img.icons8.com/metro/26/000000/guest-male.png"> <?php echo "Welcome " . $_SESSION['username'] ?></a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <form action="addbills.php" method="POST">
    <?php
      if ($emptyerror) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  Enter all feilds!
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
      if ($success) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  Bill Added.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
      ?>
      <div>
        <label for="contractid">Contract ID</label>
        <input type="number" class="form-control" name="contractid" id="contractid">
      </div>
      <div>
        <label for="month">Month</label>
        <input type="month" class="form-control" name="month" id="month">
      </div>
      <div>
        <label for="electricitybillid">Electricity Bill ID</label>
        <input type="text" class="form-control" name="electricitybillid" id="electricitybillid">
      </div>
      <div>
        <label for="electricitybill">Electricity Bill</label>
        <input type="text" class="form-control" name="electricitybill" id="electricitybill">
      </div>
      <br>
      <div class="subbutton">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>