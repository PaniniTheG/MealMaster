<?php
include_once('../../../function/connection.php');
include_once('../../../function/function.php');

date_default_timezone_set('UTC');
// $date = date('Y/m/d');
//datepicker
$ben_id = 1;
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
    <title>Count Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="mealmaster_web/Count/style/count.css" rel="stylesheet">
</head>

<body>
<?php include '../../Navbar/scripts/Navbar.php'; ?>


    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="row border rounded-5 p-3 bg-white shadow box-area">
            <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column top-box" style="background: #f0e79c; width: 100%;">
                <i class="bi bi-person-circle"></i>
                <div class="Überschrift">
                    Essensanzahl
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

            <div class="container">
                <form method="post">
                    <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column top-box" style="background: #f0e79c; width: 100%; border: 1px solid black; padding: 10px; margin: 10px;">

                        <?php if ($Mittagessen == null || empty($Mittagessen)) : ?>
                            <p>Kein Mittagessen verfügbar</p>
                        <?php else : ?>
                            <?php foreach ($Mittagessen as $mittagessen) : ?>
                                <div class="form-check">

                                    <label class="form-check-label" for="mittagessenRadioButtons">
                                        <?= htmlspecialchars($mittagessen['gericht']) ?>
                                        <?php
                                        $mittagessencount = $db->countMittagessen($date, htmlspecialchars($mittagessen['gericht']));
                                        // Display the count if it's not null
                                        if ($mittagessencount !== null) {
                                            echo " ($mittagessencount)";
                                        }
                                        ?>
                                    </label>

                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column top-box" style="background: #f0e79c; width: 100%; border: 1px solid black; padding: 10px; margin: 10px;">

                        <?php if ($Abendessen === null || empty($Abendessen)) : ?>
                            <p>Kein Abendessen verfügbar</p>
                        <?php else : ?>
                            <?php foreach ($Abendessen as $abendessen) : ?>
                                <div class="form-check">

                                <label class="form-check-label" for="mittagessenRadioButtons">
                                        <?= htmlspecialchars($abendessen['gericht']) ?>
                                        <?php
                                        $abendessencount = $db->countMittagessen($date, htmlspecialchars($abendessen['gericht']));
                                        // Display the count if it's not null
                                        if ($abendessencount !== null) {
                                            echo " ($abendessencount)";
                                        }
                                        ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- essenanzeige end -->
        </div>
    </div>
</body>

</html>