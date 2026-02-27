@extends('layouts.adminlte')

@section('title', 'Reporte de Consultas')
@section('page-title', 'Reporte de Consultas')

@push('styles')
<style>
    @media print {
        .main-sidebar, .main-header, .content-header, .no-print { display: none !important; }
        .content-wrapper { margin-left: 0 !important; padding-top: 0 !important; }
        .card { box-shadow: none !important; border: 1px solid #ddd !important; }
        .print-header { display: block !important; }
    }
    .print-header { display: none; }
</style>
@endpush

@section('content')

    <div class="print-header text-center mb-4">
        <h2>Clínica Eden — Reporte de Consultas</h2>
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

    <div class="card card-outline card-success no-print">
        <div class="card-body py-2">
            <form method="GET" class="form-inline">
                <label class="mr-2">Período:</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="form-control form-control-sm mr-2">
                <span class="mr-2">al</span>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control form-control-sm mr-2">
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-stethoscope"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Consultas</span>
                    <span class="info-box-number">{{ $consultas->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completadas</span>
                    <span class="info-box-number">{{ $consultas->where('estado', 'completada')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pendientes</span>
                    <span class="info-box-number">{{ $consultas->where('estado', 'pendiente')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Consultas del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
                al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
            </h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover table-sm mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Paciente</th>
                        <th>Médico</th>
                        <th>Motivo</th>
                        <th>Diagnóstico</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consultas as $i => $consulta)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            {{ optional($consulta->paciente)->nombre_completo ?? '—' }}
                            <br><small class="text-muted">{{ optional($consulta->paciente)->dni }}</small>
                        </td>
                        <td>{{ optional($consulta->medico)->name ?? '—' }}</td>
                        <td class="text-truncate" style="max-width:150px;" title="{{ $consulta->motivo_consulta ?? $consulta->motivo }}">
                            {{ \Illuminate\Support\Str::limit($consulta->motivo_consulta ?? $consulta->motivo ?? '—', 40) }}
                        </td>
                        <td class="text-truncate" style="max-width:150px;" title="{{ $consulta->diagnostico }}">
                            {{ \Illuminate\Support\Str::limit($consulta->diagnostico ?? '—', 40) }}
                        </td>
                        <td>{{ $consulta->fecha_consulta ? $consulta->fecha_consulta->format('d/m/Y') : '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $consulta->estado === 'completada' ? 'success' : ($consulta->estado === 'pendiente' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($consulta->estado ?? '—') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No hay consultas en este período
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($consultas->count() > 0)
        <div class="card-footer text-muted text-right">
            Total: <strong>{{ $consultas->count() }}</strong> consultas
        </div>
        @endif
    </div>

@endsection
