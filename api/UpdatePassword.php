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
        $newPassword = $json['newPassword'];

        $updatePassword = new User();
        $result = $updatePassword->updatePassword($email,$password,$newPassword);        
        if ($updatePassword->invalidEmail == false && $updatePassword->nonUser == false) {
            $myObj = new stdClass();
            $myObj->isUpdated = $result == true;                      
            $myObj->message = $result == true ? "User Password Updated successfully" : "Password could not be updated";
            echo json_encode($myObj);
        }
        else {
            $myObj = new stdClass();
            $myObj->isUpdated = false;
            $myObj->isInvalid = true;                                 
            $myObj->message = "User Password could not be updated as it is not a valid user. Probably username or password is incorrect.";
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