<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');


session_start();
require("db.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false) {
  header("location: login.php");
}

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'])
{
  if($_SESSION['username'] == 'msadmin@iitp.ac.in')
  {
      header("location: ./msadmin/requestdetails.php");
      exit;
  }
}
$email = $_SESSION['username'];
$invaliderror = false;
$success = false;

if($_SERVER["REQUEST_METHOD"] == "POST")
{
  $email = $_SESSION['username'];
  $query = "SELECT * FROM request WHERE email = '$email' AND request.status = 'PENDING'";
  $reqdetails = mysqli_query($db,$query) or die(mysqli_error($db));

  $numrows = mysqli_num_rows($reqdetails);

  if($numrows == 0)
  {
    $invaliderror = true;
  }
  else
  {
      $row = mysqli_fetch_assoc($reqdetails);
      $reqid = $row['reqid'];
      $query = "DELETE FROM request WHERE reqid = $reqid";
      mysqli_query($db,$query);
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

  <link rel="stylesheet" href="style.css">
  <title>Market Shop</title>
</head>
<style>
  h3{
    text-align: center;
    color: blue;
  }
</style>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Market Shop</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item active">
        <a class="nav-link" href="welcome.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="request.php">Shop Req</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="extensionrequest.php">Ext Req</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="cancelextensionrequest.php">Cancel Ext Req</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="paymentdetails.php">Payment Details</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
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

  <h1 style="text-align: center;">IITP Shop Booking Portal</h1>

  <div class="card-group">
    <div class="card">
      <img src="room.jpg" class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Location: Food Court</h5>
        <p class="card-text">Area : 30ft x 40ft<br /> Number of Shops : 5</p>
      </div>
    </div>
    <div class="card">
      <img src="room.jpg" class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Location: Near Old Boys Hostel</h5>
        <p class="card-text">Area : 40ft x 40ft<br /> Number of Shops : 3</p>
      </div>
    </div>
    <div class="card">
      <img src="room.jpg" class="card-img-top" alt="...">
      <div class="card-body">
        <h5 class="card-title">Location: Near Gate No.2</h5>
        <p class="card-text">Area : 50ft x 40ft<br /> Number of Shops : 5</p>
      </div>
    </div>
  </div>
  <br>
  <br>

<div>
  <h3>Important Points:</h3>
  <ul>
    <li>Once a request is made, you will be contacted within 3 business days.</li>
    <li>You can have only 1 'PENDING' request at a time</li>
    <li>Once the payment is done, it is not refundable.</li>
    <li>Verify all the details properly.</li>
  </ul>
</div>
<br>
<div>
  <h3>Your Details:</h3>
  <?php
    $query = "SELECT * FROM market_shop.login WHERE login.email = '$email'";
    $yourdetail = mysqli_query($db,$query) or die(mysqli_error($db));
    $row1 = mysqli_fetch_assoc($yourdetail);

    $query = "SELECT * FROM shopkeepers WHERE email = '$email'";
    $shopkeeperdetail = mysqli_query($db,$query) or die(mysqli_error($db));
    $row = mysqli_fetch_assoc($shopkeeperdetail);
    
    echo '<table class="table">
            <thead>
              <tr>
                <th scope="col">Shopkeeper ID</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Mobile No</th>
                <th scope="col">Security Pass ID</th>
                <th scope="col">Security Pass Validity</th>
              </tr>
            </thead>
            <tbody>';

        if(isset($row['shopkeeperid']))
        {
          $shopkeeperid = $row['shopkeeperid'];
          $sid = $row['securitypassid'];

          $query = "SELECT max(enddate) as validity FROM contract WHERE shopkeeperid = $shopkeeperid AND enddate >= curdate() AND startdate <= curdate()";
          $res = mysqli_query($db,$query) or die(mysqli_error($db));
          $row2 = mysqli_fetch_assoc($res);
  
          if(!isset($row2['validity']))
          {
            $validity = 'NOT VALID';
          }
          else
          {
            $validity = $row2['validity'];
          }
        }
        else
        {
          $sid = 'NOT AVAILABLE';
          $shopkeeperid = 'NOT AVAILABLE';
          $validity = 'NOT VALID';
        }

        echo '<tr>';
        echo '<td>'.$shopkeeperid.'</td>';
        echo '<td>'.$row1['name'].'</td>';
        echo '<td>'.$row1['email'].'</td>';
        echo '<td>'.$row1['mobile'].'</td>';
        echo '<td>'.$sid.'</td>';
        echo '<td>'.$validity.'</td>';
        echo '</tr>';

      echo '</tbody>
      </table>';
  ?>
</div>

<br>
  <div style="font-size: 20px;">
    <?php
      $email = $_SESSION['username'];
      $query = "SELECT * FROM market_shop.contract NATURAL JOIN shopkeepers NATURAL JOIN market_shop.login WHERE email = '$email' AND enddate >= curdate()";
      $contracts = mysqli_query($db,$query) or die(mysqli_error($db));

      while($row = mysqli_fetch_assoc($contracts))
      {
        $enddate = $row['enddate'];

        $today = date('Y-m-d');
        $diff=date_diff(date_create($today),date_create($enddate));
        $numdays = $diff->format('%R%a');
        if($numdays >= 0 && $numdays <= 30)
        {
          $numdays = $diff->format('%a');
          echo '<span style = "color : red;">Reminder!!!</span> Day(s) remaining for expiry of License Period for contract ID : '.$contractid.' is/are '.$numdays.'<br>';
        }
      }
    ?>
  </div>
  <br>
<div>
  <h3>Request Status:</h3>
  <?php
    $query = "SELECT * FROM request WHERE email = '{$_SESSION["username"]}' AND request.status <> 'ACCEPTED'";
    $yourbookings = mysqli_query($db,$query) or die(mysqli_error($db));

    echo '<table class="table">
            <thead>
              <tr>
                <th scope="col">Request ID</th>
                <th scope="col">Request Date</th>
                <th scope="col">Start Date</th>
                <th scope="col">End Date</th>
                <th scope="col">Location</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>';

      while($row = mysqli_fetch_assoc($yourbookings))
      {
        echo '<tr>';
        echo '<td>'.$row['reqid'].'</td>';
        echo '<td>'.$row['reqdate'].'</td>';
        echo '<td>'.$row['startdate'].'</td>';
        echo '<td>'.$row['enddate'].'</td>';
        echo '<td>'.$row['location'].'</td>';
        echo '<td>'.$row['status'].'</td>';
        echo '</tr>';
      }
      echo '</tbody>
      </table>';
  ?>
  <form action="welcome.php" method="POST">
    <?php
      if ($invaliderror) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  You do not have any "PENDING" request.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
      if ($success) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  Request cancelled.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
      ?>
      <div class="subbutton">
      <button type="submit" class="btn btn-primary">Cancel Pending Request</button>
    </div>
  </form>
</div>

<br>
<div>
  <h3>Ongoing Contracts:</h3>
  <?php
    $query = "SELECT * FROM market_shop.contract NATURAL JOIN shopkeepers NATURAL JOIN shop WHERE email = '{$_SESSION["username"]}' AND enddate >= curdate()";
    $yourcontracts = mysqli_query($db,$query) or die(mysqli_error($db));

    echo '<table class="table">
            <thead>
              <tr>
                <th scope="col">Contract ID</th>
                <th scope="col">Contract Date</th>
                <th scope="col">Start Date</th>
                <th scope="col">End Date</th>
                <th scope="col">Shop ID</th>
                <th scope="col">Location</th>
                <th scope="col">Monthly Rent</th>
              </tr>
            </thead>
            <tbody>';

      while($row = mysqli_fetch_assoc($yourcontracts))
      {
        echo '<tr>';
        echo '<td>'.$row['contractid'].'</td>';
        echo '<td>'.$row['contractdate'].'</td>';
        echo '<td>'.$row['startdate'].'</td>';
        echo '<td>'.$row['enddate'].'</td>';
        echo '<td>'.$row['shopid'].'</td>';
        echo '<td>'.$row['location'].'</td>';
        echo '<td>'.$row['monthlyrent'].'</td>';
        echo '</tr>';
      }
      echo '</tbody>
      </table>';
  ?>
</div>
<br><br>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>