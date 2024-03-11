<?php
class DatabaseConnection
{
    private $con;

    function __construct()
    {
        $server = 'localhost:3306';
        $user = 'root';
        $pwd = 'root';
        $schema = 'mealmaster';

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
        try
        {
            if($this->checkIfUserAlreadyExists($email)){
                if($this->isAccepted($email,$password)){
                    return false;
                }
                return true;
            }
            else{
                $stmt = $this->con->prepare("Insert into Benutzer(Rolle_idRolle, mail, passwort, vname, nname, class) values (3, :email , :password, :firstName, :lastName, :class)");
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
}
?>
