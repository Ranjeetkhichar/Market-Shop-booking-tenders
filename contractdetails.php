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

<style>
  h3{
    text-align: center;
    color: blue;
  }
  h4{
    color: orangered;
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

  <br>
  <div>
  <h3>Ongoing Contracts:</h3>
  <?php
    $query = "SELECT * FROM market_shop.contract WHERE enddate >= curdate()";
    $contracts = mysqli_query($db,$query) or die(mysqli_error($db));

    echo '<table class="table">
            <thead>
              <tr>
                <th scope="col">Contract ID</th>
                <th scope="col">Contract Date</th>
                <th scope="col">Start Date</th>
                <th scope="col">End Date</th>
                <th scope="col">Shop ID</th>
                <th scope="col">Shopkeeper ID</th>
                <th scope="col">Monthly Rent</th>
              </tr>
            </thead>
            <tbody>';

      while($row = mysqli_fetch_assoc($contracts))
      {
        echo '<tr>';
        echo '<td>'.$row['contractid'].'</td>';
        echo '<td>'.$row['contractdate'].'</td>';
        echo '<td>'.$row['startdate'].'</td>';
        echo '<td>'.$row['enddate'].'</td>';
        echo '<td>'.$row['shopid'].'</td>';
        echo '<td>'.$row['shopkeeperid'].'</td>';
        echo '<td>RS. '.$row['monthlyrent'].'</td>';
        echo '</tr>';
      }
      echo '</tbody>
      </table>';
  ?>
</div>
  <div>
  <h3>Ended Contracts:</h3>
  <?php
    $query = "SELECT * FROM market_shop.contract WHERE enddate < curdate()";
    $contracts = mysqli_query($db,$query) or die(mysqli_error($db));

    echo '<table class="table">
            <thead>
              <tr>
                <th scope="col">Contract ID</th>
                <th scope="col">Contract Date</th>
                <th scope="col">Start Date</th>
                <th scope="col">End Date</th>
                <th scope="col">Shop ID</th>
                <th scope="col">Shopkeeper ID</th>
                <th scope="col">Monthly Rent</th>
              </tr>
            </thead>
            <tbody>';

      while($row = mysqli_fetch_assoc($contracts))
      {
        echo '<tr>';
        echo '<td>'.$row['contractid'].'</td>';
        echo '<td>'.$row['contractdate'].'</td>';
        echo '<td>'.$row['startdate'].'</td>';
        echo '<td>'.$row['enddate'].'</td>';
        echo '<td>'.$row['shopid'].'</td>';
        echo '<td>'.$row['shopkeeperid'].'</td>';
        echo '<td>RS. '.$row['monthlyrent'].'</td>';
        echo '</tr>';
      }
      echo '</tbody>
      </table>';
  ?>
</div>
<br><br>
  <div>
  <h3>Shopkeeper Details:</h3>
  <?php
    $query = "SELECT * FROM shopkeepers NATURAL JOIN market_shop.login";
    $shopkeeperdetail = mysqli_query($db,$query) or die(mysqli_error($db));
    
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

      while($row = mysqli_fetch_assoc($shopkeeperdetail))
      {
        $shopkeeperid = $row['shopkeeperid'];
        $query = "SELECT max(enddate) as validity FROM contract WHERE shopkeeperid = $shopkeeperid AND enddate >= curdate() AND startdate <= curdate()";
        $res = mysqli_query($db,$query) or die(mysqli_error($db));
        $row1 = mysqli_fetch_assoc($res);

        if(!isset($row1['validity']))
        {
          $validity = 'NOT VALID';
        }
        else
        {
          $validity = $row1['validity'];
        }

        echo '<tr>';
        echo '<td>'.$row['shopkeeperid'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['email'].'</td>';
        echo '<td>'.$row['mobile'].'</td>';
        echo '<td>'.$row['securitypassid'].'</td>';
        echo '<td>'.$validity.'</td>';
        echo '</tr>';
      }
      echo '</tbody>
      </table>';
  ?>
</div>
<br><br>
<div>
  <h3>Shop Details:</h3>
  <h4>Food Court:</h4>
  <?php
    $query = "SELECT * FROM market_shop.contract NATURAL JOIN shopkeepers NATURAL JOIN shop NATURAL JOIN login WHERE shop.location = 'Food Court' AND startdate <= curdate() AND enddate >= curdate()";
    $contracts = mysqli_query($db,$query) or die(mysqli_error($db));

    echo '<table class="table">
            <thead>
              <tr>
                <th scope="col">Shop ID</th>
                <th scope="col">Location</th>
                <th scope="col">Shopkeeper ID</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Mobile No</th>
                <th scope="col">Security Pass ID</th>
                <th scope="col">Monthly Rent</th>
              </tr>
            </thead>
            <tbody>';

      while($row = mysqli_fetch_assoc($contracts))
      {
        echo '<tr>';
        echo '<td>'.$row['shopid'].'</td>';
        echo '<td>'.$row['location'].'</td>';
        echo '<td>'.$row['shopkeeperid'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['email'].'</td>';
        echo '<td>'.$row['mobile'].'</td>';
        echo '<td>'.$row['securitypassid'].'</td>';
        echo '<td>RS. '.$row['monthlyrent'].'</td>';
        echo '</tr>';
      }
      echo '</tbody>
      </table>';
  ?>
</div>
<br>
<div>
  <h4>Near Old Boys Hostel:</h4>
  <?php
    $query = "SELECT * FROM market_shop.contract NATURAL JOIN shopkeepers NATURAL JOIN shop NATURAL JOIN login WHERE shop.location = 'Near Old Boys Hostel' AND startdate <= curdate() AND enddate >= curdate()";
    $contracts = mysqli_query($db,$query) or die(mysqli_error($db));

    echo '<table class="table">
            <thead>
              <tr>
                <th scope="col">Shop ID</th>
                <th scope="col">Location</th>
                <th scope="col">Shopkeeper ID</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Mobile No</th>
                <th scope="col">Security Pass ID</th>
                <th scope="col">Monthly Rent</th>
              </tr>
            </thead>
            <tbody>';

      while($row = mysqli_fetch_assoc($contracts))
      {
        echo '<tr>';
        echo '<td>'.$row['shopid'].'</td>';
        echo '<td>'.$row['location'].'</td>';
        echo '<td>'.$row['shopkeeperid'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['email'].'</td>';
        echo '<td>'.$row['mobile'].'</td>';
        echo '<td>'.$row['securitypassid'].'</td>';
        echo '<td>RS. '.$row['monthlyrent'].'</td>';
        echo '</tr>';
      }
      echo '</tbody>
      </table>';
  ?>
</div>
<br>
<div>
  <h4>Near Gate No.2:</h4>
  <?php
    $query = "SELECT * FROM market_shop.contract NATURAL JOIN shopkeepers NATURAL JOIN shop NATURAL JOIN login WHERE shop.location = 'Near Gate No.2' AND startdate <= curdate() AND enddate >= curdate()";
    $contracts = mysqli_query($db,$query) or die(mysqli_error($db));

    echo '<table class="table">
            <thead>
              <tr>
                <th scope="col">Shop ID</th>
                <th scope="col">Location</th>
                <th scope="col">Shopkeeper ID</th>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Mobile No</th>
                <th scope="col">Security Pass ID</th>
                <th scope="col">Monthly Rent</th>
              </tr>
            </thead>
            <tbody>';

      while($row = mysqli_fetch_assoc($contracts))
      {
        echo '<tr>';
        echo '<td>'.$row['shopid'].'</td>';
        echo '<td>'.$row['location'].'</td>';
        echo '<td>'.$row['shopkeeperid'].'</td>';
        echo '<td>'.$row['name'].'</td>';
        echo '<td>'.$row['email'].'</td>';
        echo '<td>'.$row['mobile'].'</td>';
        echo '<td>'.$row['securitypassid'].'</td>';
        echo '<td>RS. '.$row['monthlyrent'].'</td>';
        echo '</tr>';
      }
      echo '</tbody>
      </table>';
  ?>
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>