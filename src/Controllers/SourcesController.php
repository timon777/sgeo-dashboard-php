<?php

namespace App\Controllers;

use App\Models\Source;
use App\Services\Cache;

class SourcesController extends BaseController
{
    public function index(): void
    {
        $sourceModel = new Source();
        $result = $sourceModel->all(1000); // Get more sources for accurate stats

        $sources = [];
        $stats = [
            'avgExpertise' => 0,
            'avgExperience' => 0,
            'avgAuthority' => 0,
            'avgTrust' => 0,
            'countryStats' => [],
            'typeStats' => [],
        ];

        if (isset($result['data']) && is_array($result['data'])) {
            $totalExpertise = 0;
            $totalExperience = 0;
            $totalAuthority = 0;
            $totalTrust = 0;

            foreach ($result['data'] as $s) {
                $sources[] = [
                    'id' => $s['id'],
                    'domain' => $s['domain'],
                    'type' => $s['type'] ?? 'media',
                    'country' => $s['country'] ?? 'OTHER',
                    'expertise' => (int)($s['expertise_score'] ?? 0),
                    'experience' => (int)($s['experience_score'] ?? 0),
                    'authority' => (int)($s['authority_score'] ?? 0),
                    'trust' => (int)($s['trust_score'] ?? 0),
                    'eeat' => (int)($s['eeat_combined'] ?? 0),
                    'share' => (float)($s['share_percent'] ?? 0),
                    'author' => (bool)($s['has_author'] ?? false),
                    'https' => (bool)($s['has_https'] ?? true),
                ];

                // Accumulate for averages
                $totalExpertise += (int)($s['expertise_score'] ?? 0);
                $totalExperience += (int)($s['experience_score'] ?? 0);
                $totalAuthority += (int)($s['authority_score'] ?? 0);
                $totalTrust += (int)($s['trust_score'] ?? 0);

                // Count by country
                $country = $s['country'] ?? 'OTHER';
                $stats['countryStats'][$country] = ($stats['countryStats'][$country] ?? 0) + 1;

                // Count by type
                $type = $s['type'] ?? 'media';
                $stats['typeStats'][$type] = ($stats['typeStats'][$type] ?? 0) + 1;
            }

            $count = count($result['data']);
            if ($count > 0) {
                $stats['avgExpertise'] = round($totalExpertise / $count);
                $stats['avgExperience'] = round($totalExperience / $count);
                $stats['avgAuthority'] = round($totalAuthority / $count);
                $stats['avgTrust'] = round($totalTrust / $count);
            }
        }

        $totalSources = count($sources);

        $this->render('sources/index', [
            'pageTitle' => 'Источники информации',
            'currentPage' => 'sources',
            'breadcrumb' => 'Источники',
            'sources' => $sources,
            'totalSources' => $totalSources,
            'stats' => $stats,
        ]);
    }

    public function store(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['domain'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Domain is required']);
            return;
        }

        $sourceModel = new Source();

        // Calculate E-E-A-T combined score
        $expertise = (int)($data['expertise_score'] ?? 0);
        $experience = (int)($data['experience_score'] ?? 0);
        $authority = (int)($data['authority_score'] ?? 0);
        $trust = (int)($data['trust_score'] ?? 0);
        $eeatCombined = round(($expertise + $experience + $authority + $trust) / 4);

        $result = $sourceModel->create([
            'domain' => $data['domain'],
            'type' => $data['type'] ?? 'media',
            'country' => $data['country'] ?? 'KZ',
            'expertise_score' => $expertise,
            'experience_score' => $experience,
            'authority_score' => $authority,
            'trust_score' => $trust,
            'eeat_combined' => $eeatCombined,
            'share_percent' => (float)($data['share_percent'] ?? 0),
            'has_author' => (bool)($data['has_author'] ?? false),
            'has_https' => (bool)($data['has_https'] ?? true),
        ]);

        if ($result['status'] >= 200 && $result['status'] < 300) {
            Cache::forget('sources_list');
            http_response_code(201);
            echo json_encode(['success' => true, 'data' => $result['data']]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create source']);
        }
    }

    public function update(string $id): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'PUT' && $_SERVER['REQUEST_METHOD'] !== 'PATCH') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $sourceModel = new Source();

        $source = $sourceModel->find($id);
        if (!$source) {
            http_response_code(404);
            echo json_encode(['error' => 'Source not found']);
            return;
        }

        $updateData = [];
        if (isset($data['domain'])) $updateData['domain'] = $data['domain'];
        if (isset($data['type'])) $updateData['type'] = $data['type'];
        if (isset($data['country'])) $updateData['country'] = $data['country'];
        if (isset($data['expertise_score'])) $updateData['expertise_score'] = (int)$data['expertise_score'];
        if (isset($data['experience_score'])) $updateData['experience_score'] = (int)$data['experience_score'];
        if (isset($data['authority_score'])) $updateData['authority_score'] = (int)$data['authority_score'];
        if (isset($data['trust_score'])) $updateData['trust_score'] = (int)$data['trust_score'];
        if (isset($data['share_percent'])) $updateData['share_percent'] = (float)$data['share_percent'];
        if (isset($data['has_author'])) $updateData['has_author'] = (bool)$data['has_author'];
        if (isset($data['has_https'])) $updateData['has_https'] = (bool)$data['has_https'];

