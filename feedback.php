<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');


session_start();
require("../db.php");

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false) {
  header("location: login.php");
}

$emptyerror = false;
$alreadyerror = false;
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["rating"])) {

    $shopid = $_POST['shopid'];
    $rating = $_POST['rating'];
    $email = $_SESSION['username'];
    
    if (empty($shopid) || empty($rating)) 
    {
        $emptyerror = true;
    }
    else 
    {
        $query = "SELECT * FROM feedback NATURAL JOIN contract WHERE email = '$email' AND shopid = '$shopid'";
        $result = mysqli_query($db, $query) or die(mysqli_error($db));
        $num_rows = mysqli_num_rows($result);

        if($num_rows > 0)
        {
            $alreadyerror = true;
        }
        else
        {
            $query = "SELECT * FROM contract WHERE shopid = '$shopid' AND startdate <= curdate() AND enddate >= curdate()";
            $result = mysqli_query($db, $query) or die(mysqli_error($db));
            $row = mysqli_fetch_assoc($result);
            $contractid = $row['contractid'];

            $query = "INSERT INTO feedback VALUES ($contractid,$rating,'$email')";
            mysqli_query($db, $query) or die(mysqli_error($db));

            $prevrating = $row['rating'];
            $prevfeedbackresponses = $row['feedbackresponses'];

            $newrating = bcdiv(($prevrating*$prevfeedbackresponses + $rating), (1+$prevfeedbackresponses), 2);
            $newrespose = $prevfeedbackresponses+1;

            $query = "UPDATE contract SET rating = $newrating WHERE contractid = $contractid";
            mysqli_query($db, $query) or die(mysqli_error($db));

            $query = "UPDATE contract SET feedbackresponses = $newrespose WHERE contractid = $contractid";
            mysqli_query($db, $query) or die(mysqli_error($db));

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

  <link rel="stylesheet" href="../style.css">
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
        <a class="nav-link" href="feedback.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="../logout.php">Logout</a>
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
  <h3>Shop Ratings:</h3>
  <?php
      echo '<table class="table">
      <thead>
        <tr>
          <th scope="col">Shop ID</th>
          <th scope="col">Location</th>
          <th scope="col">Shopkeeper Name</th>
          <th scope="col">Rating</th>
          <th scope="col">Feedback Count</th>
        </tr>
      </thead>
      <tbody>';

      $query = "SELECT * FROM market_shop.contract NATURAL JOIN shopkeepers NATURAL JOIN market_shop.login NATURAL JOIN shop WHERE startdate <= curdate() AND curdate() <= enddate";
      $contracts = mysqli_query($db,$query) or die(mysqli_error($db));

      while($row = mysqli_fetch_assoc($contracts))
      {
            echo '<tr>';
            echo '<td>'.$row['shopid'].'</td>';
            echo '<td>'.$row['location'].'</td>';
            echo '<td>'.$row['name'].'</td>';
            echo '<td>'.$row['rating'].'</td>';
            echo '<td>'.$row['feedbackresponses'].'</td>';
            echo '</tr>';
      }
      echo '</tbody>
      </table>';
  ?>
  <br><br>
  <h3>Your Response:</h3>
  <form action="feedback.php" method="POST">
    <?php
    if ($emptyerror) {
      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Enter all feilds!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    if ($alreadyerror) {
      echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Feedback already filled for this shop!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    if ($success) {
      echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                Feedback submitted!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>';
    }
    ?>
    <div class="col">
        <label for="shopid">Shop ID</label>
        <select id="shopid" name="shopid" class="form-control">
            <option disabled selected value> -- select an option -- </option>';
            <?php
                $query = "SELECT * FROM market_shop.contract NATURAL JOIN shop WHERE startdate <= curdate() AND curdate() <= enddate";
                $contracts = mysqli_query($db,$query) or die(mysqli_error($db));
          
                while($row = mysqli_fetch_assoc($contracts))
                {
                    $shopid = $row['shopid'];
                    echo "<option value='$shopid'>$shopid</option>";
                }
            ?>
        </select>
    </div>
    <div class="col">
        <label for="rating">Rating</label>
        <select id="rating" name="rating" class="form-control">
            <option disabled selected value> -- select an option -- </option>';
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
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