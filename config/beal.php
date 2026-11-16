<?php

return [
    'site_url' => env('BEAL_SITE_URL', 'https://www.bealconsulting.africa'),
    'contact_email' => env('BEAL_CONTACT_EMAIL'),
    'google_analytics_id' => env('BEAL_GOOGLE_ANALYTICS_ID'),
    'services' => [
        'strategic-intelligence-advisory' => [
            'slug' => 'strategic-intelligence-advisory',
            'number' => '01',
            'title' => 'Strategic Intelligence & Advisory',
            'description' => 'We provide feasibility advice, development strategy and options analysis to help clients assess opportunities and make informed investment and management decisions.',
            'introduction' => [
                'We help organisations evaluate development opportunities before they commit resources. Our strategic intelligence and advisory brings together research, feasibility assessment and project intelligence to clarify market context, investment considerations and the factors that shape a successful project.',
                'From options analysis to development strategy, we provide structured advice for complex infrastructure and property decisions. We help decision-makers compare approaches, understand key risks and identify practical next steps for project development.',
            ],
            'image' => 'assets/home-image/urban-infrastructure.jpg',
            'image_alt' => 'Elevated road infrastructure with a sweeping curve',
            'subservices' => [
                'Strategic research and analysis',
                'Feasibility and development advisory',
                'Investment and project intelligence',
                'Decision-support services',
            ],
        ],
        'project-programme-advisory' => [
            'slug' => 'project-programme-advisory',
            'number' => '02',
            'title' => 'Project & Programme Advisory',
            'description' => 'We support project and programme teams with coordination, planning, progress monitoring, reporting, project controls and recovery planning.',
            'introduction' => [
                'We connect objectives, people, budgets and delivery plans through project and programme management. Our advisory support spans planning, coordination, project controls, progress monitoring and reporting, giving clients a clearer view of delivery status and emerging issues.',
                'For complex projects and programmes, we support delivery strategy, schedule oversight and recovery planning. Regular monitoring and decision-ready reporting help sponsors and project teams focus on priorities, address constraints and keep work aligned with agreed objectives.',
            ],
            'image' => 'assets/home-image/city-development.jpg',
            'image_alt' => 'Tower cranes and high-rise construction shaping a city skyline',
            'subservices' => [
                'Project management',
                'Programme management',
                'Project planning and controls',
                'Project monitoring and reporting',
                'Delivery strategy',
            ],
        ],
        'governance-risk-performance' => [
            'slug' => 'governance-risk-performance',
            'number' => '03',
            'title' => 'Governance, Risk & Performance',
            'description' => 'We provide project health assessments, governance reviews, risk management and performance tracking that bring issues, decisions and required actions into focus.',
            'introduction' => [
                'We give decision-makers a clear view of project performance, risk and accountability. Our governance, risk and performance advisory includes project health assessments, governance reviews, portfolio oversight and performance monitoring for infrastructure and development projects.',
                'Our structured project reviews bring schedule pressure, cost concerns, quality issues, commercial risks and delivery dependencies into focus. We help clients translate findings into practical recommendations, clear ownership and timely management action, including through the BEAL Project Health Scorecard.',
            ],
            'image' => 'assets/home-image/project-analytics.jpg',
            'image_alt' => 'Project data displayed on a tablet for analysis',
            'subservices' => [
                'Project Health Assessment & Scorecards',
                'Project governance and reviews',
                'Portfolio oversight',
                'Risk management',
                'Performance monitoring',
                'Contract and commercial advisory',
            ],
        ],
        'infrastructure-development-consultancy' => [
            'slug' => 'infrastructure-development-consultancy',
            'number' => '04',
            'title' => 'Infrastructure & Development Consultancy',
            'description' => 'We advise on construction and development, coordinate technical inputs, and monitor quality, health, safety, environmental and sustainability requirements.',
            'introduction' => [
                'Infrastructure and development projects need coordinated technical advice from early planning through construction and delivery. We provide development advisory, construction consultancy and technical coordination tailored to each project’s needs.',
                'Quality, health, safety and environmental considerations can affect project outcomes at every stage. We help teams organise oversight, monitor relevant requirements and integrate sustainability considerations into development and delivery decisions.',
            ],
            'image' => 'assets/home-image/glass-architecture.jpg',
            'image_alt' => 'Modern commercial building with a reflective geometric facade',
            'subservices' => [
                'Infrastructure development advisory',
                'Construction and development consultancy',
                'Technical coordination',
                'Quality, health, safety and environmental advisory',
                'Sustainability considerations',
            ],
        ],
        'digital-analytical-solutions' => [
            'slug' => 'digital-analytical-solutions',
            'number' => '05',
            'title' => 'Digital & Analytical Solutions',
            'description' => 'We turn project dashboards, performance data and digital reporting into clear management insight and timely decisions.',
            'introduction' => [
                'We help teams use reliable project data to understand performance and act while there is still time to influence outcomes. Our digital and analytical solutions bring information together through performance analytics, data-driven reporting and digital project controls.',
                'Our management dashboards and decision-support tools make schedule, cost, risk and delivery information easier to review. We shape reporting around project objectives, giving leaders a timely view of trends, exceptions and issues that need attention.',
            ],
            'image' => 'assets/home-image/project-analytics.jpg',
            'image_alt' => 'Digital project dashboard and performance charts on a tablet',
            'subservices' => [
                'Project performance analytics',
                'Data-driven reporting',
                'Digital project controls',
                'Management dashboards',
                'Decision-support tools',
            ],
        ],
    ],
];
