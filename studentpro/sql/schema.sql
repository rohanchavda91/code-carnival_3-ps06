-- =============================================================================
-- SYNAPSE AI - Database Schema
-- Adaptive Learning Platform Database
-- MySQL 8.0+ / MariaDB 10.5+
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- Database Creation
-- -----------------------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `synapse_ai` 
  DEFAULT CHARACTER SET utf8mb4 
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `synapse_ai`;

-- -----------------------------------------------------------------------------
-- Table: users (Authentication & Core Identity)
-- -----------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` CHAR(36) NOT NULL COMMENT 'Public UUID for API references',
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL COMMENT 'Argon2id hash',
  `name` VARCHAR(150) NOT NULL,
  `avatar_url` VARCHAR(500) DEFAULT NULL,
  `is_demo` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Demo/guest accounts',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `last_login_at` TIMESTAMP NULL DEFAULT NULL,
  `login_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_uuid` (`uuid`),
  UNIQUE KEY `uk_users_email` (`email`),
  KEY `idx_users_active` (`is_active`),
  KEY `idx_users_demo` (`is_demo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: learner_profiles (Extended Learning Profile)
-- -----------------------------------------------------------------------------
CREATE TABLE `learner_profiles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  -- Onboarding / Pathway
  `interface_language` VARCHAR(10) NOT NULL DEFAULT 'en',
  `learner_type` ENUM('school','college','skills') DEFAULT NULL,
  `standard` TINYINT UNSIGNED DEFAULT NULL COMMENT '1-12 for school',
  `stream` ENUM('science','commerce','arts') DEFAULT NULL COMMENT 'For standards 11-12',
  `course_id` VARCHAR(100) DEFAULT NULL COMMENT 'College course ID',
  `course_name` VARCHAR(200) DEFAULT NULL,
  `skill_id` VARCHAR(100) DEFAULT NULL COMMENT 'Skills catalog ID',
  `skill_name` VARCHAR(200) DEFAULT NULL,
  -- Subject & Topics
  `selected_subject_id` VARCHAR(100) DEFAULT NULL,
  `selected_subject_name` VARCHAR(200) DEFAULT NULL,
  `selected_topics` JSON DEFAULT NULL COMMENT 'Array of topic IDs',
  -- Learning Preferences
  `learning_goals` JSON DEFAULT NULL COMMENT 'Array of goal keys',
  `learning_style` ENUM('step_by_step','concept_first','practice_heavy','visual') NOT NULL DEFAULT 'step_by_step',
  `daily_time` ENUM('15m','30m','45m','60m','90m') NOT NULL DEFAULT '30m',
  -- Gamification
  `xp_points` INT UNSIGNED NOT NULL DEFAULT 0,
  `streak_days` INT UNSIGNED NOT NULL DEFAULT 0,
  `last_activity_date` DATE DEFAULT NULL,
  `user_level` INT UNSIGNED NOT NULL DEFAULT 1,
  `badges` JSON DEFAULT NULL COMMENT 'Array of badge keys',
  -- Assessment
  `assessment_completed` TINYINT(1) NOT NULL DEFAULT 0,
  `assessment_answers` JSON DEFAULT NULL,
  `assessment_result` JSON DEFAULT NULL COMMENT 'Full assessment result object',
  -- Progress Tracking
  `completed_topics` JSON DEFAULT NULL COMMENT 'Array of completed topic titles',
  `in_progress_topics` JSON DEFAULT NULL COMMENT 'Array of in-progress topic objects',
  `last_active_topic` VARCHAR(200) DEFAULT NULL,
  `quiz_history` JSON DEFAULT NULL COMMENT 'Array of quiz result objects',
  -- Notes & Mistakes
  `saved_notes` JSON DEFAULT NULL COMMENT 'Array of note objects',
  `mistakes_log` JSON DEFAULT NULL COMMENT 'Array of mistake objects',
  -- AI Configuration
  `ai_config` JSON DEFAULT NULL COMMENT 'AI provider settings',
  -- Timestamps
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_profiles_user` (`user_id`),
  KEY `idx_profiles_learner_type` (`learner_type`),
  KEY `idx_profiles_standard` (`standard`),
  CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: auth_sessions (JWT/Token Sessions)
