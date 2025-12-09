-- =====================================================
-- SGEO Dashboard - Supabase Database Schema
-- Run this in Supabase SQL Editor
-- =====================================================

-- Enable UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- =====================================================
-- DROP EXISTING VIEWS (if any)
-- =====================================================
DROP VIEW IF EXISTS recent_evaluations_detailed;
DROP VIEW IF EXISTS model_performance_summary;

-- =====================================================
-- PROJECTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS projects (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(10) DEFAULT '📊',
    type VARCHAR(50) DEFAULT 'gov' CHECK (type IN ('gov', 'private')),
    badge VARCHAR(100) DEFAULT 'Гос. партнёр',
    accuracy_score DECIMAL(5,2) DEFAULT 0,
    trend_direction VARCHAR(10) DEFAULT 'up' CHECK (trend_direction IN ('up', 'down')),
    trend_percent DECIMAL(5,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- =====================================================
-- PROJECT NARRATIVES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS project_narratives (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    project_id UUID REFERENCES projects(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    order_index INTEGER DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- =====================================================
-- PROJECT STATS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS project_stats (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    project_id UUID REFERENCES projects(id) ON DELETE CASCADE,
    processed_prompts INTEGER DEFAULT 0,
    unique_sources INTEGER DEFAULT 0,
    avg_tone DECIMAL(5,2) DEFAULT 0,
    llm_models INTEGER DEFAULT 0,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(project_id)
);

-- =====================================================
-- SOURCES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS sources (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    domain VARCHAR(255) NOT NULL UNIQUE,
    type VARCHAR(50) DEFAULT 'media' CHECK (type IN ('gov', 'media', 'analytics', 'wiki', 'social', 'news')),
    country VARCHAR(100) DEFAULT 'Казахстан',
    expertise_score INTEGER DEFAULT 0 CHECK (expertise_score >= 0 AND expertise_score <= 100),
    experience_score INTEGER DEFAULT 0 CHECK (experience_score >= 0 AND experience_score <= 100),
    authority_score INTEGER DEFAULT 0 CHECK (authority_score >= 0 AND authority_score <= 100),
    trust_score INTEGER DEFAULT 0 CHECK (trust_score >= 0 AND trust_score <= 100),
    eeat_combined INTEGER DEFAULT 0 CHECK (eeat_combined >= 0 AND eeat_combined <= 100),
    share_percent DECIMAL(5,2) DEFAULT 0,
    has_author BOOLEAN DEFAULT false,
    has_https BOOLEAN DEFAULT true,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- =====================================================
-- PROJECT_SOURCES TABLE (Many-to-Many linking)
-- =====================================================
CREATE TABLE IF NOT EXISTS project_sources (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    project_id UUID NOT NULL REFERENCES projects(id) ON DELETE CASCADE,
    source_id UUID NOT NULL REFERENCES sources(id) ON DELETE CASCADE,
    usage_count INTEGER DEFAULT 1,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    UNIQUE(project_id, source_id)
);

-- =====================================================
-- PROMPT SETS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS prompt_sets (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    project_id UUID REFERENCES projects(id) ON DELETE SET NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- =====================================================
-- AI RESPONSES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS ai_responses (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    prompt TEXT NOT NULL,
    response TEXT,
    model_name VARCHAR(100) NOT NULL,
    language VARCHAR(10) DEFAULT 'ru',
    project_id UUID REFERENCES projects(id) ON DELETE SET NULL,
    prompt_set_id UUID REFERENCES prompt_sets(id) ON DELETE SET NULL,
    tone VARCHAR(20) DEFAULT 'neutral' CHECK (tone IN ('positive', 'neutral', 'negative')),
    risk_level VARCHAR(20) DEFAULT 'low' CHECK (risk_level IN ('low', 'medium', 'high')),
    response_time_ms INTEGER DEFAULT 0,
    tokens_used INTEGER DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- =====================================================
-- EVALUATIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS evaluations (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    ai_response_id UUID REFERENCES ai_responses(id) ON DELETE CASCADE,
    evaluator_model VARCHAR(100),
    coherence_score DECIMAL(5,2) DEFAULT 0,
    consistency_score DECIMAL(5,2) DEFAULT 0,
    fluency_score DECIMAL(5,2) DEFAULT 0,
    relevance_score DECIMAL(5,2) DEFAULT 0,
    avg_score DECIMAL(5,2) DEFAULT 0,
    feedback TEXT,
    evaluated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- =====================================================
-- LLM MODELS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS llm_models (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(100) NOT NULL UNIQUE,
    short_name VARCHAR(50) NOT NULL,
    icon VARCHAR(10) DEFAULT '🤖',
    color VARCHAR(20) DEFAULT '#6366f1',
    is_active BOOLEAN DEFAULT true,
    api_endpoint VARCHAR(500),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- =====================================================
-- USERS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(255),
    role VARCHAR(50) DEFAULT 'user' CHECK (role IN ('admin', 'manager', 'user')),
    avatar_initials VARCHAR(10),
    is_active BOOLEAN DEFAULT true,
    last_login TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- =====================================================
-- USER SESSIONS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS user_sessions (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id UUID REFERENCES users(id) ON DELETE CASCADE,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP WITH TIME ZONE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- =====================================================
-- REPORTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS reports (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    title VARCHAR(255) NOT NULL,
    description TEXT,
    type VARCHAR(50) DEFAULT 'weekly' CHECK (type IN ('daily', 'weekly', 'monthly', 'custom')),
    project_id UUID REFERENCES projects(id) ON DELETE SET NULL,
    data JSONB,
    generated_by UUID REFERENCES users(id) ON DELETE SET NULL,
    file_path VARCHAR(500),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- =====================================================
-- SETTINGS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS settings (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    key VARCHAR(100) NOT NULL UNIQUE,
    value TEXT,
    type VARCHAR(50) DEFAULT 'string',
    description TEXT,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- =====================================================
-- INDEXES (create after all tables)
-- =====================================================
CREATE INDEX IF NOT EXISTS idx_ai_responses_model ON ai_responses(model_name);
CREATE INDEX IF NOT EXISTS idx_ai_responses_project ON ai_responses(project_id);
CREATE INDEX IF NOT EXISTS idx_ai_responses_created ON ai_responses(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_evaluations_response ON evaluations(ai_response_id);
CREATE INDEX IF NOT EXISTS idx_evaluations_date ON evaluations(evaluated_at DESC);
CREATE INDEX IF NOT EXISTS idx_sources_domain ON sources(domain);
CREATE INDEX IF NOT EXISTS idx_sources_type ON sources(type);
CREATE INDEX IF NOT EXISTS idx_projects_active ON projects(is_active);
CREATE INDEX IF NOT EXISTS idx_user_sessions_token ON user_sessions(token);
CREATE INDEX IF NOT EXISTS idx_user_sessions_expires ON user_sessions(expires_at);
CREATE INDEX IF NOT EXISTS idx_project_sources_project ON project_sources(project_id);
CREATE INDEX IF NOT EXISTS idx_project_sources_source ON project_sources(source_id);

-- =====================================================
-- VIEWS (create after all tables exist)
-- =====================================================

-- Model Performance Summary View
CREATE OR REPLACE VIEW model_performance_summary AS
SELECT
    ar.model_name,
    COUNT(DISTINCT e.id) as total_evaluations,
    COALESCE(ROUND(AVG(e.coherence_score)::numeric, 2), 0) as avg_coherence,
    COALESCE(ROUND(AVG(e.consistency_score)::numeric, 2), 0) as avg_consistency,
    COALESCE(ROUND(AVG(e.fluency_score)::numeric, 2), 0) as avg_fluency,
    COALESCE(ROUND(AVG(e.relevance_score)::numeric, 2), 0) as avg_relevance,
    COALESCE(ROUND(AVG(e.avg_score)::numeric, 2), 0) as overall_avg_score,
    COUNT(DISTINCT ar.id) as total_responses,
    COALESCE(ROUND(AVG(ar.response_time_ms)::numeric, 0), 0) as avg_response_time
FROM ai_responses ar
LEFT JOIN evaluations e ON ar.id = e.ai_response_id
GROUP BY ar.model_name
ORDER BY overall_avg_score DESC NULLS LAST;

-- Recent Evaluations Detailed View
CREATE OR REPLACE VIEW recent_evaluations_detailed AS
SELECT
    e.id,
    e.ai_response_id,
    ar.prompt,
    ar.response,
    ar.model_name,
    ar.tone,
    ar.risk_level,
    ar.project_id,
    e.coherence_score,
    e.consistency_score,
    e.fluency_score,
    e.relevance_score,
    e.avg_score,
    e.evaluator_model,
    e.feedback,
    e.evaluated_at,
    p.name as project_name
FROM evaluations e
JOIN ai_responses ar ON e.ai_response_id = ar.id
LEFT JOIN projects p ON ar.project_id = p.id
ORDER BY e.evaluated_at DESC;

-- =====================================================
-- SAMPLE DATA - Projects
-- =====================================================
INSERT INTO projects (name, description, icon, type, badge, accuracy_score, trend_direction, trend_percent) VALUES
('Имидж Президента Токаева', 'Позиционирование реформатора и независимого лидера. Аналитика нарративов о лидерском стиле, модернизации и политической самостоятельности.', '🏛️', 'gov', 'Гос. партнёр', 78, 'up', 10),
('Январские события 2022 года', 'Переосмысление событий и их последствий. Изучение тональности: от трактовки как террористической атаки до акцента на политические реформы.', '📅', 'gov', 'Гос. партнёр', 72, 'up', 5),
('Идеология, закон и порядок', 'Нарратив сильного и справедливого государства. Мониторинг сообщений о верховенстве закона, институтах и укреплении общественного доверия.', '⚖️', 'gov', 'Гос. партнёр', 81, 'up', 8),
('Цифровой Казахстан', 'Развитие GovTech, инноваций и цифровой экосистемы. Отслеживание интереса к Astana Hub, МЦРИАП и трансформации госсектора.', '💻', 'gov', 'Гос. партнёр', 85, 'up', 12),
('АЭС и ядерная энергетика', 'Безопасность, экология и будущее атомной энергетики. Анализ общественных нарративов о строительстве АЭС и геополитических аспектах.', '⚛️', 'gov', 'Гос. партнёр', 69, 'down', 3),
('Freedom Bank', 'Имидж, технологии и клиентский опыт банка. Мониторинг пользовательских запросов, тональности и обсуждений цифровых сервисов.', '🏦', 'private', 'Частный партнёр', 82, 'up', 7),
('Freedom Broker', 'Инвестиции, торговля и доверие инвесторов. Аналитика тем вокруг брокерских услуг, прозрачности, комиссий и пользовательского опыта.', '📈', 'private', 'Частный партнёр', 76, 'up', 4)
ON CONFLICT DO NOTHING;

-- =====================================================
-- SAMPLE DATA - LLM Models
-- =====================================================
INSERT INTO llm_models (name, short_name, icon, color) VALUES
('ChatGPT', 'gpt', '🤖', '#10a37f'),
('DeepSeek', 'deepseek', '🔍', '#4d6bfe'),
('Grok', 'grok', '⚡', '#1d9bf0'),
('Gemini', 'gemini', '💎', '#4285f4'),
('Perplexity', 'perplexity', '🌐', '#8b5cf6'),
('Claude', 'claude', '🎭', '#d97706'),
('Copilot', 'copilot', '✈️', '#f59e0b')
ON CONFLICT DO NOTHING;

-- =====================================================
-- SAMPLE DATA - Sources
-- =====================================================
INSERT INTO sources (domain, type, country, expertise_score, experience_score, authority_score, trust_score, eeat_combined, share_percent, has_author, has_https) VALUES
('akorda.kz', 'gov', 'Казахстан', 95, 90, 98, 95, 95, 15.2, true, true),
('gov.kz', 'gov', 'Казахстан', 92, 88, 95, 92, 92, 12.8, true, true),
('inform.kz', 'media', 'Казахстан', 78, 82, 75, 80, 79, 8.5, true, true),
('tengrinews.kz', 'media', 'Казахстан', 85, 88, 82, 83, 85, 11.3, true, true),
('forbes.kz', 'analytics', 'Казахстан', 88, 85, 86, 84, 86, 6.7, true, true),
('kapital.kz', 'analytics', 'Казахстан', 82, 80, 78, 79, 80, 5.4, true, true),
('wikipedia.org', 'wiki', 'Международный', 75, 95, 85, 70, 81, 18.5, false, true),
('reuters.com', 'news', 'Международный', 92, 95, 94, 90, 93, 4.2, true, true)
ON CONFLICT DO NOTHING;

-- =====================================================
-- SAMPLE DATA - Default Admin User
-- Password: password (change in production!)
-- =====================================================
INSERT INTO users (email, password_hash, name, role, avatar_initials) VALUES
('admin@sgeo.kz', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Администратор', 'admin', 'АК')
ON CONFLICT DO NOTHING;

-- =====================================================
-- SAMPLE DATA - Settings
-- =====================================================
INSERT INTO settings (key, value, type, description) VALUES
('site_name', 'SGEO Dashboard', 'string', 'Название сайта'),
('default_language', 'ru', 'string', 'Язык по умолчанию'),
('items_per_page', '20', 'integer', 'Количество элементов на странице'),
('enable_notifications', 'true', 'boolean', 'Включить уведомления'),
('api_rate_limit', '100', 'integer', 'Лимит API запросов в минуту')
ON CONFLICT DO NOTHING;

-- =====================================================
-- SAMPLE DATA - AI Responses (for testing)
-- =====================================================
INSERT INTO ai_responses (prompt, response, model_name, tone, risk_level, response_time_ms) VALUES
('Расскажите о реформах президента Токаева', 'Касым-Жомарт Токаев провёл масштабные политические и экономические реформы с 2019 года...', 'ChatGPT', 'positive', 'low', 1200),
('Что произошло в Казахстане в январе 2022?', 'В январе 2022 года в Казахстане произошли массовые протесты, начавшиеся с повышения цен на газ...', 'Gemini', 'neutral', 'medium', 1500),
('Каковы перспективы цифровизации в Казахстане?', 'Казахстан активно развивает цифровую экономику через программу "Цифровой Казахстан"...', 'DeepSeek', 'positive', 'low', 800),
('Расскажите о Freedom Bank', 'Freedom Bank - один из ведущих цифровых банков Казахстана с инновационным мобильным приложением...', 'Grok', 'positive', 'low', 1100),
('Как оценивается строительство АЭС?', 'Вопрос строительства атомной электростанции остается дискуссионным в казахстанском обществе...', 'Perplexity', 'neutral', 'high', 2100)
ON CONFLICT DO NOTHING;

-- Add evaluations for sample responses
INSERT INTO evaluations (ai_response_id, evaluator_model, coherence_score, consistency_score, fluency_score, relevance_score, avg_score)
SELECT id, 'GPT-4', 85, 88, 90, 82, 86.25 FROM ai_responses WHERE model_name = 'ChatGPT' LIMIT 1
ON CONFLICT DO NOTHING;

INSERT INTO evaluations (ai_response_id, evaluator_model, coherence_score, consistency_score, fluency_score, relevance_score, avg_score)
SELECT id, 'GPT-4', 78, 80, 85, 75, 79.5 FROM ai_responses WHERE model_name = 'Gemini' LIMIT 1
ON CONFLICT DO NOTHING;

INSERT INTO evaluations (ai_response_id, evaluator_model, coherence_score, consistency_score, fluency_score, relevance_score, avg_score)
SELECT id, 'GPT-4', 88, 90, 92, 85, 88.75 FROM ai_responses WHERE model_name = 'DeepSeek' LIMIT 1
ON CONFLICT DO NOTHING;
