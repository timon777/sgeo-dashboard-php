-- =====================================================
-- Add project filtering for users
-- =====================================================

-- Add project_filter field to users table
ALTER TABLE users ADD COLUMN IF NOT EXISTS project_filter VARCHAR(50) DEFAULT 'all';
ALTER TABLE users ADD COLUMN IF NOT EXISTS login VARCHAR(100);

-- Add unique constraint on login
CREATE UNIQUE INDEX IF NOT EXISTS users_login_idx ON users(login) WHERE login IS NOT NULL;

-- Update existing users to have login field (if needed)
UPDATE users SET login = email WHERE login IS NULL;

-- Create 'gos' user (can see only government projects)
INSERT INTO users (email, login, password_hash, name, role, project_filter, avatar_initials, is_active)
VALUES (
    'gos@reportview.kz',
    'gos',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Гос. партнёр',
    'user',
    'gov',
    'ГП',
    true
)
ON CONFLICT (email) DO UPDATE SET
    login = EXCLUDED.login,
    project_filter = EXCLUDED.project_filter,
    name = EXCLUDED.name;

-- Create 'bank' user (can see only Freedom Bank private projects)
INSERT INTO users (email, login, password_hash, name, role, project_filter, avatar_initials, is_active)
VALUES (
    'bank@reportview.kz',
    'bank',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Freedom Bank',
    'user',
    'private:freedom',
    'FB',
    true
)
ON CONFLICT (email) DO UPDATE SET
    login = EXCLUDED.login,
    project_filter = EXCLUDED.project_filter,
    name = EXCLUDED.name;

-- Update existing admin user to see all projects
UPDATE users
SET project_filter = 'all', login = COALESCE(login, email)
WHERE role = 'admin' AND project_filter IS NULL;

COMMENT ON COLUMN users.project_filter IS 'Project filter: "all" (all projects), "gov" (government only), "private:partner_name" (specific partner)';
