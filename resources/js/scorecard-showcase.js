const root = document.querySelector('[data-scorecard-showcase]');

if (root) {
    const dimensions = [
        { key: 'schedule', label: 'Schedule performance', weight: 25 },
        { key: 'cost', label: 'Cost performance', weight: 25 },
        { key: 'quality', label: 'Quality compliance', weight: 20 },
        { key: 'safety', label: 'Safety performance', weight: 15 },
        { key: 'resources', label: 'Resource availability', weight: 10 },
        { key: 'risk', label: 'Risk management', weight: 5 },
    ];
    const scenarioDimensions = {
        healthy: [92, 89, 94, 96, 91, 90],
        atrisk: [72, 76, 91, 88, 74, 62],
        critical: [48, 57, 72, 35, 54, 31],
    };
    const scenarios = {
        healthy: { label: 'Healthy', modifier: 12, progress: 64, planned: 64, cost: 0.99, exposure: 'Moderate', actions: 3 },
        atrisk: { label: 'At risk', modifier: 0, progress: 58, planned: 64, cost: 0.94, exposure: 'Elevated', actions: 8 },
        critical: { label: 'Critical', modifier: -18, progress: 43, planned: 64, cost: 0.82, exposure: 'High', actions: 16 },
    };
    const projects = {
        office: { name: 'Commercial office · Nairobi', type: 'Commercial office', progress: 58, planned: 64, budget: 840000000, weeks: 38 },
        residential: { name: 'Residential development · Kiambu', type: 'Residential development', progress: 67, planned: 70, budget: 1260000000, weeks: 31 },
        healthcare: { name: 'Healthcare expansion · Nakuru', type: 'Healthcare facility', progress: 49, planned: 56, budget: 575000000, weeks: 45 },
    };
    const periods = { nov: 'November 2026', oct: 'October 2026', sep: 'September 2026' };
    const cards = {
        executive: { title: 'Executive Project Health Scorecard', kicker: 'EXECUTIVE PROJECT HEALTH', description: 'One view of progress, cost, quality, safety and delivery exposure.', chart: 'Planned vs actual progress', side: 'Health trend · six periods', kind: 'line', measures: [['Overall progress', 'progress'], ['Schedule variance', 'variance'], ['Cost performance', 'cpi'], ['Quality compliance', 'quality'], ['Safety compliance', 'safety'], ['Risk exposure', 'exposure']] },
        schedule: { title: 'Schedule & Progress Performance', kicker: 'DELIVERY AGAINST BASELINE', description: 'Track progress, overdue work and schedule pressure before it reaches the completion date.', chart: 'Planned vs actual progress', side: 'Construction phase timeline', kind: 'gantt', measures: [['Planned completion', 'planned'], ['Actual completion', 'progress'], ['Schedule variance', 'variance'], ['Schedule Performance Index', 'spi'], ['Milestones achieved', 'milestones'], ['Overdue activities', 'overdue'], ['Critical path items', 'critical'], ['Forecast completion', 'forecast']] },
        cost: { title: 'Cost & Budget Health', kicker: 'EARNED VALUE & FORECAST', description: 'Compare work completed with planned value and actual spend using earned-value indicators.', chart: 'Cumulative value & actual cost', side: 'Cost distribution', kind: 'cost', measures: [['Approved budget', 'budget'], ['Revised budget', 'revised'], ['Actual cost to date', 'actual'], ['Earned value', 'earned'], ['Planned value', 'plannedvalue'], ['Cost variance', 'cv'], ['Cost Performance Index', 'cpi'], ['Estimate at Completion', 'eac']] },
        quality: { title: 'Quality Assurance & Compliance', kicker: 'INSPECTION & DEFECT CONTROL', description: 'Monitor inspection outcomes, non-conformance and defect closure by delivery stage.', chart: 'Quality compliance · six periods', side: 'Inspection outcomes', kind: 'quality', measures: [['Quality compliance', 'quality'], ['Inspections completed', 'inspections'], ['Inspection pass rate', 'pass'], ['Open NCRs', 'ncr'], ['Open defects', 'defects'], ['Rework rate', 'rework'], ['Average close time', 'close'], ['Outstanding approvals', 'approvals']] },
        safety: { title: 'Health, Safety & Environment', kicker: 'SITE SAFETY & ENVIRONMENT', description: 'Make safety controls, site observations and corrective actions visible to delivery leaders.', chart: 'Safety performance · six periods', side: 'Corrective actions', kind: 'safety', measures: [['Safety compliance', 'safety'], ['Hours worked', 'hours'], ['Lost Time Injuries', 'lti'], ['Near-miss reports', 'nearmiss'], ['Toolbox talks', 'toolbox'], ['Safety inspections', 'safetychecks'], ['Actions outstanding', 'safetyactions'], ['Environmental compliance', 'environment']] },
        resources: { title: 'Procurement & Resource Health', kicker: 'MATERIALS · PEOPLE · EQUIPMENT', description: 'See how material availability, workforce and suppliers may affect planned delivery.', chart: 'Resource availability trend', side: 'Procurement status', kind: 'resources', measures: [['Material availability', 'materials'], ['Procurement fulfilment', 'procurement'], ['Supplier performance', 'supplier'], ['Labour productivity', 'productivity'], ['Workforce utilisation', 'utilisation'], ['Equipment availability', 'equipment'], ['Delayed deliveries', 'deliveries'], ['Outstanding orders', 'orders']] },
        risk: { title: 'Risk & Issue Management', kicker: 'EXPOSURE · MITIGATION · OWNERSHIP', description: 'Prioritise exposure and make mitigation ownership and issue ageing easier to review.', chart: 'Risk exposure · six periods', side: 'Interactive 5 × 5 risk heatmap', kind: 'risk', measures: [['Active risks', 'risks'], ['High-priority risks', 'highrisks'], ['Critical unresolved issues', 'issues'], ['Mitigation complete', 'mitigation'], ['Average issue resolution', 'resolution'], ['Emerging risk trend', 'trend'], ['Actions overdue', 'overdueactions'], ['Exposure level', 'exposure']] },
        portfolio: { title: 'Integrated Project Portfolio Health', kicker: 'MULTI-PROJECT OVERSIGHT', description: 'Compare a fictional portfolio across delivery, budget, completion and risk exposure.', chart: 'Portfolio performance comparison', side: 'Portfolio health distribution', kind: 'portfolio', measures: [['Projects monitored', 'projects'], ['On track', 'ontrack'], ['At risk', 'atrisk'], ['Critical', 'critical'], ['Portfolio budget', 'portbudget'], ['Average completion', 'avgprogress'], ['Risk exposure', 'exposure'], ['Overall performance', 'score']] },
    };
    const riskRegister = [
        { title: 'Structural steel delivery', likelihood: 4, impact: 4, owner: 'Procurement lead', mitigation: 'Confirm supplier production slot and agree a dated recovery schedule.', status: 'Mitigation in progress' },
        { title: 'Unexpected ground conditions', likelihood: 2, impact: 5, owner: 'Project manager', mitigation: 'Keep contingency allowance and complete targeted ground verification.', status: 'Under review' },
        { title: 'Materials price escalation', likelihood: 3, impact: 4, owner: 'Commercial lead', mitigation: 'Validate quotations and secure prices for critical long-lead materials.', status: 'Mitigation in progress' },
        { title: 'Design approval delays', likelihood: 3, impact: 3, owner: 'Design coordinator', mitigation: 'Maintain an approval register with named reviewers and due dates.', status: 'Open' },
        { title: 'Specialist subcontractor capacity', likelihood: 2, impact: 4, owner: 'Construction manager', mitigation: 'Confirm resource plan and identify an alternate qualified team.', status: 'Monitoring' },
    ];
    const state = { scenario: 'atrisk', card: 'executive', project: 'office', period: 'nov', calculator: [...scenarioDimensions.atrisk], safeOverride: false, resourceAllocation: 74 };
    const $ = (selector) => root.querySelector(selector);
    const $$ = (selector) => [...root.querySelectorAll(selector)];
    const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[character]);
    const clamp = (value, min = 0, max = 100) => Math.min(max, Math.max(min, value));
    const money = (value) => `KES ${(value / 1000000).toLocaleString('en-KE', { maximumFractionDigits: 0 })}m`;
    const activeProject = () => projects[state.project];
    const actualProgress = () => scenarios[state.scenario].progress + (state.project === 'residential' ? 9 : state.project === 'healthcare' ? -9 : 0) - (state.period === 'oct' ? 7 : state.period === 'sep' ? 14 : 0);
    const currentScores = () => scenarioDimensions[state.scenario].map((score) => clamp(score + (state.project === 'residential' ? 3 : state.project === 'healthcare' ? -4 : 0) + (state.period === 'sep' ? -3 : state.period === 'oct' ? -1 : 0)));
    const weightedScore = (scores) => Math.round(scores.reduce((total, score, index) => total + score * dimensions[index].weight / 100, 0));
    const healthLabel = (score) => score >= 85 ? 'HEALTHY' : score >= 70 ? 'AT RISK' : 'CRITICAL';
    const healthClass = (score) => score >= 85 ? 'healthy' : score >= 70 ? 'amber' : 'critical';
    const setGauge = (element, score, label = 'Project health score') => {
        element.style.setProperty('--gauge-score', `${clamp(score) * 3.6}deg`);
        element.dataset.health = healthClass(score);
        element.setAttribute('role', 'img');
        element.setAttribute('aria-label', `${label}: ${Math.round(score)} out of 100`);
    };
    const metricValue = (key, scores, score) => {
        const project = activeProject();
        const scenario = scenarios[state.scenario];
        const progress = actualProgress();
        const earned = project.budget * progress / 100;
        const actual = earned / scenario.cost;
        const plannedValue = project.budget * project.planned / 100;
        const values = {
            progress: `${progress}%`,
            variance: `${project.planned - progress > 0 ? '−' : '+'}${Math.abs(project.planned - progress)} pp`,
            cpi: scenario.cost.toFixed(2),
            quality: `${scores[2]}%`, safety: `${scores[3]}%`, exposure: scenario.exposure,
            planned: `${project.planned}%`, spi: (progress / project.planned).toFixed(2),
            milestones: `${Math.max(1, 8 - scenario.actions / 2)} / 10`, overdue: `${Math.round(scenario.actions * 0.75)} activities`,
            critical: `${Math.max(2, Math.round(scenario.actions / 2))} activities`, forecast: state.scenario === 'healthy' ? 'Dec 2026' : state.scenario === 'critical' ? 'Jun 2027' : 'Feb 2027',
            budget: money(project.budget), revised: money(project.budget * (state.scenario === 'critical' ? 1.08 : 1.03)), actual: money(actual), earned: money(earned), plannedvalue: money(plannedValue),
            cv: money(earned - actual), eac: money(project.budget / Math.max(.5, scenario.cost)),
            inspections: `${Math.round(scores[2] * .72)} completed`, pass: `${Math.max(60, scores[2] - 3)}%`, ncr: `${Math.max(2, 14 - Math.round(scores[2] / 10))} open`, defects: `${Math.max(4, 24 - Math.round(scores[2] / 6))} open`,
            rework: `${(100 - scores[2]) / 10}%`, close: `${Math.max(2, 12 - Math.round(scores[2] / 10))} days`, approvals: `${Math.max(2, 10 - Math.round(scores[2] / 12))} pending`,
            hours: '186,400 hrs', lti: state.scenario === 'critical' ? '1 incident' : '0 incidents', nearmiss: `${Math.round((100 - scores[3]) / 8) + 3} reported`, toolbox: '42 / 48', safetychecks: '36 completed', safetyactions: `${Math.max(1, 12 - Math.round(scores[3] / 10))} open`, environment: `${clamp(scores[3] + 4)}%`,
            materials: `${scores[4]}%`, procurement: `${clamp(scores[4] + 4)}%`, supplier: `${clamp(scores[4] + 6)} / 100`, productivity: `${clamp(scores[4] - 2)}%`, utilisation: `${state.resourceAllocation}%`, equipment: `${clamp(scores[4] + 8)}%`, deliveries: `${Math.round((100 - scores[4]) / 8)} delayed`, orders: `${Math.round((100 - scores[4]) / 5)} pending`,
            risks: `${scenario.actions + 4} active`, highrisks: `${Math.ceil(scenario.actions / 2)} high`, issues: `${Math.max(1, Math.round(scenario.actions / 4))} critical`, mitigation: `${clamp(scores[5] + 18)}% complete`, resolution: `${Math.max(3, 14 - Math.round(scores[5] / 10))} days`, trend: state.scenario === 'critical' ? 'Increasing' : 'Stable', overdueactions: `${Math.ceil(scenario.actions / 3)} overdue`,
            projects: '5 projects', ontrack: `${state.scenario === 'healthy' ? 4 : 2} projects`, atrisk: `${state.scenario === 'critical' ? 1 : 2} projects`, critical: `${state.scenario === 'critical' ? 2 : 1} project`, portbudget: 'KES 4.2bn', avgprogress: `${Math.round((scores[0] + scores[1] + scores[2]) / 3)}%`, score: `${score} / 100`,
        };
        return values[key] ?? 'Not available';
    };
    const trendSeries = (score) => {
        const current = state.card === 'executive' || state.card === 'schedule' ? actualProgress() : state.card === 'quality' ? currentScores()[2] : state.card === 'safety' ? currentScores()[3] : state.card === 'risk' ? 100 - currentScores()[5] : state.card === 'cost' ? Math.round(scenarios[state.scenario].cost * 100) : score;
        return [-30, -24, -18, -12, -6, 0].map((offset) => clamp(current + offset));
    };
    const lineChart = (score) => {
        const actual = trendSeries(score);
        const plannedTarget = state.card === 'executive' || state.card === 'schedule' ? activeProject().planned : state.card === 'risk' ? 20 : 100;
        const planned = [-30, -24, -18, -12, -6, 0].map((offset) => clamp(plannedTarget + offset));
        const x = (index) => 60 + index * 112;
        const y = (value) => 210 - value * 1.55;
        const path = (values) => values.map((value, index) => `${index ? 'L' : 'M'} ${x(index)} ${y(value)}`).join(' ');
        return `<svg viewBox="0 0 660 250" role="img" aria-label="Monthly ${escapeHtml(cards[state.card].chart.toLowerCase())}, planned baseline and actual performance"><g class="chart-gridlines">${[0, 1, 2, 3].map((index) => `<line x1="46" x2="625" y1="${40 + index * 52}" y2="${40 + index * 52}"/><text x="8" y="${44 + index * 52}">${100 - index * 25}%</text>`).join('')}</g><path class="chart-planned" d="${path(planned)}"/><path class="chart-actual" d="${path(actual)}"/>${actual.map((value, index) => `<circle class="chart-point" cx="${x(index)}" cy="${y(value)}" r="4"><title>${['Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'][index]}: actual ${value}%, planned ${planned[index]}%</title></circle>`).join('')}<g class="chart-months">${['Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'].map((month, index) => `<text x="${x(index)}" y="238">${month}</text>`).join('')}</g></svg><div class="health-chart-legend"><span><i class="legend-planned"></i>Planned baseline</span><span><i class="legend-actual"></i>Actual performance</span></div>`;
    };
    const miniChart = (score) => `<div class="health-mini-trend">${trendSeries(score).map((value, index) => `<span title="Period ${index + 1}: ${value} points"><i style="height:${value}%"></i></span>`).join('')}</div><p class="health-chart-footnote">Six-period trend · illustrative index</p>`;
    const ganttChart = (score) => `<div class="health-gantt">${[['Site preparation', 8, 6], ['Foundations', 24, 21], ['Structure', 40, 34], ['Roofing', 58, 52], ['MEP installations', 70, 64], ['Finishes', 82, 76], ['Testing & handover', 94, 90]].map(([label, start, progress]) => `<div class="gantt-row"><span>${label}</span><i><b style="left:${start}%;width:${Math.max(8, progress - start)}%"></b><em style="left:${start}%;width:${Math.max(8, progress - start + (score < 65 ? 13 : 5))}%"></em></i></div>`).join('')}<div class="health-chart-legend"><span><i class="legend-planned"></i>Planned</span><span><i class="legend-actual"></i>Actual; shaded gap indicates delay</span></div></div>`;
    const costChart = (score) => {
        const project = activeProject();
        const scenario = scenarios[state.scenario];
        const baseline = project.budget * project.planned / 100;
        const progress = actualProgress();
        const earned = project.budget * progress / 100;
        const actual = earned / scenario.cost;
        const rows = [['Planned value', baseline], ['Earned value', earned], ['Actual cost', actual]];
        const max = Math.max(...rows.map((row) => row[1]));
        return `<div class="health-value-bars">${rows.map(([label, value]) => `<div><span>${label}</span><i><b style="width:${Math.round(value / max * 100)}%"></b></i><strong>${money(value)}</strong></div>`).join('')}</div><p class="health-chart-footnote">CV = earned value − actual cost · CPI = earned value ÷ actual cost (${(earned / Math.max(1, actual)).toFixed(2)})</p>`;
    };
    const heatmap = () => `<div class="health-risk-heatmap" role="group" aria-label="Risk likelihood and impact heatmap">${Array.from({ length: 25 }, (_, index) => { const likelihood = Math.floor(index / 5) + 1; const impact = index % 5 + 1; const count = riskRegister.filter((risk) => risk.likelihood === likelihood && risk.impact === impact).length; const rating = likelihood * impact; return `<button type="button" class="risk-cell risk-${rating >= 16 ? 'high' : rating >= 8 ? 'medium' : 'low'}" data-risk-cell="${likelihood}-${impact}" aria-label="Likelihood ${likelihood}, impact ${impact}, ${count} sample risks">${count || ''}</button>`; }).join('')}</div><div class="health-risk-axis"><span>LOW LIKELIHOOD</span><span>IMPACT →</span></div><div class="health-risk-register">${riskRegister.map((risk, index) => `<button type="button" data-risk="${index}"><b>${escapeHtml(risk.title)}</b><span>${risk.likelihood} × ${risk.impact} · ${escapeHtml(risk.status)}</span></button>`).join('')}</div><p class="health-risk-detail" data-risk-detail aria-live="polite">Select a sample risk to view its owner and mitigation action.</p>`;
    const portfolioChart = (score) => {
        const samples = [['Office tower', score], ['Apartments', clamp(score + 8)], ['Healthcare', clamp(score - 10)], ['Warehouse', clamp(score + 4)], ['Mixed-use', clamp(score - 3)]];
        return `<div class="health-portfolio-bars">${samples.map(([label, value]) => `<div><span>${label}</span><i><b style="width:${value}%"></b></i><strong>${value}</strong></div>`).join('')}</div><div class="health-health-legend"><span>● Healthy 85–100</span><span>● At risk 70–84</span><span>● Critical &lt;70</span></div>`;
    };
    const renderDimensions = (scores) => {
        $('[data-dimensions]').innerHTML = dimensions.map((dimension, index) => `<div class="health-dimension"><span>${dimension.label}</span><i><b class="tone-${healthClass(scores[index])}" style="width:${scores[index]}%"></b></i><strong>${scores[index]}</strong></div>`).join('');
    };
    const renderInsights = (scores, score) => {
        const findings = [
            { title: 'Schedule is the first area to review', detail: `Reported progress is ${Math.abs(activeProject().planned - scenarios[state.scenario].progress)} percentage points ${scenarios[state.scenario].progress < activeProject().planned ? 'behind' : 'ahead of'} the example baseline.`, action: 'Validate critical-path logic and agree dated recovery actions with accountable owners.', priority: scores[0] < 60 ? 'High' : 'Watch', tone: scores[0] < 60 ? 'critical' : 'amber' },
            { title: 'Cost efficiency needs context', detail: `The example cost performance index is ${scenarios[state.scenario].cost.toFixed(2)}. An index below 1.00 means earned value is below actual cost.`, action: 'Reconcile committed cost, approved changes and forecast cost to complete.', priority: scenarios[state.scenario].cost < .9 ? 'High' : 'Watch', tone: scenarios[state.scenario].cost < .9 ? 'critical' : 'amber' },
            { title: 'Quality remains a relative strength', detail: `Quality compliance is ${scores[2]} points in this illustrative scenario; open defects still need dated ownership.`, action: 'Review open NCRs and confirm the closure evidence required at the next inspection.', priority: scores[2] < 70 ? 'High' : 'Monitor', tone: scores[2] < 70 ? 'critical' : 'healthy' },
        ];
        if (state.card === 'safety' || state.scenario === 'critical' || state.safeOverride) findings.unshift({ title: 'Safety requires active leadership attention', detail: state.safeOverride || state.scenario === 'critical' ? 'A critical safety condition or statutory non-compliance overrides the aggregate weighted score.' : 'Safety controls and open corrective actions should be reviewed with the site team.', action: 'Escalate the condition, confirm immediate controls and track corrective actions to verified closure.', priority: 'Urgent', tone: 'critical' });
        const card = cards[state.card];
        const recommendation = state.card === 'risk' ? 'Assign each high exposure a named owner, a dated mitigation and a review trigger.' : state.card === 'resources' ? 'Reconfirm long-lead material dates against the next critical work packages.' : state.card === 'quality' ? 'Prioritise defect closure and check repeat non-conformance trends by work package.' : state.card === 'portfolio' ? 'Review critical projects first, then validate whether shared resources or suppliers are creating portfolio-wide pressure.' : state.card === 'cost' ? 'Reconcile forecast cost to complete with approved scope, commitments and earned value.' : state.card === 'schedule' ? 'Refresh the near-term look-ahead and agree recovery ownership for critical-path activities.' : findings[0].action;
        const selected = findings[0];
        $('[data-insights]').innerHTML = `<article class="insight-main tone-border-${selected.tone}"><div><small>KEY FINDING</small><h5>${selected.title}</h5><p>${selected.detail}</p></div><span class="priority priority-${selected.tone}">${selected.priority} priority</span><div><small>WHY IT MATTERS</small><p>${score < 70 ? 'Several weak indicators can combine to threaten delivery, cost certainty or statutory obligations.' : 'A change caught early gives the project team more options and time to coordinate a response.'}</p></div><div><small>RECOMMENDED ACTION</small><p>${recommendation}</p></div></article><div class="insight-support"><span>RULE-BASED DEMONSTRATION INSIGHT</span><p>${escapeHtml(card.description)}</p><small>Interpretation depends on source data, agreed baselines and project context.</small></div>`;
    };
    const definitionsFor = (card, score) => {
        const common = `<p><b>Health score:</b> weighted illustrative index from 0 to 100; 85–100 Healthy, 70–84 At Risk, below 70 Critical.</p><p><b>SPI:</b> Earned Value ÷ Planned Value. Below 1.00 indicates less work earned than planned at the status date.</p><p><b>CPI:</b> Earned Value ÷ Actual Cost. Below 1.00 indicates cost efficiency is below plan for the value earned.</p><p><b>CV / SV:</b> Cost Variance = EV − AC; Schedule Variance = EV − PV. These are currency / earned-value amounts. Progress differences in this dashboard use percentage points (pp).</p><p><b>EAC:</b> Estimate at Completion = Budget at Completion ÷ CPI when current cost efficiency is assumed to continue.</p>`;
        const specifics = { risk: '<p><b>Risk rating:</b> Likelihood × impact on a 1–5 scale. Heatmap position is illustrative and does not replace a project risk assessment.</p>', quality: '<p><b>Quality compliance:</b> sample measure of inspection and documentation conformity; it is not a statutory certification.</p>', safety: '<p><b>Safety override:</b> serious incidents and statutory non-compliance warrant escalation regardless of weighted average.</p>', schedule: '<p><b>Schedule variance:</b> actual progress minus planned progress, expressed in percentage points at the same reporting cut-off.</p>' }[state.card] || '';
        return `<p><b>Current score:</b> ${score}/100. Figures depend on reporting cut-off, source records and agreed definitions.</p>${common}${specifics}`;
    };
    const render = () => {
        const scores = currentScores();
        const score = weightedScore(scores);
        const classification = healthLabel(score);
        const card = cards[state.card];
        const project = activeProject();
        const period = periods[state.period];
        const scenario = scenarios[state.scenario];
        const mainGauge = $('[data-main-gauge]');
        setGauge(mainGauge, score);
        $('[data-main-score]').textContent = score;
        $('[data-main-status]').textContent = classification;
        $('[data-main-status]').className = `status-${healthClass(score)}`;
        $('[data-main-project]').textContent = project.name;
        $('[data-main-period]').textContent = period;
        $('[data-progress]').innerHTML = `${actualProgress()}% <small>actual / ${project.planned}% planned</small>`;
        $('[data-card-kicker]').textContent = card.kicker;
        $('[data-card-title]').textContent = card.title;
        $('[data-card-description]').textContent = card.description;
        $('[data-chart-title]').textContent = card.chart;
        $('[data-chart-side-title]').textContent = card.side;
        $('[data-report-period]').textContent = period;
        $('[data-kpis]').innerHTML = card.measures.map(([label, key]) => `<article class="health-kpi"><span>${label}<button type="button" title="Illustrative measure. Select How to read these measures for definitions." aria-label="About ${label}">i</button></span><strong>${metricValue(key, scores, score)}</strong><small class="kpi-trend tone-${healthClass(score)}"><i></i>${key === 'variance' ? 'vs approved baseline' : key === 'cpi' || key === 'spi' ? 'earned-value indicator' : 'illustrative measure'}</small></article>`).join('');
        renderDimensions(scores);
        $('[data-chart-main]').innerHTML = card.kind === 'gantt' ? ganttChart(score) : card.kind === 'cost' ? costChart(score) : card.kind === 'risk' ? heatmap() : card.kind === 'portfolio' ? portfolioChart(score) : lineChart(score);
        $('[data-export-chart]').disabled = !$('[data-chart-main] svg');
        $('[data-chart-secondary]').innerHTML = card.kind === 'gantt' ? miniChart(scores[0]) : card.kind === 'cost' ? miniChart(scores[1]) : card.kind === 'risk' ? miniChart(scores[5]) : card.kind === 'portfolio' ? portfolioChart(score) : card.kind === 'quality' ? `<div class="health-donut" style="--donut-value:${scores[2] * 3.6}deg"><span><b>${scores[2]}%</b><small>compliance</small></span></div><div class="health-chart-legend"><span>● Compliant</span><span>● Requires review</span></div>` : card.kind === 'safety' ? `<div class="health-safety-banner ${state.scenario === 'critical' || state.safeOverride ? 'is-critical' : ''}"><b>${state.scenario === 'critical' || state.safeOverride ? 'CRITICAL SAFETY REVIEW' : 'SITE SAFETY STATUS'}</b><span>${state.scenario === 'critical' || state.safeOverride ? 'Immediate control verification required' : 'No lost time injury in this illustrative period'}</span><small>Always assess serious incidents and statutory obligations separately from the weighted score.</small></div>${miniChart(scores[3])}` : card.kind === 'resources' ? `<div class="health-value-bars">${['Materials', 'Workforce', 'Equipment', 'Supplier'].map((label, index) => `<div><span>${label}</span><i><b style="width:${[scores[4], clamp(scores[4] - 4), clamp(scores[4] + 8), clamp(scores[4] + 6)][index]}%"></b></i><strong>${[scores[4], clamp(scores[4] - 4), clamp(scores[4] + 8), clamp(scores[4] + 6)][index]}%</strong></div>`).join('')}</div><label class="health-resource-slider">Resource allocation <input type="range" min="40" max="100" value="${scores[4]}" data-resource-slider><span data-resource-output>${scores[4]}%</span></label>` : miniChart(score);
        if (card.kind === 'resources') {
            const workforceRow = $('[data-chart-secondary] .health-value-bars > div:nth-child(2)');
            $('[data-resource-slider]').value = state.resourceAllocation;
            $('[data-resource-output]').textContent = `${state.resourceAllocation}%`;
            workforceRow.querySelector('i b').style.width = `${state.resourceAllocation}%`;
            workforceRow.querySelector('strong').textContent = `${state.resourceAllocation}%`;
        }
        renderInsights(scores, score);
        $('[data-definitions]').innerHTML = definitionsFor(card, score);
        $('[data-paper-score]').innerHTML = `${score} <small>/ 100 · ${classification}</small>`;
        $('[data-paper-progress]').textContent = `${actualProgress()}%`;
        $('[data-paper-schedule]').textContent = `${project.planned - actualProgress() > 0 ? '−' : '+'}${Math.abs(project.planned - actualProgress())} pp`;
        $('[data-paper-cost]').textContent = `${scenario.cost.toFixed(2)} CPI`;
        $('[data-paper-summary]').textContent = score < 70 ? 'Performance indicators show material pressure across delivery, cost and risk. The project leadership team should validate immediate controls, confirm the forecast position and agree owners for recovery actions.' : score < 85 ? `Progress is ${project.planned - actualProgress()} percentage points behind the example baseline and cost efficiency is ${scenario.cost < 1 ? 'under pressure' : 'within plan'}. Quality performance remains comparatively strong. Validate the near-term recovery plan and resolve constraints affecting upcoming work.` : 'The illustrative project is performing within the example thresholds. Continue monitoring the critical path, maintain cost controls and close open quality actions to protect delivery performance.';
        const heroGauge = $('[data-hero-gauge]');
        setGauge(heroGauge, score, 'Illustrative project health score');
        $('[data-hero-score]').textContent = score;
        $('[data-hero-status]').textContent = classification;
        $('[data-hero-status]').className = `status-${healthClass(score)}`;
        $('[data-hero-project]').textContent = project.name;
        const scenarioIndex = { healthy: 0, atrisk: 1, critical: 2 }[state.scenario];
        $$('.health-scenarios button').forEach((button) => button.setAttribute('aria-pressed', String(button.dataset.scenario === state.scenario)));
        $$('[data-card]').forEach((button) => button.setAttribute('aria-selected', String(button.dataset.card === state.card)));
        const previewDimensions = scenarioDimensions[scenarioIndex];
        $$('.health-preview-bars b').forEach((bar, index) => { bar.style.width = `${previewDimensions[[0, 1, 2][index]]}%`; });
        $$('.health-preview-bars strong').forEach((label, index) => { label.textContent = previewDimensions[[0, 1, 2][index]]; });
        const scenarioScores = state.calculator;
        const calculatorScore = weightedScore(scenarioScores);
        setGauge($('[data-calculator-gauge]'), calculatorScore, 'Illustrative health score calculator');
        $('[data-calculator-score]').textContent = calculatorScore;
        $('[data-calculator-status]').textContent = state.safeOverride ? 'CRITICAL SAFETY OVERRIDE' : healthLabel(calculatorScore);
        $('[data-calculator-status]').className = state.safeOverride ? 'status-critical' : `status-${healthClass(calculatorScore)}`;
        $('[data-calculator-gauge]').dataset.health = state.safeOverride ? 'critical' : healthClass(calculatorScore);
        const weakestIndex = scenarioScores.indexOf(Math.min(...scenarioScores));
        $('[data-calculator-action]').textContent = state.safeOverride ? 'Escalate the safety condition before relying on the weighted average.' : `Focus first on the weakest dimension: ${dimensions[weakestIndex].label}.`;
        $('[data-safety-override]').hidden = !state.safeOverride;
        if (!$('[data-sliders] [data-dimension]')) {
            $('[data-sliders]').innerHTML = dimensions.map((dimension, index) => `<label class="health-slider-row ${index === weakestIndex ? 'is-weakest' : ''}"><span><b>${dimension.label}</b><small>Weight ${dimension.weight}%</small></span><input type="range" min="0" max="100" value="${scenarioScores[index]}" data-dimension="${index}" aria-label="${dimension.label}"><strong data-slider-value="${index}">${scenarioScores[index]}</strong></label>`).join('') + '<label class="health-override-control"><input type="checkbox" data-safe-override' + (state.safeOverride ? ' checked' : '') + '> Demonstrate a critical safety condition / statutory breach</label>';
        } else {
            dimensions.forEach((dimension, index) => {
                const slider = $(`[data-dimension="${index}"]`);
                slider.value = scenarioScores[index];
                slider.closest('.health-slider-row').classList.toggle('is-weakest', index === weakestIndex);
                $(`[data-slider-value="${index}"]`).textContent = scenarioScores[index];
            });
            $('[data-safe-override]').checked = state.safeOverride;
        }
        $('[data-period]').value = state.period;
        $('[data-project]').value = state.project;
    };
    const draw = () => {
        render();
        const dashboard = $('[data-dashboard]')?.closest('.health-dashboard');
        if (dashboard) {
            dashboard.classList.remove('is-refreshing');
            window.requestAnimationFrame(() => dashboard.classList.add('is-refreshing'));
            window.setTimeout(() => dashboard.classList.remove('is-refreshing'), 550);
        }
        window.history.replaceState(null, '', `${window.location.pathname}${window.location.search}#${state.card}`);
    };
    const initializeSectionReveals = () => {
        const sections = [...root.querySelectorAll(':scope > section')];
        if (!('IntersectionObserver' in window)) {
            sections.forEach((section) => section.classList.add('health-reveal', 'is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries, sectionObserver) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    sectionObserver.unobserve(entry.target);
                }
            });
        }, { rootMargin: '0px 0px -8% 0px', threshold: 0.06 });

        sections.forEach((section) => {
            section.classList.add('health-reveal');
            observer.observe(section);
        });
    };
    const stakeholderCopy = {
        owners: ['Owners & developers', 'Start with the executive health view, then follow material changes into cost, schedule and high-priority risks.', ['Executive health', 'Cost & budget', 'Risk & issues']],
        contractors: ['Contractors', 'Focus on near-term delivery constraints, resource readiness, quality closeout and safety actions.', ['Schedule', 'Resources', 'Quality', 'HSE']],
        managers: ['Project managers', 'Use the integrated view to coordinate baseline, forecast, open risks and accountable corrective actions.', ['Executive health', 'Schedule', 'Risk & issues']],
        qs: ['Quantity surveyors', 'Review earned value, commitments, forecast cost to complete and change exposure against the approved budget.', ['Cost & budget', 'Executive health']],
        engineers: ['Engineers & consultants', 'Track inspection evidence, design approvals, non-conformance closure and work-package readiness.', ['Quality', 'Schedule', 'HSE']],
        investors: ['Investors & financiers', 'Compare portfolio health, capital exposure, project completion and material exceptions.', ['Portfolio', 'Executive health', 'Risk & issues']],
    };
    const bind = () => {
        $$('[data-card]').forEach((button) => button.addEventListener('click', () => { state.card = button.dataset.card; draw(); }));
        $$('.health-scenarios button').forEach((button) => button.addEventListener('click', () => { state.scenario = button.dataset.scenario; state.calculator = [...scenarioDimensions[state.scenario]]; state.resourceAllocation = currentScores()[4]; draw(); }));
        $('[data-project]').addEventListener('change', (event) => { state.project = event.target.value; state.resourceAllocation = currentScores()[4]; draw(); });
        $('[data-period]').addEventListener('change', (event) => { state.period = event.target.value; draw(); });
        $$('[data-stakeholder]').forEach((button) => button.addEventListener('click', () => {
            const [title, description, recommendations] = stakeholderCopy[button.dataset.stakeholder];
            $$('[data-stakeholder]').forEach((candidate) => candidate.setAttribute('aria-pressed', String(candidate === button)));
            $('[data-stakeholder-detail]').innerHTML = `<small>RECOMMENDED STARTING VIEW</small><h3>${title}</h3><p>${description}</p><div>${recommendations.map((item) => `<span>${item}</span>`).join('')}</div>`;
        }));
        $('[data-sliders]').addEventListener('input', (event) => {
            if (event.target.matches('[data-dimension]')) {
                state.calculator[Number(event.target.dataset.dimension)] = Number(event.target.value);
                draw();
            }
        });
        $('[data-sliders]').addEventListener('change', (event) => {
            if (event.target.matches('[data-safe-override]')) { state.safeOverride = event.target.checked; draw(); }
        });
        $('[data-reset-calculator]').addEventListener('click', () => { state.calculator = [...scenarioDimensions.atrisk]; state.safeOverride = false; draw(); });
        $('[data-chart-main]').addEventListener('click', (event) => {
            const riskButton = event.target.closest('[data-risk]');
            if (riskButton) {
                const risk = riskRegister[Number(riskButton.dataset.risk)];
                $('[data-risk-detail]').innerHTML = `<b>${escapeHtml(risk.title)}</b> · ${risk.likelihood} likelihood × ${risk.impact} impact<br><span>Owner: ${escapeHtml(risk.owner)}. ${escapeHtml(risk.mitigation)} Status: ${escapeHtml(risk.status)}.</span>`;
            }
        });
        $('[data-chart-main]').addEventListener('click', (event) => {
            const button = event.target.closest('[data-risk-cell]');
            if (button) {
                const [likelihood, impact] = button.dataset.riskCell.split('-').map(Number);
                const matches = riskRegister.filter((risk) => risk.likelihood === likelihood && risk.impact === impact);
                $('[data-risk-detail]').textContent = matches.length ? `${matches.length} sample risk${matches.length > 1 ? 's' : ''} at likelihood ${likelihood} and impact ${impact}. Select a register entry for the action owner and mitigation.` : `No sample risks currently plotted at likelihood ${likelihood} and impact ${impact}.`;
            }
        });
        $('[data-chart-secondary]').addEventListener('input', (event) => {
            if (event.target.matches('[data-resource-slider]')) {
                state.resourceAllocation = Number(event.target.value);
                $('[data-resource-output]').textContent = `${state.resourceAllocation}% allocation`;
                const utilisationKpi = [...$('[data-kpis]').querySelectorAll('.health-kpi')].find((item) => item.querySelector('span')?.textContent.includes('Workforce utilisation'));
                if (utilisationKpi) utilisationKpi.querySelector('strong').textContent = `${state.resourceAllocation}%`;
                const workforceBar = $('[data-chart-secondary] .health-value-bars > div:nth-child(2) i b');
                if (workforceBar) workforceBar.style.width = `${state.resourceAllocation}%`;
            }
        });
        $('[data-export-chart]').addEventListener('click', () => exportChart());
        $('[data-print-report]').addEventListener('click', () => window.print());
    };
    const exportChart = () => {
        const svg = $('[data-chart-main] svg');
        if (!svg) {
            window.alert('Chart image export is available for chart views.');
            return;
        }
        const exportSvg = svg.cloneNode(true);
        const style = document.createElementNS('http://www.w3.org/2000/svg', 'style');
        style.textContent = '.chart-gridlines line{stroke:#e8eae8;stroke-dasharray:3 4}.chart-gridlines text,.chart-months text{fill:#85909a;font:10px sans-serif}.chart-planned,.chart-actual{fill:none;stroke-width:2.5}.chart-planned{stroke:#b9c1c8;stroke-dasharray:5 5}.chart-actual{stroke:#4d789e}.chart-point{fill:#fff;stroke:#365f82;stroke-width:2}';
        exportSvg.insertBefore(style, exportSvg.firstChild);
        exportSvg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
        const serialized = new XMLSerializer().serializeToString(exportSvg);
        const blob = new Blob([serialized], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const image = new Image();
        image.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = 1320;
            canvas.height = 500;
            const context = canvas.getContext('2d');
            context.fillStyle = '#fff';
            context.fillRect(0, 0, canvas.width, canvas.height);
            context.drawImage(image, 0, 0, canvas.width, canvas.height);
            URL.revokeObjectURL(url);
            const anchor = document.createElement('a');
            anchor.download = `beal-scorecard-${state.card}.png`;
            anchor.href = canvas.toDataURL('image/png');
            anchor.click();
        };
        image.onerror = () => { URL.revokeObjectURL(url); window.alert('This chart could not be exported in this browser.'); };
        image.src = url;
    };
    initializeSectionReveals();
    bind();
    const initialCard = window.location.hash.slice(1);
    if (cards[initialCard]) state.card = initialCard;
    draw();
}
