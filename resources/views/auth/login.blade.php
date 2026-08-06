<x-layouts.auth>
    <div class="w-full max-w-md mx-auto px-4">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary-container rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-primary-container/20">
                <span class="material-symbols-outlined text-white text-3xl">coffee</span>
            </div>
            <h1 class="text-display-sm font-bold text-primary-container">SimaluCoffee</h1>
            <p class="text-body-md text-on-surface-variant mt-2">Masuk ke sistem POS</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl shadow-black/5 border border-outline-variant p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-label-md font-semibold text-on-surface mb-2">Email</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">mail</span>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant bg-surface focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 text-body-md transition-all"
                               placeholder="nama@simalucoffee.com">
                    </div>
                    @error('email')
                        <p class="text-danger text-label-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-label-md font-semibold text-on-surface mb-2">Password</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">lock</span>
                        <input type="password" id="password" name="password" required
                               class="w-full pl-10 pr-12 py-3 rounded-xl border border-outline-variant bg-surface focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 text-body-md transition-all"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" tabindex="-1" class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary-container transition-colors focus:outline-none flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]" id="toggleIcon">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-danger text-label-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-outline-variant text-primary-container focus:ring-primary-container/20">
                    <label for="remember" class="text-body-sm text-on-surface-variant">Ingat saya</label>
                </div>

                <button type="submit" class="w-full py-3.5 bg-primary text-on-primary rounded-xl font-semibold text-body-md hover:bg-primary-container transition-colors shadow-lg shadow-primary/20 min-h-[48px]">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-label-sm text-on-surface-variant mt-6">&copy; {{ date('Y') }} SimaluCoffee. All rights reserved.</p>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = 'visibility';
            }
        }
    </script>
</x-layouts.auth>
