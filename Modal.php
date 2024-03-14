<?php
    $JSON_File = file_get_contents('DataBase.JSON');
    $JSON_DataBase_Info = json_decode($JSON_File, true);

    $connectObj = $JSON_DataBase_Info['LocalDB'];
    $conn = mysqli_connect($connectObj['Host'], $connectObj['User'], $connectObj['Password'], $connectObj['database']);

    // check if user data entered is valid
    function checkValid($u, $p, $e) {
        global $conn;
        global $JSON_DataBase_Info;
        $queries = $JSON_DataBase_Info['validationQueries'];
        $validated = array('badUsername' => 'false', 'badPassword'=>'false', 'badEmail' => 'false');
        
        // validate password length
        if (strlen($p) < 8) {
            $validated['badPassword'] = 'true';
        }

        // validate username
        $result = mysqli_query($conn, str_replace('[USERNAME]', $u, $queries['username']));
        if (mysqli_num_rows($result) > 0) {
            $validated['badUsername'] = 'true';
        }
        // validate email
        $result = mysqli_query($conn, str_replace('[EMAIL]', $e, $queries['email']));
        if (mysqli_num_rows($result) > 0) {
            $validated['badEmail'] = 'true';
        }

        return $validated;
    }

    // add user
    function addUser($u, $p, $e) {
        global $conn;
        global $JSON_DataBase_Info;
        $sql = $JSON_DataBase_Info['createUser'];

        $sql = str_replace('[USERNAME]', $u, $sql);
        $sql = str_replace('[PASSWORD]', $p, $sql);
        $sql = str_replace('[EMAIL]', $e, $sql);
        $sql = str_replace('[DATE]', date("Ymd"), $sql);

        mysqli_query($conn, $sql);
    }

    // check user login
    function validLogin($u, $p) {
        global $conn;
        global $JSON_DataBase_Info;
        $sql = $JSON_DataBase_Info['validationQueries']['login'];

        // replace temparary string data
        $sql = str_replace('[USERNAME]', $u, $sql);
        $sql = str_replace('[PASSWORD]', $p, $sql);

        // validate username
        $result = mysqli_query($conn, $sql);
        
        return mysqli_num_rows($result) == 1;
    }
?>