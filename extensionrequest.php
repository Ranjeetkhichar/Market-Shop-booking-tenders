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
$invaliderror = false;
$success = false;
$alredyerror = false;

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["extreqid"]))
{
  $extreqid = $_POST["extreqid"];
  $accept_deny = $_POST["accept/deny"];

  if(empty($extreqid) || empty($accept_deny))
  {
    $emptyerror = true;
  }
  else
  {
    $query = "SELECT * FROM extension WHERE extreqid = $extreqid AND extension.status = 'PENDING'";
    $pendingrequest = mysqli_query($db,$query) or die(mysqli_error($db));

    $numrows = mysqli_num_rows($pendingrequest);
    if($numrows == 0)
    {
      $invaliderror = true;
    }
    else
    {
      if($accept_deny == 'ACCEPTED')
      {
        $query = "SELECT * FROM extension NATURAL JOIN market_shop.contract WHERE extreqid = $extreqid";
        $contract = mysqli_query($db,$query) or die(mysqli_error($db));
        $contract = mysqli_fetch_assoc($contract);

        $start = strtotime($contract['enddate']);
        $start = strtotime("+1 day", $start);
        $end = strtotime($contract['extdate']);

        $contractid = $contract['contractid']; 

        $st = date('Y-m-d',$start);
        $en = date('Y-m-d',$end);

        $shopid = $contract['shopid'];
        $query = "SELECT * FROM contract WHERE shopid = '$shopid' AND startdate <= '$en' AND startdate >= '$st'";
        $result = mysqli_query($db,$query) or die(mysqli_error($db));

        $numrows = mysqli_num_rows($result);

        if($numrows > 0)
        {
          $alredyerror = true;
          $query = "UPDATE extension SET extension.status = 'DENIED' WHERE extreqid = $extreqid";
          mysqli_query($db,$query) or die(mysqli_error($db));
        }
        else
        {
          $query = "UPDATE extension SET extension.status = '$accept_deny' WHERE extreqid = $extreqid";
          mysqli_query($db,$query) or die(mysqli_error($db));
          $success = true;
          while($start <= $end)
          {
            $x = date('Y-m-d',$start);
            $query = "INSERT INTO payment (contractid,payment.month) VALUES ($contractid,'$x')";
            mysqli_query($db,$query) or die(mysqli_error($db));
      
            $start = strtotime("+1 month", $start);
          }
  
          $query = "UPDATE contract SET enddate = '$en' WHERE contractid = $contractid";
          mysqli_query($db,$query) or die(mysqli_error($db));
        }
      }
      else
      {
        $query = "UPDATE extension SET extension.status = '$accept_deny' WHERE extreqid = $extreqid";
        mysqli_query($db,$query) or die(mysqli_error($db));
        $success = true;
      }
    }
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

  <br>
  <div>
  <h3>PENDING Extension Requests:</h3>
  <?php
    $query = "SELECT * FROM extension WHERE extension.status = 'PENDING'";
    $extreq = mysqli_query($db,$query) or die(mysqli_error($db));

    echo '<table class="table">
            <thead>
              <tr>
                <th scope="col">Contract ID</th>
                <th scope="col">Extension ID</th>
                <th scope="col">Request Date</th>
                <th scope="col">Extension Date</th>
                <th scope="col">Extension Period</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>';

      while($row = mysqli_fetch_assoc($extreq))
      {
        echo '<tr>';
        echo '<td>'.$row['contractid'].'</td>';
        echo '<td>'.$row['extreqid'].'</td>';
        echo '<td>'.$row['extreqdate'].'</td>';
        echo '<td>'.$row['extdate'].'</td>';
        echo '<td>'.$row['extperiod'].'</td>';
        echo '<td>'.$row['status'].'</td>';
        echo '</tr>';
      }
      echo '</tbody>
      </table>';
  ?>
</div>
<!-- form for booking details -->
  <form action="extensionrequest.php" method="POST">
    <?php
      if ($emptyerror) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  Enter all feilds!
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
      if ($invaliderror) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  Invalid Request ID!
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
      if ($alredyerror) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  Shop already booked!
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
      if ($success) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  Accepted successfully!
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
      ?>
      <div>
        <label for="extreqid">Extension Request ID</label>
        <input type="number" class="form-control" name="extreqid" id="extreqid">
      </div>
      <div class="col">
        <label for="accept/deny">Accept/Deny</label>
        <select id="accept/deny" name="accept/deny" class="form-control">
            <option disabled selected value> -- select an option -- </option>';
            <option value="ACCEPTED">Accept</option>
            <option value="DENIED">Deny</option>
        </select>
        </div>
      <br>
      <div class="subbutton">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>