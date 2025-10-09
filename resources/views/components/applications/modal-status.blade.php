<!-- Modal principal de cambio de estado -->
<div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
  <div class="bg-white p-6 rounded-lg w-full max-w-lg shadow-lg">
    <h3 class="text-lg font-bold mb-4 text-gray-800">
      Actualizar estado de los seleccionados
    </h3>

    <p class="text-xs text-gray-600 dark:text-gray-400 leading-snug mb-3">
      💡 Puedes actualizar el estado tantas veces como sea necesario.
      El aspirante recibirá una notificación y verá los cambios en su panel.
    </p>

    {{-- Estado --}}
    <div class="mb-3">
      <label class="block text-sm font-medium">Nuevo estado</label>
      <select id="status" name="status" form="bulkForm" class="border rounded w-full px-2 py-1 text-sm">
        <option value="shortlisted">⭐ Preseleccionado</option>
        <option value="interview_scheduled">👔 Entrevista preliminar</option>
        <option value="psychotest">🧠 Pruebas psicométricas</option>
        <option value="interview_deep">🎤 Entrevista profunda</option>
        <option value="socioeconomic_study">🏠 Estudio socioeconómico</option>
        <option value="hired">✅ Contratado</option>
        <option value="rejected">❌ No contratado</option>
      </select>
    </div>

    {{-- Submódulos --}}
    <div id="section-interview" class="hidden">
      @include('components.applications.fields-interview')
    </div>

    <div id="section-psychotest" class="hidden">
      @include('components.applications.fields-psychotest')
    </div>

    <div id="section-socioeconomic" class="hidden">
      @include('components.applications.fields-socioeconomic')
    </div>

    {{-- Botones --}}
    <div class="flex justify-end gap-2 mt-4">
      <button type="button" id="closeModal"
              class="bg-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-400">
        Cancelar
      </button>
      <button type="submit" form="bulkForm"
              class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
        Guardar cambios
      </button>
    </div>
  </div>
</div>

<script>
  (function () {
    const modal = document.getElementById('modal');
    const closeBtn = document.getElementById('closeModal');
    const status = document.getElementById('status');

    const secInterview  = document.getElementById('section-interview');
    const secPsychotest = document.getElementById('section-psychotest');
    const secSocio      = document.getElementById('section-socioeconomic');

    const inputInterviewAt      = document.querySelector('input[name="interview_at"]');
    const inputInterviewChannel = document.querySelector('input[name="interview_channel"]');
    const inputInterviewLink    = document.querySelector('input[name="interview_link"]');

    const chkInternalPsy = document.getElementById('use_internal_psychotest');
    const inputPsyLink   = document.getElementById('psychotest_link');

    function toggleSections() {
      const v = status.value;
      const showInterview  = (v === 'interview_scheduled' || v === 'interview_deep');
      const showPsychotest = (v === 'psychotest');
      const showSocio      = (v === 'socioeconomic_study');

      secInterview.classList.toggle('hidden', !showInterview);
      secPsychotest.classList.toggle('hidden', !showPsychotest);
      secSocio.classList.toggle('hidden', !showSocio);

      if (inputInterviewAt)      inputInterviewAt.required      = showInterview;
      if (inputInterviewChannel) inputInterviewChannel.required = showInterview;
      if (inputInterviewLink)    inputInterviewLink.required    = false;

      if (showPsychotest) {
        handleInternalPsyToggle();
      } else {
        if (inputPsyLink) { inputPsyLink.disabled = false; inputPsyLink.required = false; }
        if (chkInternalPsy) chkInternalPsy.checked = false;
      }
    }

    function handleInternalPsyToggle() {
      if (!chkInternalPsy || !inputPsyLink) return;
      const on = chkInternalPsy.checked;
      inputPsyLink.disabled = on;
      inputPsyLink.required = !on;
      if (on) inputPsyLink.value = '';
    }

    status?.addEventListener('change', toggleSections);
    chkInternalPsy?.addEventListener('change', handleInternalPsyToggle);

    closeBtn?.addEventListener('click', () => {
      modal.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    });

    window.openBulkStatusModal = function () {
      modal.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
      toggleSections();
    };

    toggleSections();
  })();
</script>
