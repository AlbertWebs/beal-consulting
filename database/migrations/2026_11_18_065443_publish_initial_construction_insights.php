<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $timestamp = now();
        $insights = [
            [
                'title' => 'NCA compliance in Kenya: the project controls developers should get right',
                'slug' => 'nca-compliance-kenya-project-controls',
                'category' => 'Construction governance',
                'summary' => 'A practical overview of NCA contractor and project registration, the National Building Code 2024, and how teams can make statutory compliance visible in project controls.',
                'seo_title' => 'NCA Compliance in Kenya: A Developer’s Project Checklist | BEAL',
                'seo_description' => 'Understand Kenya’s NCA contractor and project registration requirements and connect statutory approvals to project governance and delivery controls.',
                'content_html' => <<<'HTML'
<p>Construction compliance is easiest to manage when it is treated as part of project planning and governance, rather than a last-minute document chase. In Kenya, developers, contractors and consultants each have responsibilities that need to be understood and tracked from the outset.</p>
<h2>Check contractor registration and current licensing</h2>
<p>The National Construction Authority (NCA) states that a person may not carry on the business of a contractor unless registered and holding a valid practising licence. Before award and mobilisation, confirm the contractor’s registration category and class of works against the package, and check that specialist subcontractors hold the registrations and licences relevant to their scope.</p>
<h2>Plan project registration early</h2>
<p>NCA’s project-registration guidance says construction projects in the public and private sectors are to be registered. It places the application responsibility on the project owner and states that an application should be made within thirty days after award of the construction contract or tender to an NCA-registered contractor. Confirm the applicable process and document list directly with NCA for the specific project.</p>
<p>Registration preparation can involve approved architectural and structural drawings, county planning approvals, consultant registrations and practising licences, a signed contract, a quantity-surveyor-approved bill-of-quantities summary and other approvals that apply to the project. Assign an owner and due date to each requirement in the project approvals register.</p>
<h2>Use the National Building Code 2024 in design and delivery reviews</h2>
<p>The National Building Code 2024 was published as Legal Notice No. 47 on 1 March 2024, replacing the 1968 adoptive by-laws. Design teams and project leaders should verify which code provisions and transition arrangements apply to their project, then include the relevant checks in design coordination, construction quality planning, inspections and handover records.</p>
<h2>Make compliance visible in project governance</h2>
<ul><li>Maintain an approvals and statutory-obligations register with responsible parties, evidence and due dates.</li><li>Link design submissions and procurement packages to the approvals they depend on.</li><li>Review unresolved approvals, licence expiry dates and inspection actions at project meetings.</li><li>Escalate any condition that could affect mobilisation, safety, cost or the critical path.</li><li>Retain approved drawings, inspection records and completion evidence in the handover file.</li></ul>
<p>A simple compliance tracker does not replace advice from qualified professionals or confirmation from the relevant authorities. It gives the client and delivery team a shared view of what is required, who owns it and what needs attention.</p>
<p><strong>Sources:</strong> <a href="https://www.nca.go.ke/project-registration" target="_blank" rel="noopener noreferrer">NCA project registration guidance</a>; <a href="https://www.nca.go.ke/local-contractors" target="_blank" rel="noopener noreferrer">NCA contractor registration guidance</a>; <a href="https://www.nca.go.ke/building-code" target="_blank" rel="noopener noreferrer">NCA National Building Code 2024</a>.</p>
<p><em>This article is general project-governance information, not legal advice. Confirm current requirements with NCA, county authorities and the project’s registered consultants.</em></p>
HTML,
            ],
            [
                'title' => 'After the Eastleigh building collapse: what project governance must make visible',
                'slug' => 'eastleigh-building-collapse-project-governance-lessons',
                'category' => 'Project governance',
                'summary' => 'The October 2026 Eastleigh collapse is a human tragedy. Without pre-empting investigations, it underlines why approvals, design changes, inspections and site conditions must be visible and acted on.',
                'seo_title' => 'Eastleigh Building Collapse: Project Governance Lessons | BEAL',
                'seo_description' => 'A careful project-governance perspective on the October 2026 Eastleigh building collapse, focusing on approvals, inspections, change control and public safety.',
                'content_html' => <<<'HTML'
<p>The collapse of a building under construction in Nairobi’s Eastleigh area on 23 October 2026 was first and foremost a human tragedy. As of reporting published on 5 November, eight people had been confirmed dead and sixteen were still believed to be missing. The investigation and recovery process must establish the facts; this article does not speculate about the cause or assign blame.</p>
<p>For the construction sector, the event is a sobering reminder that project controls exist to protect people as well as budgets and schedules. Good governance makes safety-critical information visible early enough for someone with authority to act.</p>
<h2>Keep approval status connected to work on site</h2>
<p>Approved drawings, county permissions, NCA registration and other required approvals should be accessible to the project team and linked to the work they govern. A change in design, use, height, sequence or site conditions should trigger a documented review of whether approvals, calculations, temporary works or inspections need to be revisited.</p>
<h2>Give inspections and stop-work concerns a clear path</h2>
<p>Inspection plans should identify what must be checked, by whom, at what stage and what evidence is retained. When a concern is raised, the team needs a route to record it, assess urgency, assign an accountable owner and escalate it without delay. Schedule pressure should never obscure an unresolved safety-critical issue.</p>
<h2>Track changes, non-conformances and close-out</h2>
<p>Design revisions, site instructions, non-conformances and corrective actions should be controlled in a traceable record. The record should show the decision, technical review, approvals, responsible party and verification that the action was completed. This helps prevent informal changes from becoming invisible risks.</p>
<h2>Include neighbours and the public in risk planning</h2>
<p>Busy urban sites need practical controls for access, hoarding, overhead hazards, deliveries, emergency routes and communication with neighbouring businesses and residents. These controls should be reviewed as site conditions change and coordinated with the relevant authorities and competent professionals.</p>
<p>These are governance principles, not conclusions about the Eastleigh incident. Investigators and public authorities are responsible for establishing what happened. The construction industry’s responsibility is to build systems where approvals, inspections, design changes and safety concerns cannot quietly fall between roles.</p>
<p><strong>Sources:</strong> <a href="https://apnews.com/article/5c66ab1fdf995e92dad8aabae5358ba9" target="_blank" rel="noopener noreferrer">Associated Press, 5 November 2026: recovery update</a>; <a href="https://apnews.com/article/a7dcc0932437d79d52ea29aa860ec11e" target="_blank" rel="noopener noreferrer">Associated Press, 23 October 2026: initial collapse report</a>; <a href="https://www.nca.go.ke/building-code" target="_blank" rel="noopener noreferrer">NCA National Building Code 2024</a>.</p>
<p><em>This is an independent project-governance perspective. It is not an investigation report and does not state the cause of the collapse.</em></p>
HTML,
            ],
            [
                'title' => 'The quiet project risk hiding in a concrete pour: curing and handover discipline',
                'slug' => 'concrete-curing-construction-quality-project-controls',
                'category' => 'Construction quality',
                'summary' => 'Concrete curing can look like a small site task. Treating it as a planned, recorded quality activity helps teams protect workmanship and avoid preventable repair and delay.',
                'seo_title' => 'Concrete Curing: A Construction Quality Control | BEAL',
                'seo_description' => 'Why concrete curing belongs in construction quality plans, inspection records and programme coordination, not just in a site diary.',
                'content_html' => <<<'HTML'
<p>Concrete gets plenty of attention before a pour: reinforcement, formwork, access, testing and the delivery sequence. The work that follows the pour can receive less attention, even though curing is part of achieving the performance intended by the design and specification.</p>
<p>Curing means maintaining suitable moisture and temperature conditions while concrete develops strength and durability. The exact method and duration depend on the mix, element, exposure, specification and professional direction. The site team should follow the project documents and advice from the responsible engineer rather than rely on a one-size-fits-all rule.</p>
<h2>Make the curing plan part of the pour plan</h2>
<p>Before placement, confirm who will protect and cure each element, what materials and water supply are available, how access will be controlled, and how weather or sequencing could affect the method. Include the activity in the look-ahead programme so follow-on trades do not remove protection or load an element prematurely.</p>
<h2>Record what was done</h2>
<p>A short, consistent record can capture the element and pour date, specified curing method, start and finish checks, interruptions, weather conditions and any instructions or corrective action. Photographs can help where they provide useful evidence. The purpose is not paperwork for its own sake; it is to give the project team a reliable account of whether the specified process was followed.</p>
<h2>Connect quality, time and cost</h2>
<p>If curing is missed or interrupted, the project may face investigation, testing, repair, rework or delay. Those responses can affect access, subsequent trades and handover. A project health review should therefore consider quality evidence alongside programme dependencies, cost exposure and the action needed to protect the work.</p>
<p>Small, repeatable controls often make a practical difference: name an owner, confirm materials before the pour, include curing in inspections, and verify completion before releasing the next activity. The project engineer’s specification remains the authority for the required method and acceptance criteria.</p>
<p><strong>Further reading:</strong> <a href="https://www.concrete.org/frequentlyaskedquestions/faqid/789.aspx" target="_blank" rel="noopener noreferrer">American Concrete Institute: What is curing?</a></p>
HTML,
            ],
        ];

        foreach ($insights as $index => $insight) {
            if (DB::table('content_entries')->where('type', 'insight')->where('slug', $insight['slug'])->exists()) {
                continue;
            }

            DB::table('content_entries')->insert([
                ...$insight,
                'type' => 'insight',
                'author' => 'BEAL Consulting',
                'status' => 'published',
                'is_featured' => $index === 0,
                'sort_order' => ($index + 1) * 10,
                'published_at' => $timestamp,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('content_entries')->where('type', 'insight')->whereIn('slug', [
            'nca-compliance-kenya-project-controls',
            'eastleigh-building-collapse-project-governance-lessons',
            'concrete-curing-construction-quality-project-controls',
        ])->delete();
    }
};
