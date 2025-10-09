@switch($status)
    @case('submitted')
        <span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">📄 Recibido</span>
        @break
    @case('shortlisted')
        <span class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">⭐ Preseleccionado</span>
        @break
    @case('interview_scheduled')
        <span class="px-2 py-1 rounded text-xs bg-purple-100 text-purple-800">👔 Entrevista preliminar</span>
        @break
    @case('psychotest')
        <span class="px-2 py-1 rounded text-xs bg-pink-100 text-pink-800">🧠 Psicotest</span>
        @break
    @case('interview_deep')
        <span class="px-2 py-1 rounded text-xs bg-indigo-100 text-indigo-800">🎤 Entrevista profunda</span>
        @break
    @case('socioeconomic_study')
        <span class="px-2 py-1 rounded text-xs bg-orange-100 text-orange-800">🏠 Estudio socioeconómico</span>
        @break
    @case('hired')
        <span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">✅ Contratado</span>
        @break
    @case('rejected')
        <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800">❌ No contratado</span>
        @break
    @case('closed')
        <span class="px-2 py-1 rounded text-xs bg-gray-300 text-gray-800">📌 Cerrado</span>
        @break
    @default
        <span class="px-2 py-1 rounded text-xs bg-zinc-100 text-zinc-700">-</span>
@endswitch
