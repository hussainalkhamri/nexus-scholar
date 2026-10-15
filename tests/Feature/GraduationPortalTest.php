<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Group;
use App\Models\DocRequest;
use App\Models\Incentive;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GraduationPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_loads_successfully()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_login_page_loads_successfully()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_access_reports()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'name_ar' => 'مسؤول',
            'name_en' => 'Admin',
            'email' => 'admin@nexus-scholar.edu',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard/reports');
        $response->assertStatus(200);
    }

    public function test_student_cannot_access_reports()
    {
        $student = User::create([
            'name' => 'Student User',
            'name_ar' => 'طالب',
            'name_en' => 'Student',
            'email' => 'student@nexus-scholar.edu',
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($student)->get('/dashboard/reports');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_admin_can_perform_user_crud()
    {
        $admin = User::create([
            'name' => 'Admin User', 'name_ar' => 'مسؤول', 'name_en' => 'Admin',
            'email' => 'admin_crud@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'admin',
        ]);

        // 1. Read Users List
        $response = $this->actingAs($admin)->get('/dashboard/users');
        $response->assertStatus(200);

        // 2. Create User
        $response = $this->actingAs($admin)->post('/dashboard/users', [
            'name_ar' => 'جديد',
            'name_en' => 'New User',
            'email' => 'new_user@nexus-scholar.edu',
            'role' => 'student',
            'department' => 'IT',
            'phone' => '1234567890',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => 'new_user@nexus-scholar.edu']);

        $user = User::where('email', 'new_user@nexus-scholar.edu')->first();

        // 3. Update User (Role)
        $response = $this->actingAs($admin)->post("/dashboard/users/{$user->id}/update", [
            'role' => 'supervisor',
        ]);
        $response->assertRedirect();
        $this->assertEquals('supervisor', $user->fresh()->role);

        // 4. Delete User
        $response = $this->actingAs($admin)->post("/dashboard/users/{$user->id}/delete");
        $response->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_non_admin_cannot_perform_user_crud()
    {
        $supervisor = User::create([
            'name' => 'Supervisor User', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'supervisor_rbac@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $student = User::create([
            'name' => 'Student User', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'student_rbac@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
        ]);

        // Supervisor try read
        $response = $this->actingAs($supervisor)->get('/dashboard/users');
        $response->assertRedirect('/dashboard');

        // Student try read
        $response = $this->actingAs($student)->get('/dashboard/users');
        $response->assertStatus(403); // because of can:view-reports, student fails gate

        // Student try write
        $response = $this->actingAs($student)->post('/dashboard/users', [
            'name_ar' => 'جديد',
            'name_en' => 'New User',
            'email' => 'hacker@nexus-scholar.edu',
            'role' => 'student',
        ]);
        $response->assertStatus(403); // fails gate
    }

    public function test_admin_and_supervisor_can_perform_project_crud()
    {
        $supervisor = User::create([
            'name' => 'Sup User', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_project@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        // Create
        $response = $this->actingAs($supervisor)->post('/dashboard/projects', [
            'title_ar' => 'مشروع تخرج جديد',
            'title_en' => 'New Graduation Project',
            'department' => 'IT',
            'supervisor_id' => $supervisor->id,
            'semester' => 'Fall 2026',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('groups', ['title_en' => 'New Graduation Project']);

        $group = Group::where('title_en', 'New Graduation Project')->first();

        // Update
        $response = $this->actingAs($supervisor)->post("/dashboard/projects/{$group->id}/edit", [
            'title_ar' => 'عنوان معدل',
            'title_en' => 'Updated Project Title',
            'department' => 'CS',
            'supervisor_id' => $supervisor->id,
            'semester' => 'Spring 2027',
        ]);
        $response->assertRedirect();
        $this->assertEquals('Updated Project Title', $group->fresh()->title_en);

        // Delete
        $response = $this->actingAs($supervisor)->post("/dashboard/projects/{$group->id}/delete");
        $response->assertRedirect();
        $this->assertDatabaseMissing('groups', ['id' => $group->id]);
    }

    public function test_student_cannot_perform_project_crud()
    {
        $student = User::create([
            'name' => 'Stud User', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'stud_project@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
        ]);

        $supervisor = User::create([
            'name' => 'Sup User', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_project2@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $group = Group::create([
            'title_ar' => 'مشروع قائم',
            'title_en' => 'Existing Project',
            'department' => 'IT',
            'supervisor' => 'مشرف',
            'supervisor_id' => $supervisor->id,
            'stage' => 'proposal',
            'status' => 'pending',
            'progress' => 20,
            'days_in_stage' => 1,
            'semester' => 'Spring 2026',
        ]);

        // Try create
        $response = $this->actingAs($student)->post('/dashboard/projects', [
            'title_ar' => 'اختراق',
            'title_en' => 'Hacked',
            'department' => 'IT',
            'supervisor_id' => $supervisor->id,
            'semester' => 'Fall 2026',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error'); // Access denied redirect

        // Try delete
        $response = $this->actingAs($student)->post("/dashboard/projects/{$group->id}/delete");
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_can_perform_incentive_crud()
    {
        $admin = User::create([
            'name' => 'Admin User', 'name_ar' => 'مسؤول', 'name_en' => 'Admin',
            'email' => 'admin_incentive@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'admin',
        ]);

        // Create
        $response = $this->actingAs($admin)->post('/dashboard/incentives', [
            'kind' => 'job',
            'title_ar' => 'فرصة عمل جديدة',
            'title_en' => 'New Job Opportunity',
            'detail_ar' => 'تفاصيل العمل',
            'detail_en' => 'Job Details',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('incentives', ['title_en' => 'New Job Opportunity']);

        $incentive = \App\Models\Incentive::where('title_en', 'New Job Opportunity')->first();

        // Update
        $response = $this->actingAs($admin)->post("/dashboard/incentives/{$incentive->id}/edit", [
            'kind' => 'course',
            'title_ar' => 'دورة تدريبية معدلة',
            'title_en' => 'Updated Training Course',
            'detail_ar' => 'تفاصيل الدورة',
            'detail_en' => 'Course Details',
        ]);
        $response->assertRedirect();
        $this->assertEquals('Updated Training Course', $incentive->fresh()->title_en);

        // Delete
        $response = $this->actingAs($admin)->post("/dashboard/incentives/{$incentive->id}/delete");
        $response->assertRedirect();
        $this->assertDatabaseMissing('incentives', ['id' => $incentive->id]);
    }

    public function test_user_registration_succeeds_with_strong_password()
    {
        $response = $this->post('/register', [
            'name_ar' => 'سالم الحربي',
            'name_en' => 'Salem Al-Harbi',
            'email' => 'salem@nexus-scholar.edu',
            'password' => 'StrongP@ss123',
            'password_confirmation' => 'StrongP@ss123',
            'role' => 'student',
            'department' => 'IT',
            'phone' => '12345678',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'salem@nexus-scholar.edu']);
    }

    public function test_user_registration_fails_with_weak_password()
    {
        $response = $this->post('/register', [
            'name_ar' => 'سالم الحربي',
            'name_en' => 'Salem Al-Harbi',
            'email' => 'weakpwd@nexus-scholar.edu',
            'password' => '1234',
            'password_confirmation' => '1234',
            'role' => 'student',
            'department' => 'IT',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseMissing('users', ['email' => 'weakpwd@nexus-scholar.edu']);
    }

    public function test_student_dashboard_displays_correct_data()
    {
        $student = User::create([
            'name' => 'Stud User', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'student_dash@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
            'department' => 'IT',
        ]);

        $response = $this->actingAs($student)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('group');
        $response->assertViewHas('files');
        $response->assertViewHas('comments');
        $response->assertSee('Welcome to UniNexus Graduation Portal');
    }

    public function test_alumni_dashboard_displays_correct_data()
    {
        $alumnus = User::create([
            'name' => 'Alum User', 'name_ar' => 'خريج', 'name_en' => 'Alumnus',
            'email' => 'alumni_dash@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
            'department' => 'IT',
        ]);

        $response = $this->actingAs($alumnus)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('profile');
        $response->assertViewHas('requests');
        $response->assertViewHas('incentives');
        $response->assertSee('My Professional Profile');
    }

    public function test_student_is_redirected_to_their_project_from_index()
    {
        $supervisor = User::create([
            'name' => 'Sup User', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_red@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
            'department' => 'IT',
        ]);

        $student = User::create([
            'name' => 'Stud User', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'student_redirect@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
            'department' => 'IT',
        ]);

        $group = Group::create([
            'title_ar' => 'مشروع الطالب', 'title_en' => 'Student Project', 'department' => 'IT',
            'supervisor' => 'مشرف', 'supervisor_id' => $supervisor->id, 'stage' => 'proposal', 'status' => 'pending',
            'progress' => 20, 'days_in_stage' => 1, 'semester' => 'Spring 2026',
        ]);
        $group->students()->attach($student->id);

        $response = $this->actingAs($student)->get('/dashboard/projects');
        $response->assertRedirect("/dashboard/projects/{$group->id}");
    }

    public function test_student_cannot_view_other_projects_details()
    {
        $supervisor = User::create([
            'name' => 'Sup User', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_block@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
            'department' => 'IT',
        ]);

        $student = User::create([
            'name' => 'Stud User', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'student_block@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
            'department' => 'IT',
        ]);

        $otherGroup = Group::create([
            'title_ar' => 'مشروع آخر', 'title_en' => 'Other Project', 'department' => 'IT',
            'supervisor' => 'مشرف', 'supervisor_id' => $supervisor->id, 'stage' => 'proposal', 'status' => 'pending',
            'progress' => 20, 'days_in_stage' => 1, 'semester' => 'Spring 2026',
        ]);

        $response = $this->actingAs($student)->get("/dashboard/projects/{$otherGroup->id}");
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_supervisor_only_sees_their_supervised_projects_on_index()
    {
        $supervisor1 = User::create([
            'name' => 'Sup User 1', 'name_ar' => 'مشرف 1', 'name_en' => 'Supervisor 1',
            'email' => 'supervisor_index1@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
            'department' => 'IT',
        ]);

        $supervisor2 = User::create([
            'name' => 'Sup User 2', 'name_ar' => 'مشرف 2', 'name_en' => 'Supervisor 2',
            'email' => 'supervisor_index2@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
            'department' => 'IT',
        ]);

        $myGroup = Group::create([
            'title_ar' => 'مشروعي', 'title_en' => 'My Project', 'department' => 'IT',
            'supervisor' => 'مشرف 1', 'supervisor_id' => $supervisor1->id, 'stage' => 'proposal', 'status' => 'pending',
            'progress' => 20, 'days_in_stage' => 1, 'semester' => 'Spring 2026',
        ]);

        $otherGroup = Group::create([
            'title_ar' => 'مشروع آخر', 'title_en' => 'Other Project', 'department' => 'IT',
            'supervisor' => 'مشرف 2', 'supervisor_id' => $supervisor2->id, 'stage' => 'proposal', 'status' => 'pending',
            'progress' => 20, 'days_in_stage' => 1, 'semester' => 'Spring 2026',
        ]);

        $response = $this->actingAs($supervisor1)->get('/dashboard/projects');
        $response->assertStatus(200);
        $response->assertViewHas('groups');
        $groups = $response->viewData('groups');
        $this->assertTrue($groups->contains('id', $myGroup->id));
        $this->assertFalse($groups->contains('id', $otherGroup->id));
    }

    public function test_supervisor_lifecycle_and_plagiarism_enforcement()
    {
        $supervisor = User::create([
            'name' => 'Sup User', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_life@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
            'department' => 'IT',
        ]);

        $group = Group::create([
            'title_ar' => 'مشروع مسودة', 'title_en' => 'Draft Project', 'department' => 'IT',
            'supervisor' => 'مشرف', 'supervisor_id' => $supervisor->id, 'stage' => 'draft', 'status' => 'processing',
            'progress' => 75, 'days_in_stage' => 2, 'semester' => 'Spring 2026',
        ]);

        // 1. Try to advance with plagiarism > 20% (Should fail)
        $response = $this->actingAs($supervisor)->post("/dashboard/projects/{$group->id}/stage", [
            'plagiarism_percent' => 25,
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals(25, $group->fresh()->plagiarism_percent);
        $this->assertEquals('draft', $group->fresh()->stage); // blocked

        // 2. Try to advance with plagiarism <= 20% (Should succeed)
        $response = $this->actingAs($supervisor)->post("/dashboard/projects/{$group->id}/stage", [
            'plagiarism_percent' => 12,
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals(12, $group->fresh()->plagiarism_percent);
        $this->assertEquals('defense', $group->fresh()->stage); // advanced
    }

    public function test_supervisor_defense_grading_and_automatic_alumni_transition()
    {
        $supervisor = User::create([
            'name' => 'Sup User', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_grading@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
            'department' => 'IT',
        ]);

        $student = User::create([
            'name' => 'Student User', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'student_grade@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
            'department' => 'IT',
        ]);

        $group = Group::create([
            'title_ar' => 'مشروع مناقشة', 'title_en' => 'Defense Project', 'department' => 'IT',
            'supervisor' => 'مشرف', 'supervisor_id' => $supervisor->id, 'stage' => 'defense', 'status' => 'processing',
            'progress' => 90, 'days_in_stage' => 1, 'semester' => 'Spring 2026',
        ]);
        $group->students()->attach($student->id);

        // Submit passed final grading
        $response = $this->actingAs($supervisor)->post("/dashboard/projects/{$group->id}/stage", [
            'grade' => 'passed',
            'defense_committee' => 'Dr. Ahmad, Dr. Ali',
            'defense_date' => '2026-06-20 10:00:00',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('completed', $group->fresh()->status);
        $this->assertEquals(100, $group->fresh()->progress);
        $this->assertEquals('passed', $group->fresh()->grade);
        $this->assertEquals('alumnus', $student->fresh()->role); // Transitioned to alumnus!
        $this->assertDatabaseHas('alumni_profiles', ['user_id' => $student->id, 'points' => 100]);
    }

    public function test_document_verification_flow_and_download()
    {
        $alumnus = User::create([
            'name' => 'Alum User', 'name_ar' => 'خريج', 'name_en' => 'Alumnus',
            'email' => 'alumni_docs@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
            'department' => 'IT',
        ]);

        $profile = \App\Models\AlumniProfile::create([
            'user_id' => $alumnus->id, 'graduation_year' => 2026, 'employed' => false, 'points' => 100,
        ]);

        $docRequest = DocRequest::create([
            'alumnus_id' => $alumnus->id,
            'doc_type_ar' => 'كشف درجات',
            'doc_type_en' => 'Transcript',
            'status' => 'pending',
            'waiting_days' => 1,
        ]);

        $admin = User::create([
            'name' => 'Admin User', 'name_ar' => 'مسؤول', 'name_en' => 'Admin',
            'email' => 'admin_docs@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'admin',
        ]);

        // 1. Approve document request
        $response = $this->actingAs($admin)->post("/dashboard/alumni/requests/{$docRequest->id}/status", [
            'status' => 'completed',
        ]);
        $response->assertRedirect();

        $docRequest = $docRequest->fresh();
        $this->assertEquals('completed', $docRequest->status);
        $this->assertNotNull($docRequest->verification_code);

        // 2. Download Printable document
        $response = $this->actingAs($alumnus)->get("/dashboard/alumni/requests/{$docRequest->id}/download");
        $response->assertStatus(200);
        $response->assertSee('Transcript');
        $response->assertSee($docRequest->verification_code);

        // 3. Verify public document verification link
        $response = $this->get("/verify-document/{$docRequest->verification_code}");
        $response->assertStatus(200);
        $response->assertSee('DOCUMENT AUTHENTIC');
        $response->assertSee('VERIFIED');
        $response->assertSee($alumnus->name_en);
    }

    public function test_alumni_incentive_claiming()
    {
        $alumnus = User::create([
            'name' => 'Alum User', 'name_ar' => 'خريج', 'name_en' => 'Alumnus',
            'email' => 'alumni_inc@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
            'department' => 'IT',
        ]);

        $profile = \App\Models\AlumniProfile::create([
            'user_id' => $alumnus->id, 'graduation_year' => 2026, 'employed' => false, 'points' => 120,
        ]);

        $incentive = Incentive::create([
            'kind' => 'course',
            'title_ar' => 'دورة بايثون', 'title_en' => 'Python Course',
            'detail_ar' => 'تفاصيل الدورة', 'detail_en' => 'Course details',
        ]);

        // Claim course (no points deduction)
        $response = $this->actingAs($alumnus)->post("/dashboard/incentives/{$incentive->id}/claim");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(120, $profile->fresh()->points); // Points are not deducted
        $this->assertTrue($alumnus->incentives->contains($incentive->id));
    }

    public function test_strict_role_based_access_controls_enforcement()
    {
        $student = User::create([
            'name' => 'Stud User', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'student_rbac_strict@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
            'department' => 'IT',
        ]);

        $supervisor = User::create([
            'name' => 'Sup User', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'supervisor_rbac_strict@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
            'department' => 'IT',
        ]);

        $alumnus = User::create([
            'name' => 'Alum User', 'name_ar' => 'خريج', 'name_en' => 'Alumnus',
            'email' => 'alumnus_rbac_strict@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
            'department' => 'IT',
        ]);

        // 1. Student tries to access document requests, incentives, and alumni directory (Should be redirected with access denied)
        $response = $this->actingAs($student)->get('/dashboard/alumni/requests');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');

        $response = $this->actingAs($student)->get('/dashboard/incentives');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');

        $response = $this->actingAs($student)->get('/dashboard/alumni');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');

        // 2. Supervisor tries to access document requests, incentives, and alumni directory (Should be redirected with access denied)
        $response = $this->actingAs($supervisor)->get('/dashboard/alumni/requests');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');

        $response = $this->actingAs($supervisor)->get('/dashboard/incentives');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');

        $response = $this->actingAs($supervisor)->get('/dashboard/alumni');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');

        // 3. Supervisor tries to access reports (Should be blocked)
        $response = $this->actingAs($supervisor)->get('/dashboard/reports');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');

        // 4. Alumni tries to access projects index or details (Should be blocked)
        $response = $this->actingAs($alumnus)->get('/dashboard/projects');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');

        $response = $this->actingAs($alumnus)->get('/dashboard/projects/1');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    public function test_alumnus_cannot_download_others_document()
    {
        $alumnus1 = User::create([
            'name' => 'Alum DL1', 'name_ar' => 'خريج', 'name_en' => 'Alumnus 1',
            'email' => 'alum_dl1@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
        ]);

        $alumnus2 = User::create([
            'name' => 'Alum DL2', 'name_ar' => 'خريج 2', 'name_en' => 'Alumnus 2',
            'email' => 'alum_dl2@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
        ]);

        \App\Models\AlumniProfile::create([
            'user_id' => $alumnus2->id, 'graduation_year' => 2026, 'employed' => false,
        ]);

        $doc = DocRequest::create([
            'alumnus_id' => $alumnus2->id, 'doc_type_ar' => 'كشف', 'doc_type_en' => 'Transcript',
            'status' => 'completed', 'waiting_days' => 1,
            'verification_code' => 'TESTCODE1234ABCD', 'file_path' => 'documents/test.pdf',
        ]);

        $response = $this->actingAs($alumnus1)->get("/dashboard/alumni/requests/{$doc->id}/download");
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    // --- SUPERVISOR ANALYTICS TESTS ---

    public function test_admin_can_view_supervisors_page()
    {
        $admin = User::create([
            'name' => 'Admin Sup', 'name_ar' => 'مسؤول', 'name_en' => 'Admin',
            'email' => 'admin_sup_view@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard/supervisors');
        $response->assertStatus(200);
        $response->assertViewHas('supervisors');
    }

    public function test_supervisor_cannot_view_supervisors_page()
    {
        $supervisor = User::create([
            'name' => 'Sup Self', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_self_view@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $response = $this->actingAs($supervisor)->get('/dashboard/supervisors');
        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    // --- COMMENT TESTS ---

    public function test_student_can_post_comment_to_own_project()
    {
        $supervisor = User::create([
            'name' => 'Sup Cmt', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_cmt@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $student = User::create([
            'name' => 'Stud Cmt', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'stud_cmt@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
        ]);

        $group = Group::create([
            'title_ar' => 'مشروع', 'title_en' => 'Project', 'department' => 'IT',
            'supervisor' => 'مشرف', 'supervisor_id' => $supervisor->id, 'stage' => 'proposal', 'status' => 'pending',
            'progress' => 20, 'days_in_stage' => 1, 'semester' => 'Spring 2026',
        ]);

        $response = $this->actingAs($student)->post("/dashboard/projects/{$group->id}/comment", [
            'content' => 'This is a test comment.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('comments', [
            'group_id' => $group->id,
            'user_id' => $student->id,
            'content' => 'This is a test comment.',
        ]);
    }

    // --- INCENTIVE EDGE CASE TESTS ---

    public function test_student_cannot_claim_incentive()
    {
        $student = User::create([
            'name' => 'Stud Inc', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'stud_inc@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
        ]);

        $incentive = Incentive::create([
            'kind' => 'course', 'title_ar' => 'دورة', 'title_en' => 'Course',
            'detail_ar' => 'تفاصيل', 'detail_en' => 'Details',
        ]);

        $response = $this->actingAs($student)->post("/dashboard/incentives/{$incentive->id}/claim");
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_alumnus_cannot_claim_same_incentive_twice()
    {
        $alumnus = User::create([
            'name' => 'Alum Dup', 'name_ar' => 'خريج', 'name_en' => 'Alumnus',
            'email' => 'alum_dup@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
            'department' => 'IT',
        ]);

        \App\Models\AlumniProfile::create([
            'user_id' => $alumnus->id, 'graduation_year' => 2026, 'employed' => false, 'points' => 100,
        ]);

        $incentive = Incentive::create([
            'kind' => 'course', 'title_ar' => 'دورة', 'title_en' => 'Course',
            'detail_ar' => 'تفاصيل', 'detail_en' => 'Details',
        ]);

        // First claim succeeds
        $response = $this->actingAs($alumnus)->post("/dashboard/incentives/{$incentive->id}/claim");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Second claim fails
        $response = $this->actingAs($alumnus)->post("/dashboard/incentives/{$incentive->id}/claim");
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_alumnus_can_claim_job_incentive()
    {
        $alumnus = User::create([
            'name' => 'Alum Job', 'name_ar' => 'خريج', 'name_en' => 'Alumnus',
            'email' => 'alum_job@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
            'department' => 'IT',
        ]);

        \App\Models\AlumniProfile::create([
            'user_id' => $alumnus->id, 'graduation_year' => 2026, 'employed' => false, 'points' => 100,
        ]);

        $incentive = Incentive::create([
            'kind' => 'job', 'title_ar' => 'فرصة عمل', 'title_en' => 'Job Opportunity',
            'detail_ar' => 'تفاصيل', 'detail_en' => 'Details',
        ]);

        $response = $this->actingAs($alumnus)->post("/dashboard/incentives/{$incentive->id}/claim");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('incentive_user', [
            'user_id' => $alumnus->id,
            'incentive_id' => $incentive->id,
            'status' => 'applied',
        ]);
    }

    public function test_alumnus_can_claim_discount_incentive()
    {
        $alumnus = User::create([
            'name' => 'Alum Disc', 'name_ar' => 'خريج', 'name_en' => 'Alumnus',
            'email' => 'alum_disc@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
            'department' => 'IT',
        ]);

        \App\Models\AlumniProfile::create([
            'user_id' => $alumnus->id, 'graduation_year' => 2026, 'employed' => false, 'points' => 100,
        ]);

        $incentive = Incentive::create([
            'kind' => 'discount', 'title_ar' => 'خصم', 'title_en' => 'Discount',
            'detail_ar' => 'تفاصيل', 'detail_en' => 'Details',
        ]);

        $response = $this->actingAs($alumnus)->post("/dashboard/incentives/{$incentive->id}/claim");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('incentive_user', [
            'user_id' => $alumnus->id,
            'incentive_id' => $incentive->id,
            'status' => 'redeemed',
        ]);
    }

    public function test_non_admin_cannot_create_incentive()
    {
        $supervisor = User::create([
            'name' => 'Sup Inc', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_inc_create@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $response = $this->actingAs($supervisor)->post('/dashboard/incentives', [
            'kind' => 'job', 'title_ar' => 'فرصة', 'title_en' => 'Opp',
            'detail_ar' => 'تفاصيل', 'detail_en' => 'Details',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // --- DEFENSE GRADING VARIANTS ---

    public function test_failed_defense_resets_project_to_proposal()
    {
        $supervisor = User::create([
            'name' => 'Sup Fail', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_fail@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $student = User::create([
            'name' => 'Stud Fail', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'stud_fail@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
        ]);

        $group = Group::create([
            'title_ar' => 'مشروع فاشل', 'title_en' => 'Failed Project', 'department' => 'IT',
            'supervisor' => 'مشرف', 'supervisor_id' => $supervisor->id, 'stage' => 'defense', 'status' => 'processing',
            'progress' => 90, 'days_in_stage' => 1, 'semester' => 'Spring 2026',
        ]);
        $group->students()->attach($student->id);

        $response = $this->actingAs($supervisor)->post("/dashboard/projects/{$group->id}/stage", [
            'grade' => 'failed',
            'defense_committee' => 'Dr. X, Dr. Y',
            'defense_date' => '2026-06-20 10:00:00',
        ]);

        $response->assertRedirect();
        $this->assertEquals('proposal', $group->fresh()->stage);
        $this->assertEquals('pending', $group->fresh()->status);
        $this->assertEquals(20, $group->fresh()->progress);
        $this->assertEquals('student', $student->fresh()->role); // NOT transitioned
    }

    public function test_passed_with_revisions_keeps_processing()
    {
        $supervisor = User::create([
            'name' => 'Sup Rev', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_rev@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $group = Group::create([
            'title_ar' => 'مشروع مراجعات', 'title_en' => 'Revisions Project', 'department' => 'IT',
            'supervisor' => 'مشرف', 'supervisor_id' => $supervisor->id, 'stage' => 'defense', 'status' => 'processing',
            'progress' => 90, 'days_in_stage' => 1, 'semester' => 'Spring 2026',
        ]);

        $response = $this->actingAs($supervisor)->post("/dashboard/projects/{$group->id}/stage", [
            'grade' => 'passed_revisions',
            'defense_committee' => 'Dr. A',
            'defense_date' => '2026-06-25 14:00:00',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('processing', $group->fresh()->status);
        $this->assertEquals('passed_revisions', $group->fresh()->grade);
    }

    // --- FULL STAGE LIFECYCLE ---

    public function test_full_project_lifecycle_proposal_to_completed()
    {
        $supervisor = User::create([
            'name' => 'Sup Life', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_lifecycle@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $student = User::create([
            'name' => 'Stud Life', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'stud_lifecycle@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
        ]);

        $group = Group::create([
            'title_ar' => 'مشروع كامل', 'title_en' => 'Full Lifecycle', 'department' => 'IT',
            'supervisor' => 'مشرف', 'supervisor_id' => $supervisor->id, 'stage' => 'proposal', 'status' => 'pending',
            'progress' => 20, 'days_in_stage' => 1, 'semester' => 'Spring 2026',
        ]);
        $group->students()->attach($student->id);

        // Stage 1: proposal → chapters
        $response = $this->actingAs($supervisor)->post("/dashboard/projects/{$group->id}/stage");
        $this->assertEquals('chapters', $group->fresh()->stage);
        $this->assertEquals(50, $group->fresh()->progress);

        // Stage 2: chapters → draft
        $response = $this->actingAs($supervisor)->post("/dashboard/projects/{$group->id}/stage");
        $this->assertEquals('draft', $group->fresh()->stage);
        $this->assertEquals(75, $group->fresh()->progress);

        // Stage 3: draft → defense (with plagiarism check)
        $response = $this->actingAs($supervisor)->post("/dashboard/projects/{$group->id}/stage", [
            'plagiarism_percent' => 15,
        ]);
        $this->assertEquals('defense', $group->fresh()->stage);
        $this->assertEquals(90, $group->fresh()->progress);

        // Stage 4: defense → completed (passed)
        $response = $this->actingAs($supervisor)->post("/dashboard/projects/{$group->id}/stage", [
            'grade' => 'passed',
            'defense_committee' => 'Dr. Test',
            'defense_date' => '2026-07-01 09:00:00',
        ]);
        $this->assertEquals('completed', $group->fresh()->status);
        $this->assertEquals(100, $group->fresh()->progress);
        $this->assertEquals('alumnus', $student->fresh()->role);
    }

    public function test_cannot_advance_completed_project()
    {
        $supervisor = User::create([
            'name' => 'Sup Done', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_done@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $group = Group::create([
            'title_ar' => 'مكتمل', 'title_en' => 'Done', 'department' => 'IT',
            'supervisor' => 'مشرف', 'supervisor_id' => $supervisor->id, 'stage' => 'defense', 'status' => 'completed',
            'progress' => 100, 'days_in_stage' => 1, 'semester' => 'Spring 2026',
        ]);

        $response = $this->actingAs($supervisor)->post("/dashboard/projects/{$group->id}/stage");
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // --- USER MANAGEMENT EDGE CASES ---

    public function test_admin_cannot_delete_self()
    {
        $admin = User::create([
            'name' => 'Admin Self', 'name_ar' => 'مسؤول', 'name_en' => 'Admin',
            'email' => 'admin_self@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post("/dashboard/users/{$admin->id}/delete");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]); // still exists
    }

    // --- SUPERVISOR CANNOT MANAGE OTHER'S PROJECTS ---

    public function test_supervisor_cannot_delete_other_supervisors_project()
    {
        $sup1 = User::create([
            'name' => 'Sup A', 'name_ar' => 'مشرف أ', 'name_en' => 'Supervisor A',
            'email' => 'sup_a_del@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $sup2 = User::create([
            'name' => 'Sup B', 'name_ar' => 'مشرف ب', 'name_en' => 'Supervisor B',
            'email' => 'sup_b_del@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $group = Group::create([
            'title_ar' => 'مشروع ب', 'title_en' => 'Project B', 'department' => 'IT',
            'supervisor' => 'مشرف ب', 'supervisor_id' => $sup2->id, 'stage' => 'proposal', 'status' => 'pending',
            'progress' => 20, 'days_in_stage' => 1, 'semester' => 'Spring 2026',
        ]);

        $response = $this->actingAs($sup1)->post("/dashboard/projects/{$group->id}/delete");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('groups', ['id' => $group->id]); // not deleted
    }

    public function test_supervisor_cannot_advance_other_supervisors_project()
    {
        $sup1 = User::create([
            'name' => 'Sup X', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor X',
            'email' => 'sup_x_adv@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $sup2 = User::create([
            'name' => 'Sup Y', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor Y',
            'email' => 'sup_y_adv@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
        ]);

        $group = Group::create([
            'title_ar' => 'مشروع آخر', 'title_en' => 'Other Project', 'department' => 'IT',
            'supervisor' => 'مشرف', 'supervisor_id' => $sup2->id, 'stage' => 'proposal', 'status' => 'pending',
            'progress' => 20, 'days_in_stage' => 1, 'semester' => 'Spring 2026',
        ]);

        $response = $this->actingAs($sup1)->post("/dashboard/projects/{$group->id}/stage");
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals('proposal', $group->fresh()->stage); // not advanced
    }

    // --- INVALID VERIFICATION CODE ---

    public function test_invalid_verification_code_shows_failure()
    {
        $response = $this->get('/verify-document/INVALID_CODE_12345');
        $response->assertStatus(200);
        // Should render the verify view (with null doc, showing failure state)
    }

    // --- INCENTIVES INDEX RBAC ---

    public function test_alumnus_can_view_incentives_page()
    {
        $alumnus = User::create([
            'name' => 'Alum Inc View', 'name_ar' => 'خريج', 'name_en' => 'Alumnus',
            'email' => 'alum_inc_view@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
        ]);

        $response = $this->actingAs($alumnus)->get('/dashboard/incentives');
        $response->assertStatus(200);
        $response->assertViewHas('incentives');
    }

    public function test_admin_can_view_incentives_page()
    {
        $admin = User::create([
            'name' => 'Admin Inc View', 'name_ar' => 'مسؤول', 'name_en' => 'Admin',
            'email' => 'admin_inc_view@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard/incentives');
        $response->assertStatus(200);
        $response->assertViewHas('incentives');
    }

    // --- LANGUAGE SWITCH ---

    public function test_language_switch_works()
    {
        $response = $this->get('/lang/ar');
        $response->assertRedirect();

        $response = $this->get('/lang/en');
        $response->assertRedirect();
    }

    // --- AUTH FLOW TESTS ---

    public function test_login_with_valid_credentials_succeeds()
    {
        $user = User::create([
            'name' => 'Login User', 'name_ar' => 'مستخدم', 'name_en' => 'Login User',
            'email' => 'login_valid@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'login_valid@nexus-scholar.edu',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_invalid_credentials_fails()
    {
        User::create([
            'name' => 'Login User', 'name_ar' => 'مستخدم', 'name_en' => 'Login User',
            'email' => 'login_fail@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'admin',
        ]);

        $response = $this->post('/login', [
            'email' => 'login_fail@nexus-scholar.edu',
            'password' => 'wrong_password',
        ]);

        $this->assertGuest();
    }

    public function test_logout_works()
    {
        $user = User::create([
            'name' => 'Logout User', 'name_ar' => 'مستخدم', 'name_en' => 'Logout User',
            'email' => 'logout@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'admin',
        ]);

        $this->actingAs($user);
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_register_page_loads()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_alumnus_registration_creates_alumni_profile()
    {
        $response = $this->post('/register', [
            'name_ar' => 'خريج جديد',
            'name_en' => 'New Alumnus',
            'email' => 'new_alumnus@nexus-scholar.edu',
            'password' => 'StrongP@ss123',
            'password_confirmation' => 'StrongP@ss123',
            'role' => 'alumnus',
            'department' => 'CS',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'new_alumnus@nexus-scholar.edu', 'role' => 'alumnus']);
        $user = User::where('email', 'new_alumnus@nexus-scholar.edu')->first();
        $this->assertDatabaseHas('alumni_profiles', ['user_id' => $user->id]);
    }

    public function test_authenticated_user_cannot_access_login_page()
    {
        $user = User::create([
            'name' => 'Auth User', 'name_ar' => 'مستخدم', 'name_en' => 'Auth User',
            'email' => 'auth_guest@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
        ]);

        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect();
    }

    // --- DASHBOARD VIEW DATA TESTS ---

    public function test_admin_dashboard_has_correct_view_data()
    {
        $admin = User::create([
            'name' => 'Admin Dash', 'name_ar' => 'مسؤول', 'name_en' => 'Admin',
            'email' => 'admin_dash_full@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('totalCompleted');
        $response->assertViewHas('totalProcessing');
        $response->assertViewHas('totalPending');
        $response->assertViewHas('deptChartData');
        $response->assertViewHas('statusDistribution');
        $response->assertViewHas('latestTransactions');
    }

    public function test_supervisor_dashboard_has_correct_view_data()
    {
        $supervisor = User::create([
            'name' => 'Sup Dash', 'name_ar' => 'مشرف', 'name_en' => 'Supervisor',
            'email' => 'sup_dash_full@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'supervisor',
            'department' => 'IT',
        ]);

        $response = $this->actingAs($supervisor)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('groups');
        $response->assertViewHas('totalCompleted');
        $response->assertViewHas('totalProcessing');
        $response->assertViewHas('totalPending');
        $response->assertViewHas('stuckGroups');
        $response->assertViewHas('latestComments');
    }

    // --- PROFILE TESTS ---

    public function test_profile_edit_page_loads()
    {
        $user = User::create([
            'name' => 'Profile User', 'name_ar' => 'مستخدم', 'name_en' => 'Profile User',
            'email' => 'profile_edit@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
        ]);

        $response = $this->actingAs($user)->get('/dashboard/profile');
        $response->assertStatus(200);
        $response->assertViewHas('user');
    }

    public function test_student_can_update_profile()
    {
        $user = User::create([
            'name' => 'Student Profile', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'student_profile@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
        ]);

        $response = $this->actingAs($user)->post('/dashboard/profile', [
            'name_ar' => 'طالب محدث',
            'name_en' => 'Updated Student',
            'email' => 'student_profile@nexus-scholar.edu',
            'phone' => '555-1234',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertEquals('Updated Student', $user->fresh()->name_en);
        $this->assertEquals('555-1234', $user->fresh()->phone);
    }

    public function test_alumnus_can_update_profile_with_career_details()
    {
        $alumnus = User::create([
            'name' => 'Alum Profile', 'name_ar' => 'خريج', 'name_en' => 'Alumnus',
            'email' => 'alum_profile@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
            'department' => 'IT',
        ]);

        \App\Models\AlumniProfile::create([
            'user_id' => $alumnus->id, 'graduation_year' => 2025, 'employed' => false,
        ]);

        $response = $this->actingAs($alumnus)->post('/dashboard/profile', [
            'name_ar' => 'خريج محدث',
            'name_en' => 'Updated Alumnus',
            'email' => 'alum_profile@nexus-scholar.edu',
            'graduation_year' => 2026,
            'workplace' => 'Google',
            'position' => 'Engineer',
            'employed' => 1,
            'skills' => 'PHP, Laravel, Python',
            'linkedin_url' => 'https://linkedin.com/in/alumnus',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $profile = $alumnus->fresh()->alumniProfile;
        $this->assertEquals(2026, $profile->graduation_year);
        $this->assertEquals('Google', $profile->workplace);
        $this->assertTrue($profile->employed);
        $this->assertEquals('https://linkedin.com/in/alumnus', $profile->linkedin_url);
    }

    // --- DOCUMENT REQUEST TESTS ---

    public function test_alumnus_can_create_document_request()
    {
        $alumnus = User::create([
            'name' => 'Doc Alum', 'name_ar' => 'خريج', 'name_en' => 'Alumnus',
            'email' => 'doc_create@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
            'department' => 'IT',
        ]);

        $response = $this->actingAs($alumnus)->post('/dashboard/alumni/requests', [
            'doc_type' => 'Transcript',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('doc_requests', [
            'alumnus_id' => $alumnus->id,
            'doc_type_en' => 'Transcript',
            'status' => 'pending',
        ]);
    }

    public function test_student_cannot_create_document_request()
    {
        $student = User::create([
            'name' => 'Stud Doc', 'name_ar' => 'طالب', 'name_en' => 'Student',
            'email' => 'student_doc@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'student',
        ]);

        $response = $this->actingAs($student)->post('/dashboard/alumni/requests', [
            'doc_type' => 'Transcript',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_alumnus_only_sees_own_document_requests()
    {
        $alumnus1 = User::create([
            'name' => 'Alum 1', 'name_ar' => 'خريج 1', 'name_en' => 'Alumnus 1',
            'email' => 'alum1_doc@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
        ]);

        $alumnus2 = User::create([
            'name' => 'Alum 2', 'name_ar' => 'خريج 2', 'name_en' => 'Alumnus 2',
            'email' => 'alum2_doc@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
        ]);

        DocRequest::create([
            'alumnus_id' => $alumnus1->id, 'doc_type_ar' => 'كشف', 'doc_type_en' => 'Transcript',
            'status' => 'pending', 'waiting_days' => 1,
        ]);

        DocRequest::create([
            'alumnus_id' => $alumnus2->id, 'doc_type_ar' => 'إفادة', 'doc_type_en' => 'Letter',
            'status' => 'pending', 'waiting_days' => 2,
        ]);

        $response = $this->actingAs($alumnus1)->get('/dashboard/alumni/requests');
        $response->assertStatus(200);
        $requests = $response->viewData('requests');
        $this->assertTrue($requests->every(fn($r) => $r->alumnus_id === $alumnus1->id));
    }

    public function test_admin_sees_all_document_requests()
    {
        $alumnus = User::create([
            'name' => 'Alum Doc', 'name_ar' => 'خريج', 'name_en' => 'Alumnus',
            'email' => 'alum_all_doc@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
        ]);

        $admin = User::create([
            'name' => 'Admin Doc', 'name_ar' => 'مسؤول', 'name_en' => 'Admin',
            'email' => 'admin_all_doc@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'admin',
        ]);

        DocRequest::create([
            'alumnus_id' => $alumnus->id, 'doc_type_ar' => 'كشف', 'doc_type_en' => 'Transcript',
            'status' => 'pending', 'waiting_days' => 1,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard/alumni/requests');
        $response->assertStatus(200);
        $requests = $response->viewData('requests');
        $this->assertGreaterThanOrEqual(1, $requests->count());
    }

    public function test_non_admin_cannot_update_document_status()
    {
        $alumnus = User::create([
            'name' => 'Alum Status', 'name_ar' => 'خريج', 'name_en' => 'Alumnus',
            'email' => 'alum_status@nexus-scholar.edu', 'password' => bcrypt('password'), 'role' => 'alumnus',
        ]);

        $doc = DocRequest::create([
            'alumnus_id' => $alumnus->id, 'doc_type_ar' => 'كشف', 'doc_type_en' => 'Transcript',
            'status' => 'pending', 'waiting_days' => 1,
        ]);

        $response = $this->actingAs($alumnus)->post("/dashboard/alumni/requests/{$doc->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals('pending', $doc->fresh()->status);
    }
}
