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
        $name = $json['name'];
        $phone = $json['phone'];
        $date_of_birth = $json['date_of_birth'];
        $date_joined = $json['date_joined'];
        $updated_date = $json['updated_date'];
        $activated = $json['activated'];

        $register = new User();
        $result = $register->validateNewUser($email,$password,$name,$date_of_birth,$phone,$date_joined,$updated_date,$activated);    
        if ($register->invalidEmail == false) {
            $myObj = new stdClass();
            $myObj->isExistingUser = $result == false;
            $myObj->message = $result == false ? "Please try to login as this email has already been registered with us." : "The email address is now registered";
            echo json_encode($myObj);
    }
    else{
        $myObj = new stdClass();
        $myObj->isInvalidUser = true;
        $myObj->message = "Invalid email: ".$email." is not a valid one";            
        echo json_encode($myObj);
    }
    }
    else {
        $myObj = new stdClass();
        $myObj->message = "Invalid request method. Please use post to insert";            
        echo json_encode($myObj);
    }
}
catch(Exception $e){
    echo "Something went wrong ".$e;
}
?>