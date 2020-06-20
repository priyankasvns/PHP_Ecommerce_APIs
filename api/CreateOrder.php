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

class UsersTable
{

    private $conn;
    private $table = 'Users';

    public $user_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function validateUserId($usr_id)
    {
        $query = 'SELECT * FROM ' . $this->table . ' where user_id = ?';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $usr_id);
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
}


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

    public function fetchAllProductsFromCart($usr_id)
    {
        $query = 'SELECT 
                cart_id,
                product_id,
                user_id,
                address_id,
                quantity,
                time_added
                FROM 
                ' . $this->table . ' where user_id = ?';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $usr_id);
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

    public function deleteCartRecs($usr_id)
     {
        $query = 'DELETE FROM ' . $this->table . ' 
          WHERE user_id = ?';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $usr_id);

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
            echo 'issue in executing the delete statement in cart table  ' . $e;
        }

     }

    
}

class ProductsTable
{

    private $conn;
    private $table = 'Products';

    public $product_id;
    public $size;
    public $color;
    public $name;
    public $description;
    public $price;
    public $picture;
    public $available_quantity;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function readProductsData($prod_id)
    {
        $query = 'SELECT 
                price,
                available_quantity
                FROM 
                ' . $this->table . ' where product_id = :product_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":product_id", $prod_id);
        if ($stmt->execute()) {
            return $stmt;
        }
        else{
            echo "Error executing select query in products table   ";
        }
    }

    public function updateQuantity($prod_id, $quantity)
    {
        $query = 'UPDATE ' . $this->table . '
                SET available_quantity = :quantity
                 where product_id = :product_id';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':product_id', $prod_id);

        try {
            if ($stmt->execute()) {
                return true;
            }

            printf("Error %s. \n", $stmt->error);
            return false;
        } catch (Exception $e) {
            echo 'issue in executing the update statement in products table  ' . $e;
        }
    }

}

class OrdersTable
{

    private $conn;
    private $table = 'Orders';

    public $order_reference_id;
    public $user_id;
    public $product_id;
    public $cart_id;
    public $quantity;
    public $status;
    public $amount;
    public $order_date_time;
    public $order_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getOrderId($usr_id)
    {
        //SELECT order_id FROM `Orders` WHERE user_id = 1 order by order_id DESC LIMIT 1
        $query = 'SELECT order_id FROM ' . $this->table . ' where user_id = :user_id ORDER BY order_id DESC LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $usr_id);
        if ($stmt->execute()) {
            return $stmt;
        }
        else{
            echo "Error executing query while fetching order id of the user   ";
            return null;
        }
    }

    public function createOrder($user_id, $product_id, $cart_id, $quantity, $amount, $order_id)
    {

        $query = 'INSERT INTO ' . $this->table .
            '( user_id, product_id, cart_id, quantity, status, amount, order_date_time, order_id)
                VALUES ( :user_id, :product_id, :cart_id, :quantity, "order placed" , :amount, now(), :order_id)';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':cart_id', $cart_id);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':amount', $amount);
        $new_Order_Id = $order_id + 1;
        $stmt->bindParam(':order_id', $new_Order_Id);

        try {
            if ($stmt->execute()) {
                return true;
            }

            printf("Error %s. \n", $stmt->error);
            return false;
        } catch (Exception $e) {
            echo 'issue in executing the insert statement in orders table   ' . $e;
        }
    }


}


