<x-guest-layout>
    <!-- Judul -->
    <h2 class="text-center text-3xl font-bold text-gray-800">Selamat Datang 👋</h2>
    <p class="text-center text-gray-500 text-sm">Silakan login untuk melanjutkan</p>

    <!-- Status Session -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    

    <!-- Form Login -->
    <form method="POST" action="{{ route('login') }}" novalidate id="loginForm">
        @csrf

        <!-- Email -->
        <div class="mt-6">
            <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-semibold" />
            <x-text-input 
                id="email" 
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autofocus 
                autocomplete="username"
                pattern="[^@\s]+@[^@\s]+\.[^@\s]+" 
                onblur="validateEmail()"
                oninput="scheduleEmailValidation()"
            />
            <div id="emailError" class="hidden mt-1 text-red-600 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span>Format email tidak valid. Pastikan menggunakan @ dan domain yang benar</span>
            </div>
            @error('email')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-semibold" />
            <div class="relative">
                <x-text-input 
                    id="password" 
                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg pr-10"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password"
                    pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_])[A-Za-z\d@$!%*?&_]{8,}$"
                    onblur="validatePassword()"
                    oninput="schedulePasswordValidation()"
                />
                <!-- Tombol Show/Hide Password -->
                <button 
                    type="button" 
                    id="togglePassword" 
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none mt-1"
                    aria-label="Toggle password visibility"
                >
                    <svg id="eyeIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg id="eyeSlashIcon" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            
            <!-- Password Error Message -->
            <div id="passwordError" class="hidden mt-1 text-red-600 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <span>Password harus mengandung huruf besar, angka, dan simbol (@$!%*?&_)</span>
            </div>
            
            @error('password')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <!-- Tombol -->
        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:underline" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif

            <x-primary-button 
                id="submitBtn"
                class="ms-3 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
            >
                {{ __('Masuk') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        let emailTimeout, passwordTimeout;

        function validateEmail() {
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            const submitBtn = document.getElementById('submitBtn');
            
            const email = emailInput.value;
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            
            if (email && !emailRegex.test(email)) {
                emailError.classList.remove('hidden');
                emailInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                submitBtn.disabled = true;
            } else {
                emailError.classList.add('hidden');
                emailInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                emailInput.classList.add('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');
                updateSubmitButton();
            }
        }

        function validatePassword() {
            const passwordInput = document.getElementById('password');
            const passwordError = document.getElementById('passwordError');
            const submitBtn = document.getElementById('submitBtn');
            
            const password = passwordInput.value;
            
            // Password requirements - DENGAN underscore
            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /\d/.test(password);
            const hasSpecial = /[@$!%*?&_]/.test(password); // INCLUDE underscore
            const hasLength = password.length >= 8;
            
            const isValid = hasUppercase && hasLowercase && hasNumber && hasSpecial && hasLength;
            
            if (password && !isValid) {
                passwordError.classList.remove('hidden');
                passwordInput.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                submitBtn.disabled = true;
            } else {
                passwordError.classList.add('hidden');
                passwordInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
                passwordInput.classList.add('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');
                updateSubmitButton();
            }
        }

        function scheduleEmailValidation() {
            // Clear previous timeout
            clearTimeout(emailTimeout);
            
            // Hide error while typing
            const emailError = document.getElementById('emailError');
            const emailInput = document.getElementById('email');
            emailError.classList.add('hidden');
            emailInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            emailInput.classList.add('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');
            
            // Schedule validation after 1 second of inactivity
            emailTimeout = setTimeout(() => {
                const email = document.getElementById('email').value;
                if (email) {
                    validateEmail();
                }
            }, 1000);
            
            updateSubmitButton();
        }

        function schedulePasswordValidation() {
            // Clear previous timeout
            clearTimeout(passwordTimeout);
            
            // Hide error while typing
            const passwordError = document.getElementById('passwordError');
            const passwordInput = document.getElementById('password');
            passwordError.classList.add('hidden');
            passwordInput.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            passwordInput.classList.add('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');
            
            // Schedule validation after 1 second of inactivity
            passwordTimeout = setTimeout(() => {
                const password = document.getElementById('password').value;
                if (password) {
                    validatePassword();
                }
            }, 1000);
            
            updateSubmitButton();
        }

        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            const email = emailInput.value;
            const password = passwordInput.value;
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            
            const passwordRequirements = (
                /[A-Z]/.test(password) &&
                /[a-z]/.test(password) &&
                /\d/.test(password) &&
                /[@$!%*?&_]/.test(password) && // INCLUDE underscore
                password.length >= 8
            );
            
            submitBtn.disabled = !(email && password && emailRegex.test(email) && passwordRequirements);
        }

        // Toggle Password Visibility
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeSlashIcon = document.getElementById('eyeSlashIcon');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    if (type === 'text') {
                        eyeIcon.classList.add('hidden');
                        eyeSlashIcon.classList.remove('hidden');
                    } else {
                        eyeIcon.classList.remove('hidden');
                        eyeSlashIcon.classList.add('hidden');
                    }
                });
            }
        });
    </script>
</x-guest-layout>