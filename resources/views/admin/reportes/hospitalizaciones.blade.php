@extends('layouts.adminlte')

@section('title', 'Reporte de Hospitalizaciones')
@section('page-title', 'Reporte de Hospitalizaciones')

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
        <h2>Clínica Eden — Reporte de Hospitalizaciones</h2>
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

    <div class="card card-outline card-warning no-print">
        <div class="card-body py-2">
            <form method="GET" class="form-inline">
                <label class="mr-2">Período:</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="form-control form-control-sm mr-2">
                <span class="mr-2">al</span>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control form-control-sm mr-2">
                <button type="submit" class="btn btn-warning btn-sm">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-hospital"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Ingresos</span>
                    <span class="info-box-number">{{ $hospitalizaciones->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-bed"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Activos</span>
                    <span class="info-box-number">{{ $hospitalizaciones->whereIn('estado', ['activo', 'activa', 'hospitalizado'])->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-sign-out-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Con Alta</span>
                    <span class="info-box-number">{{ $hospitalizaciones->whereNotNull('fecha_alta')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Hospitalizaciones del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
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
                        <th>Diagnóstico de Ingreso</th>
                        <th>F. Ingreso</th>
                        <th>F. Alta</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hospitalizaciones as $i => $h)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            {{ optional($h->paciente)->nombre_completo ?? '—' }}
                            <br><small class="text-muted">{{ optional($h->paciente)->dni }}</small>
                        </td>
                        <td>{{ optional($h->medico)->name ?? '—' }}</td>
                        <td class="text-truncate" style="max-width:160px;" title="{{ $h->diagnostico_ingreso }}">
                            {{ \Illuminate\Support\Str::limit($h->diagnostico_ingreso ?? '—', 40) }}
                        </td>
                        <td>{{ $h->fecha_ingreso ? \Carbon\Carbon::parse($h->fecha_ingreso)->format('d/m/Y') : '—' }}</td>
                        <td>{{ $h->fecha_alta ? \Carbon\Carbon::parse($h->fecha_alta)->format('d/m/Y') : '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $h->fecha_alta ? 'success' : 'warning' }}">
                                {{ $h->fecha_alta ? 'Alta' : 'Internado' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No hay hospitalizaciones en este período
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($hospitalizaciones->count() > 0)
        <div class="card-footer text-muted text-right">
            Total: <strong>{{ $hospitalizaciones->count() }}</strong> registros
        </div>
        @endif
    </div>

@endsection
