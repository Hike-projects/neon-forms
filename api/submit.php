<?php
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
  die("Connection failed: " . $conn->connect_error);
}

$name = $_POST['name'];
$email = $_POST['email'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
$stmt->bind_param("ss", $name, $email);

if ($stmt->execute()) {
  echo "New record created successfully";
} else {
  echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
