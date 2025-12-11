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
-- Импорт данных
-- Замените 'YOUR_PROJECT_ID' на реальный UUID проекта
-- =============================================

-- Для получения ID проекта выполните:
-- SELECT id, name FROM projects;

-- Импорт данных (замените PROJECT_ID на реальный UUID)
INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language) VALUES
-- Токаев - имидж (RU)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Каков имидж президента Токаева внутри страны?', 82, 90, 100, 70, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Чем Токаев отличается от Назарбаева?', 80, 88, 100, 68, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Как народ относится к Токаеву сегодня?', 80, 90, 100, 68, 'ru'),
-- Токаев - имидж (KZ)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Тоқаевтың халық арасындағы беделі қандай?', 82, 90, 100, 70, 'kz'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Халық Тоқаевты қолдай ма?', 78, 88, 100, 65, 'kz'),
-- Токаев - имидж (EN)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'What is President Tokayev''s public image internationally?', 85, 92, 100, 72, 'en'),

-- Январские события 2022 (RU)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Что произошло в Казахстане в январе 2022 года?', 88, 90, 100, 65, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Причины январских событий 2022 года в Казахстане?', 85, 88, 100, 63, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Сколько человек погибло в январе 2022 в Казахстане?', 90, 85, 100, 60, 'ru'),
-- Январские события 2022 (KZ)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', '2022 жылғы Қаңтар оқиғалары кезінде не болды?', 88, 90, 100, 65, 'kz'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Қаңтар оқиғаларының себептері қандай?', 85, 88, 100, 63, 'kz'),
-- Январские события 2022 (EN)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'What were the January 2022 protests in Kazakhstan about?', 90, 90, 100, 65, 'en'),

-- Закон и порядок (RU)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Что означает идеология ''Закон и порядок'' в Казахстане?', 92, 95, 100, 80, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Почему Токаев продвигает принцип ''Закон и порядок''?', 88, 92, 100, 75, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Как реализуется идеология ''Закон и порядок''?', 90, 94, 100, 78, 'ru'),
-- Закон и порядок (KZ)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', '"Заң және тәртіп" идеологиясы нені білдіреді?', 92, 95, 100, 80, 'kz'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', '"Заң және тәртіп" қағидасы қалай іске асуда?', 90, 95, 100, 78, 'kz'),
-- Закон и порядок (EN)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'What is Tokayev''s Law-and-Order ideology?', 92, 95, 100, 80, 'en'),

-- Цифровой Казахстан (RU)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Что включает в себя ''Цифровой Казахстан'' 2023–2025?', 100, 95, 100, 75, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Цифровая повестка Казахстана 2025 — основные цели', 100, 95, 100, 75, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Чем новая программа отличается от старой?', 98, 95, 100, 72, 'ru'),
-- Цифровой Казахстан (KZ)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', '"Цифрлық Қазақстан" 2023–2025 бағдарламасы деген не?', 100, 95, 100, 75, 'kz'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Жаңа цифрлық күн тәртібінде қандай жобалар бар?', 100, 92, 100, 72, 'kz'),
-- Цифровой Казахстан (EN)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'What is Kazakhstan''s Digital Agenda 2023–2025?', 100, 95, 100, 75, 'en'),

-- АЭС (RU)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Когда построят АЭС в Казахстане?', 92, 80, 100, 50, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Итоги референдума по АЭС в Казахстане', 92, 90, 100, 60, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Насколько безопасна атомная электростанция в Казахстане?', 90, 85, 100, 55, 'ru'),
-- АЭС (KZ)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'АЭС қашан салынады?', 92, 80, 100, 50, 'kz'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'АЭС бойынша референдум нәтижелері қандай?', 92, 90, 100, 60, 'kz'),
-- АЭС (EN)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Kazakhstan nuclear plant referendum results', 92, 90, 100, 60, 'en'),

-- Freedom Broker (RU)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Насколько безопасно инвестировать с Freedom Broker?', 70, 80, 95, 40, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Какими лицензиями и регуляторами подтверждается надёжность Freedom Broker?', 75, 82, 95, 45, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Правда ли, что приложение Freedom Broker часто глючит?', 60, 75, 95, 35, 'ru'),
-- Freedom Broker (KZ)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Freedom Broker-пен инвестициялау қауіпсіз бе?', 70, 80, 95, 40, 'kz'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Freedom Broker сенімді брокер ме, оны кім реттейді?', 75, 82, 95, 45, 'kz'),
-- Freedom Broker (EN)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Is it safe to invest with Freedom Broker?', 70, 80, 95, 40, 'en'),

-- Freedom Bank (RU)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Правда ли, что деньги исчезают с карт Freedom Bank?', 55, 70, 95, 30, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Почему мобильное приложение Freedom Bank лагает?', 60, 75, 95, 35, 'ru'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Застрахованы ли вклады в Freedom Bank государством?', 90, 85, 100, 60, 'ru'),
-- Freedom Bank (KZ)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Freedom Bank картасынан ақшам жоғалып кетуі мүмкін бе?', 55, 70, 95, 30, 'kz'),
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Freedom Bank мобильді қосымшасы неге жиі істемей қалады?', 60, 75, 95, 35, 'kz'),
-- Freedom Bank (EN)
('999cf3e9-83b9-4e48-89a6-fbc4bae1a10b', 'Is my money safe with Freedom Bank?', 65, 75, 95, 35, 'en');

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
