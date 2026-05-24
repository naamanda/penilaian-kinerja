<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LifeSync</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#B8CBEF] min-h-screen flex items-center justify-center p-4">

    <!-- Card Utama (Dikecilkan ukurannya agar lebih pas) -->
    <div class="bg-[#234C92] w-full max-w-[500px] rounded-[40px] shadow-2xl py-12 px-8">

        <!-- Logo & Brand -->
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('assets/logo.png') }}" alt="logo" class="w-20 mb-4">
            <h1 class="text-white text-5xl font-bold tracking-tight">LifeSync</h1>
            <p class="text-white text-sm mt-2 opacity-90 text-center font-medium">
                Terapkan Lingkungan Kerja Yang Lebih Disiplin !
            </p>
            <!-- Line Tipis -->
            <div class="w-full max-w-[320px] border-b border-white/30 mt-4"></div>
        </div>

        <!-- Alert Error -->
        @if(session('error'))
        <div class="bg-red-500/20 border border-red-500 text-white text-xs p-3 rounded-xl mb-6 text-center">
            {{ session('error') }}
        </div>
        @endif

        <!-- Form Login -->
        <form action="/login-proses" method="POST" class="flex flex-col items-center">
            @csrf

            <div class="w-full max-w-[320px] space-y-4">
                
                <!-- Input Username -->
                <input
                    type="text"
                    name="username"
                    placeholder="username"
                    required
                    class="w-full h-[48px] rounded-full bg-[#D9EAF3] px-6 outline-none text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-cyan-400 transition-all">

                <!-- Input Password dengan Toggle Mata -->
                <div class="relative group">
                    <input
                        type="password"
                        id="passwordInput"
                        name="password"
                        placeholder="password"
                        required
                        class="w-full h-[48px] rounded-full bg-[#D9EAF3] px-6 outline-none text-gray-700 placeholder-gray-500 focus:ring-2 focus:ring-cyan-400 transition-all">
                    
                    <button
                        type="button"
                        onclick="togglePassword()"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 transition-colors">
                        
                        <!-- Ikon Mata Terbuka (Muncul saat password tersembunyi) -->
                        <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>

                        <!-- Ikon Mata Tertutup (Hidden secara default) -->
                        <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>

                <!-- Tombol Login -->
                <div class="flex justify-center pt-4">
                    <button
                        type="submit"
                        class="bg-[#89D7EE] hover:bg-cyan-300 transition duration-300 text-[#1C3F7B] font-extrabold px-12 py-2.5 rounded-full shadow-lg">
                        LOGIN
                    </button>
                </div>

            </div>
        </form>

    </div>

    <!-- Script Toggle Mata -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            }
        }
    </script>

</body>
</html>