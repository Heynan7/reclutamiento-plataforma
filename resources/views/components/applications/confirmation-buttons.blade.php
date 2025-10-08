@if($application->availability_response)
    <p class="mt-2">
        <strong>🙋‍♂️ Tu respuesta:</strong>
        @if($application->availability_response === 'accepted')
            ✅ Confirmada el {{ \Carbon\Carbon::parse($application->availability_confirmed_at)->format('d/m/Y H:i') }}
        @else
            ❌ Rechazada el {{ \Carbon\Carbon::parse($application->availability_confirmed_at)->format('d/m/Y H:i') }}
        @endif
    </p>
@else
    <div class="mt-2 flex gap-3">
        <form method="POST" action="{{ route('applications.availability.post', $application->id) }}">
            @csrf
            <input type="hidden" name="response" value="accepted">
            <button type="submit"
                    class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700 transition">
                ✅ Confirmar asistencia
            </button>
        </form>
        <form method="POST" action="{{ route('applications.availability.post', $application->id) }}">
            @csrf
            <input type="hidden" name="response" value="declined">
            <button type="submit"
                    class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition">
                ❌ No puedo asistir
            </button>
        </form>
    </div>
@endif
