<?php
class DatabaseConnection
{
    private $con;

    public function __construct()
    {
        $server = 'localhost:3306';
        $user = 'root';
        $pwd = 'root';
        $schema = 'pptest';

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

    public function checkUser($email, $password)
    {
        try
        {
            $stmt = $this->con->prepare("SELECT COUNT(*) FROM login WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $checkEmail = $stmt->fetchColumn();

            $stmt=null;

            $stmt = $this->con->prepare("SELECT COUNT(*) FROM login WHERE password = :password");
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->execute();

            $checkPassword = $stmt->fetchColumn();

            return ($checkEmail > 0) && ($checkPassword > 0);
        }
        catch(Exception $e)
        {
            echo 'Fehler beim Überprüfen des Benutzernamens: '.$e->getMessage();
            return false;
        }
    }
}

/*$db = new DatabaseConnection($server, $user, $pwd, $schema);

$email = 'johannesderhuan@gmail.com';
if ($db->checkUser($email, 'haha13')) {
    echo "accepted";
} else {
    echo "not accepted";
}*/
?>
