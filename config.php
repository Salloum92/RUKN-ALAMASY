<?php

class Database
{
    private $conn;

    public function __construct()
    {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "rukndb";

        $this->conn = new mysqli($servername, $username, $password, $dbname);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        } // 🔥 إضافة هذه الأسطر لحل مشكلة العربية
        $this->conn->set_charset("utf8mb4");
        $this->conn->query("SET NAMES 'utf8mb4'");
        $this->conn->query("SET CHARACTER SET utf8mb4");
        $this->conn->query("SET SESSION collation_connection = 'utf8mb4_unicode_ci'");
    }

    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }

    // 🔥 تحديث دالة التحقق لدعم العربية
    function validate($value)
    {
        if (is_string($value)) {
            $value = trim($value);
            $value = stripslashes($value);
            $value = $this->conn->real_escape_string($value);
        }
        return $value;
    }

    function eQuery($sql, $params = [])
    {
        // 🔥 التأكد من أن الترميز مضبوط قبل كل استعلام
        $this->conn->set_charset("utf8mb4");
        
        if ($stmt = $this->conn->prepare($sql)) {
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }

            if ($stmt->execute()) {
                if (strpos($sql, 'SELECT') === 0) {
                    $result = $stmt->get_result();
                    $data = $result->fetch_all(MYSQLI_ASSOC);
                    
                    // 🔥 تحسين النصوص العربية في النتائج
                    if (is_array($data)) {
                        array_walk_recursive($data, function(&$item, $key) {
                            if (is_string($item)) {
                                $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                            }
                        });
                    }
                    
                    return $data;
                }
                return true;
            } else {
                error_log("ERROR: " . $stmt->error);
                return false;
            }
        } else {
            error_log("ERROR: " . $this->conn->error);
            return false;
        }
    }

    public function executeQuery($sql)
    {
        // 🔥 التأكد من الترميز قبل التنفيذ
        $this->conn->set_charset("utf8mb4");
        
        $result = $this->conn->query($sql);
        if ($result === false) {
            die("ERROR: " . $this->conn->error);
        }
        
        // 🔥 معالجة النتائج لدعم العربية
        if ($result instanceof mysqli_result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
            array_walk_recursive($data, function(&$item, $key) {
                if (is_string($item)) {
                    $item = mb_convert_encoding($item, 'UTF-8', 'UTF-8');
                }
            });
            return $data;
        }
        
        return $result;
    }

    public function select($table, $columns = "*", $condition = "")
    {
        $sql = "SELECT $columns FROM $table $condition";
        $result = $this->executeQuery($sql);
        
        // 🔥 إرجاع النتائج مع دعم العربية
        if (is_array($result)) {
            return $result;
        }
        return [];
    }

    // باقي الدوال تبقى كما هي مع إضافة set_charset إذا لزم الأمر
    public function getById($table, $id)
    {
        $id = intval($id);
        $condition = "WHERE id = $id";
        $result = $this->select($table, "*", $condition);
        return $result ? $result[0] : null;
    }

    function insert($table, $data)
    {
        $this->conn->set_charset("utf8mb4");
        
        $columns = implode(", ", array_keys($data));
        $values = implode(", ", array_map(function ($item) {
            return "'" . $this->conn->real_escape_string($item) . "'";
        }, array_values($data)));

        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        return $this->executeQuery($sql);
    }

    public function update($table, $data, $condition = "")
    {
        $this->conn->set_charset("utf8mb4");
        
        $set = '';
        foreach ($data as $key => $value) {
            $set .= "$key = '" . $this->conn->real_escape_string($value) . "', ";
        }
        $set = rtrim($set, ', ');
        $sql = "UPDATE $table SET $set $condition";
        return $this->executeQuery($sql);
    }

    public function delete($table, $condition = "")
    {
        $sql = "DELETE FROM $table $condition";
        return $this->executeQuery($sql);
    }

    function hashPassword($password)
    {
        return hash_hmac('sha256', $password, "iqbolshoh");
    }

    public function login($username, $password, $table)
    {
        $username = $this->validate($username);
        $condition = "WHERE username = '" . $username . "' AND password = '" . $this->hashPassword($password) . "'";
        return $this->select($table, "*", $condition);
    }

    public function count($table)
    {
        $userId = $_SESSION['id'];
        $result = $this->executeQuery("SELECT COUNT(*) AS total_elements FROM $table WHERE user_id = $userId");
        $row = $result[0] ?? [];
        return $row['total_elements'] ?? 0;
    }

    function lastInsertId()
    {
        return $this->conn->insert_id;
    }
}


?>