<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Logging Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link href="mealmaster_web/auth/style/LogIn.css" rel="stylesheet">
</head>

<body>
        <div class="container d-flex justify-content-center align-items-center min-vh-100">
            <div class="row border rounded-5 p-3 bg-white shadow box-area">
                <!--Left side-->
                <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #f0e79c">
                    <div class="featured-image mb-3">
                        <img src="mealmaster_web/images/Firmenlogo.png" class="img-fluid" style="width: 250px;">
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
                        <p>Bitte geben Sie ihre Daten ein</p>
                      </div>
                      <form method="post">
                        <div class="input-group mb-3">
                         <input type="text" name="email" class="form-control form-control-lg bg-light fs-6" placeholder="Email">
                        </div>
                        <div class="input-group mb-1">
                         <input type="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password">
                        </div>
                        <div class="input-group mb-3">
                         <button class="btn btn-lg btn-primary w-100 fs-6">Login</button>
                        </div>
                      </form>
                      <div class="row">
                        <small>Noch keinen Account? <a href="#">Registrieren</a></small>
                      </div>
                </div>
                <!--Right side end-->
            </div>
        </div>

</body>
 <!--https://www.youtube.com/watch?v=oF28ns9eVdc tut fertig, 
  nächste schritte:
    -Datenbank verbindung erstellen einbinden
    -registrieren seite einbinden
-->
</html>