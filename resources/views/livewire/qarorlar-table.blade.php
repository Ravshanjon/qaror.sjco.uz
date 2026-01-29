<div class="space-y-4">

    {{-- FILTERLAR --}}
    <div class="flex flex-col md:flex-row items-stretch gap-4">

        {{-- 🔍 SEARCH — CHAP TOMON --}}
        <input
            type="text"
            wire:model.live.debounce.500ms="search"
            placeholder="Izlash..."
            class="border px-3 py-2 rounded w-full md:max-w-md"
        />

        {{-- 🔢 NUMBER + 📅 YEAR — O‘NG TOMON --}}
        <div class="flex gap-2 md:ml-auto">

            <input
                type="text"
                wire:model.live.debounce.500ms="number"
                placeholder="Qaror raqami (2092)"
                class="border px-3 py-2 rounded text-sm w-40"
            />

            <select
                wire:model.live="year"
                class="border px-2 py-2 rounded text-sm w-28"
            >
                <option value="">Yil</option>
                @foreach($this->years as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>

        </div>
    </div>

    {{-- INFO --}}
    <div class="text-sm text-gray-600">
        Jami: <span class="font-semibold">{{ $qarorlar->total().' '. 'ta' }}</span>
    </div>

    {{-- 🔥 SCROLL TARGET --}}
    <div id="qarorlar-table-start" class="scroll-mt-40"></div>

    {{-- TABLE --}}
    <div class="overflow-x-auto border rounded-lg">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 w-16">№</th>
                <th class="px-4 py-3">Mazmuni</th>
                <th class="px-4 py-3 w-32">Qaror raqami</th>
                <th class="px-4 py-3 w-32">Sana</th>
            </tr>
            </thead>

            <tbody>
            @forelse($qarorlar as $i => $q)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 text-sm/6 font-semibold text-black sm:flex-row sm:gap-2 sm:pr-4 dark:text-gray-400 py-3">
                        {{ $qarorlar->firstItem() + $i }}.
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('pdf.show', ['number' => $q->number]) }}"
                           class="text-sm/6 font-semibold text-black sm:flex-row sm:gap-2 sm:pr-4 dark:text-gray-400 hover:underline">
                            {{ $q->title }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm/6 font-medium  text-black sm:flex-row sm:gap-2 sm:pr-4 dark:text-gray-400">№ {{ $q->number }}</td>
                    <td class="px-4 py-3 text-sm/6 font-mediu text-black sm:flex-row sm:gap-2 sm:pr-4 dark:text-gray-400">
                        {{ \Carbon\Carbon::parse($q->created_date)->format('d.m.Y') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                        Natija topilmadi
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    <div class="flex items-center gap-4 justify-between">
       <div class="flex items-center">
           <div class="text-sm text-gray-600 mr-2">
               Jami: <span class="font-semibold">{{ $qarorlar->total().' '. 'ta' }}</span>
           </div>
           <div>
               <select wire:model.live="perPage"
                       class="px-3 py-2 border rounded text-sm">
                   <option value="25">25 ta</option>
                   <option value="50">50 ta</option>
                   <option value="75">75 ta</option>
                   <option value="100">100 ta</option>
               </select>
           </div>
       </div>
        <div>
            {{ $qarorlar->links('pagination::tailwind') }}
        </div>

    </div>

    {{-- 🔥 JS (MINIMAL, LIVEWIRE’GA TEGMAYDI) --}}


</div>
