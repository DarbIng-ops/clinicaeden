<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo que representa a los pacientes de la clínica.
 *
 * Administra información personal, contactos de emergencia, historial clínico
 * y relaciones con consultas, hospitalizaciones y citas.
 */
class Paciente extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * @var array<int, string> Atributos asignables en masa
     */
    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'tipo_sangre',
        'alergias',
        'enfermedades_cronicas',
        'contacto_emergencia_nombre',
        'contacto_emergencia_telefono',
        'foto',
        'activo',
    ];

    /**
     * @var array<string, string> Conversión automática de atributos
     */
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
    ];

    /**
     * Boot del modelo - ejecuta validaciones y mutators automáticos
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        // Mutators automáticos al crear/actualizar
        static::creating(function ($paciente) {
            $paciente->dni = strtoupper(trim($paciente->dni));
            $paciente->nombres = ucwords(strtolower(trim($paciente->nombres)));
            $paciente->apellidos = ucwords(strtolower(trim($paciente->apellidos)));
            $paciente->email = strtolower(trim($paciente->email));
            $paciente->ciudad = ucwords(strtolower(trim($paciente->ciudad)));
            $paciente->contacto_emergencia_nombre = ucwords(strtolower(trim($paciente->contacto_emergencia_nombre)));
        });
        
        static::updating(function ($paciente) {
            $paciente->dni = strtoupper(trim($paciente->dni));
            $paciente->nombres = ucwords(strtolower(trim($paciente->nombres)));
            $paciente->apellidos = ucwords(strtolower(trim($paciente->apellidos)));
            $paciente->email = strtolower(trim($paciente->email));
            $paciente->ciudad = ucwords(strtolower(trim($paciente->ciudad)));
            $paciente->contacto_emergencia_nombre = ucwords(strtolower(trim($paciente->contacto_emergencia_nombre)));
        });
    }

    // Relaciones
    /**
     * Relación: citas asociadas al paciente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    /**
     * Relación: historia clínica del paciente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function historiaClinica()
    {
        return $this->hasOne(HistoriaClinica::class);
    }

    /**
     * Relación: consultas médicas registradas para el paciente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function consultas()
    {
        return $this->hasMany(Consulta::class);
    }

    // Accessor para nombre completo
    /**
     * Obtener el nombre completo del paciente.
     *
     * @return string
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    // Accessor para edad
    /**
     * Obtener la edad actual del paciente.
     *
     * @return int|null
     */
    public function getEdadAttribute()
    {
        return $this->fecha_nacimiento?->age;
    }

    // Scopes para búsquedas frecuentes
    /**
     * Scope para filtrar pacientes activos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para buscar un paciente por DNI.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @param string $dni Documento a localizar
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorDni($query, $dni)
    {
        return $query->where('dni', strtoupper(trim($dni)));
    }

    /**
     * Scope para búsquedas generales por término.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @param string|null $termino Texto a buscar
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBuscar($query, $termino)
    {
        if (empty($termino)) {
            return $query;
        }
        
        return $query->where(function($q) use ($termino) {
            $q->where('nombres', 'like', "%{$termino}%")
              ->orWhere('apellidos', 'like', "%{$termino}%")
              ->orWhere('dni', 'like', "%{$termino}%")
              ->orWhere('email', 'like', "%{$termino}%");
        });
    }

    /**
     * Scope para filtrar por sexo biológico declarado.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @param string $sexo Valor de sexo a filtrar
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorSexo($query, $sexo)
    {
        return $query->where('sexo', $sexo);
    }

    /**
     * Scope para filtrar pacientes por ciudad.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Consulta base
     * @param string $ciudad Ciudad o parte de ella
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePorCiudad($query, $ciudad)
    {
        return $query->where('ciudad', 'like', "%{$ciudad}%");
    }

    // Métodos auxiliares
    /**
     * Determinar si el paciente reporta alergias.
     *
     * @return bool
     */
    public function tieneAlergias()
    {
        return !empty($this->alergias);
    }

    /**
     * Determinar si el paciente reporta enfermedades crónicas.
     *
     * @return bool
     */
    public function tieneEnfermedadesCronicas()
    {
        return !empty($this->enfermedades_cronicas);
    }

    /**
     * Verificar si el paciente es menor de edad.
     *
     * @return bool
     */
    public function esMenorDeEdad()
    {
        return ($this->edad ?? 0) < 18;
    }

    /**
     * Confirmar si el paciente tiene medios de contacto registrados.
     *
     * @return bool
     */
    public function puedeSerContactado()
    {
        return !empty($this->telefono) || !empty($this->email);
    }

    public function hospitalizaciones()
    {
        return $this->hasMany(Hospitalizacion::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    public function encuestasSatisfaccion()
    {
        return $this->hasMany(EncuestaSatisfaccion::class);
    }
}