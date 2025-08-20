<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses Report - {{ $bundle->name }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header h2 {
            color: #64748b;
            margin: 10px 0 0 0;
            font-size: 18px;
            font-weight: normal;
        }
        .info-section {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #2563eb;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 20px 5px 0;
            width: 150px;
            color: #374151;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
            color: #6b7280;
        }
        .courses-section {
            margin-top: 30px;
        }
        .section-title {
            font-size: 22px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        .course-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 25px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .course-header {
            background-color: #2563eb;
            color: white;
            padding: 15px 20px;
        }
        .course-title {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .course-instructor {
            font-size: 14px;
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .course-body {
            padding: 20px;
        }
        .course-description {
            color: #6b7280;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stats-row {
            display: table-row;
        }
        .stat-item {
            display: table-cell;
            padding: 10px;
            text-align: center;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
        }
        .stat-value {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            display: block;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .progress-fill {
            height: 100%;
            background-color: #10b981;
            transition: width 0.3s ease;
        }
        .score-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .score-excellent { background-color: #10b981; }
        .score-good { background-color: #3b82f6; }
        .score-average { background-color: #f59e0b; }
        .score-poor { background-color: #ef4444; }
        .course-details {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
        }
        .detail-grid {
            display: table;
            width: 100%;
        }
        .detail-row {
            display: table-row;
        }
        .detail-label {
            display: table-cell;
            font-weight: bold;
            padding: 3px 15px 3px 0;
            width: 120px;
            color: #374151;
            font-size: 14px;
        }
        .detail-value {
            display: table-cell;
            padding: 3px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .requirements-list, .outcomes-list {
            margin: 10px 0;
            padding-left: 20px;
        }
        .requirements-list li, .outcomes-list li {
            margin-bottom: 5px;
            color: #6b7280;
            font-size: 14px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
        }
        .summary-stats {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        .summary-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 15px;
        }
        .no-courses {
            text-align: center;
            padding: 40px;
            color: #6b7280;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Courses Progress Report</h1>
        <h2>{{ $bundle->name }}</h2>
    </div>

    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Student Name:</div>
                <div class="info-value">{{ $user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Bundle:</div>
                <div class="info-value">{{ $bundle->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Generated:</div>
                <div class="info-value">{{ $generated_at }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Courses:</div>
                <div class="info-value">{{ count($courses_data) }}</div>
            </div>
        </div>
    </div>

    @if(count($courses_data) > 0)
        <div class="summary-stats">
            <div class="summary-title">Overall Summary</div>
            <div class="stats-grid">
                <div class="stats-row">
                    <div class="stat-item">
                        <span class="stat-value">{{ array_sum(array_column($courses_data, 'total_lessons')) }}</span>
                        <span class="stat-label">Total Lessons</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ array_sum(array_column($courses_data, 'completed_lessons')) }}</span>
                        <span class="stat-label">Completed</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ number_format(array_sum(array_column($courses_data, 'completion_percentage')) / count($courses_data), 1) }}%</span>
                        <span class="stat-label">Avg Progress</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ number_format(array_sum(array_column($courses_data, 'average_score')) / count(array_filter($courses_data, function($c) { return $c['average_score'] > 0; })), 1) }}</span>
                        <span class="stat-label">Avg Score</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="courses-section">
            <h2 class="section-title">Course Details</h2>
            
            @foreach($courses_data as $course)
                <div class="course-card">
                    <div class="course-header">
                        <h3 class="course-title">{{ $course['name'] }}</h3>
                        <p class="course-instructor">Instructor: {{ $course['instructor'] }}</p>
                    </div>
                    
                    <div class="course-body">
                        @if($course['description'])
                            <p class="course-description">{{ $course['description'] }}</p>
                        @endif
                        
                        <div class="stats-grid">
                            <div class="stats-row">
                                <div class="stat-item">
                                    <span class="stat-value">{{ $course['total_lessons'] }}</span>
                                    <span class="stat-label">Total Lessons</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">{{ $course['completed_lessons'] }}</span>
                                    <span class="stat-label">Completed</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">{{ $course['completion_percentage'] }}%</span>
                                    <span class="stat-label">Progress</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-value">{{ $course['average_score'] }}</span>
                                    <span class="stat-label">Avg Score</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $course['completion_percentage'] }}%"></div>
                        </div>
                        
                        @if($course['average_score'] > 0)
                            <div style="margin: 10px 0;">
                                <span class="score-badge 
                                    @if($course['average_score'] >= 90) score-excellent
                                    @elseif($course['average_score'] >= 80) score-good
                                    @elseif($course['average_score'] >= 70) score-average
                                    @else score-poor
                                    @endif">
                                    Score: {{ $course['average_score'] }}
                                </span>
                            </div>
                        @endif
                        
                        <div class="course-details">
                            <div class="detail-grid">
                                <div class="detail-row">
                                    <div class="detail-label">Level:</div>
                                    <div class="detail-value">{{ ucfirst($course['level']) }}</div>
                                </div>
                                <div class="detail-row">
                                    <div class="detail-label">Language:</div>
                                    <div class="detail-value">{{ $course['language'] }}</div>
                                </div>
                                @if($course['duration'])
                                    <div class="detail-row">
                                        <div class="detail-label">Duration:</div>
                                        <div class="detail-value">{{ $course['duration'] }} {{ $course['duration_units'] }}</div>
                                    </div>
                                @endif
                                <div class="detail-row">
                                    <div class="detail-label">Price:</div>
                                    <div class="detail-value">{{ number_format($course['price']['amount'], 0, ',', '.') }} {{ $course['price']['currency'] }}</div>
                                </div>
                            </div>
                            
                            @if(count($course['requirements']) > 0)
                                <div style="margin-top: 15px;">
                                    <strong>Requirements:</strong>
                                    <ul class="requirements-list">
                                        @foreach($course['requirements'] as $requirement)
                                            <li>{{ $requirement }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            @if(count($course['learning_outcomes']) > 0)
                                <div style="margin-top: 15px;">
                                    <strong>Learning Outcomes:</strong>
                                    <ul class="outcomes-list">
                                        @foreach($course['learning_outcomes'] as $outcome)
                                            <li>{{ $outcome }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="no-courses">
            <p>No courses found in this bundle.</p>
        </div>
    @endif

    <div class="footer">
        <p>This report was generated automatically on {{ $generated_at }}</p>
        <p>© {{ date('Y') }} Learning Management System</p>
    </div>
</body>
</html>