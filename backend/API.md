# API Documentation
# SIG UMKM Kuliner Kecamatan Sungailiat

## Base URL
```
http://localhost:8000/api
```

---

## Authentication

### Login
```
POST /api/auth/login
```

**Request Body:**
```json
{
  "email": "admin@gisumkm.test",
  "password": "password"
}
```

**Response (200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@gisumkm.test",
    "role": "admin"
  }
}
```

---

### Logout
```
POST /api/auth/logout
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "Successfully logged out"
}
```

---

### Get Current User
```
GET /api/auth/me
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "id": 1,
  "name": "Administrator",
  "email": "admin@gisumkm.test",
  "role": "admin",
  "role_label": "Administrator"
}
```

---

### Refresh Token
```
POST /api/auth/refresh
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

---

## Public Map Layers

### Get UMKM Markers
```
GET /api/map/umkms
```

**Optional Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| village_id | integer | Filter by village |
| category | string | Filter by category |

**Response (200):**
```json
{
  "type": "FeatureCollection",
  "name": "UMKM Kuliner",
  "crs": {
    "type": "name",
    "properties": {"name": "urn:ogc:def:crs:EPSG::4326"}
  },
  "features": [
    {
      "type": "Feature",
      "properties": {
        "id": 1,
        "name": "SONYA ERINAWARTI",
        "owner": "SONYA",
        "category": "Industri Cokelat",
        "address": "Jalan Jenderal Ahmad Yani No. 55",
        "potential_score": 78.5,
        "potential_level": "tinggi",
        "village_name": "Parit Padang"
      },
      "geometry": {
        "type": "Point",
        "coordinates": [106.1038, -1.8889]
      }
    }
  ]
}
```

---

### Get Village Boundaries
```
GET /api/map/villages
```

**Response (200):** GeoJSON FeatureCollection with Polygon geometries

---

### Get Roads
```
GET /api/map/roads
```

**Response (200):** GeoJSON FeatureCollection with LineString geometries

---

### Get Settlements
```
GET /api/map/settlements
```

**Response (200):** GeoJSON FeatureCollection with Polygon geometries

---

### Get Trading Centers
```
GET /api/map/trading-centers
```

**Response (200):** GeoJSON FeatureCollection with Point geometries

---

### Get Schools
```
GET /api/map/schools
```

**Response (200):** GeoJSON FeatureCollection with Point geometries

---

### Get Government Facilities
```
GET /api/map/government-facilities
```

**Response (200):** GeoJSON FeatureCollection with Point geometries

---

### Get Tourisms
```
GET /api/map/tourisms
```

**Response (200):** GeoJSON FeatureCollection with Point geometries

---

## UMKM Management (Protected)

### List UMKM
```
GET /api/umkms
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| search | string | Search by name or owner |
| category | string | Filter by category |
| village_id | integer | Filter by village |
| potential_level | string | Filter by potential (tinggi/sedang/rendah) |
| per_page | integer | Items per page (default: 15) |

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "SONYA ERINAWARTI",
      "owner": "SONYA",
      "category": "Industri Cokelat",
      "address": "Jalan Jenderal Ahmad Yani No. 55",
      "latitude": -1.888984,
      "longitude": 106.103887,
      "potential_score": 78.5,
      "potential_level": "tinggi",
      "potential_label": "Potensi Tinggi",
      "potential_color": "#22c55e",
      "village_id": 1,
      "village_name": "Parit Padang",
      "primary_photo_url": "http://localhost:8000/storage/umkm-photos/photo.jpg",
      "created_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

---

### Create UMKM
```
POST /api/umkms
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "Warung Makan Sederahana",
  "owner": "Budi Santoso",
  "category": "Warung Makan",
  "address": "Jalan Sudirman No. 123",
  "latitude": -1.854586,
  "longitude": 106.102551,
  "village_id": 1
}
```

**Response (201):**
```json
{
  "message": "UMKM created successfully",
  "data": {
    "id": 42,
    "name": "Warung Makan Sederahana",
    ...
  }
}
```

---

### Get UMKM Detail
```
GET /api/umkms/{id}
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "name": "SONYA ERINAWARTI",
    "owner": "SONYA",
    "category": "Industri Cokelat",
    "address": "Jalan Jenderal Ahmad Yani No. 55",
    "latitude": -1.888984,
    "longitude": 106.103887,
    "potential_score": 78.5,
    "potential_level": "tinggi",
    "potential_label": "Potensi Tinggi",
    "village": {
      "id": 1,
      "name": "Parit Padang"
    },
    "photos": [
      {
        "id": 1,
        "url": "http://localhost:8000/storage/umkm-photos/photo.jpg",
        "is_primary": true
      }
    ],
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

---

### Update UMKM
```
PUT /api/umkms/{id}
Authorization: Bearer {token}
```

**Request Body:** Same as create (all fields optional)

**Response (200):**
```json
{
  "message": "UMKM updated successfully",
  "data": {...}
}
```

