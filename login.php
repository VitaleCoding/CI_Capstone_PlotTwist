<?php
header('Content-Type: application/json');

// Your jsonbin.io credentials
$apiKey = "$2a$10$R1S9oi04F7AYLcy30BU37eHzClLDoyFBvYWdA3OZBYBm7IixjYbn";
$binId = "68db3f4bae596e708f009e5c";

$input = json_decode(file_get_contents("php://input"), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (!$username || !$password) {
    echo json_encode(["success" => false, "error" => "Missing username or password."]);
    exit;
}

// Fetch user data from jsonbin.io
$url = "https://api.jsonbin.io/v3/b/$binId/latest";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "X-Master-Key: $apiKey",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    echo json_encode(["success" => false, "error" => "Could not connect to jsonbin.io."]);
    exit;
}

$data = json_decode($response, true);
$users = $data['record'] ?? [];

$foundUser = null;
foreach ($users as $user) {
    if (
        ($user['username'] === $username || $user['email'] === $username) &&
        $user['password'] === $password
    ) {
        $foundUser = $user;
        break;
    }
}

if ($foundUser) {
    echo json_encode([
        "success" => true,
        "username" => $foundUser['username'],
        "favoriteGenres" => $foundUser['favoriteGenres'] ?? []
    ]);
} else {
    echo json_encode([
        "success" => false,
        "error" => "Invalid username or password."
    ]);
}
?>
