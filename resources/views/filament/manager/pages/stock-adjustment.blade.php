<x-filament-panels::page>

    <div class="max-w-xl bg-white p-6 rounded shadow">

        <form wire:submit.prevent="submit" class="space-y-4">

            {{ $this->form }}

            <button type="submit"
                    class="px-4 py-2 bg-primary-600 text-white rounded">
                Save Stock Adjustment
            </button>

        </form>

    </div>

</x-filament-panels::page>