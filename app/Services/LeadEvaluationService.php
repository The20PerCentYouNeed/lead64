<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class LeadEvaluationService
{
    /**
     * Evaluate a lead using OpenAI based on the user's ICP criteria.
     *
     * @return array{score: int, classification: string, reasoning: string, insights: array<mixed>, recommendations: array<mixed>}
     */
    public function evaluate(Lead $lead): array
    {
        $user = $lead->user;
        $prompt = $this->buildPrompt($lead, $user);

        try {
            $response = OpenAI::chat()->create([
                'model' => config('openai.model', config('services.openai.model', 'gpt-4')),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert lead qualification assistant. Analyze leads based on the provided Ideal Customer Profile (ICP) criteria and return a detailed evaluation.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'response_format' => [
                    'type' => 'json_object',
                ],
            ]);

            $content = $response->choices[0]->message->content;
            $evaluation = json_decode($content, true);

            return [
                'score' => $evaluation['score'] ?? 0,
                'classification' => $evaluation['classification'] ?? 'cold',
                'reasoning' => $evaluation['reasoning'] ?? '',
                'insights' => $evaluation['insights'] ?? [],
                'recommendations' => $evaluation['recommendations'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('OpenAI evaluation failed', [
                'lead_id' => $lead->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    protected function buildPrompt(Lead $lead, User $user): string
    {
        $icpContext = $this->buildIcpContext($user);
        $leadData = $this->buildLeadData($lead);

        return <<<PROMPT
Evaluate the following lead based on the Ideal Customer Profile (ICP) criteria provided below.

ICP CRITERIA:
{$icpContext}

LEAD DATA:
{$leadData}

Please evaluate this lead and return a JSON response with the following structure:
{
  "score": <integer 0-100>,
  "classification": "<hot|warm|cold>",
  "reasoning": "<detailed explanation of why this score and classification>",
  "insights": {
    "strengths": ["<strength 1>", "<strength 2>"],
    "concerns": ["<concern 1>", "<concern 2>"]
  },
  "recommendations": ["<recommendation 1>", "<recommendation 2>"]
}

Score Guidelines:
- 80-100: Hot lead - Strong fit, high priority, likely to convert
- 50-79: Warm lead - Good potential, worth pursuing
- 0-49: Cold lead - Weak fit, low priority

Classification Guidelines:
- "hot": Excellent fit, high buying intent, strong indicators
- "warm": Good fit, some positive indicators, moderate interest
- "cold": Weak fit, few positive indicators, low interest

PROMPT;
    }

    protected function buildLeadData(Lead $lead): string
    {
        $data = [];

        // Contact Information
        $data[] = 'CONTACT INFORMATION:';
        if ($lead->name) {
            $data[] = "Name: {$lead->name}";
        }
        if ($lead->email) {
            $data[] = "Email: {$lead->email}";
        }
        if ($lead->phone) {
            $data[] = "Phone: {$lead->phone}";
        }
        if ($lead->job_title) {
            $data[] = "Job Title: {$lead->job_title}";
        }

        // Company Information
        $data[] = "\nCOMPANY INFORMATION:";
        if ($lead->company_name) {
            $data[] = "Company Name: {$lead->company_name}";
        }
        if ($lead->company_size) {
            $data[] = "Company Size: {$lead->company_size}";
        }
        if ($lead->industry) {
            $data[] = "Industry: {$lead->industry}";
        }
        if ($lead->website) {
            $data[] = "Website: {$lead->website}";
        }
        if ($lead->country) {
            $data[] = "Country: {$lead->country}";
        }

        // Qualification
        $data[] = "\nQUALIFICATION:";
        if ($lead->message) {
            $data[] = "Message/Project Description: {$lead->message}";
        }
        if ($lead->budget) {
            $data[] = "Budget: {$lead->budget}";
        }
        if ($lead->timeline) {
            $data[] = "Timeline: {$lead->timeline}";
        }
        if ($lead->source) {
            $data[] = "Lead Source: {$lead->source}";
        }

        // Social Media
        $socialProfiles = [];
        if ($lead->linkedin_url) {
            $socialProfiles[] = "LinkedIn: {$lead->linkedin_url}";
        }
        if ($lead->facebook_url) {
            $socialProfiles[] = "Facebook: {$lead->facebook_url}";
        }
        if ($lead->instagram_url) {
            $socialProfiles[] = "Instagram: {$lead->instagram_url}";
        }
        if ($lead->twitter_url) {
            $socialProfiles[] = "Twitter/X: {$lead->twitter_url}";
        }
        if (! empty($socialProfiles)) {
            $data[] = "\nSOCIAL MEDIA PROFILES:";
            $data[] = implode("\n", $socialProfiles);
        }

        // Extra Info
        if ($lead->extra_info && is_array($lead->extra_info) && ! empty($lead->extra_info)) {
            $data[] = "\nADDITIONAL INFORMATION:";
            foreach ($lead->extra_info as $key => $value) {
                $data[] = "{$key}: {$value}";
            }
        }

        return implode("\n", $data);
    }

    protected function buildIcpContext(User $user): string
    {
        $context = [];

        if ($user->company_name) {
            $context[] = "Company: {$user->company_name}";
        }

        if ($user->company_description) {
            $context[] = "Company Description: {$user->company_description}";
        }

        if ($user->industry) {
            $context[] = "Industry: {$user->industry}";
        }

        if ($user->ideal_industries) {
            $industries = is_array($user->ideal_industries) ? implode(', ', $user->ideal_industries) : $user->ideal_industries;
            $context[] = "Ideal Industries: {$industries}";
        }

        if ($user->ideal_company_sizes) {
            $sizes = is_array($user->ideal_company_sizes) ? implode(', ', $user->ideal_company_sizes) : $user->ideal_company_sizes;
            $context[] = "Ideal Company Sizes: {$sizes}";
        }

        if ($user->ideal_use_cases) {
            $context[] = "Ideal Use Cases: {$user->ideal_use_cases}";
        }

        if ($user->disqualifiers) {
            $context[] = "Disqualifiers: {$user->disqualifiers}";
        }

        if ($user->additional_context) {
            $context[] = "Additional Context: {$user->additional_context}";
        }

        return implode("\n", $context) ?: 'No ICP criteria specified.';
    }
}
