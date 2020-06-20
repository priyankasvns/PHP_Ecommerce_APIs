<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include('../models/GetOrders.php'); 
try{
    $http_verb = strtolower($_SERVER['REQUEST_METHOD']);    
    if($http_verb == 'get'){                 
        if(isset($_GET['order_id']) && $_GET['order_id'] != ""){               
            $orderRequest = new orderGetRequest();       
            $order  Request->order_id = $_GET['order_id'];
            $orderToFetch_id = $orderRequest->order_id;           
            $result = $orderRequest->fetchSingleorder($orderToFetch_id);   
            $row = $result->rowCount(); 
            if ($result) { 
                $order_arr = array();
                $order_arr['data'] = array();  
                while($row = $result->fetch(PDO::FETCH_ASSOC)){
                    
                    extract($row);
                    $order_history[] = array(  
                        'order_id' => $order_id                   
                        'product_id' => $product_id,
                        'user_id' => $user_id
                        'quantity' => $quantity,
                        'status' => $status,
                        'amount' => $amount,
                        'order_date_time' => $order_date_time                        
                    );      

                    array_push($order_arr['data'],$order_history);

                }
                echo json_encode(array_pop($order_history));        
        }
        else {
            echo json_encode(array('message' => 'No orders found'));
        }
        }
        else if(!isset($_GET['order_id']) || $_GET['order_id'] == ""){   
            $orderHistoryRequest = new OrderGetRequest();              
            $result = $orderHistoryRequest->fetchOrders();            
            $rowCount = $result->rowCount();                    
            $isOrderAvailable = false;
            if($rowCount>0){
                $order_arr = array();
                $order_arr['data'] = array();
                while($row = $result->fetch(PDO::FETCH_ASSOC)){
                    extract($row);  
                    $product_item[] = array(  
                        'order_id' => $order_id,                     
                        'product_id' => $product_id,
                        'cart_id' => $cart_id
                        'status' => $status,
                        'quantity' => $color,
                        'amount' => $amount,
                        'order_date_time' => $order_date_time
                        
                    );     
                    
                    array_push($order_arr['data'],$order_history);

                    $isorderAvailable = true;
                
                }                
                echo json_encode(array_pop($order_arr['data']));
                
        }
        else{
            $isOrderAvailable = false;
            echo json_encode(array('message' => 'No order found'));            
        }
    }
    else {
        die();
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



