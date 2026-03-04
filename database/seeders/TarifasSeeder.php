<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tarifa;

class TarifasSeeder extends Seeder
{
    public function run(): void
    {
        $tarifas = [
            // ── Consultas ──────────────────────────────────────
            ['consultas', 'Consulta médica general',          35000],
            ['consultas', 'Consulta médica especialista',     80000],
            ['consultas', 'Consulta de urgencias',            55000],
            ['consultas', 'Control / seguimiento',            25000],

            // ── Enfermería ─────────────────────────────────────
            ['enfermeria', 'Toma de signos vitales',          8000],
            ['enfermeria', 'Aplicación de inyección IM/IV',  12000],
            ['enfermeria', 'Curación simple',                 20000],
            ['enfermeria', 'Curación compleja',               45000],
            ['enfermeria', 'Toma de muestra laboratorio',    15000],
            ['enfermeria', 'Suero / hidratación IV',          35000],

            // ── Hospitalización ────────────────────────────────
            ['hospitalizacion', 'Habitación compartida (por día)', 120000],
            ['hospitalizacion', 'Habitación privada (por día)',    220000],
            ['hospitalizacion', 'UCI (por día)',                   450000],

            // ── Procedimientos ─────────────────────────────────
            ['procedimientos', 'Electrocardiograma',          40000],
            ['procedimientos', 'Radiografía simple',          55000],
            ['procedimientos', 'Ecografía abdominal',         90000],
            ['procedimientos', 'Pequeña cirugía ambulatoria', 180000],
            ['procedimientos', 'Sutura simple',               60000],

            // ── Medicamentos ───────────────────────────────────
            ['medicamentos', 'Aplicación medicamento oral',   5000],
            ['medicamentos', 'Nebulización',                  18000],
            ['medicamentos', 'Oxigenoterapia (por hora)',     25000],
        ];

        foreach ($tarifas as [$categoria, $nombre, $precio]) {
            Tarifa::firstOrCreate(
                ['categoria' => $categoria, 'nombre' => $nombre],
                ['precio' => $precio, 'activo' => true]
            );
        }

        $this->command->info('✅ ' . count($tarifas) . ' tarifas creadas.');
    }
}
