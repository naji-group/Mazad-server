<x-filament-panels::page>
    @foreach ($settings as $setting)
        <livewire:setting.static-form :setting="$setting" :key="'edit-' . $setting->id" />
    @endforeach
</x-filament-panels::page>
