<?php

 session_start();
 
  $GLOBALS['db'] = new PDO('mysql:dbname=shopoo; host=localhost',"root",""); 
  $GLOBALS['db']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 $myObj = new stdClass();

  function addComments($user_id, $order_reference_id, $content, $comment_image, $created_date, $modified_date)
  {    
    $sql = $GLOBALS['db']->prepare('INSERT INTO Comments (user_id, order_reference_id, content ,comment_image, created_date, modified_date)'.'VALUES (:user_id, :order_reference_id, :content, :comment_image, :created_date, :modified_date)');
 
    $sql->bindValue(':user_id', $user_id);
    $sql->bindValue(':order_reference_id', $order_reference_id);
    $sql->bindValue(':content', $content);
     $sql->bindValue(':comment_image', $comment_image);
     $sql->bindValue(':created_date', $created_date);
     $sql->bindValue(':modified_date', $modified_date);
  
    $sql->execute();

    return $GLOBALS['db']->lastInsertId();
   
  }

  function ifAddedProduct($user_id)
  {
    $cmd =  'SELECT order_reference_id FROM Orders WHERE user_id = :user_id';
    $sql = $GLOBALS['db']->prepare($cmd);
    $sql->bindValue(':user_id', $user_id);

    $sql->execute();

    $result = $sql->fetch(PDO::FETCH_NUM);
    return $result[0];

  }

  function deleteComments($user_id)
  {

     try 
      {
      $db = new PDO('mysql:dbname=shopoo; host=localhost'); 
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = $db->prepare('DELETE FROM Comments WHERE user_id = :user_id');
        $sql->bindValue(':user_id', $user_id);

        $sql->execute();       
        
      }
    
    catch(PDOException $e) 
    {
      echo $e->getMessage();
    }  
  }

  $http_verb = strtolower($_SERVER['REQUEST_METHOD']);

  if ($http_verb == 'post')
  {
    try
    {
        $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);
            
        $uid = $_SESSION['user_id'];
        $pid = ifProductExits($_SESSION['user_id']);
        $content = $json['content'];
        $comment_image = $json['comment_image'];
        $created_date = $json['created_date'];
        $modified_date = $json['modified_date'];


        if ( $_SESSION['user_id'] != NULL)
        {
         
          echo $_SESSION['user_id'];

          if(ifProductExists($_SESSION['user_id']))
          {

            $comment_id = addComments($user_id, $order_reference_id, $content, $comment_image, $created_date, $modified_date);

            echo $comment_id;

            $myObj->cAdded = $comment_id > 0;
            $myObj->cAdded = true;
            $myObj->message = $comment_id > 0 ? "" : "Something went wrong.";
            $myObj->order_reference_id = $order_reference_id;
            echo json_encode($myObj);
          }
          else
          {
           
            $myObj->cAdded = false;
            $myObj->message = "No product found.";
            echo json_encode($myObj);
          }
            
        }
        else
        {
            $myObj = new stdClass();
            $myObj->cAdded = false;
            $myObj->message = "Please login";
            echo json_encode($myObj);
        }
    }
    catch (Exception $e) 
    {
      $myObj = new stdClass();
      $myObj->cAdded = false;
      $myObj->message = "something went wrong, please try again later";
      echo json_encode($myObj);
    }
  }
  else if($http_verb == 'delete')
    {

      try
      {
         $delete = trim(file_get_contents("php://input"));
        $json = json_decode($delete, true);
            
       
        $user_id = $_SESSION['user_id'];
        

        if ( $_SESSION['user_id'] != NULL)
        {
            deleteComments($user_id);
        }
        else
        {
            $myObj = new stdClass();
            $myObj->cDeleted = false;
            $myObj->message = "Please login first..";
            echo json_encode($myObj);
        }
      }
      catch (Exception $e) 
      {
      $myObj = new stdClass();
      $myObj->cDeleted = false;
      $myObj->message = "something went wrong, please try again later";
      echo json_encode($myObj);
      }
     
    }
   
?>
