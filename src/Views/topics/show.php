<!-- Hero Gauge Section -->
<div class="hero-gauge-section">
    <div class="hero-gauge-card">
        <div class="hero-gauge-container">
            <svg class="hero-gauge-svg" width="320" height="200" viewBox="0 0 320 200" style="overflow: visible;">
                <defs>
                    <linearGradient id="topicGaugeGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#ef4444"/>
                        <stop offset="25%" style="stop-color:#f97316"/>
                        <stop offset="50%" style="stop-color:#fbbf24"/>
                        <stop offset="75%" style="stop-color:#22c55e"/>
                        <stop offset="100%" style="stop-color:#22c55e"/>
                    </linearGradient>
                    <filter id="topicNeedleShadow" x="-50%" y="-50%" width="200%" height="200%">
                        <feDropShadow dx="0" dy="2" stdDeviation="2" flood-opacity="0.3"/>
                    </filter>
                </defs>

                <!-- Background arc -->
                <path d="M 30 160 A 130 130 0 0 1 290 160"
                      stroke="var(--border-subtle)" stroke-width="28" fill="none" stroke-linecap="round"/>

                <!-- Gradient arc -->
                <path d="M 30 160 A 130 130 0 0 1 290 160"
                      stroke="url(#topicGaugeGradient)" stroke-width="28" fill="none" stroke-linecap="round"/>

                <!-- Labels -->
                <text x="30" y="185" font-size="14" font-weight="500" fill="var(--text-tertiary)" text-anchor="middle">0</text>
                <text x="290" y="185" font-size="14" font-weight="500" fill="var(--text-tertiary)" text-anchor="middle">100</text>

                <!-- Value -->
                <text x="160" y="140" text-anchor="middle" font-size="48" font-weight="700" fill="var(--text-primary)" id="topicGaugeValue"><?= $stats['avgAccuracy'] ?></text>

                <!-- Needle (rendered last to be on top) -->
                <g id="topicAccuracyNeedle">
                    <line x1="160" y1="160" x2="160" y2="50"
                          stroke="var(--text-primary)" stroke-width="4" stroke-linecap="round"/>
                    <circle cx="160" cy="160" r="10" fill="var(--text-primary)"/>
                    <circle cx="160" cy="160" r="5" fill="var(--bg-card)"/>
                </g>
            </svg>
            <div class="hero-gauge-label">
                <span class="hero-gauge-title">Индекс точности</span>
            </div>
        </div>
    </div>
</div>

<!-- Topic Stats Header -->
<div class="topic-stats-header">
    <div class="topic-stats-grid">
        <div class="topic-stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= number_format($stats['responsesCount']) ?></div>
                <div class="stat-label">Ответов</div>
            </div>
        </div>
        <div class="topic-stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14,2 14,8 20,8"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= number_format($stats['promptsCount']) ?></div>
                <div class="stat-label">Промтов</div>
            </div>
        </div>
        <div class="topic-stat-card">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M12 1v6m0 6v10"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= number_format($stats['modelsCount']) ?></div>
                <div class="stat-label">LLM моделей</div>
            </div>
        </div>
    </div>
</div>

