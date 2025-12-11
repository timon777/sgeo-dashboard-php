<?php

namespace App\Controllers;

use App\Models\Source;

class SourcesController extends BaseController
{
    public function index(): void
    {
        $sourceModel = new Source();
        $result = $sourceModel->all(100);

        $sources = [];
        if (isset($result['data']) && is_array($result['data'])) {
            foreach ($result['data'] as $s) {
                $sources[] = [
                    'id' => $s['id'],
                    'domain' => $s['domain'],
                    'type' => $s['type'],
                    'country' => $s['country'],
                    'expertise' => (int)$s['expertise_score'],
                    'experience' => (int)$s['experience_score'],
                    'authority' => (int)$s['authority_score'],
                    'trust' => (int)$s['trust_score'],
                    'eeat' => (int)$s['eeat_combined'],
                    'share' => (float)$s['share_percent'],
                    'author' => (bool)$s['has_author'],
                    'https' => (bool)$s['has_https'],
                    'url_rating' => (int)($s['url_rating'] ?? 0),
                    'domain_rating' => (int)($s['domain_rating'] ?? 0),
                ];
            }
        }

        $totalSources = $sourceModel->count();

        $this->render('sources/index', [
            'pageTitle' => 'Источники информации',
            'currentPage' => 'sources',
            'breadcrumb' => 'Источники',
            'sources' => $sources,
            'totalSources' => $totalSources,
        ]);
    }

    public function store(): void
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['domain'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Domain is required']);
            return;
        }

        $sourceModel = new Source();

        // Check if domain already exists
        $existing = $sourceModel->findByDomain($input['domain']);
        if ($existing) {
            http_response_code(409);
            echo json_encode(['error' => 'Domain already exists', 'id' => $existing['id']]);
            return;
        }

        // Calculate E-E-A-T combined score
        $expertise = (int)($input['expertise_score'] ?? 0);
        $experience = (int)($input['experience_score'] ?? 0);
        $authority = (int)($input['authority_score'] ?? 0);
        $trust = (int)($input['trust_score'] ?? 0);
        $eeatCombined = round(($expertise + $experience + $authority + $trust) / 4);

        $data = [
            'domain' => $input['domain'],
            'type' => $input['type'] ?? 'media',
            'country' => $input['country'] ?? 'US',
            'expertise_score' => $expertise,
            'experience_score' => $experience,
            'authority_score' => $authority,
            'trust_score' => $trust,
            'eeat_combined' => $eeatCombined,
            'share_percent' => (float)($input['share_percent'] ?? 0),
            'has_author' => (bool)($input['has_author'] ?? false),
            'has_https' => (bool)($input['has_https'] ?? true),
            'url_rating' => (int)($input['url_rating'] ?? 0),
            'domain_rating' => (int)($input['domain_rating'] ?? 0),
        ];

        $result = $sourceModel->create($data);

        if (isset($result['error'])) {
            http_response_code(500);
            echo json_encode(['error' => $result['error']]);
            return;
        }

