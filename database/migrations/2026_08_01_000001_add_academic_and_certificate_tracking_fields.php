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
        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->string('birth_place')->nullable()->after('graduation_year');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->integer('enrollment_year')->nullable()->after('birth_date');
            $table->string('graduation_session')->default('أول')->after('enrollment_year'); // أول / ثاني
            $table->string('prev_qualification_type')->nullable()->after('graduation_session'); // ثانوية عامة علمي / دبلوم...
            $table->integer('prev_qualification_year')->nullable()->after('prev_qualification_type');
            $table->string('prev_qualification_issuer')->nullable()->after('prev_qualification_year');
            $table->decimal('prev_qualification_gpa', 5, 2)->nullable()->after('prev_qualification_issuer');
        });

        Schema::table('doc_requests', function (Blueprint $table) {
            $table->date('submitted_to_ministry_at')->nullable()->after('status');
            $table->string('ministry_status')->default('in_ministry')->after('submitted_to_ministry_at'); // in_ministry, approved, ready, delivered, returned
            $table->string('modification_reason_type')->nullable()->after('ministry_status');
            $table->text('modification_details')->nullable()->after('modification_reason_type');
            $table->date('delivered_to_student_at')->nullable()->after('modification_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc_requests', function (Blueprint $table) {
            $table->dropColumn([
                'submitted_to_ministry_at',
                'ministry_status',
                'modification_reason_type',
                'modification_details',
                'delivered_to_student_at'
            ]);
        });

        Schema::table('alumni_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'birth_place',
                'birth_date',
                'enrollment_year',
                'graduation_session',
                'prev_qualification_type',
                'prev_qualification_year',
                'prev_qualification_issuer',
                'prev_qualification_gpa'
            ]);
        });
    }
};
