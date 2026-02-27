@extends('layouts.adminlte')

@section('title', 'Inicio — Administración')
@section('page-title', 'Inicio')

@push('styles')
<style>
    /* ── Paleta institucional ────────────────────────────── */
    :root {
        --eden-oscuro:  #1A2E4A;
        --eden-medio:   #2D5F8A;
        --eden-claro:   #4A90C4;
        --eden-verde:   #27AE60;
        --eden-naranja: #E67E22;
        --eden-rojo:    #C0392B;
        --eden-gris:    #F2F4F7;
    }

    /* ── Header motivacional ─────────────────────────────── */
    .eden-header {
        background: linear-gradient(135deg, var(--eden-oscuro) 0%, var(--eden-medio) 100%);
        border-radius: 12px;
        padding: 2rem 2.5rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .eden-header::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
    }
    .eden-header::after {
        content: '';
        position: absolute;
        bottom: -60px; right: 60px;
        width: 280px; height: 280px;
        background: rgba(255,255,255,.04);
        border-radius: 50%;
    }
    .eden-header .header-logo {
        height: 80px;
        width: auto;
        opacity: .85;
        filter: brightness(0) invert(1);
    }

    /* ── Stat cards ──────────────────────────────────────── */
    .stat-card {
        border-radius: 12px;
        border: none;
        overflow: hidden;
        transition: transform .2s ease, box-shadow .2s ease;
        cursor: default;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,.15) !important;
    }
    .stat-card .stat-icon {
        width: 64px; height: 64px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem;
        background: rgba(255,255,255,.2);
        transition: transform .2s ease;
    }
    .stat-card:hover .stat-icon { transform: scale(1.1); }
    .stat-card .stat-value { font-size: 2.6rem; font-weight: 700; line-height: 1; }
    .stat-card .stat-label { font-size: .75rem; text-transform: uppercase; letter-spacing: .06em; opacity: .85; }
    .stat-card .stat-sub   { font-size: .78rem; opacity: .7; margin-top: .25rem; }
    .stat-card .stat-link  {
        display: block; padding: .55rem 1.25rem;
        background: rgba(0,0,0,.12);
        color: rgba(255,255,255,.9);
        font-size: .8rem; font-weight: 600;
        text-decoration: none;
        transition: background .2s;
    }
    .stat-card .stat-link:hover { background: rgba(0,0,0,.22); color: #fff; }

    /* ── Quick-access grid ───────────────────────────────── */
    .quick-btn {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 1.1rem .75rem;
        border-radius: 10px;
        background: var(--eden-gris);
        border: 1px solid #e2e6ea;
        color: var(--eden-oscuro);
        text-decoration: none;
        font-size: .8rem; font-weight: 600; text-align: center;
        transition: background .2s, color .2s, transform .2s, box-shadow .2s;
        min-height: 90px;
    }
    .quick-btn i { font-size: 1.5rem; margin-bottom: .45rem; color: var(--eden-medio); transition: color .2s; }
    .quick-btn:hover {
        background: var(--eden-medio);
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(45,95,138,.25);
        text-decoration: none;
    }
    .quick-btn:hover i { color: #fff; }

    /* ── Activity feed ───────────────────────────────────── */
    .activity-item {
        display: flex; align-items: flex-start;
        padding: .75rem 1rem;
        border-radius: 8px;
        transition: background .15s;
    }
    .activity-item:hover { background: var(--eden-gris); }
    .activity-avatar {
        width: 40px; height: 40px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 1rem;
        color: #fff;
    }

    /* ── Section headings ────────────────────────────────── */
    .section-title {
        font-size: 1rem; font-weight: 700;
        color: var(--eden-oscuro);
        border-left: 4px solid var(--eden-medio);
        padding-left: .75rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')

{{-- ── Header motivacional ─────────────────────────────────────────── --}}
<div class="eden-header text-white">
    <div class="d-flex justify-content-between align-items-center">
        <div style="position:relative;z-index:1;">
            <h2 class="font-weight-bold mb-1" style="font-size:1.8rem;">
                ¡Bienvenido, {{ auth()->user()->name }}!
            </h2>
            <p class="mb-1" style="font-size:1rem;opacity:.9;">
                {{ $mensajeMotivacional }}
            </p>
            <p class="mb-0" style="font-size:.85rem;opacity:.7;">
                <i class="far fa-calendar-alt mr-1"></i>
                {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
            </p>
        </div>
        <div class="d-none d-md-block" style="position:relative;z-index:1;">
            <img src="{{ asset('images/logoGrande.png') }}"
                 alt="Clínica Eden"
                 class="header-logo"
                 onerror="this.style.display='none'">
        </div>
    </div>
</div>

{{-- ── Stat cards ───────────────────────────────────────────────────── --}}
<div class="row">

    {{-- Usuarios activos --}}
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow mb-4" style="background:var(--eden-medio);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="stat-label mb-1">Usuarios Activos</p>
                        <p class="stat-value">{{ $usuariosActivos }}</p>
                        <p class="stat-sub">{{ $totalUsuarios }} en total</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <a href="{{ route('admin.usuarios') }}" class="stat-link">
                Ver usuarios <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    {{-- Médicos --}}
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow mb-4" style="background:var(--eden-verde);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="stat-label mb-1">Médicos Activos</p>
                        <p class="stat-value">{{ $medicosTotal }}</p>
                        <p class="stat-sub">generales + especialistas</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-user-md"></i></div>
                </div>
            </div>
            <a href="{{ route('admin.asignaciones.medicos') }}" class="stat-link">
                Ver asignaciones <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    {{-- Pacientes --}}
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow mb-4" style="background:var(--eden-naranja);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="stat-label mb-1">Pacientes</p>
                        <p class="stat-value">{{ $pacientesTotal }}</p>
                        <p class="stat-sub">registrados en el sistema</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-procedures"></i></div>
                </div>
            </div>
            <a href="{{ route('admin.reportes.pacientes') }}" class="stat-link">
                Ver reporte <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

    {{-- Hospitalizados --}}
    <div class="col-6 col-md-3">
        <div class="card stat-card shadow mb-4" style="background:var(--eden-rojo);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="stat-label mb-1">Hospitalizados</p>
                        <p class="stat-value">{{ $hospitalizadosActuales }}</p>
                        <p class="stat-sub">actualmente internados</p>
                    </div>
                    <div class="stat-icon"><i class="fas fa-bed"></i></div>
                </div>
            </div>
            <a href="{{ route('admin.reportes.hospitalizaciones') }}" class="stat-link">
                Ver reporte <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>

</div>

{{-- ── Segunda fila: Ingresos + Accesos rápidos ─────────────────────── --}}
<div class="row">

    {{-- Ingresos del mes --}}
    <div class="col-12 col-md-4 col-lg-3">
        <div class="card shadow mb-4" style="border-top:4px solid var(--eden-verde);border-radius:12px;">
            <div class="card-body">
                <p class="section-title" style="border-color:var(--eden-verde);">Ingresos del Mes</p>
                <p class="font-weight-bold mb-0" style="font-size:2rem;color:var(--eden-verde);">
                    $ {{ number_format($ingresosMes, 0, ',', '.') }}
                </p>
                <p class="text-muted" style="font-size:.8rem;">Facturas pagadas — {{ now()->locale('es')->isoFormat('MMMM YYYY') }}</p>
                <a href="{{ route('admin.reportes.financiero') }}"
                   class="d-inline-flex align-items-center mt-2 font-weight-bold"
                   style="font-size:.82rem;color:var(--eden-verde);text-decoration:none;">
                    Ver reporte financiero <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Accesos rápidos --}}
    <div class="col-12 col-md-8 col-lg-9">
        <div class="card shadow mb-4" style="border-radius:12px;">
            <div class="card-body">
                <p class="section-title">Accesos Rápidos</p>
                <div class="row">
                    <div class="col-4 col-sm-4 col-md-2 mb-3">
                        <a href="{{ route('admin.usuarios.crear') }}" class="quick-btn">
                            <i class="fas fa-user-plus"></i>
                            Nuevo Usuario
                        </a>
                    </div>
                    <div class="col-4 col-sm-4 col-md-2 mb-3">
                        <a href="{{ route('admin.usuarios') }}" class="quick-btn">
                            <i class="fas fa-users-cog"></i>
                            Personal
                        </a>
                    </div>
                    <div class="col-4 col-sm-4 col-md-2 mb-3">
                        <a href="{{ route('admin.asignaciones.medicos') }}" class="quick-btn">
                            <i class="fas fa-calendar-check"></i>
                            Asignaciones
                        </a>
                    </div>
                    <div class="col-4 col-sm-4 col-md-2 mb-3">
                        <a href="{{ route('admin.reportes') }}" class="quick-btn">
                            <i class="fas fa-chart-bar"></i>
                            Reportes
                        </a>
                    </div>
                    <div class="col-4 col-sm-4 col-md-2 mb-3">
                        <a href="{{ route('admin.balance-ingresos') }}" class="quick-btn">
                            <i class="fas fa-money-bill-wave"></i>
                            Finanzas
                        </a>
                    </div>
                    <div class="col-4 col-sm-4 col-md-2 mb-3">
                        <a href="{{ route('admin.balance-personal') }}" class="quick-btn">
                            <i class="fas fa-user-check"></i>
                            Balance
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ── Actividad reciente ───────────────────────────────────────────── --}}
<div class="card shadow mb-4" style="border-radius:12px;">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <p class="section-title mb-0">Actividad Reciente</p>
            <span class="badge badge-secondary" style="font-size:.72rem;">Últimas 24 horas</span>
        </div>

        @if($actividadReciente->isNotEmpty())
            <div>
                @foreach($actividadReciente as $item)
                <div class="activity-item">
                    <div class="activity-avatar mr-3" style="background:{{ $item->color }};">
                        <i class="fas {{ $item->icono }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-0 font-weight-bold" style="font-size:.88rem;color:var(--eden-oscuro);">
                            {{ $item->descripcion }}
                        </p>
                        <p class="mb-0 text-muted" style="font-size:.78rem;">
                            {{ $item->detalle }} &mdash;
                            <span>{{ $item->tiempo->diffForHumans() }}</span>
                        </p>
                    </div>
                </div>
                @if(!$loop->last)
                    <hr class="my-1" style="border-color:#eef0f3;">
                @endif
                @endforeach
            </div>
        @else
            <div class="text-center py-5 text-muted">
                <i class="fas fa-clock fa-2x mb-2 d-block" style="opacity:.35;"></i>
                <p class="mb-0" style="font-size:.9rem;">No hay actividad registrada en las últimas 24 horas</p>
            </div>
        @endif
    </div>
</div>

@endsection
