<?php
require_once __DIR__ . '/../../utils_loader.php';

Session::allowUnauthenticatedOnly();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::json(['error' => 'Invalid request method'], Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

$email = isset($_POST['email']) ? $_POST['email'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$repeatedPassword = isset($_POST['repeated_password']) ? $_POST['repeated_password'] : null;

if (empty($email) || empty($password) || empty($repeatedPassword)) {
    Response::json(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    Response::json(['error' => 'Invalid email format'], Response::HTTP_BAD_REQUEST);
    exit;
}

if ($password !== $repeatedPassword) {
    Response::json(['error' => 'Passwords do not match'], Response::HTTP_BAD_REQUEST);
    exit;
}

$db = new Database();

$emailCheckStmt = $db->getPdo()->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
$emailCheckStmt->execute(['email' => $email]);
if ($emailCheckStmt->fetchColumn() > 0) {
    Response::json(['error' => 'Email is already registered'], Response::HTTP_CONFLICT);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);

$insertStmt = $db->getPdo()->prepare('INSERT INTO users (email, password_hash) VALUES (:email, :password_hash)');
$success = $insertStmt->execute(['email' => $email, 'password_hash' => $passwordHash]);

if (!$success) {
    Response::json(['error' => 'Failed to register user'], Response::HTTP_INTERNAL_SERVER_ERROR);
    exit;
}

Response::json(['message' => 'User registered successfully'], Response::HTTP_CREATED);