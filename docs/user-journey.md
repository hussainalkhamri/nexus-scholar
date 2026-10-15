# User Journeys & End-to-End Walkthrough

This document outlines the core end-to-end user journeys demonstrated within **Nexus Scholar**.

---

## Journey 1: The Graduation Candidate Experience
1. **Login**: Student logs in using their academic ID (`441001`) or email (`student@nexus-scholar.edu`).
2. **Cohort Workspace**:
   - The student views their project dashboard (e.g. `GP-1001: نظام أرشفة الملفات الطبية الذكي`).
   - Tracks current phase on the visual 4-step progress stepper.
3. **Collaboration & Iteration**:
   - The student uploads updated drafts (PDF / Word) in the attachments card.
   - Posts comments and notes directly to the supervisor in the revision thread.
4. **Final Defense & Elevation**:
   - Once the examination committee records a passing grade, the student's status automatically upgrades to `alumnus`.

---

## Journey 2: The Academic Supervisor (Faculty Mentor)
1. **Overview**: Faculty member logs in (`supervisor@nexus-scholar.edu`) and inspects supervised cohorts.
2. **Review & Guidance**:
   - Reviews uploaded chapters and adds guidance comments.
   - Evaluates scientific integrity: Inputs the Turnitin plagiarism percentage.
   - If plagiarism is $\le 20\%$, the button to advance to Defense is unlocked.
3. **Examination & Grading**:
   - Convenes the defense committee and records the final grade (`passed`, `passed_revisions`, or `failed`).
   - Passing immediately synthesizes the graduate profiles.

---

## Journey 3: The Graduate & Credential Verification
1. **Document Request**:
   - Alumnus (`alumni@nexus-scholar.edu`) navigates to `/dashboard/alumni/requests`.
   - Submits request for Graduation Certificate or Academic Transcript.
2. **Issuance & Public Verification**:
   - Department Officer reviews and approves the request.
   - System mints a unique verification code (`VERIFY-NXOBJVTPY1KE`).
   - Any external employer or institution visits `/verify-document/{code}` (no authentication required).
   - The system displays a verified green shield, confirmed student identity, degree, and graduation year.
