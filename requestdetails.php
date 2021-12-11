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

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["reqid"]))
{
  $reqid = $_POST["reqid"];
  $accept_deny = $_POST["accept/deny"];

  if(empty($reqid) || empty($accept_deny))
  {
    $emptyerror = true;
  }
  else
  {
    // booking details
    $query = "SELECT * FROM request WHERE reqid = $reqid AND request.status = 'PENDING'";
    $pendingrequest = mysqli_query($db,$query);

    $numrows = mysqli_num_rows($pendingrequest);
    if($numrows == 0)
    {
      $invaliderror = true;
    }
    else
    {
        if($accept_deny == 'DENIED')
        {
            $query = "UPDATE request SET request.status = 'DENIED' WHERE reqid = $reqid";
            mysqli_query($db,$query);
        }
        else
        {
            $_SESSION['reqid'] = $reqid;
            header("location: ./acceptrequest.php");
            exit;
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
  <h3>PENDING requests:</h3>
  <?php
    $query = "SELECT * FROM request WHERE request.status = 'PENDING'";
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
                <th scope="col">Email</th>
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
        echo '<td>'.$row['email'].'</td>';
        echo '</tr>';
      }
      echo '</tbody>
      </table>';
  ?>
</div>
<!-- form for booking details -->
  <form action="requestdetails.php" method="POST">
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
      <div>
        <label for="reqid">Request ID</label>
        <input type="number" class="form-control" name="reqid" id="reqid">
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