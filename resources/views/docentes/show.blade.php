<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle del Docente') }}
            </h2>
            <a href="{{ route('docentes.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
                ← Volver al listado
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Información Personal -->
                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Información Personal</h3>
                        
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">RUT</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $docente->rut_completo }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nombre Completo</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $docente->nombre_completo }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Género</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $docente->doc_genero == 'M' ? 'Masculino' : 'Femenino' }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha de Nacimiento</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $docente->doc_fec_nac ? $docente->doc_fec_nac->format('d/m/Y') : 'No registrada' }}
                                </dd>
                            </div>
                        </dl>

                        <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-4 border-b pb-2">Institución</h3>
                        
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">RBD</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $docente->rbd }}</dd>
                            </div>
                            
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Nombre Establecimiento</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $docente->nombre_esta }}</dd>
                            </div>
                        </dl>

                        <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-4 border-b pb-2">Información Laboral</h3>
                        
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Función Principal</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $docente->funcion_principal }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Función Secundaria</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $docente->funcion_secundaria ?? 'N/A' }}</dd>
                            </div>
                        </dl>

                        <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-4 border-b pb-2">Distribución de Horas</h3>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <dt class="text-xs font-medium text-blue-600 uppercase">Contrato</dt>
                                <dd class="mt-1 text-2xl font-semibold text-blue-900">{{ $docente->horas_contrato }}h</dd>
                            </div>
                            
                            <div class="bg-green-50 p-3 rounded-lg">
                                <dt class="text-xs font-medium text-green-600 uppercase">Aula</dt>
                                <dd class="mt-1 text-2xl font-semibold text-green-900">{{ $docente->horas_aula }}h</dd>
                            </div>
                            
                            <div class="bg-purple-50 p-3 rounded-lg">
                                <dt class="text-xs font-medium text-purple-600 uppercase">Inglés</dt>
                                <dd class="mt-1 text-2xl font-semibold text-purple-900">{{ $docente->horas_ingles }}h</dd>
                            </div>
                            
                            <div class="bg-indigo-50 p-3 rounded-lg">
                                <dt class="text-xs font-medium text-indigo-600 uppercase">Directivas</dt>
                                <dd class="mt-1 text-2xl font-semibold text-indigo-900">{{ $docente->horas_directivas }}h</dd>
                            </div>
                            
                            <div class="bg-yellow-50 p-3 rounded-lg">
                                <dt class="text-xs font-medium text-yellow-600 uppercase">Téc. Pedagógica</dt>
                                <dd class="mt-1 text-2xl font-semibold text-yellow-900">{{ $docente->horas_tecnico_pedagogica }}h</dd>
                            </div>
                            
                            <div class="bg-pink-50 p-3 rounded-lg">
                                <dt class="text-xs font-medium text-pink-600 uppercase">Pedagógicas F1</dt>
                                <dd class="mt-1 text-2xl font-semibold text-pink-900">{{ $docente->horas_pedagogicas_funcion_1 }}h</dd>
                            </div>
                        </div>

                        @if($docente->sector_funcion_1 || $docente->sector_funcion_2)
                        <h3 class="text-lg font-semibold text-gray-900 mt-6 mb-4 border-b pb-2">Sectores y Subsectores</h3>
                        
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($docente->sector_funcion_1)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sector Función 1</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $docente->sector_funcion_1 }}</dd>
                                @if($docente->sub_sector_funcion_1)
                                <dd class="mt-1 text-xs text-gray-500">{{ $docente->sub_sector_funcion_1 }}</dd>
                                @endif
                            </div>
                            @endif
                            
                            @if($docente->sector_funcion_2)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sector Función 2</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $docente->sector_funcion_2 }}</dd>
                                @if($docente->sub_sector_funcion_2)
                                <dd class="mt-1 text-xs text-gray-500">{{ $docente->sub_sector_funcion_2 }}</dd>
                                @endif
                            </div>
                            @endif
                        </dl>
                        @endif

                    </div>
                </div>

                <!-- Panel Lateral -->
                <div class="space-y-6">
                    
                    <!-- Estado de Idoneidad -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Estado de Idoneidad</h3>
                            <div class="flex items-center justify-center p-4 rounded-lg
                                {{ $docente->estado_idoneidad == 'OK' ? 'bg-green-100' : 'bg-red-100' }}">
                                <span class="text-3xl font-bold
                                    {{ $docente->estado_idoneidad == 'OK' ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $docente->estado_idoneidad }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Auditoría -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Auditoría</h3>
                            
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fecha de Carga</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $docente->fecha_carga->format('d/m/Y') }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Última Actualización</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $docente->fecha_actualizacion->format('d/m/Y H:i') }}</dd>
                                </div>
                                
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Registrado el</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $docente->created_at->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>