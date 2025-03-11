<?php
header("Content-Type: application/json");

$response = array();
$status_code = 200;

try {
    // Load environment variables if using dotenv
    // $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    // $dotenv->load();

    // Connection string
    $connectionString = "postgresql://neondb_owner:npg_JVjzC2O7YPeg@ep-silent-block-a5v8vaqt-pooler.us-east-2.aws.neon.tech/neondb?sslmode=require";
    $url = parse_url($connectionString);

    $servername = $url["host"];
    $port = $url["port"];
    $username = $url["user"];
    $password = $url["pass"];
    $dbname = ltrim($url["path"], "/");

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Sanitize and validate inputs
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if (!$email) {
        throw new Exception("Invalid email format");
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $name, $email);

    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $response['message'] = "New record created successfully";

} catch (Exception $e) {
    $status_code = 500;
    $response['error'] = $e->getMessage();
}

// Close connections
if (isset($stmt)) {
    $stmt->close();
}
if (isset($conn)) {
    $conn->close();
}

// Send response
http_response_code($status_code);
echo json_encode($response);
?>
