<?php
//Require reservering data to use variable in this file
/** @var mysqli $db */
require_once "includes/database.php";

if (isset($_POST['submit'])) {
    // DELETE DATA
    // Remove the reservation data from the database
    $query = "DELETE FROM reserveringssysteem WHERE id = " . mysqli_escape_string($db, $_POST['id']);

    mysqli_query($db, $query) or die ('Error: '.mysqli_error($db));

    //Close connection
    mysqli_close($db);

    //Redirect to homepage after deletion & exit script
    header("Location: overzicht.php");
    exit;

} else if(isset($_GET['id'])) {
    //Retrieve the GET parameter from the 'Super global'
    $reserverenId = $_GET['id'];

    //Get the reservation from the database result
    $query = "SELECT * FROM reserveringssysteem WHERE id = " . mysqli_escape_string($db, $reserverenId);
    $result = mysqli_query($db, $query) or die ('Error: ' . $query );

    if(mysqli_num_rows($result) == 1)
    {
        $reserveren = mysqli_fetch_assoc($result);
    }
    else {
        // redirect when db returns no result
        header('Location: overzicht.php');
        exit;
    }
} else {
    // Id was not present in the url OR the form was not submitted

    // redirect to overzicht.php
    header('Location: overzicht.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="css/deletestyle.css"/>
    <title>Delete - <?= $reserveren['id'] . ' - ' . $reserveren['naam'] ?></title>
</head>
<body>
<style>
    h2 {
        color: white;
    }
    .red-color {
        color: #e73a3f;
    }
</style>
<h2>Delete - <?= $reserveren['id'] . ' - ' . $reserveren['naam'] ?></h2>
<form action="" method="post">
    <p class="red-color">
        Weet u zeker dat u de reservering "<?= htmlspecialchars($reserveren['naam'])?>" wilt verwijderen?
    </p>
    <input type="hidden" name="id" value="<?= $reserveren['id'] ?>"/>
    <input type="submit" class="btn" name="submit" value="Verwijderen"/>
</form>
<form action="overzicht.php">
<input type="submit" class="btn" value="Ga terug naar reserveringslijst"/>
</form>
</body>
</html>