---

### Delete UMKM
```
DELETE /api/umkms/{id}
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "message": "UMKM deleted successfully"
}
```

---

### Upload Photo
```
POST /api/umkms/{id}/photos
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Form Data:**
| Field | Type | Description |
|-------|------|-------------|
| photo | file | Image file (jpeg, png, jpg, gif, webp) |
| is_primary | boolean | Set as primary photo |

**Response (201):**
```json
{
  "message": "Photo uploaded successfully",
  "data": {
    "id": 5,
    "url": "http://localhost:8000/storage/umkm-photos/new-photo.jpg",
    "is_primary": false
  }
}
```

---

### Get Categories
```
GET /api/umkms/categories
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": [
    "Industri Cokelat",
    "Industri Kue Basah",
    "Kedai Minuman",
    "Restoran",
    "Warung Makan"
  ]
}
```

---

## Villages (Protected)

### List Villages
```
GET /api/villages
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| search | string | Search by name |
| per_page | integer | Items per page |

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Sungailiat",
      "population": 15000,
      "area_km2": 5.2,
      "density": 2884.62,
      "umkm_count": 5
    }
  ],
  "meta": {...}
}
```

---

### Get Village Detail
```
GET /api/villages/{id}
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Sungailiat",
    "population": 15000,
    "area_km2": 5.2,
    "density": 2884.62,
    "umkm_count": 5
  }
}
```

---

## Dashboard (Protected)

### Get Statistics
```
GET /api/dashboard/stats
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": {
    "total_umkm": 41,
    "total_categories": 8,
    "total_villages": 13,
    "by_potential": {
      "tinggi": 15,
      "sedang": 20,
      "rendah": 6
    }
  }
}
```

---

### Get UMKM by Village
```
GET /api/dashboard/by-village
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Sri Menanti",
      "umkm_count": 12,
      "population": 4300
    }
  ]
}
```

---

### Get UMKM by Category
```
GET /api/dashboard/by-category
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": [
    {
      "category": "Industri Kue Basah",
      "count": 15
    },
    {
      "category": "Warung Makan",
      "count": 10
    }
  ]
}
```

---

### Get UMKM by Potential
```
GET /api/dashboard/by-potential
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "data": [
    {
      "level": "1",
      "count": 15
    },
    {
      "level": "2",
      "count": 20
    },
    {
      "level": "3",
      "count": 6
    }
  ]
}
```

---

## User Management (Admin Only)

### List Users
```
GET /api/users
Authorization: Bearer {token} (Admin only)
```

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Administrator",
      "email": "admin@gisumkm.test",
      "role": "admin",
      "role_label": "Administrator",
      "created_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "meta": {...}
}
```

---

### Create User
```
POST /api/users
Authorization: Bearer {token} (Admin only)
```

**Request Body:**
```json
{
  "name": "Petugas Baru",
  "email": "petugas@gisumkm.test",
  "password": "password123",
  "role": "field_officer"
}
```

**Response (201):**
```json
{
  "message": "User created successfully",
  "data": {...}
}
```

---

### Update User
```
PUT /api/users/{id}
Authorization: Bearer {token} (Admin only)
```

**Request Body:**
```json
{
  "name": "Nama Baru",
  "role": "admin"
}
```

---

### Delete User
```
DELETE /api/users/{id}
Authorization: Bearer {token} (Admin only)
```

**Response (200):**
```json
{
  "message": "User deleted successfully"
}
```

---

### Reset Password
```
POST /api/users/{id}/reset-password
Authorization: Bearer {token} (Admin only)
```

**Request Body:**
```json
{
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

---

## Routing

### Calculate Route
```
GET /api/routing
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| start_lat | float | Start latitude |
| start_lng | float | Start longitude |
| end_lat | float | End latitude |
| end_lng | float | End longitude |

**Response (200):**
```json
{
  "data": {
    "geometry": {
      "type": "LineString",
      "coordinates": [[106.1038, -1.8889], ...]
    },
    "distance": 2500,
    "distance_km": 2.5,
    "duration": 480,
    "duration_minutes": 8.0
  }
}
```

---

### Find Nearest Road Point
```
GET /api/routing/nearest
Authorization: Bearer {token}
```

**Query Parameters:**
| Parameter | Type | Description |
|-----------|------|-------------|
| lat | float | Latitude |
| lng | float | Longitude |

**Response (200):**
```json
{
  "data": {
    "lat": -1.854586,
    "lng": 106.102551,
    "distance": 15.5,
    "name": "Jalan Sudirman"
  }
}
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated"
}
```

### 403 Forbidden
```json
{
  "message": "Forbidden"
}
```

### 404 Not Found
```json
{
  "message": "Not Found"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  }
}
```

---

## Role Permissions

| Role | Permissions |
|------|-------------|
| `admin` | Full access: CRUD all data, manage users, view dashboard |
| `field_officer` | CRUD UMKM, upload photos, view map |
| `public` | View map layers only |