-- -----------------------------------------------------------------------------
CREATE TABLE `auth_sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL COMMENT 'SHA-256 of the JWT/token',
  `refresh_token_hash` CHAR(64) DEFAULT NULL COMMENT 'SHA-256 of refresh token',
  `user_agent` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `refresh_expires_at` TIMESTAMP DEFAULT NULL,
  `revoked_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_sessions_token` (`token_hash`),
  KEY `idx_sessions_user` (`user_id`),
  KEY `idx_sessions_expires` (`expires_at`),
  KEY `idx_sessions_refresh` (`refresh_token_hash`),
  CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: assessment_sessions (Diagnostic & Level Assessments)
-- -----------------------------------------------------------------------------
CREATE TABLE `assessment_sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `session_type` ENUM('diagnostic','level_check','topic_quiz') NOT NULL DEFAULT 'diagnostic',
  `subject_id` VARCHAR(100) DEFAULT NULL,
  `subject_name` VARCHAR(200) DEFAULT NULL,
  `questions` JSON NOT NULL COMMENT 'Snapshot of questions asked',
  `answers` JSON NOT NULL COMMENT 'User answers with timing',
  `score` INT UNSIGNED NOT NULL DEFAULT 0,
  `total_questions` INT UNSIGNED NOT NULL DEFAULT 0,
  `percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `level` ENUM('beginner','intermediate','advanced','expert') DEFAULT NULL,
  `calibrated_difficulty` VARCHAR(100) DEFAULT NULL,
  `strong_areas` JSON DEFAULT NULL,
  `focus_areas` JSON DEFAULT NULL,
  `topic_breakdown` JSON DEFAULT NULL,
  `generated_roadmap` JSON DEFAULT NULL COMMENT 'Personalized learning roadmap',
  `recommended_starting_topic` VARCHAR(200) DEFAULT NULL,
  `time_spent_seconds` INT UNSIGNED DEFAULT NULL,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_assessments_user` (`user_id`),
  KEY `idx_assessments_type` (`session_type`),
  KEY `idx_assessments_subject` (`subject_id`),
  KEY `idx_assessments_completed` (`completed_at`),
  CONSTRAINT `fk_assessments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: quiz_sessions (Practice Quizzes)
-- -----------------------------------------------------------------------------
CREATE TABLE `quiz_sessions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `quiz_type` ENUM('practice','generated','mistake_review') NOT NULL DEFAULT 'practice',
  `subject_id` VARCHAR(100) DEFAULT NULL,
  `subject_name` VARCHAR(200) DEFAULT NULL,
  `topic_ids` JSON DEFAULT NULL COMMENT 'Topics covered in this quiz',
  `questions` JSON NOT NULL,
  `answers` JSON NOT NULL,
  `score` INT UNSIGNED NOT NULL DEFAULT 0,
  `total_questions` INT UNSIGNED NOT NULL DEFAULT 0,
  `percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `time_spent_seconds` INT UNSIGNED DEFAULT NULL,
  `xp_earned` INT UNSIGNED NOT NULL DEFAULT 0,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_quiz_user` (`user_id`),
  KEY `idx_quiz_type` (`quiz_type`),
  KEY `idx_quiz_subject` (`subject_id`),
  KEY `idx_quiz_completed` (`completed_at`),
  CONSTRAINT `fk_quiz_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: ai_conversations (AI Mentor Chat History)
-- -----------------------------------------------------------------------------
CREATE TABLE `ai_conversations` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `session_id` VARCHAR(100) NOT NULL COMMENT 'Groups messages into a conversation',
  `messages` JSON NOT NULL COMMENT 'Full conversation array',
  `message_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `model_used` VARCHAR(100) DEFAULT NULL,
  `provider` VARCHAR(50) DEFAULT NULL,
  `total_tokens` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ai_session` (`user_id`, `session_id`),
  KEY `idx_ai_user` (`user_id`),
  KEY `idx_ai_updated` (`updated_at`),
  CONSTRAINT `fk_ai_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: user_notes (Notes Maker - Separate for querying)
-- -----------------------------------------------------------------------------
CREATE TABLE `user_notes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `note_id` VARCHAR(100) NOT NULL COMMENT 'Client-side ID for sync',
  `title` VARCHAR(300) NOT NULL,
  `content` LONGTEXT NOT NULL,
  `tags` JSON DEFAULT NULL COMMENT 'Array of tag strings',
  `subject_id` VARCHAR(100) DEFAULT NULL,
  `subject_name` VARCHAR(200) DEFAULT NULL,
  `topic` VARCHAR(200) DEFAULT NULL,
  `is_ai_generated` TINYINT(1) NOT NULL DEFAULT 0,
  `word_count` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_notes_user_client` (`user_id`, `note_id`),
  KEY `idx_notes_user` (`user_id`),
  KEY `idx_notes_subject` (`subject_id`),
  KEY `idx_notes_updated` (`updated_at`),
  FULLTEXT KEY `ft_notes_content` (`title`, `content`),
  CONSTRAINT `fk_notes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: user_mistakes (Mistake Mastery - Separate for querying)
-- -----------------------------------------------------------------------------
CREATE TABLE `user_mistakes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `mistake_id` VARCHAR(100) NOT NULL COMMENT 'Client-side ID for sync',
  `topic` VARCHAR(200) NOT NULL,
  `question` TEXT NOT NULL,
  `user_answer` TEXT NOT NULL,
  `correct_answer` TEXT NOT NULL,
  `explanation` TEXT DEFAULT NULL,
  `subject_id` VARCHAR(100) DEFAULT NULL,
  `subject_name` VARCHAR(200) DEFAULT NULL,
  `mastered` TINYINT(1) NOT NULL DEFAULT 0,
  `mastered_at` TIMESTAMP NULL DEFAULT NULL,
  `review_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `last_reviewed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_mistakes_user_client` (`user_id`, `mistake_id`),
  KEY `idx_mistakes_user` (`user_id`),
  KEY `idx_mistakes_topic` (`topic`),
  KEY `idx_mistakes_mastered` (`mastered`),
  KEY `idx_mistakes_subject` (`subject_id`),
  CONSTRAINT `fk_mistakes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: curriculum_subjects (School Subjects Reference)
