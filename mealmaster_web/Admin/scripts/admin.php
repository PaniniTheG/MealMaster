<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerichte</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="mealmaster_web/Admin/style/adminS.css" rel="stylesheet">
    <style>

    </style>
</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="sidebar">
                <img src="mealmaster_web/images/Firmenlogo.png" class="img-fluid" draggable="false" style="max-width: 100%; height: auto;">
                    <?php 
                    $gerichte = array('Apfelstrudel',
                                        'Bratwurst',                         
                                        'Currywurst',                         
                                        'Döner Kebab',                         
                                        'Dürüm');
                                        sort($gerichte);
                                        $currentLetter = null;
                                        foreach ($gerichte as $gericht) {
                                            $firstLetter = strtoupper(substr($gericht, 0, 1));
                                            if ($firstLetter !== $currentLetter) {
                                                $currentLetter = $firstLetter;
                                                echo "<h2>$currentLetter</h2>";
                                            }
                                            echo "<div class='gericht' draggable='true' ondragstart='drag(event)'>
                                            $gericht<button class='hinzufuegen-button' onclick='addFromSidebar(\"$gericht\")'>+</button></div>";
                                        }                     ?></div>
            </div>
            <div class="col-md-8">
                <div id="drag-drop-field" class="drag-drop-field" ondrop="drop(event)" ondragover="allowDrop(event)">
                    <p class="placeholder-text">Hier Gericht reinziehen</p>
                </div>
                <div><label for="wochentag">Wähle einen Wochentag:</label><select id="wochentag">
                        <option value="Montag">Montag</option>
                        <option value="Dienstag">Dienstag</option>
                        <option value="Mittwoch">Mittwoch</option>
                        <option value="Donnerstag">Donnerstag</option>
                        <option value="Freitag">Freitag</option>
                        <option value="Samstag">Samstag</option>
                        <option value="Sonntag">Sonntag</option>
                    </select></div>
            </div>
        </div>
    </div><button id="save-button" class="btn btn-primary" style="position: fixed; bottom: 20px; right: 20px;" onclick="saveMenu()">Speichern</button>
    <script>
        var maxGerichte = 3;

        function allowDrop(ev) {
            ev.preventDefault();
        }

        function drag(ev) {
            ev.dataTransfer.setData("text", ev.target.innerText);
        }

        function drop(ev) {
            ev.preventDefault();
            var field = ev.target;
            if (field.childElementCount >= maxGerichte) {
                alert("Es können nur zwei Gerichte hinzugefügt werden.");
                return;
            }
            var data = ev.dataTransfer.getData("text");
            var gerichte = Array.from(field.children).map(node => node.innerText.trim());
            if (gerichte.includes(data)) {
                alert("Dieses Gericht wurde bereits hinzugefügt.");
                return;
            }
            var node = document.createElement("div");
            node.innerText = data;
            node.classList.add('gericht-container');
            field.appendChild(node);
            addRemoveButton(node);
            document.querySelector('.placeholder-text').style.display = 'none';
        }

        function addFromSidebar(gericht) {
            var field = document.getElementById('drag-drop-field');
            if (field.querySelectorAll('.gericht-container').length >= 2) {
                alert("Es können nur zwei Gerichte hinzugefügt werden.");
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