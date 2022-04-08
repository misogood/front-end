<?php
//Require database in this file & image helpers
require_once "includes/database.php";


        //Update the reservation in the database
    $stmt = $db->prepare("UPDATE `reserveringssysteem`
                  SET naam = ?, `telefoonnummer` = ?, `mail` = ?, `datum` = ?, `tijd` = ?, `personen` = ?, `opmerkingen` = ?
                  WHERE `id` = ?");
    $stmt->bind_param("sisssisi", $name, $telnr, $mail, $datum, $time, $personen, $opmerkingen, $reserverenId);

//Check if Post isset, else do nothing
if (isset($_POST['submit'])) {
    //Postback with the data showed to the user, first retrieve data from 'Super global'
    $reserverenId   = mysqli_escape_string($db, $_POST['id']);
    $name           = mysqli_escape_string($db, $_POST['naam']);
    $telnr          = mysqli_escape_string($db, $_POST['telefoonnummer']);
    $mail           = mysqli_escape_string($db, $_POST['mail']);
    $datum          = mysqli_escape_string($db, $_POST['datum']);
    $time           = mysqli_escape_string($db, $_POST['tijd']);
    $personen       = mysqli_escape_string($db, $_POST['personen']);
    $opmerkingen    = mysqli_escape_string($db, $_POST['opmerkingen']);

    function getErrorsForFields($name, $telnr, $mail, $datum, $time, $personen, $opmerkingen) {
//Check if data is valid & generate error if not so
        $errors = [];
        if ($name == "") {
            $errors[] = 'Uw Naam cannot be empty';
        }
        if ($telnr == "") {
            $errors[] = 'Uw Telefoonnummer cannot be empty';
        }
        if ($mail == "") {
            $errors[] = ' Uw E-mail cannot be empty';
        }
        if ($datum == "") {
            $errors[] = 'dd-mm-jjjj cannot be empty';
        }
        if ($time == "") {
            $errors[] = 'Tijd cannot be empty';
        }
        if (!is_numeric($personen) || strlen($personen) != 1 && strlen($personen) != 2) {
            $errors[] = ' Aantal Personen needs to be a number with the length of 1 or 2';
        }
        return $errors;
    }
    $errors = getErrorsForFields($name, $telnr, $mail, $datum, $time, $personen, $opmerkingen);

    $hasErrors = !empty($errors);

    //Save variables to array so the form won't break
    //This array is build the same way as the db result
    $reserveren = [
        'id'             => $reserverenId,
        'naam'           => $name,
        'telefoonnummer' => $telnr,
        'mail'           => $mail,
        'datum'          => $datum,
        'tijd'           => $time,
        'personen'       => $personen,
        'opmerkingen'    => $opmerkingen,
    ];
    $stmt->execute();

    if (empty($errors)) {
            header('Location: overzicht.php');
            exit;
        } else {
            $errors[] = 'Something went wrong in your database query: ' . mysqli_error($db);
        }


} else if(isset($_GET['id'])) {
    //Retrieve the GET parameter from the 'Super global'
    $reserverenId = $_GET['id'];

    //Get the record from the database result
    $query = "SELECT * FROM reserveringssysteem WHERE id = " . mysqli_escape_string($db, $reserverenId);
    $result = mysqli_query($db, $query);
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
    header('Location: overzicht.php');
    exit;
}

//Close connection
mysqli_close($db);
?>
<!doctype html>
<html lang="en">
<head>
    <title>Edit - <?= $reserveren['id'] . ' - ' . $reserveren['naam'] ?></title>
    <meta charset="utf-8"/>
    <link rel="stylesheet" type="text/css" href="css/editstyle.css"/>
</head>
<body>
<style>
    h1 {
        color: white;
    }
</style>
<h1>Edit - <?= $reserveren['id'] . ' - ' . $reserveren['naam'] ?></h1>
<?php if (isset($errors) && !empty($errors)) { ?>
    <ul class="errors">
        <?php for ($i = 0; $i < count($errors); $i++) { ?>
            <li><?= $errors[$i]; ?></li>
        <?php } ?>
    </ul>
<?php } ?>

<?php if (isset($success)) { ?>
    <p class="success">Je reservering is bijgewerkt in de database</p>
<?php } ?>


<form action="<?= htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data">
    <div class="data-field">
        <label for="Uw Naam">Naam</label>
        <input id="naam" type="text" placeholder="Uw Naam" name="naam" value="<?= $reserveren['naam'] ?>" required/>
        <span class="errors"><?= (isset($errors['naam']) ? $errors['naam'] : '') ?></span>
    </div>
    <div class="data-field">
        <label for="Uw Telefoonnummer">Telefoonnummer</label>
        <input id="telefoonnummer" type="text" placeholder="Uw Telefoonnummer" name="telefoonnummer" value="<?= $reserveren['telefoonnummer'] ?>" required/>
        <span class="errors"><?= isset($errors['telefoonnummer']) ? $errors['telefoonnummer'] : '' ?></span>
    </div>
    <div class="data-field">
        <label for="Uw E-mail">Email</label>
        <input id="email" type="email" placeholder="Uw E-mail" name="mail" value="<?= $reserveren['mail'] ?>" required/>
        <span class="errors"><?= isset($errors['mail']) ? $errors['mail'] : '' ?></span>
    </div>
    <div class="data-field">
        <label for="dd-mm-jjjj">Datum</label>
        <input id="dd-mm-jjjj" type="date" placeholder="Datum" name="datum" value="<?= $reserveren['datum'] ?>" required/>
        <span class="errors"><?= isset($errors['datum']) ? $errors['datum'] : '' ?></span>
    </div>
    <div class="data-field">
        <label for="Tijd">Tijd</label>
        <input id="tijd" type="time" placeholder="Tijd" name="tijd" value="<?= $reserveren['tijd'] ?>" required/>
        <span class="errors"><?= isset($errors['tijd']) ? $errors['tijd'] : '' ?></span>
    </div>
    <div class="data-field">
        <label for="Aantal Personen">Aantal Personen</label>
        <input id="personen" type="number" placeholder="Aantal Personen" name="personen" value="<?= $reserveren['personen'] ?>" required/>
        <span class="errors"><?= isset($errors['personen']) ? $errors['personen'] : '' ?></span>
    </div>
    <div class="data-field">
        <label for="Opmerkingen">Opmerkingen</label>
        <input id="opmerkingen" type="text" placeholder="Opmerkingen" name="opmerkingen" value="<?= $reserveren['opmerkingen'] ?>"/>
        <span class="errors"><?= isset($errors['opmerkingen']) ? $errors['opmerkingen'] : '' ?></span>
    </div>
    <div class="data-submit">
        <input type="hidden" name="id" value="<?= $reserverenId ?>"/>
        <input type="submit" class="btn" name="submit" value="Save"/>
    </div>
</form>
<form action="overzicht.php">
    <input type="submit" class="btn" value="Ga terug naar reserveringslijst"/>
</form>
</body>
</html>