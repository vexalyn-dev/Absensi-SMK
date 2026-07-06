<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">Login - {{ config('app.name', 'ICB CT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #FFFFFF;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            position: relative;
            width: 900px;
            max-width: 95%;
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15);
            overflow: hidden;
            display: flex;
            min-height: 600px;
        }

        /* Panel Kiri (Biru/Kuning) */
        .auth-panel {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            transition: transform 0.8s cubic-bezier(0.77, 0, 0.175, 1);
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 35px;
            text-align: center;
        }

        .auth-panel.login {
            background: #0F172A;
            transform: translateX(0);
        }

        .auth-panel.register {
            background: #FACC15;
            transform: translateX(100%);
        }

        .logo-container {
            width: 90px;
            height: 90px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .panel-content {
            color: white;
            z-index: 1;
        }

        .panel-content h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
        }

        .panel-content p {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .btn-toggle {
            padding: 14px 45px;
            border: 2px solid white;
            background: transparent;
            color: white;
            border-radius: 14px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.4s ease;
        }

        .btn-toggle:hover {
            background: white;
            color: #0F172A;
            transform: translateY(-3px);
        }

        /* Container Form di Kanan */
        .forms-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        /* Individual Forms */
        .form-container {
            position: absolute;
            top: 0;
            width: 50%;
            height: 100%;
            padding: 35px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
            transition: all 0.6s ease-in-out;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .form-container::-webkit-scrollbar {
            display: none;
        }

        /* Login Form - Default Visible */
        #loginForm {
            right: 0;
            opacity: 1;
            z-index: 2;
        }

        /* Register Form - Default Hidden */
        #registerForm {
            left: 0;
            opacity: 0;
            z-index: 1;
        }

        /* Saat Toggle ke Register */
        #loginForm.hidden {
            opacity: 0;
            z-index: 1;
        }

        #registerForm.visible {
            opacity: 1;
            z-index: 2;
            justify-content: flex-start;
        }

        #registerForm.visible .form-header {
            margin-bottom: 1rem;
        }

        #registerForm.visible .input-group {
            margin-bottom: 0.75rem;
        }

        #registerForm.visible .divider {
            margin: 1rem 0;
        }

        #registerForm.visible .terms-text {
            margin-top: 1rem;
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 800;
            color: #0F172A;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #64748B;
            font-size: 0.95rem;
        }

        .input-group {
            width: 100%;
            margin-bottom: 1.25rem;
        }

        .input-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #E2E8F0;
            border-radius: 12px;
            font-size: 0.95rem;
            background: #F8FAFC;
            font-family: 'Inter', sans-serif;
        }

        .input-wrapper input[type="password"],
        .input-wrapper input.password-input {
            padding-right: 48px;
        }

        /* Hide browser native password reveal button */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }
        input[type="password"]::-webkit-credentials-auto-fill-button,
        input[type="password"]::-webkit-textfield-decoration-container {
            display: none !important;
            visibility: hidden;
            pointer-events: none;
        }
        input[type="password"] {
            -webkit-appearance: none;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #0F172A;
            background: white;
            box-shadow: 0 0 0 5px rgba(15, 23, 42, 0.08);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            width: 20px;
            height: 20px;
        }

        /* Custom Eye Icon */
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            width: 20px;
            height: 20px;
            cursor: pointer;
            transition: color 0.3s ease;
            background: none;
            border: none;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle:hover {
            color: #0F172A;
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #0F172A;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.25);
            margin-top: 0.75rem;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.35);
            background: #1E3A8A;
        }

        .error-message {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #DC2626;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }

        .success-message {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #16A34A;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: #94A3B8;
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #E2E8F0, transparent);
        }

        .divider span {
            padding: 0 1.25rem;
        }

        .social-login {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-btn {
            width: 50px;
            height: 50px;
            border: 2px solid #E2E8F0;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.4s ease;
            background: white;
            text-decoration: none;
        }

        .social-btn:hover {
            border-color: #FACC15;
            transform: translateY(-5px) scale(1.1);
        }

        .social-btn img,
        .social-btn svg {
            width: 24px;
            height: 24px;
        }

        .terms-text {
            font-size: 0.75rem;
            color: #94A3B8;
            text-align: center;
            margin-top: 1.5rem;
            line-height: 1.6;
        }

        .terms-text a {
            color: #FACC15;
            font-weight: 600;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
                height: auto;
            }

            .auth-panel {
                display: none;
            }

            .forms-container {
                position: relative;
                width: 100%;
                height: auto;
            }

            .form-container {
                position: relative;
                width: 100%;
                opacity: 1 !important;
                z-index: 2 !important;
            }

            #registerForm {
                display: none;
            }

            #registerForm.visible {
                display: flex;
            }
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <!-- Sliding Panel (KIRI) -->
        <div class="auth-panel login" id="authPanel">
            <div class="logo-container">
                @php $appSettings = \App\Models\AppSetting::getInstance(); @endphp
                @if($appSettings->app_logo)
                    <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo">
                @else
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                @endif
            </div>
            <div class="panel-content">
                <h1 id="panelTitle">Selamat Datang Di Website ICB CINTA TEKNIKA</h1>
                <p id="panelText">Sistem Absensi Guru Termodern & Terpercaya</p>
                <button class="btn-toggle" id="toggleBtn" onclick="toggleAuth()">Buat Akun</button>
            </div>
        </div>

        <!-- Forms Container (KANAN) -->
        <div class="forms-container">
            <!-- Login Form -->
            <div class="form-container" id="loginForm">
                <div class="form-header">
                    <h2>Login</h2>
                    <p>Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                @if ($errors->any())
                    <div class="error-message">{{ $errors->first() }}</div>
                @endif

                @if (session('success'))
                    <div class="success-message">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="input-group">
                        <label for="login-email">Email</label>
                        <div class="input-wrapper">
                            <input type="email" id="login-email" name="email" placeholder="nama@email.com" required
                                value="{{ old('email') }}">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="login-password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="login-password" name="password" placeholder="••••••••" required
                                class="password-input">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <button type="button" class="password-toggle"
                                onclick="togglePassword('login-password', this)">
                                <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Masuk</button>
                </form>

                <div class="divider"><span>atau masuk dengan</span></div>

                <div class="social-login">
                    <a href="{{ url('auth/google') }}"
                        class="w-full flex items-center justify-center gap-3 px-6 py-3.5 bg-white border-2 border-slate-200 hover:border-navy-800 hover:bg-slate-50 rounded-xl font-semibold text-slate-700 transition-all shadow-sm hover:shadow-md">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google"
                            class="w-5 h-5">
                        <span>Masuk dengan Google</span>
                    </a>
                </div>
            </div>
            {{-- END #loginForm --}}

            <!-- Register Form -->
            <div class="form-container" id="registerForm">
                <div class="form-header">
                    <h2>Register</h2>
                    <p>Lengkapi form di bawah untuk membuat akun baru</p>
                </div>

                @if ($errors->any())
                    <div class="error-message">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('register.post') }}">
                    @csrf
                    <div class="input-group">
                        <label for="register-name">Nama Lengkap</label>
                        <div class="input-wrapper">
                            <input type="text" id="register-name" name="name" placeholder="Vexalyn Dev" required
                                value="{{ old('name') }}">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="register-email">Email</label>
                        <div class="input-wrapper">
                            <input type="email" id="register-email" name="email" placeholder="nama@email.com" required
                                value="{{ old('email') }}">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                </path>
                            </svg>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="register-password">Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="register-password" name="password" placeholder="••••••••"
                                required class="password-input">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <button type="button" class="password-toggle"
                                onclick="togglePassword('register-password', this)">
                                <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="register-password-confirm">Konfirmasi Password</label>
                        <div class="input-wrapper">
                            <input type="password" id="register-password-confirm" name="password_confirmation"
                                placeholder="••••••••" required class="password-input">
                            <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <button type="button" class="password-toggle"
                                onclick="togglePassword('register-password-confirm', this)">
                                <svg class="eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">Daftar Sekarang</button>

                    <p class="terms-text">
                        Dengan mendaftar, Anda menyetujui <a href="#">Syarat &amp; Ketentuan</a> serta <a
                            href="#">Kebijakan Privasi</a> kami.
                    </p>
                </form>

                <div class="divider"><span>atau daftar dengan</span></div>

                <div class="social-login">
                    <a href="{{ url('auth/google') }}"
                        class="w-full flex items-center justify-center gap-3 px-6 py-3.5 bg-white border-2 border-slate-200 hover:border-navy-800 hover:bg-slate-50 rounded-xl font-semibold text-slate-700 transition-all shadow-sm hover:shadow-md">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google"
                            class="w-5 h-5">
                        <span>Daftar dengan Google</span>
                    </a>
                </div>
            </div>
            {{-- END #registerForm --}}

        </div>
        {{-- END .forms-container --}}
    </div>
    {{-- END .auth-container --}}

    <script>
        function toggleAuth() {
            const panel = document.getElementById('authPanel');
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const panelTitle = document.getElementById('panelTitle');
            const panelText = document.getElementById('panelText');
            const toggleBtn = document.getElementById('toggleBtn');

            if (loginForm.classList.contains('hidden')) {
                // Show Login
                panel.classList.remove('register');
                panel.classList.add('login');
                loginForm.classList.remove('hidden');
                registerForm.classList.remove('visible');
                panelTitle.textContent = 'Selamat Datang Di Website ICB CINTA TEKNIKA';
                panelText.textContent = 'Aplikasi Presensi Guru ICB Cinta Teknika';
                toggleBtn.textContent = 'Buat Akun';
                document.getElementById('pageTitle').textContent = 'Login - {{ config('app.name', 'ICB CT') }}';
            } else {
                // Show Register
                panel.classList.remove('login');
                panel.classList.add('register');
                loginForm.classList.add('hidden');
                registerForm.classList.add('visible');
                panelTitle.textContent = 'Daftar Akun Baru';
                panelText.textContent = 'Silahkan register untuk mulai menggunakan aplikasi';
                toggleBtn.textContent = 'Sudah Punya Akun?';
                document.getElementById('pageTitle').textContent = 'Register - {{ config('app.name', 'ICB CT') }}';
            }
        }

        // Toggle Password Visibility
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const eyeIcon = button.querySelector('.eye-icon');

            if (input.type === 'password') {
                input.type = 'text';
                // Change to eye-off icon
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21">
                    </path>
                `;
            } else {
                input.type = 'password';
                // Change back to eye icon
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                    </path>
                `;
            }
        }
    </script>
</body>

</html>