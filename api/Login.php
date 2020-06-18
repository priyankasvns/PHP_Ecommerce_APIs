<?php
session_start();
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include('../models/ValidateUser.php');     

try {
    $http_verb = strtolower($_SERVER['REQUEST_METHOD']);
    if ($http_verb == 'post') {
        $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);
        $email = $json['email'];
        $password = $json['password'];
        $login = new User();
        $result = $login->validateUser($email,$password);        
        $myObj = new stdClass();
        $myObj->isLoggedIn = $result > 0;
        $myObj->message = $result > 0 ? "Logged in successfully" : "Email or password does not match";

        $_SESSION['user_id']=$result;
        $_SESSION['isLoggedIn']=$myObj->isLoggedIn;

        echo json_encode($myObj);
    }
    else if($http_verb == 'delete'){        
        $myObj = new stdClass();
        $myObj->message = "Logged out successfully";
        $myObj->isLoggedIn = false;
        $_SESSION['isLoggedIn'] = false;
        echo json_encode($myObj);
        
    }
    else{
        $myObj = new stdClass(); 
        $myObj->message = "Please select either POST or DELETE method verbs.";
        $myObj->isLoggedIn = false; 
        $_SESSION['isLoggedIn'] = false;     
        echo json_encode($myObj);
        
    }
} catch (Exception $e) {
    $myObj = new stdClass();
    $myObj->isLoggedIn = false;
    $myObj->message = "Something went wrong. Please try again later ".$e;
    echo json_encode($myObj);
}
?>