<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

    <nav class="p-4 bg-white dark:bg-gray-800 shadow mb-8">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">🚀 Posts Manager</h1>
            <button onclick="
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                }" class="p-2 bg-gray-200 dark:bg-gray-700 rounded-lg">
                🌓 Toggle Mode
            </button>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            @foreach($stats as $stat)
                <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
                    <p class="text-sm opacity-70">{{ ucfirst($stat->status) }}</p>
                    <p class="text-2xl font-bold">{{ $stat->count }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($posts as $post)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow overflow-hidden">
                    @if($post->image_path)
                        <img src="{{ asset('storage/'.$post->image_path) }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-6">
                        <h3 class="text-xl font-bold">{{ $post->title }}</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $post->body }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </main>
</body>
</html>