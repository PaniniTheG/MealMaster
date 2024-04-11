<?php
    // include_once('function/function.php');
    include_once('../../../function/function.php');
    $user_id = $_GET['user_id'];
    setUserId($user_id);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/account.css">
    <title>Konto</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col d-flex justify-content-center align-items-center">
                <div class="box-container">
                    <h1>Konto Optionen</h1>
                    <div class="dropdown" onclick="toggleDropdown(this)">
                        <div class="dropdown-header">Konto Informationen</div>
                        <div class="dropdown-content">
                            <?php $value=getEmail()?>
                            <input type="text" id="mail" value="<?php echo $value; ?>" readonly onclick="stopPropagation(event)" oninput="checkChanges()">
                            
                            <?php $value=getFirstname()?>
                            <input type="text" id="firstname" value="<?php echo $value; ?>" readonly onclick="stopPropagation(event)" oninput="checkChanges()">
                            
                            <?php $value=getLastname()?>
                            <input type="text" id="lastname" value="<?php echo $value; ?>" readonly onclick="stopPropagation(event)" oninput="checkChanges()">
                            
                            <?php $value=getClass()?>
                            <input type="text" id="class" value="<?php echo $value; ?>" readonly onclick="stopPropagation(event)" oninput="checkChanges()">
                            <div class="buttons-container">
                                <button onclick="toggleEditMode(event)">Bearbeiten</button>
                                <button id="saveButton" onclick="saveChanges()" disabled>Speichern</button>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown" onclick="toggleDropdown(this)">
                        <div class="dropdown-header">Neue Benutzer</div>
                        <div class="dropdown-content">
                             <?php
                                makeTableForNewUser();
                            ?>
                        </div>
                    </div>
                    <div class="dropdown" onclick="toggleDropdown(this)">
                        <div class="dropdown-header">Aktive Benutzer</div>
                        <div class="dropdown-content">
                            <?php
                                makeTableForActiveUser();
                            ?>
                        </div>
                    </div>
                    <div class="dropdown" onclick="toggleDropdown(this)">
                        <div class="dropdown-header">Abgelehnte Benutzer</div>
                        <div class="dropdown-content">
                            <?php
                                makeTableForDeactivatedUser();
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var initialFieldValues = {};

        function toggleDropdown(dropdown) {
            var header = dropdown.querySelector('.dropdown-header');
            var isActive = dropdown.classList.contains('active');
            if (!isActive) {
                dropdown.classList.add('active');
                // Save dropdown state to a cookie
                document.cookie = "dropdown_" + header.textContent + "=open";
            } else {
                if (header.contains(event.target)) {
                    dropdown.classList.remove('active');
                    // Remove dropdown state cookie
                    document.cookie = "dropdown_" + header.textContent + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                }
            }
        }

        // Function to get cookie value by name
        function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for(var i = 0; i <ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        // Function to check if a dropdown should be open based on cookies
        function checkDropdownState() {
            var dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(function(dropdown) {
                var header = dropdown.querySelector('.dropdown-header');
                var cookieName = "dropdown_" + header.textContent;
                var cookieValue = getCookie(cookieName);
                if (cookieValue === "open") {
                    dropdown.classList.add('active');
                }
            });
        }

        // Call function to check dropdown state on page load
        checkDropdownState();

        function toggleEditMode(event) {
            event.stopPropagation();
            var dropdownContent = event.target.closest('.dropdown').querySelector('.dropdown-content');
            dropdownContent.classList.toggle('edit-mode');
            var inputs = dropdownContent.querySelectorAll('input');
            inputs.forEach(function(input) {
                input.readOnly = !input.readOnly;
            });
            if (dropdownContent.classList.contains('edit-mode')) {
                storeInitialFieldValues(inputs);
            }
            checkChanges();
        }

        function stopPropagation(event) {
            event.stopPropagation();
        }

        function storeInitialFieldValues(inputs) {
            inputs.forEach(function(input) {
                initialFieldValues[input.placeholder] = input.value;
            });
        }

        function checkChanges() {
            var saveButton = document.getElementById('saveButton');
            var dropdownContent = document.querySelector('.dropdown-content');
            var inputs = dropdownContent.querySelectorAll('input');
            var changed = Array.from(inputs).some(function(input) {
                return input.value !== initialFieldValues[input.placeholder];
            });
            saveButton.disabled = !changed;
        }

        function saveChanges() {
            alert('Änderungen gespeichert!');
            var dropdownContent = document.querySelector('.dropdown-content');
            dropdownContent.classList.remove('edit-mode');
            var inputs = dropdownContent.querySelectorAll('input');
            inputs.forEach(function(input) {
                input.readOnly = true;
            });
            storeInitialFieldValues(inputs);
            checkChanges();
        }
    </script>
</body>
</html>
