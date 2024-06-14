@extends('layouts.app')

@section('content')
    <br><br><br><br>
    <div class="max-w-md mx-auto px-4 py-8">
        <div class="bg-black rounded-lg shadow-md px-4 py-4">
            <h1 class="text-2xl font-bold mb-4 text-center text-white">Registrar Abono</h1>
            
            <form id="abonoForm" action="{{ route('abonos.store', $prestamo) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="monto" class="block text-white">Monto</label>
                    <input type="text" name="monto" id="monto" class="w-full px-3 py-2 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ old('monto') }}" required>
                    @error('monto')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fecha" class="block text-white">Fecha</label>
                    <input type="date" name="fecha" id="fecha" class="w-full px-3 py-2 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ old('fecha') }}" required>
                    @error('fecha')
                        <span class="text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Registrar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Script para limpiar el formulario después de enviar
        document.getElementById('abonoForm').addEventListener('submit', function() {
            // No limpiamos los campos aquí para asegurar que se envíen al servidor
        });
    </script>
@endsection
