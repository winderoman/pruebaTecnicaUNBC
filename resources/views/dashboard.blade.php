<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard - Sistema Satélite SIGE') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Tarjetas de Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                
                <!-- Total Docentes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Docentes</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_docentes']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Docentes Idóneos -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Idóneos (OK)</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['docentes_idoneos']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Docentes No Idóneos -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">No Idóneos</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['docentes_no_idoneos']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Instituciones -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Instituciones</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_instituciones']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Docentes por Función -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Docentes por Función Principal</h3>
                        <div class="space-y-3">
                            @foreach($docentesPorFuncion as $funcion)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">{{ $funcion->funcion_principal }}</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $funcion->total }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($funcion->total / $stats['total_docentes']) * 100 }}%"></div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Últimas Cargas -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Últimas Cargas de Datos</h3>
                        <div class="space-y-4">
                            @foreach($ultimasCargas as $carga)
                            <div class="border-l-4 border-blue-500 pl-4">
                                <p class="text-sm font-medium text-gray-900">{{ $carga->nombre_esta }}</p>
                                <p class="text-xs text-gray-500">RBD: {{ $carga->rbd }}</p>
                                <div class="flex justify-between items-center mt-1">
                                    <span class="text-xs text-gray-400">{{ $carga->fecha_carga->format('d/m/Y') }}</span>
                                    <span class="text-xs font-semibold text-blue-600">{{ $carga->total }} docentes</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <!-- Botón para ver lista completa -->
            <div class="mt-6">
                <a href="{{ route('docentes.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Ver Lista Completa de Docentes
                </a>
            </div>

            <a href="{{ route('importacion.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
                Importar Datos
            </a>

        </div>
    </div>
</x-app-layout>