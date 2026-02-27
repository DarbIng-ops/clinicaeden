@extends('layouts.adminlte')

@section('title', 'Reportes y Estadísticas')
@section('page-title', 'Reportes y Estadísticas')

@section('content')

    <!-- Filtro de período -->
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-calendar-alt mr-1"></i> Período del Reporte</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reportes') }}" class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label>Fecha Inicio</label>
                        <input type="date" name="fecha_inicio"
                               value="{{ request('fecha_inicio', now()->startOfMonth()->format('Y-m-d')) }}"
                               class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label>Fecha Fin</label>
                        <input type="date" name="fecha_fin"
                               value="{{ request('fecha_fin', now()->format('Y-m-d')) }}"
                               class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i> Actualizar Estadísticas
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Estadísticas rápidas del mes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-user-plus"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pacientes Nuevos</span>
                    <span class="info-box-number">{{ \App\Models\Paciente::whereMonth('created_at', now()->month)->count() }}</span>
                    <span class="progress-description text-muted">este mes</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-stethoscope"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Consultas</span>
                    <span class="info-box-number">{{ \App\Models\Consulta::whereMonth('created_at', now()->month)->count() }}</span>
                    <span class="progress-description text-muted">este mes</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-bed"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Hospitalizaciones</span>
                    <span class="info-box-number">{{ \App\Models\Hospitalizacion::whereMonth('created_at', now()->month)->count() }}</span>
                    <span class="progress-description text-muted">este mes</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Ingresos</span>
                    <span class="info-box-number">
                        ${{ number_format(\App\Models\Factura::where('estado','pagado')->whereMonth('created_at', now()->month)->sum('total'), 0, ',', '.') }}
                    </span>
                    <span class="progress-description text-muted">este mes</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de reportes -->
    <div class="row">

        @php
            $fi = request('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
            $ff = request('fecha_fin', now()->format('Y-m-d'));
            $qs = http_build_query(['fecha_inicio' => $fi, 'fecha_fin' => $ff]);
        @endphp

        {{-- Pacientes --}}
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-users mr-1"></i> Pacientes</h3>
                    <div class="card-tools">
                        <span class="badge badge-info">Disponible</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Listado de pacientes registrados con datos personales e historial de consultas.</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.reportes.pacientes') }}?{{ $qs }}"
                       class="btn btn-info btn-sm btn-block">
                        <i class="fas fa-eye mr-1"></i> Ver Reporte →
                    </a>
                </div>
            </div>
        </div>

        {{-- Consultas --}}
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-stethoscope mr-1"></i> Consultas</h3>
                    <div class="card-tools">
                        <span class="badge badge-success">Disponible</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Historial de consultas médicas realizadas, médico asignado y diagnósticos.</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.reportes.consultas') }}?{{ $qs }}"
                       class="btn btn-success btn-sm btn-block">
                        <i class="fas fa-eye mr-1"></i> Ver Reporte →
                    </a>
                </div>
            </div>
        </div>

        {{-- Hospitalizaciones --}}
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-hospital mr-1"></i> Hospitalizaciones</h3>
                    <div class="card-tools">
                        <span class="badge badge-warning">Disponible</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Registro de ingresos hospitalarios, altas médicas y estado de cada paciente.</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.reportes.hospitalizaciones') }}?{{ $qs }}"
                       class="btn btn-warning btn-sm btn-block">
                        <i class="fas fa-eye mr-1"></i> Ver Reporte →
                    </a>
                </div>
            </div>
        </div>

        {{-- Financiero --}}
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card card-outline card-danger">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-invoice-dollar mr-1"></i> Financiero</h3>
                    <div class="card-tools">
                        <span class="badge badge-danger">Disponible</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Ingresos, facturas pagadas, pendientes y totales del período seleccionado.</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.reportes.financiero') }}?{{ $qs }}"
                       class="btn btn-danger btn-sm btn-block">
                        <i class="fas fa-eye mr-1"></i> Ver Reporte →
                    </a>
                </div>
            </div>
        </div>

        {{-- Satisfacción --}}
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card card-outline card-pink" style="border-top-color: #e83e8c;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-star mr-1"></i> Satisfacción</h3>
                    <div class="card-tools">
                        <span class="badge" style="background:#e83e8c;color:#fff;">Disponible</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Encuestas de satisfacción, calificaciones promedio y nivel de recomendación.</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.reportes.satisfaccion') }}?{{ $qs }}"
                       class="btn btn-sm btn-block text-white" style="background:#e83e8c;">
                        <i class="fas fa-eye mr-1"></i> Ver Reporte →
                    </a>
                </div>
            </div>
        </div>

        {{-- Medicamentos / Tratamientos --}}
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-pills mr-1"></i> Medicamentos</h3>
                    <div class="card-tools">
                        <span class="badge badge-secondary">Disponible</span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted">Tratamientos y medicamentos recetados con dosis, frecuencia y costo estimado.</p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.reportes.medicamentos') }}?{{ $qs }}"
                       class="btn btn-secondary btn-sm btn-block">
                        <i class="fas fa-eye mr-1"></i> Ver Reporte →
                    </a>
                </div>
            </div>
        </div>

    </div>

@endsection
