<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CsvImportService;

class ImportarIdoneidadDocente extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sige:importar-idoneidad 
                            {archivo : Ruta del archivo CSV a importar}
                            {--fecha= : Fecha de carga (YYYY-MM-DD). Por defecto: hoy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar datos de Idoneidad Docente desde archivo CSV';

    /**
     * Execute the console command.
     */
    public function handle(CsvImportService $importService)
    {
        $archivo = $this->argument('archivo');
        $fecha = $this->option('fecha');

        $this->info('Iniciando importación de Idoneidad Docente...');
        $this->info("Archivo: {$archivo}");

        if (!file_exists($archivo)) {
            $this->error("❌ El archivo no existe: {$archivo}");
            return 1;
        }

        try {
            $stats = $importService->importarIdoneidadDocente($archivo, $fecha);

            $this->newLine();
            $this->info('Importación completada exitosamente');
            $this->newLine();
            
            $this->table(
                ['Métrica', 'Cantidad'],
                [
                    ['Total registros procesados', $stats['total']],
                    ['Nuevos importados', $stats['importados']],
                    ['Actualizados', $stats['actualizados']],
                    ['Errores', $stats['errores']],
                ]
            );

            if ($stats['errores'] > 0 && count($stats['errores_detalle']) > 0) {
                $this->newLine();
                $this->warn('Detalles de errores:');
                
                foreach (array_slice($stats['errores_detalle'], 0, 5) as $error) {
                    $this->line("  Línea {$error['linea']}: {$error['error']}");
                }

                if (count($stats['errores_detalle']) > 5) {
                    $this->line("  ... y " . (count($stats['errores_detalle']) - 5) . " errores más");
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error durante la importación: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}