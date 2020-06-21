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


class CommentTable
{

    private $conn;
    private $table = 'Comment';

    public $comment_id;
    public $user_id;
    public $product_id;
    public $content;
    public $created_date;
    public $modified_date;
    public $comment_image;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function readProductsCommentData($prod_id)
    {
        $query = 'SELECT 
                comment_id,
                user_id,
                product_id,
                content,
                created_date,
                modified_date,
                comment_image 
                FROM 
                ' . $this->table . ' where product_id = ?';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $prod_id);
        $stmt->execute();

        return $stmt;
    }
}

function getAllCommentsForAProduct($prd_id)
{
    header('Access-COntrol-Allow-Origin: *');
    header('Content-type: application/json');
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $fetchComments = new CommentTable($GLOBALS['db']);

    $result = $fetchComments->readProductsCommentData($prd_id);

    $rowCount = $result->rowCount();
    echo 'rowcount is:   '.$rowCount;
    if ($rowCount > 0) {
        $comments_arr = array();
        $comments_arr['data'] = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $comments_item[] = array(
                'comment_id' => $comment_id,
                'user_id' => $user_id,
                'product_id' => $product_id,
                'content' => $content,
                'created_date' => $created_date,
                'modified_date' => $modified_date,
                'comment_image' => base64_encode($comment_image)
            );

            array_push($comments_arr['data'], $comments_item);
            echo json_encode(array_pop($comments_item));
        }

        
        return true;

    } 
    else 
    {
        echo json_encode(array('message' => 'no comments found for this products   '));
        return false;
    }
}

$http_verb = strtolower($_SERVER['REQUEST_METHOD']);

if ($http_verb == 'post') {
    try {
        $post = trim(file_get_contents("php://input"));
        $json = json_decode($post, true);

        $product_id = $json['product_id'];
        $result = getAllCommentsForAProduct($product_id);
        if($result){
            echo 'success!  ';
        }
        else{
            echo 'fail  ';
        }

    } catch (Exception $e) {
        echo $e;
    }
}
