<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link href="mealmaster_web/auth/style/LogIn.css" rel="stylesheet">
  <link rel="icon" href="/mealmaster_web/images/Firmenlogo.png" type="image/x-icon">
</head>

<body>
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="row border rounded-5 p-3 bg-white shadow box-area">
                <!--Left side-->
                <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #f0e79c">
                    <div class="featured-image mb-3">
                        <img src="mealmaster_web/images/Firmenlogo.png" class="img-fluid" style="max-width: 100%; height: auto;">
                    </div>
                    <p class="fs-2" style="font-family: 'Courier New', Courier, monospace; font-weight: 600;">Jetzt anschauen</p>
                    <small class="text-wrap text-center" style="width: 17rem;font-family: 'Courier New', Courier, monospace;">Erhalten Sie einen Überblick über den Speiseplan</small>
                </div>
                <!--Left side end-->

                <!--Right side-->
                <div class="col-md-6 right-box">
                  <div class="row align-items-center">
                      <div class="header-text mb-1"></div-header-text>
                        <h2>Hallo</h2>
                        <p>Bitte geben Sie Ihr neues Passwort ein!</p>
                      </div>
                      <?php
                        resetPassword();
                      ?>
                      <!--Reset Form-->
                      <form action="" method="post" onsubmit="return validateForm()">
                        <div class="input-group mb-1">
                          <input type="number" name="pin" class="form-control form-control-lg bg-light fs-6" placeholder="PIN" required>
                        </div>
                        <div class="input-group mb-1">
                         <input type="password" id="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Passwort" required>
                        </div>
                        <div class="input-group mb-3">
                         <input type="password" name="repeadPassword" class="form-control form-control-lg bg-light fs-6" placeholder="Passwort wiederholen" required>
                        </div>
                        <div class="input-group mb-3">
                         <button class="btn btn-lg btn-primary w-100 fs-6" name="reset">Passwort zurücksetzen</button>
                        </div>
                      </form>
                      <!--Reset Form End-->
                </div>
                <!--Right side end-->
            </div>
        </div>


<!-- POP-UP Space -->
<div id="popup">
    <button class="close-btn" onclick="closePopup()">X</button>
    <!-- POP-UP Inhalt Start -->
      <div class="row align-items-center">
        <form action="" id="resetForm" method="post">
          <p>Das Passwort muss mindestens 6 Zeichen lang sein, Groß- und Kleinschreibung enthalten, mindestens eine Zahl und ein Sonderzeichen haben.</p>
        </form>
      </div>
    <!-- POP-UP Inhalt Ende -->
  </div>

  <!-- POP-UP Space Ende -->

<div id="overlay" onclick="closePopup()"></div>
  
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
    var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/;

    if (!passwordPattern.test(password)) {
      showPopup();
      return false;
    }
    return true;
  }
  </script>

</body>
</html>