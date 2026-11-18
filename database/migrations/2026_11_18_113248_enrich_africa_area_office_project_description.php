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
        $this->updateProjectDescription(
            '<p>This project is included as part of the Founding Director’s professional experience. It is not presented as an engagement contracted by BEAL Consulting Ltd.</p>',
            <<<'HTML'
                <p>An area office must do more than provide space for people to work. It needs to support day-to-day operations, serve the people who use it, and remain practical to maintain. Shaping a development around those needs starts with a clear brief and decisions that stay connected as the work moves from planning into delivery.</p>
                <p>For an office development of this kind, design, approvals, procurement, building services, programme and cost are closely linked. A change to the brief can affect design coordination; a gap in procurement can disrupt the sequence on site; and an unresolved interface can create rework or make handover harder. Teams need timely information, clear responsibilities and a shared view of progress to address these issues while there is still room to act.</p>
                <p>Our team’s earlier professional experience on Africa Area Office Development informs how we approach complex projects at BEAL today. We look across governance and decision-making, design and delivery interfaces, schedule and cost performance, emerging risks, quality, safety and handover readiness. Bringing these areas into one clear picture helps clients understand what is happening, where attention is needed and who should take the next step.</p>
                <p>This experience was gained through our team’s earlier professional roles, before BEAL Consulting Ltd. The project was not contracted by BEAL. We are keeping this account focused on the experience that informs our work today while verified details about the project and our team’s specific responsibilities are confirmed for publication.</p>
                HTML,
        );

        DB::table('content_entries')
            ->where('type', 'project')
            ->where('slug', 'africa-area-office-development')
            ->where('summary', 'Project experience gained by the Founding Director through a previous professional engagement.')
            ->update([
                'summary' => 'Our team’s earlier experience on Africa Area Office Development informs how we connect project governance, coordination and delivery performance at BEAL.',
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->updateProjectDescription(
            <<<'HTML'
                <p>An area office must do more than provide space for people to work. It needs to support day-to-day operations, serve the people who use it, and remain practical to maintain. Shaping a development around those needs starts with a clear brief and decisions that stay connected as the work moves from planning into delivery.</p>
                <p>For an office development of this kind, design, approvals, procurement, building services, programme and cost are closely linked. A change to the brief can affect design coordination; a gap in procurement can disrupt the sequence on site; and an unresolved interface can create rework or make handover harder. Teams need timely information, clear responsibilities and a shared view of progress to address these issues while there is still room to act.</p>
                <p>Our team’s earlier professional experience on Africa Area Office Development informs how we approach complex projects at BEAL today. We look across governance and decision-making, design and delivery interfaces, schedule and cost performance, emerging risks, quality, safety and handover readiness. Bringing these areas into one clear picture helps clients understand what is happening, where attention is needed and who should take the next step.</p>
                <p>This experience was gained through our team’s earlier professional roles, before BEAL Consulting Ltd. The project was not contracted by BEAL. We are keeping this account focused on the experience that informs our work today while verified details about the project and our team’s specific responsibilities are confirmed for publication.</p>
                HTML,
            '<p>This project is included as part of the Founding Director’s professional experience. It is not presented as an engagement contracted by BEAL Consulting Ltd.</p>',
        );

        DB::table('content_entries')
            ->where('type', 'project')
            ->where('slug', 'africa-area-office-development')
            ->where('summary', 'Our team’s earlier experience on Africa Area Office Development informs how we connect project governance, coordination and delivery performance at BEAL.')
            ->update([
                'summary' => 'Project experience gained by the Founding Director through a previous professional engagement.',
                'updated_at' => now(),
            ]);
    }

    private function updateProjectDescription(string $oldContent, string $newContent): void
    {
        DB::table('content_entries')
            ->where('type', 'project')
            ->where('slug', 'africa-area-office-development')
            ->where('content_html', $oldContent)
            ->update([
                'content_html' => $newContent,
                'updated_at' => now(),
            ]);
    }
};
