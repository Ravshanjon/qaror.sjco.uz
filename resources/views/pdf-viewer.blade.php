<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $qaror->title ?? 'Qaror PDF' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        #pdf-container {
            overflow-y: auto;
            background: #525659;
        }
        .pdf-page-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }
        .pdf-page-wrapper canvas {
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            background: white;
        }
        #pdf-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: white;
        }
        .spinner {
            border: 4px solid rgba(255,255,255,0.3);
            border-top: 4px solid white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-right: 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-100 p-2 md:p-10">
<script>
    function shareQaror() {
        const url = window.location.href;
        const title = @json($qaror->title);

        if (navigator.share) {
            navigator.share({
                title: title,
                text: title,
                url: url
            }).catch(() => {});
        } else {
            navigator.clipboard.writeText(url).then(() => {
                alert("Havola nusxalandi!");
            });
        }
    }
</script>
<div class="bg-white p-3 md:p-6 rounded-lg max-w-7xl mx-auto">
    <div class="flex items-center gap-5 border-b pb-5 mb-5">
        <div class="flex items-center">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="gray" class="w-12 h-12 md:size-16">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
            </div>
            <div class="text-sm md:text-xl font-bold uppercase leading-tight">
                SUDYALAR OLIY KENGASHINING <br> QARORI
            </div>
        </div>
    </div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-2">
        <div class="max-w-2xl font-semibold text-zinc-600 text-sm md:text-md border p-2 rounded-lg">
            {{ $qaror->title ?? 'Qaror hujjati' }}
        </div>
        <div class="flex flex-wrap justify-start md:justify-between items-center gap-2">
            <div class="font-semibold uppercase text-xs md:text-sm border p-2 rounded-lg text-zinc-600">
                {{ $qaror->created_date?->format('d.m.Y') ?? '-' }}
            </div>
            <div class="font-semibold uppercase text-xs md:text-sm border p-2 rounded-lg text-zinc-600">
                № {{ $qaror->number }}
            </div>
            <div class="font-semibold uppercase text-xs md:text-sm border p-2 rounded-lg text-zinc-600 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 md:size-5 mr-1 md:mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <div class="font-semibold text-xs md:text-sm">
                    {{ number_format($qaror->views) }}
                </div>
            </div>
            @if($qaror->pdf_path)
            <a href="/storage/{{ $qaror->pdf_path }}" download class="font-semibold uppercase text-xs md:text-sm border p-2 rounded-lg text-zinc-600 flex items-center hover:bg-gray-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 md:size-5 mr-1">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Yuklab olish
            </a>
            @endif
        </div>
    </div>
    <div id="pdf-container" class="bg-gray-700 rounded-lg shadow overflow-hidden h-[75vh] md:h-[85vh]">
        @if($qaror->pdf_path)
            <div id="pdf-loading">
                <div class="spinner"></div>
                <span>PDF yuklanmoqda...</span>
            </div>
            <div id="pdf-viewer"></div>
        @else
            <div class="flex items-center justify-center h-full text-gray-500 bg-white">
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

@if($qaror->pdf_path)
<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    const pdfUrl = '/storage/{{ $qaror->pdf_path }}';
    const container = document.getElementById('pdf-viewer');
    const loadingEl = document.getElementById('pdf-loading');

    async function renderPDF() {
        try {
            const pdf = await pdfjsLib.getDocument(pdfUrl).promise;
            loadingEl.style.display = 'none';

            const containerWidth = document.getElementById('pdf-container').clientWidth;
            // Get device pixel ratio for high-DPI screens (Android phones typically have 2-3x)
            const pixelRatio = window.devicePixelRatio || 1;

            for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                const page = await pdf.getPage(pageNum);

                // Calculate scale to fit container width with some padding
                const desiredWidth = Math.min(containerWidth - 40, 900);
                const viewport = page.getViewport({ scale: 1 });
                const scale = desiredWidth / viewport.width;
                const scaledViewport = page.getViewport({ scale: scale });

                // Create viewport for high-DPI rendering
                const highResViewport = page.getViewport({ scale: scale * pixelRatio });

                const wrapper = document.createElement('div');
                wrapper.className = 'pdf-page-wrapper';

                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');

                // Set actual canvas size (high resolution)
                canvas.height = highResViewport.height;
                canvas.width = highResViewport.width;

                // Set display size via CSS (scaled down to look crisp)
                canvas.style.width = scaledViewport.width + 'px';
                canvas.style.height = scaledViewport.height + 'px';

                wrapper.appendChild(canvas);
                container.appendChild(wrapper);

                await page.render({
                    canvasContext: context,
                    viewport: highResViewport
                }).promise;
            }
        } catch (error) {
            console.error('PDF yuklashda xatolik:', error);
            loadingEl.innerHTML = '<span style="color: #f87171;">PDF yuklashda xatolik yuz berdi. <a href="' + pdfUrl + '" download style="text-decoration: underline;">Yuklab olish</a></span>';
        }
    }

    renderPDF();
</script>
@endif


