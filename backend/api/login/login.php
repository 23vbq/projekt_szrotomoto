<?php
require_once __DIR__ . '/../../utils_loader.php';

Session::allowUnauthenticatedOnly();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

$email = isset($_POST['email']) ? $_POST['email'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

if (empty($email) || empty($password)) {
    Response::error('Missing required fields', Response::HTTP_BAD_REQUEST);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    Response::error('Invalid email format', Response::HTTP_BAD_REQUEST);
    exit;
}

$db = new Database();

$loginStmt = $db->getPdo()->prepare('SELECT id, name, password_hash FROM users WHERE email = :email');
$loginStmt->execute(['email' => $email]);
$user = $loginStmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password_hash'])) {
    Response::error('Invalid email or password', Response::HTTP_UNAUTHORIZED);
    exit;
}

Session::login($user['id'], $user['name']);
Response::json([
    'message' => 'Login successful',
    'user_name' => $user['name']
]);