<!-- Tab Content -->
<div class="topic-content">
    <?php if ($activeTab === 'overview'): ?>
    <!-- Overview Tab -->
    <div class="tab-panel" id="overview-panel">
        <h1 class="page-title">Промты</h1>

        <!-- Radar Chart Section - Prompt Quality -->
        <div class="section-block radar-section">
            <h2 class="section-title-lg">Оценка качества промтов по ключевым критериям</h2>
            <p class="section-description">Сводная оценка всех промтов проекта по пяти критериям: нейтральность, стабильность, логичность и риск галлюцинаций. Радар показывает средний профиль качества формулировок.</p>

            <div class="radar-content">
                <div class="radar-chart-container">
                    <canvas id="promptQualityRadar"></canvas>
                </div>
                <div class="radar-accordion">
                    <div class="accordion">
                        <div class="accordion-item">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <span class="accordion-title">1. Нейтральность (Linguistic Neutrality)</span>
                                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m6 9 6 6 6-6"/>
                                </svg>
                            </div>
                            <div class="accordion-content">
                                <div class="accordion-body">
                                    Степень отсутствия в промпте эмоционально, идеологически или коннотативно окрашенных элементов. Максимальная оценка присваивается, если формулировка полностью объективна и не склоняет модель к позитивной или негативной интерпретации.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <span class="accordion-title">2. Функциональная стабильность (Functional Stability)</span>
                                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m6 9 6 6 6-6"/>
                                </svg>
                            </div>
                            <div class="accordion-content">
                                <div class="accordion-body">
                                    Способность промпта обеспечивать сопоставимое поведение разных LLM по структуре, глубине и формату ответа. Максимальная оценка присваивается, если промпт вызывает единообразные по формату и уровню детализации ответы во всех моделях.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <span class="accordion-title">3. Логическая корректность (Logical Soundness)</span>
                                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m6 9 6 6 6-6"/>
                                </svg>
                            </div>
                            <div class="accordion-content">
                                <div class="accordion-body">
                                    Степень внутренней непротиворечивости, ясности и логической согласованности инструкции. Максимальная оценка присваивается, если промпт логичен, однозначен и не содержит конфликтующих условий.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <div class="accordion-header" onclick="toggleAccordion(this)">
                                <span class="accordion-title">4. Защита от галлюцинаций (Anti-Hallucination)</span>
                                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m6 9 6 6 6-6"/>
                                </svg>
                            </div>
                            <div class="accordion-content">
                                <div class="accordion-body">
                                    Устойчивость промпта к провоцированию модели на генерацию недостоверной или вымышленной информации. Максимальная оценка присваивается, если промпт не содержит ложных предпосылок и минимизирует риск спекулятивных выводов.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prompts Table -->
        <div class="section-block">
            <div class="table-header-row">
                <h3 class="section-title">Оценки промтов <span class="count-badge"><?= $tabData['totalCount'] ?? 0 ?></span></h3>
            </div>
            <div class="table-container">
                <table class="table prompts-table" id="overviewPromptsTable">
                    <thead>
                        <tr>
                            <th>Промпт (запрос)</th>
                            <th>Нейтральн.</th>
                            <th>Стабильн.</th>
                            <th>Логичность</th>
                            <th>Антигаллюц.</th>
                            <th>Среднее</th>
                        </tr>
                    </thead>
                    <tbody id="overviewPromptsBody">
                        <?php foreach ($tabData['prompts'] ?? [] as $index => $prompt): ?>
                        <tr>
                            <td class="prompt-cell">
                                <span class="prompt-number">№<?= $index + 1 ?></span>
                                <a href="#" class="prompt-link"><?= htmlspecialchars($prompt['shortText']) ?></a>
                            </td>
                            <td class="score-cell"><?= $prompt['neutrality'] ?? $prompt['coherence'] ?? 0 ?>%</td>
                            <td class="score-cell"><?= $prompt['stability'] ?? $prompt['consistency'] ?? 0 ?>%</td>
                            <td class="score-cell"><?= $prompt['soundness'] ?? $prompt['fluency'] ?? 0 ?>%</td>
                            <td class="score-cell"><?= $prompt['antiHallucination'] ?? $prompt['relevance'] ?? 0 ?>%</td>
                            <td class="score-cell"><?= $prompt['avgScore'] ?? 0 ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($tabData['hasMore'] ?? false): ?>
            <div class="load-more-container">
                <button type="button" class="btn-load-more" id="loadMoreOverview"
                        data-topic-id="<?= htmlspecialchars($topic['id']) ?>"
                        data-offset="10"
                        data-tab="overview">
                    <span class="btn-text">Загрузить ещё</span>
                    <span class="btn-loader" style="display:none;">
                        <svg class="spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="30 60"/></svg>
                    </span>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php elseif ($activeTab === 'responses'): ?>
    <!-- Responses Tab -->
    <div class="tab-panel" id="responses-panel">
        <!-- Model Comparison -->
        <?php if (!empty($tabData['modelComparison'])): ?>
        <div class="section-block">
            <h3 class="section-title">Сравнение моделей</h3>
            <div class="model-comparison">
                <?php foreach ($tabData['modelComparison'] as $model): ?>
                <div class="model-card">
                    <div class="model-name"><?= htmlspecialchars($model['name']) ?></div>
                    <div class="model-score"><?= $model['avgScore'] ?></div>
                    <div class="model-meta"><?= $model['count'] ?> ответов</div>
                    <div class="model-bar">
                        <div class="model-bar-fill" style="width: <?= $model['avgScore'] ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Responses List -->
        <div class="section-block">
            <h3 class="section-title">Список ответов <span class="count-badge"><?= $tabData['totalCount'] ?></span></h3>
            <div class="responses-list" id="responsesList">
                <?php foreach ($tabData['responses'] as $response): ?>
                <div class="response-card">
                    <div class="response-header">
                        <span class="llm-badge <?= strtolower($response['model']) ?>"><?= htmlspecialchars($response['model']) ?></span>
                        <span class="response-date"><?= $response['date'] ?></span>
                        <span class="tone-badge <?= $response['tone'] ?>"><?= ucfirst($response['tone']) ?></span>
                    </div>
                    <div class="response-prompt">
                        <strong>Промт:</strong> <?= htmlspecialchars($response['prompt']) ?>
                    </div>
                    <div class="response-text">
                        <strong>Ответ:</strong> <?= htmlspecialchars($response['response']) ?>
                    </div>
                    <div class="response-metrics">
                        <div class="metric">
                            <span class="metric-label">Связность</span>
                            <span class="metric-value <?= $response['coherence'] >= 70 ? 'good' : ($response['coherence'] >= 50 ? 'medium' : 'bad') ?>"><?= $response['coherence'] ?></span>
                        </div>
                        <div class="metric">
                            <span class="metric-label">Согласов.</span>
                            <span class="metric-value <?= $response['consistency'] >= 70 ? 'good' : ($response['consistency'] >= 50 ? 'medium' : 'bad') ?>"><?= $response['consistency'] ?></span>
                        </div>
                        <div class="metric">
                            <span class="metric-label">Беглость</span>
                            <span class="metric-value <?= $response['fluency'] >= 70 ? 'good' : ($response['fluency'] >= 50 ? 'medium' : 'bad') ?>"><?= $response['fluency'] ?></span>
                        </div>
                        <div class="metric">
                            <span class="metric-label">Релевант.</span>
                            <span class="metric-value <?= $response['relevance'] >= 70 ? 'good' : ($response['relevance'] >= 50 ? 'medium' : 'bad') ?>"><?= $response['relevance'] ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if (empty($tabData['responses'])): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <p>Нет ответов по этой теме</p>
                </div>
                <?php endif; ?>
            </div>
            <?php if ($tabData['hasMore'] ?? false): ?>
            <div class="load-more-container">
                <button type="button" class="btn-load-more" id="loadMoreResponses"
                        data-topic-id="<?= htmlspecialchars($topic['id']) ?>"
                        data-offset="10"
                        data-tab="responses">
                    <span class="btn-text">Загрузить ещё</span>
                    <span class="btn-loader" style="display:none;">
                        <svg class="spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="30 60"/></svg>
                    </span>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php elseif ($activeTab === 'prompts'): ?>
    <!-- Prompts Tab -->
    <div class="tab-panel" id="prompts-panel">
        <!-- Prompts List -->
        <div class="section-block">
            <h3 class="section-title">Список промтов <span class="count-badge"><?= $tabData['totalCount'] ?? 0 ?></span></h3>
            <div class="prompts-list" id="promptsList">
                <?php foreach ($tabData['prompts'] ?? [] as $prompt): ?>
                <div class="prompt-card">
                    <div class="prompt-header">
                        <span class="llm-badge models-count"><?= $prompt['modelsCount'] ?? 1 ?> LLM</span>
                        <span class="responses-count"><?= $prompt['responsesCount'] ?? 1 ?> ответов</span>
                        <span class="prompt-date"><?= $prompt['date'] ?></span>
                    </div>
                    <div class="prompt-text"><?= htmlspecialchars($prompt['text']) ?></div>
                </div>
                <?php endforeach; ?>

                <?php if (empty($tabData['prompts'])): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14,2 14,8 20,8"/>
                    </svg>
                    <p>Нет промтов по этой теме</p>
                </div>
                <?php endif; ?>
            </div>
            <?php if ($tabData['hasMore'] ?? false): ?>
            <div class="load-more-container">
                <button type="button" class="btn-load-more" id="loadMorePrompts"
                        data-topic-id="<?= htmlspecialchars($topic['id']) ?>"
                        data-offset="10"
                        data-tab="prompts">
                    <span class="btn-text">Загрузить ещё</span>
                    <span class="btn-loader" style="display:none;">
                        <svg class="spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="30 60"/></svg>
                    </span>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php elseif ($activeTab === 'sources'): ?>
    <!-- Sources Tab -->
    <div class="tab-panel" id="sources-panel">
        <!-- Sources Stats -->
        <div class="section-block">
            <h3 class="section-title">Статистика источников</h3>
            <div class="sources-stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?= $tabData['totalCount'] ?></div>
                    <div class="stat-label">Всего источников</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= $tabData['avgEeat'] ?></div>
                    <div class="stat-label">Средний E-E-A-T</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-value"><?= $tabData['weakCount'] ?></div>
                    <div class="stat-label">Слабых источников</div>
                </div>
            </div>
        </div>

        <!-- Distribution Charts -->
        <div class="section-block">
            <h3 class="section-title">Распределение</h3>
            <div class="distribution-grid">
                <div class="distribution-card">
                    <h4>По странам</h4>
                    <div class="distribution-list">
                        <?php
                        $countryLabels = ['KZ' => 'Казахстан', 'RU' => 'Россия', 'US' => 'США', 'UK' => 'UK', 'OTHER' => 'Другие'];
                        foreach ($tabData['countryStats'] as $country => $count):
                        ?>
                        <div class="distribution-item">
                            <span class="dist-label"><?= $countryLabels[$country] ?? $country ?></span>
                            <span class="dist-value"><?= $count ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="distribution-card">
                    <h4>По типам</h4>
                    <div class="distribution-list">
                        <?php
                        $typeLabels = ['gov' => 'Гос. сайты', 'media' => 'СМИ', 'analytics' => 'Аналитика', 'wiki' => 'Wiki'];
                        foreach ($tabData['typeStats'] as $type => $count):
                        ?>
                        <div class="distribution-item">
                            <span class="dist-label"><?= $typeLabels[$type] ?? $type ?></span>
                            <span class="dist-value"><?= $count ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weak Sources Warning -->
        <?php if ($tabData['weakCount'] > 0): ?>
        <div class="section-block">
            <h3 class="section-title warning-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/>
                    <line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                Слабые источники (E-E-A-T < 50)
            </h3>
            <div class="weak-sources-list">
                <?php foreach (array_slice($tabData['weakSources'], 0, 5) as $source): ?>
                <div class="weak-source-item">
                    <span class="weak-domain"><?= htmlspecialchars($source['domain']) ?></span>
                    <span class="weak-eeat"><?= $source['eeat'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Sources Table -->
        <div class="section-block">
            <h3 class="section-title">Все источники <span class="count-badge"><?= $tabData['totalCount'] ?></span></h3>
            <div class="table-container sources-table-wrap">
                <table class="table" id="sourcesTable">
                    <thead>
                        <tr>
                            <th>Домен</th>
                            <th>DR</th>
                            <th>UR</th>
                            <th>Тип</th>
                            <th>Страна</th>
                            <th>Эксперт.</th>
                            <th>Опыт</th>
                            <th>Авторит.</th>
                            <th>Доверие</th>
                            <th>E-E-A-T</th>
                        </tr>
                    </thead>
                    <tbody id="sourcesBody">
                        <?php foreach ($tabData['sources'] as $source): ?>
                        <tr>
                            <td>
                                <a href="https://<?= htmlspecialchars($source['domain']) ?>" target="_blank" class="domain-link">
                                    <?= htmlspecialchars($source['domain']) ?>
                                </a>
                            </td>
                            <td class="mono rank-cell <?= ($source['domainRank'] ?? 0) >= 50 ? 'high' : (($source['domainRank'] ?? 0) >= 20 ? 'medium' : '') ?>"><?= $source['domainRank'] ?? 0 ?></td>
                            <td class="mono rank-cell <?= ($source['urlRank'] ?? 0) >= 50 ? 'high' : (($source['urlRank'] ?? 0) >= 20 ? 'medium' : '') ?>"><?= $source['urlRank'] ?? 0 ?></td>
                            <td><span class="type-badge <?= $source['type'] ?>"><?= $typeLabels[$source['type']] ?? $source['type'] ?></span></td>
                            <td><?= $source['country'] ?></td>
                            <td class="mono"><?= $source['expertise'] ?></td>
                            <td class="mono"><?= $source['experience'] ?></td>
                            <td class="mono"><?= $source['authority'] ?></td>
                            <td class="mono"><?= $source['trust'] ?></td>
                            <td>
                                <span class="eeat-badge <?= $source['eeat'] >= 80 ? 'high' : ($source['eeat'] >= 50 ? 'medium' : 'low') ?>">
                                    <?= $source['eeat'] ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($tabData['hasMore'] ?? false): ?>
            <div class="load-more-container">
                <button type="button" class="btn-load-more" id="loadMoreSources"
                        data-topic-id="<?= htmlspecialchars($topic['id']) ?>"
                        data-offset="10"
                        data-tab="sources">
                    <span class="btn-text">Загрузить ещё</span>
                    <span class="btn-loader" style="display:none;">
                        <svg class="spinner" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" fill="none" stroke-dasharray="30 60"/></svg>
                    </span>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
/* Topic Stats Header */
.topic-stats-header {
    margin-bottom: 24px;
}

.topic-stats-grid {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}

.topic-stats-grid .topic-stat-card {
    flex: 0 1 200px;
}

.topic-stat-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px 20px;
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    transition: all var(--transition-fast);
}

