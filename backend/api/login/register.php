<?php
require_once __DIR__ . '/../../utils_loader.php';

Session::allowUnauthenticatedOnly();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('Invalid request method', Response::HTTP_METHOD_NOT_ALLOWED);
    exit;
}

$email = isset($_POST['email']) ? $_POST['email'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;
$repeatedPassword = isset($_POST['repeated_password']) ? $_POST['repeated_password'] : null;
$name = isset($_POST['name']) ? $_POST['name'] : null;

if (empty($email) || empty($password) || empty($repeatedPassword) || empty($name)) {
    Response::error('Missing required fields', Response::HTTP_BAD_REQUEST);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    Response::error('Invalid email format', Response::HTTP_BAD_REQUEST);
    exit;
}

if ($password !== $repeatedPassword) {
    Response::error('Passwords do not match', Response::HTTP_BAD_REQUEST);
    exit;
}

$db = new Database();

$emailCheckStmt = $db->getPdo()->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
$emailCheckStmt->execute(['email' => $email]);
if ($emailCheckStmt->fetchColumn() > 0) {
    Response::error('Email is already registered', Response::HTTP_CONFLICT);
    exit;
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);

$insertStmt = $db->getPdo()->prepare('INSERT INTO users (email, password_hash, name) VALUES (:email, :password_hash, :name)');
$success = $insertStmt->execute(['email' => $email, 'password_hash' => $passwordHash, 'name' => $name]);

if (!$success) {
    Response::error('Failed to register user', Response::HTTP_INTERNAL_SERVER_ERROR);
    exit;
}

Response::json(['message' => 'User registered successfully'], Response::HTTP_CREATED);