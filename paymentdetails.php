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

  <div>
  <h3>Rent Payment Details</h3>
  <?php
    $email = $_SESSION['username'];
    $query = "SELECT * FROM payment NATURAL JOIN market_shop.contract NATURAL JOIN shopkeepers NATURAL JOIN shop WHERE email = '$email'";
    $paymentdetails = mysqli_query($db,$query) or die(mysqli_error($db));

    echo '<table class="table">
            <thead>
              <tr>
                <th scope="col">Contract ID</th>
                <th scope="col">Shop ID</th>
                <th scope="col">Location</th>
                <th scope="col">Month</th>
                <th scope="col">Rent</th>
                <th scope="col">Rent Status</th>
              </tr>
            </thead>
            <tbody>';

      while($row = mysqli_fetch_assoc($paymentdetails))
      {
        echo '<tr>';
        echo '<td>'.$row['contractid'].'</td>';
        echo '<td>'.$row['shopid'].'</td>';
        echo '<td>'.$row['location'].'</td>';
        echo '<td>'.$row['month'].'</td>';
        echo '<td>'.$row['monthlyrent'].'</td>';
        echo '<td>'.$row['rentstatus'].'</td>';
        echo '</tr>';
      }
      echo '</tbody>
      </table>';
    ?>
    </div>
<br>
  <div>
  <h3>Electricity Bill Payment Details</h3>
  <?php
    $email = $_SESSION['username'];
    $query = "SELECT * FROM payment NATURAL JOIN market_shop.contract NATURAL JOIN shopkeepers NATURAL JOIN shop WHERE email = '$email' AND electricitybill <> 'NULL'";
    $paymentdetails = mysqli_query($db,$query) or die(mysqli_error($db));

    echo '<table class="table">
            <thead>
              <tr>
                <th scope="col">Contract ID</th>
                <th scope="col">Shop ID</th>
                <th scope="col">Location</th>
                <th scope="col">Month</th>
                <th scope="col">Electricity Bill</th>
                <th scope="col">Electricity Bill Status</th>
              </tr>
            </thead>
            <tbody>';

      while($row = mysqli_fetch_assoc($paymentdetails))
      {
        echo '<tr>';
        echo '<td>'.$row['contractid'].'</td>';
        echo '<td>'.$row['shopid'].'</td>';
        echo '<td>'.$row['location'].'</td>';
        echo '<td>'.$row['month'].'</td>';
        echo '<td>'.$row['electricitybill'].'</td>';
        echo '<td>'.$row['electricitybillstatus'].'</td>';
        echo '</tr>';
      }
      echo '</tbody>
      </table>';
    ?>
    </div>


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>