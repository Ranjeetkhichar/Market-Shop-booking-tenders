<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
session_start();

// check if the user is already logged in
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true)
{
        header("location: feedback.php");
        exit;
}

$emptyerror = false;
$invaliderror = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require("../db.php");

    $email = mysqli_real_escape_string($db,$_POST["email"]);
    $password = mysqli_real_escape_string($db,$_POST["password"]);

    if (empty($email) || empty($password)) {
        $emptyerror = true;
    }
    else
    {
        $query = "SELECT * from iitplogin WHERE email = '$email'";
        $result = mysqli_query($db, $query) or die(mysqli_error($db));
    
        $row = mysqli_fetch_assoc($result);
    
        if (isset($row) && $password == $row["password"]) {
                $_SESSION['username'] = $email;
                $_SESSION['loggedin'] = true;

                header("location: feedback.php");
                exit;
        } else {
            $invaliderror = true;
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

    <title>IITP Feedback Login</title>
</head>

<body>
    <div class="col-md-4 container">
        <h1 style="text-align: center;">IITP Feedback Login</h1>
        <?php
        if($invaliderror)
        {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Invalid Username or Password!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        }
        if($emptyerror)
        {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Enter all feilds!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
        }
        ?>
        <form action="login.php" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <div>
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" name="password" id="password">
            </div>
            <button type="submit" class="btn btn-primary w-100 mt-3">Submit</button>
        </form>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</html>