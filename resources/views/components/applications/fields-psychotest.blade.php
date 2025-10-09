<div id="psychotestFields" class="hidden">
    <div class="mb-3">
        <label for="psychotest_link" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
            Enlace externo (opcional)
        </label>

        <input type="text" id="psychotest_link" name="psychotest_link" form="bulkForm"
               class="border border-gray-300 dark:border-gray-600 rounded-lg w-full px-3 py-1.5 text-sm
                      focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-800 dark:text-gray-100"
               placeholder="Ej. https://" value="">

        <small id="psychotest_hint" class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">
            Puedes dejarlo vacío si usarás la prueba interna del sistema.
        </small>
    </div>

    <div class="mb-3 flex items-center gap-2">
        <input type="checkbox" id="use_internal_psychotest" name="use_internal_psychotest" form="bulkForm"
               class="h-4 w-4 text-indigo-600 border-gray-300 rounded cursor-pointer focus:ring-indigo-500">
        <label for="use_internal_psychotest" class="text-sm text-gray-700 dark:text-gray-200">
            Usar prueba básica interna del sistema
        </label>
    </div>

    <p class="text-xs text-gray-500 dark:text-gray-400 leading-snug">
        ⚠️ Si colocas un enlace externo, se ignorará la prueba interna.<br>
        Si activas la interna, se eliminará automáticamente el enlace externo.<br>
        <span class="font-semibold text-indigo-600">Recuerda:</span> puedes enviar cualquier tipo de enlace válido o personalizado.
    </p>
</div>
