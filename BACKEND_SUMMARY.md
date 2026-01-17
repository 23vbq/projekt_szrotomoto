# Backend API Summary

Quick reference guide for backend endpoints and functionality.

## Authentication Endpoints

### `/api/login/me.php` (GET)
**Purpose**: Check current authentication status  
**Auth Required**: No  
**Response**:
```json
{
  "authenticated": true/false,
  "user_id": 123,
  "user_name": "User Name"
}
```
**Usage**: Used by frontend to determine if user is logged in and show/hide UI elements accordingly.

### `/api/login/login.php` (POST)
**Purpose**: Authenticate user  
**Auth Required**: No (must be unauthenticated)  
**Request Body**: `email`, `password` (form data)  
**Response**: `{ "message": "Login successful", "user_name": "..." }`  
**Sets**: Session cookie with `is_authenticated`, `user_id`, `user_name`

### `/api/login/logout.php` (GET)
**Purpose**: Logout current user  
**Auth Required**: Yes  
**Response**: `{ "message": "Logout successful" }`  
**Clears**: Session data

### `/api/login/register.php` (POST)
**Purpose**: Register new user  
**Auth Required**: No (must be unauthenticated)  
**Request Body**: `email`, `password`, `repeated_password`, `name` (form data)  
**Response**: `{ "message": "User registered successfully" }`  
**Status Codes**: 201 (success), 400 (validation error), 409 (email exists)

## Session Management

**Class**: `Session` (`utils/Session.php`)

**Methods**:
- `Session::allowAuthenticatedOnly()` - Requires authentication, returns 401 if not
- `Session::allowUnauthenticatedOnly()` - Requires no authentication, returns 400 if authenticated
- `Session::login($userId, $userName)` - Sets session variables
- `Session::logout()` - Destroys session
- `Session::getUserId()` - Returns current user ID or null

**Session Variables**:
- `$_SESSION['is_authenticated']` - boolean
- `$_SESSION['user_id']` - integer
- `$_SESSION['user_name']` - string

## Offers Endpoints

### `/api/offers/search.php` (GET)
**Purpose**: Get all active offers  
**Auth Required**: No  
**Response**: Array of offer objects with brand/model/user info

### `/api/offers/show.php?offer_id={id}` (GET)
**Purpose**: Get single offer details  
**Auth Required**: No (but inactive offers only visible to creator)  
**Response**: Full offer object

### `/api/offers/create.php` (POST)
**Purpose**: Create new offer  
**Auth Required**: Yes  
**Request**: multipart/form-data with offer fields + optional `files[]`  
**Response**: `{ "message": "Offer created successfully", "offer_id": 123 }`

### `/api/offers/edit.php?offer_id={id}` (POST)
**Purpose**: Edit existing offer  
**Auth Required**: Yes (must be offer creator)  
**Request**: multipart/form-data with fields to update + optional `files[]`  
**Response**: Updated offer object

### `/api/offers/setAsSold.php?offer_id={id}` (GET)
**Purpose**: Mark offer as sold  
**Auth Required**: Yes (must be offer creator)  
**Response**: Updated offer object

### `/api/offers/setAsRemoved.php?offer_id={id}` (GET)
**Purpose**: Remove offer  
**Auth Required**: Yes (must be offer creator)  
**Response**: Updated offer object

## Vehicle Data Endpoints

### `/api/vehicles/brands.php` (GET)
**Purpose**: Get all vehicle brands  
**Auth Required**: No  
**Response**: `[{ "id": 1, "name": "BMW" }, ...]`

### `/api/vehicles/models.php?brand_id={id}` (GET)
**Purpose**: Get vehicle models (optionally filtered by brand)  
**Auth Required**: No  
**Response**: `[{ "id": 1, "name": "Seria 3", "brand_id": 1 }, ...]`

## Static Values Endpoints

### `/api/values/fuelType.php` (GET)
**Response**: `["Petrol", "Diesel", "Electric", "Hybrid"]`

### `/api/values/transmissionType.php` (GET)
**Response**: `["Manual", "Automatic"]`

### `/api/values/bodyType.php` (GET)
**Response**: `["Sedan", "Hatchback", "SUV", ...]`

### `/api/values/countries.php` (GET)
**Response**: `["Austria", "Belgium", ...]` (alphabetically sorted)

## Attachments Endpoints

### `/api/attachments/create.php` (POST)
**Purpose**: Upload image file  
**Auth Required**: Yes  
**Request**: multipart/form-data with `file` field  
**Limits**: 10MB max, image types only (JPEG, PNG, GIF, WebP)  
**Response**: `{ "attachment_id": 123, "message": "File uploaded successfully" }`

### `/api/attachments/show.php?id={id}` (GET)
**Purpose**: Display attachment image  
**Auth Required**: No  
**Response**: Image file with appropriate Content-Type header

## Response Format

**Success**: JSON with data or message
```json
{ "message": "Success message" }
{ "data": [...] }
```

**Error**: JSON with error message
```json
{ "message": "Error description" }
```

**Status Codes**:
- 200: OK
- 201: Created
- 400: Bad Request (validation error)
- 401: Unauthorized (not authenticated)
- 403: Forbidden (not authorized)
- 404: Not Found
- 409: Conflict (e.g., email already exists)
- 500: Internal Server Error

## Database Schema

**Tables**:
- `users` - User accounts
- `brands` - Vehicle brands
- `models` - Vehicle models (linked to brands)
- `offers` - Vehicle offers (with JSON attachments array)
- `attachments` - File metadata
- `migrations` - Migration tracking

## Utilities

**Database**: `Database::getPdo()` - Returns PDO instance (singleton)  
**Response**: `Response::json($data, $statusCode)` - Send JSON response  
**Response**: `Response::error($message, $statusCode)` - Send error response  
**QueryBuilder**: Fluent interface for building SQL queries  
**AttachmentUploader**: Handles file uploads with validation

## Common Patterns

### Checking Authentication
```php
Session::allowAuthenticatedOnly(); // Throws 401 if not authenticated
$userId = Session::getUserId(); // Returns null if not authenticated
```

### Returning JSON
```php
Response::json(['message' => 'Success'], Response::HTTP_OK);
Response::error('Error message', Response::HTTP_BAD_REQUEST);
```

### Database Query
```php
$pdo = Database::getPdo();
$stmt = $pdo->prepare('SELECT * FROM table WHERE id = :id');
$stmt->execute(['id' => $id]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
```

