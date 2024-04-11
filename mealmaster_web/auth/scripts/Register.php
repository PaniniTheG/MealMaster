<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registrierungsseite</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link href="mealmaster_web/auth/style/Register.css" rel="stylesheet">
  <style>
    /* Hier können Sie zusätzliche CSS-Anpassungen vornehmen */
  </style>
</head>

<body>
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row border rounded-5 p-3 bg-white shadow box-area">
      <!-- Linke Seite -->
      <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #f0e79c">
        <div class="featured-image mb-3">
          <img src="mealmaster_web/images/Firmenlogo.png" class="img-fluid" style="max-width: 100%; height: auto;">
        </div>
        <p class="fs-2" style="font-family: 'Courier New', Courier, monospace; font-weight: 600;">Jetzt anschauen</p>
        <small class="text-wrap text-center" style="width: 17rem;font-family: 'Courier New', Courier, monospace;">Erhalten Sie einen Überblick über den Speiseplan</small>
      </div>
      <!-- Ende Linke Seite -->

      <!-- Rechte Seite -->
      <div class="col-md-6 right-box">
        <div class="row align-items-center">
          <div class="header-text mb-1"></div-header-text>
          <h2>Hallo</h2>
          <p>Bitte geben Sie Ihre Daten ein</p>
        </div>

        <!-- PHP-Registrierungsfunktion -->
        <?php
          registerNewUser();
        ?>

        <!-- Registrierungsformular -->
        <form action="" method="post" onsubmit="return validateForm()">
          <div class="input-group mb-1">
            <input type="text" name="firstName" class="form-control form-control-lg bg-light fs-6" placeholder="Vorname" required>
          </div>
          <div class="input-group mb-1">
            <input type="text" name="lastName" class="form-control form-control-lg bg-light fs-6" placeholder="Nachname" required>
          </div>
          <div class="input-group mb-1">
            <input type="text" name="class" class="form-control form-control-lg bg-light fs-6" placeholder="Klasse" required>
          </div>
          <div class="input-group mb-1">
            <input type="text" name="email" class="form-control form-control-lg bg-light fs-6" placeholder="Email" required>
          </div>
          <div class="input-group mb-1">
            <input type="password" id="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Passwort" required>
          </div>
          <div class="input-group mb-3">
            <input type="password" name="repeatPassword" class="form-control form-control-lg bg-light fs-6" placeholder="Passwort wiederholen" required>
          </div>
          <div class="input-group mb-3">
            <button class="btn btn-lg btn-primary w-100 fs-6" name="register">Registrieren</button>
          </div>
        </form>
        <!-- Ende Registrierungsformular -->

        <div class="row">
          <small>Bereits einen Account? <a href="index.php?site=LogIn">Anmelden</a></small>
        </div>
      </div>
      <!-- Ende Rechte Seite -->
    </div>
  </div>

  <!-- POP-UP-Bereich -->
  <div id="popup" style="display:none;">
    <button class="close-btn" onclick="closePopup()">X</button>
    <!-- POP-UP-Inhalt Start -->
    <div class="row align-items-center">
      <form action="" id="resetForm" method="post">
        <p>Das Passwort muss mindestens 6 Zeichen lang sein, Groß- und Kleinschreibung enthalten, mindestens eine Zahl und ein Sonderzeichen haben.</p>
      </form>
    </div>
    <!-- POP-UP-Inhalt Ende -->
  </div>
  <!-- Ende POP-UP-Bereich -->

  <div id="overlay" style="display:none;" onclick="closePopup()"></div>

  <script>
    function showPopup() {
      document.getElementById('popup').style.display = 'block';
      document.getElementById('overlay').style.display = 'block';
      document.querySelector('.close-btn').style.display = 'block';
      document.body.style.overflow = 'hidden';
    }

    function closePopup() {
      document.getElementById('popup').style.display = 'none';
      document.getElementById('overlay').style.display = 'none';
      document.querySelector('.close-btn').style.display = 'none';
      document.body.style.overflow = 'auto';
    }

    function validateForm() {
      var password = document.getElementById("password").value;
      var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_])[A-Za-z\d@$!%*?&_]{6,}$/;

      if (!passwordPattern.test(password)) {
        showPopup();
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
