<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Progress;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CertificateService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function create(Certificate $certificate, Progress $progress, Request $request)
    {
        if (!$certificate->hasMedia('certificates') || $request->input('force_create', false)) {
            // Generate PDF using blade
            $certificate->clearMediaCollection('certificates');
            $name = $request->user()->name;
            $certificate_name = $progress->model->name . ' certificate';
            $certificate_number = $certificate->certificate_number;
            $date = $certificate->issue_date;
            $instructor_signature = $progress->model->user->hasMedia('signatures') ? $progress->model->getMedia('signatures')->first()->getFullUrl() : public_path('images/director_signature.png');
            $director_signature = public_path('images/director_signature.png');
            $pdf = Pdf::loadView('pdf.certificate', compact(
                'name',
                'certificate_name',
                'certificate_number',
                'instructor_signature',
                'director_signature',
                'date'
            ))
                ->setPaper('a4', 'landscape')->setWarnings(false)->output();
            $certificate
                ->addMediaFromString($pdf)
                ->usingFileName(Str::upper(Str::slug($progress->model->name . '_' . $request->user()->id . '_' . $certificate->certificate_number, '_')) . '.pdf')
                ->toMediaCollection('certificates', 's3');
        }
    }
}
