<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include the database configuration file
include_once __DIR__ . '/../config/database.php';

// Initialize the database connection
$database = new Database();
$db = $database->getConnection();

// Check if the database connection was successful
if (!$db) {
    echo json_encode(["message" => "Database connection failed."]);
    exit;
}

// Set HTTP headers for API requests
header("Access-Control-Allow-Origin: *"); // Allow requests from any origin
header("Content-Type: application/json"); // Response content type
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE"); // Allowed HTTP methods
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Retrieve the HTTP method of the request
$requestMethod = $_SERVER["REQUEST_METHOD"];

// Upload directory
$uploadDir = __DIR__ . '/../uploads/';

// Handle API requests based on the HTTP method
switch ($requestMethod) {
    case "GET":
        // Retrieve all contacts
        $query = "SELECT * FROM contacts";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($contacts); // Return the result as JSON
        break;

    case "POST":
        // Add a new contact with optional image upload
        if (!empty($_POST["name"]) && !empty($_POST["phone"])) {
            $fileName = null;

            // Handle file upload
            if (!empty($_FILES["profile_picture"]["name"])) {
                $fileName = basename($_FILES["profile_picture"]["name"]);
                $targetFilePath = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
                    echo json_encode(["message" => "Failed to upload image"]);
                    exit;
                }
            }

            $query = "INSERT INTO contacts (name, phone, profile_picture) VALUES (:name, :phone, :profile_picture)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":name", $_POST["name"]);
            $stmt->bindParam(":phone", $_POST["phone"]);
            $stmt->bindParam(":profile_picture", $fileName);
            $stmt->execute();
            echo json_encode(["message" => "Contact created"]);
        } else {
            echo json_encode(["message" => "Incomplete data"]);
        }
        break;

    case "PUT":
        // Update an existing contact with optional image upload
        if (!empty($_POST["id"]) && !empty($_POST["name"]) && !empty($_POST["phone"])) {
            $fileName = null;

            // Handle file upload
            if (!empty($_FILES["profile_picture"]["name"])) {
                $fileName = basename($_FILES["profile_picture"]["name"]);
                $targetFilePath = $uploadDir . $fileName;

                if (!move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
                    echo json_encode(["message" => "Failed to upload image"]);
                    exit;
                }
            }

            $query = "UPDATE contacts SET name = :name, phone = :phone, profile_picture = :profile_picture WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $_POST["id"]);
            $stmt->bindParam(":name", $_POST["name"]);
            $stmt->bindParam(":phone", $_POST["phone"]);
            $stmt->bindParam(":profile_picture", $fileName);
            $stmt->execute();
            echo json_encode(["message" => "Contact updated"]);
        } else {
            echo json_encode(["message" => "Incomplete data"]);
        }
        break;

    case "DELETE":
        // Delete an existing contact
        $data = json_decode(file_get_contents("php://input"), true);
        if (!empty($data["id"])) {
            $query = "DELETE FROM contacts WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $data["id"]);
            $stmt->execute();
            echo json_encode(["message" => "Contact deleted"]);
        } else {
            echo json_encode(["message" => "Incomplete data"]);
        }
        break;

    default:
        // Handle unsupported HTTP methods
        echo json_encode(["message" => "Method not allowed"]);
        break;
}

// Ensure the upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}
?>
