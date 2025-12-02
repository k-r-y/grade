-- Migration to fix duplicate school_id issue
-- This allows school_id to be NULL during registration (Steps 1-2)
-- and filled in later during profile completion (Step 3)

USE kld_grading_system;

-- Modify the school_id column to allow NULL values
ALTER TABLE users MODIFY COLUMN school_id VARCHAR(50) DEFAULT NULL UNIQUE;

-- Update any existing empty string values to NULL
UPDATE users SET school_id = NULL WHERE school_id = '';
