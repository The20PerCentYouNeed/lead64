<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Remove old columns
            $table->dropColumn(['title', 'data']);

            // Required fields
            $table->string('name')->after('id');
            $table->string('email')->after('name');
            $table->text('message')->after('email');

            // Optional contact fields
            $table->string('phone')->nullable()->after('message');
            $table->string('job_title')->nullable()->after('phone');

            // Optional company fields
            $table->string('company_name')->nullable()->after('job_title');
            $table->string('company_size')->nullable()->after('company_name');
            $table->string('industry')->nullable()->after('company_size');
            $table->string('website')->nullable()->after('industry');
            $table->string('country')->nullable()->after('website');

            // Optional qualification fields
            $table->string('budget')->nullable()->after('country');
            $table->string('timeline')->nullable()->after('budget');
            $table->string('source')->nullable()->after('timeline');

            // Social media fields
            $table->string('linkedin_url')->nullable()->after('source');
            $table->string('facebook_url')->nullable()->after('linkedin_url');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('twitter_url')->nullable()->after('instagram_url');

            // Flexible data
            $table->json('extra_info')->nullable()->after('twitter_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Remove new columns
            $table->dropColumn([
                'name',
                'email',
                'message',
                'phone',
                'job_title',
                'company_name',
                'company_size',
                'industry',
                'website',
                'country',
                'budget',
                'timeline',
                'source',
                'linkedin_url',
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'extra_info',
            ]);

            // Restore old columns
            $table->string('title')->after('id');
            $table->json('data')->after('title');
        });
    }
};
