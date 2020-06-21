<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include('../models/Address.php');     

try{
    $http_verb = strtolower($_SERVER['REQUEST_METHOD']);
    
    if ($http_verb == 'post') {
        $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);
        $user_id = $json['user_id'];
        $address_id = $json['address_id'];
        $last_name = $json['last_name'];
        $first_name = $json['first_name'];
        $address1 = $json['address1'];
        $postcode = $json['postcode'];
        $city = $json['city'];
        $phone = $json['phone'];

        $address = new userAddressId();
        $stmt = $address->updateAddress($user_id, $address_id, $last_name, $first_name, $address1, $postcode, $city, $phone);
               
    }
    else{
        $myObj = new stdClass(); 
        $myObj->message = "Please select either POST method"; 
        echo json_encode($myObj);    
    }
}
catch(Exception $e){
    echo "Something went wrong. Please give the correct json as input and retry. ".$e;
}

?>