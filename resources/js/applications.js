// ===================================================================
// 📦 applications.js — Control de postulaciones, modales y campos dinámicos
// ===================================================================

// ===============================
// Seleccionar/deseleccionar todos
// ===============================
document.getElementById('selectAll')?.addEventListener('change', e => {
  document.querySelectorAll('input[name="applications[]"]').forEach(cb => {
    cb.checked = e.target.checked;
  });
});

// ===============================
// Abrir modal principal
// ===============================
document.getElementById('openModal')?.addEventListener('click', () => {
  const seleccionados = document.querySelectorAll('input[name="applications[]"]:checked');
  if (seleccionados.length === 0) {
    alert("Selecciona al menos un candidato.");
    return;
  }
  const modal = document.getElementById('modal');
  modal.classList.remove('hidden');
  modal.classList.add('flex');
});

// ===============================
// Cerrar modal
// ===============================
document.getElementById('closeModal')?.addEventListener('click', () => {
  const modal = document.getElementById('modal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
});

// ===============================
// Mostrar campos según el estado seleccionado
// ===============================
const statusSelect = document.getElementById('status');
if (statusSelect) {
  const interview = document.getElementById('interviewFields');
  const psychotest = document.getElementById('psychotestFields');
  const socioeco = document.getElementById('socioeconomicFields');

  const toggleFields = () => {
    const val = statusSelect.value;
    interview?.classList.toggle('hidden', !['interview_scheduled', 'interview_deep', 'interview_pre'].includes(val));
    psychotest?.classList.toggle('hidden', val !== 'psychotest');
    socioeco?.classList.toggle('hidden', val !== 'socioeconomic_study');
  };

  statusSelect.addEventListener('change', toggleFields);
  toggleFields(); // inicializa al cargar
}

// ===============================
// Exclusión lógica del psicotest (link externo vs interno)
// ===============================
const link = document.getElementById('psychotest_link');
const check = document.getElementById('use_internal_psychotest');
link?.addEventListener('input', () => { if (link.value) check.checked = false; });
check?.addEventListener('change', () => { if (check.checked) link.value = ""; });

// ===============================
// Sincronizar mensaje adicional
// ===============================
const msgField = document.getElementById('interview_message');
if (msgField) {
  msgField.addEventListener('input', () => {
    msgField.setAttribute('value', msgField.value);
  });
}

// ===============================
// Modal de análisis IA
// ===============================
document.querySelectorAll('.open-analysis').forEach(btn => {
  btn.addEventListener('click', () => {
    const modal = document.getElementById('analysisModal');
    let data = {};
    try { data = JSON.parse(btn.dataset.analysis); } catch {}

    document.getElementById('analysisMotivos').innerHTML =
      (data.motivos || []).map(m => `<li>${m}</li>`).join('') || '<li>No hay motivos</li>';

    document.getElementById('analysisObs').innerText = data.observaciones || '';
    document.getElementById('analysisRaw').innerText = JSON.stringify(data, null, 2);

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  });
});

document.getElementById('closeAnalysis')?.addEventListener('click', () => {
  const modal = document.getElementById('analysisModal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
});

// ===============================
// Modal Psychotest
// ===============================
document.querySelectorAll('.open-psychotest').forEach(btn => {
  btn.addEventListener('click', () => {
    const modal = document.getElementById('psychotestModal');
    let data = {};
    try { data = JSON.parse(btn.dataset.psychotest); } catch {}

    const answersList = document.getElementById('psychotestAnswers');
    const rawBox = document.getElementById('psychotestRaw');
    const scoreEl = document.querySelector('#psychotestScore span');
    const dateEl = document.querySelector('#psychotestDate span');

    const score = btn.dataset.score ?? null;
    const completedAt = btn.dataset.completedAt ?? null;

    if (scoreEl) scoreEl.textContent = score || '—';
    if (dateEl) dateEl.textContent = completedAt || '—';

    const html = Object.keys(data)
      .map((q, i) => `<li><strong>Pregunta ${i + 1}:</strong> ${data[q]}</li>`)
      .join('');

    answersList.innerHTML = html || '<li>No hay respuestas registradas.</li>';
    rawBox.innerText = JSON.stringify(data, null, 2);

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  });
});

document.getElementById('closePsychotest')?.addEventListener('click', () => {
  const modal = document.getElementById('psychotestModal');
  modal.classList.add('hidden');
  modal.classList.remove('flex');
});

// ===============================
// 📅 Calendario visual con Flatpickr
// ===============================
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";
import { Spanish } from "flatpickr/dist/l10n/es.js";

document.addEventListener('DOMContentLoaded', () => {
  const interviewInput = document.getElementById('interview_at');
  if (interviewInput) {
    flatpickr(interviewInput, {
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      time_24hr: true,
      locale: Spanish,
      allowInput: true
    });
  }
});
