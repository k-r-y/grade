-- Additional migration to fix full_name issue
-- This allows full_name to be NULL during registration (Steps 1-2)
-- and filled in later during profile completion (Step 3)

USE kld_grading_system;

-- Modify the full_name column to allow NULL values
ALTER TABLE users MODIFY COLUMN full_name VARCHAR(100) DEFAULT NULL;

-- Update any existing empty string values to NULL
UPDATE users SET full_name = NULL WHERE full_name = '';
