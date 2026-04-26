<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Resume</title>
    <style>
        body { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; color: #222; }
        .header { text-align: center; margin-bottom: 20px; }
        .name { font-size: 22px; font-weight: bold; }
        .title { font-size: 14px; color: #666; }
        .section { margin-top: 16px; }
        .section h3 { border-bottom: 1px solid #ddd; padding-bottom: 4px; margin-bottom: 8px; }
        .item { margin-bottom: 8px; }
        .small { font-size: 12px; color: #666; }
    </style>
</head>
<body>

<div class="header">
    <div class="name">{{ $user->name }}</div>
    @if(isset($user->profile) && $user->profile->title)
        <div class="title">{{ $user->profile->title }}</div>
    @endif
</div>

@if(isset($user->profile) && $user->profile->biography)
<div class="section">
    <h3>Sobre mí</h3>
    <div class="item">{{ $user->profile->biography }}</div>
</div>
@endif

@if($user->experiences && $user->experiences->count())
<div class="section">
    <h3>Experiencia</h3>
    @foreach($user->experiences as $exp)
        <div class="item">
            <div><strong>{{ $exp->role ?? $exp->title ?? 'Rol' }}</strong> - <span class="small">{{ $exp->company ?? '' }} — {{ $exp->start_date ? $exp->start_date->format('Y-m') : '' }} @if($exp->end_date) - {{ $exp->end_date->format('Y-m') }} @else - Presente @endif</span></div>
            @if($exp->description)
                <div class="small">{{ $exp->description }}</div>
            @endif
        </div>
    @endforeach
</div>
@endif

@if($user->educations && $user->educations->count())
<div class="section">
    <h3>Educación</h3>
    @foreach($user->educations as $edu)
        <div class="item">
            <div><strong>{{ $edu->degree ?? $edu->title ?? $edu->school }}</strong> <span class="small">- {{ $edu->school ?? '' }} ({{ $edu->start_year ?? '' }} @if($edu->end_year) - {{ $edu->end_year }} @endif)</span></div>
            @if($edu->description)
                <div class="small">{{ $edu->description }}</div>
            @endif
        </div>
    @endforeach
</div>
@endif

@if($user->skills && $user->skills->count())
<div class="section">
    <h3>Habilidades</h3>
    <div class="item">
        {{ $user->skills->pluck('name')->join(', ') }}
    </div>
</div>
@endif

</body>
</html>
