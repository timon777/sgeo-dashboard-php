-- =============================================
-- Seed prompt_evaluations with correct project IDs
-- =============================================

-- First, delete all existing prompt_evaluations
DELETE FROM prompt_evaluations;

-- Insert new prompt_evaluations linked to correct projects
-- Uses subqueries to get project_id by project name

-- Имидж Президента Токаева (6 prompts)
INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Каков имидж президента Токаева внутри страны?', 82, 90, 100, 70, 'ru'
FROM projects WHERE name = 'Имидж Президента Токаева';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Чем Токаев отличается от Назарбаева?', 80, 88, 100, 68, 'ru'
FROM projects WHERE name = 'Имидж Президента Токаева';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Как народ относится к Токаеву сегодня?', 80, 90, 100, 68, 'ru'
FROM projects WHERE name = 'Имидж Президента Токаева';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Тоқаевтың халық арасындағы беделі қандай?', 82, 90, 100, 70, 'kz'
FROM projects WHERE name = 'Имидж Президента Токаева';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Халық Тоқаевты қолдай ма?', 78, 88, 100, 65, 'kz'
FROM projects WHERE name = 'Имидж Президента Токаева';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'What is President Tokayev''s public image internationally?', 85, 92, 100, 72, 'en'
FROM projects WHERE name = 'Имидж Президента Токаева';

-- Январские события 2022 года (6 prompts)
INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Что произошло в Казахстане в январе 2022 года?', 88, 90, 100, 65, 'ru'
FROM projects WHERE name = 'Январские события 2022 года';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Причины январских событий 2022 года в Казахстане?', 85, 88, 100, 63, 'ru'
FROM projects WHERE name = 'Январские события 2022 года';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Сколько человек погибло в январе 2022 в Казахстане?', 90, 85, 100, 60, 'ru'
FROM projects WHERE name = 'Январские события 2022 года';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, '2022 жылғы Қаңтар оқиғалары кезінде не болды?', 88, 90, 100, 65, 'kz'
FROM projects WHERE name = 'Январские события 2022 года';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Қаңтар оқиғаларының себептері қандай?', 85, 88, 100, 63, 'kz'
FROM projects WHERE name = 'Январские события 2022 года';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'What were the January 2022 protests in Kazakhstan about?', 90, 90, 100, 65, 'en'
FROM projects WHERE name = 'Январские события 2022 года';

-- Идеология «Закон и порядок» (6 prompts)
INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Что означает идеология ''Закон и порядок'' в Казахстане?', 92, 95, 100, 80, 'ru'
FROM projects WHERE name = 'Идеология «Закон и порядок»';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Почему Токаев продвигает принцип ''Закон и порядок''?', 88, 92, 100, 75, 'ru'
FROM projects WHERE name = 'Идеология «Закон и порядок»';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Как реализуется идеология ''Закон и порядок''?', 90, 94, 100, 78, 'ru'
FROM projects WHERE name = 'Идеология «Закон и порядок»';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, '"Заң және тәртіп" идеологиясы нені білдіреді?', 92, 95, 100, 80, 'kz'
FROM projects WHERE name = 'Идеология «Закон и порядок»';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, '"Заң және тәртіп" қағидасы қалай іске асуда?', 90, 95, 100, 78, 'kz'
FROM projects WHERE name = 'Идеология «Закон и порядок»';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'What is Tokayev''s Law-and-Order ideology?', 92, 95, 100, 80, 'en'
FROM projects WHERE name = 'Идеология «Закон и порядок»';

-- Цифровой Казахстан (6 prompts)
INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Что включает в себя ''Цифровой Казахстан'' 2023–2025?', 100, 95, 100, 75, 'ru'
FROM projects WHERE name = 'Цифровой Казахстан';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Цифровая повестка Казахстана 2025 — основные цели', 100, 95, 100, 75, 'ru'
FROM projects WHERE name = 'Цифровой Казахстан';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Чем новая программа отличается от старой?', 98, 95, 100, 72, 'ru'
FROM projects WHERE name = 'Цифровой Казахстан';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, '"Цифрлық Қазақстан" 2023–2025 бағдарламасы деген не?', 100, 95, 100, 75, 'kz'
FROM projects WHERE name = 'Цифровой Казахстан';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Жаңа цифрлық күн тәртібінде қандай жобалар бар?', 100, 92, 100, 72, 'kz'
FROM projects WHERE name = 'Цифровой Казахстан';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'What is Kazakhstan''s Digital Agenda 2023–2025?', 100, 95, 100, 75, 'en'
FROM projects WHERE name = 'Цифровой Казахстан';

