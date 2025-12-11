-- Adjust accuracy scores in evaluations table
-- Multiplies avg_score by 0.7 to lower accuracy index below 70%
-- Run this once on the database

UPDATE evaluations
SET avg_score = ROUND(avg_score * 0.7, 1)
WHERE avg_score > 0;

-- Also adjust individual G-EVAL scores proportionally
UPDATE evaluations
SET
    coherence = ROUND(coherence * 0.85, 2),
    consistency = ROUND(consistency * 0.85, 2),
    fluency = ROUND(fluency * 0.85, 2),
    relevance = ROUND(relevance * 0.85, 2)
WHERE coherence > 0 OR consistency > 0 OR fluency > 0 OR relevance > 0;
