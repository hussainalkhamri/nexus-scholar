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
        Schema::table('groups', function (Blueprint $table) {
            $table->integer('plagiarism_percent')->nullable();
            $table->string('grade')->nullable(); // passed, passed_revisions, failed
            $table->text('defense_committee')->nullable();
            $table->dateTime('defense_date')->nullable();
        });

        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->string('linkedin_url')->nullable();
        });

        Schema::table('doc_requests', function (Blueprint $table) {
            $table->string('file_path')->nullable();
            $table->string('verification_code')->nullable()->unique();
        });

        Schema::create('incentive_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('incentive_id')->constrained('incentives')->onDelete('cascade');
            $table->string('status')->default('applied'); // applied, redeemed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incentive_user');

        Schema::table('doc_requests', function (Blueprint $table) {
            $table->dropColumn(['file_path', 'verification_code']);
        });

        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->dropColumn(['linkedin_url']);
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['plagiarism_percent', 'grade', 'defense_committee', 'defense_date']);
        });
    }
};
