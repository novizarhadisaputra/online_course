# API Documentation: Active Bundles with Courses

## Endpoint

**GET** `/api/bundles/active/with-courses`

## Description

Endpoint ini mengembalikan daftar bundle aktif yang memiliki relasi dengan courses dan telah berhasil dibeli oleh user yang terautentikasi. Endpoint ini dirancang khusus untuk menampilkan hanya bundle yang aktif, memiliki kursus terkait, dan telah dibeli oleh user.

## Access Control

Endpoint ini dibatasi hanya untuk bundle yang telah dibeli oleh user dengan status transaksi 'success'. Hanya bundle yang telah dibeli user dengan transaksi berhasil yang akan ditampilkan.

## Authentication

Endpoint ini berada dalam grup protected dan memerlukan autentikasi menggunakan Sanctum token.

```
Authorization: Bearer {your-token}
```

## Query Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `search` | string | No | Pencarian berdasarkan nama bundle |
| `sort_by` | string | No | Field untuk sorting (default: 'name') |
| `sort_order` | string | No | Urutan sorting: 'asc' atau 'desc' (default: 'asc') |
| `per_page` | integer | No | Jumlah item per halaman (default: 15, max: 100) |
| `page` | integer | No | Nomor halaman (default: 1) |

## Response Format

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Active bundles with courses retrieved successfully",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Web Development Bundle",
        "slug": "web-development-bundle",
        "description": "Complete web development course bundle",
        "image": "https://example.com/bundle-image.jpg",
        "status": "active",
        "is_featured": true,
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-20T14:45:00.000000Z",
        "price": {
          "id": 1,
          "original_price": 500000,
          "discounted_price": 350000,
          "currency": "IDR",
          "discount_percentage": 30
        },
        "metadata": {
          "duration": "6 months",
          "total_courses": 5,
          "total_products": 3,
          "difficulty_level": "Intermediate",
          "language": "Indonesian",
          "requirements": [
            "Basic HTML knowledge",
            "Computer with internet access"
          ],
          "benefits": [
            "Learn modern web development",
            "Build real-world projects",
            "Get certificate of completion"
          ],
          "features": [
            "Lifetime access",
            "Mobile and desktop access",
            "Community support"
          ]
        },
        "courses": [
          {
            "id": 1,
            "name": "HTML & CSS Fundamentals",
            "slug": "html-css-fundamentals",
            "description": "Learn the basics of HTML and CSS",
            "status": "active",
            "difficulty_level": "Beginner"
          },
          {
            "id": 2,
            "name": "JavaScript Essentials",
            "slug": "javascript-essentials",
            "description": "Master JavaScript programming",
            "status": "active",
            "difficulty_level": "Intermediate"
          }
        ],
        "products": [
          {
            "id": 1,
            "name": "Web Development Toolkit",
            "description": "Essential tools for web development",
            "type": "digital"
          }
        ]
      }
    ],
    "first_page_url": "http://localhost:8000/api/bundles/active/with-courses?page=1",
    "from": 1,
    "last_page": 3,
    "last_page_url": "http://localhost:8000/api/bundles/active/with-courses?page=3",
    "links": [
      {
        "url": null,
        "label": "&laquo; Previous",
        "active": false
      },
      {
        "url": "http://localhost:8000/api/bundles/active/with-courses?page=1",
        "label": "1",
        "active": true
      }
    ],
    "next_page_url": "http://localhost:8000/api/bundles/active/with-courses?page=2",
    "path": "http://localhost:8000/api/bundles/active/with-courses",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 45
  }
}
```

### Error Response (422 Unprocessable Entity)

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "per_page": [
      "The per page must not be greater than 100."
    ],
    "sort_order": [
      "The selected sort order is invalid."
    ]
  }
}
```

### Error Response (500 Internal Server Error)

```json
{
  "success": false,
  "message": "An error occurred while retrieving active bundles with courses",
  "error": "Database connection failed"
}
```

## Example Requests

### Basic Request

```bash
curl -X GET "http://localhost:8000/api/bundles/active/with-courses" \
  -H "Authorization: Bearer your-token-here" \
  -H "Accept: application/json"
```

### Request with Search and Pagination

```bash
curl -X GET "http://localhost:8000/api/bundles/active/with-courses?search=web&per_page=10&page=1" \
  -H "Authorization: Bearer your-token-here" \
  -H "Accept: application/json"
```

### Request with Sorting

```bash
curl -X GET "http://localhost:8000/api/bundles/active/with-courses?sort_by=created_at&sort_order=desc" \
  -H "Authorization: Bearer your-token-here" \
  -H "Accept: application/json"
```

## Features

1. **Filtering**: Hanya menampilkan bundle dengan status 'active' yang memiliki relasi dengan courses aktif
2. **Caching**: Response di-cache selama 1 jam untuk meningkatkan performa
3. **Search**: Pencarian berdasarkan nama bundle
4. **Sorting**: Pengurutan berdasarkan field tertentu
5. **Pagination**: Mendukung pagination dengan kustomisasi jumlah item per halaman
6. **Eager Loading**: Memuat relasi courses, products, price, dan metadata secara efisien
7. **Validation**: Validasi parameter input untuk memastikan data yang valid
8. **Error Handling**: Penanganan error yang komprehensif dengan response yang informatif

## Business Logic

- Bundle harus memiliki status 'active'
- Bundle harus memiliki minimal satu course yang terkait
- Course yang terkait juga harus memiliki status 'active'
- **Membatasi akses hanya pada bundle yang telah dibeli oleh user yang terautentikasi**
- Validasi kepemilikan user melalui record `TransactionDetail` dengan `transaction.status = 'success'`
- Response di-cache per user untuk meningkatkan performa
- Mendukung pencarian dan pengurutan untuk kemudahan navigasi

## Cache Strategy

Endpoint ini menggunakan cache dengan key yang unik berdasarkan user ID dan parameter request: `purchased_bundles_with_courses_{user_id}_{hash_of_request_params}`. Cache akan expired setelah 1 jam atau ketika ada perubahan data bundle/course. Caching per user memastikan isolasi data antar user.

## Rate Limiting

Endpoint ini berada dalam grup protected yang menggunakan middleware auth:sanctum, sehingga rate limiting mengikuti konfigurasi default Laravel untuk authenticated users.