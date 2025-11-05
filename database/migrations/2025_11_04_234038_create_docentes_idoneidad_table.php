<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('docentes_idoneidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institucion_id')->constrained('instituciones')->onDelete('cascade');
            
            // Datos personales del docente
            $table->string('doc_run', 10)->comment('RUT sin dígito verificador');
            $table->string('doc_dv', 1)->comment('Dígito verificador');
            $table->string('doc_nombre', 100);
            $table->string('doc_paterno', 100);
            $table->string('doc_materno', 100)->nullable();
            $table->char('doc_genero', 1)->comment('M o F');
            $table->date('doc_fec_nac')->nullable();
            
            // Datos del establecimiento (redundante pero útil para consultas)
            $table->string('rbd', 10);
            $table->string('nombre_esta', 255);
            
            // Datos de la función
            $table->string('funcion_principal', 100);
            $table->string('funcion_secundaria', 100)->nullable();
            $table->integer('horas_contrato')->default(0);
            $table->integer('horas_ingles')->default(0);
            $table->integer('horas_directivas')->default(0);
            $table->integer('horas_tecnico_pedagogica')->default(0);
            $table->integer('horas_aula')->default(0);
            $table->integer('horas_pedagogicas_funcion_1')->default(0);
            $table->integer('horas_pedagogicas_funcion_2')->default(0);
            
            // Sectores y subsectores
            $table->string('sector_funcion_1', 150)->nullable();
            $table->string('sub_sector_funcion_1', 150)->nullable();
            $table->string('sector_funcion_2', 150)->nullable();
            $table->string('sub_sector_funcion_2', 150)->nullable();
            
            // Estado de idoneidad
            $table->string('estado_idoneidad', 20)->default('PENDIENTE')->comment('OK, NO OK, PENDIENTE');
            
            // Auditoría
            $table->date('fecha_carga')->comment('Fecha en que se cargó el archivo');
            $table->timestamp('fecha_actualizacion')->useCurrent()->comment('Última actualización del registro');
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index('doc_run');
            $table->index('institucion_id');
            $table->index('rbd');
            $table->index('fecha_carga');
            $table->index('estado_idoneidad');
            
            // Constraint único: no duplicar docente en misma institución y fecha de carga
            $table->unique(['institucion_id', 'doc_run', 'doc_dv', 'fecha_carga'], 'unique_docente_carga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docentes_idoneidad');
    }
};