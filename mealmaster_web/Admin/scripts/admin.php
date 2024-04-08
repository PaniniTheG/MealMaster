<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerichte</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="mealmaster_web/Admin/style/adminS.css">
    <style>

    </style>
</head>

<body class="no-select">

    <div class="woTag">
        <label for="wochentag">Wähle einen Wochentag:</label>
        <select id="wochentag">
            <option value="Montag">Montag</option>
            <option value="Dienstag">Dienstag</option>
            <option value="Mittwoch">Mittwoch</option>
            <option value="Donnerstag">Donnerstag</option>
            <option value="Freitag">Freitag</option>

        </select>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="sidebar">
                <div class="esZeit">
                    <select id="essenZeit">
                    <option value="Montag">Mittagessen</option>
                    <option value="Dienstag">Abendessen</option>
                </select>
                </div>
                    <img src="mealmaster_web/images/Firmenlogo.png" class="img-fluid" alt="Responsive image" border="4">
                    <input name="neuspeisen" type="text" class="essnneu" placeholder="Neues Essen"><button name="submit" class="hinzu">+</button>
                    <?php
                       
                    /*$gericht = array(

                        'Apfelstrudel',
                        'Bratwurst',
                        'Currywurst',
                        'Döner Kebab',
                        'Dürüm'
                    );*/

                    //insertNewGericht();
                    sort($gericht);
                    $currentLetter = null;
                    foreach ($gericht as $gerichte) {
                        $firstLetter = strtoupper(substr($gerichte, 0, 1));
                        if ($firstLetter !== $currentLetter) {
                            $currentLetter = $firstLetter;
                            echo "<h2>$currentLetter</h2>";
                        }
                        echo "<div class='gericht' draggable='true' ondragstart='drag(event)'>$gerichte</div>";
                    }
                    ?>
            </div>
            </div>
            <div class="col-md-8">
                <div placeholder="Mittagessen" id="drag-drop-field" class="drag-drop-field" ondrop="drop(event)" ondragover="allowDrop(event)">
                    <p class="placeholder-text">Hier Gericht reinziehen</p>
                </div>
            </div>
        </div>
    </div>
    <button name="menu" value="essenspeichern" id="save-button" class="btn btn-primary" style="position: fixed; bottom: 20px; right: 20px;" onclick="saveMenu()">Speichern</button>
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