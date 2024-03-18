<?php
    if (empty($_POST['page'])) {
        session_start();
        if (!empty($_SESSION['signed'])) {
            include('HTML_Pages/Start_Page.html');
            exit();
        }
        else {
            session_unset();
            session_destroy();
            include('index.html');
            exit();
        }
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
                    // set filters for search
                    $_SESSION['experience'] = '';
                    $_SESSION['order'] = 'Newest-To-Oldest';
                    $_SESSION['search'] = '';
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
    else if($_POST['page'] == 'startPage') {

        session_start();

        switch($_POST['command']) {
            case 'logOut':
                session_unset();
                session_destroy();
                include('index.html');
                exit();
                break;

            case 'profile':
                include('HTML_Pages/Profile_Page.html');
                exit();
                break;

            case 'myPosts':
                include('HTML_Pages/myPosts_Page.html');
                exit();
                break;

            case 'getPosts':
                echo json_encode(getFilteredPosts($_SESSION['search'], $_SESSION['experience'], $_SESSION['order']));
                exit();
                break;

            case 'getFilters':
                echo json_encode($_SESSION);
                exit();
                break;

            case 'createPost':
                createPost($_SESSION['username'], $_POST['postTitle'], $_POST['postContent']);
                exit();
                break;

            case 'applyFilters':
                $_SESSION['experience'] = $_POST['experience'];
                $_SESSION['order'] = $_POST['order'];
                $_SESSION['search'] = $_POST['search'];
                exit();
                break;
                
            default:
                echo "Unknown command from start Page<br>";
                exit();
                break;
        }
    }
?>