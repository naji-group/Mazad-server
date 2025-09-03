<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
<div class="space-y-2" wire:key="image-preview-{{ time() }}">
 
    @if($imageUrl = $getImageUrl())

        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
            <img 
                src="{{ $imageUrl }}" 
                alt="{{ $getAltText() }}" 
                class="object-contain mx-auto"
                style="height: {{ $getImageHeight() }}px"
            >
         
        </div>
    @else
        <p class="text-sm text-gray-500 text-center">
         لايوجد
        </p>
    @endif
</div>
</x-dynamic-component>


