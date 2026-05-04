<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>CV - {{ $user->name }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            background: #f3f4f6;
            color: #374151;
            line-height: 1.6;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 800px;
            background: #ffffff;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        /* HEADER */
        .header {
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .name {
            font-size: 26px;
            font-weight: bold;
            color: #1f2937;
            margin: 0;
        }

        .title {
            color: #6366f1;
            font-size: 16px;
            margin: 5px 0;
        }

        .contact {
            font-size: 12px;
            color: #9ca3af;
        }

        /* SECCIONES */
        .section {
            margin-top: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            color: #4f46e5;
            margin-bottom: 10px;
            border-left: 5px solid #a5b4fc;
            padding-left: 10px;
        }

        /* ITEMS */
        .item {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            background: #f9fafb;
        }

        .item-title {
            font-weight: bold;
            font-size: 14px;
            color: #111827;
        }

        .item-subtitle {
            color: #6b7280;
            font-size: 13px;
        }

        .item-date {
            color: #9ca3af;
            font-size: 11px;
        }

        /* SOBRE MI */
        .about {
            font-size: 13px;
            background: #eef2ff;
            padding: 12px;
            border-radius: 8px;
        }

        /* SKILLS */
        .skills-container {
            margin-top: 10px;
        }

        .skill-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin: 4px;
        }

    </style>
</head>
<body>

<div class="container">

    <div class="header">
        <h1 class="name">{{ $user->name }}</h1>
        <p class="title">{{ $user->profile->title ?? 'Desarrollador' }}</p>
        <p class="contact">{{ $user->email }} | {{ $user->profile->location ?? 'Ubicación no definida' }}</p>
    </div>

    @if($user->profile && $user->profile->biography)
        <div class="section">
            <div class="section-title">Sobre mí</div>
            <div class="about">
                {{ $user->profile->biography }}
            </div>
        </div>
    @endif

    <div class="section">
        <div class="section-title">Experiencia Laboral</div>
        @foreach($user->experiences as $exp)
            <div class="item">
                <div class="item-title">{{ $exp->position }}</div>
                <div class="item-subtitle">{{ $exp->company }}</div>
                <div class="item-date">{{ $exp->start_date }} — {{ $exp->end_date ?? 'Actualidad' }}</div>
            </div>
        @endforeach
    </div>

    <div class="section">
        <div class="section-title">Formación Académica</div>
        @foreach($user->educations as $edu)
            <div class="item">
                <div class="item-title">{{ $edu->degree }}</div>
                <div class="item-subtitle">{{ $edu->institution }}</div>
                <div class="item-date">{{ $edu->start_date }} — {{ $edu->end_date ?? 'Actualidad' }}</div>
            </div>
        @endforeach
    </div>

    @if(count($user->projects) > 0)
        <div class="section">
            <div class="section-title">Portafolio de Proyectos</div>
            @foreach($user->projects as $project)
                <div class="item">
                    <div class="item-title">
                        {{ $project->title }}
                    </div>
                    <div class="about" style="background: transparent; padding: 5px 0; margin-bottom: 2px;">
                        {{ $project->description }}
                    </div>
                    @if($project->repo_url)
                        <div style="font-size: 10px; color: #4f46e5; font-style: italic;">
                            Repo: {{ $project->repo_url }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <div class="section">
        <div class="section-title">Habilidades</div>
        <div class="skills-container">
            @foreach($user->skills as $skill)
                <span class="skill-badge">{{ $skill->name }}</span>
            @endforeach
        </div>
    </div>

</div>

</body>
</html>
