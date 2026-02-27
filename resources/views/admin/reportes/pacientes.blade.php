@extends('layouts.adminlte')

@section('title', 'Reporte de Pacientes')
@section('page-title', 'Reporte de Pacientes')

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

    {{-- Encabezado imprimible (solo visible al imprimir) --}}
    <div class="print-header text-center mb-4">
        <h2>Clínica Eden — Reporte de Pacientes</h2>
        <p>Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
        <p>Generado: {{ now()->format('d/m/Y H:i') }}</p>
        <hr>
    </div>

    {{-- Barra de acciones --}}
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <div>
            <a href="{{ route('admin.reportes') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
        <button onclick="window.print()" class="btn btn-danger btn-sm">
            <i class="fas fa-file-pdf mr-1"></i> Descargar PDF
        </button>
    </div>

    {{-- Filtro de período --}}
    <div class="card card-outline card-info no-print">
        <div class="card-body py-2">
            <form method="GET" class="form-inline">
                <label class="mr-2">Período:</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="form-control form-control-sm mr-2">
                <span class="mr-2">al</span>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control form-control-sm mr-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter mr-1"></i> Filtrar
                </button>
            </form>
        </div>
    </div>

    {{-- Resumen --}}
    <div class="row">
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Pacientes</span>
                    <span class="info-box-number">{{ $pacientes->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-notes-medical"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Con Consultas</span>
                    <span class="info-box-number">{{ $pacientes->filter(fn($p) => $p->consultas->count() > 0)->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Sin Consultas</span>
                    <span class="info-box-number">{{ $pacientes->filter(fn($p) => $p->consultas->count() === 0)->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                Pacientes registrados del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
                al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
            </h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover table-sm mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>DNI</th>
                        <th>Nombre Completo</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>F. Registro</th>
                        <th class="text-center">Consultas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pacientes as $i => $paciente)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $paciente->dni }}</td>
                        <td>{{ $paciente->nombres }} {{ $paciente->apellidos }}</td>
                        <td>{{ $paciente->email ?? '—' }}</td>
                        <td>{{ $paciente->telefono ?? '—' }}</td>
                        <td>{{ $paciente->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $paciente->consultas->count() }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No hay pacientes registrados en este período
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pacientes->count() > 0)
        <div class="card-footer text-muted text-right">
            Total: <strong>{{ $pacientes->count() }}</strong> pacientes
        </div>
        @endif
    </div>

@endsection