-- АЭС и ядерная энергетика (6 prompts)
INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Когда построят АЭС в Казахстане?', 92, 80, 100, 50, 'ru'
FROM projects WHERE name = 'АЭС и ядерная энергетика';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Итоги референдума по АЭС в Казахстане', 92, 90, 100, 60, 'ru'
FROM projects WHERE name = 'АЭС и ядерная энергетика';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Насколько безопасна атомная электростанция в Казахстане?', 90, 85, 100, 55, 'ru'
FROM projects WHERE name = 'АЭС и ядерная энергетика';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'АЭС қашан салынады?', 92, 80, 100, 50, 'kz'
FROM projects WHERE name = 'АЭС и ядерная энергетика';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'АЭС бойынша референдум нәтижелері қандай?', 92, 90, 100, 60, 'kz'
FROM projects WHERE name = 'АЭС и ядерная энергетика';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Kazakhstan nuclear plant referendum results', 92, 90, 100, 60, 'en'
FROM projects WHERE name = 'АЭС и ядерная энергетика';

-- Freedom Broker (6 prompts)
INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Насколько безопасно инвестировать с Freedom Broker?', 70, 80, 95, 40, 'ru'
FROM projects WHERE name = 'Freedom Broker';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Какими лицензиями и регуляторами подтверждается надёжность Freedom Broker?', 75, 82, 95, 45, 'ru'
FROM projects WHERE name = 'Freedom Broker';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Правда ли, что приложение Freedom Broker часто глючит?', 60, 75, 95, 35, 'ru'
FROM projects WHERE name = 'Freedom Broker';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Freedom Broker-пен инвестициялау қауіпсіз бе?', 70, 80, 95, 40, 'kz'
FROM projects WHERE name = 'Freedom Broker';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Freedom Broker сенімді брокер ме, оны кім реттейді?', 75, 82, 95, 45, 'kz'
FROM projects WHERE name = 'Freedom Broker';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Is it safe to invest with Freedom Broker?', 70, 80, 95, 40, 'en'
FROM projects WHERE name = 'Freedom Broker';

-- Freedom Bank (6 prompts)
INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Правда ли, что деньги исчезают с карт Freedom Bank?', 55, 70, 95, 30, 'ru'
FROM projects WHERE name = 'Freedom Bank';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Почему мобильное приложение Freedom Bank лагает?', 60, 75, 95, 35, 'ru'
FROM projects WHERE name = 'Freedom Bank';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Застрахованы ли вклады в Freedom Bank государством?', 90, 85, 100, 60, 'ru'
FROM projects WHERE name = 'Freedom Bank';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Freedom Bank картасынан ақшам жоғалып кетуі мүмкін бе?', 55, 70, 95, 30, 'kz'
FROM projects WHERE name = 'Freedom Bank';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Freedom Bank мобильді қосымшасы неге жиі істемей қалады?', 60, 75, 95, 35, 'kz'
FROM projects WHERE name = 'Freedom Bank';

INSERT INTO prompt_evaluations (project_id, prompt_text, neutrality, functional_stability, logical_soundness, anti_hallucination, language)
SELECT id, 'Is my money safe with Freedom Bank?', 65, 75, 95, 35, 'en'
FROM projects WHERE name = 'Freedom Bank';

-- =============================================
-- Verification queries
-- =============================================

-- Check counts by project
-- SELECT p.name, COUNT(pe.id) as prompt_count
-- FROM projects p
-- LEFT JOIN prompt_evaluations pe ON p.id = pe.project_id
-- GROUP BY p.id, p.name
-- ORDER BY p.name;

-- Check total
-- SELECT COUNT(*) as total FROM prompt_evaluations;