.topic-stat-card:hover {
    border-color: var(--border-default);
    transform: translateY(-2px);
}

.topic-stat-card.highlight {
    background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
    border-color: rgba(99, 102, 241, 0.3);
}

.topic-stat-card .stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    color: var(--text-tertiary);
}

.topic-stat-card.highlight .stat-icon {
    background: rgba(99, 102, 241, 0.15);
    color: var(--accent-primary);
}

.topic-stat-card .stat-icon svg {
    width: 22px;
    height: 22px;
}

.topic-stat-card .stat-content {
    flex: 1;
}

.topic-stat-card .stat-value {
    font-size: 24px;
    font-weight: 700;
    font-family: 'JetBrains Mono', monospace;
    color: var(--text-primary);
    line-height: 1.2;
}

.topic-stat-card.highlight .stat-value {
    background: var(--accent-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.topic-stat-card .stat-label {
    font-size: 12px;
    color: var(--text-tertiary);
    margin-top: 2px;
}

/* Content */
.topic-content { margin-bottom: 32px; }

/* Overview Tab Styles */
.page-title {
    font-size: 32px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 24px;
    text-align: center;
}

.section-title-lg {
    font-size: 20px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 12px;
}

.section-description {
    font-size: 14px;
    color: var(--text-secondary);
    line-height: 1.6;
    margin-bottom: 24px;
}

.radar-section {
    padding: 32px;
}

.radar-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    align-items: start;
}

.radar-chart-container {
    width: 100%;
    max-width: 480px;
    height: 300px;
    padding: 0;
}

.radar-accordion {
    flex: 1;
}

.radar-accordion .accordion {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.radar-accordion .accordion-item {
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    overflow: hidden;
}

.radar-accordion .accordion-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    cursor: pointer;
    transition: background var(--transition-fast);
}

.radar-accordion .accordion-header:hover {
    background: rgba(255, 255, 255, 0.05);
}

.radar-accordion .accordion-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
}

