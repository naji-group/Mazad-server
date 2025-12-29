 <div >
      {{-- 🔹 Tabs فوق الجدول --}}
      <div class="sticky top-0 z-10 bg-white dark:bg-gray-900 py-2   hover:bg-blue-500 transition "> 
        <div class="flex gap-2 flex-wrap mb-4">

            {{-- All --}}
            <button
                wire:click="setActiveSocial('all')"
                class="group flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold transition-all duration-200
                    {{ $activeSocial === 'all'
                        ? 'bg-pink-600 text-white shadow'
                        : 'bg-pink-50 text-pink-700 hover:bg-pink-100'
                    }}"
            >
                الكل
            </button>
        
            {{-- Platforms --}}
            @foreach ($socialTabs as $social)
                <button wire:key="social-tab-{{ $social['id'] }}"
                    wire:click="setActiveSocial({{ $social['id'] }})"
                    class="group flex items-center gap-2 px-4 py-2   text-pink-700 rounded-full text-sm font-semibold transition-all duration-200
               
                     {{ (string)$activeSocial === (string)$social['id']
                            ? 'bg-pink-600 text-white shadow'
                            : 'bg-pink-50 text-pink-700 hover:bg-pink-100'
                        }}"
                >
                    {{ $social['name'] }}
        
                    {{-- Count Badge --}}
                    <span
                        class="flex items-center justify-center min-w-[22px] h-[22px] text-xs font-bold rounded-full
                            {{ (string)$activeSocial === (string)$social['id']
                                ? 'bg-white text-pink-600'
                                : 'bg-pink-200 text-pink-800'
                            }}"
                    >
                        {{ $social['auctions_count'] }}
                    </span>
                </button>
            @endforeach
        
        </div>
   
</div>
<div class="relative">

    <div
        wire:loading
        wire:target="setActiveSocial"
        class="absolute inset-0 bg-white/60 dark:bg-black/40 flex items-center justify-center z-10"
    >
        <x-filament::loading-indicator />
    </div>

    <div
        wire:loading.class="opacity-50 pointer-events-none"
        wire:target="setActiveSocial"
    >
     
    </div>

</div>

{{ $this->table }}
</div>
