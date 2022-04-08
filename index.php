<?php

$apiKey = "4dc20a34e8efccbcf851eb7d6cf3288d";
$cityId = "2758011";
$googleApiUrl = "http://api.openweathermap.org/data/2.5/weather?id=" . $cityId . "&lang=en&units=metric&APPID=" . $apiKey;

$ch = curl_init();

curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_VERBOSE, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);

curl_close($ch);
$data = json_decode($response);
$currentTime = time();

require_once ("includes/database.php");


//Save the reservering to the database
        $stmt = $db->prepare( "INSERT INTO `reserveringssysteem` (`naam`,`telefoonnummer`, `mail`, `datum`, `tijd`, `personen`, `opmerkingen`)
                  VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("sisssis", $name, $telnr, $mail, $datum, $time, $personen, $opmerkingen);

//Check if Post isset, else do nothing
if (isset($_POST['submit'])) {
    //Postback with the data showed to the user, first retrieve data from 'Super global'
    $name = mysqli_escape_string($db, $_POST['naam']);
    $telnr = mysqli_escape_string($db, $_POST['telefoonnummer']);
    $mail = mysqli_escape_string($db, $_POST['mail']);
    $datum = mysqli_escape_string($db, $_POST['datum']);
    $time = mysqli_escape_string($db, $_POST['tijd']);
    $personen = mysqli_escape_string($db, $_POST['personen']);
    $opmerkingen = mysqli_escape_string($db, $_POST['opmerkingen']);

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
        if (!is_numeric($personen) || strlen($personen) != 1 || strlen($personen) != 2) {
            $errors[] = ' Aantal Personen needs to be a number with the length of 2';
        }
        if ($opmerkingen == "") {
            $errors[] = 'Opmerkingen cannot be empty';
        }
        return $errors;
    }
    $errors = getErrorsForFields($name, $telnr, $mail, $datum, $time, $personen, $opmerkingen);

    $hasErrors = !empty($errors);

    if (!$hasErrors) {
        insertIntoDatabase($name, $telnr, $mail, $datum, $time, $personen, $opmerkingen);
    }

        $stmt->execute();

        if ($stmt) {
          // header('Location: index.php');
            echo"Reservering gelukt!";

            $subject = "Wok 'n Sushi Reservering";
            $body = "Beste $name,
                     uw reservering voor $personen personen op $datum, om $time is gelukt!
                     mvg,
                     Wok'n Sushi";
            $headers = [
                'From' => 'dannyhu2002@gmail.com'
            ];
            //for($i=0;$i<50;$i++){
                if (mail($mail, $subject, $body, $headers)) {
                    echo " Email successfully sent to $mail...";
                } else {
                    echo " Email sending failed...";
                }
            //}

//            exit;
        } else {
            $errors[] = 'Something went wrong in your database query: ' . mysqli_error($db);
        }

mysqli_close($db);
}
?>

<!doctype html>
<html>
<head>
    <title> &copy; Wok 'n Sushi Reserveringen</title>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css"/>
    <link rel="stylesheet" type="text/css" href="css/weather.css"/>
    <script src="https://kit.fontawesome.com/dcb2a70d47.js" crossorigin="anonymous"></script>
</head>
<header>
    <nav>
        <a href="index.php"><img src="wslogo.png" width="300" height="80" class="logo"></a>
   <ul>
       <li><a href="index.php">Home Page</a></li>
       <li><a href="https://www.woknsushi-koperwiek.nl">Official Site</a></li>
       <li><a href="https://www.woknsushi-koperwiek.nl/wp-content/uploads/2019/01/alacart-menukaart2019.pdf">Download A La Carte Menukaart</a></li>
       <li><a href="https://www.woknsushi-koperwiek.nl/algemene-voorwaarden/">Algemene Voorwaarden</a></li>
       <li><a href="login.php">Login</a></li>
   </ul>
    </nav>
</header>
<body>
<button id="scrollToTopBtn" onclick="scrollToTopBtn">
    <i class="fas fa-chevron-up"></i>
</button>
<div class="center">
<h1> Online Tafel Reserveren</h1>
    <p class="red-color">KOM GEZELLIG EEN HAPPIE ETEN</p>
    <p class="gray-color">U kunt ons ook gelijk bellen voor een reservering: (31) 10 254 2635</p>
    <div id="error"></div>
    <form id="form" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data">
    <div class="data-field">
        <label for="Uw Naam"></label>
        <input id="naam" type="text" placeholder="Uw Naam"  name="naam" value="<?= (isset($name) ? $name : ''); ?>" />
        <span><?= (isset($errors['Uw Naam']) ? $errors['Uw Naam'] : '') ?></span>
        <label for="Uw Telefoonnummer"></label>
        <input id="telefoonnummer" type="text" placeholder="Uw Telefoonnummer" name="telefoonnummer" value="<?= (isset($telnr) ? $telnr : ''); ?>" required/>
        <label for="Uw Email"></label>
        <input id="email" type="email" placeholder="Uw E-mail" name="mail" value="<?= (isset($mail) ? $mail : ''); ?>" required/>
    </div>
    <div class="data-field">
        <label for="dd-mm-jjjj"></label>
        <input id="dd-mm-jjjj" type="date" placeholder="Datum" name="datum" value="<?= (isset($datum) ? $datum : ''); ?>" required/>
        <label for="Tijd"></label>
        <input id="tijd" type="time" placeholder="Tijd" name="tijd" value="<?= (isset($time) ? $time : ''); ?>" />
        <label for="Aantal Personen"></label>
        <input id="personen" type="number" placeholder="Aantal Personen" name="personen" value="<?= (isset($personen) ? $personen : ''); ?>" required/>
    </div>
    <div class="data-field">
        <label for="Opmerkingen"></label>
        <input id="opmerkingen" type="text" placeholder="Opmerkingen" name="opmerkingen" value="<?= (isset($opmerkingen) ? $opmerkingen : ''); ?>"/>
    </div>
    <div class="data-submit">
        <input type="submit" class="btn" name="submit" value="RESERVEER NU"/>
    </div>
</form>
</div>
<footer>
    <div class="report-container">
        <h2><?php echo $data->name; ?></h2>
        <div class="time">
            <div><?php echo date("l G:i", $currentTime); ?></div>
            <div><?php echo date("jS F, Y",$currentTime); ?></div>
            <div><?php echo ucwords($data->weather[0]->description); ?></div>
        </div>
        <div class="weather-forecast">
            <img
                src="http://openweathermap.org/img/w/<?php echo $data->weather[0]->icon; ?>.png" class="weather-icon"/>
            <span class="max-temperature"><?php echo $data->main->temp_max; ?>°C</span>
            <span class="min-temperature"><?php echo $data->main->temp_min; ?>°C</span>
        </div>
        <div class="time">
            <div>Humidity: <?php echo $data->main->humidity; ?> %</div>
            <div>Wind: <?php echo $data->wind->speed; ?> km/h</div>
        </div>
    </div>
</footer>
<script src="main.js"></script>
</body>
</html>
