<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Course;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class GenerateCertificateJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Course $course, public User $user) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Generate PDF from a view
        $pdf = Pdf::loadView('pdf.certificate', ['course' => $this->course, 'user' => $this->user])
            ->setPaper('a4', 'landscape');

        // Save PDF to storage
        $this->course->addMedia($pdf)->toMediaCollection('certificates');
    }
}
