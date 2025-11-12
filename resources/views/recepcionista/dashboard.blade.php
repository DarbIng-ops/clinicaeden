@extends('layouts.adminlte')

@section('title', 'Panel de Recepción')

@section('page-title', 'Panel de Recepción')

@section('content')
    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-calendar-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Citas Hoy</span>
                    <span class="info-box-number">15</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-plus"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Nuevos Pacientes</span>
                    <span class="info-box-number">3</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">En Espera</span>
                    <span class="info-box-number">5</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bienvenido, {{ auth()->user()->name }}</h3>
                </div>
                <div class="card-body">
                    <p>Este es el panel de <strong>Recepción</strong>.</p>
                    <p class="text-muted">Rol: {{ auth()->user()->role }}</p>
                    <p>Desde aquí puedes registrar pacientes, agendar citas y gestionar la agenda de la clínica.</p>
                </div>
            </div>
        </div>
    </div>
@endsection