# Status
### Base healthcheck
Base healthcheck endpoint to verify if the backend is running.

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
    "repeated_password": "securepassword",
    "name": "Handlarz Mirek"
}
```

**Response:**

Code: 201 (User created successfully) || (400 Bad Request - Invalid input) || (409 User already exists)

Content: message indicating success or error details.

### Login
Login an existing user.

**Request:**

URL:
```
/api/login/login.php
```
| Method | Auth required |
| --- | --- |
| POST | No |

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "securepassword"
}
```

**Response:**

Code: 200 (Login successful) || (400 Bad Request - Invalid input) || (401 Unauthorized - Invalid credentials)

Content: message indicating success or error details.
```json
{
    "message": "Login successful",
    "user_name": "Handlarz Mirek"
}
```

### Logout
Logout the currently authenticated user.

**Request:**

URL:
```
/api/login/logout.php
```
| Method | Auth required |
| --- | --- |
| * | Yes |

**Response:**

Code: 200

Content:
```json
{
    "message": "Logout successful"
}
```

# Vehicles
### Brands
Get a list of all vehicle brands. Ordered alphabetically by name.

**Request:**

URL:
```
/api/vehicles/brands.php
```
| Method | Auth required |
| --- | --- |
| GET | No |

**Response:**

Code: 200
Content:

```json
[
    {
        "id": 2,
        "name": "Audi"
    },
    {
        "id": 1,
        "name": "BMW"
    },
    ...
]
```

### Models
Get a filtered list of vehicle models for a given brand. Ordered alphabetically by name.

**Request:**

URL:
```
/api/vehicles/models.php?brand_id={brand_id}
```
| Method | Auth required |
| --- | --- |
| GET | No |

**Response:**

Code: 200

Content:
```json
[
    {
        "id": 5,
        "name": "A4",
        "brand_id": 2
    },
    {
        "id": 2,
        "name": "Seria 1",
        "brand_id": 1
    },
    ...
]
```

# Application Values
### Fuel Types
Get a list of all fuel types.

**Request:**

URL:
```
/api/values/fuelType.php
```
| Method | Auth required |
| --- | --- |
| GET | No |

**Response:**

Code: 200

Content:
```json
[
    "Petrol",
    "Diesel",
    "Electric",
    "Hybrid",
    ...
]
```

### Transmission Types
Get a list of all transmission types.

**Request:**

URL:
```
/api/values/transmissionType.php
```
| Method | Auth required |
| --- | --- |
| GET | No |

**Response:**

Code: 200

Content:
```json
[
    "Manual",
    "Automatic",
    "Semi-automatic",
    ...
]
```

### Body Types
Get a list of all vehicle body types.

**Request:**

URL:
```
/api/values/bodyType.php
```
| Method | Auth required |
| --- | --- |
| GET | No |

**Response:**

Code: 200

Content:
```json
[
    "Sedan",
    "Hatchback",
    "SUV",
    "Coupe",
    ...
]
```

### Countries
Get a list of all countries sorted alphabetically.

**Request:**

URL:
```
/api/values/countries.php
```
| Method | Auth required |
| --- | --- |
| GET | No |

**Response:**

Code: 200

Content:
```json
[
    "Austria",
    "Belgium",
    "Croatia",
    "Denmark",
    ...
]
```