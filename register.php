<?php
// register.php — creates a new user in JSONBin (appends to array) with first/last/email/username/password
// Expects: POST JSON { firstName, lastName, email, username, password }
// Returns: 201 { ok: true } or 4xx { error }

header('Content-Type: application/json');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$firstName = trim($input['firstName'] ?? '');
$lastName  = trim($input['lastName'] ?? '');
$email     = trim($input['email'] ?? '');
$username  = trim($input['username'] ?? '');
$password  = (string)($input['password'] ?? '');

// Basic validation
$validName = (bool)preg_match("/^[\p{L} .'-]{1,60}$/u", $firstName) && (bool)preg_match("/^[\p{L} .'-]{1,60}$/u", $lastName);
if (!$validName) { http_response_code(400); echo json_encode(['error'=>'Invalid name']); exit; }
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { http_response_code(400); echo json_encode(['error'=>'Invalid email']); exit; }
if ($username === '' || strlen($username) < 3 || strlen($username) > 24 || !preg_match('/^[a-zA-Z0-9_.\-]+$/', $username)) {
  http_response_code(400); echo json_encode(['error'=>'Invalid username']); exit;
}
if (strlen($password) < 8) { http_response_code(400); echo json_encode(['error'=>'Password too short']); exit; }

// Load JSONBin credentials
$BIN_ID     = getenv('JSONBIN_BIN_ID')     ?: null;
$MASTER_KEY = getenv('JSONBIN_MASTER_KEY') ?: null;
if (!$BIN_ID || !$MASTER_KEY) {
  $cfgPath = __DIR__ . '/jsonbin.config.php';
  if (is_file($cfgPath)) { $cfg = require $cfgPath; $BIN_ID = $BIN_ID ?: ($cfg['BIN_ID'] ?? null); $MASTER_KEY = $MASTER_KEY ?: ($cfg['MASTER_KEY'] ?? null); }
}
if (!$BIN_ID || !$MASTER_KEY) { http_response_code(500); echo json_encode(['error'=>'Server not configured for JSONBin']); exit; }

// Read users
$ch = curl_init("https://api.jsonbin.io/v3/b/$BIN_ID/latest");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [ "X-Master-Key: $MASTER_KEY", "X-Bin-Meta: false" ],
  CURLOPT_TIMEOUT => 10
]);
$res = curl_exec($ch);
$err = curl_error($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($err || $code >= 400 || !$res) { http_response_code(502); echo json_encode(['error'=>'Failed to read database']); exit; }

$data = json_decode($res, true);
$users = isset($data['record']) ? $data['record'] : $data;
if (!is_array($users)) $users = [];

// Uniqueness checks (case-insensitive)
foreach ($users as $u) {
  if (!is_array($u)) continue;
  if (isset($u['username']) && strcasecmp($u['username'], $username) === 0) { http_response_code(409); echo json_encode(['error'=>'Username already taken']); exit; }
  if (isset($u['email']) && strcasecmp($u['email'], $email) === 0) { http_response_code(409); echo json_encode(['error'=>'Email already registered']); exit; }
}

// Append new user with password hash
$newUser = [
  'userId' => 'user_' . bin2hex(random_bytes(6)),
  'firstName' => $firstName,
  'lastName'  => $lastName,
  'username'  => $username,
  'email'     => $email,
  'passwordHash' => password_hash($password, PASSWORD_DEFAULT),
  'favoriteGenres' => [],
  'likedMovies' => [],
  'recommendedMovies' => []
];
$users[] = $newUser;