-- -----------------------------------------------------------------------------
CREATE TABLE `curriculum_subjects` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject_key` VARCHAR(100) NOT NULL COMMENT 'e.g., math_s8, sci_s10',
  `standard` TINYINT UNSIGNED NOT NULL COMMENT '1-12',
  `stream` ENUM('science','commerce','arts') DEFAULT NULL,
  `name` VARCHAR(200) NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `icon` VARCHAR(10) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `topics` JSON NOT NULL COMMENT 'Array of topic strings',
  `difficulty_tier` ENUM('primary','middle','secondary','higher_secondary') NOT NULL,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_curriculum_key` (`subject_key`),
  KEY `idx_curriculum_standard` (`standard`),
  KEY `idx_curriculum_stream` (`stream`),
  KEY `idx_curriculum_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: college_courses (College Courses Reference)
-- -----------------------------------------------------------------------------
CREATE TABLE `college_courses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_key` VARCHAR(100) NOT NULL COMMENT 'e.g., bca, btech_cse, mba',
  `code` VARCHAR(50) NOT NULL,
  `name` VARCHAR(300) NOT NULL,
  `level` ENUM('ug','pg','diploma','cert') NOT NULL,
  `category` ENUM('cs_it','comm_mgmt','science','arts_humanities','professional') NOT NULL,
  `duration` VARCHAR(50) NOT NULL,
  `icon` VARCHAR(10) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `topics` JSON NOT NULL COMMENT 'Array of topic strings',
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_college_key` (`course_key`),
  KEY `idx_college_level` (`level`),
  KEY `idx_college_category` (`category`),
  KEY `idx_college_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: skills_catalog (Professional Skills Reference)
-- -----------------------------------------------------------------------------
CREATE TABLE `skills_catalog` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `skill_key` VARCHAR(100) NOT NULL COMMENT 'e.g., python_mastery, uiux_figma',
  `name` VARCHAR(300) NOT NULL,
  `category` ENUM('tech','languages','design','career','business') NOT NULL,
  `difficulty` VARCHAR(100) DEFAULT NULL,
  `badge` VARCHAR(100) DEFAULT NULL,
  `icon` VARCHAR(10) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `topics` JSON NOT NULL COMMENT 'Array of topic strings',
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_skills_key` (`skill_key`),
  KEY `idx_skills_category` (`category`),
  KEY `idx_skills_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: question_bank (Assessment & Quiz Questions)
-- -----------------------------------------------------------------------------
CREATE TABLE `question_bank` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_key` VARCHAR(150) NOT NULL COMMENT 'Unique identifier',
  `subject_id` VARCHAR(100) DEFAULT NULL COMMENT 'Links to curriculum/college/skills',
  `subject_type` ENUM('school','college','skills') NOT NULL,
  `topic` VARCHAR(200) NOT NULL,
  `difficulty` ENUM('easy','medium','hard') NOT NULL DEFAULT 'medium',
  `question_text` TEXT NOT NULL,
  `question_type` ENUM('multiple_choice','true_false','short_answer','fill_blank') NOT NULL DEFAULT 'multiple_choice',
  `options` JSON DEFAULT NULL COMMENT 'For MCQ: array of {id, text, is_correct}',
  `correct_answer` TEXT NOT NULL,
  `explanation` TEXT DEFAULT NULL,
  `tags` JSON DEFAULT NULL COMMENT 'Array of concept tags',
  `source` VARCHAR(100) DEFAULT 'curriculum' COMMENT 'curriculum, ai_generated, manual',
  `usage_count` INT UNSIGNED NOT NULL DEFAULT 0,
  `success_rate` DECIMAL(5,2) DEFAULT NULL COMMENT 'Percentage of correct answers',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_question_key` (`question_key`),
  KEY `idx_questions_subject` (`subject_id`, `subject_type`),
  KEY `idx_questions_topic` (`topic`),
  KEY `idx_questions_difficulty` (`difficulty`),
  KEY `idx_questions_active` (`is_active`),
  FULLTEXT KEY `ft_questions_text` (`question_text`, `explanation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: user_preferences (App Settings)
