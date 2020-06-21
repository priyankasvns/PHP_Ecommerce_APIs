<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
// include('../config/config.php');
include('../models/GetOrders.php');     
try{
    $http_verb = strtolower($_SERVER['REQUEST_METHOD']);    
    if($http_verb == 'get'){                 
        // Get all orders in the database
        if(isset($_GET['email']) && $_GET['email'] != "" ){ 
            if (isset($_GET['password']) && $_GET['password'] !="") {
        
            $orderRequest = new OrdersGetRequest();              
            $result = $orderRequest->fetchOrders($_GET['email'],$_GET['password']); 
            if ($result != null) {
                $rowCount = $result->rowCount();                             
            
                if($rowCount>0){
                    $order_arr = array();
                    $order_arr['data'] = array();
                    while($row = $result->fetch(PDO::FETCH_ASSOC)){
                        extract($row);                           
                        $order_item[] = array(                                               
                            'order_reference_id' => $order_reference_id,
                            'product_id' => $product_id,
                            'quantity' => $quantity,
                            'amount' => $amount,
                            'order_date_time' => $order_date_time
                            
                        );                    
                        
                        array_push($order_arr['data'],$order_item);
    
                    
                    }
                    $myObj = new stdClass();
                    $myObj->isOrderFetched = true;
                    $myObj->message = "orders fetched successfully";
                    echo json_encode($myObj);                
                    // echo json_encode(array_pop($order_arr['data']));   
            } 
            else {
                $myObj = new stdClass();
                    $myObj->isOrderFetched = false;
                    $myObj->message = "orders couldn't be fetched as there are no orders for the user";
                    echo json_encode($myObj);  
            }                                                         
        }
        else{          
            $myObj = new stdClass();
            $myObj->isOrderFetched = false;
            $myObj->message = "orders couldn't be fetched. The parameters passed by the user are invalid";
            echo json_encode($myObj);      
            // echo json_encode(array('message' => 'No orders found'));            
        }
    }
    else {
        $myObj = new stdClass();
        $myObj->isOrderFetched = false;
        $myObj->message = "Please provide the password as well";
        echo json_encode($myObj);      
    }
        
    }
    else {
        $myObj = new stdClass();
        $myObj->isOrderFetched = false;
        $myObj->message = "Please provide the email and other parameters";
        echo json_encode($myObj);      
    }
    
    }
    else{
        echo json_encode(array('message' => 'Incorrect request method. Expected GET. Please try again later'));
    }
}
catch(Exception $e){
echo json_encode(array('message' => 'Something went wrong, please try again later'));
}
?>