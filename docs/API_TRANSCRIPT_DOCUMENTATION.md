# API Transkrip Nilai - Dokumentasi

API ini menyediakan endpoint untuk mengambil data transkrip nilai siswa dari tabel bundles dan courses yang memiliki relasi dengan score individu.

## Base URL
```
{APP_URL}/api/protected/transcripts
```

## Autentikasi
Semua endpoint memerlukan autentikasi menggunakan Sanctum Bearer Token:
```
Authorization: Bearer {your_token}
```

## Endpoints

### 1. Get Transcript List
Mengambil daftar transkrip nilai dengan pagination dan filter.

**Endpoint:** `GET /api/protected/transcripts`

**Parameters:**
- `student_id` (optional, UUID): ID siswa yang ingin dilihat transkripnya
- `semester` (optional, integer): Filter berdasarkan semester (1-20)
- `academic_year` (optional, integer): Filter berdasarkan tahun akademik (2020-2030)
- `is_graduated` (optional, boolean): Filter berdasarkan status kelulusan
- `per_page` (optional, integer): Jumlah item per halaman (1-100, default: 15)
- `page` (optional, integer): Nomor halaman (default: 1)

**Example Request:**
```bash
curl -X GET \
  '{APP_URL}/api/protected/transcripts?student_id=123e4567-e89b-12d3-a456-426614174000&semester=1&academic_year=2024&per_page=10' \
  -H 'Authorization: Bearer {your_token}' \
  -H 'Accept: application/json'
```

**Example Response:**
```json
{
  "success": true,
  "message": "Transcript data retrieved successfully",
  "data": [
    {
      "id": "123e4567-e89b-12d3-a456-426614174000",
      "student": {
        "id": "123e4567-e89b-12d3-a456-426614174000",
        "name": "John Doe",
        "email": "john@example.com"
      },
      "course_info": {
        "type": "course",
        "id": "123e4567-e89b-12d3-a456-426614174001",
        "name": "Introduction to Programming",
        "code": "CS101",
        "credits": 3,
        "description": "Basic programming concepts",
        "level": "beginner",
        "language": "en",
        "duration": 40,
        "category": {
          "id": "123e4567-e89b-12d3-a456-426614174002",
          "name": "Computer Science"
        },
        "status": "published",
        "is_paid": true
      },
      "score_details": {
        "value": 85.5,
        "grade_letter": "A",
        "grade_point": 4.0,
        "is_graduated": true,
        "semester": 1
      },
      "academic_info": {
        "academic_year": 2024,
        "completion_date": "2024-06-15",
        "enrollment_date": "2024-01-15"
      },
      "metadata": {
        "model_type": "App\\Models\\Course",
        "model_id": "123e4567-e89b-12d3-a456-426614174001",
        "created_at": "2024-01-15T10:00:00.000000Z",
        "updated_at": "2024-06-15T15:30:00.000000Z"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 45,
    "from": 1,
    "to": 10
  }
}
```

### 2. Get Student Detailed Transcript
Mengambil transkrip lengkap untuk siswa tertentu dengan statistik.

**Endpoint:** `GET /api/protected/transcripts/{studentId}`

**Parameters:**
- `studentId` (required, UUID): ID siswa
- `semester` (optional, integer): Filter berdasarkan semester
- `academic_year` (optional, integer): Filter berdasarkan tahun akademik

**Example Request:**
```bash
curl -X GET \
  '{APP_URL}/api/protected/transcripts/123e4567-e89b-12d3-a456-426614174000?semester=1' \
  -H 'Authorization: Bearer {your_token}' \
  -H 'Accept: application/json'
```

