<?php
class DatabaseConnection
{
    private $con;

    function __construct()
    {
        $server = 'localhost:3306';
        $user = 'root';
        $pwd = 'root';
        $schema = 'MealMasterV2';

        try
        {
            $this->con = new PDO('mysql:host='.$server.';dbname='.$schema.';charset=utf8', $user, $pwd);
            $this->con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(Exception $e)
        {
            echo 'Fehler bei der Verbindung zur Datenbank: '.$e->getMessage();
        }
    }

    function checkUser($email, $password)
    {
        try
        {
            $stmt = $this->con->prepare("SELECT COUNT(*) FROM Benutzer WHERE mail = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $checkEmail = $stmt->fetchColumn();

            $stmt=null;

            $stmt = $this->con->prepare("SELECT COUNT(*) FROM Benutzer WHERE passwort = :password");
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->execute();

            $checkPassword = $stmt->fetchColumn();

            return ($checkEmail > 0) && ($checkPassword > 0);
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function checkIfUserAlreadyExists($email){
        try
        {
            $stmt = $this->con->prepare("SELECT COUNT(*) FROM Benutzer WHERE mail = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $checkEmail = $stmt->fetchColumn();

            return ($checkEmail > 0);
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function createNewUser($firstName, $lastName, $class, $email, $password){
        $randomBytes = random_bytes(64);

        $token = bin2hex($randomBytes);

        $sha256Token = hash('sha256', $token);
        
        try
        {
            if($this->checkIfUserAlreadyExists($email)){
                if($this->isAccepted($email,$password)){
                    return false;
                }
                return true;
            }
            else{

                $stmt = $this->con->prepare("Insert into Benutzer(rolle_idrolle, mail, passwort, vname, nname, class, pin) values (3, :email , :password, :firstName, :lastName, :class, $token)");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
                $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
                $stmt->bindParam(':class', $class, PDO::PARAM_STR);
                $stmt->execute();
            }
            return true;
        }
        catch(Exception $e)
        {
        return false;
        }
    }

    function isAccepted($email, $password){
        try
        {
            $stmt = $this->con->prepare("select Count(*) from Benutzer where mail=:email and Rolle_idRolle=3;");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $isAccepted=$stmt->fetchColumn();
            return ($isAccepted <= 0);
        }
        catch(Exception $e)
        {
            return false;
        }
    }

    function sendResetRequest($email, $pin){
        
        global $resetMail;
        $resetMail=$email;

        date_default_timezone_set('Europe/Berlin');

        $expireDate = date("Y-m-d H:i:s", strtotime("+30 minutes"));
        try{
            $stmt = $this->con->prepare("SELECT ben_id FROM benutzer WHERE mail = :email limit 1");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $id = $stmt->fetchColumn();

            $stmt = $this->con->prepare("UPDATE Benutzer SET pin = :pin, ablauf_datum_pin= :expireDate WHERE mail= :email and ben_id= :id");
            $stmt->bindParam(':pin', $pin, PDO::PARAM_STR);
            $stmt->bindParam(':expireDate', $expireDate, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            $stmt->execute();

            return true;


        }
        catch(Exception $e){
            return false;
        }
    }

    function checkPin($pin){
        try
        {
            $stmt = $this->con->prepare("SELECT COUNT(*) FROM Benutzer WHERE pin = :pin");
            $stmt->bindParam(':pin', $pin, PDO::PARAM_STR);
            $stmt->execute();
    
            $checkToken = $stmt->fetchColumn();
    
            $stmt = $this->con->prepare("SELECT ablauf_datum_pin FROM Benutzer WHERE pin = :pin");
            $stmt->bindParam(':pin', $pin, PDO::PARAM_STR);
            $stmt->execute();
    
            $ablauf_datum_token = $stmt->fetchColumn();

            date_default_timezone_set('Europe/Berlin');

            $currentDateTime = date("Y-m-d H:i:s");

            $resetDateTime= date($ablauf_datum_token);
    
            return ($checkToken > 0) && ($resetDateTime > $currentDateTime);
        }
        catch(Exception $e)
        {
            return false;
        }
    }


    function updatePassword($password, $pin){        
        try
        {
            $stmt = $this->con->prepare("UPDATE Benutzer set passwort=:newPW where pin=:pin");
            $stmt->bindParam(':pin', $pin, PDO::PARAM_STR);
            $stmt->bindParam(':newPW', $password, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(Exception $e)
        {
            //TODO
        }
    }
}

?>
