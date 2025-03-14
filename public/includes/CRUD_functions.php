<?php
if (!class_exists('User')) {
    class User {
        private $pdo;

        public function __construct() {
            $config = include('db_server.php'); // ✅ Updated to match your new config file

            try {
                $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
                $this->pdo = new PDO($dsn, $config['username'], $config['password']);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }

        public function getPdo() {
            return $this->pdo;
        }

        // 🔹 Read Function - Fetch all users from the "user" table
        public function readUsers() {
            try {
                $stmt = $this->pdo->prepare("SELECT iduser, f_name, l_name, usertype, email, password, otp, otp_expiry FROM user"); // ✅ Updated table name
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Error fetching users: " . $e->getMessage());
            }
        }
        public function createUser($f_name, $l_name, $email, $password) {
            try {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT); // 🔒 Bcrypt hashing
        
                $stmt = $this->pdo->prepare("
                    INSERT INTO user (f_name, l_name, email, password)
                    VALUES (:f_name, :l_name, :email, :password)
                ");
                $stmt->execute([
                    ':f_name' => $f_name,
                    ':l_name' => $l_name,
                    ':email' => $email,
                    ':password' => $hashedPassword
                ]);
                return true;
            } catch (PDOException $e) {
                die("Error creating user: " . $e->getMessage());
            }
        }
        public function updateUser($iduser, $f_name, $l_name, $email, $password = null) {
            try {
                // Check if password is provided for updating
                if ($password) {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                    $sql = "UPDATE user SET f_name = :f_name, l_name = :l_name, email = :email, password = :password WHERE iduser = :iduser";
                } else {
                    $sql = "UPDATE user SET f_name = :f_name, l_name = :l_name, email = :email WHERE iduser = :iduser";
                }
        
                $stmt = $this->pdo->prepare($sql);
        
                $params = [
                    ':f_name' => $f_name,
                    ':l_name' => $l_name,
                    ':email' => $email,
                    ':iduser' => $iduser
                ];
        
                // Include password only if provided
                if ($password) {
                    $params[':password'] = $hashedPassword;
                }
        
                $stmt->execute($params);
        
                return true;
            } catch (PDOException $e) {
                die("Error updating user: " . $e->getMessage());
            }
        }
        public function getUserById($iduser) {
            try {
                $stmt = $this->pdo->prepare("SELECT iduser, f_name, l_name, email FROM user WHERE iduser = :iduser");
                $stmt->execute([':iduser' => $iduser]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Error fetching user: " . $e->getMessage());
            }
        }
        
        public function deleteUser($iduser) {
            try {
                $stmt = $this->pdo->prepare("DELETE FROM user WHERE iduser = :iduser");
                return $stmt->execute([':iduser' => $iduser]);
            } catch (PDOException $e) {
                die("Error deleting user: " . $e->getMessage());
            }
        }
        
        
    }
}
?>
