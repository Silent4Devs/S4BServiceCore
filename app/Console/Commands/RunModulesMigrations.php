<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RunModulesMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-modules-migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modulos = [
            [
                'nombre' => 'Capacitaciones Basico',
                'path' => 'Modules\CapacitacionesCore\database\migrations',
                'conexion' => 'capacitaciones_db',
            ],
            [
                'nombre' => 'Capacitaciones Carpeta',
                'path' => 'Modules\CapacitacionesCore\database\migrations\Capacitaciones',
                'conexion' => 'capacitaciones_db',
            ],
            [
                'nombre' => 'Auth',
                'path' => 'Modules\Auth4You\database\migrations',
                'conexion' => 'auth_db',
            ],
        ];

        foreach ($modulos as $modulo) {
            $this->info("Migrando módulo: {$modulo['nombre']}");

            Artisan::call('migrate', [
                '--path' => $modulo['path'],
                '--database' => $modulo['conexion'],
                // '--force' => true, // opcional si lo usarás en producción
            ]);

            $this->info(Artisan::output());
        }

        $this->info('Migraciones de módulos completadas.');
        return 0;
    }
}
