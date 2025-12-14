<?php

namespace App\Jobs;

use App\Models\Evaluation;
use App\Models\Lead;
use App\Services\LeadEvaluationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EvaluateLeadJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Lead $lead
    ) {}

    public function handle(LeadEvaluationService $service): void
    {
        try {
            $this->lead->update(['status' => 'evaluating']);

            $evaluationData = $service->evaluate($this->lead);

            Evaluation::updateOrCreate(
                ['lead_id' => $this->lead->id],
                $evaluationData
            );

            $this->lead->update(['status' => 'evaluated']);
        } catch (\Exception $e) {
            Log::error('Lead evaluation failed', [
                'lead_id' => $this->lead->id,
                'error' => $e->getMessage(),
            ]);

            $this->lead->update(['status' => 'failed']);

            throw $e;
        }
    }
}
