<?php
    if (empty($_POST['page'])) {
        include('index.html');
        exit();
    }

    require('Modal.php');

    if($_POST['page'] == 'index')
    {
        switch($_POST['command']) {
            case 'signUp':
                $validate = checkValid($_POST['username'], $_POST['password'], $_POST['email']);
                if ($validate['badPassword'] === 'true' || $validate['badUsername'] === 'true' || $validate['badEmail'] === 'true') {
                    echo json_encode($validate);
                }
                else {
                    addUser($_POST['username'], $_POST['password'], $_POST['email']);
                    echo 'userCreated';
                }
                exit();
                break;

            case 'login':
                if (validLogin($_POST['username'], $_POST['password'])) {
                    session_start();
                    $_SESSION['signed'] = 'YES';
                    $_SESSION['username'] = $_POST['username'];
                    $_SESSION['experience'] = 'none';
                    $_SESSION['group-by'] = 'none';
                    $_SESSION['search'] = 'none';
                    include('HTML_Pages/Start_Page.html');
                }
                else {
                    echo 'false';
                }
                exit();
                break;

            default:
                echo "Unknown command from index Page<br>";
                exit();
                break;
        }
    }
?>