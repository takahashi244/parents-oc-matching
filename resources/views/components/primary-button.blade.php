<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-primary border border-transparent rounded-full font-semibold text-sm text-white transition-colors hover:bg-[#1F388F] focus:outline-none focus:ring-2 focus:ring-primary/30 focus:ring-offset-2 active:bg-[#1A2F7A] disabled:opacity-40']) }}>
    {{ $slot }}
</button>
