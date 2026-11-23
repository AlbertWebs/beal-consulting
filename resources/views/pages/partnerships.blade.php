@extends('layouts.public')

@section('head')
    <meta property="og:image" content="{{ $bannerImage }}">
@endsection

@section('content')
<section class="partnership-hero">
    <div class="partnership-hero-copy">
        <x-breadcrumb :items="[['label' => 'Partnerships']]" />
        <p class="eyebrow"><span class="eyebrow-rule"></span>WORKING TOGETHER</p>
        <h1>Good projects need <em>good partners.</em></h1>
        <p class="partnership-hero-summary">The right expertise, in the right place, at the right time can change how a project moves. BEAL works with firms and professionals who want to deliver thoughtful, well-managed projects across Kenya and Africa.</p>
        <div class="partnership-hero-actions">
            <a class="button button-gold" href="{{ route('pages.contact', ['enquiry' => 'professional-partner']) }}">Discuss a partnership <span aria-hidden="true">↗</span></a>
            <a class="text-link text-link-light" href="#ways-to-work">Explore ways to work together <span aria-hidden="true">↓</span></a>
        </div>
        <div class="partnership-hero-note"><span></span>Independent expertise. Shared commitment to delivery.</div>
    </div>
    <div class="partnership-visual" aria-label="Illustration of connected project teams across Africa" role="img">
        <div class="partnership-visual-grid"></div>
        <div class="partnership-visual-orbit partnership-orbit-one"></div>
        <div class="partnership-visual-orbit partnership-orbit-two"></div>
        <div class="partnership-visual-map" aria-hidden="true"><img src="{{ asset('assets/africa-continent.svg') }}" alt=""><span>AFRICA</span></div>
        <div class="partnership-node partnership-node-one"><i></i><span>LOCAL INSIGHT</span></div>
        <div class="partnership-node partnership-node-two"><i></i><span>SHARED STANDARDS</span></div>
        <div class="partnership-node partnership-node-three"><i></i><span>PROJECT DELIVERY</span></div>
        <div class="partnership-visual-caption"><span>BEAL / PARTNERSHIPS</span><b>One team.<br>Clear responsibilities.</b></div>
    </div>
</section>

<section class="partnership-intro section-pad">
    <div class="secondary-index"><span>01</span><i></i><span>A PRACTICAL APPROACH</span></div>
    <div class="partnership-intro-grid">
        <h2>Strong collaboration starts with <em>clarity.</em></h2>
        <div><p>Every project brings its own people, place and pressure points. We build working relationships around what the assignment actually needs: the right skills, clear roles and open communication from the outset.</p><p>We welcome conversations with organisations looking for a capable local delivery partner, professionals with complementary expertise, and development teams seeking independent project support.</p></div>
    </div>
</section>

<section class="partnership-pathways section-pad" id="ways-to-work" aria-labelledby="partnership-pathways-title">
    <div class="secondary-index"><span>02</span><i></i><span>WAYS TO WORK TOGETHER</span></div>
    <div class="partnership-section-heading"><div><p class="partnership-kicker">PARTNERSHIP OPPORTUNITIES</p><h2 id="partnership-pathways-title">Different strengths.<br><em>A shared project purpose.</em></h2></div><p>We shape each collaboration to the brief, agree responsibilities early and keep the project’s objectives in view.</p></div>
    <div class="partnership-pathway-grid">
        <article class="partnership-pathway-card">
            <span class="partnership-card-number">01</span><span class="partnership-card-symbol" aria-hidden="true">↗</span>
            <p class="partnership-card-label">FOR CONSULTANCIES &amp; DELIVERY FIRMS</p><h3>Extend your delivery reach.</h3>
            <p>Work with a Kenya-based consultancy that understands local project conditions and can contribute planning, coordination, monitoring and delivery oversight to a wider team.</p>
            <a href="{{ route('pages.contact', ['enquiry' => 'professional-partner']) }}">Discuss a delivery collaboration <span aria-hidden="true">↗</span></a>
        </article>
        <article class="partnership-pathway-card">
            <span class="partnership-card-number">02</span><span class="partnership-card-symbol" aria-hidden="true">＋</span>
            <p class="partnership-card-label">FOR INDEPENDENT PROFESSIONALS</p><h3>Bring complementary expertise.</h3>
            <p>We connect with experienced built-environment specialists whose knowledge can strengthen a project team, from technical coordination to specialist advisory input.</p>
            <a href="{{ route('pages.contact', ['enquiry' => 'professional-partner']) }}">Introduce your expertise <span aria-hidden="true">↗</span></a>
        </article>
        <article class="partnership-pathway-card">
            <span class="partnership-card-number">03</span><span class="partnership-card-symbol" aria-hidden="true">◎</span>
            <p class="partnership-card-label">FOR DEVELOPERS &amp; INSTITUTIONS</p><h3>Build a stronger project team.</h3>
            <p>Bring BEAL into the conversation when a development needs independent oversight, clear governance or joined-up management across its delivery stages.</p>
            <a href="{{ route('pages.contact', ['enquiry' => 'project-partnership']) }}">Talk through your project <span aria-hidden="true">↗</span></a>
        </article>
    </div>
</section>

<section class="partnership-principles">
    <div class="section-pad partnership-principles-inner">
        <div><div class="secondary-index secondary-index-light"><span>03</span><i></i><span>HOW WE WORK</span></div><h2>Make the working relationship <em>work.</em></h2><p>Good partnerships depend on more than a shared pitch. They need a clear basis for working together.</p></div>
        <ol>
            <li><span>01</span><div><b>Start with the brief</b><p>We understand the project, the client’s needs and the capability the team is missing.</p></div></li>
            <li><span>02</span><div><b>Agree roles early</b><p>Scope, responsibilities and communication are made clear before work begins.</p></div></li>
            <li><span>03</span><div><b>Stay accountable</b><p>Each partner contributes their expertise while keeping shared project outcomes in focus.</p></div></li>
        </ol>
    </div>
</section>

<section class="partnership-contact section-pad">
    <div class="secondary-index"><span>04</span><i></i><span>START A CONVERSATION</span></div>
    <div class="partnership-contact-layout"><div><h2>Have a project or capability in mind?</h2><p>Tell us what you are working on and how you think we could contribute. We’ll take it from there.</p></div><a class="button button-dark" href="{{ route('pages.contact', ['enquiry' => 'professional-partner']) }}">Talk with BEAL <span aria-hidden="true">↗</span></a></div>
</section>
@endsection
