<?php
require_once 'base.php';

$db = new Database();
$stmt = $db->getPdo()->prepare("SELECT NOW()");
$stmt->execute();

echo json_encode($stmt->fetch());