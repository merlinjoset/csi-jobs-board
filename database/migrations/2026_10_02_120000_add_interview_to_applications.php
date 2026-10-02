<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // A short response/note from the provider to the seeker.
            $table->text('provider_message')->nullable()->after('status');
            // Interview scheduling.
            $table->timestamp('interview_at')->nullable()->after('provider_message');
            $table->string('interview_mode')->nullable()->after('interview_at');       // In-person | Online | Phone
            $table->string('interview_location')->nullable()->after('interview_mode'); // address or meeting link
            $table->text('interview_note')->nullable()->after('interview_location');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['provider_message', 'interview_at', 'interview_mode', 'interview_location', 'interview_note']);
        });
    }
};
