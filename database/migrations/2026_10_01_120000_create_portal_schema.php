<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Two roles: job seeker (default) and job provider (employer).
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('seeker')->after('email'); // seeker | provider
            $table->string('headline')->nullable()->after('role');
            $table->string('phone')->nullable()->after('headline');
        });

        // Job postings, owned by a provider. Named job_posts to avoid colliding
        // with Laravel's queue "jobs" table.
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // provider
            $table->string('title');
            $table->string('company');
            $table->string('location');
            $table->string('employment_type')->default('Full-time');
            $table->string('category')->default('Other');
            $table->string('salary')->nullable();
            $table->text('description');
            $table->text('skills')->nullable(); // comma-separated keywords
            $table->string('status')->default('open'); // open | closed
            $table->timestamps();
        });

        // Resumes uploaded by seekers. Parsed text + extracted skills power the
        // job suggestions.
        Schema::create('resumes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // seeker
            $table->string('original_name');
            $table->string('path');
            $table->string('mime')->nullable();
            $table->longText('parsed_text')->nullable();
            $table->text('skills')->nullable(); // comma-separated, extracted
            $table->timestamps();
        });

        // A seeker applies to a job with a resume; providers see these.
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // seeker
            $table->foreignId('resume_id')->nullable()->constrained()->nullOnDelete();
            $table->text('cover_note')->nullable();
            $table->string('status')->default('applied'); // applied | shortlisted | rejected
            $table->timestamps();
            $table->unique(['job_post_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
        Schema::dropIfExists('resumes');
        Schema::dropIfExists('job_posts');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'headline', 'phone']);
        });
    }
};
