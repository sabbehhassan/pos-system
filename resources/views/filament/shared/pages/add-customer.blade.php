<x-filament::page>
    <x-filament::card>
        <form wire:submit.prevent="save" class="space-y-4">

            {{ $this->form }}

            <x-filament::button type="submit" color="primary">
                Save Customer
            </x-filament::button>

        </form>
    </x-filament::card>
</x-filament::page>