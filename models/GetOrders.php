<?php
class OrdersGetRequest{
    private $host = 'localhost';
    private $db_name = 'shopoo';
    private $db_user = 'root';
    private $db_password = '';
    private $conn;
    private $table = 'orders';

// Order table properties
    public $order_id;
    public $order_reference_id;
    public $email;
    public $password;
    public $product_id;
    public $quantity;   
    public $amount;
    public $order_date_time;
    public $userId;

    public $fetched;
    public $validUser;
    //Fetching all orders from the database
    public function fetchOrders($email,$password){
        $this->email = $email;
        $this->password = $password;
        try{
            $GLOBALS['conn'] = new PDO('mysql:host='.$this->host.';dbname='.$this->db_name,$this->db_user,$this->db_password);
            $GLOBALS['conn']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){
            echo 'Database Connection Error: '.$e->getMessage();
        }
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->invalidEmail = false;
            $stmt = $GLOBALS['conn']->prepare('SELECT * FROM users WHERE email = :email');    
            $stmt->bindValue(':email',$this->email);        
            $stmt->execute(); 
            $result = $stmt->fetch(PDO::FETCH_ASSOC);            
            if ($result) {
                if(password_verify($this->password, $result['password'])){                    
                    $this->userId = $result['user_id']; 
                    
                    $stmt = $GLOBALS['conn']->prepare('SELECT Order_id, order_reference_id, product_id, quantity, amount, order_date_time FROM '.$this->table.' WHERE user_id = :userid');                    
                    $stmt->bindValue(':userid',$this->userId);
                    $stmt ->execute();                     
                    try{               
                        if($stmt->execute()){
                            $this->fetched = true; 
                            $this->validUser = true;                            
                            return $stmt;                                              
                        }
                        else{
                            $this->fetched = false; 
                            return error_log("Unable to fetch the orders");                   
                        }                
                      }
                      catch (Exception $e){
                        echo "Some error occured while performing database related tasks.".$e;
                    }    
                }                                
            } 
            else {
                $this->validUser = false;
            }            
        }
        else{
            $this->invalidEmail = true;
        }
    }
}
?>