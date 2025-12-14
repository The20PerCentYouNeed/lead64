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
        Schema::table('users', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('name');
            $table->text('company_description')->nullable()->after('company_name');
            $table->string('website')->nullable()->after('company_description');
            $table->string('industry')->nullable()->after('website');
            $table->json('ideal_industries')->nullable()->after('industry');
            $table->json('ideal_company_sizes')->nullable()->after('ideal_industries');
            $table->text('ideal_use_cases')->nullable()->after('ideal_company_sizes');
            $table->text('disqualifiers')->nullable()->after('ideal_use_cases');
            $table->text('additional_context')->nullable()->after('disqualifiers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'company_description',
                'website',
                'industry',
                'ideal_industries',
                'ideal_company_sizes',
                'ideal_use_cases',
                'disqualifiers',
                'additional_context',
            ]);
        });
    }
};
