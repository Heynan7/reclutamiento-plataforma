<div class="overflow-x-auto">
    <table class="w-full text-sm border border-gray-200 shadow-sm rounded-lg">
        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
            <tr>
                <th class="px-3 py-2 border-b text-center">
                    <input type="checkbox" id="selectAll" class="cursor-pointer">
                </th>
                <th class="px-3 py-2 border-b text-left">Candidato</th>
                <th class="px-3 py-2 border-b text-left">Correo</th>
                <th class="px-3 py-2 border-b text-left">Teléfono</th>
                <th class="px-3 py-2 border-b text-left">CV</th>
                <th class="px-3 py-2 border-b text-left">Estado</th>
                <th class="px-3 py-2 border-b text-left">Ranking IA</th>
                <th class="px-3 py-2 border-b text-left">Disponibilidad</th>
                <th class="px-3 py-2 border-b text-left">Psicotest</th>
            </tr>
        </thead>

        <tbody>
        @foreach ($applications as $application)
            <tr class="hover:bg-gray-50 transition">
                {{-- ✅ Checkbox --}}
                <td class="px-3 py-2 border-b text-center">
                    <input type="checkbox" name="applications[]" value="{{ $application->id }}" class="cursor-pointer">
                </td>

                {{-- 👤 Candidato --}}
                <td class="px-3 py-2 border-b font-semibold text-indigo-700">
                    <a href="{{ route('admin.applications.show', $application->id) }}" class="hover:underline">
                        {{ $application->user->name }}
                    </a>
                </td>

                {{-- 📧 Correo --}}
                <td class="px-3 py-2 border-b text-gray-700">
                    {{ $application->user->email }}
                </td>

                {{-- 📞 Teléfono --}}
                <td class="px-3 py-2 border-b text-gray-700">
                    @if(!empty($application->user->phone))
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $application->user->phone) }}"
                           target="_blank"
                           class="text-green-700 hover:underline">
                            {{ $application->user->phone }}
                        </a>
                    @else
                        <span class="text-gray-400 text-xs">Sin teléfono</span>
                    @endif
                </td>

                {{-- 📄 CV --}}
                <td class="px-3 py-2 border-b">
                    @if($application->cv_file)
                        <a href="{{ route('admin.applications.downloadCv', $application->id) }}"
                           target="_blank"
                           class="text-blue-600 hover:underline">
                            📄 Ver CV
                        </a>
                    @else
                        <span class="text-gray-500 text-xs">No adjuntado</span>
                    @endif
                </td>

                {{-- 🔖 Estado --}}
                <td class="px-3 py-2 border-b">
                    @php
                        $displayStatus = $application->status;
                        if ($application->status === 'closed' && $application->status_before_closed) {
                            $displayStatus = $application->status_before_closed;
                        }
                    @endphp
                    @include('components.application-status-badge', ['status' => $displayStatus])
                </td>

                {{-- 🤖 Ranking IA --}}
                <td class="px-3 py-2 border-b">
                    @if($application->ranking)
                        <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800 font-bold">
                            {{ $application->ranking->score }} / 100
                        </span>
                        @if(!empty($application->ranking->analysis))
                            <button type="button"
                                    data-analysis='@json($application->ranking->analysis)'
                                    class="ml-2 text-blue-600 text-xs underline open-analysis">
                                Ver detalles
                            </button>
                        @endif
                    @else
                        <span class="text-gray-400 text-xs">Pendiente</span>
                    @endif
                </td>

                {{-- 📅 Disponibilidad (incluye estudio socioeconómico) --}}
                <td class="px-3 py-2 border-b">
                    @php
                        $showAvailability = in_array($application->status, [
                            'interview_scheduled',
                            'interview_deep',
                            'socioeconomic_study',
                        ], true);
                    @endphp

                    @if($showAvailability)
                        @if($application->availability_response === 'accepted')
                            <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">✅ Confirmado</span>
                        @elseif($application->availability_response === 'declined')
                            <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800">❌ No asiste</span>
                        @else
                            <span class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">⏳ Pendiente</span>
                        @endif

                        {{-- Link al estudio cuando aplique --}}
                        @if($application->status === 'socioeconomic_study' && $application->socioeconomic_link)
                            <a href="{{ $application->socioeconomic_link }}" target="_blank"
                               class="ml-2 text-indigo-700 underline text-xs">
                                Abrir estudio
                            </a>
                        @endif
                    @else
                        <span class="text-gray-400 text-xs">N/A</span>
                    @endif
                </td>

                {{-- 🧠 Psicotest --}}
                <td class="px-3 py-2 border-b">
                    @php
                        $sc = $application->psychotest_score;
                        $badgeClass = 'bg-gray-100 text-gray-800';
                        if (!is_null($sc)) {
                            if ($sc >= 80) $badgeClass = 'bg-green-50 text-green-800';
                            elseif ($sc >= 50) $badgeClass = 'bg-yellow-50 text-yellow-800';
                            else $badgeClass = 'bg-red-50 text-red-800';
                        }
                    @endphp

                    @if(!is_null($application->psychotest_score) && $application->psychotest_completed_at)
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $badgeClass }}">
                                {{ number_format($sc, 0) }} / 100
                            </span>
                            <a href="{{ route('admin.applications.psychotestResults', $application->id) }}"
                               class="text-blue-600 text-xs underline">
                               Ver resultados
                            </a>
                        </div>
                    @elseif($application->status === 'psychotest')
                        <span class="text-yellow-600 text-xs">Pendiente</span>
                    @else
                        <span class="text-gray-400 text-xs">N/A</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
