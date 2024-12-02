
<?php
class Database {
    // Database credentials
    private $host = "localhost"; // Server address
    private $db_name = "contacts_db"; // Database name
    private $username = "root"; // MySQL username
    private $password = "38wYTc8k@p8PNxR"; // MySQL password
    public $conn; // Connection instance

    // Method to establish database connection
    public function getConnection() {
        $this->conn = null;
        try {
            // Create a new PDO connection
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error handling
        } catch (PDOException $exception) {
            // Handle connection errors
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
