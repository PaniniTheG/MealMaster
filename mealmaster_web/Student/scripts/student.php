<?php
include_once('../../../function/connection.php');
include_once('../../../function/function.php');


date_default_timezone_set('UTC');
// $date = date('Y/m/d');
//datepicker
$ben_id = $_SESSION['ben_id'];
if (isset($_POST['date'])) {
    $date = $_POST['date'];
    setcookie("DateCookie", $date, time() + 1000, "/");
} else if (isset($_COOKIE['DateCookie'])) {
    // Retrieve the value of the cookie
    $cookieValue = $_COOKIE['DateCookie'];
    $date = $cookieValue; // Output the value of the cookie
} else {
    $date = date('Y-m-d', strtotime('+7 days'));
}



$db = new DatabaseConnection();
$Mittagessen = $db->getCurrentMittagessen($date);
$Abendessen = $db->getCurrentAbendessen($date);
$mittagessenUser = $db->getMittagessenUser($ben_id, $date);
$abendessenUser = $db->getAbendessenUser($ben_id, $date);



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitSpeiseplan"])) {
    // Check if a radio button is selected
    if (isset($_POST["mittagessenRadioButtons"])) {
        // Call the function with the selected option as parameter
        // echo ($_POST["mittagessenRadioButtons"]);
        $mittagessenWahlName = $_POST["mittagessenRadioButtons"];
    } else {
        echo "No Mittagessen selected";
    }
    if (isset($_POST["abendessenRadioButtons"])) {
        // echo ($_POST["abendessenRadioButtons"]);
        $abendessenWahlName = $_POST["abendessenRadioButtons"];
    } else {
        echo "No Abendessen selected";
    }
    if ($mittagessenWahlName == 'on') {
        $mittagessenWahlName = null;
    }
    if ($abendessenWahlName == 'on') {
        $abendessenWahlName = null;
    }

    $db->insertSpeiseplanUser($date, $mittagessenWahlName, $abendessenWahlName, $ben_id);
    header("Refresh:0");

}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitAbendessen"])) {
    // Check if a radio button is selected
    if (isset($_POST["flexRadioDefault"])) {
        // Call the function with the selected option as parameter
        echo ($_POST["flexRadioDefault"]);
    } else {
        echo "No Abendessen selected";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submitMittagessen"])) {
    // Check if a radio button is selected
    if (isset($_POST["flexRadioDefault"])) {
        // Call the function with the selected option as parameter
        echo ($_POST["flexRadioDefault"]);
    } else {
        echo "No Mittagessen selected";
    }
}





?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="mealmaster_web/student/style/student.css" rel="stylesheet">
    <link rel="icon" href="/mealmaster_web/images/Firmenlogo.png" type="image/x-icon">
</head>

<body>

<?php include '../../Navbar/scripts/Navbar.php'; ?>


    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row border rounded-5 p-3 bg-white shadow box-area">
            <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column top-box" style="background: #f0e79c; width: 100%;">
                <i class="bi bi-person-circle"></i>
                <div class="Überschrift">
                    Essensauswahl
                </div>
            </div>
            <!-- datepicker -->
            <form id="dateForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="date" name="date" id="datePicker" value="<?php echo $date; ?>" min="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" max="<?php echo date('Y-m-d', strtotime('+3 weeks')); ?>" style=" border: 1px solid black; padding: 10px; margin: 10px;">
            </form>

            <script>
                document.getElementById('datePicker').addEventListener('change', function() {

                    document.getElementById('dateForm').submit();

                });
            </script>

            <!-- datepicker end -->
            <!-- essen anzeige -->

            <div class="container">
                <form method="post">
                    <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column top-box" style="background: #f0e79c; width: 100%; border: 1px solid black; padding: 10px; margin: 10px;">
                        <?php if ($mittagessenUser != null) : ?>
                            <?php foreach ($mittagessenUser as $mittagessenuser) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mittagessenUserRadioButtons" id="mittagessenUserRadioButtons" required checked disabled>
                                    <label class="form-check-label" for="mittagessenUserRadioButtons">

                                        <?php
                                        // Check if $mittagessenuser is an array
                                        if (is_array($mittagessenuser)) {
                                            // Access the 'gericht' element if $mittagessenuser is an array
                                            echo htmlspecialchars($mittagessenuser['gericht']);
                                        } else {
                                            // Handle the case where $mittagessenuser is not an array
                                            echo "Kein Mittagessen ausgewählt";
                                        }
                                        ?>


                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php elseif ($Mittagessen == null || empty($Mittagessen)) : ?>
                            <p>Kein Mittagessen verfügbar</p>
                        <?php else : ?>
                            <input class="form-check-input" type="radio" name="mittagessenRadioButtons" id="mittagessenRadioButtons" required>
                            <label class="form-check-label" for="mittagessenRadioButtons">Keine auswahl</label>
                            <?php foreach ($Mittagessen as $mittagessen) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mittagessenRadioButtons" id="mittagessenRadioButtons" value="<?= htmlspecialchars($mittagessen['gericht']) ?>" required>
                                    <label class="form-check-label" for="mittagessenRadioButtons">

                                        <?= htmlspecialchars($mittagessen['gericht']) ?>

                                    </label>
                                </div>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column top-box" style="background: #f0e79c; width: 100%; border: 1px solid black; padding: 10px; margin: 10px;">
                        <?php if ($abendessenUser != null) : ?>
                            <?php foreach ($abendessenUser as $abendessenuser) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="abendessenUserRadioButtons" id="abendessenUserRadioButtons"  required checked disabled>
                                    <label class="form-check-label" for="abendessenUserRadioButtons">

                                        <?php
                                        // Check if $abendessenuser is an array
                                        if (is_array($abendessenuser)) {
                                            // Access the 'gericht' element if $abendessenuser is an array
                                            echo htmlspecialchars($abendessenuser['gericht']);
                                        } else {
                                            // Handle the case where $abendessenuser is not an array
                                            echo "Kein Abendessen ausgewählt";
                                        }
                                        ?>

                                    </label>

                                </div>
                            <?php endforeach; ?>
                        <?php elseif ($Abendessen === null || empty($Abendessen)) : ?>
                            <p>Kein Abendessen verfügbar</p>

                        <?php else : ?>
                            <input class="form-check-input" type="radio" name="abendessenRadioButtons" id="abendessenRadioButtons" required>
                            <label class="form-check-label" for="abendessenRadioButtons">Keine Auswahl</label>
                            <?php foreach ($Abendessen as $abendessen) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="abendessenRadioButtons" id="abendessenRadioButtons" value="<?= htmlspecialchars($abendessen['gericht']) ?>" required>
                                    <label class="form-check-label" for="abendessenRadioButtons">

                                        <?= htmlspecialchars($abendessen['gericht']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                            <input type="submit" class="btn btn-outline-secondary" name="submitSpeiseplan" value="Submit">
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- essenanzeige end -->
        </div>
    </div>
</body>

</html>