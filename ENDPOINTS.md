# Status
### Base healthcheck
Base healthckeck endpoint to verify if the backend is running.

**Request:**
URL:
```
/
```
| Method | Auth required |
| --- | --- |
| GET | No |

**Response:**
Code: 200
Content:
```json
{
  "status": "ok"
}
```

### Server status
Server status endpoint to verify backend and database connectivity.

**Request:**

URL:
```
/api/status.php
```
| Method | Auth required |
| --- | --- |
| GET | No |

**Response:**
Code: 200
Content:
```json
{
    "status": "ok",
    "current_time": $currentTimeFromServer->format('Y-m-d H:i:s'),
    "current_time_from_db": $currentTimeFromDb
}
```

# Login
### Register
Register a new user.

**Request:**
URL:
```
/api/login/register.php
```
| Method | Auth required |
| --- | --- |
| POST | No |

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "securepassword",
    "confirm_password": "securepassword"
}
```

**Response:**
Code: 201 (User created successfully) || (400 Bad Request - Invalid input) || (409 User already exists)
Content: message indicating success or error details.