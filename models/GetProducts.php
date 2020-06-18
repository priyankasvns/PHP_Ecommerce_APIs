<?php
class ProductGetRequest{
    private $host = 'localhost';
    private $db_name = 'shopo';
    private $db_user = 'root';
    private $db_password = '';
    private $conn;
    private $table = 'products';
    

    //Products' properties
    public $product_id;
    public $size;
    public $color;
    public $name;
    public $description;
    public $price;
    public $picture;
    public $available_quantity;

    //Fetching all products from the database
    public function fetchProducts(){
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
            // echo "Connection is successful and no problem using prepare()";
            $stmt = $this->conn->prepare('SELECT Prod.product_id, Prod.size, Prod.color, Prod.name, Prod.description, Prod.price, Prod.picture, Prod.available_quantity FROM products Prod WHERE available_quantity > 0');
            $stmt->execute();            
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
    public function fetchSingleProduct($product){
        $this->conn = null;
        $this->product_id = $product;
        echo $this->product_id;
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
        $query = 'SELECT Prod.product_id, Prod.size, Prod.color, Prod.name, Prod.description, Prod.price, Prod.picture, Prod.available_quantity FROM products Prod
        WHERE product_id = :productid LIMIT 1';
        
        if($this->conn != null){
        $statement = $this->conn->prepare($query);
        $statement->bindParam(':productid', $this->product_id);
        $statement->execute();
        }
        else{
            echo "Some error with DB connection . Please check it.";
        }
        return $statement;
    }
    catch(Exception $e){
        echo "Some error occured while executing the fetch query.";
    }

    }
}
?>