<section class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-all duration-200">
    <header class="mb-8">
        <div class="flex items-center gap-3 mb-3">
            <div class="p-2 bg-blue-50 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ __('Perbarui Password') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ __('Pastikan akun Anda menggunakan password yang panjang dan acak untuk tetap aman.') }}
                </p>
            </div>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <!-- Password Saat Ini -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <x-input-label for="update_password_current_password" :value="__('Password Saat Ini')" class="text-sm font-medium text-gray-700" />
                <span class="text-xs text-gray-500">Wajib diisi</span>
            </div>
            <div class="relative">
                <x-text-input 
                    id="update_password_current_password" 
                    name="current_password" 
                    type="password" 
                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
                    placeholder="Masukkan password Anda saat ini"
                    autocomplete="current-password" 
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none" onclick="togglePassword('update_password_current_password')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-sm" />
        </div>

        <!-- Password Baru -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <x-input-label for="update_password_password" :value="__('Password Baru')" class="text-sm font-medium text-gray-700" />
                <span class="text-xs text-gray-500">Minimal 8 karakter</span>
            </div>
            <div class="relative">
                <x-text-input 
                    id="update_password_password" 
                    name="password" 
                    type="password" 
                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
                    placeholder="Masukkan password baru Anda"
                    autocomplete="new-password" 
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none" onclick="togglePassword('update_password_password')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-sm" />
        </div>

        <!-- Konfirmasi Password Baru -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <x-input-label for="update_password_password_confirmation" :value="__('Konfirmasi Password Baru')" class="text-sm font-medium text-gray-700" />
                <span class="text-xs text-gray-500">Harus sama dengan password baru</span>
            </div>
            <div class="relative">
                <x-text-input 
                    id="update_password_password_confirmation" 
                    name="password_confirmation" 
                    type="password" 
                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" 
                    placeholder="Konfirmasi password baru Anda"
                    autocomplete="new-password" 
                />
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <button type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none" onclick="togglePassword('update_password_password_confirmation')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-sm" />
        </div>

        <!-- Tombol Aksi -->
        <div class="flex items-center justify-between pt-4">
            <div class="flex items-center gap-4">
                <x-primary-button class="px-6 py-3 bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 rounded-lg font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('Simpan Perubahan') }}
                </x-primary-button>

                @if (session('status') === 'password-updated')
                    <div
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95"
                        x-init="setTimeout(() => show = false, 3000)"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-green-700 bg-green-50 rounded-lg border border-green-200"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('Password berhasil diperbarui!') }}
                    </div>
                @endif
            </div>
            
            <button type="button" class="text-sm text-gray-500 hover:text-gray-700 underline transition-colors duration-200">
                {{ __('Lupa password Anda?') }}
            </button>
        </div>
    </form>
</section>

<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
    
    // Ganti icon mata
    const button = input.parentElement.querySelector('button');
    const svg = button.querySelector('svg');
    if (type === 'text') {
        svg.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
        `;
    } else {
        svg.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        `;
    }
}
</script>