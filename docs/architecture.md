# System Architecture & Technical Specifications

## 1. Architectural Overview
**Nexus Scholar** is engineered around domain-driven patterns within the Laravel MVC framework. It manages complex academic transitions, multi-tenant department categorization, and lifecycle gating.

---

## 2. Core Domain Models & Relationships

```mermaid
erDiagram
    User ||--o{ Group : "supervises (as supervisor)"
    User }o--o{ Group : "belongs to (via group_student)"
    User ||--o| AlumniProfile : "has profile (when alumnus)"
    User ||--o{ DocRequest : "submits"
    Group ||--o{ Comment : "contains discussion"
    Group ||--o{ ProjectFile : "attaches drafts"
    User ||--o{ Incentive : "claims"

    User {
        bigint id PK
        string name_ar
        string name_en
        string email
        string role
        string department
        string academic_id
    }

    Group {
        bigint id PK
        string title_ar
        string title_en
        string department
        string stage
        string status
        int progress
        int plagiarism_percent
        string grade
        datetime defense_date
    }

    AlumniProfile {
        bigint id PK
        bigint user_id FK
        int graduation_year
        string workplace
        string position
        json skills
        int points
    }

    DocRequest {
        bigint id PK
        bigint alumnus_id FK
        string doc_type_ar
        string doc_type_en
        string status
        string verification_code
    }
```

---

## 3. Milestone State Machine & Scientific Integrity Enforcer

```mermaid
stateDiagram-v2
    [*] --> Proposal : Student Team Submits
    Proposal --> Chapters : Supervisor Approves Outline
    Chapters --> Draft : Thesis Chapters Written & Reviewed
    
    state Draft {
        [*] --> PlagiarismCheck
        PlagiarismCheck --> Blocked : Plagiarism > 20%
        PlagiarismCheck --> EligibleForDefense : Plagiarism <= 20%
    }
    
    Draft --> Defense : Supervisor Approves Draft & Plagiarism <= 20%
    
    state Defense {
        [*] --> ExaminationCommittee
        ExaminationCommittee --> Passed : Approved
        ExaminationCommittee --> PassedWithRevisions : Minor Edits
        ExaminationCommittee --> Failed : Rejected
    }
    
    Passed --> Completed : Progress = 100%
    Passed --> AutoElevation : Students Promoted to Alumni
    Failed --> Proposal : Stage Resets to Start
```

---

## 4. Reporting & PDF Engine Design
The reporting subsystem generates formal academic transcripts and executive summaries in Arabic and English:
- **Challenge**: Standard PDF renderers do not support complex bidirectional text or Arabic letter connections (e.g., disconnected isolated glyphs).
- **Solution**: The `app/Helpers/helpers.php` layer employs `Khaled.Alshamaa/Ar-Php` glyph shaping algorithms via `ar_fix()`, transforming characters into their contextual medial/final/initial ligatures prior to DOMPDF HTML compilation.
- **Result**: Native PDF outputs formatted with official university crests, QR authentication tokens, and bilingual student details.