function fetchProductsFromCart($usr_id)
{
    
    $order_id_generated = false;
    $order_placed = false;
    header('Access-COntrol-Allow-Origin: *');
    header('Content-type: application/json');
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    $validationOfUserId = new UsersTable(($GLOBALS['db']));
    $validationOfUserId->user_id = $usr_id;
    $result = $validationOfUserId->validateUserId($usr_id);
    
    $userValid = $result->rowCount();

    //echo 'user id valid'.$userValid;

    if($userValid > 0){

        $checkProdExists = new CartTable($GLOBALS['db']);

        $checkProdExists->user_id = $usr_id;

        $result = $checkProdExists->fetchAllProductsFromCart($usr_id);

        $fetchProductsCount = $result->rowCount();

        if ($fetchProductsCount > 0) {
            $getQuantity_arr = array();
            $getQuantity_arr['data'] = array();

            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                $getQuantity_item[] = array(
                    'cart_id' => $cart_id,
                    'product_id' => $product_id,
                    'user_id' => $user_id,
                    'address_id' => $address_id,
                    'quantity' => $quantity,
                    'time_added' => $time_added
                );

                array_push($getQuantity_arr['data'], $getQuantity_item);
            }
        } else {
            echo json_encode(array('message' => 'no products found in the cart for this user id   '));
        }

        $checkProductsData = new ProductsTable($GLOBALS['db']);

        $prd_ids['data'] = array();
        for ($x = 0; $x < sizeof($getQuantity_arr['data']); $x++) {

            array_push($prd_ids['data'], $getQuantity_arr['data'][$x][$x]['product_id']);
            $p_id = array_pop($prd_ids['data']);
            $result = $checkProductsData->readProductsData($p_id);
            $prodDataRowCount = $result->rowCount();

            if ($prodDataRowCount > 0) {
                $prodData_arr = array();
                $prodData_arr['data'] = array();

                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $prodData_item[] = array(
                        'price' => $price,
                        'available_quantity' => $available_quantity
                    );

                    array_push($prodData_arr['data'], $prodData_item);
                }
            } else {
                echo json_encode(array('message' => 'product data doesnt exists in product table    '));
            }
        }

        for ($z = 0; $z < sizeof($prodData_item); $z++) {
            if ($prodData_arr['data'][0][$z]['available_quantity'] > $getQuantity_arr['data'][$z][$z]['quantity']) {
                $new_quantity = $prodData_arr['data'][0][$z]['available_quantity'] - $getQuantity_arr['data'][$z][$z]['quantity'];
                $current_prod_id = $getQuantity_arr['data'][$z][$z]['product_id'];

                if ($checkProductsData->updateQuantity($current_prod_id, $new_quantity)) {
                    //echo 'quantity updated, can place order   ';
                    $getLastOrderId = new OrdersTable($GLOBALS['db']);
                    $result = $getLastOrderId->getOrderId($usr_id);
                    $lastIdRowCount = $result->rowCount();
                    //echo 'last id rows return    ' . $lastIdRowCount;
                    if ($lastIdRowCount > 0) {
                        $lastId_arr = array();
                        $lastId_arr['data'] = array();

                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            extract($row);
                            $lastId_item[] = array(
                                'order_id' => $order_id
                            );

                            array_push($lastId_arr['data'], $lastId_item);
                        }
                        $fetchedLastId = true;
                    }


                    if ($fetchedLastId == true || $order_id_generated == true) {
                        $createOrderRec = new OrdersTable($GLOBALS['db']);

                        if ($createOrderRec->createOrder($usr_id, $getQuantity_arr['data'][$z][$z]['product_id'], $getQuantity_arr['data'][$z][$z]['cart_id'], $getQuantity_arr['data'][$z][$z]['quantity'], $prodData_arr['data'][0][$z]['price'], $lastId_arr['data'][0][0]['order_id'])) {
                            $order_placed = true;
                            $order_id_generated = true;
                        }
                    } elseif ($fetchedLastId == false && $order_id_generated == false) {
                        $createOrderRec = new OrdersTable($GLOBALS['db']);
                        $first_order_id = 0;
                        if ($createOrderRec->createOrder($usr_id, $getQuantity_arr['data'][$z][$z]['product_id'], $getQuantity_arr['data'][$z][$z]['cart_id'], $getQuantity_arr['data'][$z][$z]['quantity'], $prodData_arr['data'][0][$z]['price'], $first_order_id)) {
                            $order_placed = true;
                        }
                    }
                }
            }
        }
    }
    else{
        echo 'Please Login or register in order to make an order!  ';
    }

    if($order_placed){
        echo 'Order is placed successfully!';
    }
    
    
}
 
$http_verb = strtolower($_SERVER['REQUEST_METHOD']);

if ($http_verb == 'post') {
    try {
        $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);

        $user_id = $json['user_id'];

        fetchProductsFromCart($user_id);
    }
    catch(Exception $e){
        echo $e;
    }
}

?>
