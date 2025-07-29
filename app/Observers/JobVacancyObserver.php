<?php

namespace App\Observers;

use App\Models\JobVacancy;
use Illuminate\Support\Str;

class JobVacancyObserver
{
    /**
     * Handle the JobVacancy "creating" event.
     */
    public function creating(JobVacancy $jobVacancy): void
    {
        $jobVacancy->slug = Str::slug($jobVacancy->name);
        $jobVacancy->user_id = auth()->user()->id;
    }

    /**
     * Handle the JobVacancy "created" event.
     */
    public function created(JobVacancy $jobVacancy): void
    {
        //
    }

    /**
     * Handle the JobVacancy "updating" event.
     */
    public function updating(JobVacancy $jobVacancy): void
    {
        $jobVacancy->slug = Str::slug($jobVacancy->name);
    }

    /**
     * Handle the JobVacancy "updated" event.
     */
    public function updated(JobVacancy $jobVacancy): void
    {
        //
    }

    /**
     * Handle the JobVacancy "deleted" event.
     */
    public function deleted(JobVacancy $jobVacancy): void
    {
        //
    }

    /**
     * Handle the JobVacancy "restored" event.
     */
    public function restored(JobVacancy $jobVacancy): void
    {
        //
    }

    /**
     * Handle the JobVacancy "force deleted" event.
     */
    public function forceDeleted(JobVacancy $jobVacancy): void
    {
        //
    }
}
