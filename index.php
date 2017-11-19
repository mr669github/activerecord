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
    public function __construct()
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
         $recordsSet  =  $stmt->fetchAll();
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
                $sql = "Update ".static::$table. " SET ".static::$columnToUpdate."='".static::$newInfo."' WHERE id=".static::$id;
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


?>