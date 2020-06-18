<?php
ini_set("display_errors", 1);
ini_set("track_errors", 1);
ini_set("html_errors", 1);
error_reporting(E_ALL);

header('Content-type: application/json');
$localhost = 'localhost';
$db_user = 'root';
$db_password = '';
$db_name = 'shopoo';
$GLOBALS['db'] = new PDO('mysql:host=' . $localhost . ';dbname=' . $db_name . ';', $db_user, $db_password);
$GLOBALS['db']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

class CartTable
{

    private $conn;
    private $table = 'Cart';

    public $cart_id;
    public $product_id;
    public $user_id;
    public $address_id;
    public $quantity;
    public $time_added;


    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function isProductExistsInCart($usr_id, $prod_id)
    {
        $query = 'SELECT 
                cart_id,
                product_id,
                user_id,
                address_id,
                quantity,
                time_added
                FROM 
                ' . $this->table . ' where user_id = ? and product_id = ? LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $usr_id);
        $stmt->bindParam(2, $prod_id);
        try {
            if ($stmt->execute()) {
                return $stmt;
            }


            printf("Error %s. \n", $stmt->error);
            return null;
        } catch (Exception $e) {
            echo $e;
        }
    }

    public function updateQuantity($usr_id, $prod_id, $prev_quantity_of_Product, $quantity)
    {
        $query = 'UPDATE ' . $this->table . '
                SET quantity = :quantity
                 where user_id = :user_id and product_id = :product_id LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $final_quantity = $prev_quantity_of_Product - $quantity;
        $stmt->bindParam(':quantity', $final_quantity);
        $stmt->bindParam(':user_id', $usr_id);
        $stmt->bindParam(':product_id', $prod_id);

        try {
            if ($stmt->execute()) {
                return true;
            }

            printf("Error %s. \n", $stmt->error);
            return false;
        } catch (Exception $e) {
            echo 'issue in executing the update statement' . $e;
        }
    }

    public function deleteCartRec($prod_id, $usr_id)
     {
        $query = 'DELETE FROM ' . $this->table . '
          WHERE product_id = ? and user_id = ?';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $prod_id);
        $stmt->bindParam(2, $usr_id);

        try 
        {
            if ($stmt->execute()) 
            {
                return true;
            }

            printf("Error %s. \n", $stmt->error);
            return false;
        } 
        catch (Exception $e) 
        {
            echo 'issue in executing the delete statement' . $e;
        }

     }

}


function checkIfProductAlreadyExistsInCart($usr_id, $prod_id)
{
    header('Access-COntrol-Allow-Origin: *');
    header('Content-type: application/json');

    $checkProdExists = new CartTable($GLOBALS['db']);

    $checkProdExists->user_id = $usr_id;
    $checkProdExists->product_id = $prod_id;

    $result = $checkProdExists->isProductExistsInCart($usr_id, $prod_id);

    $num = $result->rowCount();

    if ($num > 0) {
        $getQuantity_arr = array();
        $getQuantity_arr['data'] = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $getQuantity_item = array(
                'cart_id' => $cart_id,
                'product_id' => $product_id,
                'user_id' => $user_id,
                'address_id' => $address_id,
                'quantity' => $quantity,
                'time_added' => $time_added
            );

            array_push($getQuantity_arr['data'], $getQuantity_item);
        }

        //echo json_encode($getQuantity_arr);

        return $quantity;
    } else {
        //echo json_encode(array('message' => 'product already doesnt exists in cart'));
        return null;
    }
}

function updateProductQuantity($user_id, $product_id, $prev_quantity_of_Product, $quantity)
{
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


    $updateCartRecord = new CartTable($GLOBALS['db']);


    if ($updateCartRecord->updateQuantity($user_id, $product_id, $prev_quantity_of_Product, $quantity)) {
        echo "Removed ".$quantity." quantity of the product with prod_id:" .$product_id." from cart successfully!";
    } else {
        echo "Removal ".$quantity." quantity of the product with prod_id:" .$product_id." from cart was unsuccessful!";
    }
}

function deleteCartEntry($product_id, $user_id){
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


    $deleteCartRecord = new CartTable($GLOBALS['db']);


    if ($deleteCartRecord->deleteCartRec($product_id, $user_id)) 
    {
        echo "Deletion of product is successful!";
    } else {
        echo 'Deletion of product is unsuccessful!';
    }
}

$http_verb = strtolower($_SERVER['REQUEST_METHOD']);

if ($http_verb == 'post') {
    try {
        $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);

        $product_id = $json['product_id'];
        $user_id = $json['user_id'];
        $quantity = $json['quantity'];

        $prev_quantity_of_Product = checkIfProductAlreadyExistsInCart($user_id, $product_id);
        if ($prev_quantity_of_Product != null) 
        {
            if($prev_quantity_of_Product > $quantity){
                if($prev_quantity_of_Product > 1){
                    $update = updateProductQuantity($user_id, $product_id, $prev_quantity_of_Product, $quantity);
                }
                elseif($prev_quantity_of_Product > 0 && $prev_quantity_of_Product == 1){
                    $delete = deleteCartEntry($product_id, $user_id);
                }
                else{
                    echo 'No products have been added to cart with product id'.$product_id.'by user'.$user_id;
                }
            }
            else{
                $delete = deleteCartEntry($product_id, $user_id);
            }
            
            
        } else {
            echo 'No products have been added to cart with product id'.$product_id.'by user'.$user_id;
        }
    } catch (Exception $e) {
        echo $e;
    }
}


?>