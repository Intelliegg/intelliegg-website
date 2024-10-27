<?php

require_once 'database.php';

Class Customer{
    //attributes

    public $id;
    public $username;
    public $email;
    public $password;

    protected $db;

    function __construct()
    {
        $this->db = new Database();
    }
    function add(){
        $sql = "INSERT INTO account (username, email, password, status) VALUES 
        (:username, :email, :password, 'not_verified');";

        $query=$this->db->connect()->prepare($sql);
        $query->bindParam(':username', $this->username);
        $query->bindParam(':email', $this->email);
        // Hash the password securely using password_hash
        $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
        $query->bindParam(':password', $hashedPassword);
        
        if($query->execute()){
            return true;
        }
        else{
            return false;
        }	
    }
    function activate_account(){
        $sql = "UPDATE account SET status = 'verified' WHERE email = :email;";
        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':email', $this->email);
        return $query->execute();
    }

    function is_email_exist(){
        $sql = "SELECT * FROM account WHERE email = :email;";
        $query=$this->db->connect()->prepare($sql);
        $query->bindParam(':email', $this->email);
        if($query->execute()){
            if($query->rowCount()>0){
                return true;
            }
        }
        return false;
    }
}

?>