.radar-accordion .accordion-icon {
    width: 18px;
    height: 18px;
    color: var(--text-tertiary);
    transition: transform var(--transition-fast);
}

.radar-accordion .accordion-item.active .accordion-icon {
    transform: rotate(180deg);
}

.radar-accordion .accordion-content {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.radar-accordion .accordion-item.active .accordion-content {
    max-height: 200px;
}

.radar-accordion .accordion-body {
    padding: 0 18px 16px;
    font-size: 13px;
    color: var(--text-secondary);
    line-height: 1.6;
}

/* Prompts Table */
.prompts-table {
    width: 100%;
}

.prompts-table th {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    text-align: left;
    padding: 12px 16px;
    border-bottom: 1px solid var(--border-subtle);
}

.prompts-table td {
    padding: 14px 16px;
    border-bottom: 1px solid var(--border-subtle);
    vertical-align: top;
}

.prompt-cell {
    max-width: 400px;
}

.prompt-number {
    display: block;
    font-size: 11px;
    font-weight: 600;
    color: var(--text-tertiary);
    margin-bottom: 4px;
}

.prompt-link {
    color: var(--accent-primary);
    text-decoration: underline;
    font-size: 13px;
    line-height: 1.4;
}

.prompt-link:hover {
    color: var(--accent-secondary);
}

.score-cell {
    font-size: 13px;
    font-family: 'JetBrains Mono', monospace;
    color: var(--text-secondary);
    white-space: nowrap;
}

/* Load More Button */
.load-more-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid var(--border-subtle);
}

