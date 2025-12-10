# Hospital Appointment System - Database Schema

## Overview

This document outlines the complete database schema for the MediCare Hospital Management System. The schema is designed to support a multi-role appointment booking system with admin, doctor, frontdesk, and patient portals.

## Entity Relationship Diagram (ERD) Description

```
users (1) ----< (many) appointments
users (1) ----< (many) prescriptions
users (1) ----< (many) doctor_schedules
users (1) ----< (many) notifications

appointments (many) >---- (1) users (as doctor)
appointments (many) >---- (1) users (as patient)
appointments (1) ----< (many) prescriptions

doctor_schedules (many) >---- (1) users (doctor)

specialties (1) ----< (many) users (doctors)
```

---

## Tables

### 1. users

**Purpose**: Central table for all system users (admin, doctor, frontdesk, patient)

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| role | ENUM | NOT NULL | User role: 'admin', 'doctor', 'frontdesk', 'patient' |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Email address for login |
| password | VARCHAR(255) | NOT NULL | Hashed password |
| first_name | VARCHAR(100) | NOT NULL | User's first name |
| last_name | VARCHAR(100) | NOT NULL | User's last name |
| phone | VARCHAR(20) | NULLABLE | Contact phone number |
| date_of_birth | DATE | NULLABLE | Date of birth (mainly for patients) |
| gender | ENUM | NULLABLE | 'male', 'female', 'other' |
| address | TEXT | NULLABLE | Full address |
| profile_image | VARCHAR(255) | NULLABLE | Path to profile image |
| status | ENUM | DEFAULT 'active' | 'active', 'inactive', 'suspended' |
| email_verified_at | TIMESTAMP | NULLABLE | Email verification timestamp |
| remember_token | VARCHAR(100) | NULLABLE | Laravel remember token |
| created_at | TIMESTAMP | NULL | Record creation time |
| updated_at | TIMESTAMP | NULL | Last update time |
| deleted_at | TIMESTAMP | NULL | Soft delete timestamp |

**Indexes**:
- PRIMARY KEY: id
- UNIQUE INDEX: email
- INDEX: role
- INDEX: status

---

### 2. doctor_profiles

**Purpose**: Extended profile information specific to doctors

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| user_id | BIGINT UNSIGNED | FOREIGN KEY, UNIQUE, NOT NULL | References users.id |
| specialty_id | BIGINT UNSIGNED | FOREIGN KEY, NULLABLE | References specialties.id |
| qualification | VARCHAR(255) | NULLABLE | Medical degrees (e.g., MBBS, MD) |
| experience_years | SMALLINT UNSIGNED | DEFAULT 0 | Years of experience |
| consultation_fee | DECIMAL(10,2) | DEFAULT 0.00 | Consultation fee in currency |
| bio | TEXT | NULLABLE | Doctor biography |
| license_number | VARCHAR(50) | NULLABLE | Medical license number |
| available_for_booking | BOOLEAN | DEFAULT true | Whether accepting new appointments |
| created_at | TIMESTAMP | NULL | Record creation time |
| updated_at | TIMESTAMP | NULL | Last update time |

**Indexes**:
- PRIMARY KEY: id
- UNIQUE INDEX: user_id
- FOREIGN KEY: user_id REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY: specialty_id REFERENCES specialties(id) ON DELETE SET NULL
- INDEX: specialty_id

---

### 3. patient_profiles

**Purpose**: Extended profile information specific to patients

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| user_id | BIGINT UNSIGNED | FOREIGN KEY, UNIQUE, NOT NULL | References users.id |
| emergency_contact_name | VARCHAR(100) | NULLABLE | Emergency contact person |
| emergency_contact_phone | VARCHAR(20) | NULLABLE | Emergency contact number |
| blood_group | VARCHAR(5) | NULLABLE | Blood type (A+, B-, O+, etc.) |
| allergies | TEXT | NULLABLE | Known allergies (comma-separated) |
| medical_history | TEXT | NULLABLE | Past medical conditions |
| current_medications | TEXT | NULLABLE | Ongoing medications |
| insurance_provider | VARCHAR(100) | NULLABLE | Health insurance company |
| insurance_number | VARCHAR(50) | NULLABLE | Insurance policy number |
| created_at | TIMESTAMP | NULL | Record creation time |
| updated_at | TIMESTAMP | NULL | Last update time |

**Indexes**:
- PRIMARY KEY: id
- UNIQUE INDEX: user_id
- FOREIGN KEY: user_id REFERENCES users(id) ON DELETE CASCADE

---

### 4. specialties

