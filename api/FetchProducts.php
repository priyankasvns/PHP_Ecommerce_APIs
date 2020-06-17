<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
include('../models/GetProducts.php');     
try{
    $http_verb = strtolower($_SERVER['REQUEST_METHOD']);    
    if($http_verb == 'get'){                 
        if(isset($_GET['product_id']) && $_GET['product_id'] != ""){               
            $productRequest = new ProductGetRequest();       
            $productRequest->product_id = $_GET['product_id'];
            $productToFetch_id = $productRequest->product_id;           
            $result = $productRequest->fetchSingleProduct($productToFetch_id);   
            $row = $result->rowCount();                
            if ($result) { 
                $product_arr = array();
                $product_arr['data'] = array();  
                while($row = $result->fetch(PDO::FETCH_ASSOC)){
                    extract($row);
                    $product_item[] = array(                       
                        'product_id' => $product_id,
                        'size' => $size,
                        'color' => $color,
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'picture' => base64_encode($picture),
                        'available_quantity' => $available_quantity
                    );                    
                    
                    array_push($product_arr['data'],$product_item);

                }
                echo json_encode(array_pop($product_arr['data']));        
        }
        else {
            echo json_encode(array('message' => 'No products found'));
        }
        }
        // Get all products in the database
        else if(!isset($_GET['product_id']) || $_GET['product_id'] == ""){   
            $productRequest = new ProductGetRequest();              
            $result = $productRequest->fetchProducts();            
            $rowCount = $result->rowCount();                    
            $isProductAvailable = false;
            if($rowCount>0){
                $product_arr = array();
                $product_arr['data'] = array();
                while($row = $result->fetch(PDO::FETCH_ASSOC)){
                    extract($row);                           
                    $product_item[] = array(                       
                        'product_id' => $product_id,
                        'size' => $size,
                        'color' => $color,
                        'name' => $name,
                        'description' => $description,
                        'price' => $price,
                        'picture' => base64_encode($picture),
                        'available_quantity' => $available_quantity
                    );                    
                    
                    array_push($product_arr['data'],$product_item);

                    $isProductAvailable = true;
                
                }                
                echo json_encode(array_pop($product_arr['data']));
                // $emptyMessage = "Products fetched successfully";
        }
        else{
            $isProductAvailable = false;
            echo json_encode(array('message' => 'No products found'));            
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