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

$emptyerror = false;
$invaliderror = false;
$success = false;

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["extreqid"]))
{
    $id = $_POST["extreqid"];

    if(empty($id))
    {
        $emptyerror = true;
    }
    else
    {
        $email = $_SESSION['username'];
        $query = "SELECT * FROM extension NATURAL JOIN market_shop.contract NATURAL JOIN shopkeepers WHERE email = '$email' AND extreqid = $id AND extension.status = 'PENDING'";
        $result = mysqli_query($db,$query) or die(mysqli_error($db));

        $numrows = mysqli_num_rows($result);
        if($numrows == 0)
        {
            $invaliderror = true;
        }
        else
        {
            $query = "DELETE FROM extension WHERE extreqid = $id";
            mysqli_query($db,$query) or die(mysqli_error($db));
            $success = true;
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
  <h3>Extension Requests Status:</h3>
  <?php
    $query = "SELECT * FROM extension NATURAL JOIN market_shop.contract NATURAL JOIN shopkeepers WHERE email = '{$_SESSION["username"]}'";
    $yourbookings = mysqli_query($db,$query) or die(mysqli_error($db));

    $numrows = mysqli_num_rows($yourbookings);
    $reqid = 0;

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

      while($row = mysqli_fetch_assoc($yourbookings))
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
  <form action="cancelextensionrequest.php" method="POST">
    <?php
      if ($success) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                  Your request has been cancelled.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
      if ($emptyerror) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  Empty Fields!
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
      if ($invaliderror) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                  Invalid Inputs!
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
      }
      ?>
      <div>
          <label for="extreqid" class="form-label">Extension ID</label>
          <input type="number" class="form-control" name="extreqid" id="extreqid">
      </div>
      <br>
      <div class="subbutton">
      <button type="submit" class="btn btn-primary">Cancel</button>
    </div>
  </form>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>