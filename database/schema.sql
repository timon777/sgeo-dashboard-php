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
-- DAILY_STATS TABLE (for trend calculation)
-- =====================================================
CREATE TABLE IF NOT EXISTS daily_stats (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    stat_date DATE NOT NULL UNIQUE,
    total_projects INTEGER DEFAULT 0,
    total_prompts INTEGER DEFAULT 0,
    total_sources INTEGER DEFAULT 0,
    avg_accuracy DECIMAL(5,2) DEFAULT 0,
    total_responses INTEGER DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
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
CREATE INDEX IF NOT EXISTS idx_daily_stats_date ON daily_stats(stat_date DESC);

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

