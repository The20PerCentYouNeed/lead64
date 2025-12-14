<?php

namespace Tests\Feature\Api;

use App\Jobs\EvaluateLeadJob;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    public function testWebhookRequiresAuthentication(): void
    {
        $response = $this->postJson('/api/webhook/lead', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $response->assertStatus(401);
    }

    public function testWebhookCreatesLeadAndDispatchesJob(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $leadData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'company' => 'Acme Corp',
            'message' => 'Interested in your services',
        ];

        $response = $this->postJson('/api/webhook/lead', $leadData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'evaluating',
            ])
            ->assertJsonStructure([
                'lead_id',
            ]);

        $this->assertDatabaseHas('leads', [
            'user_id' => $user->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'company_name' => 'Acme Corp',
            'message' => 'Interested in your services',
            'status' => 'pending',
        ]);

        $lead = Lead::where('user_id', $user->id)->first();
        $this->assertEquals('John Doe', $lead->name);
        $this->assertEquals('john@example.com', $lead->email);
        $this->assertEquals('Acme Corp', $lead->company_name);

        Queue::assertPushed(EvaluateLeadJob::class, function ($job) use ($lead) {
            return $job->lead->id === $lead->id;
        });
    }

    public function testWebhookAcceptsAnyJsonData(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $customData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'message' => 'Test message',
            'custom_field_1' => 'value1',
            'custom_field_2' => 'value2',
        ];

        $response = $this->postJson('/api/webhook/lead', $customData);

        $response->assertStatus(200);

        $lead = Lead::where('user_id', $user->id)->first();
        $this->assertEquals('Jane Doe', $lead->name);
        $this->assertEquals('jane@example.com', $lead->email);
        $this->assertArrayHasKey('custom_field_1', $lead->extra_info);
        $this->assertEquals('value1', $lead->extra_info['custom_field_1']);
        $this->assertEquals('value2', $lead->extra_info['custom_field_2']);
    }

    public function testUserCanOnlySeeOwnLeads(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Lead::factory()->create(['user_id' => $user1->id]);
        Lead::factory()->create(['user_id' => $user2->id]);

        Sanctum::actingAs($user1);

        $leads = Lead::query()->where('user_id', auth()->id())->get();

        $this->assertCount(1, $leads);
        $this->assertEquals($user1->id, $leads->first()->user_id);
    }
}
