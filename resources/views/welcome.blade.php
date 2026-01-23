<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <title>Qarorlar</title>
        <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>

<body class="bg-gray-100 p-10">

<div class="bg-white p-6 rounded-lg max-w-7xl mx-auto">
    <div class="flex items-center gap-5 border-b pb-5 mb-5">
        {{--        <img src="./img/logo.svg" class="w-20 h-20" alt="">--}}
        <div class="flex  items-center">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="gray" class="size-16">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
            </div>
            <a href="/" class="text-xl font-bold uppercase leading-tight">
                SUDYALAR OLIY KENGASHINING <br> QARORLARI
            </a>

        </div>
    </div>

    <livewire:qarorlar-table />

</div>
@livewireScripts
<script>
    const btn = document.getElementById('scrollBtn');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 120) {
            btn.classList.remove('opacity-0', 'scale-75', 'pointer-events-none');
            btn.classList.add('opacity-100', 'scale-100');
        } else {
            btn.classList.add('opacity-0', 'scale-75', 'pointer-events-none');
            btn.classList.remove('opacity-100', 'scale-100');
        }
    });
</script>
</body>
</html>
