<?php
include_once('../../../function/function.php');
setUserId();

logout();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../style/account.css">
    <title>Konto</title>
    <form action="" method="post" id="hiddenForm">
        <input type="hidden" name="hidden_mail" id="hidden_mail">
        <input type="hidden" name="hidden_firstname" id="hidden_firstname">
        <input type="hidden" name="hidden_lastname" id="hidden_lastname">
        <input type="hidden" name="hidden_class" id="hidden_class">
    </form>
</head>
<body>
<?php include '../../Navbar/scripts/Navbar.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col d-flex justify-content-center align-items-center">
            <div class="box-container">
                <h1>Konto Optionen</h1>
                <div class="dropdown" onclick="toggleDropdown(this)">
                    <div class="dropdown-header">Konto Informationen</div>
                    <div class="dropdown-content">
                        <?php $value=getEmail()?>
                        <input type="text" name="mail" id="mail" value="<?php echo $value; ?>" readonly onclick="stopPropagation(event)" oninput="checkChanges()">

                        <?php $value=getFirstname()?>
                        <input type="text" name="firstname" id="firstname" value="<?php echo $value; ?>" readonly onclick="stopPropagation(event)" oninput="checkChanges()">

                        <?php $value=getLastname()?>
                        <input type="text" name="lastname" id="lastname" value="<?php echo $value; ?>" readonly onclick="stopPropagation(event)" oninput="checkChanges()">

                        <?php $value=getClass()?>
                        <input type="text" name="class" id="school_class" value="<?php echo $value; ?>" readonly onclick="stopPropagation(event)" oninput="checkChanges()">

                        <div class="buttons-container">
                            <button onclick="toggleEditMode(event)">Bearbeiten</button>
                            <button name="saveButton" id="saveButton" onclick="saveChanges()" disabled>Speichern</button>
                        </div>

                        <?php saveUserData(); ?>

                    </div>
                </div>

                <?php if(isAdmin()){?>

                <div class="dropdown" onclick="toggleDropdown(this)">
                    <div class="dropdown-header">Neue Benutzer</div>
                    <div class="dropdown-content">
                        <!-- Suchbalken -->
                        <div class="search-container">
                            <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Nach E-Mail suchen...">
                        </div>
                        <?php
                            makeTableForNewUser();
                        ?>
                    </div>
                </div>
                <div class="dropdown" onclick="toggleDropdown(this)">
                    <div class="dropdown-header">Aktive Benutzer</div>
                    <div class="dropdown-content">
                    <!-- Suchbalken für die zweite Tabelle -->
                    <div class="search-container">
                        <input type="text" id="searchInput2" onkeyup="searchTable2('.table2')" placeholder="Nach E-Mail suchen...">
                    </div>
                        <?php
                            makeTableForActiveUser();
                        ?>
                    </div>
                </div>
                <div class="dropdown" onclick="toggleDropdown(this)">
                    <div class="dropdown-header">Abgelehnte Benutzer</div>
                    <div class="dropdown-content">
                    <!-- Suchbalken für die zweite Tabelle -->
                    <div class="search-container">
                        <input type="text" id="searchInput3" onkeyup="searchTable3('.table3')" placeholder="Nach E-Mail suchen...">
                    </div>
                        <?php
                            makeTableForDeactivatedUser();
                        ?>
                    </div>
                </div>
                <?php }?>
            </div>
        </div>
    </div>
</div>
<form action="" method="post">
    <div style="display: flex; justify-content: flex-end;">
        <button class="btn btn-lg btn-primary fs-6 mb-2 mr-2" name="logout" id="responsiveBtn">Abmelden</button>
    </div>
</form>

<script>
    var initialFieldValues = {};

    window.addEventListener('DOMContentLoaded', function() {
    var button = document.getElementById('responsiveBtn');
    var buttonWidth = button.offsetWidth;
    var buttonTextWidth = button.scrollWidth;
    
    if (buttonTextWidth > buttonWidth) {
        button.style.width = buttonTextWidth + 'px';
    }
    });

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
        } else {
            checkChanges();
        }
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
        var dropdownContent = document.querySelector('.dropdown.active .dropdown-content');
        var inputs = dropdownContent.querySelectorAll('input');
        var changed = Array.from(inputs).some(function(input) {
            return input.value !== initialFieldValues[input.placeholder];
        });
        saveButton.disabled = !changed;
    }

    function saveChanges() {
        var dropdownContent = document.querySelector('.dropdown.active .dropdown-content');
        var inputs = dropdownContent.querySelectorAll('input');
        var hiddenForm = document.getElementById('hiddenForm');
        inputs.forEach(function(input) {
            var hiddenInput = hiddenForm.querySelector('#hidden_' + input.placeholder.toLowerCase());
            hiddenInput.value = input.value;
        });
        hiddenForm.submit();
    }

    // Suchfunktion für die Tabelle
    function searchTable() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput");
        filter = input.value.toUpperCase();
        table = document.querySelector('.table1');
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1]; // Ändere die Zahl, wenn die E-Mail-Spalte an einer anderen Position ist
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    function searchTable2() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput2");
        filter = input.value.toUpperCase();
        table = document.querySelector('.table2');
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1]; // Ändere die Zahl, wenn die E-Mail-Spalte an einer anderen Position ist
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }

    function searchTable3() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchInput3");
        filter = input.value.toUpperCase();
        table = document.querySelector('.table3');
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1]; // Ändere die Zahl, wenn die E-Mail-Spalte an einer anderen Position ist
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    }
</script>

</body>
</html>
