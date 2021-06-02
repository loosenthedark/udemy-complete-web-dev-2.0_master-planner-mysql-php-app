<?php
session_start();

$plans = "";

if (array_key_exists("id", $_COOKIE))
{
    $_SESSION["id"] = $_COOKIE["id"];
}

if (array_key_exists("id", $_SESSION))
{
    include ("connection.php");
    $query = "SELECT `diary_entry` FROM `users` WHERE `id` = " . mysqli_real_escape_string($link, $_SESSION["id"]) . " LIMIT 1";
    $row = mysqli_fetch_array(mysqli_query($link, $query));
    $plans = $row["diary_entry"];
}
else
{
    header("Location: index.php");
}

include ("head.php");
?>

    <body class="body-logged-in">
        <nav class="navbar navbar-dark bg-success">
            <a class="navbar-brand font-weight-bold pl-2" href="#">Master Planner</a>
            <a id="btn-log-out" class="px-3 py-2 btn btn-primary" href="index.php?logout=1">Log Out</a>
        </nav>
        <div class="container-fluid pt-3">
            <textarea class="form-control planner" placeholder="Get started by typing your master plans here...&#10;&#10;They'll save automatically, and will be securely stored for when you next log in!"><?php echo $plans; ?>
            </textarea>
        </div>

<?php include("footer.php"); ?>

    </body>
</html>