<x-layouts.app.sidebar>
    <flux:main>
        {{ $slot }}

         @persist('toast')
            <flux:toast />
        @endpersist
    </flux:main>
</x-layouts.app.sidebar>
