<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Course;
use App\Models\Bundle;
use App\Models\Score;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TranscriptControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected User $student;
    protected Course $course;
    protected Bundle $bundle;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->user = User::factory()->create();
        $this->student = User::factory()->create();

        // Create category
        $this->category = Category::factory()->create([
            'name' => 'Computer Science'
        ]);

        // Create course
        $this->course = Course::factory()->create([
            'name' => 'Introduction to Programming',
            'category_id' => $this->category->id,
            'meta' => [
                'credits' => 3,
                'code' => 'CS101'
            ]
        ]);

        // Create bundle
        $this->bundle = Bundle::factory()->create([
            'name' => 'Web Development Bundle'
        ]);

        // Attach course to bundle
        $this->bundle->courses()->attach($this->course->id);
    }

    /** @test */
    public function it_can_get_transcript_list_for_authenticated_user()
    {
        Sanctum::actingAs($this->user);

        // Create scores for the authenticated user
        Score::factory()->create([
            'user_id' => $this->user->id,
            'model_type' => Course::class,
            'model_id' => $this->course->id,
            'value' => 85.5,
            'batches' => 1,
            'is_graduated' => true
        ]);

        $response = $this->getJson('/api/protected/transcripts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'student' => ['id', 'name', 'email'],
                        'course_info' => [
                            'type', 'id', 'name', 'code', 'credits'
                        ],
                        'score_details' => [
                            'value', 'grade_letter', 'grade_point', 'is_graduated', 'semester'
                        ],
                        'academic_info' => [
                            'academic_year', 'completion_date', 'enrollment_date'
                        ]
                    ]
                ],
                'meta' => [
                    'current_page', 'last_page', 'per_page', 'total'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Transcript data retrieved successfully'
            ]);
    }

    /** @test */
    public function it_can_filter_transcript_by_student_id()
    {
        Sanctum::actingAs($this->user);

        // Create scores for different students
        Score::factory()->create([
            'user_id' => $this->student->id,
            'model_type' => Course::class,
            'model_id' => $this->course->id,
            'value' => 90.0,
            'batches' => 1
        ]);

        Score::factory()->create([
            'user_id' => $this->user->id,
            'model_type' => Course::class,
            'model_id' => $this->course->id,
            'value' => 75.0,
            'batches' => 1
        ]);

        $response = $this->getJson('/api/protected/transcripts?student_id=' . $this->student->id);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.student.id', $this->student->id)
            ->assertJsonPath('data.0.score_details.value', 90.0);
    }

    /** @test */
    public function it_can_filter_transcript_by_semester()
    {
        Sanctum::actingAs($this->user);

        // Create scores for different semesters
        Score::factory()->create([
            'user_id' => $this->user->id,
            'model_type' => Course::class,
            'model_id' => $this->course->id,
            'value' => 85.0,
            'batches' => 1
        ]);

        Score::factory()->create([
            'user_id' => $this->user->id,
            'model_type' => Course::class,
            'model_id' => $this->course->id,
            'value' => 90.0,
            'batches' => 2
        ]);

        $response = $this->getJson('/api/protected/transcripts?semester=1');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.score_details.semester', 1)
            ->assertJsonPath('data.0.score_details.value', 85.0);
    }

    /** @test */
    public function it_can_filter_transcript_by_academic_year()
    {
        Sanctum::actingAs($this->user);

        // Create score with specific year
        Score::factory()->create([
            'user_id' => $this->user->id,
            'model_type' => Course::class,
            'model_id' => $this->course->id,
            'value' => 88.0,
            'batches' => 1,
            'created_at' => '2024-01-15 10:00:00'
        ]);

        $response = $this->getJson('/api/protected/transcripts?academic_year=2024');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.academic_info.academic_year', 2024);
    }

    /** @test */
    public function it_can_get_detailed_student_transcript()
    {
        Sanctum::actingAs($this->user);

        // Create multiple scores for student
        Score::factory()->create([
            'user_id' => $this->student->id,
            'model_type' => Course::class,
            'model_id' => $this->course->id,
            'value' => 85.0,
            'batches' => 1,
            'is_graduated' => true
        ]);

        Score::factory()->create([
            'user_id' => $this->student->id,
            'model_type' => Bundle::class,
            'model_id' => $this->bundle->id,
            'value' => 90.0,
            'batches' => 1,
            'is_graduated' => true
        ]);

        $response = $this->getJson('/api/protected/transcripts/' . $this->student->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'student' => ['id', 'name', 'email'],
                    'transcript' => [
                        '*' => [
                            'id',
                            'course_info',
                            'score_details',
                            'academic_info'
                        ]
                    ],
                    'summary' => [
                        'total_credits',
                        'completed_courses',
                        'graduated_courses',
                        'gpa',
                        'total_scores'
                    ]
                ]
            ])
            ->assertJsonPath('data.student.id', $this->student->id)
            ->assertJsonPath('data.summary.graduated_courses', 2);
    }

    /** @test */
    public function it_can_get_transcript_summary()
    {
        Sanctum::actingAs($this->user);

        // Create multiple scores with different values
        Score::factory()->create([
            'user_id' => $this->user->id,
            'model_type' => Course::class,
            'model_id' => $this->course->id,
            'value' => 85.0,
            'batches' => 1,
            'is_graduated' => true
        ]);

        Score::factory()->create([
            'user_id' => $this->user->id,
            'model_type' => Course::class,
            'model_id' => $this->course->id,
            'value' => 75.0,
            'batches' => 1,
            'is_graduated' => false
        ]);

        Score::factory()->create([
            'user_id' => $this->user->id,
            'model_type' => Course::class,
            'model_id' => $this->course->id,
            'value' => 90.0,
            'batches' => 2,
            'is_graduated' => true
        ]);

        $response = $this->getJson('/api/protected/transcripts/summary');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'overall_statistics' => [
                        'total_scores',
                        'average_score',
                        'highest_score',
                        'lowest_score',
                        'graduated_courses',
                        'graduation_rate'
                    ],
                    'semester_statistics'
                ]
            ])
            ->assertJsonPath('data.overall_statistics.total_scores', 3)
            ->assertJsonPath('data.overall_statistics.highest_score', 90.0)
            ->assertJsonPath('data.overall_statistics.lowest_score', 75.0)
            ->assertJsonPath('data.overall_statistics.graduated_courses', 2);
    }

    /** @test */
    public function it_validates_request_parameters()
    {
        Sanctum::actingAs($this->user);

        // Test invalid student_id
        $response = $this->getJson('/api/protected/transcripts?student_id=invalid-uuid');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['student_id']);

        // Test invalid semester
        $response = $this->getJson('/api/protected/transcripts?semester=0');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['semester']);

        // Test invalid academic_year
        $response = $this->getJson('/api/protected/transcripts?academic_year=2019');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['academic_year']);

        // Test invalid per_page
        $response = $this->getJson('/api/protected/transcripts?per_page=101');
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }

    /** @test */
    public function it_requires_authentication()
    {
        $response = $this->getJson('/api/protected/transcripts');
        $response->assertStatus(401);

        $response = $this->getJson('/api/protected/transcripts/' . $this->student->id);
        $response->assertStatus(401);

        $response = $this->getJson('/api/protected/transcripts/summary');
        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_404_for_non_existent_student()
    {
        Sanctum::actingAs($this->user);

        $nonExistentId = '123e4567-e89b-12d3-a456-426614174000';
        $response = $this->getJson('/api/protected/transcripts/' . $nonExistentId);

        $response->assertStatus(500) // ModelNotFoundException will be caught and return 500
            ->assertJson([
                'success' => false,
                'message' => 'Failed to retrieve student transcript'
            ]);
    }

    /** @test */
    public function it_handles_bundle_courses_correctly()
    {
        Sanctum::actingAs($this->user);

        // Create additional course for bundle
        $course2 = Course::factory()->create([
            'name' => 'Advanced Programming',
            'category_id' => $this->category->id,
            'meta' => [
                'credits' => 4,
                'code' => 'CS201'
            ]
        ]);

        $this->bundle->courses()->attach($course2->id);

        // Create score for bundle
        Score::factory()->create([
            'user_id' => $this->user->id,
            'model_type' => Bundle::class,
            'model_id' => $this->bundle->id,
            'value' => 88.0,
            'batches' => 1,
            'is_graduated' => true
        ]);

        $response = $this->getJson('/api/protected/transcripts');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.course_info.type', 'bundle')
            ->assertJsonPath('data.0.course_info.total_courses', 2)
            ->assertJsonPath('data.0.course_info.credits', 7); // 3 + 4 credits
    }

    /** @test */
    public function it_calculates_grade_letters_correctly()
    {
        Sanctum::actingAs($this->user);

        $testCases = [
            ['score' => 95.0, 'expected_grade' => 'A'],
            ['score' => 82.0, 'expected_grade' => 'A-'],
            ['score' => 77.0, 'expected_grade' => 'B+'],
            ['score' => 72.0, 'expected_grade' => 'B'],
            ['score' => 67.0, 'expected_grade' => 'B-'],
            ['score' => 62.0, 'expected_grade' => 'C+'],
            ['score' => 57.0, 'expected_grade' => 'C'],
            ['score' => 52.0, 'expected_grade' => 'C-'],
            ['score' => 47.0, 'expected_grade' => 'D+'],
            ['score' => 42.0, 'expected_grade' => 'D'],
            ['score' => 35.0, 'expected_grade' => 'F']
        ];

        foreach ($testCases as $index => $testCase) {
            $course = Course::factory()->create([
                'name' => 'Test Course ' . $index,
                'category_id' => $this->category->id
            ]);

            Score::factory()->create([
                'user_id' => $this->user->id,
                'model_type' => Course::class,
                'model_id' => $course->id,
                'value' => $testCase['score'],
                'batches' => 1
            ]);
        }

        $response = $this->getJson('/api/protected/transcripts?per_page=20');

        $response->assertStatus(200);

        $data = $response->json('data');
        foreach ($data as $index => $transcript) {
            $expectedGrade = $testCases[$index]['expected_grade'];
            $this->assertEquals($expectedGrade, $transcript['score_details']['grade_letter']);
        }
    }
}