-- -----------------------------------------------------------------------------
CREATE TABLE `user_preferences` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `theme` ENUM('light','dark','system') NOT NULL DEFAULT 'system',
  `notifications_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `email_notifications` TINYINT(1) NOT NULL DEFAULT 0,
  `daily_reminder_time` TIME DEFAULT NULL,
  `language` VARCHAR(10) NOT NULL DEFAULT 'en',
  `ai_provider` VARCHAR(50) DEFAULT 'ollama',
  `ai_endpoint` VARCHAR(500) DEFAULT 'http://localhost:11434/api/generate',
  `ai_model` VARCHAR(100) DEFAULT 'llama3.2',
  `ai_socratic_mode` TINYINT(1) NOT NULL DEFAULT 0,
  `data_sync_enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `analytics_opt_in` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_preferences_user` (`user_id`),
  CONSTRAINT `fk_preferences_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: activity_log (Audit Trail & Analytics)
-- -----------------------------------------------------------------------------
CREATE TABLE `activity_log` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `event_type` VARCHAR(100) NOT NULL COMMENT 'login, lesson_start, quiz_complete, etc.',
  `event_data` JSON DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_activity_user` (`user_id`),
  KEY `idx_activity_type` (`event_type`),
  KEY `idx_activity_created` (`created_at`),
  CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Table: password_resets (Password Reset Tokens)
-- -----------------------------------------------------------------------------
CREATE TABLE `password_resets` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `token_hash` CHAR(64) NOT NULL COMMENT 'SHA-256 of reset token',
  `expires_at` TIMESTAMP NOT NULL,
  `used_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_resets_email` (`email`),
  KEY `idx_resets_token` (`token_hash`),
  KEY `idx_resets_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Views for Common Queries
-- -----------------------------------------------------------------------------

-- User with Profile View
CREATE OR REPLACE VIEW `v_user_profile` AS
SELECT 
  u.`id`, u.`uuid`, u.`email`, u.`name`, u.`avatar_url`, u.`is_demo`, u.`is_active`,
  u.`last_login_at`, u.`login_count`, u.`created_at` AS user_created_at,
  p.`interface_language`, p.`learner_type`, p.`standard`, p.`stream`,
  p.`course_id`, p.`course_name`, p.`skill_id`, p.`skill_name`,
  p.`selected_subject_id`, p.`selected_subject_name`, p.`selected_topics`,
  p.`learning_goals`, p.`learning_style`, p.`daily_time`,
  p.`xp_points`, p.`streak_days`, p.`last_activity_date`, p.`user_level`,
  p.`badges`, p.`assessment_completed`, p.`assessment_result`,
  p.`completed_topics`, p.`in_progress_topics`, p.`last_active_topic`,
  p.`quiz_history`, p.`saved_notes`, p.`mistakes_log`, p.`ai_config`,
  p.`updated_at` AS profile_updated_at
FROM `users` u
LEFT JOIN `learner_profiles` p ON p.`user_id` = u.`id`
WHERE u.`is_active` = 1;

-- Assessment Summary View
CREATE OR REPLACE VIEW `v_assessment_summary` AS
SELECT 
  a.`id`, a.`user_id`, a.`session_type`, a.`subject_id`, a.`subject_name`,
  a.`score`, a.`total_questions`, a.`percentage`, a.`level`,
  a.`calibrated_difficulty`, a.`strong_areas`, a.`focus_areas`,
  a.`generated_roadmap`, a.`recommended_starting_topic`,
  a.`time_spent_seconds`, a.`completed_at`, a.`created_at`
FROM `assessment_sessions` a
WHERE a.`completed_at` IS NOT NULL;

-- Quiz Summary View
CREATE OR REPLACE VIEW `v_quiz_summary` AS
SELECT 
  q.`id`, q.`user_id`, q.`quiz_type`, q.`subject_id`, q.`subject_name`,
  q.`topic_ids`, q.`score`, q.`total_questions`, q.`percentage`,
  q.`time_spent_seconds`, q.`xp_earned`, q.`completed_at`, q.`created_at`
FROM `quiz_sessions` q
WHERE q.`completed_at` IS NOT NULL;

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================================================
-- End of Schema
-- =============================================================================