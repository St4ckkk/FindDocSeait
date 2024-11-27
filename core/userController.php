<?php

include_once 'Database.php';

class userController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getUserAdmin()
    {
        $query = "SELECT users.*, roles.role_name 
                  FROM users 
                  JOIN roles ON users.role_id = roles.role_id 
                  WHERE roles.role_name = 'admin' OR roles.role_name = 'super_admin'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUsersByOffice($office_id)
    {
        $query = "SELECT users.*, roles.role_name 
                  FROM users 
                  JOIN roles ON users.role_id = roles.role_id 
                  WHERE users.office_id = :office_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':office_id', $office_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserRole($id)
    {
        $query = "SELECT roles.role_name 
                  FROM users 
                  JOIN roles ON users.role_id = roles.role_id 
                  WHERE users.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['role_name'];
    }

    public function addAdmin($params)
    {
        // Check if the username or email already exists
        $checkQuery = "SELECT COUNT(*) FROM users WHERE username = :username OR email = :email";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':username', $params['username']);
        $checkStmt->bindParam(':email', $params['email']);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn() > 0) {
            return ['status' => 'error', 'message' => 'Username or email already exists'];
        }

        // Insert the new admin
        $query = "INSERT INTO users (fullname, username, password, email, role_id, office_id) VALUES (:fullname, :username, :password, :email, :role_id, :office_id)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':fullname', $params['fullname']);
        $stmt->bindParam(':username', $params['username']);
        $hashedPassword = password_hash($params['password'], PASSWORD_BCRYPT); // Hash the password
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $params['email']);
        $stmt->bindParam(':role_id', $params['role_id']);
        $stmt->bindParam(':office_id', $params['office_id']);

        if ($stmt->execute()) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to add admin'];
        }
    }

    public function addOffice($params)
    {
        // Check if the office name already exists
        $checkQuery = "SELECT COUNT(*) FROM offices WHERE name = :name";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':name', $params['name']);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn() > 0) {
            return ['status' => 'error', 'message' => 'Office name already exists'];
        }

        // Insert the new office
        $query = "INSERT INTO offices (name) VALUES (:name)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $params['name']);

        if ($stmt->execute()) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to add office'];
        }
    }

    public function getUserAssignedOffice($office_id)
    {
        $query = "SELECT users.*, roles.role_name 
                  FROM users 
                  JOIN roles ON users.role_id = roles.role_id 
                  WHERE users.office_id = :office_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':office_id', $office_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOffices()
    {
        $query = "SELECT * FROM offices";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUsers()
    {
        $query = "SELECT users.*, roles.role_name, offices.name as office_name 
              FROM users 
              JOIN roles ON users.role_id = roles.role_id
              LEFT JOIN offices ON users.office_id = offices.office_id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRoles()
    {
        $query = "SELECT * FROM roles";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getUserPermissions($user_id)
    {
        $query = "SELECT permission FROM user_permissions WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $permissions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $permissions[] = $row['permission'];
        }
        return $permissions;
    }

    public function updateUserRole($user_id, $role_id)
    {
        $query = "UPDATE users SET role_id = :role_id WHERE id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':role_id', $role_id);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            return ['status' => 'success'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update role'];
        }
    }

    public function getUserLoginLogs()
    {
        $query = "SELECT id, user_id, ip_address, user_agent, status, risk_level, device_type, os_type, browser_type 
                  FROM login_logs 
                  WHERE 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}