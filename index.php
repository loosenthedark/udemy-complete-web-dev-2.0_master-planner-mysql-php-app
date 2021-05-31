<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<meta name="Description" content="Enter your description here"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/4.6.0/yeti/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
<link rel="stylesheet" href="assets/css/style.css">
<title>Title</title>
</head>
<body>
    
<?php

    session_start();
    
    if (array_key_exists("logout", $_GET)) {
        unset($_SESSION["id"]);
        setcookie("id", "", time() - 60 * 60);
        $_COOKIE["id"] = "";
    } else if (array_key_exists("id", $_SESSION) OR array_key_exists("id", $_COOKIE)) {
        header("Location: logged-in.php");
    }
    
    $error = "";

    if (array_key_exists("submit", $_POST)) {
        
        $link = mysqli_connect("sdb-h.hosting.stackcp.net", "secret-diary-313836081f", "401mrs5ig6","secret-diary-313836081f");
        if (mysqli_connect_error()) {
        die ("There was a problem connecting to the database!");
        }
        
        if (!$_POST["email"]) {
            $error .= "<p>An email address is required!</p>";
        }
        if (!$_POST["password"]) {
            $error .= "<p>A password is required!</p>";
        }
        
        if ($error != "") {
            $error = "<p>There were errors in your form: </p>".$error;
        } else {
            if ($_POST["sign_up"] == "1") {
                $query = "SELECT `id` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link, $_POST["email"])."' LIMIT 1";
                $result = mysqli_query($link, $query);
                if (mysqli_num_rows($result) > 0) {
                    $error = "<p>We're sorry, but that email address is already taken!</p>";
                } else {
                    $query = "INSERT INTO `users` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST["email"])."', '".mysqli_real_escape_string($link, $_POST["password"])."')";
                    if (!mysqli_query($link, $query)) {
                        $error = "<p>There was a problem signing you up. Please refresh the page and try again!</p>";
                    } else {
                        $query = "UPDATE `users` SET `password` = '".password_hash($_POST["password"], PASSWORD_DEFAULT)."' WHERE `id` = ".mysqli_insert_id($link)." LIMIT 1";
                        mysqli_query($link, $query);
                        $_SESSION["id"] = mysqli_insert_id($link);
                        if (isset ($_POST["stay_logged_in"]) == "1") {
                        setcookie("id", mysqli_insert_id($link), time() + 60 * 60 * 24 * 365);
                        }
                        header("Location: logged-in.php");
                    }
                }
            } else {
                $query = "SELECT * FROM `users` WHERE `email` = '".mysqli_real_escape_string($link, $_POST['email'])."'";
                $result = mysqli_query($link, $query);
                $row = mysqli_fetch_array($result);
                if (array_key_exists("id", $row)) {
                    if (password_verify($_POST['password'], $row['password'])) {
                        $_SESSION["id"] = $row["id"];
                        if (isset($_POST["stay_logged_in"]) == "1") {
                            setcookie("id", $row["id"], time() + 60 * 60 * 24 * 365);
                        }
                        header("Location: logged-in.php");
                    } else {
                        $error = "You have entered an invalid email address/password. Please try again!";
                    }
                } else {
                    $error = "You have entered an invalid email address/password. Please try again!";
                }
            }
        }
    
    // $query = "INSERT INTO `users` (`email`, `password`) VALUES ('verocha@gmail.com', 'asdf789')";
    
    // $name = "Rob O'Grady";
    
    // $query = "UPDATE `users` SET `password` = 'Boards1969!' WHERE `email` = 'paulharrington@loosenthedark.tech' LIMIT 1";
    
    // mysqli_query($link, $query);
    
    // $query = "SELECT `email` FROM `users` WHERE `name` = '".mysqli_real_escape_string($link, $name)."'";
    
    // if ($result = mysqli_query($link, $query)) {
    //     while($row = mysqli_fetch_array($result)) {
    //     print_r($row);
    //     }
    //
    
    // setcookie("customerId", "", time() + 60 * 60 * 24);
    // $_COOKIE["customerId"] = "test";
    // echo $_COOKIE["customerId"];
}
?>

<h1>What's the craic?</h1>

<div id="error"><?php echo $error; ?></p>
<form method="POST">
<input type="email" name="email" placeholder="Email address">
<input type="password" name="password" placeholder="Password">
<label for="signup-checkbox">Stay logged in?</label>
<input type="checkbox" id="signup-checkbox" name="stay_logged_in" value="1">
<input type="hidden" name="sign_up" value="1">
<input type="submit" name="submit" value="Sign Up!">
</form>

<form method="POST">
<input type="email" name="email" placeholder="Email address">
<input type="password" name="password" placeholder="Password">
<label for="login-checkbox">Stay logged in?</label>
<input type="checkbox" id="login-checkbox" name="stay_logged_in" value="1">
<input type="hidden" name="sign_up" value="0">
<input type="submit" name="submit" value="Log In!">
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
</body>
</html>