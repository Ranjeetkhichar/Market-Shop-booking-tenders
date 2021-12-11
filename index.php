<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require("../db.php");
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
  <title>Market Shop Feedback</title>
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
        <a class="nav-link" href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="login.php">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="feedback.php">Your Feedback</a>
        </li>
      </ul>
    </div>
  </nav>

  <h1 style="text-align: center;">IITP Shop Feedback</h1>


<div>
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
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>