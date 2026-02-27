@extends('layouts.adminlte')

@section('title', 'Reporte de Satisfacción')
@section('page-title', 'Reporte de Satisfacción')

@push('styles')
<style>
    @media print {
        .main-sidebar, .main-header, .content-header, .no-print { display: none !important; }
        .content-wrapper { margin-left: 0 !important; padding-top: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        .print-header { display: block !important; }
    }
    .print-header { display: none; }
    .stars { color: #f39c12; letter-spacing: 2px; }
</style>
@endpush

@section('content')

    <div class="print-header text-center mb-4">
        <h2>Clínica Eden — Reporte de Satisfacción</h2>
        <p>Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
        <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
        <hr>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="{{ route('admin.reportes') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
        <button onclick="window.print()" class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf mr-1"></i> Descargar PDF
        </button>
    </div>

    <div class="card no-print" style="border-top: 3px solid #e83e8c;">
        <div class="card-body py-2">
            <form method="GET" class="form-inline">
                <label class="mr-2">Período:</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="form-control form-control-sm mr-2">
                <span class="mr-2">al</span>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control form-control-sm mr-2">
                <button type="submit" class="btn btn-sm text-white" style="background:#e83e8c;">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon elevation-1" style="background:#e83e8c;"><i class="fas fa-poll text-white"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Encuestas</span>
                    <span class="info-box-number">{{ $encuestas->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-star"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Calificación Promedio</span>
                    <span class="info-box-number">{{ $promedioGeneral }} / 5</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-thumbs-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Recomendarían</span>
                    <span class="info-box-number">{{ $recomendarian }} / {{ $encuestas->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Encuestas del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
                al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
            </h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover table-sm mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th class="text-center">Atención Médica</th>
                        <th class="text-center">Enfermería</th>
                        <th class="text-center">Calidad General</th>
                        <th class="text-center">Recomienda</th>
                        <th>Nivel</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($encuestas as $i => $enc)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ optional($enc->paciente)->nombre_completo ?? '—' }}</td>
                        <td class="text-center">{{ $enc->atencion_medica ?? '—' }}/5</td>
                        <td class="text-center">{{ $enc->atencion_enfermeria ?? '—' }}/5</td>
                        <td class="text-center">
                            <span class="badge badge-{{ $enc->calidad_general >= 4 ? 'success' : ($enc->calidad_general >= 3 ? 'warning' : 'danger') }}">
                                {{ $enc->calidad_general ?? '—' }}/5
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-{{ $enc->recomendaria ? 'success' : 'danger' }}">
                                {{ $enc->recomendaria ? 'Sí' : 'No' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $enc->nivel_satisfaccion === 'Excelente' ? 'success' : ($enc->nivel_satisfaccion === 'Bueno' ? 'info' : ($enc->nivel_satisfaccion === 'Regular' ? 'warning' : 'danger')) }}">
                                {{ $enc->nivel_satisfaccion }}
                            </span>
                        </td>
                        <td>{{ $enc->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No hay encuestas en este período
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($encuestas->count() > 0)
        <div class="card-footer text-muted text-right">
            Promedio general: <strong>{{ $promedioGeneral }}/5</strong> —
            Recomendarían: <strong>{{ $recomendarian }}</strong> de {{ $encuestas->count() }}
        </div>
        @endif
    </div>

@endsection
