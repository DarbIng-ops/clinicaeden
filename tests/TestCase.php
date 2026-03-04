<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Garantiza que la app reporte entorno 'testing' (necesario para que
        // VerifyCsrfToken omita la validación durante las pruebas)
        if ($this->app['env'] !== 'testing') {
            $this->app['env'] = 'testing';
        }
    }
}
