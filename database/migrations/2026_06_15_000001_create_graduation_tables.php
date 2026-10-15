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
        // 1. Groups Table
        Schema::create('groups', function (Blueprint $table) {
            $table->id(); // Corresponds to GP-1000, GP-1001 etc.
            $table->string('title_ar');
            $table->string('title_en');
            $table->string('department'); // IT, CS, Medicine, Pharmacy, Dentistry
            $table->string('supervisor'); // Or foreign key to users. For simplicity with mock-data, we can store supervisor name or foreign key. Let's store supervisor name and supervisor_id to link
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('stage', ['proposal', 'chapters', 'draft', 'defense'])->default('proposal');
            $table->enum('status', ['completed', 'processing', 'pending', 'overdue'])->default('pending');
            $table->integer('progress')->default(0);
            $table->integer('days_in_stage')->default(1);
            $table->string('semester');
            $table->timestamps();
        });

        // 2. Group Students Pivot Table
        Schema::create('group_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 3. Alumni Profiles Table
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('graduation_year');
            $table->string('workplace')->nullable();
            $table->string('position')->nullable();
            $table->boolean('employed')->default(false);
            $table->json('skills')->nullable(); // Store tags/skills array as JSON
            $table->timestamps();
        });

        // 4. Document Requests Table
        Schema::create('doc_requests', function (Blueprint $table) {
            $table->id(); // REQ-3000, REQ-3001 etc.
            $table->foreignId('alumnus_id')->constrained('users')->onDelete('cascade');
            $table->string('doc_type_ar');
            $table->string('doc_type_en');
            $table->enum('status', ['completed', 'processing', 'pending', 'overdue'])->default('pending');
            $table->integer('waiting_days')->default(1);
            $table->timestamps(); // includes created_at (request date)
        });

        // 5. Incentives, training, and jobs table
        Schema::create('incentives', function (Blueprint $table) {
            $table->id(); // I1, I2 etc.
            $table->enum('kind', ['course', 'job', 'discount']);
            $table->string('title_ar');
            $table->string('title_en');
            $table->text('detail_ar');
            $table->text('detail_en');
            $table->timestamps();
        });

        // 6. Comments (discussion threads) table
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('author_name')->nullable();
            $table->text('content');
            $table->timestamps();
        });

        // 7. Project uploaded files table
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('filename');
            $table->string('file_path');
            $table->string('file_size');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_files');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('incentives');
        Schema::dropIfExists('doc_requests');
        Schema::dropIfExists('alumni_profiles');
        Schema::dropIfExists('group_student');
        Schema::dropIfExists('groups');
    }
};
