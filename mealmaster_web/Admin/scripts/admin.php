<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerichte</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="mealmaster_web/Admin/style/adminS.css" type="text/css">
    <style>

    </style>
</head>

<body class="no-select">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="sidebar">
                    <div class="esZeit">
                        <select id="essenZeit">
                            <option name="mittag" value="Montag">Mittagessen</option>
                            <option name="abend" value="Dienstag">Abendessen</option>
                        </select>
                    </div>

                    <?php
                    if (isset($_POST['date'])) {
                        $date = $_POST['date'];
                    } else {
                        $date = date('Y-m-d'); // Default to 7 days from now
                    }
                    ?>
                    <script>
                        document.getElementById('datePicker').addEventListener('change', function() {

                            document.getElementById('dateForm').submit();

                        });
                    </script>
                    <form id="dateForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="date" name="date" id="datePicker" value="<?php echo $date; ?>" max="<?php echo date('Y-m-d', strtotime('+3 weeks')); ?>">
                    </form>
                    <!-- datepicker end -->
                    <form action="" method="post">
                        <input name="neuspeisen" type="text" class="essnneu" placeholder="Neues Essen"><button name="submit" class="hinzu">+</button>
                    </form>

                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $neuesEssen = $_POST['neuspeisen'];
                        insertSpeise($neuesEssen);
                    }

                    ausgabeGericht();


                    ?>
                </div>
            </div>
            <div class="col-md-8">
                <div name="gerichteinfügen" placeholder="Mittagessen" id="drag-drop-field" class="drag-drop-field" ondrop="drop(event)" ondragover="allowDrop(event)">
                    <p class="placeholder-text">Hier Gericht reinziehen</p>
                </div>
            </div>
        </div>
    </div>
    <button name="menu" value="essenspeichern" id="save-button" class="btn btn-primary" style="position: fixed; bottom: 20px; right: 20px;" onclick="saveMenu()">Speichern</button>

    <?php

    if (isset($_POST['menu']) && isset($_POST['mittag']) && isset($_POST['date'])) {
        $menuid = $_POST['gerichteinfügen'];
        $date = $_POST['date'];

        insertMittag($menuid, $date);
          
    } 

    if (isset($_POST['menu']) && isset($_POST['abend']) && isset($_POST['date'])) {
        $menuid = $_POST['gerichteinfügen'];
        $date = $_POST['date'];

        insertAbend($menuid, $date);
     
    } 
    ?> 
    <script>
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
            node.innerText = gericht;
            node.classList.add('gericht-container');
            field.appendChild(node);
            addRemoveButton(node);
            document.querySelector('.placeholder-text').style.display = 'none';
        }

        function addRemoveButton(node) {
            var button = document.createElement("button");
            button.innerText = "X";
            button.className = "remove-button";
            button.onclick = function() {
                node.parentNode.removeChild(node);
                if (document.getElementById('drag-drop-field').childElementCount === 0) {
                    document.querySelector('.placeholder-text').style.display = 'block';
                }
            };
            node.appendChild(button);
        }

        function saveMenu() {
            var menuItems = document.getElementById('drag-drop-field').children;
            var menu = [];
            var wochentag = document.getElementById('wochentag').value;
            for (var i = 0; i < menuItems.length; i++) {
                menu.push(menuItems[i].innerText.trim());
            }
            alert("Das Menü für " + wochentag + " wurde gespeichert: " + JSON.stringify(menu));
        }
    </script>
</body>

</html>