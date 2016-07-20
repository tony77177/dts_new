<?php

/**
 * @copyright phoneplus.com
 * @file:DB.class.php
 * @author: lu xingyun
 * @update:2012-10-2
 * @version 1.0
 * */
class db {

    private $dbHost;
    private $dbUser;
    private $dbPwd;
    private $dbName;
    private $charset;
    public $errMsg;
    public $conn;
    public $result = array();

    public function __construct($db) {
        //$dbHost,$dbUser,$dbPwd,$dbName=""
        $this->dbHost = $db['host'];
        $this->dbUser = $db['user'];
        $this->dbPwd = $db['pwd'];
        $this->dbName = $db['databasename'];
        $this->charset=$db['charset'];
        $this->connect();
        $this->selectDb();
    }

    public function connect() {
        if (!empty($this->dbHost)) {
            $this->conn = @mysql_connect($this->dbHost, $this->dbUser, $this->dbPwd) or die("不能连接到:$this->dbHost");
            if (!$this->conn) {
                die("数据库链接失败！");
            }
        } else {
            die("数据库主机不能为空！");
        }
    }

    public function selectDb() {
        if (!empty($this->dbName)) {
            $rs = @mysql_select_db($this->dbName) or die("找不到数据库：$this->dbName");
            @mysql_set_charset("utf8", $this->conn);
        }
    }

    //向数据库发送一条SQL语句
    public function exec($arg1) {
        return mysql_fetch_array(mysql_query($arg1, $this->conn));
    }
    
    /**
     * 执行SQL返回一个结果集
     * @param type $sql
     * @return type
     */
    public function query($sql){
        return mysql_fetch_array(mysql_query($sql));
    }

//MySQL 查询功能
//例子: $this->select("*","users","username='dhruv'",array('limit_start'=>1,'limit'=>10,'order_by'=>'order_id'));
//* 用fields 去超找
//users 是表明
// username='dhruv' 是条件
//'limit_start'=>1,'limit'=>10,'order_by'=>'order_id'是以limit_start未开始查找，查找10条，对order_id经行 order by

    public function select($fields = "*", $table, $conditions = "", $options = array()) {
        $this->result = array();   //初始化
        $do = "SELECT " . $fields . " FROM " . $table;
        if ($conditions != "") {
            $do .= " WHERE " . $conditions;
        }
        if (isset($options['order_by'])) {
            $do .= " ORDER BY " . $options['order_by'];
            if (isset($options['order_dir'])) {
                $do .= " " . my_strtoupper($options['order_dir']);
            }
        }
        if (isset($options['limit_start']) && isset($options['limit'])) {
            $do .= " LIMIT " . $options['limit_start'] . ", " . $options['limit'];
        } elseif (isset($options['limit'])) {
            $do .= " LIMIT " . $options['limit'];
        }
        $exeSql = $this->exec($do);
        if ($exeSql) {
            while ($row = $this->fetch_array($exeSql)) {
                $this->result[] = $row;
            }
            return $this->result;
        }
        else
            die("查找数据失败！执行的SQL语句是：" . $sql);
        //return $this->exec($do);
    }

//  查找结果集的条数
    public function num_rows($arg1) {
        $num = mysql_fetch_row($arg1);
        return $num[0];
    }

//对SQL语句经行执行查找  
    public function fetch_array($arg1) {
        return mysql_fetch_array($arg1);
    }

//数据库插入功能
    public function insert($table, $fields, $values) {
        $sql = "INSERT INTO " . $table . "(" . $fields . ") VALUES (" . $values . ")";
        $exeSql = $this->exec($sql);
        if ($exeSql)
            return $exeSql;
        else
            die("插入数据失败！执行的SQL语句是：" . $sql);
        //return $this->exec("INSERT INTO ".$arg1."(".$arg2.") VALUES (".$arg3.")");
    }

    //查找数据库是否存在 比如查找db_stat
    public function select_db($arg1) {
        return mysql_select_db($arg1);
    }

    //关闭MySQL 的连接
    public function close() {
        mysql_close();
    }

//查看 table  表的信息  
    public function show_fields_from($table) {
        $do = "SHOW FIELDS FROM " . $table;
        $query = $this->exec($do);
        while ($field = $this->fetch_array($query)) {
            $field_info[] = $field;
        }
        return $field_info;
    }

//添加转义字符

    public function escape($string) {
        if (function_exists("mysql_real_escape_string")) {
            $string = mysql_real_escape_string($string);
        } else {
            $string = addslashes($string);
        }
        return $string;
    }

//删除表的数据
    public function delete($table, $where = "", $limit = "") {
        $do = "";
        if (!empty($where)) {
            $do .= " WHERE $where";
        }

        if (!empty($limit)) {
            $do .= " LIMIT $limit";
        }
        $sql = "DELETE FROM $table $do";
        $exeSql = $this->exec($sql);
        if ($exeSql)
            return $exeSql;
        else
            die("删除数据失败！执行的SQL语句是：" . $sql);
        /* return $this->exec("
          DELETE
          FROM $table
          $do
          "); */
    }

//更新数据库
    public function update($table, $array = array(), $where = "", $limit = "", $no_quote = false) {//print_r($array);exit;
        if (!is_array($array)) {
            return false;
        }//echo 2;exit;

        $comma = "";
        $do = "";
        $quote = "'";

        if ($no_quote == true) {
            $quote = "";
        }

        foreach ($array as $field => $value) {
            $do .= $comma . "`" . $field . "`={$quote}{$value}{$quote}";
            $comma = ', ';
        }

        if (!empty($where)) {
            $do .= " WHERE $where";
        }

        if (!empty($limit)) {
            $do .= " LIMIT $limit";
        }
        $sql = "UPDATE $table SET $do";
        $exeSql = $this->exec($sql);
        if ($exeSql)
            return $exeSql;
        else
            die("更新数据失败！执行的SQL语句是：$sql");
        //return $this->exec();
    }

//添加转义字符，在一些特殊符号前面添加\,从而达到转义的目的，比如在单引号前面添加转义字符    
    function escape_string_like($string) {
        return $this->escape(str_replace(array('%', '_'), array('\\%', '\\_'), $string));
    }

//删除数据库中的一个表  
    function drop_table($table) {
        $do = "DROP TABLE " . $table;
        $result = $this->exec($do);
        return $result;
    }

//对数据库影响的行数
    function affect_rows() {
        return mysql_affected_rows();
    }

//Returns the name of the character set
//$arg1 should be the mysql connection ..like $arg1 = mysql_connect('localhost','user','pass');
    function client_encod($arg1) {
        return mysql_client_encoding($arg1);
    }

//查询数据库的名字 
    function mysql_current_db() {
        return $this->exec("SELECT DATABASE()");
    }



//查询数据库表中字段的长度
    function field_len($arg1) {
        return mysql_field_len($arg1);
    }


//返回结果集中字段的数目
    function num_fields($arg1) {
        return mysql_num_fields($arg1);
    }

}
?> 