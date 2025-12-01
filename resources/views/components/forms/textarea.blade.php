@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => null,
    'value' => '',
    'rows' => 6,
    'disabled' => false,
    'error' => null,
])

<div {{ $attributes->except('class') }}>
    @if ($label)
        <label for="{{ $id ?? $name }}" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ $label }}
        </label>
    @endif
    <textarea
        name="{{ $name }}"
        id="{{ $id ?? $name }}"
        placeholder="{{ $placeholder }}"
        rows="{{ $rows }}"
        @disabled($disabled)
        {{ $attributes->class([
            'w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30',
            'shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800' => !$error,
            'border-gray-300 dark:border-gray-700' => !$error && !$disabled,
            'border-error-300 dark:border-error-700 focus:border-error-300 focus:ring-error-500/10 dark:focus:border-error-800' => $error,
            'disabled:border-gray-100 disabled:bg-gray-50 disabled:placeholder:text-gray-300 dark:disabled:border-gray-800 dark:disabled:bg-white/[0.03] dark:disabled:placeholder:text-white/15' => $disabled,
        ])->merge(['class' => '']) }}
    >{{ old($name, $value) }}</textarea>

    @if ($error)
        <p class="text-theme-xs text-error-500 mt-1">
            {{ $error }}
        </p>
    @endif
</div>
