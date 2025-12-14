<?php

namespace Tests\Feature\Services;

use App\Models\Lead;
use App\Models\User;
use App\Services\LeadEvaluationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\TestCase;

class LeadEvaluationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testEvaluationReturnsStructuredData(): void
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'score' => 85,
                                'classification' => 'hot',
                                'reasoning' => 'Strong fit for enterprise automation needs',
                                'insights' => [
                                    'strengths' => ['Enterprise company', 'Clear use case'],
                                    'concerns' => ['Budget not mentioned'],
                                ],
                                'recommendations' => [
                                    'Follow up within 24 hours',
                                    'Prepare enterprise pricing',
                                ],
                            ]),
                        ],
                    ],
                ],
            ]),
        ]);

        $user = User::factory()->create([
            'company_name' => 'Test Company',
            'ideal_industries' => ['Technology', 'Healthcare'],
            'ideal_use_cases' => 'Enterprise automation',
        ]);

        $lead = Lead::factory()->create([
            'user_id' => $user->id,
            'name' => 'John Doe',
            'email' => 'john@enterprise.com',
            'company_name' => 'Enterprise Corp',
            'industry' => 'Technology',
            'message' => 'Looking for AI automation solutions',
        ]);

        $service = new LeadEvaluationService;
        $result = $service->evaluate($lead);

        $this->assertEquals(85, $result['score']);
        $this->assertEquals('hot', $result['classification']);
        $this->assertArrayHasKey('reasoning', $result);
        $this->assertArrayHasKey('insights', $result);
        $this->assertArrayHasKey('recommendations', $result);
    }

    public function testEvaluationHandlesMissingIcpGracefully(): void
    {
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'score' => 50,
                                'classification' => 'warm',
                                'reasoning' => 'Limited ICP context',
                                'insights' => [],
                                'recommendations' => [],
                            ]),
                        ],
                    ],
                ],
            ]),
        ]);

        $user = User::factory()->create();
        $lead = Lead::factory()->create(['user_id' => $user->id]);

        $service = new LeadEvaluationService;
        $result = $service->evaluate($lead);

        $this->assertIsInt($result['score']);
        $this->assertIsString($result['classification']);
    }
}
