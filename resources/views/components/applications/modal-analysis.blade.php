<div id="analysisModal" class="hidden fixed inset-0 bg-black bg-opacity-40 items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-lg shadow-lg">
        <h3 class="text-lg font-bold mb-4 text-gray-800">Detalles del análisis IA</h3>
        <ul id="analysisMotivos" class="list-disc list-inside text-sm text-gray-700"></ul>
        <p id="analysisObs" class="mt-3 text-sm text-gray-600 italic"></p>
        <div class="mt-4">
            <label class="block text-xs font-semibold text-gray-500 mb-1">JSON crudo (debug):</label>
            <pre id="analysisRaw" class="bg-gray-100 text-xs p-2 rounded overflow-x-auto max-h-40"></pre>
        </div>
        <div class="flex justify-end mt-4">
            <button id="closeAnalysis" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700">
                Cerrar
            </button>
        </div>
    </div>
</div>
