<?php
class User{
    private $host = 'localhost';
    private $db_name = 'shopoo';
    private $db_user = 'root';
    private $db_password = '';
    private $table = 'users';

    //User properties

    public $email;//unique
    public $name;
    public $date_of_birth;
    public $phone;
    public $password;
    public $date_joined;
    public $updated_date;
    public $activated;

    public $inserted;
    public $invalidEmail;

    public function validateUser($email,$password){
        $userId = 0;
        $this->email = $email;
        $this->password = $password;
        try{
            $GLOBALS['conn'] = new PDO('mysql:host='.$this->host.';dbname='.$this->db_name,$this->db_user,$this->db_password);
            $GLOBALS['conn']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){
            echo 'Database Connection Error: '.$e->getMessage();
        }
        try{
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($this->password)>0) {
            $stmt = $GLOBALS['conn']->prepare('SELECT * FROM '.$this->table.' WHERE email=:email AND activated = 1');
            $stmt->bindValue(':email',$this->email);
            $stmt->execute();

            if($user = $stmt->fetch(PDO::FETCH_ASSOC))
            {                
                if(password_verify($this->password, $user['password'])){
                    $userId = $user['user_id'];
                }                
        }
    }
}
    catch(Exception $e){
        echo $e->getMessage();
    }
    return $userId;
}

    public function validateNewUser($email,$password,$name,$date_of_birth,$phone,$date_joined,$updated_date,$activated){
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->date_of_birth = $date_of_birth;
        $this->phone = $phone;
        $this->date_joined = $date_joined;
        $this->updated_date = $updated_date;
        $this->activated = $activated;
        try{
            $GLOBALS['conn'] = new PDO('mysql:host='.$this->host.';dbname='.$this->db_name,$this->db_user,$this->db_password);
            $GLOBALS['conn']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e){
            echo 'Database Connection Error: '.$e->getMessage();
        }        
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $stmt = $GLOBALS['conn']->prepare('SELECT COUNT(*) FROM '.$this->table.' WHERE email=:email');
                $stmt->bindValue(':email',$this->email);
                $stmt->execute();
                
                $result = $stmt->fetch(PDO::FETCH_NUM);             
                if($result[0] > 0){
                    $this->inserted = false;                    
                }                      
              else {
                $stmt = $GLOBALS['conn']->prepare('INSERT INTO ' . $this->table .
                '( email, name, date_of_birth, phone, password, date_joined, updated_date, activated)
                VALUES ( :email, :name, :date_of_birth, :phone, :password, :date_joined, :updated_date, :activated)');
                $stmt->bindValue(':email',$this->email);
                $stmt->bindValue(':name',$this->name);
                $stmt->bindValue(':date_of_birth',$this->date_of_birth);
                $stmt->bindValue(':phone',$this->phone);
                $stmt->bindValue(':password',password_hash($this->password,PASSWORD_DEFAULT));
                $stmt->bindValue(':date_joined',$this->date_joined);
                $stmt->bindValue(':updated_date',$this->updated_date);
                $stmt->bindValue(':activated',$this->activated); 
                try{               
                if($stmt->execute() == true){
                    $this->inserted = true;                   
                }
                else{
                    $this->inserted = false;                    
                }                
              }
              catch (Exception $e){
                echo "Some error occured while performing database related tasks.".$e;
            }           
        }
        $this->invalidEmail = false;
    }
    else{
        $this->inserted = false;
        $this->invalidEmail = true;       
    }
    return $this->inserted; 
    }
}
?>