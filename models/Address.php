<?php
class userAddressId
{
    private $host = 'localhost';
    private $db_name = 'shopoo';
    private $db_user = 'root';
    private $db_password = '';
    private $table = 'address';

    //address table properties
    public $address_id;
    public $user_id;
    public $last_name;
    public $first_name;
    public $street_address;
    public $postcode;
    public $city;
    public $phone;


    public function readAddressId($user_id)
    {   
        try{
            $GLOBALS['conn'] = new PDO('mysql:host='.$this->host.';dbname='.$this->db_name,$this->db_user,$this->db_password);
            $GLOBALS['conn']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
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
            ' . $this->table . ' Where user_id = ? ';

            $stmt = $GLOBALS['conn']->prepare($query);
            $stmt->bindParam(1, $user_id);

            if($stmt->execute())
            {   

                return $stmt;
            }

        }
        catch(PDOException $e){
            echo 'Database Connection Error: '.$e->getMessage();
        }
    }

    public function updateAddress($user_id, $address_id, $last_name, $first_name, $street_address, $postcode, $city, $phone)
    {   
        $this->user_id = $user_id;
        $this->address_id = $address_id;
        $this->last_name = $last_name;
        $this->first_name = $first_name;
        $this->street_address = $street_address;
        $this->postcode = $postcode;
        $this->city = $city;
        $this->phone = $phone;
  
        try{
            $GLOBALS['conn'] = new PDO('mysql:host='.$this->host.';dbname='.$this->db_name,$this->db_user,$this->db_password);
            $GLOBALS['conn']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        }
        catch (Exception $e) {
            echo 'issue in connecting database' . $e;
        }

        try {
            $query = 'UPDATE ' . $this->table . '
            SET last_name = :last_name,
            first_name = :first_name,
            street_address = :street_address,
            postcode = :postcode,
            city = :city,
            phone = :phone
            where user_id = :user_id and address_id = :address_id LIMIT 1';

            $stmt = $GLOBALS['conn']->prepare($query);

            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':address_id', $this->address_id);
            $stmt->bindValue(':last_name', $this->last_name);
            $stmt->bindValue(':first_name', $this->first_name);
            $stmt->bindValue(':street_address', $this->street_address);
            $stmt->bindValue(':postcode', $this->postcode);
            $stmt->bindValue(':city', $this->city);
            $stmt->bindValue(':phone', $this->phone);

            if ($stmt->execute()) {
                $myObj = new stdClass();
                $myObj->isUpdated = true;
                $myObj->message = "Address is updated";
                echo json_encode($myObj);
            }
            else {
                printf("Error %s. \n", $stmt->error);
                return false;
            }
        } catch (Exception $e) {
            
            echo 'issue in executing the update statement' . $e;
        }   
    }
}



?>