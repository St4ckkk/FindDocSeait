<?php
include_once 'Database.php';

class VirtualIPManager
{
    private $db;
    private $ip_ranges = [
        '192.168.0.0/16',  // Private network range
        '172.16.0.0/12',   // Private network range
        '10.0.0.0/8'       // Private network range
    ];

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // private function initializeIPTable()
    // {
    //     $sql = "CREATE TABLE IF NOT EXISTS virtual_ips (
    //         id INT AUTO_INCREMENT PRIMARY KEY,
    //         user_id INT UNIQUE,
    //         virtual_ip VARCHAR(45),
    //         assigned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    //         last_used TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    //         is_blocked BOOLEAN DEFAULT FALSE,
    //         block_reason TEXT,
    //         FOREIGN KEY (user_id) REFERENCES users(id)
    //     )";
    //     $stmt = $this->db->prepare($sql);
    //     $stmt->execute();
    // }

    private function generateRandomIP()
    {
        $range = $this->ip_ranges[array_rand($this->ip_ranges)];
        list($network, $mask) = explode('/', $range);
        $network_binary = ip2long($network);
        $mask_binary = ~((1 << (32 - $mask)) - 1);
        $random_ip = $network_binary | (~$mask_binary & rand(1, (1 << (32 - $mask)) - 2));
        return long2ip($random_ip);
    }

    public function assignVirtualIP($user_id)
    {
        $stmt = $this->db->prepare("SELECT virtual_ip FROM virtual_ips WHERE user_id = ?");
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['virtual_ip'];
        }

        do {
            $virtual_ip = $this->generateRandomIP();
            $stmt = $this->db->prepare("SELECT id FROM virtual_ips WHERE virtual_ip = ?");
            $stmt->bindParam(1, $virtual_ip);
            $stmt->execute();
        } while ($stmt->fetch(PDO::FETCH_ASSOC));

        $stmt = $this->db->prepare("INSERT INTO virtual_ips (user_id, virtual_ip) VALUES (?, ?)");
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $virtual_ip);
        $stmt->execute();

        return $virtual_ip;
    }

    public function blockVirtualIP($virtual_ip, $reason = 'Suspicious activity')
    {
        $stmt = $this->db->prepare("UPDATE virtual_ips SET is_blocked = TRUE, block_reason = ? WHERE virtual_ip = ?");
        $stmt->bindParam(1, $reason);
        $stmt->bindParam(2, $virtual_ip);
        return $stmt->execute();
    }

    public function isIPBlocked($virtual_ip)
    {
        $stmt = $this->db->prepare("SELECT is_blocked FROM virtual_ips WHERE virtual_ip = ?");
        $stmt->bindParam(1, $virtual_ip);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['is_blocked'];
        }
        return false;
    }

    public function getVirtualIP($user_id)
    {
        $stmt = $this->db->prepare("SELECT virtual_ip FROM virtual_ips WHERE user_id = ?");
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return $result['virtual_ip'];
        }
        return null;
    }
}
?>