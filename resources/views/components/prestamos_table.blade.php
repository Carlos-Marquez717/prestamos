@foreach ($prestamos as $prestamo)
<tr>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ $prestamo->created_at->format('Y-m-d') }}</td>
    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">{{ number_format($prestamo->monto, 2) }}</td>
</tr>
@endforeach
