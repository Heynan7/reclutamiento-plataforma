<div id="interviewFields" class="hidden">
    {{-- Medio de la entrevista --}}
    <div class="mb-3">
        <label for="interview_channel" class="block text-sm font-medium">Medio</label>
        <select id="interview_channel" name="interview_channel" form="bulkForm"
                class="border rounded w-full px-2 py-1 text-sm">
            <option value="">Seleccione medio</option>
            <option value="Zoom">Zoom</option>
            <option value="Google Meet">Google Meet</option>
            <option value="Teléfono">Teléfono</option>
            <option value="Presencial">Presencial</option>
        </select>
    </div>

{{-- Fecha y hora con calendario visual Flatpickr --}}
<div class="mb-3">
    <label for="interview_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
        Fecha y hora
    </label>
    <input type="text" id="interview_at" name="interview_at" form="bulkForm"
           class="border rounded-lg w-full px-3 py-2 text-sm bg-white dark:bg-gray-800 dark:text-gray-100
                  focus:ring-2 focus:ring-sky-400 focus:border-sky-400 transition"
           placeholder="Selecciona fecha y hora">
</div>

    {{-- Enlace o ubicación --}}
    <div class="mb-3">
        <label for="interview_link" class="block text-sm font-medium">Link o ubicación</label>
        <input type="text" id="interview_link" name="interview_link" form="bulkForm"
               class="border rounded w-full px-2 py-1 text-sm"
               placeholder="Ej. enlace de reunión o dirección física">
    </div>

    {{-- Mensaje adicional --}}
    <div class="mb-3">
        <label for="interview_message" class="block text-sm font-medium">Mensaje adicional</label>
        <textarea id="interview_message" name="interview_message" rows="3" form="bulkForm"
                  class="border rounded w-full px-2 py-1 text-sm resize-none"
                    placeholder="Ej. Instrucciones, recomendaciones, etc."></textarea>
    </div>
{{-- Recordatorio informativo --}}
<p class="text-xs text-gray-600 dark:text-gray-400 leading-snug mt-2">
    💡 Puedes ajustar esta información en cualquier momento si te equivocas.<br>
    Si la entrevista es por teléfono, puedes dejar vacío el campo de enlace o ubicación.<br>
    Verifica bien la fecha, hora y medio antes de guardar.
</p>

</div>
