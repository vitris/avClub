<?php
include '../common/configuration.php';
include '../model/database.php';
include '../model/requests_db.php';
include '../common/functions.php';
session_start();

// get default values
$message = "";
$username = $_SESSION['USERNAME'];
$teacher_id = $_SESSION['TEACHER_ID'];
$avtech_id = $_SESSION['TECH_ID'];
$room = filter_input(INPUT_POST,'room');
$problem_type = filter_input(INPUT_POST,'problem_type');
$comment = filter_input(INPUT_POST,'comment');
$requestTypes = getRequestTypes();


// if the list token was not provided, go back to the landing page
if (!isset($_SESSION['LOGGED_IN'])){
    header('../index.php');
    exit();
}

// if the logout button was clicked....
if (isset($_POST['btn_logout'])){
    header('Location: ../people/people_logout.php');
    exit();
}

if (($_SESSION['TYPE'] == 'teacher') && (isset($_POST['ADDREQUEST']))){
    if ((empty($teacher_id)  || empty($room)) || empty($problem_type)) {
        $message = "* One or more required fields are missing.";
        include 'request_add.php';
        exit();
    } else
    {
        $success = addRequest($teacher_id,$room,$problem_type,$comment);
        if ($success == 'true')
            {
            global $last_id;
            $confirmation = 'T0000000' .$last_id;
            addConfirmation($confirmation,$last_id);
            include 'request_confirm.php';
            exit();
            } else {
            $message = "An unexpected error occurred.";
            }

    }
}

//teachers see the add form
if ($_SESSION['TYPE'] == 'teacher'){
    include 'request_add.php';
    exit();
}


//techs have the option to close requests.
$close_this_request_id = filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);
if (($_SESSION['TYPE'] == 'tech') && (!empty($close_this_request_id))){
    closeRequest($close_this_request_id,$avtech_id);
    $requests = getRequests('In Progress');
    include 'request_list.php';
    exit();
}

//techs see the request list
if ($_SESSION['TYPE'] == 'tech'){
    $requests = getRequests('In Progress');
    include 'request_list.php';
    exit();
}


?>
