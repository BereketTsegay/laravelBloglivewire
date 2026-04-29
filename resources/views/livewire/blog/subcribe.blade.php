<div class="bg-indigo-600 rounded-lg p-8 text-white">
    <div class="max-w-xl mx-auto text-center">
        <div class="rounded-lg p-4 mb-4" wire:transition.duration.300ms>
            <h2 class="text-xl font-bold text-gray-800">Subscribe to Our Blog</h2>
            <p class="text-white">Get the latest posts delivered straight to your inbox.</p>
            <form class="mt-4 flex flex-col sm:flex-row gap-2 text-white" wire:submit.prevent="subscribe">
                <flux:input
                    type="email"
                    wire:model="email"
                    required
                    placeholder="Enter your email"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <flux:error name="email"></flux:error>
                <flux:button
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-200"
                >
                    Subscribe
                </flux:button>
        </div>
    </div>
</div>
