<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include('../models/Address.php');       

try{
    $http_verb = strtolower($_SERVER['REQUEST_METHOD']);
    if ($http_verb == 'get') {
        $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);
        $user_id = $json['user_id'];

        $address = new userAddressId();

        $stmt = $address->readAddressId($user_id);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();

        $num = $stmt->rowCount();

        if ($num > 0) {
            $getAddressId_arr = array();
            $getAddressId_arr['data'] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $getAddressId_item = array(
                    'address_id' => $address_id,
                    'user_id' => $user_id,
                    'last_name' => $last_name,
                    'first_name' => $first_name,
                    'street_address' => $street_address,
                    'postcode' => $postcode,
                    'city' => $city,
                    'phone' => $phone
                );
                array_push($getAddressId_arr['data'], $getAddressId_item);
            }
            //echo json_encode($getAddressId_arr);
            
            $myObj = new stdClass();
            $myObj->getAddress = true;
            $myObj->message = "Get user's address";
            echo json_encode($myObj);
            
        } else {
            $myObj = new stdClass();
            $myObj->getAddress = false;
            $myObj->message = "There is no address in user's account";
            echo json_encode($myObj);
        }

        
    }
}
catch(Exception $e){
    echo "Something went wrong. Please give the correct json as input and retry. ".$e;
}

?>