<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include('../Web Technologies (Project 2)/Database/Mysql_implementation.sql')

session_start();

try{
    $http_verb = strtolower($_SERVER['REQUEST_METHOD']);

    if ($http_verb == 'post')
    {
        $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);

        $email = $json['email']; // contains p.s@gmail.com
        $password = $json['password'] //contains priyanka
        $user_id = $json['user_id']; // contains 1
        $order_reference_id = $json['order_reference_id']; //contains 1 and is fixed
        $content = $json['']

        $isValid = strToLower($email) == 'p.s@gmail.com' && $password == 'priyanka' && $user_id == '1';

        $myObj = new stdClass();
        $myObj->isLoggedIn = $isValid;
        $myObj->message = $isValid ? "" : "email/password does not match";

        if ($isValid)
            $_SESSION['isLoggedIn'] = true;

            echo json_encode($myObj);
    }
    else if ($http_verb == 'get')
    {
        
        $myObj = new stdClass();
        $myObj->toPostComment = $_SESSION['toPostComment'];
        echo json_encode($myObj);
        }
}

?>