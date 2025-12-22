# Status
### Base healthcheck
Base healthckeck endpoint to verify if the backend is running.
Request:
**URL:** `/`
**Method:** `GET`
**Auth required:** No
Response:
**Code:** 200
**Content:**
```json
{
  "status": "ok"
}
```

### Server status
Server status endpoint to verify backend and database connectivity.
Request:
**URL:** `/status.php`
**Method:** `GET`
**Auth required:** No
Response:
**Code:** 200
**Content:**
```json
{
    "status" => "ok",
    "current_time" => $currentTimeFromServer->format('Y-m-d H:i:s'),
    "current_time_from_db" => $currentTimeFromDb
}
```