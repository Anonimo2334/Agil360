<div>
    <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $group['title'] }}</h3>
    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
        @foreach ($group['options'] as $optionKey => $label)
            <div x-data="{ checked: false }" data-checkbox-option class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                <label class="flex items-start gap-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                    <input x-model="checked" type="checkbox" name="{{ $groupKey }}[{{ $optionKey }}][activo]" value="{{ $label }}" data-summary-label="{{ $group['title'] }}"
                        class="mt-0.5 size-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                    <span>{{ $label }}</span>
                </label>
                <div x-show="checked" x-transition class="mt-3">
                    <textarea :disabled="!checked" name="{{ $groupKey }}[{{ $optionKey }}][comentario]" data-comment-field rows="2"
                        placeholder="Comentario adicional para {{ strtolower($label) }}"
                        class="w-full resize-none rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 placeholder:text-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300"></textarea>
                </div>
            </div>
        @endforeach
    </div>
</div>
