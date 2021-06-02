<?php
session_start();
if (array_key_exists("logout", $_GET))
{
    unset($_SESSION["id"]);
    setcookie("id", "", time() - 60 * 60);
    $_COOKIE["id"] = "";
}
else if (array_key_exists("id", $_SESSION) or array_key_exists("id", $_COOKIE))
{
    header("Location: logged-in.php");
    // echo "<script>window.location.href='logged-in.php';</script>";
}
$error_email = $error_password = $error_taken = $error_problem = $error_invalid = "";
if (array_key_exists("submit", $_POST))
{

    include ("connection.php");

    if (!$_POST["email"])
    {
        $error_email .= "<li>An email address is required!</li>";
    }
    if (!$_POST["password"])
    {
        $error_password .= "<li>A password is required!</li>";
    }

    if ($error_email == "" and $error_password == "") {
        if ($_POST["sign_up"] == "1")
        {
            $query = "SELECT `id` FROM `users` WHERE `email` = '" . mysqli_real_escape_string($link, $_POST["email"]) . "' LIMIT 1";
            $result = mysqli_query($link, $query);
            if (mysqli_num_rows($result) > 0)
            {
                $error_taken .= "<li>We're sorry, but that email address is already taken!</li>";
            }
            else
            {
                $query = "INSERT INTO `users` (`email`, `password`) VALUES ('" . mysqli_real_escape_string($link, $_POST["email"]) . "', '" . mysqli_real_escape_string($link, $_POST["password"]) . "')";
                if (!mysqli_query($link, $query))
                {
                    $error_problem .= "<li>There was a problem signing you up. Please refresh the page and try again!</li>";
                }
                else
                {
                    $newUser = mysqli_insert_id($link);
                    $query = "UPDATE `users` SET `password` = '" . password_hash($_POST["password"], PASSWORD_DEFAULT) . "' WHERE `id` = " . $newUser . " LIMIT 1";
                    mysqli_query($link, $query);
                    $_SESSION["id"] = $newUser;
                    if (isset($_POST["stay_logged_in"]) == "1")
                    {
                        setcookie("id", $newUser, time() + 60 * 60 * 24 * 365);
                    }
                    header("Location: logged-in.php");
                    // echo "<script>window.location.href='logged-in.php';</script>";
                }
            }
        }
        else
        {
            $query = "SELECT * FROM `users` WHERE `email` = '" . mysqli_real_escape_string($link, $_POST['email']) . "'";
            $result = mysqli_query($link, $query);
            $row = mysqli_fetch_array($result);
            if (array_key_exists("id", $row))
            {
                if (password_verify($_POST['password'], $row['password']))
                {
                    $_SESSION["id"] = $row["id"];
                    if (isset($_POST["stay_logged_in"]) == "1")
                    {
                        setcookie("id", $row["id"], time() + 60 * 60 * 24 * 365);
                    }
                    header("Location: logged-in.php");
                    // echo "<script>window.location.href='logged-in.php';</script>";
                }
                else
                {
                    $error_invalid = "You have entered an invalid email address/password. Please try again!";
                }
            }
            else
            {
                $error_invalid = "You have entered an invalid email address/password. Please try again!";
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
<?php include ("head.php") ?>

<body>
  <div class="container-fluid pt-5 px-lg-5">
    <div class="container pt-md-5 px-lg-5">
      <h1 class="master-planner-heading mb-3 text-center">Master Planner</h1>
      <p class="text-center text-white px-md-4 px-lg-5 mx-md-5 mx-lg-auto">Whether you're brainstorming for a small project or plotting world domination, Master Planner can help you keep your ideas organised, updated and securely stored.</p>
      <div id="form-signup-wrapper">
        <p class="text-center text-success mb-4">Interested? Then sign up today!</p>
        <div class="error"><?php if($_POST["sign_up"] == "1" and $error_email != "" or $error_password != "" or $error_taken != "" or $error_problem != "") { echo '<div class="alert alert-danger alert-dismissible fade show pr-5 mx-auto" role="alert">
          <p class="text-center mb-2"><strong>There was an error(s) in your form:</strong></p><ul class="ml-md-3 mb-1">'.$error_email.$error_password.$error_taken.$error_problem.'</ul> 
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button></div>'; } ?></div>
        <form id="form-signup" method="POST">
          <div class="form-group mx-auto">
            <input class="form-control" type="email" name="email" placeholder="Email address">
          </div>
          <div class="form-group mx-auto">
            <input class="form-control" type="password" name="password" placeholder="Password">
          </div>
          <div class="form-group form-check mx-auto text-center">
            <input type="checkbox" class="form-check-input" id="signup-checkbox" name="stay_logged_in" value="1">
            <label class="form-check-label text-white" for="signup-checkbox">Stay logged in?</label>
          </div>
          <input type="hidden" name="sign_up" value="1">
          <div class="form-group mx-auto mb-0 text-center">
            <input type="submit" class="btn btn-success px-3 py-2 mr-1" name="submit" value="Sign Up!">
            <a id="show-form-login" class="px-3 py-2 btn btn-outline-secondary show-other-form">Log In</a>
          </div>
        </form>
      </div>
      <div id="form-login-wrapper">
        <p class="text-center text-primary mb-4">Already signed up? Then log in below...</p>
        <div class="error" id="error-invalid"><?php if($error_invalid != "") { echo '<div class="alert alert-danger alert-dismissible fade show pr-5 mx-auto" role="alert">
          <p class="text-center mb-1"><strong>'.$error_invalid.'</strong></p> 
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          </div><script>$("#form-login-wrapper").toggle();
                    $("#form-signup-wrapper").toggle();</script>'; } ?></div>
        <form id="form-login" method="POST">
          <div class="form-group mx-auto">
            <input class="form-control" type="email" name="email" placeholder="Email address" required>
          </div>
          <div class="form-group mx-auto">
            <input class="form-control" type="password" name="password" placeholder="Password" required>
          </div>
          <div class="form-group form-check mx-auto text-center">
            <input type="checkbox" class="form-check-input" id="login-checkbox" name="stay_logged_in" value="1">
            <label class="form-check-label text-white" for="login-checkbox">Stay logged in?</label>
          </div>
          <input type="hidden" name="sign_up" value="0">
          <div class="form-group mx-auto mb-0 text-center">
            <input type="submit" class="btn btn-primary px-3 py-2 mr-1" name="submit" value="Log In!">
            <a id="show-form-signup" class="px-3 py-2 btn btn-outline-secondary show-other-form">Sign Up</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php include("footer.php") ?>
</body>
</html>