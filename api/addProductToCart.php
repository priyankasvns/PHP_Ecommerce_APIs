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

class CheckProductsQuantity
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
                product_id,
                size,
                color,
                name,
                description,
                price,
                picture,
                available_quantity 
                FROM 
                ' . $this->table . ' where product_id = ? LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $prod_id);
        $stmt->execute();

        return $stmt;
    }
}


class getAddressId
{

    private $conn;
    private $table = 'Address';

    public $address_id;
    public $user_id;
    public $last_name;
    public $first_name;
    public $address1;
    public $postcode;
    public $city;
    public $phone;

    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function readAddressId($usr_id)
    {
        $query = 'SELECT 
                address_id,
                user_id,
                last_name,
                first_name,
                street_address,
                postcode,
                city,
                phone
                FROM 
                ' . $this->table . ' where user_id = ? LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $usr_id);
        $stmt->execute();

        return $stmt;
    }
}

class createCartRec
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
        $final_quantity = $prev_quantity_of_Product + $quantity;
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

    public function createCart($product_id, $address_id, $user_id, $quantity)
    {

        $query = 'INSERT INTO ' . $this->table .
            '( product_id, user_id, address_id, quantity, time_added)
                VALUES ( :product_id, :user_id, :address_id, :quantity, now())';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':address_id', $address_id);
        $stmt->bindParam(':quantity', $quantity);

        try {
            if ($stmt->execute()) {
                return true;
            }

            printf("Error %s. \n", $stmt->error);
            return false;
        } catch (Exception $e) {
            echo 'issue in executing the insert statement' . $e;
        }
    }
}


function isProductAvailable($product_id)
{
    $isProdAvailable = false;

    header('Access-COntrol-Allow-Origin: *');
    header('Content-type: application/json');

    $checkQuantity = new CheckProductsQuantity($GLOBALS['db']);

    $checkQuantity->product_id = $product_id;

    $result = $checkQuantity->readProductsData($product_id);

    $num = $result->rowCount();

    if ($num > 0) {
        $checkQuantity_arr = array();
        $checkQuantity_arr['data'] = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $checkQuantity_item = array(
                'product_id' => $product_id,
                'size' => $size,
                'color' => $color,
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'picture' => $picture,
                'available_quantity' => $available_quantity
            );

            array_push($checkQuantity_arr['data'], $checkQuantity_item);
        }

        echo json_encode($checkQuantity_arr);
    } else {
        echo json_encode(array('message' => 'no available products'));
    }

    if ($available_quantity > 0) {

        return $available_quantity;
        
    } else {

        return null;
        echo 'Product is not available. Please try later!';
     
    }
    
}

function fetchAddressOfUser($user_id)
{

    header('Access-COntrol-Allow-Origin: *');
    header('Content-type: application/json');

    $getAddressId = new getAddressId($GLOBALS['db']);

    $getAddressId->user_id = $user_id;

    $result = $getAddressId->readAddressId($user_id);

    $num = $result->rowCount();

    if ($num > 0) {
        $getAddressId_arr = array();
        $getAddressId_arr['data'] = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
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


    } else {
        echo json_encode(array('message' => 'no address for user exists'));
    }

    if ($address_id != null) {
        return $address_id;
    } else {
        echo 'Failure to fetch address id for the user!';
        return null;
    }
}

function checkIfProductAlreadyExistsInCart($usr_id, $prod_id)
{
    header('Access-COntrol-Allow-Origin: *');
    header('Content-type: application/json');

    $checkProdExists = new createCartRec($GLOBALS['db']);

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


    $updateCartRecord = new createCartRec($GLOBALS['db']);


    if ($updateCartRecord->updateQuantity($user_id, $product_id, $prev_quantity_of_Product, $quantity)) {
        echo "Update successful!";
    } else {
        echo 'Update is unsuccessful!';
    }
}


function createCartEntry($product_id, $address_id, $user_id, $quantity)
{

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


    $createCartRecord = new createCartRec($GLOBALS['db']);


    if ($createCartRecord->createCart($product_id, $address_id, $user_id, $quantity)) {
        echo "Insert successful!";
    } else {
        echo 'Insert is unsuccessful!';
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



        if (isProductAvailable($product_id)) {
            $fetchedAddressId = fetchAddressOfUser($user_id);
            if ($fetchedAddressId != null) {
                $prev_quantity_of_Product = checkIfProductAlreadyExistsInCart($user_id, $product_id);
                if ($prev_quantity_of_Product != null) {
                    $update = updateProductQuantity($user_id, $product_id, $prev_quantity_of_Product, $quantity);
                } else {
                    $insert = createCartEntry($product_id, $fetchedAddressId, $user_id, $quantity);
                }
            }
        }
    } catch (Exception $e) {
        echo $e;
    }
}
