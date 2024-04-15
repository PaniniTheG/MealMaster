<?php
class DatabaseConnection
{
    private $con;

    function __construct()
    {
        $server = 'localhost:3306';
        $user = 'root';
        $pwd = 'root';
        $schema = 'mealmasterV2';

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
        $hashedPassword = $this->hash_password($password);
        try
        {
            $stmt = $this->con->prepare("SELECT COUNT(*) FROM Benutzer WHERE mail = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $checkEmail = $stmt->fetchColumn();

            $stmt=null;

            $stmt = $this->con->prepare("SELECT COUNT(*) FROM Benutzer WHERE passwort = :password");
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
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

        $hashedPassword = $this->hash_password($password);
        
        try
        {
            if($this->checkIfUserAlreadyExists($email)){
                if($this->isAccepted($email,$hashedPassword)){
                    return false;
                }
                return true;
            }
            else{

                $stmt = $this->con->prepare("Insert into Benutzer(rolle_idrolle, mail, passwort, vname, nname, class, pin) values (3, :email , :password, :firstName, :lastName, :class, :pin)");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
                $stmt->bindParam(':firstName', $firstName, PDO::PARAM_STR);
                $stmt->bindParam(':lastName', $lastName, PDO::PARAM_STR);
                $stmt->bindParam(':class', $class, PDO::PARAM_STR);
                $stmt->bindParam(':pin', $sha256Token, PDO::PARAM_STR);
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
    function makeStatement($query, $array = null)
    {
        try
        {
            $stmt = $this->con->prepare($query);
            $stmt->execute($array);
            return $stmt;
        } catch(Exception $e)
        {
            return $e;
        }
    }  

    function sendResetRequest($email, $pin){

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
            $hashedPassword = $this->hash_password($password);

            $stmt = $this->con->prepare("UPDATE Benutzer set passwort=:newPW where pin=:pin");
            $stmt->bindParam(':pin', $pin, PDO::PARAM_STR);
            $stmt->bindParam(':newPW', $hashedPassword, PDO::PARAM_STR);
            $stmt->execute();
        }
        catch(Exception $e)
        {
            //TODO
        }
    }

    function hash_password($pw){
        $hashedPassword = hash('sha256', trim($pw));

        return $hashedPassword;

    }
    

    function insertNewGericht($gericht)
    {
 
        if ($gericht != null){
            $stmt = $this->con->prepare("INSERT INTO gericht (gericht) VALUES (:gericht)");
            $stmt->bindParam(':gericht', $gericht);
            $stmt->execute();
 
        }
       
    }
 
    function getGericht()
    {    
            $stmt = $this->con->prepare("SELECT gericht FROM gericht ORDER BY gericht");
            $stmt->execute();
            return $stmt;    
 
    }
 
    function insertNewMittag($date, $idgericht)
    {
        $stmt = $this->con->prepare("INSERT INTO mittagessen (MittagessenDate, gericht_idgericht) VALUES (:MittagessenDate, :idgericht)");
        $stmt->bindParam(':idgericht', $idgericht, PDO::PARAM_INT);
        $stmt->bindParam(':MittagessenDate', $date, PDO::PARAM_STR);
        $stmt->execute();
    }
 
    function insertNewAbend($date, $idgericht)
    {
            $stmt = $this->con->prepare("INSERT INTO abendessen (AbendessenDate, gericht_idgericht) VALUES (:AbendessenDate, :idgericht)");
            $stmt->bindParam(':idgericht', $idgericht, PDO::PARAM_INT);
            $stmt->bindParam(':AbendessenDate', $date, PDO::PARAM_STR);
            $stmt->execute();        
    }
 
    function getGerichteID($gerichte)
    {
        $id = array();
       
        foreach($gerichte as $gericht)
        {
            $stmt = $this->con->prepare("SELECT idgericht FROM gericht WHERE gericht = :gerichtName");
            $stmt->bindParam(':gerichtName', $gericht);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
 
            $id[] = $result['idgericht'];
        }
        return $id;
    }
 
    function getMittagAnmeldeAnz()
    {
        $stmt = $this->con->prepare("SELECT count(*) FROM abendessen");
        $stmt->execute();
        return $stmt;
    }
 
    function getAbendAnmeldeAnz()
    {
        $stmt = $this->con->prepare("SELECT count(*) FROM mittagessen");
        $stmt->execute();
        return $stmt;
    }
 
    function getAbendGericht()
    {    
            $stmt = $this->con->prepare("SELECT Gericht FROM gericht ");
            $stmt->execute();
            return $stmt;    
 
    }
 
    function getMittagGericht()
    {    
            $stmt = $this->con->prepare("SELECT Gericht FROM gericht ");
            $stmt->execute();
            return $stmt;    
 
    }

    function getCurrentMittagessen($date)
    {
        $stmt = $this->con->prepare("SELECT gericht from gericht g, mittagessen m where g.idgericht = m.gericht_idgericht and m.MittagessenDate = '$date'");

        $stmt->execute();

        // holen aller gerichte
        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($menus != null) {
            return $menus;
        } else {
            return false;
        }
    }

    function getCurrentAbendessen($date)
    {
        $stmt = $this->con->prepare("SELECT gericht from gericht g, abendessen m where g.idgericht = m.gericht_idgericht and m.AbendessenDate = '$date'");

        $stmt->execute();

        // holen aller gerichte
        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($menus != null) {
            return $menus;
        } else {
            return false;
        }
    }


    function insertSpeiseplanUser($date, $mittagessenName, $abendessenName, $ben_id)
    {
        $mittagessenId = null;
        $abendessenId = null;

        if ($mittagessenName != null) {
            $mittagessenIdStmt = $this->con->prepare("SELECT m.idMittagessen FROM mittagessen m JOIN gericht g ON m.idMittagessen = g.idgericht WHERE g.gericht = :mittagessenName");
            $mittagessenIdStmt->execute(array(':mittagessenName' => $mittagessenName));
            $mittagessenIdResult = $mittagessenIdStmt->fetch();

            // Check if any rows were fetched
            if ($mittagessenIdResult !== false) {
                $mittagessenId = $mittagessenIdResult['idMittagessen'];
            } else {
                // Handle the case where no rows were fetched
                // For example, throw an exception or set a default value
            }
        }

        if ($abendessenName != null) {
            $abendessenIdStmt = $this->con->prepare("SELECT a.idAbendessen FROM abendessen a JOIN gericht g ON a.idAbendessen = g.idgericht WHERE g.gericht = :abendessenName");
            $abendessenIdStmt->execute(array(':abendessenName' => $abendessenName));
            $abendessenIdResult = $abendessenIdStmt->fetch();

            // Check if any rows were fetched
            if ($abendessenIdResult !== false) {
                $abendessenId = $abendessenIdResult['idAbendessen'];
            } else {
                // Handle the case where no rows were fetched
                // For example, throw an exception or set a default value
            }
        }

        $stmt = $this->con->prepare("INSERT INTO speiseplanuser (SpeiseplanUser_id, speiseplanUserDate, abendessen_idabendessen, mittagessen_idmittagessen, benutzer_ben_id) VALUES (null, :date, :abendessenId, :mittagessenId, :ben_id)");
        $stmt->execute(array(':date' => $date, ':abendessenId' => $abendessenId, ':mittagessenId' => $mittagessenId, ':ben_id' => $ben_id));
    }

    function getMittagessenUser($ben_id, $date)
    {
        $stmt = $this->con->prepare("SELECT g.gericht FROM speiseplanuser su LEFT JOIN mittagessen m ON su.mittagessen_idMittagessen = m.idMittagessen LEFT JOIN gericht g ON g.idgericht = m.gericht_idgericht WHERE su.speiseplanuserDate = :currentdate AND su.benutzer_ben_id = :ben_id;");
        $stmt->bindParam(':currentdate', $date);
        $stmt->bindParam(':ben_id', $ben_id);
        $stmt->execute();

        $mittagessenuser = $stmt->fetchAll(PDO::FETCH_ASSOC);


        if (empty($mittagessenuser)) {
            return null;
        } else {
            foreach ($mittagessenuser as $mittagessen) {
                if (empty($mittagessen['gericht'])) {
                    return array('message' => 'There is no abendessen set');
                }
            }
            return $mittagessenuser;
        }
    }
    function getAbendessenUser($ben_id, $date)
    {
        $stmt = $this->con->prepare("SELECT g.gericht FROM speiseplanuser su LEFT JOIN abendessen a ON su.abendessen_idAbendessen = a.idAbendessen LEFT JOIN gericht g ON g.idgericht = a.gericht_idgericht WHERE su.speiseplanuserDate = :currentdate AND su.benutzer_ben_id = :ben_id;");
        $stmt->bindParam(':currentdate', $date);
        $stmt->bindParam(':ben_id', $ben_id);
        $stmt->execute();

        $abendessenuser = $stmt->fetchAll(PDO::FETCH_ASSOC);


        if (empty($abendessenuser)) {
            return null;
        } else {
            foreach ($abendessenuser as $abendessen) {
                if (empty($abendessen['gericht'])) {
                    return array('message' => 'There is no abendessen set');
                }
            }
            return $abendessenuser;
        }
    }
    function countMittagessen($date, $mittagessenName)
    {
        // Count the number of users who have selected Mittagessen on the given date
        $stmtMittagessenCount = $this->con->prepare("SELECT COUNT(*) AS mittagessenCount FROM speiseplanuser su 
        LEFT JOIN mittagessen m ON su.mittagessen_idMittagessen = m.idMittagessen
        LEFT JOIN gericht g ON m.gericht_idgericht = g.idgericht
        WHERE su.speiseplanuserDate = :date AND g.gericht = :mittagessenName");
        $stmtMittagessenCount->execute(array(':date' => $date, ':mittagessenName' => $mittagessenName));
        $mittagessenCount = $stmtMittagessenCount->fetch(PDO::FETCH_ASSOC)['mittagessenCount'];
        return $mittagessenCount;
    }

    function countAbendessen($date, $abendessenName)
    {
        // Count the number of users who have selected Abendessen on the given date
        $stmtAbendessenCount = $this->con->prepare("SELECT COUNT(*) AS abendessenCount FROM speiseplanuser su 
        LEFT JOIN abendessen a ON su.abendessen_idAbendessen = a.idAbendessen
        LEFT JOIN gericht g ON a.gericht_idgericht = g.idgericht
        WHERE su.speiseplanuserDate = :date AND g.gericht = :abendessenName");
        $stmtAbendessenCount->execute(array(':date' => $date, ':abendessenName' => $abendessenName));
        $abendessenCount = $stmtAbendessenCount->fetch(PDO::FETCH_ASSOC)['abendessenCount'];

        return $abendessenCount;
    }
}

?>
