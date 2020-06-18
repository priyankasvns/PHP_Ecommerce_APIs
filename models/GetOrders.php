<?php
class OrdersGetRequest{
    private $host = 'localhost';
    private $db_name = 'shopo';
    private $db_user = 'root';
    private $db_password = '';
    private $conn;
    private $table = 'orders';
// Order table properties
    public $order_id;
    public $user_id;
    public $product_id;
    public $cart_id;
    public $quantity;
    public $status;
    public $amount;
    public $order_date_time;
    //Fetching all orders from the database
    public function fetchOrders(){
        $this->conn = null;
        try{
            $this->conn = new PDO('mysql:host='.$this->host.';dbname='.$this->db_name,$this->db_user,$this->db_password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){
            echo 'Database Connection Error: '.$e->getMessage();
        }
        try{
            if($this->conn != null)
            {
                // echo "Connection is successful";
                
                $stmt = $this->conn->prepare('SELECT Order.Order_id, Order.product_id,Order.cart_id,Order.quantity, Order.status, Order.amount, Order.order_date_time FROM orders Order WHERE order_id = $order_id');
                $stmt ->execute();  
            }                        
             
        else{
            echo "Problem fetching dbConnection variable and prepare method cannot be used yet";
        }
        return $stmt;
        }
        catch(Exception $e){
        echo "Some error occured while executing the fetch query.";
    }

    }
    public function fetchSingleOrder($Order){
        $this->conn = null;
        $this->order_id = $order;
        echo $this->order_id;
        try{
            $this->conn = new PDO('mysql:host='.$this->host.';dbname='.$this->db_name,$this->db_user,$this->db_password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){
            echo 'Database Connection Error: '.$e->getMessage();
        }
        $query = 'SELECT Order.order_id,  Order.product_id,Order.cart_id,Order.quantity, Order.status, Order.amount, Order.order_date_time FROM orders Order WHERE order_id = $order_id
        WHERE order_id = :orderid LIMIT 1';
        
        if($this->conn != null){
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':orderid', $this->order_id);
        $statement->execute();
        }
        else{
            echo "Some error with DB connection . Please check it.";
        }
        return $statement;
    }
}
?>


       
    

    
