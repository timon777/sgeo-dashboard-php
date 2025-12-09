-- =====================================================
-- SGEO Dashboard - Link AI Responses to Projects
-- Run this in Supabase SQL Editor after importing ai_responses
-- =====================================================

-- First, let's see current projects
-- SELECT id, name FROM projects;

-- =====================================================
-- Create mapping based on prompt text patterns
-- =====================================================

-- Freedom Bank prompts
UPDATE ai_responses
SET project_id = (SELECT id FROM projects WHERE name = 'Freedom Bank' LIMIT 1)
WHERE project_id IS NULL
AND (
    prompt ILIKE '%Freedom Bank%'
);

-- Freedom Broker prompts
UPDATE ai_responses
SET project_id = (SELECT id FROM projects WHERE name = 'Freedom Broker' LIMIT 1)
WHERE project_id IS NULL
AND (
    prompt ILIKE '%Freedom Broker%'
);

-- Имидж Президента Токаева prompts
UPDATE ai_responses
SET project_id = (SELECT id FROM projects WHERE name = 'Имидж Президента Токаева' LIMIT 1)
WHERE project_id IS NULL
AND (
    prompt ILIKE '%Токаев%'
    OR prompt ILIKE '%Тоқаев%'
    OR prompt ILIKE '%Tokayev%'
    OR prompt ILIKE '%Назарбаев%'
);

-- Январские события 2022 года prompts
UPDATE ai_responses
SET project_id = (SELECT id FROM projects WHERE name = 'Январские события 2022 года' LIMIT 1)
WHERE project_id IS NULL
AND (
    prompt ILIKE '%январ%2022%'
    OR prompt ILIKE '%январских событий%'
    OR prompt ILIKE '%Қаңтар%'
    OR prompt ILIKE '%January 2022%'
    OR prompt ILIKE '%protests in Kazakhstan%'
);

-- Идеология «Закон и порядок» prompts
UPDATE ai_responses
SET project_id = (SELECT id FROM projects WHERE name ILIKE '%закон%порядок%' OR name ILIKE '%Идеология%' LIMIT 1)
WHERE project_id IS NULL
AND (
    prompt ILIKE '%Закон и порядок%'
    OR prompt ILIKE '%закон и порядок%'
    OR prompt ILIKE '%Law-and-Order%'
    OR prompt ILIKE '%Law and Order%'
    OR prompt ILIKE '%Заң және тәртіп%'
);

-- Цифровой Казахстан prompts
UPDATE ai_responses
SET project_id = (SELECT id FROM projects WHERE name = 'Цифровой Казахстан' LIMIT 1)
WHERE project_id IS NULL
AND (
    prompt ILIKE '%Цифровой Казахстан%'
    OR prompt ILIKE '%Цифрлық Қазақстан%'
    OR prompt ILIKE '%Digital%Kazakh%'
    OR prompt ILIKE '%Digital Agenda%'
    OR prompt ILIKE '%цифровая повестка%'
    OR prompt ILIKE '%цифрлық күн%'
);

-- АЭС и ядерная энергетика prompts
UPDATE ai_responses
SET project_id = (SELECT id FROM projects WHERE name = 'АЭС и ядерная энергетика' LIMIT 1)
WHERE project_id IS NULL
AND (
    prompt ILIKE '%АЭС%'
    OR prompt ILIKE '%атом%'
    OR prompt ILIKE '%nuclear%'
    OR prompt ILIKE '%референдум%'
    OR prompt ILIKE '%referendum%'
);

-- =====================================================
-- Verify the results
-- =====================================================
SELECT
    p.name as project_name,
    COUNT(ar.id) as response_count
FROM projects p
LEFT JOIN ai_responses ar ON ar.project_id = p.id
GROUP BY p.id, p.name
ORDER BY response_count DESC;

-- Show unlinked responses (if any)
SELECT COUNT(*) as unlinked_count
FROM ai_responses
WHERE project_id IS NULL;
