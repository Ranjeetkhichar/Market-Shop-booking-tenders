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

if($_SERVER["REQUEST_METHOD"] == "POST")
{
  $reqid = $_SESSION["reqid"];

  $query = "SELECT * FROM request WHERE reqid = $reqid";
  $request = mysqli_query($db,$query);
  $request = mysqli_fetch_assoc($request);

  $startdate = $request['startdate'];
  $enddate = $request['enddate'];
  $email = $request['email'];

  if(empty($reqid) || empty($_POST["shopid"]) || empty($_POST["monthlyrent"]))
  {
    $emptyerror = true;
  }
  else
  {
    $shopid = $_POST["shopid"];
    $monthlyrent = $_POST["monthlyrent"];
    $query = "SELECT * FROM shopkeepers WHERE email = '$email'";
    $shopkeeper = mysqli_query($db,$query);
    $numrows = mysqli_num_rows($shopkeeper);

    if($numrows == 0)
    {
        $query = "SELECT max(shopkeeperid) as id FROM shopkeepers";
        $shopkeeperid = mysqli_query($db,$query);
        $shopkeeperid = mysqli_fetch_assoc($shopkeeperid);
        $shopkeeperid = $shopkeeperid['id'] + 1;

        $securitypassid = 'SC-'.$shopkeeperid;
        $query = "INSERT INTO shopkeepers VALUES ($shopkeeperid,'$email','$securitypassid')";
        mysqli_query($db,$query);
    }
    else
    {
        $shopkeeper = mysqli_fetch_assoc($shopkeeper);
        $shopkeeperid = $shopkeeper['shopkeeperid'];
    }

    $query = "SELECT max(contractid) as id FROM market_shop.contract";
    $contractid = mysqli_query($db,$query);
    $contractid = mysqli_fetch_assoc($contractid);
    $contractid = $contractid['id'] + 1;

    $query = "INSERT INTO market_shop.contract (contractid,shopid,shopkeeperid,startdate,enddate,monthlyrent) VALUES ($contractid,'$shopid',$shopkeeperid,'$startdate','$enddate','$monthlyrent')";
    mysqli_query($db,$query);

    $query = "DELETE FROM request WHERE reqid = $reqid";
    mysqli_query($db,$query);

    $start = strtotime($startdate);
    $end = strtotime($enddate);

    while($start <= $end)
    {
      $x = date('Y-m-d',$start);
      $query = "INSERT INTO payment (contractid,payment.month) VALUES ($contractid,'$x')";
      mysqli_query($db,$query) or die(mysqli_error($db));

      $start = strtotime("+1 month", $start);
    }

    echo 'Accepted successfully <br> <a href = "requestdetails.php">Go Back</a>';
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

<!-- form for booking details -->
  <form action="acceptrequest.php" method="POST">
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
      ?>
      <h2>Contract Details:</h2>
      <div class="col">
        <label for="shopid">Shop ID</label>
        <select id="shopid" name="shopid" class="form-control">
            <option disabled selected value> -- select an option -- </option>;
            <?php
                $reqid = $_SESSION['reqid'];
                $query = "SELECT * FROM request WHERE reqid = $reqid";
                $request = mysqli_query($db,$query) or die(mysqli_error($db));
                $request = mysqli_fetch_assoc($request);
                $location = $request['location'];
                
                $startdate = $request['startdate'];
                $enddate = $request['enddate'];
                $email = $request['email'];
                $query = "SELECT shopid FROM shop WHERE shop.location = '$location' AND shopid NOT IN (SELECT shopid FROM market_shop.contract NATURAL JOIN shop WHERE shop.location = '$location' AND '$startdate' <= enddate AND '$enddate' >= startdate)";
                $shops = mysqli_query($db,$query) or die(mysqli_error($db));
                
                while($row = mysqli_fetch_assoc($shops))
                {
                    $shopid = $row['shopid'];
                    echo "<option value = '$shopid'>$shopid</option>";
                }
            ?>
        </select>
        </div>
      <div>
        <label for="monthlyrent">Monthly Rent</label>
        <input type="number" class="form-control" name="monthlyrent" id="monthlyrent">
      </div>
      <br>
      <br>
      <div class="subbutton">
      <button type="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>