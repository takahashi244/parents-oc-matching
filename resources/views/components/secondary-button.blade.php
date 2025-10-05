<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-surface border border-border rounded-full font-semibold text-sm text-text transition-colors hover:bg-surfaceMuted focus:outline-none focus:ring-2 focus:ring-border/60 focus:ring-offset-2 disabled:opacity-40']) }}>
    {{ $slot }}
</button>
