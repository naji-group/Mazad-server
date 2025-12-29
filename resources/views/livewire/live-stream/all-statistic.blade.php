<div>
    {{ $this->liveInfolist }}
    <x-filament::fieldset style="margin-top: 20px;font-size:large">
    <x-slot name="label" style="font-size: large !important;">
        <span style="font-size: 20px">
       إحصائيات المنصات
   </span>
    </x-slot>
    <livewire:live-stream.social-statistic :livestream="$livestream" />
    
</x-filament::fieldset>
</div>
