<?php
//turn on debugging messages
ini_set('display_errors', 'On');
error_reporting(E_ALL);
define('DATABASE', 'mr669');
define('USERNAME', 'mr669');
define('PASSWORD', 'abHYFGPw');
define('CONNECTION', 'sql1.njit.edu');
class dbConn
{
    //Holds a connection object
    protected static $db;
    //private construct
    private function __construct()
    {
        try {
            // assigning a PDO object to db variable
            self::$db = new PDO( 'mysql:host=' . CONNECTION .';dbname=' . DATABASE, USERNAME, PASSWORD );
            self::$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
             echo 'Connected successfully<br>';
            }
        catch (PDOException $e) {
            //Output error - would normally log this to error file rather than output to user.
            echo "Connection Error: " . $e->getMessage();
            }
    }
    // get connection function. Static method - accessible without instantiation
    public static function getConnection() 
    {
        //Guarantees single instance, if no connection object exists then create one.
        if (!self::$db) {
            //new connection object.
            new dbConn();
        }
        //return connection.
        return self::$db;
    }
}
 class collection
{
    static public function create()
      {
        $model = new static::$modelName;
        return $model;
      }
       
    public  function findAll()
      {
         $db = dbConn::getConnection();
         $table = get_called_class();
         $sql = 'SELECT * FROM ' . $table;
         $stmt = $db->prepare($sql);
         $stmt->execute();
            
         $class = static::$modelName;
         $stmt->setFetchMode(PDO::FETCH_CLASS, $class);
        
         $records =  $stmt->fetchAll();
         return $records;
      }
    public  function findOne($id)
      {
         $db = dbConn::getConnection();
         $table = get_called_class();
         $sql = 'SELECT * FROM ' . $table . ' WHERE id =' . $id;
         $stmt = $db->prepare($sql);
         $stmt->execute();
         $class = static::$modelName;
         $stmt->setFetchMode(PDO::FETCH_CLASS,$class);
         $records  =  $stmt->fetchAll();
         return $records;
      }
}
    class accounts extends collection
    {
        protected static $modelName='accounts';
    }
    class todos extends collection
    {
        protected static $modelName='todos';
    }

    class model
{
          static $column;
          static $value;
       
          public function save()
           {
             if (static::$id == '')
              {
               $db=dbConn::getConnection();
               $arr = get_object_vars($this);
               static::$column = implode(', ', $arr);
               static::$value = implode(', ',array_fill(0,count($arr),'?'));
               $sql = $this->insert();
               $stmt=$db->prepare($sql);
               $stmt->execute(static::$data);
              }
             else
              {
               $db=dbConn::getConnection();
               $arr = get_object_vars($this);
               $sql = $this->update();
               $stmt=$db->prepare($sql);
               $stmt->execute();
              }
           }
           private function insert()
            {
                $sql = "Insert Into ".static::$table." (". static::$column . ") Values(". static::$value . ") ";
                return $sql;
            }
           private function update()
            {
                $sql = "Update ".static::$table. " SET ".static::$updateColumn."='".static::$updateTo."' WHERE id=".static::$id;
                return $sql;
             }
                    
                   
            public function delete()
             {
                $db=dbConn::getConnection();
                $sql = 'Delete From '.static::$table.' WHERE id='.static::$id;
                $stmt=$db->prepare($sql);
                $stmt->execute();
                echo'Deleted record which has ID :'.static::$id;
             }
}
//account and todo extends code
class account extends model
{
              public $email = 'email';
              public $fname = 'fname';
              public $lname = 'lname';
              public $phone =  'phone';
              public $birthday = 'birthday';
              public $gender= 'gender';
              public $password = 'password';
              static $table = 'accounts';
              static $id = '6';
              static $data = array('mr669@njit.edu','Nisha','Ram','272','1993-09-05','Female','mona');
              static $updateColumn = 'fname';
              static $updateTo ='Jenny';
}
class todo extends model
{
               public $owneremail = 'owneremail';
               public $ownerid = 'ownerid';
               public $createddate = 'createddate';
               public $duedate = 'duedate';
               public $message = 'message';
               public $isdone = 'isdone';
               static $table = 'todos';
               static $id = '6';
               static $data = array('web@njit.edu','1','2017-01-01','2017-12-12','Done','1');
               static $updateColumn = 'message';
               static $updateTo ='Hi I am updated!'; 
}
//class for table creation
class table
{
        static  function makeTable($result)
        {
            echo '<table>';
            echo "<table cellpadding='10px' border='2px' style='border-collapse:collapse' text-align :'center' width ='100%'white-space : nowrap'font-''weight:bold'>";
            foreach($result as $column)
            {

                echo '<tr>';
                foreach($column as $row)
                   {
                     echo '<td>';
                     echo $row;
                     echo '</td>';
                    }
                echo '</tr>';
            }
            echo '</table>';
        }
}
         echo '<h1 style="text-align:center;">All Records From Accounts Table</h1>';
         $records = accounts::create();
         $result = $records->findAll();
         table::makeTable($result);
         echo '<br>';
         echo '<br>';
         echo '<h1 style="text-align:center;">Select ID from Accounts Table, ID is : 2 </h1>';
         $result= $records->findOne(2);
         table::makeTable($result);
         echo '<br>';
         echo '<br>';
         echo '<br>';
         echo '<h1 style="text-align:center;">All Records From Todos Table </h1>';
         $records = todos::create();
         $result= $records->findAll();
         table::makeTable($result);
         echo '<br>';
         echo '<br>';
         echo '<h1 style="text-align:center;">Select ID  from Todos Table, ID is : 5 </h1>';
         $result= $records->findOne(5);
         table::makeTable($result);
         echo '<h1 style="text-align:center;">Update Fname Column in Accounts Table where ID is : 6 </h1>';
         $obj = new account;
         $obj->save();
         $records = accounts::create();
         $result = $records->findAll();
         table::makeTable($result);
         echo '<br>';
         echo '<br>';
         echo '<h1 style="text-align:center;">Insert New Row in Todos </h1>';
         $obj = new todo;
         $obj->save();
         $records = todos::create();
         $result= $records->findAll();
         table::makeTable($result);
         echo '<br>';
         echo '<br>';
         echo '<h1 style="text-align:center;">Delete ID 6 from Todos Table </h1>';
         $obj = new todo;
         $obj->delete();
         $records = todos::create();
         $result= $records->findAll();
         table::makeTable($result);

        
?>
