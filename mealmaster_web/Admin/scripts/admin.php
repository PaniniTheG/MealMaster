<?php

include_once('../../../function/function.php');

?>

<!DOCTYPE html>
<html lang="de">
 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerichte</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="icon" href="/mealmaster_web/images/Firmenlogo.png" type="image/x-icon">
    <link rel="stylesheet" href="/mealmaster_web/Admin/style/adminS.css" type="text/css">
 
</head>
 
<body class="no-select">

<?php include '../../Navbar/scripts/Navbar.php'; ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-4">
                <div class="sidebar">
                <?php
                    gotoCount();
                ?>

 
                    <form action="" method="POST">
                        <input type="hidden" name="myArray" id="myArrayInput">
                        <div class="esZeit">
                            <select id="essenZeit" name="essenZeit">
                                <option value="mittag" name="mittag">Mittagessen</option>
                                <option value="abend" name="abend">Abendessen</option>
                            </select>
                        </div>
                        <div>
                            <input type="date" id="datum" name="datum" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <button name="menu" value="essenspeichern" id="save-button" class="btn btn-primary" style="position: fixed; bottom: 20px; right: 20px;" onclick="saveMenu()">Speichern</button>
                    </form>

                    <form action="" method="post">
                        <input name="neuspeisen" type="text" class="essnneu" placeholder="Neues Essen"><button name="submit" class="hinzu">+</button>
                    </form>
 
 
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['neuspeisen'])) {
                        $neuesEssen = $_POST['neuspeisen'];
                        insertSpeise($neuesEssen);
                    }
 
                    ausgabeGericht();
 
 
                    ?>
                </div>
            </div>
            <form method="post" action="">
                <div class="col-md-8">
                    <div name="gerichteinfügen" placeholder="Mittagessen" id="drag-drop-field" class="drag-drop-field" ondrop="drop(event)" ondragover="allowDrop(event)">
                        <p class="placeholder-text">Hier Gericht reinziehen</p>
                    </div>
                </div>
            </form>

            <form method="post" action="">
                <button type="submit" name="showCount"class="btn-primary" style="position: fixed; bottom: 230px; right: 550px;"> Angemeldete Schüler anzeigen! </button>
            </form>
        </div>
    </div>
    <?php
    setGerichte();
 
    ?>
    <script>
        let arr = [];
        var maxGerichte = 2;
 
        document.addEventListener('DOMContentLoaded', function() {
            var gerichteInSidebar = document.querySelectorAll('.gericht');
            gerichteInSidebar.forEach(function(gericht) {
                gericht.addEventListener('click', function() {
                    addFromSidebar(gericht.innerText);
                });
            });
        });
 
        function addFromSidebar(gericht) {
            var field = document.getElementById('drag-drop-field');
            if (field.querySelectorAll('.gericht-container').length >= maxGerichte) {
                alert("Es können nur " + maxGerichte + " Gerichte hinzugefügt werden.");
                return;
            }
            var node = document.createElement("div");
            arr.push(gericht)
            node.innerText = gericht;
            node.classList.add('gericht-container');
            field.appendChild(node);
            addRemoveButton(node, gericht);
            document.querySelector('.placeholder-text').style.display = 'none';
        }
 
        function addRemoveButton(node, gericht) {
            var button = document.createElement("button");
            button.innerText = "X";
            button.className = "remove-button";
            button.onclick = function() {
                node.parentNode.removeChild(node);
                if (document.getElementById('drag-drop-field').childElementCount === 0) {
                    document.querySelector('.placeholder-text').style.display = 'block';
                }
                var index = array.indexOf(gericht);
                if (index !== -1) {
                    arr.splice(index, 1);
                }
            };
            node.appendChild(button);
        }
 
        function saveMenu() {
            // Array mit den ausgewählten Gerichten
            var menuItems = document.getElementById('drag-drop-field').children;
            var menuArray = [];
            for (var i = 0; i < menuItems.length; i++) {
                menuArray.push(menuItems[i].innerText.trim());
            }
            // Das Array als JSON-String in das myArray-Feld eintragen
            document.getElementById('myArrayInput').value = JSON.stringify(arr);
            // Das Formular abschicken
            document.getElementById('save-menu-form').submit();
        }
    </script>
</body>
 
</html>