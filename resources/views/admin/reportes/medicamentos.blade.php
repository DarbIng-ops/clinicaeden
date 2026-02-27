@extends('layouts.adminlte')

@section('title', 'Reporte de Medicamentos')
@section('page-title', 'Reporte de Medicamentos y Tratamientos')

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
        <h2>Clínica Eden — Reporte de Medicamentos y Tratamientos</h2>
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

    <div class="card card-outline card-secondary no-print">
        <div class="card-body py-2">
            <form method="GET" class="form-inline">
                <label class="mr-2">Período:</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="form-control form-control-sm mr-2">
                <span class="mr-2">al</span>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control form-control-sm mr-2">
                <button type="submit" class="btn btn-secondary btn-sm">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-pills"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Tratamientos</span>
                    <span class="info-box-number">{{ $tratamientos->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Completados</span>
                    <span class="info-box-number">{{ $tratamientos->where('estado', 'completado')->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-dollar-sign"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Costo Total</span>
                    <span class="info-box-number">$ {{ number_format($totalCosto, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Tratamientos del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
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
                        <th>Descripción</th>
                        <th>Dosis</th>
                        <th>Frecuencia</th>
                        <th>Estado</th>
                        <th class="text-right">Costo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tratamientos as $i => $t)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ optional(optional($t->consulta)->paciente)->nombre_completo ?? '—' }}</td>
                        <td>{{ optional(optional($t->consulta)->medico)->name ?? '—' }}</td>
                        <td class="text-truncate" style="max-width:160px;" title="{{ $t->descripcion }}">
                            {{ \Illuminate\Support\Str::limit($t->descripcion ?? '—', 35) }}
                        </td>
                        <td>{{ $t->dosis ?? '—' }}</td>
                        <td>{{ $t->frecuencia ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $t->estado === 'completado' ? 'success' : ($t->estado === 'activo' ? 'info' : 'secondary') }}">
                                {{ ucfirst($t->estado ?? '—') }}
                            </span>
                        </td>
                        <td class="text-right">
                            {{ $t->costo ? '$ ' . number_format($t->costo, 0, ',', '.') : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No hay tratamientos en este período
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($tratamientos->count() > 0)
                <tfoot class="bg-light">
                    <tr>
                        <th colspan="7" class="text-right">Costo Total Estimado:</th>
                        <th class="text-right text-danger">$ {{ number_format($totalCosto, 0, ',', '.') }}</th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

@endsection
