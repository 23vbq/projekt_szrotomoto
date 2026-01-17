<?php
require_once __DIR__ . '/../../utils_loader.php';

Session::allowAuthenticatedOnly();

Session::logout();
Response::json(['message' => 'Logout successful']);