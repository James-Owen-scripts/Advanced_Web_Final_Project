<?php
    $JSON_File = file_get_contents('Resources/DATA/DataBase.JSON');
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

    // Function to get all user posts
    function getFilteredPosts($term, $experience, $order) {
        global $conn;
        global $JSON_DataBase_Info;
        $sql = $JSON_DataBase_Info['userPosts']['getFilteredPosts'][$order];
        // replace the term
        $sql = str_replace('[TERM]', $term, $sql);
        // if user doesn't set the experience of the posters filter
        if ($experience == '') {
            $sql = str_replace('[EXPERIENCE]', '', $sql);
        }
        else {
            $sql = str_replace('[EXPERIENCE]', $JSON_DataBase_Info['userPosts']['getFilteredPosts']['experience'], $sql);
        }
        $result = mysqli_query($conn, $sql);
        $rows = [];
        $i = 0;
        while($row = mysqli_fetch_assoc($result)) {
            $rows[$i++] = $row;
        }
        return $rows;
    }

    // Function to create a post
    function createPost($u, $title, $content){
        global $conn;
        global $JSON_DataBase_Info;
        $sql = $JSON_DataBase_Info['userPosts']['createPost'];

        // replace with correct data
        $sql = str_replace('[USERNAME]', $u, $sql);
        $sql = str_replace('[TITLE]', $title, $sql);
        $sql = str_replace('[CONTENT]', $content, $sql);
        $sql = str_replace('[DATE]', date("Ymd"), $sql);

        mysqli_query($conn, $sql);
    }

    // function to get post and comments
    function getFullPost($postId) {
        global $conn;
        global $JSON_DataBase_Info;
        $sql = $JSON_DataBase_Info['userPosts']['getPost'];
        $sql = str_replace('[POSTID]', $postId, $sql);

        // get the post selected
        $post = mysqli_fetch_assoc(mysqli_query($conn, $sql));

        // get comments
        $sql = $JSON_DataBase_Info['userComments']['allPostComments'];
        $sql = str_replace('[POSTID]', $postId, $sql);

        $result = mysqli_query($conn, $sql);
        $comments = [];
        $i = 0;
        while($row = mysqli_fetch_assoc($result)) {
            $comments[$i++] = $row;
        }

        // return all data
        return array('post' => $post, 'comments' => $comments);
    }

    // function to create a comment on a post
    function createComment($u, $postId, $comment) {
        global $conn;
        global $JSON_DataBase_Info;
        $date = date("Ymd");

        $sql = $JSON_DataBase_Info['userComments']['insertAComment'];
        $sql = str_replace('[USERNAME]', $u, $sql);
        $sql = str_replace('[POSTID]', $postId, $sql);
        $sql = str_replace('[COMMENT]', $comment, $sql);
        $sql = str_replace('[DATE]', $date, $sql);

        mysqli_query($conn, $sql);
    }
?>