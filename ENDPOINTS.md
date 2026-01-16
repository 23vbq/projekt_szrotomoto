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

# Offers
### Search
Get a list of all active offers. Ordered by creation date descending.

**Request:**

URL:
```
/api/offers/search.php
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
        "id": 1,
        "created_at": "2025-12-28 10:30:00",
        "updated_at": "2025-12-28 10:30:00",
        "title": "BMW 320d",
        "description": "Good condition",
        "price": 15000,
        "production_year": 2015,
        "odometer": 120000,
        "fuel_type": "Diesel",
        "transmission": "Manual",
        "displacement": 2000,
        "horsepower": 184,
        "body_type": "Sedan",
        "doors_amount": 4,
        "seats_amount": 5,
        "brand_name": "BMW",
        "model_name": "Seria 3",
        "user_name": "Handlarz Mirek"
    },
    ...
]
```

### Create
Create a new offer. Requires authentication.

**Request:**

URL:
```
/api/offers/create.php
```
| Method | Auth required |
| --- | --- |
| POST | Yes |

**Request Body:**
```json
{
    "model_id": 1,
    "title": "BMW 320d",
    "description": "Good condition vehicle",
    "price": 15000,
    "production_year": 2015,
    "odometer": 120000,
    "fuel_type": "Diesel",
    "transmission": "Manual",
    "color": "Black",
    "displacement": 2000,
    "horsepower": 184,
    "torque": 400,
    "body_type": "Sedan",
    "doors_amount": 4,
    "seats_amount": 5,
    "vin": "WBADT43452G915989",
    "registration_number": "WX12ABC",
    "country_of_origin": "Poland",
    "is_accident_free": "1",
    "is_first_hand": "0",
    "is_used": "1",
    "has_warranty": "0",
    "has_service_book": "1"
}
```

Required fields: `model_id`, `title`, `price`, `production_year`, `odometer`, `fuel_type`, `transmission`, `body_type`

Validation:
- `fuel_type` must be one of the valid fuel types from `/api/values/fuelType.php`
- `transmission` must be one of the valid transmission types from `/api/values/transmissionType.php`
- `body_type` must be one of the valid body types from `/api/values/bodyType.php`
- `country_of_origin` (if provided) must be one of the valid countries from `/api/values/countries.php`
- Boolean fields (`is_accident_free`, `is_first_hand`, `is_used`, `has_warranty`, `has_service_book`) accept "1" or "0" or empty

**Response:**

Code: 201 (Offer created successfully) || (400 Bad Request) || (401 Unauthorized - Not authenticated)

Content: message indicating success or error details.
```json
{
    "message": "Offer created successfully",
    "offer_id": 1
}
```

**Error Responses:**
- Missing required fields:
```json
{
    "message": "Missing required fields"
}
```
- Invalid request data (invalid enum values):
```json
{
    "message": "Invalid request data"
}
```

### Show
Get a specific offer by ID.

**Request:**

URL:
```
/api/offers/show.php?offer_id={offer_id}
```
| Method | Auth required |
| --- | --- |
| GET | No |

**Response:**

Code: 200 (Offer found) || (404 Not Found - Offer not found or inactive)

Content:
```json
{
    "id": 1,
    "created_at": "2025-12-28 10:30:00",
    "updated_at": "2025-12-28 10:30:00",
    "title": "BMW 320d",
    "description": "Good condition vehicle",
    "price": 15000,
    "production_year": 2015,
    "odometer": 120000,
    "fuel_type": "Diesel",
    "transmission": "Manual",
    "color": "Black",
    "displacement": 2000,
    "horsepower": 184,
    "torque": 400,
    "body_type": "Sedan",
    "doors_amount": 4,
    "seats_amount": 5,
    "vin": "WBADT43452G915989",
    "registration_number": "WX12ABC",
    "country_of_origin": "Poland",
    "is_accident_free": 1,
    "is_first_hand": 0,
    "is_used": 1,
    "has_warranty": 0,
    "has_service_book": 1,
    "status": "active",
    "model_id": 1,
    "created_by": 1,
    "brand_name": "BMW",
    "model_name": "Seria 3",
    "user_name": "Handlarz Mirek"
}
```

### Edit
Edit an existing offer. Requires authentication and authorization (only offer creator can edit).

**Request:**

URL:
```
/api/offers/edit.php?offer_id={offer_id}
```
| Method | Auth required |
| --- | --- |
| POST | Yes |

**Request Body:**
All fields are optional, only the fields you want to update:
```json
{
    "title": "BMW 320d",
    "description": "Updated description",
    "price": 14500,
    "production_year": 2015,
    "odometer": 120500,
    "fuel_type": "Diesel",
    "transmission": "Manual",
    "color": "Black",
    "displacement": 2000,
    "horsepower": 184,
    "torque": 400,
    "body_type": "Sedan",
    "doors_amount": 4,
    "seats_amount": 5,
    "vin": "WBADT43452G915989",
    "registration_number": "WX12ABC",
    "country_of_origin": "Poland",
    "is_accident_free": "1",
    "is_first_hand": "0",
    "is_used": "1",
    "has_warranty": "0",
    "has_service_book": "1"
}
```

Validation:
- `fuel_type` (if provided) must be one of the valid fuel types from `/api/values/fuelType.php`
- `transmission` (if provided) must be one of the valid transmission types from `/api/values/transmissionType.php`
- `body_type` (if provided) must be one of the valid body types from `/api/values/bodyType.php`
- `country_of_origin` (if provided) must be one of the valid countries from `/api/values/countries.php`

**Response:**

Code: 200 (Offer updated successfully) || (400 Bad Request) || (401 Unauthorized) || (403 Forbidden - Not the offer creator) || (404 Not Found)

Content: message indicating success or error details.
```json
{
    "message": "Offer updated successfully"
}
```

**Error Responses:**
- Invalid request data (invalid enum values):
```json
{
    "message": "Invalid request data"
}
```

### Set as Sold
Mark an offer as sold. Requires authentication and authorization (only offer creator can mark as sold).

**Request:**

URL:
```
/api/offers/setAsSold.php?offer_id={offer_id}
```
| Method | Auth required |
| --- | --- |
| GET | Yes |

**Response:**

Code: 200 (Offer marked as sold) || (401 Unauthorized) || (403 Forbidden - Not the offer creator) || (404 Not Found)

Content: message indicating success or error details.
```json
{
    "message": "Offer marked as sold"
}
```

### Set as Removed
Remove an offer. Requires authentication and authorization (only offer creator can remove).

**Request:**

URL:
```
/api/offers/setAsRemoved.php?offer_id={offer_id}
```
| Method | Auth required |
| --- | --- |
| GET | Yes |

**Response:**

Code: 200 (Offer removed) || (401 Unauthorized) || (403 Forbidden - Not the offer creator) || (404 Not Found)

Content: message indicating success or error details.
```json
{
    "message": "Offer removed successfully"
}
```