        http_response_code(201);
        echo json_encode(['success' => true, 'data' => $result]);
    }

    public function update(string $id): void
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        $sourceModel = new Source();

        // Check if source exists
        $existing = $sourceModel->find($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['error' => 'Source not found']);
            return;
        }

        $data = [];

        // Update only provided fields
        $fields = ['domain', 'type', 'country', 'share_percent', 'has_author', 'has_https', 'url_rating', 'domain_rating'];
        foreach ($fields as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field];
            }
        }

        // Handle E-E-A-T scores
        $scoreFields = ['expertise_score', 'experience_score', 'authority_score', 'trust_score'];
        $recalculateEeat = false;
        foreach ($scoreFields as $field) {
            if (isset($input[$field])) {
                $data[$field] = (int)$input[$field];
                $recalculateEeat = true;
            }
        }

        // Recalculate E-E-A-T combined if any score changed
        if ($recalculateEeat) {
            $expertise = $data['expertise_score'] ?? $existing['expertise_score'];
            $experience = $data['experience_score'] ?? $existing['experience_score'];
            $authority = $data['authority_score'] ?? $existing['authority_score'];
            $trust = $data['trust_score'] ?? $existing['trust_score'];
            $data['eeat_combined'] = round(($expertise + $experience + $authority + $trust) / 4);
        }

        if (empty($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'No data to update']);
            return;
        }

        $result = $sourceModel->update($id, $data);

        if (isset($result['error'])) {
            http_response_code(500);
            echo json_encode(['error' => $result['error']]);
            return;
        }

        echo json_encode(['success' => true, 'data' => $result]);
    }

    public function destroy(string $id): void
    {
        header('Content-Type: application/json');

        $sourceModel = new Source();

        $existing = $sourceModel->find($id);
        if (!$existing) {
            http_response_code(404);
            echo json_encode(['error' => 'Source not found']);
            return;
        }

        $result = $sourceModel->delete($id);

        if (isset($result['error'])) {
            http_response_code(500);
            echo json_encode(['error' => $result['error']]);
            return;
        }

        echo json_encode(['success' => true]);
    }

    public function import(): void
    {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);

        if (empty($input['sources']) || !is_array($input['sources'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Sources array is required']);
            return;
        }

        $sourceModel = new Source();
        $updateExisting = $input['update_existing'] ?? false;

        $imported = 0;
        $updated = 0;
        $errors = 0;

        foreach ($input['sources'] as $sourceData) {
            if (empty($sourceData['domain'])) {
                $errors++;
                continue;
            }

            // Check if exists
            $existing = $sourceModel->findByDomain($sourceData['domain']);

            // Calculate E-E-A-T
            $expertise = (int)($sourceData['expertise_score'] ?? 0);
            $experience = (int)($sourceData['experience_score'] ?? 0);
            $authority = (int)($sourceData['authority_score'] ?? 0);
            $trust = (int)($sourceData['trust_score'] ?? 0);
            $eeatCombined = round(($expertise + $experience + $authority + $trust) / 4);

            // Detect country from affiliation text
            $country = $this->detectCountry($sourceData['country'] ?? '');

            $recordData = [
                'domain' => $sourceData['domain'],
                'type' => $sourceData['type'] ?? 'media',
                'country' => $country,
                'expertise_score' => $expertise,
                'experience_score' => $experience,
                'authority_score' => $authority,
                'trust_score' => $trust,
                'eeat_combined' => $eeatCombined,
                'share_percent' => (float)($sourceData['share_percent'] ?? 0),
                'has_author' => (bool)($sourceData['has_author'] ?? false),
                'has_https' => strpos($sourceData['domain'], 'https') !== false || (bool)($sourceData['has_https'] ?? true),
            ];

            // Add optional fields if provided
            if (!empty($sourceData['source_url'])) {
                $recordData['source_url'] = $sourceData['source_url'];
            }
            if (isset($sourceData['url_rating'])) {
                $recordData['url_rating'] = (int)$sourceData['url_rating'];
            }
            if (isset($sourceData['domain_rating'])) {
                $recordData['domain_rating'] = (int)$sourceData['domain_rating'];
            }
            if (isset($sourceData['organic_traffic'])) {
                $recordData['organic_traffic'] = (int)$sourceData['organic_traffic'];
            }

            if ($existing) {
                if ($updateExisting) {
                    $result = $sourceModel->update($existing['id'], $recordData);
                    if (!isset($result['error'])) {
                        $updated++;
                    } else {
                        $errors++;
                    }
                }
            } else {
                $result = $sourceModel->create($recordData);
                if (!isset($result['error'])) {
                    $imported++;
                } else {
                    $errors++;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'imported' => $imported,
            'updated' => $updated,
            'errors' => $errors,
            'total' => count($input['sources']),
        ]);
    }

    public function exportCsv(): void
    {
        $sourceModel = new Source();
        $result = $sourceModel->all(10000);

        $filename = 'sources_export_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // BOM for Excel UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header
        fputcsv($output, [
            'Domain', 'Type', 'Country', 'URL Rating', 'Domain Rating',
            'Experience', 'Expertise', 'Authority', 'Trust', 'E-E-A-T Combined',
            'Share %', 'Has Author', 'Has HTTPS'
        ], ';');

        // Data
        if (isset($result['data'])) {
            foreach ($result['data'] as $source) {
                fputcsv($output, [
                    $source['domain'],
                    $source['type'],
                    $source['country'],
                    $source['url_rating'] ?? 0,
                    $source['domain_rating'] ?? 0,
                    $source['experience_score'],
                    $source['expertise_score'],
                    $source['authority_score'],
                    $source['trust_score'],
                    $source['eeat_combined'],
                    $source['share_percent'],
                    $source['has_author'] ? 'Yes' : 'No',
                    $source['has_https'] ? 'Yes' : 'No',
                ], ';');
            }
        }

        fclose($output);
        exit;
    }

    private function detectCountry(string $affiliation): string
    {
        $affiliation = mb_strtolower($affiliation);

        $countryMap = [
            'казахстан' => 'KZ',
            'kazakhstan' => 'KZ',
            'kz' => 'KZ',
            'россия' => 'RU',
            'russia' => 'RU',
            'ru' => 'RU',
            'сша' => 'US',
            'usa' => 'US',
            'us' => 'US',
            'америк' => 'US',
            'великобритан' => 'GB',
            'uk' => 'GB',
            'англ' => 'GB',
            'герман' => 'DE',
            'germany' => 'DE',
            'de' => 'DE',
            'франц' => 'FR',
            'france' => 'FR',
            'fr' => 'FR',
            'китай' => 'CN',
            'china' => 'CN',
            'cn' => 'CN',
            'международн' => 'INT',
            'international' => 'INT',
        ];

        foreach ($countryMap as $key => $code) {
            if (strpos($affiliation, $key) !== false) {
                return $code;
            }
        }

        return 'US';
    }
}
