<?php

require '../vendor/autoload.php';
Dotenv\Dotenv::createImmutable(dirname(__FILE__, 2))->load();

date_default_timezone_set('Asia/Ho_Chi_Minh');
class Database
{
    private $database  = "";
    private $username  = "";
    private $password  = "";
    private $localhost   = "";
    private $conn = false;
    private $pdo = "";
    private $result = array();

    public function __construct()
    {
        $this->localhost  = $_ENV['LOCALHOST'];
        $this->database  = $_ENV['DATABASE'];
        $this->username  = $_ENV['USERNAME'];
        $this->password  = $_ENV['PASSWORD'];
        if (!$this->conn) {
            $this->pdo = new PDO("mysql:host=" . $this->localhost . ";dbname=" . $this->database, $this->username, $this->password, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
            $this->conn = true;
        }
        if ($this->conn) {
            return true;
        }
        if ($this->pdo->errorInfo()) {
            array_push($this->result, $this->pdo->errorInfo());
            return false;
        } else {
            return true;
        }
    }
    //close connection
    public function __destruct()
    {
        if ($this->conn) {
            $this->pdo === null;
            $this->conn = false;
            return true;
        } else {
            return false;
        }
    }

    //get result and the assignment is an empty array
    public function getResult()
    {
        $val = $this->result;
        $this->result = array();
        return $val;
    }
    //function check table exist or not
    private function tableExist($table)
    {
        $sql = "SHOW TABLES FROM $this->database LIKE '{$table}'";
        $tableInDb = $this->pdo->query($sql);
        if ($tableInDb) {
            if ($tableInDb->rowCount() == 1) {
                return true;
            } else {
                array_push($this->result, "Table '{$table}' does not exist in Database '{$this->database}'");
            }
        } else {
            return false;
        }
    }

    //get data
    public function select($table, $column = "*", $join = null, $on = null, $where = null, $order = null, $limit = null, $groupBy = null)
    {
        try {
            if ($this->tableExist($table)) {
                $sql = "SELECT $column FROM $table";
                if ($join) {
                    $sql .= " JOIN $join";
                }
                if ($on) {
                    $sql .= " ON $on";
                }
                if ($where) {
                    $sql .= " WHERE $where";
                }
                if ($groupBy) {
                    $sql .= " GROUP BY $groupBy";
                }
                if ($order) {
                    $sql .= " ORDER BY $order";
                }
                if ($limit) {
                    $sql .= " LIMIT $limit";
                }
                $query = $this->pdo->query($sql);
                $this->result = $query->fetchAll(PDO::FETCH_ASSOC);
                return true;
            } else {
                return false;
            }
        } catch (PDOException $err) {
            array_push($this->result, $err);
            return false;
        }
    }

    //update
    public function update($table, $params = array(), $where = null)
    {
        try {
            if ($this->tableExist($table)) {
                $arg = array();
                foreach ($params as $key => $value) {
                    $arg[] = "$key = '$value'";
                }
                $sql = "UPDATE $table SET " . implode(', ', $arg) . " WHERE $where";
                $this->pdo->query($sql);

                return true;
            } else {
                return false;
            }
        } catch (PDOException $err) {
            array_push($this->result, $err);
            return false;
        }
    }

    //create
    public function insert($table, $params = array())
    {
        try {
            if ($this->tableExist($table)) {
                $table_column = implode(',', array_keys($params));
                $table_value = implode("','", array_values($params));
                $sql = "INSERT INTO $table ($table_column) VALUES ('$table_value')";

                $query = $this->pdo->query($sql);
                $this->result = $this->pdo->lastInsertId();
                return true;
            } else {
                return false;
            }
        } catch (PDOException $err) {
            array_push($this->result, $err);
            return false;
        }
    }

    //delete
    public function delete($table, $where = null)
    {
        try {
            if ($this->tableExist($table)) {
                $sql = "DELETE FROM $table WHERE $where";
                $this->pdo->query($sql);
                return true;
            } else {
                return false;
            }
        } catch (PDOException $err) {
            array_push($this->result, $err);
            return false;
        }
    }

    //get connection
    public function getConnection()
    {
        return $this->pdo;
    }
}