**Purpose**: Medical specialties/departments

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| name | VARCHAR(100) | UNIQUE, NOT NULL | Specialty name (e.g., Cardiology) |
| description | TEXT | NULLABLE | Specialty description |
| icon | VARCHAR(50) | NULLABLE | Icon identifier for UI |
| status | ENUM | DEFAULT 'active' | 'active', 'inactive' |
| created_at | TIMESTAMP | NULL | Record creation time |
| updated_at | TIMESTAMP | NULL | Last update time |

**Indexes**:
- PRIMARY KEY: id
- UNIQUE INDEX: name
- INDEX: status

**Sample Data**:
- Cardiology (Heart and cardiovascular system)
- Pediatrics (Children's health)
- Orthopedics (Bones and joints)
- Dermatology (Skin conditions)
- Neurology (Nervous system)

---

### 5. appointments

**Purpose**: Core appointment booking records

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| appointment_number | VARCHAR(20) | UNIQUE, NOT NULL | Human-readable ID (e.g., APT-2024-0001) |
| patient_id | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL | References users.id (patient) |
| doctor_id | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL | References users.id (doctor) |
| appointment_date | DATE | NOT NULL | Scheduled date |
| appointment_time | TIME | NOT NULL | Scheduled time |
| duration_minutes | SMALLINT UNSIGNED | DEFAULT 30 | Appointment duration |
| status | ENUM | DEFAULT 'pending' | 'pending', 'confirmed', 'checked_in', 'in_progress', 'completed', 'cancelled', 'no_show' |
| appointment_type | ENUM | DEFAULT 'consultation' | 'consultation', 'follow_up', 'emergency', 'check_up' |
| reason_for_visit | TEXT | NULLABLE | Patient's reason for appointment |
| symptoms | TEXT | NULLABLE | Current symptoms |
| notes | TEXT | NULLABLE | Additional notes from patient |
| cancellation_reason | TEXT | NULLABLE | Reason if cancelled |
| booked_by | BIGINT UNSIGNED | FOREIGN KEY, NULLABLE | References users.id (who created it) |
| booked_via | ENUM | DEFAULT 'online' | 'online', 'frontdesk', 'phone', 'admin' |
| reminder_sent | BOOLEAN | DEFAULT false | Whether reminder was sent |
| created_at | TIMESTAMP | NULL | Record creation time |
| updated_at | TIMESTAMP | NULL | Last update time |
| deleted_at | TIMESTAMP | NULL | Soft delete timestamp |

**Indexes**:
- PRIMARY KEY: id
- UNIQUE INDEX: appointment_number
- FOREIGN KEY: patient_id REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY: doctor_id REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY: booked_by REFERENCES users(id) ON DELETE SET NULL
- INDEX: appointment_date, appointment_time
- INDEX: status
- INDEX: patient_id, appointment_date
- INDEX: doctor_id, appointment_date

---

### 6. doctor_schedules

**Purpose**: Doctor availability and working hours

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| doctor_id | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL | References users.id (doctor) |
| day_of_week | TINYINT UNSIGNED | NOT NULL | 0=Sunday, 1=Monday, ... 6=Saturday |
| start_time | TIME | NOT NULL | Shift start time |
| end_time | TIME | NOT NULL | Shift end time |
| slot_duration | SMALLINT UNSIGNED | DEFAULT 30 | Minutes per appointment slot |
| max_patients | SMALLINT UNSIGNED | DEFAULT 20 | Maximum patients per day |
| is_available | BOOLEAN | DEFAULT true | Whether accepting appointments |
| notes | TEXT | NULLABLE | Special notes for the day |
| created_at | TIMESTAMP | NULL | Record creation time |
| updated_at | TIMESTAMP | NULL | Last update time |

**Indexes**:
- PRIMARY KEY: id
- FOREIGN KEY: doctor_id REFERENCES users(id) ON DELETE CASCADE
- UNIQUE INDEX: doctor_id, day_of_week (prevent duplicate schedules)
- INDEX: doctor_id, is_available

**Sample Data**:
- Dr. Sharma: Monday-Friday, 9:00 AM - 5:00 PM, 30-min slots
- Dr. Mehta: Monday-Saturday, 10:00 AM - 6:00 PM, 30-min slots

---

### 7. doctor_schedule_exceptions

**Purpose**: Special dates when doctor is unavailable or has different hours

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| doctor_id | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL | References users.id (doctor) |
| exception_date | DATE | NOT NULL | Specific date |
| is_available | BOOLEAN | DEFAULT false | Available or not on this date |
| start_time | TIME | NULLABLE | Alternative start time if available |
| end_time | TIME | NULLABLE | Alternative end time if available |
| reason | VARCHAR(255) | NULLABLE | Reason for exception (vacation, conference) |
| created_at | TIMESTAMP | NULL | Record creation time |
| updated_at | TIMESTAMP | NULL | Last update time |

**Indexes**:
- PRIMARY KEY: id
- FOREIGN KEY: doctor_id REFERENCES users(id) ON DELETE CASCADE
- UNIQUE INDEX: doctor_id, exception_date
- INDEX: exception_date

---

### 8. prescriptions

**Purpose**: Medical prescriptions issued by doctors

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| prescription_number | VARCHAR(20) | UNIQUE, NOT NULL | Human-readable ID (e.g., RX-2024-0001) |
| appointment_id | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL | References appointments.id |
| patient_id | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL | References users.id (patient) |
| doctor_id | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL | References users.id (doctor) |
| diagnosis | TEXT | NULLABLE | Medical diagnosis |
| medications | JSON | NULLABLE | Array of medications with dosage |
| instructions | TEXT | NULLABLE | General instructions |
| follow_up_date | DATE | NULLABLE | Next appointment date |
| notes | TEXT | NULLABLE | Additional notes |
| created_at | TIMESTAMP | NULL | Record creation time |
| updated_at | TIMESTAMP | NULL | Last update time |

**Indexes**:
- PRIMARY KEY: id
- UNIQUE INDEX: prescription_number
- FOREIGN KEY: appointment_id REFERENCES appointments(id) ON DELETE CASCADE
- FOREIGN KEY: patient_id REFERENCES users(id) ON DELETE CASCADE
- FOREIGN KEY: doctor_id REFERENCES users(id) ON DELETE CASCADE
- INDEX: patient_id
- INDEX: doctor_id

**Sample Medications JSON Structure**:
```json
[
  {
    "name": "Amoxicillin",
    "dosage": "500mg",
    "frequency": "3 times daily",
    "duration": "7 days",
    "instructions": "Take with food"
  }
]
```

---

### 9. notifications

**Purpose**: System notifications and reminders

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| user_id | BIGINT UNSIGNED | FOREIGN KEY, NOT NULL | References users.id (recipient) |
| type | ENUM | NOT NULL | 'appointment_reminder', 'appointment_confirmed', 'appointment_cancelled', 'prescription_ready', 'system_alert' |
| title | VARCHAR(255) | NOT NULL | Notification title |
| message | TEXT | NOT NULL | Notification message |
| related_type | VARCHAR(50) | NULLABLE | Related model type (Appointment, Prescription) |
| related_id | BIGINT UNSIGNED | NULLABLE | Related model ID |
| channel | ENUM | DEFAULT 'database' | 'database', 'email', 'sms', 'push' |
| is_read | BOOLEAN | DEFAULT false | Read status |
| sent_at | TIMESTAMP | NULLABLE | When notification was sent |
| read_at | TIMESTAMP | NULLABLE | When notification was read |
| created_at | TIMESTAMP | NULL | Record creation time |
| updated_at | TIMESTAMP | NULL | Last update time |

**Indexes**:
- PRIMARY KEY: id
- FOREIGN KEY: user_id REFERENCES users(id) ON DELETE CASCADE
- INDEX: user_id, is_read
- INDEX: type
- INDEX: created_at

---

### 10. audit_logs

**Purpose**: Track system activities for security and compliance

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| user_id | BIGINT UNSIGNED | FOREIGN KEY, NULLABLE | References users.id (who performed action) |
| action | VARCHAR(100) | NOT NULL | Action type (created, updated, deleted, login) |
| model_type | VARCHAR(100) | NULLABLE | Affected model (Appointment, User) |
| model_id | BIGINT UNSIGNED | NULLABLE | Affected model ID |
| old_values | JSON | NULLABLE | Previous values |
| new_values | JSON | NULLABLE | New values |
| ip_address | VARCHAR(45) | NULLABLE | User's IP address |
| user_agent | TEXT | NULLABLE | Browser user agent |
| created_at | TIMESTAMP | NULL | Record creation time |

**Indexes**:
- PRIMARY KEY: id
- FOREIGN KEY: user_id REFERENCES users(id) ON DELETE SET NULL
- INDEX: user_id
- INDEX: action
- INDEX: model_type, model_id
- INDEX: created_at

---

### 11. settings

**Purpose**: System-wide configuration settings

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT | Unique identifier |
| key | VARCHAR(100) | UNIQUE, NOT NULL | Setting key (e.g., 'site_name') |
| value | TEXT | NULLABLE | Setting value |
| type | ENUM | DEFAULT 'string' | 'string', 'integer', 'boolean', 'json' |
| group | VARCHAR(50) | DEFAULT 'general' | Setting group for organization |
| description | TEXT | NULLABLE | Description of setting |
| created_at | TIMESTAMP | NULL | Record creation time |
| updated_at | TIMESTAMP | NULL | Last update time |

**Indexes**:
- PRIMARY KEY: id
- UNIQUE INDEX: key
- INDEX: group

**Sample Settings**:
- hospital_name: "MediCare Hospital"
- default_appointment_duration: 30
- appointment_reminder_hours: 24
- max_advance_booking_days: 30
- timezone: "Asia/Kolkata"

---

### 12. password_reset_tokens

**Purpose**: Laravel password reset functionality

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| email | VARCHAR(255) | PRIMARY KEY | User email |
| token | VARCHAR(255) | NOT NULL | Reset token |
| created_at | TIMESTAMP | NULL | Token creation time |

**Indexes**:
- PRIMARY KEY: email
- INDEX: token

---

### 13. sessions

**Purpose**: Laravel session storage

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | VARCHAR(255) | PRIMARY KEY | Session ID |
| user_id | BIGINT UNSIGNED | NULLABLE | References users.id |
| ip_address | VARCHAR(45) | NULLABLE | User IP |
| user_agent | TEXT | NULLABLE | Browser user agent |
| payload | LONGTEXT | NOT NULL | Session data |
| last_activity | INTEGER | NOT NULL | Last activity timestamp |

**Indexes**:
- PRIMARY KEY: id
- INDEX: user_id
- INDEX: last_activity

---

## Relationships Summary

### One-to-Many Relationships

1. **users → doctor_profiles** (1:1)
   - A user with role 'doctor' has one doctor profile

2. **users → patient_profiles** (1:1)
   - A user with role 'patient' has one patient profile

3. **specialties → doctor_profiles** (1:Many)
   - A specialty can have many doctors

4. **users (patient) → appointments** (1:Many)
   - A patient can have many appointments

5. **users (doctor) → appointments** (1:Many)
   - A doctor can have many appointments

6. **users (doctor) → doctor_schedules** (1:Many)
   - A doctor can have multiple schedule entries (one per day)

7. **users (doctor) → doctor_schedule_exceptions** (1:Many)
   - A doctor can have multiple exception dates

8. **appointments → prescriptions** (1:Many)
   - An appointment can have multiple prescriptions

9. **users → notifications** (1:Many)
   - A user can have many notifications

10. **users → audit_logs** (1:Many)
    - A user can have many audit log entries

---

## Data Integrity Rules

### Cascade Delete
- When a user is deleted, cascade delete: appointments, doctor_schedules, prescriptions, notifications
- When an appointment is deleted, cascade delete: prescriptions

### Set Null
- When a specialty is deleted, set doctor_profiles.specialty_id to NULL
- When a booking user is deleted, set appointments.booked_by to NULL

### Soft Deletes
- users: Allow recovery of deleted accounts
- appointments: Maintain history of cancelled appointments

---

## Performance Optimization

### Recommended Indexes

1. **users table**:
   - email (UNIQUE) - Fast login lookups
   - role - Filter by user role
   - status - Filter active users

2. **appointments table**:
   - (doctor_id, appointment_date) - Doctor's daily schedule
   - (patient_id, appointment_date) - Patient's appointment history
   - appointment_number (UNIQUE) - Quick lookup
   - status - Filter by appointment status

3. **doctor_schedules table**:
   - (doctor_id, day_of_week) (UNIQUE) - Prevent duplicate schedules
   - doctor_id - List all schedules for a doctor

4. **notifications table**:
   - (user_id, is_read) - Unread notifications
   - created_at - Recent notifications

5. **audit_logs table**:
   - (model_type, model_id) - Track changes to specific records
   - user_id - User activity history
   - created_at - Recent activities

---

## Data Migration Strategy

### Phase 1: Core Tables
1. users
2. specialties
3. doctor_profiles
4. patient_profiles

### Phase 2: Scheduling
5. doctor_schedules
6. doctor_schedule_exceptions

### Phase 3: Appointments
7. appointments
8. prescriptions

### Phase 4: Supporting Tables
9. notifications
10. audit_logs
11. settings
12. password_reset_tokens
13. sessions

### Seeding Order
1. specialties (seed first - required for doctors)
2. users (admin, sample doctors, frontdesk)
3. doctor_profiles (link to users)
4. doctor_schedules (default schedules)
5. settings (system configuration)

---

## Security Considerations

### Sensitive Data
- **passwords**: Hashed using bcrypt (Laravel default)
- **remember_token**: Encrypted token for "remember me" functionality
- **insurance_number**: Consider encryption at rest
- **medical_history**: HIPAA/data protection compliance required

### Access Control
- **Role-based access**: Enforce at application level
- **Patient data**: Only accessible by assigned doctor and admin
- **Audit logging**: Track all access to sensitive data
