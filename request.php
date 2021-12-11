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

function findenddate($month) {
    $month = explode('-',$month);
    if($month[1] == '01' || $month[1] == '03' || $month[1] == '05' || $month[1] == '07' || $month[1] == '08' || $month[1] == '10' || $month[1] == '12')
    {
        return 31;
    }
    else if($month[1] == '02')
    {
        return 28;
    }
    else
    {
        return 30;
    }
}

$notavailable = false;
$emptyerror = false;
$invaliderror = false;
$success = false;
$pendingerror = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["startdate"])) {

    $startdate = $_POST["startdate"];
    $enddate = $_POST["enddate"];
    $location = $_POST["location"];

    $username = $_SESSION['username'];

    $today = date('Y-m-d'); 
    
    if (empty($startdate) || empty($enddate) || empty($location)) 
    {
        $emptyerror = true;
    }
    else 
    {
        $query = "SELECT * FROM request WHERE email = '$username' AND request.status = 'PENDING'";
        $result = mysqli_query($db, $query) or die(mysqli_error($db));
        $num_rows = mysqli_num_rows($result);

        if($num_rows > 0)
        {
          $pendingerror = true;
        }
        else
        {        
          $startdate = $startdate.'-'.'01';
          $enddate = $enddate.'-'.findenddate($enddate);

          if ($startdate > $enddate || $startdate < $today) {
              $invaliderror = true;
          } else {
              $query = "SELECT shopid FROM shop WHERE shop.location = '$location' AND shopid NOT IN (SELECT shopid FROM market_shop.contract NATURAL JOIN shop WHERE shop.location = '$location' AND $startdate < enddate AND $enddate > startdate)";
              $availableshops = mysqli_query($db, $query) or die(mysqli_error($db));

              $num_rows = mysqli_num_rows($availableshops);
              if ($num_rows == 0) {
              $notavailable = true;
              } else {
                $query = "SELECT max(reqid) FROM request";
                $result = mysqli_query($db, $query) or die(mysqli_error($db));

                $row = mysqli_fetch_assoc($result);
                $reqid = $row['max(reqid)'];
                $reqid = $reqid+1;

                $query = "INSERT INTO request (reqid,email,startdate,enddate,request.location) VALUES ($reqid,'$username','$startdate','$enddate','$location')";
                mysqli_query($db, $query) or die(mysqli_error($db));

                $success = true;
              }
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
</div>
      <br><br>
  <form action="request.php" method="POST">
    <?php
    if ($notavailable) {
      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                No shop available in the provided interval and location.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    if ($emptyerror) {
      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Enter all feilds!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    if ($invaliderror) {
      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Invalid inputs!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    if ($pendingerror) {
      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                You already have a PENDING request!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    if ($success) {
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                Request Posted. Your request will be reviewed by competitive authorities. Please review your request status at the home page.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    ?>
    <div class="col">
        <label for="startdate">Start Month</label>
        <input type="month" class="form-control" name="startdate" id="startdate" value="<?php echo date('Y-m');?>">
    </div>
    <br>
    <div class="col">
        <label for="enddate">End Month</label>
        <input type="month" class="form-control" name="enddate" id="enddate" value="<?php echo date('Y-m');?>">
        <small style="color: red;">End month is inclusive.</small>
    </div>
    <br>
    <div class="col">
        <label for="location">Location</label>
        <select id="location" name="location" class="form-control">
            <option disabled selected value> -- select an option -- </option>';
            <option value="Food Court">Food Court</option>
            <option value="Near Old Boys Hostel">Near Old Boys Hostel</option>
            <option value="Near Gate No.2">Near Gate No.2</option>
        </select>
    </div>
    <br>
    <div class="subbutton">
      <button type="submit" class="btn btn-primary">Make Request</button>
    </div>
  </form>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>