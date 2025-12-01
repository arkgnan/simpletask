@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [], // Expects an array: ['value' => 'Display Text'] or ['Option 1', 'Option 2']
    'selected' => null,
    'disabled' => false,
    'error' => null,
])

<div {{ $attributes->except('class') }}>
    @if ($label)
        <label for="{{ $id ?? $name }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ $label }}
        </label>
    @endif
    <div class="relative z-20 bg-transparent">
        <select
            name="{{ $name }}"
            id="{{ $id ?? $name }}"
            @disabled($disabled)
            {{ $attributes->class([
                'h-11 w-full appearance-none rounded-lg border bg-transparent bg-none px-4 py-2.5 pr-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30',
                'shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800' => !$error,
                'border-gray-300 dark:border-gray-700' => !$error && !$disabled,
                'border-error-300 dark:border-error-700 focus:border-error-300 focus:ring-error-500/10 dark:focus:border-error-800' => $error,
                'disabled:border-gray-100 disabled:bg-gray-50 disabled:placeholder:text-gray-300 dark:disabled:border-gray-800 dark:disabled:bg-white/[0.03] dark:disabled:placeholder:text-white/15' => $disabled,
            ])->merge(['class' => '']) }}
        >
            @php
                $currentValue = (string) old($name, $selected);
            @endphp
            <option value="" class="text-gray-700 dark:bg-gray-900 dark:text-gray-400" @selected($currentValue === '')>
                Select an option
            </option>
            @foreach ($options as $optionValue => $optionText)
                <option
                    value="{{ $optionValue }}"
                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                    @selected((string) $optionValue === $currentValue)>
                    {{ $optionText }}
                </option>
            @endforeach
        </select>
        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke="" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </span>
    </div>

    @if ($error)
        <p class="mt-1 text-theme-xs text-error-500">
            {{ $error }}
        </p>
    @enderror
</div>
