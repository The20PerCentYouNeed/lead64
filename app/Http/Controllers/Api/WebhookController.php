<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\EvaluateLeadJob;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();

        // Map known fields
        $leadData = [
            'user_id' => auth()->id(),
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'message' => $data['message'] ?? $data['description'] ?? $data['project_description'] ?? null,
            'phone' => $data['phone'] ?? $data['phone_number'] ?? null,
            'job_title' => $data['job_title'] ?? $data['position'] ?? null,
            'company_name' => $data['company_name'] ?? $data['company'] ?? null,
            'company_size' => $data['company_size'] ?? null,
            'industry' => $data['industry'] ?? null,
            'website' => $data['website'] ?? $data['company_website'] ?? null,
            'country' => $data['country'] ?? null,
            'budget' => $data['budget'] ?? null,
            'timeline' => $data['timeline'] ?? null,
            'source' => $data['source'] ?? $data['lead_source'] ?? null,
            'linkedin_url' => $data['linkedin_url'] ?? $data['linkedin'] ?? null,
            'facebook_url' => $data['facebook_url'] ?? $data['facebook'] ?? null,
            'instagram_url' => $data['instagram_url'] ?? $data['instagram'] ?? null,
            'twitter_url' => $data['twitter_url'] ?? $data['twitter'] ?? null,
            'status' => 'pending',
        ];

        // Collect any unmapped fields into extra_info
        $knownFields = [
            'name', 'email', 'message', 'description', 'project_description',
            'phone', 'phone_number', 'job_title', 'position',
            'company_name', 'company', 'company_size', 'industry',
            'website', 'company_website', 'country', 'budget', 'timeline',
            'source', 'lead_source',
            'linkedin_url', 'linkedin', 'facebook_url', 'facebook',
            'instagram_url', 'instagram', 'twitter_url', 'twitter',
        ];

        $extraInfo = [];
        foreach ($data as $key => $value) {
            if (! in_array($key, $knownFields) && $value !== null) {
                $extraInfo[$key] = $value;
            }
        }

        if (! empty($extraInfo)) {
            $leadData['extra_info'] = $extraInfo;
        }

        $lead = Lead::create($leadData);

        EvaluateLeadJob::dispatch($lead);

        return response()->json([
            'success' => true,
            'lead_id' => $lead->id,
            'status' => 'evaluating',
        ]);
    }
}