        // Recalculate E-E-A-T if any score changed
        if (isset($data['expertise_score']) || isset($data['experience_score']) ||
            isset($data['authority_score']) || isset($data['trust_score'])) {
            $e = $updateData['expertise_score'] ?? $source['expertise_score'];
            $x = $updateData['experience_score'] ?? $source['experience_score'];
            $a = $updateData['authority_score'] ?? $source['authority_score'];
            $t = $updateData['trust_score'] ?? $source['trust_score'];
            $updateData['eeat_combined'] = round(($e + $x + $a + $t) / 4);
        }

        $updateData['updated_at'] = date('c');

        $result = $sourceModel->update($id, $updateData);

        if ($result['status'] >= 200 && $result['status'] < 300) {
            Cache::forget('sources_list');
            echo json_encode(['success' => true, 'data' => $result['data']]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update source']);
        }
    }

    public function destroy(string $id): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $sourceModel = new Source();
        $result = $sourceModel->delete($id);

        if ($result['status'] >= 200 && $result['status'] < 300) {
            Cache::forget('sources_list');
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete source']);
        }
    }

    public function import(): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['sources']) || !is_array($data['sources'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Sources array is required']);
            return;
        }

        $sourceModel = new Source();
        $updateExisting = $data['update_existing'] ?? false;

        $imported = 0;
        $updated = 0;
        $errors = 0;
        $errorDetails = [];

        foreach ($data['sources'] as $sourceData) {
            if (empty($sourceData['domain'])) {
                $errors++;
                continue;
            }

            // Check if domain already exists
            $existing = $sourceModel->byDomain($sourceData['domain']);

            // Only use fields that exist in the database schema
            $recordData = [
                'domain' => $sourceData['domain'],
                'type' => $sourceData['type'] ?? 'media',
                'country' => $sourceData['country'] ?? 'OTHER',
                'expertise_score' => (int)($sourceData['expertise_score'] ?? 0),
                'experience_score' => (int)($sourceData['experience_score'] ?? 0),
                'authority_score' => (int)($sourceData['authority_score'] ?? 0),
                'trust_score' => (int)($sourceData['trust_score'] ?? 0),
                'eeat_combined' => (int)($sourceData['eeat_combined'] ?? 0),
                'share_percent' => (float)($sourceData['share_percent'] ?? 0),
                'has_author' => (bool)($sourceData['has_author'] ?? false),
                'has_https' => (bool)($sourceData['has_https'] ?? true),
            ];

            // If E-E-A-T combined is 0, calculate it
            if ($recordData['eeat_combined'] === 0) {
                $recordData['eeat_combined'] = (int)round((
                    $recordData['expertise_score'] +
                    $recordData['experience_score'] +
                    $recordData['authority_score'] +
                    $recordData['trust_score']
                ) / 4);
            }

            try {
                if ($existing) {
                    if ($updateExisting) {
                        $recordData['updated_at'] = date('c');
                        $result = $sourceModel->update($existing['id'], $recordData);
                        if (isset($result['status']) && $result['status'] >= 200 && $result['status'] < 300) {
                            $updated++;
                        } else {
                            $errors++;
                            $errorDetails[] = 'Update failed for ' . $sourceData['domain'] . ': ' . json_encode($result);
                        }
                    }
                    // If not updating, skip silently (domain already exists)
                } else {
                    $result = $sourceModel->create($recordData);
                    if (isset($result['status']) && $result['status'] >= 200 && $result['status'] < 300) {
                        $imported++;
                    } else {
                        $errors++;
                        $errorDetails[] = 'Create failed for ' . $sourceData['domain'] . ': status=' . ($result['status'] ?? 'unknown') . ', data=' . json_encode($result['data'] ?? null);
                    }
                }
            } catch (\Exception $e) {
                $errors++;
                $errorDetails[] = 'Exception for ' . $sourceData['domain'] . ': ' . $e->getMessage();
            }
        }

        Cache::forget('sources_list');

        $response = [
            'success' => true,
            'imported' => $imported,
            'updated' => $updated,
            'errors' => $errors,
        ];

        // Include error details for debugging (first 5)
        if (!empty($errorDetails)) {
            $response['error_details'] = array_slice($errorDetails, 0, 5);
        }

        echo json_encode($response);
    }

    public function exportCsv(): void
    {
        $sourceModel = new Source();
        $result = $sourceModel->all(1000);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="sources_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, ['Домен', 'Тип', 'Страна', 'Экспертиза', 'Опыт', 'Авторитетность', 'Доверие', 'E-E-A-T', 'Доля (%)', 'Автор', 'HTTPS']);

        if (isset($result['data'])) {
            foreach ($result['data'] as $s) {
                fputcsv($output, [
                    $s['domain'],
                    $s['type'],
                    $s['country'],
                    $s['expertise_score'],
                    $s['experience_score'],
                    $s['authority_score'],
                    $s['trust_score'],
                    $s['eeat_combined'],
                    $s['share_percent'],
                    $s['has_author'] ? 'Да' : 'Нет',
                    $s['has_https'] ? 'Да' : 'Нет'
                ]);
            }
        }

        fclose($output);
    }
}