**Example Response:**
```json
{
  "success": true,
  "message": "Student transcript retrieved successfully",
  "data": {
    "student": {
      "id": "123e4567-e89b-12d3-a456-426614174000",
      "name": "John Doe",
      "email": "john@example.com"
    },
    "transcript": [
      {
        "id": "123e4567-e89b-12d3-a456-426614174000",
        "course_info": {
          "type": "bundle",
          "id": "123e4567-e89b-12d3-a456-426614174003",
          "name": "Web Development Bundle",
          "code": "WEB001",
          "credits": 12,
          "description": "Complete web development course bundle",
          "duration": 120,
          "status": "published",
          "is_paid": true,
          "courses": [
            {
              "id": "123e4567-e89b-12d3-a456-426614174004",
              "name": "HTML & CSS Fundamentals",
              "code": "WEB101",
              "credits": 3,
              "level": "beginner",
              "category": {
                "id": "123e4567-e89b-12d3-a456-426614174005",
                "name": "Web Development"
              }
            },
            {
              "id": "123e4567-e89b-12d3-a456-426614174006",
              "name": "JavaScript Programming",
              "code": "WEB102",
              "credits": 4,
              "level": "intermediate",
              "category": {
                "id": "123e4567-e89b-12d3-a456-426614174005",
                "name": "Web Development"
              }
            }
          ],
          "total_courses": 2
        },
        "score_details": {
          "value": 88.0,
          "grade_letter": "A",
          "grade_point": 4.0,
          "is_graduated": true,
          "semester": 1
        },
        "academic_info": {
          "academic_year": 2024,
          "completion_date": "2024-06-15",
          "enrollment_date": "2024-01-15"
        }
      }
    ],
    "summary": {
      "total_credits": 15,
      "completed_courses": 3,
      "graduated_courses": 2,
      "gpa": 3.75,
      "total_scores": 3
    }
  }
}
```

### 3. Get Transcript Summary
Mengambil ringkasan statistik transkrip siswa.

**Endpoint:** `GET /api/protected/transcripts/summary`

**Parameters:**
- `student_id` (optional, UUID): ID siswa (default: authenticated user)

**Example Request:**
```bash
curl -X GET \
  '{APP_URL}/api/protected/transcripts/summary?student_id=123e4567-e89b-12d3-a456-426614174000' \
  -H 'Authorization: Bearer {your_token}' \
  -H 'Accept: application/json'
```

**Example Response:**
```json
{
  "success": true,
  "message": "Transcript summary retrieved successfully",
  "data": {
    "overall_statistics": {
      "total_scores": 15,
      "average_score": 82.5,
      "highest_score": 95.0,
      "lowest_score": 65.0,
      "graduated_courses": 12,
      "graduation_rate": 80.0
    },
    "semester_statistics": {
      "1": {
        "total_courses": 5,
        "average_score": 85.0,
        "graduated_courses": 4
      },
      "2": {
        "total_courses": 6,
        "average_score": 80.5,
        "graduated_courses": 5
      },
      "3": {
        "total_courses": 4,
        "average_score": 82.0,
        "graduated_courses": 3
      }
    }
  }
}
```

## Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "student_id": [
      "Student ID must be a valid UUID format."
    ],
    "semester": [
      "Semester must be at least 1."
    ]
  },
  "data": null
}
```

### Unauthorized (401)
```json
{
  "message": "Unauthenticated."
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Student not found",
  "error": "No query results for model [App\\Models\\User] 123e4567-e89b-12d3-a456-426614174000"
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Failed to retrieve transcript data",
  "error": "Database connection failed"
}
```

## Grade Scale

| Score Range | Letter Grade | Grade Point |
|-------------|--------------|-------------|
| 85-100      | A            | 4.0         |
| 80-84       | A-           | 3.7         |
| 75-79       | B+           | 3.3         |
| 70-74       | B            | 3.0         |
| 65-69       | B-           | 2.7         |
| 60-64       | C+           | 2.3         |
| 55-59       | C            | 2.0         |
| 50-54       | C-           | 1.7         |
| 45-49       | D+           | 1.3         |
| 40-44       | D            | 1.0         |
| 0-39        | F            | 0.0         |

## Rate Limiting

- Authentication endpoints: 3 requests per minute
- General API endpoints: Standard Laravel rate limiting applies

## Notes

1. **Credits Calculation**: Credits are retrieved from course metadata or meta fields, defaulting to 3 if not specified.
2. **Course Codes**: Generated from course metadata, meta fields, or auto-generated from course name.
3. **GPA Calculation**: Weighted average based on credits and grade points.
4. **Pagination**: All list endpoints support pagination with configurable page size.
5. **Filters**: Multiple filters can be combined for precise data retrieval.
6. **Security**: All endpoints require authentication and validate user permissions.

## Testing

Untuk testing API, gunakan tools seperti Postman atau curl dengan langkah berikut:

1. Dapatkan authentication token melalui login endpoint
2. Gunakan token dalam header Authorization
3. Test setiap endpoint dengan berbagai parameter
4. Verifikasi response format dan data accuracy

## Support

Untuk pertanyaan atau masalah terkait API ini, silakan hubungi tim development.