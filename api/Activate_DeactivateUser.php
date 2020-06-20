<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include('../models/ValidateUser.php');     

try{
    $http_verb = strtolower($_SERVER['REQUEST_METHOD']);
    if ($http_verb == 'post') {
        $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);
        $email = $json['email'];
        $password = $json['password'];
        $activated = $json['activated'];

        $activate_deactivate = new User();
        $result = $activate_deactivate->ActivateDeactivateUser($email,$password,$activated);        
        if ($activate_deactivate->invalidEmail == false) {
            $myObj = new stdClass();
            $myObj->isUpdated = $result == true;                      
            $myObj->message = $result == true ? "User Profile Updated successfully" : "Could not be updated";
            echo json_encode($myObj);
        }
    }
    else{
        $myObj = new stdClass();
        $myObj->isUpdated = false;
        $myObj->message = "Invalid request method. Please use POST to update";            
        echo json_encode($myObj);
    }
}
catch(Exception $e){
    echo "Something went wrong. Please give the correct json as input and retry. ".$e;
}