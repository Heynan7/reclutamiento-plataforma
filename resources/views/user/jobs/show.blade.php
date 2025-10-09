<x-app-layout>  
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $job->title }}
        </h2>
    </x-slot>

    <div class="py-6 px-4 max-w-3xl mx-auto">

        {{-- Mensajes de éxito o error --}}
        @if (session('success'))
            <div id="feedback" class="mb-4 p-4 rounded bg-green-100 text-green-700 border border-green-400">
                {{ session('success') }}
            </div>
        @elseif (session('error'))
            <div id="feedback" class="mb-4 p-4 rounded bg-red-100 text-red-700 border border-red-400">
                {{ session('error') }}
            </div>
        @endif

        <script>
            setTimeout(function () {
                const msg = document.getElementById('feedback');
                if (msg) {
                    msg.style.opacity = '0';
                    setTimeout(() => msg.remove(), 1000);
                }
            }, 4000);
        </script>

        @php
            $myApplication = auth()->check()
                ? \App\Models\Application::where('user_id', auth()->id())
                    ->where('job_id', $job->id)
                    ->latest()
                    ->first()
                : null;

            $statusBadge = '';
            if ($myApplication) {
                switch ($myApplication->status) {
                    case 'submitted':
                        $statusBadge = '<span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">Recibido</span>';
                        break;
                    case 'shortlisted':
                        $statusBadge = '<span class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">Preseleccionado</span>';
                        break;
                    case 'interview_scheduled':
                        $statusBadge = '<span class="px-2 py-1 rounded text-xs bg-purple-100 text-purple-800">Entrevista</span>';
                        break;
                    case 'closed':
                        $statusBadge = '<span class="px-2 py-1 rounded text-xs bg-gray-200 text-gray-800">Cerrado</span>';
                        break;
                    default:
                        $statusBadge = '<span class="px-2 py-1 rounded text-xs bg-zinc-100 text-zinc-700">-</span>';
                }
            }
        @endphp

        <div class="bg-white p-6 rounded shadow space-y-6">

            {{-- Descripción primero --}}
            <div>
                <h3 class="text-lg font-bold mb-2 text-indigo-700">Descripción del puesto:</h3>
                <p class="text-gray-700">{{ $job->description }}</p>
            </div>

            {{-- Estado de postulación / Formulario --}}
            @if($myApplication)
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        {!! $statusBadge !!}
                        <a href="{{ route('user.applications.show', $myApplication) }}"
                           class="inline-block px-3 py-1 rounded text-xs bg-indigo-600 text-white hover:bg-indigo-700">
                            Ver estado de mi postulación
                        </a>
                    </div>
                    <p class="text-sm text-gray-600">
                        Ya enviaste tu postulación el {{ $myApplication->created_at->format('d/m/Y H:i') }}.
                        Te avisaremos por email ante cualquier cambio.
                    </p>
                    @if($myApplication->cv_file)
                        <p class="mt-2 text-sm">
                            <a href="{{ route('user.applications.downloadCv', $myApplication) }}" 
                               target="_blank" 
                               class="text-blue-600 hover:underline">
                                📄 Ver CV enviado
                            </a>
                        </p>
                    @endif
                </div>

            @elseif($job->is_open && (method_exists($job, 'trashed') ? !$job->trashed() : true))
                <form method="POST" action="{{ route('user.applications.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="job_id" value="{{ $job->id }}">

                    <div>
                        <label for="cv_file" class="block font-medium text-sm text-gray-700">
                            Sube tu CV <span class="text-red-600">*</span>
                        </label>
                        <input type="file" name="cv_file" id="cv_file"
                               accept="application/pdf"
                               class="mt-1 block w-full border-gray-300 rounded shadow-sm" required>
                        <p class="text-xs text-gray-500 mt-1">Solo se permiten archivos en formato PDF (máx. 5 MB).</p>
                        @error('cv_file')
                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="cover_letter" class="block font-medium text-sm text-gray-700">
                            Carta de presentación (opcional)
                        </label>
                        <textarea name="cover_letter" id="cover_letter" rows="4"
                                  class="mt-1 block w-full border-gray-300 rounded shadow-sm"></textarea>
                        @error('cover_letter')
                            <div class="text-red-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                        Postularme
                    </x-primary-button>
                </form>

            @else
                <div class="p-4 rounded bg-yellow-50 text-yellow-800 border border-yellow-200">
                    Esta vacante ya no recibe postulaciones (cerrada o archivada).
                </div>
            @endif

            {{-- Imagen destacada al final --}}
            @if($job->image)
                <div class="w-full bg-gray-50 rounded overflow-hidden">
                    <img 
                        src="{{ route('user.jobs.image', $job->id) }}" 
                        alt="Imagen de la vacante {{ $job->title }}"
                        class="w-full max-h-[500px] object-contain mx-auto"
                    >
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
