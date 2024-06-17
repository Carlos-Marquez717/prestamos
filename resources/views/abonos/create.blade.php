@extends('layouts.app')

@section ('css')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

@endsection

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
                    
                    <input type="text" name="fecha" id="fecha" placeholder="dd-mm-yyyy"  class="w-full px-3 py-2 rounded-md bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500" value="{{ old('fecha') }}" required>
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
    @section('js')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
        <script>
            // Script para limpiar el formulario después de enviar
            document.getElementById('abonoForm').addEventListener('submit', function() {
                // No limpiamos los campos aquí para asegurar que se envíen al servidor
            });
        </script>

        <script>   
            $(document).ready(function() {
                $('#fecha').datepicker({
                    dateFormat: 'dd-mm-yy'
                });
            });
        </script>

    @endsection
@endsection