.btn-load-more {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 32px;
    background: var(--bg-tertiary);
    border: 1px solid var(--border-default);
    border-radius: var(--radius-md);
    color: var(--text-primary);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.btn-load-more:hover {
    background: var(--bg-secondary);
    border-color: var(--accent-primary);
    color: var(--accent-primary);
}

.btn-load-more:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-load-more .spinner {
    width: 18px;
    height: 18px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.table-header-row {
    margin-bottom: 16px;
}

@media (max-width: 1200px) {
    .radar-content {
        grid-template-columns: 1fr;
    }

    .radar-chart-container {
        max-width: 400px;
        height: 280px;
        margin: 0 auto;
    }
}

.section-block {
    background: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: var(--radius-lg);
    padding: 24px;
    margin-bottom: 20px;
}

.section-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.count-badge {
    background: var(--bg-tertiary);
    padding: 2px 8px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 500;
    color: var(--text-tertiary);
}

/* Model Comparison */
.model-comparison {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.model-card {
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    padding: 16px;
}

.model-name {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.model-score {
    font-size: 28px;
    font-weight: 700;
    font-family: 'JetBrains Mono', monospace;
    color: var(--accent-primary);
}

.model-meta {
    font-size: 12px;
    color: var(--text-tertiary);
    margin-bottom: 8px;
}

.model-bar {
    height: 4px;
    background: var(--bg-secondary);
    border-radius: 2px;
    overflow: hidden;
}

.model-bar-fill {
    height: 100%;
    background: var(--accent-gradient);
    border-radius: 2px;
}

/* Response Card */
.response-card {
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    padding: 16px;
    margin-bottom: 12px;
}

.response-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.response-date {
    font-size: 12px;
    color: var(--text-tertiary);
    margin-left: auto;
}

.tone-badge {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
}
.tone-badge.positive { background: var(--success-bg); color: var(--success); }
.tone-badge.negative { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
.tone-badge.neutral { background: var(--bg-secondary); color: var(--text-tertiary); }

.response-prompt, .response-text {
    font-size: 13px;
    color: var(--text-secondary);
    margin-bottom: 12px;
    line-height: 1.5;
}
.response-prompt strong, .response-text strong {
    color: var(--text-tertiary);
    font-weight: 500;
}

.response-metrics {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.metric {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.metric-label {
    font-size: 10px;
    color: var(--text-tertiary);
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.metric-value {
    font-size: 14px;
    font-weight: 600;
    font-family: 'JetBrains Mono', monospace;
}
.metric-value.good { color: var(--success); }
.metric-value.medium { color: var(--warning); }
.metric-value.bad { color: var(--danger); }

/* Quality Stats */
.quality-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.quality-card {
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    padding: 16px;
}

.quality-label {
    font-size: 13px;
    color: var(--text-secondary);
    margin-bottom: 8px;
}

.quality-value {
    font-size: 24px;
    font-weight: 700;
    font-family: 'JetBrains Mono', monospace;
    color: var(--accent-primary);
    margin-bottom: 8px;
}

.quality-bar {
    height: 6px;
    background: var(--bg-secondary);
    border-radius: 3px;
    overflow: hidden;
}

.quality-bar-fill {
    height: 100%;
    background: var(--accent-gradient);
    border-radius: 3px;
}

/* Prompt Card */
.prompt-card {
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    padding: 14px;
    margin-bottom: 10px;
}

.prompt-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.prompt-date {
    font-size: 12px;
    color: var(--text-tertiary);
    margin-left: auto;
}

.prompt-text {
    font-size: 13px;
    color: var(--text-secondary);
    line-height: 1.5;
    margin-bottom: 10px;
}

.prompt-metrics {
    display: flex;
    gap: 16px;
}

.metric-mini {
    display: flex;
    align-items: center;
    gap: 6px;
}
.metric-mini .metric-label {
    font-size: 11px;
}
.metric-mini .metric-value {
    font-size: 13px;
    color: var(--accent-primary);
}

/* Sources Stats */
.sources-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.stat-card {
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    padding: 20px;
    text-align: center;
}
.stat-card.warning {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.2);
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    font-family: 'JetBrains Mono', monospace;
    color: var(--text-primary);
    margin-bottom: 4px;
}
.stat-card.warning .stat-value { color: var(--danger); }

.stat-label {
    font-size: 12px;
    color: var(--text-tertiary);
}

/* Distribution */
.distribution-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

.distribution-card {
    background: var(--bg-tertiary);
    border-radius: var(--radius-md);
    padding: 16px;
}
.distribution-card h4 {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 12px;
}

.distribution-item {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid var(--border-subtle);
}
.distribution-item:last-child { border-bottom: none; }

.dist-label { font-size: 13px; color: var(--text-secondary); }
.dist-value { font-size: 13px; font-weight: 600; color: var(--text-primary); font-family: 'JetBrains Mono', monospace; }

/* Weak Sources */
.warning-title { color: var(--danger); }
.warning-title svg { width: 18px; height: 18px; stroke: var(--danger); }

.weak-sources-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.weak-source-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    background: rgba(239, 68, 68, 0.05);
    border: 1px solid rgba(239, 68, 68, 0.15);
    border-radius: var(--radius-md);
}

.weak-domain {
    font-size: 13px;
    color: var(--text-secondary);
}

.weak-eeat {
    font-size: 14px;
    font-weight: 600;
    font-family: 'JetBrains Mono', monospace;
    color: var(--danger);
}

/* Table styles */
.sources-table-wrap {
    overflow-x: auto;
}
.domain-link {
    color: var(--accent-primary);
    text-decoration: none;
}
.domain-link:hover { text-decoration: underline; }

.mono {
    font-family: 'JetBrains Mono', monospace;
}

.type-badge {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
}
.type-badge.gov { background: rgba(99, 102, 241, 0.15); color: #818cf8; }
.type-badge.media { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
.type-badge.analytics { background: rgba(139, 92, 246, 0.15); color: #a78bfa; }
.type-badge.wiki { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }

.eeat-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
    font-family: 'JetBrains Mono', monospace;
}
.eeat-badge.high { background: var(--success-bg); color: var(--success); }
.eeat-badge.medium { background: rgba(245, 158, 11, 0.15); color: var(--warning); }
.eeat-badge.low { background: rgba(239, 68, 68, 0.15); color: var(--danger); }

/* Rank cells */
.rank-cell { font-weight: 600; color: var(--text-tertiary); }
.rank-cell.high { color: var(--success); }
.rank-cell.medium { color: var(--warning); }

/* LLM Badge */
.llm-badge {
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
}
.llm-badge.chatgpt, .llm-badge.gpt-4, .llm-badge.gpt { background: rgba(16, 163, 127, 0.15); color: #10a37f; }
.llm-badge.claude { background: rgba(204, 150, 92, 0.15); color: #cc965c; }
.llm-badge.gemini { background: rgba(66, 133, 244, 0.15); color: #4285f4; }
.llm-badge.perplexity { background: rgba(32, 178, 170, 0.15); color: #20b2aa; }
.llm-badge.deepseek { background: rgba(99, 102, 241, 0.15); color: #6366f1; }
.llm-badge.models-count { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }
.responses-count {
    font-size: 12px;
    color: var(--text-secondary);
    margin-left: 8px;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 48px 20px;
    color: var(--text-tertiary);
}
.empty-state svg {
    width: 48px;
    height: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}
.empty-state p {
    font-size: 14px;
}

/* Responsive */
@media (max-width: 1000px) {
    .hero-top { flex-direction: column; }
    .hero-stats { grid-template-columns: repeat(2, 1fr); }
    .quality-stats { grid-template-columns: 1fr; }
    .sources-stats-grid { grid-template-columns: 1fr; }
    .distribution-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .topic-tabs { flex-direction: column; }
    .hero-stats { grid-template-columns: 1fr; }
}
</style>

<?php if ($activeTab === 'overview'): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const radarCanvas = document.getElementById('promptQualityRadar');
    if (!radarCanvas) return;

    const radarData = <?= json_encode($tabData['radarData'] ?? [
        'neutrality' => 75,
        'stability' => 80,
        'soundness' => 85,
        'antiHallucination' => 70
    ]) ?>;

    new Chart(radarCanvas, {
        type: 'radar',
        data: {
            labels: [
                'Нейтральность',
                'Стабильность',
                'Логичность',
                'Защита от галлюцинаций'
            ],
            datasets: [{
                label: 'Оценка промтов',
                data: [
                    radarData.neutrality || 75,
                    radarData.stability || 80,
                    radarData.soundness || 85,
                    radarData.antiHallucination || 70
                ],
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                borderColor: 'rgb(16, 185, 129)',
                borderWidth: 2,
                pointBackgroundColor: 'rgb(16, 185, 129)',
                pointBorderColor: isLightTheme() ? '#1a1c22' : '#fff',
                pointHoverBackgroundColor: isLightTheme() ? '#1a1c22' : '#fff',
                pointHoverBorderColor: 'rgb(16, 185, 129)',
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 0,
                    bottom: 0,
                    left: 40,
                    right: 40
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100,
                    min: 0,
                    ticks: {
                        stepSize: 20,
                        font: { size: 10 },
                        color: '#9ca3af',
                        backdropColor: 'transparent'
                    },
                    grid: {
                        color: 'rgba(156, 163, 175, 0.2)'
                    },
                    angleLines: {
                        color: 'rgba(156, 163, 175, 0.2)'
                    },
                    pointLabels: {
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        color: '#e5e7eb',
                        padding: 15
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});

// Load More functionality
document.getElementById('loadMoreOverview')?.addEventListener('click', async function() {
    const btn = this;
    const topicId = btn.dataset.topicId;
    const offset = parseInt(btn.dataset.offset);
    const tab = btn.dataset.tab;

    // Show loading state
    btn.disabled = true;
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.btn-loader').style.display = 'inline-flex';

    try {
        const response = await fetch(`/api/topics/${topicId}/data?tab=${tab}&offset=${offset}&limit=10`);
        const result = await response.json();

        if (result.success && result.data.prompts) {
            const tbody = document.getElementById('overviewPromptsBody');
            const currentRows = tbody.querySelectorAll('tr').length;

            result.data.prompts.forEach((prompt, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="prompt-cell">
                        <span class="prompt-number">№${currentRows + index + 1}</span>
                        <a href="#" class="prompt-link">${escapeHtml(prompt.shortText)}</a>
                    </td>
                    <td class="score-cell">${prompt.neutrality || prompt.coherence || 0}%</td>
                    <td class="score-cell">${prompt.stability || prompt.consistency || 0}%</td>
                    <td class="score-cell">${prompt.soundness || prompt.fluency || 0}%</td>
                    <td class="score-cell">${prompt.antiHallucination || prompt.relevance || 0}%</td>
                    <td class="score-cell">${prompt.avgScore || 0}%</td>
                `;
                tbody.appendChild(row);
            });

            // Update offset for next load
            btn.dataset.offset = offset + 10;

            // Hide button if no more data
            if (!result.data.hasMore) {
                btn.parentElement.style.display = 'none';
            }
        }
    } catch (error) {
        console.error('Error loading more data:', error);
    } finally {
        // Reset button state
        btn.disabled = false;
        btn.querySelector('.btn-text').style.display = 'inline';
        btn.querySelector('.btn-loader').style.display = 'none';
    }
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
<?php endif; ?>

<?php if ($activeTab === 'responses'): ?>
<script>
document.getElementById('loadMoreResponses')?.addEventListener('click', async function() {
    const btn = this;
    const topicId = btn.dataset.topicId;
    const offset = parseInt(btn.dataset.offset);

    btn.disabled = true;
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.btn-loader').style.display = 'inline-flex';

    try {
        const response = await fetch(`/api/topics/${topicId}/data?tab=responses&offset=${offset}&limit=10`);
        const result = await response.json();

        if (result.success && result.data.responses) {
            const list = document.getElementById('responsesList');

            result.data.responses.forEach(r => {
                const card = document.createElement('div');
                card.className = 'response-card';
                card.innerHTML = `
                    <div class="response-header">
                        <span class="llm-badge ${r.model.toLowerCase()}">${escapeHtml(r.model)}</span>
                        <span class="response-date">${r.date}</span>
                        <span class="tone-badge ${r.tone}">${r.tone.charAt(0).toUpperCase() + r.tone.slice(1)}</span>
                    </div>
                    <div class="response-prompt"><strong>Промт:</strong> ${escapeHtml(r.prompt)}</div>
                    <div class="response-text"><strong>Ответ:</strong> ${escapeHtml(r.response)}</div>
                    <div class="response-metrics">
                        <div class="metric"><span class="metric-label">Связность</span><span class="metric-value ${r.coherence >= 70 ? 'good' : (r.coherence >= 50 ? 'medium' : 'bad')}">${r.coherence}</span></div>
                        <div class="metric"><span class="metric-label">Согласов.</span><span class="metric-value ${r.consistency >= 70 ? 'good' : (r.consistency >= 50 ? 'medium' : 'bad')}">${r.consistency}</span></div>
                        <div class="metric"><span class="metric-label">Беглость</span><span class="metric-value ${r.fluency >= 70 ? 'good' : (r.fluency >= 50 ? 'medium' : 'bad')}">${r.fluency}</span></div>
                        <div class="metric"><span class="metric-label">Релевант.</span><span class="metric-value ${r.relevance >= 70 ? 'good' : (r.relevance >= 50 ? 'medium' : 'bad')}">${r.relevance}</span></div>
                    </div>
                `;
                list.appendChild(card);
            });

            btn.dataset.offset = offset + 10;
            if (!result.data.hasMore) btn.parentElement.style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading more:', error);
    } finally {
        btn.disabled = false;
        btn.querySelector('.btn-text').style.display = 'inline';
        btn.querySelector('.btn-loader').style.display = 'none';
    }
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
<?php endif; ?>

<?php if ($activeTab === 'prompts'): ?>
<script>
document.getElementById('loadMorePrompts')?.addEventListener('click', async function() {
    const btn = this;
    const topicId = btn.dataset.topicId;
    const offset = parseInt(btn.dataset.offset);

    btn.disabled = true;
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.btn-loader').style.display = 'inline-flex';

    try {
        const response = await fetch(`/api/topics/${topicId}/data?tab=prompts&offset=${offset}&limit=10`);
        const result = await response.json();

        if (result.success && result.data.prompts) {
            const list = document.getElementById('promptsList');

            result.data.prompts.forEach(p => {
                const card = document.createElement('div');
                card.className = 'prompt-card';
                card.innerHTML = `
                    <div class="prompt-header">
                        <span class="llm-badge models-count">${p.modelsCount || 1} LLM</span>
                        <span class="responses-count">${p.responsesCount || 1} ответов</span>
                        <span class="prompt-date">${p.date}</span>
                    </div>
                    <div class="prompt-text">${escapeHtml(p.text)}</div>
                    <div class="prompt-metrics">
                        <div class="metric-mini"><span class="metric-label">Связн.</span><span class="metric-value">${p.coherence || 0}</span></div>
                        <div class="metric-mini"><span class="metric-label">Согл.</span><span class="metric-value">${p.consistency || 0}</span></div>
                        <div class="metric-mini"><span class="metric-label">Бегл.</span><span class="metric-value">${p.fluency || 0}</span></div>
                        <div class="metric-mini"><span class="metric-label">Релев.</span><span class="metric-value">${p.relevance || 0}</span></div>
                    </div>
                `;
                list.appendChild(card);
            });

            btn.dataset.offset = offset + 10;
            if (!result.data.hasMore) btn.parentElement.style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading more:', error);
    } finally {
        btn.disabled = false;
        btn.querySelector('.btn-text').style.display = 'inline';
        btn.querySelector('.btn-loader').style.display = 'none';
    }
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
<?php endif; ?>

<?php if ($activeTab === 'sources'): ?>
<script>
document.getElementById('loadMoreSources')?.addEventListener('click', async function() {
    const btn = this;
    const topicId = btn.dataset.topicId;
    const offset = parseInt(btn.dataset.offset);

    btn.disabled = true;
    btn.querySelector('.btn-text').style.display = 'none';
    btn.querySelector('.btn-loader').style.display = 'inline-flex';

    const typeLabels = {gov: 'Гос. сайты', media: 'СМИ', analytics: 'Аналитика', wiki: 'Wiki'};

    try {
        const response = await fetch(`/api/topics/${topicId}/data?tab=sources&offset=${offset}&limit=10`);
        const result = await response.json();

        if (result.success && result.data.sources) {
            const tbody = document.getElementById('sourcesBody');

            result.data.sources.forEach(s => {
                const row = document.createElement('tr');
                const eeatClass = s.eeat >= 80 ? 'high' : (s.eeat >= 50 ? 'medium' : 'low');
                row.innerHTML = `
                    <td><a href="https://${escapeHtml(s.domain)}" target="_blank" class="domain-link">${escapeHtml(s.domain)}</a></td>
                    <td><span class="type-badge ${s.type}">${typeLabels[s.type] || s.type}</span></td>
                    <td>${s.country}</td>
                    <td class="mono">${s.experience}</td>
                    <td class="mono">${s.expertise}</td>
                    <td class="mono">${s.authority}</td>
                    <td class="mono">${s.trust}</td>
                    <td><span class="eeat-badge ${eeatClass}">${s.eeat}</span></td>
                `;
                tbody.appendChild(row);
            });

            btn.dataset.offset = offset + 10;
            if (!result.data.hasMore) btn.parentElement.style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading more:', error);
    } finally {
        btn.disabled = false;
        btn.querySelector('.btn-text').style.display = 'inline';
        btn.querySelector('.btn-loader').style.display = 'none';
    }
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
<?php endif; ?>

<!-- Topic Gauge Animation (works on all tabs) -->
<script>
(function() {
    function initTopicGaugeAnimation() {
        const needle = document.getElementById('topicAccuracyNeedle');
        const valueText = document.getElementById('topicGaugeValue');
        if (!needle || !valueText) return;

        const targetValue = parseInt(valueText.textContent) || 0;
        const startAngle = -90;
        const targetAngle = -90 + (targetValue / 100) * 180;

        let startTime = null;
        const duration = 1500;

        function easeOutCubic(t) {
            return 1 - Math.pow(1 - t, 3);
        }

        function animateNeedle(currentTime) {
            if (!startTime) startTime = currentTime;
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            const easedProgress = easeOutCubic(progress);
            const currentAngle = startAngle + (targetAngle - startAngle) * easedProgress;
            const currentValue = Math.round(targetValue * easedProgress);

            needle.setAttribute('transform', `rotate(${currentAngle}, 160, 160)`);
            valueText.textContent = currentValue;

            if (progress < 1) {
                requestAnimationFrame(animateNeedle);
            } else {
                valueText.textContent = targetValue;
            }
        }

        needle.setAttribute('transform', `rotate(${startAngle}, 160, 160)`);
        valueText.textContent = '0';

        setTimeout(() => {
            requestAnimationFrame(animateNeedle);
        }, 300);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTopicGaugeAnimation);
    } else {
        initTopicGaugeAnimation();
    }
})();
</script>
