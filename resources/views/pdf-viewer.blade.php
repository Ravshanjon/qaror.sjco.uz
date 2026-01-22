<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>{{ $qaror->title ?? 'Qaror PDF' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10">
<script>
    function shareQaror() {
        const url = window.location.href;
        const title = @json($qaror->title);

        // Zamonaviy brauzerlar (mobile, chrome, edge)
        if (navigator.share) {
            navigator.share({
                title: title,
                text: title,
                url: url
            }).catch(() => {});
        } else {
            // Fallback: linkni copy qilish
            navigator.clipboard.writeText(url).then(() => {
                alert("Havola nusxalandi!");
            });
        }
    }
</script>
<div class="bg-white p-6 rounded-lg max-w-7xl mx-auto">
    <div class="flex items-center gap-5 border-b pb-5 mb-5">
        {{--        <img src="./img/logo.svg" class="w-20 h-20" alt="">--}}
        <div class="flex items-center">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="gray" class="size-16">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
            </div>
            <div class="text-xl font-bold uppercase leading-tight">
                SUDYALAR OLIY KENGASHINING <br> QARORI
            </div>

        </div>

    </div>
    <div class="flex justify-between items-center mb-4">
        <div class="max-w-2xl font-semibold text-zinc-600 text-md border p-2  rounded-lg">
            {{ $qaror->title ?? 'Qaror hujjati' }}
        </div>
        <div class="flex justify-between items-center">
            <div class="font-semibold uppercase text-sm border p-2 rounded-lg text-zinc-600 mr-2">
                {{ $qaror->created_date?->format('d.m.Y') ?? '-' }}
            </div>
            <div class="font-semibold uppercase text-sm border p-2 rounded-lg text-zinc-600 mr-2">
                № {{ $qaror->number }}
            </div>
            <div class="font-semibold uppercase text-sm border p-2 rounded-lg text-zinc-600 mr-2 flex items-center justify-between">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <div class="font-semibold text-sm">
                    {{ number_format($qaror->views) }}
                </div>

            </div>

        </div>
    </div>
    <div class="bg-white rounded-lg shadow overflow-hidden h-[85vh]">
        @if($qaror->pdf_path)
            <iframe
                src="/storage/{{ $qaror->pdf_path }}"
                class="w-full h-screen"
                frameborder="0"
            ></iframe>
        @else
            <div class="flex items-center justify-center h-full text-gray-500">
                <div class="text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-16 mx-auto mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    <p class="text-lg font-medium">PDF fayl mavjud emas</p>
                </div>
            </div>
        @endif
    </div>
</div>


