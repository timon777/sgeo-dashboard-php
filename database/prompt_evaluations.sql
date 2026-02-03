-- =============================================
-- Таблица оценок промптов (prompt_evaluations)
-- =============================================

-- Создание таблицы
CREATE TABLE IF NOT EXISTS prompt_evaluations (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    project_id UUID REFERENCES projects(id) ON DELETE CASCADE,
    prompt_text TEXT NOT NULL,
    neutrality INTEGER NOT NULL DEFAULT 0 CHECK (neutrality >= 0 AND neutrality <= 100),
    functional_stability INTEGER NOT NULL DEFAULT 0 CHECK (functional_stability >= 0 AND functional_stability <= 100),
    logical_soundness INTEGER NOT NULL DEFAULT 0 CHECK (logical_soundness >= 0 AND logical_soundness <= 100),
    anti_hallucination INTEGER NOT NULL DEFAULT 0 CHECK (anti_hallucination >= 0 AND anti_hallucination <= 100),
    avg_score INTEGER GENERATED ALWAYS AS ((neutrality + functional_stability + logical_soundness + anti_hallucination) / 4) STORED,
    language VARCHAR(10) DEFAULT 'ru',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Индексы для быстрого поиска
CREATE INDEX IF NOT EXISTS idx_prompt_evaluations_project_id ON prompt_evaluations(project_id);
CREATE INDEX IF NOT EXISTS idx_prompt_evaluations_avg_score ON prompt_evaluations(avg_score DESC);

-- RLS политики (если нужно)
ALTER TABLE prompt_evaluations ENABLE ROW LEVEL SECURITY;

CREATE POLICY "Enable read access for all users" ON prompt_evaluations
    FOR SELECT USING (true);

CREATE POLICY "Enable insert for authenticated users" ON prompt_evaluations
    FOR INSERT WITH CHECK (true);

CREATE POLICY "Enable update for authenticated users" ON prompt_evaluations
    FOR UPDATE USING (true);


-- =============================================
-- View для агрегированных данных по проекту
-- =============================================

CREATE OR REPLACE VIEW prompt_evaluations_summary AS
SELECT
    project_id,
    COUNT(*) as total_prompts,
    ROUND(AVG(neutrality), 1) as avg_neutrality,
    ROUND(AVG(functional_stability), 1) as avg_stability,
    ROUND(AVG(logical_soundness), 1) as avg_soundness,
    ROUND(AVG(anti_hallucination), 1) as avg_anti_hallucination,
    ROUND(AVG(avg_score), 1) as overall_avg_score,
    COUNT(CASE WHEN language = 'ru' THEN 1 END) as ru_count,
    COUNT(CASE WHEN language = 'kz' THEN 1 END) as kz_count,
    COUNT(CASE WHEN language = 'en' THEN 1 END) as en_count
FROM prompt_evaluations
GROUP BY project_id;

-- =============================================
-- Проверка данных
-- =============================================

-- SELECT * FROM prompt_evaluations ORDER BY created_at DESC LIMIT 10;
-- SELECT * FROM prompt_evaluations_summary;
