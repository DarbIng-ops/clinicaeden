@extends('layouts.adminlte')

@section('title', 'Panel de Administración')

@section('page-title', 'Panel de Administración')

@section('content')
    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Usuarios</span>
                    <span class="info-box-number">10</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-user-md"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Médicos</span>
                    <span class="info-box-number">5</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-injured"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pacientes</span>
                    <span class="info-box-number">150</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-calendar-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Citas Hoy</span>
                    <span class="info-box-number">12</span>
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
                    <p>Este es el panel de <strong>Administración</strong> de ClinicaEden.</p>
                    <p class="text-muted">Rol: {{ auth()->user()->role }}</p>
                    <p>Desde aquí puedes gestionar usuarios, médicos, pacientes y toda la configuración del sistema.</p>
                </div>
            </div>
        </div>
    </div>
@endsection