// resources/js/psychotest.js
document.addEventListener('DOMContentLoaded', () => {
  const likertItems = [
    // key, texto, factor, reverse (true/false)
    ["C1", "Cumplo mis tareas incluso sin supervisión.", "C", false],
    ["C2", "Dejo cosas para último momento.", "C", true],
    ["C3", "Mantengo mi trabajo ordenado y documentado.", "C", false],
    ["C4", "Me cuesta seguir procesos establecidos.", "C", true],

    ["T1", "Disfruto colaborar y compartir información con el equipo.", "T", false],
    ["T2", "Prefiero trabajar aislado aunque afecte al grupo.", "T", true],
    ["T3", "Escucho y valoro las ideas de otros.", "T", false],
    ["T4", "Me irrita tener que coordinar con otros.", "T", true],

    ["A1", "Me adapto rápidamente a cambios de prioridades.", "A", false],
    ["A2", "Los cambios en el trabajo me desorientan por mucho tiempo.", "A", true],
    ["A3", "Aprendo herramientas nuevas con facilidad.", "A", false],
    ["A4", "Me cuesta ajustarme cuando cambian los planes.", "A", true],

    ["S1", "Tomo decisiones serenas bajo presión.", "S", false],
    ["S2", "El estrés me paraliza y no puedo rendir.", "S", true],
    ["S3", "En picos de trabajo mantengo la calidad.", "S", false],
    ["S4", "Ante plazos cortos me bloqueo.", "S", true],

    ["I1", "Haría lo correcto aunque nadie me vea.", "I", false],
    ["I2", "Pequeñas trampas son aceptables si ayudan al equipo.", "I", true],
    ["I3", "Evito manipular información para parecer mejor.", "I", false],
    ["I4", "Si conviene al resultado, ocultaría errores.", "I", true],

    ["N1", "Busco mejoras sin que me lo pidan.", "N", false],
    ["N2", "Solo hago lo que está en mi descripción de puesto.", "N", true],
    ["N3", "Propongo ideas y me pongo en marcha.", "N", false],
    // Atención (AA): debe marcarse “4 = De acuerdo” para pasar el check
    ["AA", "Para esta pregunta de control, selecciona 'De acuerdo' (4).", "ATTN", false],
  ];

  const sjt = [
    {
      key: "S1",
      stem: "Tienes dos tareas urgentes y un compañero te pide ayuda inmediata.",
      options: [
        ["A", "Ayudas de inmediato y pospones tus tareas."],
        ["B", "Evalúas prioridades y acuerdas un momento breve para asistir."], // correcta
        ["C", "Dices que no puedes ayudar en ningún caso."],
        ["D", "Pides a tu jefe que reasigne todas tus tareas."]
      ],
      correct: "B"
    },
    {
      key: "S2",
      stem: "Notas un error pequeño en un reporte ya enviado al cliente.",
      options: [
        ["A", "Esperas a ver si el cliente lo detecta."],
        ["B", "Informas de inmediato el error y envías corrección."], // correcta
        ["C", "Editas el archivo en silencio, sin avisar."],
        ["D", "Culpas a otro para evitar fricciones."]
      ],
      correct: "B"
    },
    {
      key: "S3",
      stem: "El plan del proyecto cambia dos días antes de la entrega.",
      options: [
        ["A", "Te niegas a cambiar porque está 'fuera de alcance'."],
        ["B", "Actualizas plan y comunicas impacto y riesgos."], // correcta
        ["C", "Sigues como si nada y entregas lo pactado."],
        ["D", "Trabajas extra sin avisar a nadie de cambios."]
      ],
      correct: "B"
    },
    {
      key: "S4",
      stem: "Un compañero nuevo comete errores frecuentes.",
      options: [
        ["A", "Lo criticas en público para que mejore."],
        ["B", "Le ofreces apoyo y compartes buenas prácticas."], // correcta
        ["C", "Ignoras el tema; no es tu responsabilidad."],
        ["D", "Informas directamente a dirección sin hablar con él."]
      ],
      correct: "B"
    },
    {
      key: "S5",
      stem: "Recibes una solicitud ambigua del cliente.",
      options: [
        ["A", "Asumes lo más probable y avanzas."],
        ["B", "Pides aclaraciones y confirmas por escrito."], // correcta
        ["C", "Respondes con un presupuesto alto para cubrirte."],
        ["D", "Rechazas la solicitud por falta de detalles."]
      ],
      correct: "B"
    },
    {
      key: "S6",
      stem: "La herramienta clave cae 2 horas. ¿Qué haces?",
      options: [
        ["A", "Esperas a que vuelva; no hay nada que hacer."],
        ["B", "Buscas alternativas y reordenas tareas críticas."], // correcta
        ["C", "Aprovechas para ponerte al día con el chat."],
        ["D", "Anuncias que no se podrá cumplir nada hoy."]
      ],
      correct: "B"
    }
  ];

  // Utilidades
  const shuffle = (arr) => arr
    .map(v => [Math.random(), v])
    .sort((a,b)=>a[0]-b[0])
    .map(v => v[1]);

  // Randomizar ítems y escenarios
  const shuffledLikert = shuffle(likertItems);
  const shuffledSJT = shuffle(sjt);

  // Render Likert
  const likertContainer = document.getElementById('likertContainer');
  shuffledLikert.forEach(([key, text]) => {
    const row = document.createElement('div');
    row.className = "p-3 border rounded";
    row.innerHTML = `
      <label class="block font-medium text-gray-700 mb-2">${text}</label>
      <div class="flex gap-3 text-sm">
        ${[1,2,3,4,5].map(v => `
          <label class="inline-flex items-center gap-1">
            <input type="radio" name="answers[likert][${key}]" value="${v}" required>
            <span>${v}</span>
          </label>
        `).join('')}
      </div>
    `;
    likertContainer.appendChild(row);
  });

  // Render SJT
  const sjtContainer = document.getElementById('sjtContainer');
  shuffledSJT.forEach((q, idx) => {
    const box = document.createElement('div');
    box.className = "p-3 border rounded";
    const options = shuffle(q.options);
    box.innerHTML = `
      <p class="font-medium text-gray-800 mb-2">${idx+1}. ${q.stem}</p>
      <div class="space-y-2">
        ${options.map(([code, label]) => `
          <label class="block">
            <input type="radio" name="answers[sjt][${q.key}]" value="${code}" class="mr-2" required>
            ${label}
          </label>
        `).join('')}
      </div>
    `;
    sjtContainer.appendChild(box);
  });

  // Metadatos
  const start = Date.now();
  document.getElementById('start_ts').value = start.toString();

  document.getElementById('psychotestForm').addEventListener('submit', (e) => {
    const durationSec = Math.round((Date.now() - start) / 1000);
    document.getElementById('duration_sec').value = durationSec.toString();

    // Straightlining (todas las respuestas iguales en Likert, ignorando la de atención)
    const values = Array.from(document.querySelectorAll('input[name^="answers[likert]"]:checked'))
      .filter(i => !i.name.includes("[AA]"))
      .map(i => Number(i.value));
    const allSame = values.length > 1 && values.every(v => v === values[0]);
    document.getElementById('straightline').value = allSame ? '1' : '0';

    // Atención: AA debe ser 4
    const att = document.querySelector('input[name="answers[likert][AA]"]:checked');
    const attentionOk = att && att.value === "4";
    document.getElementById('attention_ok').value = attentionOk ? '1' : '0';
  });
});
