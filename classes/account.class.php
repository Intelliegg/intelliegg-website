<?php

require_once 'database.php';

class Account {

    public $id;
    public $email;
    public $password;
    protected $db;

    function __construct() {
        $this->db = new Database();
    }
    public function getAccountByEmail($email) {
        $sql = "SELECT * FROM account WHERE email = :email LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':email', $email);
        $query->execute();

        return $query->fetch(PDO::FETCH_ASSOC);
    }

    function sign_in_customer() {
        $sql = "SELECT * FROM account WHERE email = :email LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':email', $this->email);

        if ($query->execute()) {
            $accountData = $query->fetch(PDO::FETCH_ASSOC);

            if ($accountData && password_verify($this->password, $accountData['password'])) {
                if ($accountData['status'] == 'verified') {
                    $this->id = $accountData['id'];
                    return 'verified';
                } else {
                    $this->id = $accountData['id'];
                    return 'not_verified';
                }
            }
        }

        return false;
    }
    public function updatePassword($email, $hashedPassword) {
        $sql = "UPDATE account SET password = :password WHERE email = :email";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':email', $email);
        $query->bindParam(':password', $hashedPassword);

        try {
            $query->execute();
            return true; // Password updated successfully
        } catch (PDOException $e) {
            echo "Error updating password: " . $e->getMessage();
            return false; // Failed to update password
        }
    }
}
?>
