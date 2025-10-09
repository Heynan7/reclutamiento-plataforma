<div id="psychotestModal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg shadow-lg">
        <h3 class="text-lg font-bold mb-4 text-gray-800">Respuestas del psicotest</h3>
        <ul id="psychotestAnswers" class="list-disc list-inside text-sm text-gray-700 space-y-2"></ul>
        <div class="mt-4">
            <label class="block text-xs font-semibold text-gray-500 mb-1">JSON crudo (debug):</label>
            <pre id="psychotestRaw" class="bg-gray-100 text-xs p-2 rounded overflow-x-auto max-h-40"></pre>
        </div>
        <div class="flex justify-end mt-4">
            <button id="closePsychotest" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                Cerrar
            </button>
        </div>
    </div>
</div>
