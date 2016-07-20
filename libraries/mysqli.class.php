<?php

/* TODO: Add code here */

class Database {

    //数据库访问类 -- order server
    private $server = "";
    private $port = "3306";
    private $user = "";
    private $pwd = "";
    private $database = "";
    public $isOpen = false;
    public $Conn = false;
    public $Error = array("errno" => 0, "message" => "");
    public $Result = array();               //保存查询后的结果集,Result[0]=第一个结果集

    //					  Result[0][0]=第一个结果集的第一行
    //					  Result[0][0][0]=第一个结果集的第一行的第一列,列也可用数据表中的字段名表示
    //构造函数
    function __construct($db) {
        //$this->server=Peng58admin::int2ip($server);
        //die(11111111111111);
        $this->server = $db['host'];
        $this->user = $db['user'];
        $this->pwd = $db['pwd'];
        //$this->port=$port;
        $this->database = $db['dbname'];
        //die(var_dump($this));
        $this->Open();
    }

    //打开数据库连接
    public function Open() {
        //echo ($this->server)."<br>";echo ($this->user)."<br>";echo ($this->pwd)."<br>";echo ($this->database)."<br>";exit;
        $this->Conn = mysqli_connect($this->server, $this->user, $this->pwd, $this->database);

        //echo mysqli_connect_error();
        if (mysqli_connect_errno() > 0) {
            $Error["error"] = mysqli_connect_errno();
            $Error["message"] = mysqli_connect_error();
            $this->isOpen = false;
            return false;
        }

        mysqli_set_charset($this->Conn, "utf8");

        $this->isOpen = true;
        return true;
    }

    //执行多语句查询并将所有结果存入Result中
    public function Query($query) {
        if ($this->isOpen) {
            if (@mysqli_multi_query($this->Conn, $query)) {
                $i = 0;
                do {
                    if ($result = mysqli_store_result($this->Conn)) {
                        $j = 0;
                        while ($row = mysqli_fetch_array($result)) {
                            $this->Result[$i][$j] = $row;
                            $j++;
                        }
                        mysqli_free_result($result);
                        $i++;
                    }
                } while (mysqli_next_result($this->Conn));
            }

//            if (mysqli_errno($this->Conn) == 0)
//                return true;
//            else {
//                $this->Error["errno"] = mysqli_errno($this->Conn);
//                $this->Error["message"] = mysqli_error($this->Conn);
//                return false;
//            }
        } else {
            //return false;
        }
    }

    //清除Result中的结果集
    public function ClearResult() {
        $this->Result = array();
    }

    /**
     * 根据传入的查询语句返回数集;
     * @param string $sql 传入的mysql查询语句
     */
    public function ReturnDataTable($sql) {
        if ($this->Open()) {
            $db = $this->Conn;
            $db->query($sql);
//            $result = $db->query($sql);
//            if ($result) {
//                $row = $result->fetch_array();
//                $i = 0;
//                while ($row) {
//                    $datatable[$i] = $row;
//                    $i++;
//                    $row = $result->fetch_array();
//                }
//            }
            //return $datatable;
        } else {
            //return null;
        }
    }

    //关闭数据库连接
    public function Close() {
        if ($this->isOpen) {
            mysqli_close($this->Conn);
        }
    }

}

/* $db=new Database();
  $conn=$db->Open();
  if (!$conn)
  {
  echo("fail");
  }
  else
  {
  $db->Close();
  echo("成功");
  } */
?>