<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title id="pageTitle">Login - {{ config('app.name', 'ICB CT') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            padding: 16px;
            background: linear-gradient(135deg, #F8FAFC 0%, #E2E8F0 100%);
            margin: 0;
        }

        .auth-container {
            position: relative;
            width: 900px;
            max-width: calc(100% - 32px);
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
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            transform: translateX(0);
        }

        .auth-panel.register {
            background: linear-gradient(135deg, #FACC15 0%, #F59E0B 100%);
            transform: translateX(100%);
        }

        .logo-container {
            width: 96px;
            height: 96px;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            background: transparent;
            backdrop-filter: none;
            border: 0;
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: none;
        }

        .logo-container svg {
            width: 72px;
            height: 72px;
        }

        .panel-content {
            color: white;
            z-index: 1;
        }

        .panel-content h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .panel-content p {
            font-size: 0.85rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 300px;
            line-height: 1.6;
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
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            letter-spacing: 0.02em;
        }

        .btn-toggle:hover {
            background: white;
            color: #0F172A;
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(255, 255, 255, 0.2);
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
            justify-content: flex-start;
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
            transition: all 0.3s ease;
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
            padding: 16px;
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
            letter-spacing: 0.02em;
            min-height: 48px;
            touch-action: manipulation;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.35);
            background: #1E3A8A;
        }

        .error-message {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-left: 4px solid #DC2626;
            color: #DC2626;
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 500;
            animation: slideDown 0.3s ease;
        }

        .error-message div {
            margin: 5px 0;
        }

        .error-message div:first-child {
            margin-top: 0;
        }

        .error-message div:last-child {
            margin-bottom: 0;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .social-btn:hover {
            border-color: #FACC15;
            transform: translateY(-5px) scale(1.1);
            box-shadow: 0 6px 15px rgba(250, 204, 21, 0.3);
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
            transition: all 0.2s ease;
        }

        .terms-text a:hover {
            text-decoration: underline;
        }

        /* MOBILE RESPONSIVE */
        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
                height: auto;
                min-height: auto;
                padding: 0;
                margin: 0 auto;
                box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.15);
                border-radius: 20px;
                overflow: hidden;
                background: white;
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
                padding: 32px 26px 40px;
                border-radius: 0 0 20px 20px;
                overflow-y: auto;
                max-height: calc(100vh - 300px);
            }

            #loginForm {
                display: flex;
                opacity: 1 !important;
                z-index: 2 !important;
            }

            #loginForm.hidden {
                display: none;
                opacity: 0 !important;
                z-index: 1 !important;
            }

            #registerForm {
                display: none;
                opacity: 0 !important;
                z-index: 1 !important;
            }

            #registerForm.visible {
                display: flex;
                opacity: 1 !important;
                z-index: 2 !important;
            }

            .form-header h2 {
                font-size: 1.75rem;
                margin-bottom: 0.25rem;
            }

            .form-header p {
                font-size: 0.9rem;
                line-height: 1.5;
            }

            .input-wrapper input {
                padding: 12px 14px 12px 40px;
                font-size: 0.9rem;
            }

            .input-group {
                margin-bottom: 1rem;
            }

            .btn-submit {
                padding: 14px;
                font-size: 1.05rem;
            }

            .divider {
                margin: 1.25rem 0;
            }

            .panel-content h1 {
                font-size: 1.3rem;
            }

            .panel-content p {
                font-size: 0.8rem;
                max-width: 100%;
            }

            .btn-toggle {
                padding: 12px 35px;
                font-size: 0.9rem;
            }

            .social-btn {
                width: 45px;
                height: 45px;
            }
        }

        /* SMALL MOBILE */
        @media (max-width: 480px) {
            .mobile-header {
                padding: 32px 20px 28px;
                border-radius: 16px 16px 0 0;
            }

            .mobile-logo-container {
                width: 100px;
                height: 100px;
                margin: 0 auto 16px;
                border-radius: 18px;
            }

            .mobile-header h1 {
                font-size: 1.35rem;
                margin-bottom: 8px;
            }

            .mobile-header p {
                font-size: 0.85rem;
                line-height: 1.6;
                max-width: 290px;
            }

            .form-header h2 {
                font-size: 1.5rem;
            }

            .form-header p {
                font-size: 0.85rem;
            }

            .input-wrapper input {
                padding: 11px 12px 11px 36px;
                font-size: 0.85rem;
            }

            .btn-submit {
                padding: 13px;
                font-size: 1rem;
            }

            .divider {
                margin: 1rem 0;
            }

            .terms-text {
                font-size: 0.7rem;
            }
        }

        /* Mobile Header Panel - NAVY/GOLD THEME */
        .mobile-header {
            display: none;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            padding: 40px 24px 36px;
            border-radius: 20px 20px 0 0;
            text-align: center;
            color: white;
            margin-bottom: 0;
            box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.25);
            position: relative;
            overflow: hidden;
        }

        .mobile-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: -50px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .mobile-header::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: -50px;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(250, 204, 21, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .mobile-logo-container {
            width: 110px;
            height: 110px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, rgba(250, 204, 21, 0.15) 0%, rgba(250, 204, 21, 0.08) 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2.5px solid rgba(250, 204, 21, 0.4);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }

        .mobile-logo-container img {
            width: 85%;
            height: 85%;
            object-fit: contain;
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.3));
        }

        .mobile-header h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 10px;
            color: #FACC15;
            text-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
            letter-spacing: -0.3px;
            position: relative;
            z-index: 1;
        }

        .mobile-header p {
            font-size: 0.9rem;
            opacity: 0.95;
            line-height: 1.7;
            max-width: 300px;
            margin: 0 auto;
            color: #E2E8F0;
            font-weight: 400;
            position: relative;
            z-index: 1;
        }

        /* Register Link Styling */
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #E2E8F0;
        }

        .register-link p {
            color: #64748B;
            font-size: 0.9rem;
            margin: 0;
        }

        .register-link a {
            color: #FACC15;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .register-link a:hover,
        .register-link a:active {
            color: #F59E0B;
            text-decoration: underline;
        }

        /* Update Mobile Responsive */
        @media (max-width: 768px) {
            .mobile-header {
                display: block;
                padding: 35px 22px 32px;
                margin-bottom: 0;
                border-radius: 20px 20px 0 0;
                background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
            }

            .mobile-logo-container {
                width: 105px;
                height: 105px;
                margin: 0 auto 18px;
            }

            .mobile-header h1 {
                font-size: 1.45rem;
                margin-bottom: 10px;
            }

            .mobile-header p {
                font-size: 0.88rem;
                line-height: 1.65;
            }

            .auth-container {
                flex-direction: column;
                padding: 0;
                min-height: auto;
                box-shadow: none;
                border-radius: 20px 20px 0 0;
                max-width: 100%;
            }

            .auth-panel {
                display: none !important;
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
                padding: 28px 24px 32px;
                overflow-y: auto;
                max-height: calc(100vh - 280px);
            }

            #loginForm {
                display: flex;
                opacity: 1 !important;
                z-index: 2 !important;
            }

            #loginForm.hidden {
                display: none;
                opacity: 0 !important;
                z-index: 1 !important;
            }

            #registerForm {
                display: none;
                opacity: 0 !important;
                z-index: 1 !important;
            }

            #registerForm.visible {
                display: flex;
                opacity: 1 !important;
                z-index: 2 !important;
            }

            .form-header h2 {
                font-size: 1.65rem;
                margin-bottom: 0.3rem;
            }

            .form-header p {
                font-size: 0.9rem;
                line-height: 1.5;
            }

            .input-wrapper input {
                padding: 13px 14px 13px 42px;
                font-size: 16px;
                min-height: 44px;
            }

            .input-icon {
                width: 18px;
                height: 18px;
            }

            .password-toggle {
                width: 18px;
                height: 18px;
            }

            .input-group {
                margin-bottom: 1.1rem;
            }

            .btn-submit {
                padding: 15px;
                font-size: 1.05rem;
                min-height: 50px;
            }

            .divider {
                margin: 1.25rem 0;
                font-size: 0.85rem;
            }

            .social-btn {
                width: 48px;
                height: 48px;
            }

            .terms-text {
                font-size: 0.75rem;
                line-height: 1.5;
            }

            .register-link {
                margin-top: 1.25rem;
                padding-top: 1.25rem;
            }

            .register-link p {
                font-size: 0.9rem;
            }

            .register-link a {
                font-weight: 600;
            }
        }

        /* Small Mobile */
        @media (max-width: 480px) {
            .mobile-header {
                padding: 25px 20px;
            }

            .mobile-logo-container {
                width: 90px;
                height: 90px;
            }

            .mobile-header h1 {
                font-size: 1.25rem;
            }

            .mobile-header p {
                font-size: 0.8rem;
            }

            .form-container {
                padding: 20px 20px 25px;
            }

            .form-header h2 {
                font-size: 1.5rem;
            }

            .btn-submit {
                padding: 13px;
                font-size: 1rem;
            }
        }

        /* Extra Small Mobile (360px and below) */
        @media (max-width: 360px) {
            body {
                padding: 12px;
            }

            .mobile-header {
                padding: 28px 18px 24px;
                border-radius: 16px 16px 0 0;
            }

            .mobile-logo-container {
                width: 95px;
                height: 95px;
                margin: 0 auto 14px;
                border-radius: 16px;
            }

            .mobile-header h1 {
                font-size: 1.25rem;
                margin-bottom: 6px;
            }

            .mobile-header p {
                font-size: 0.8rem;
                line-height: 1.55;
                max-width: 270px;
            }

            .auth-container {
                max-width: 100%;
                padding: 0;
                border-radius: 16px;
                overflow: hidden;
            }

            .form-container {
                padding: 24px 18px 32px;
                max-height: calc(100vh - 240px);
                border-radius: 0 0 16px 16px;
            }

            #loginForm {
                display: flex;
                opacity: 1 !important;
                z-index: 2 !important;
            }

            #loginForm.hidden {
                display: none;
                opacity: 0 !important;
                z-index: 1 !important;
            }

            #registerForm {
                display: none;
                opacity: 0 !important;
                z-index: 1 !important;
            }

            #registerForm.visible {
                display: flex;
                opacity: 1 !important;
                z-index: 2 !important;
            }

            .form-header h2 {
                font-size: 1.4rem;
                margin-bottom: 0.2rem;
            }

            .form-header p {
                font-size: 0.8rem;
            }

            .input-wrapper input {
                padding: 11px 11px 11px 36px;
                font-size: 16px;
                min-height: 44px;
            }

            .input-group {
                margin-bottom: 0.9rem;
            }

            .btn-submit {
                padding: 13px;
                font-size: 0.95rem;
                min-height: 44px;
                margin-top: 0.5rem;
            }

            .divider {
                margin: 0.9rem 0;
                font-size: 0.75rem;
            }

            .terms-text {
                font-size: 0.65rem;
                line-height: 1.4;
                margin-top: 0.8rem;
            }

            .register-link {
                margin-top: 1rem;
                padding-top: 1rem;
            }

            .register-link p {
                font-size: 0.85rem;
            }

            .register-link a {
                font-weight: 600;
            }
        }

        @media (max-width: 768px) {
            html,
            body {
                min-height: 100%;
            }

            body {
                align-items: flex-start;
                padding: 18px 14px;
                background: #FFFFFF;
            }

            .auth-container {
                width: min(100%, 390px);
                max-width: 390px;
                min-height: auto;
                display: block;
                border-radius: 24px;
                overflow: hidden;
                background: #FFFFFF;
                box-shadow: 0 22px 45px rgba(15, 23, 42, 0.18);
                border: 1px solid rgba(226, 232, 240, 0.9);
            }

            .auth-container::before,
            .auth-container::after {
                content: '';
                position: absolute;
                width: 34px;
                height: 34px;
                background: #0F172A;
                z-index: 30;
                pointer-events: none;
            }

            .auth-container.register-mode::before,
            .auth-container.register-mode::after {
                background: #FACC15;
            }

            .auth-container::before {
                top: -1px;
                left: -1px;
                border-bottom-right-radius: 20px;
                box-shadow: inset -1px -1px 0 rgba(226, 232, 240, 0.9);
            }

            .auth-container::after {
                right: -1px;
                bottom: -1px;
                border-top-left-radius: 20px;
                box-shadow: inset 1px 1px 0 rgba(226, 232, 240, 0.9);
            }

            .auth-panel {
                display: none !important;
            }

            .mobile-header {
                display: block !important;
                padding: 36px 24px 18px;
                background: #FFFFFF;
                border-radius: 24px 24px 0 0;
                box-shadow: none;
                color: #0F172A;
                overflow: visible;
            }

            .mobile-header::before,
            .mobile-header::after {
                display: none;
            }

            .mobile-logo-container {
                width: 92px;
                height: 92px;
                margin: 0 auto 16px;
                border-radius: 0;
                background: transparent;
                border: 0;
                box-shadow: none;
            }

            .mobile-logo-container img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                filter: none;
            }

            .mobile-logo-container svg {
                width: 50%;
                height: 50%;
                color: #0F172A;
            }

            .mobile-header h1 {
                font-size: 1.25rem;
                color: #0F172A;
                text-shadow: none;
                margin-bottom: 6px;
                letter-spacing: 0;
            }

            .mobile-header p {
                font-size: 0.82rem;
                line-height: 1.55;
                color: #64748B;
                max-width: 280px;
            }

            .forms-container {
                position: relative;
                width: 100%;
                height: auto;
                overflow: visible;
            }

            .form-container {
                position: relative;
                width: 100%;
                height: auto;
                min-height: 0;
                max-height: none;
                padding: 10px 24px 34px;
                overflow: visible;
                justify-content: flex-start;
                background: #FFFFFF;
                border-radius: 0 0 24px 24px;
            }

            #loginForm {
                display: flex;
                right: auto;
                opacity: 1 !important;
                z-index: 2 !important;
            }

            #loginForm.hidden {
                display: none;
            }

            #registerForm {
                display: none;
                left: auto;
                opacity: 0 !important;
            }

            #registerForm.visible {
                display: flex;
                opacity: 1 !important;
            }

            .form-header {
                margin-bottom: 1.35rem;
                text-align: center;
            }

            .form-header h2 {
                font-size: 1.35rem;
                margin-bottom: 0.3rem;
                color: #0F172A;
            }

            .form-header p {
                font-size: 0.82rem;
                color: #64748B;
            }

            .input-group {
                margin-bottom: 0.9rem;
            }

            .input-group label {
                font-size: 0.78rem;
                margin-bottom: 0.4rem;
            }

            .input-wrapper input {
                min-height: 42px;
                padding: 11px 44px 11px 40px;
                border-width: 0;
                border-radius: 999px;
                background: #F1F5F9;
                font-size: 16px;
                box-shadow: inset 0 0 0 1px #E2E8F0;
            }

            .input-wrapper input:focus {
                border-color: transparent;
                box-shadow: inset 0 0 0 2px #0F172A, 0 0 0 4px rgba(250, 204, 21, 0.18);
            }

            .input-icon {
                width: 16px;
                height: 16px;
                left: 15px;
            }

            .password-toggle {
                right: 15px;
                width: 18px;
                height: 18px;
            }

            .btn-submit {
                min-height: 42px;
                padding: 11px 16px;
                border-radius: 999px;
                margin-top: 0.35rem;
                font-size: 0.95rem;
                background: linear-gradient(135deg, #0F172A, #1E293B);
                box-shadow: 0 10px 22px rgba(15, 23, 42, 0.24);
            }

            .divider {
                margin: 1rem 0;
                font-size: 0.72rem;
            }

            .social-login a {
                min-height: 42px;
                border-radius: 999px;
                padding: 10px 16px;
                font-size: 0.85rem;
            }

            .register-link {
                margin-top: 1rem;
                padding-top: 1rem;
            }

            .register-link p {
                font-size: 0.82rem;
            }

            .error-message,
            .success-message {
                border-radius: 14px;
                font-size: 0.82rem;
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 380px) {
            body {
                padding: 12px;
            }

            .auth-container {
                border-radius: 20px;
            }

            .mobile-header {
                padding: 32px 20px 14px;
            }

            .mobile-logo-container {
                width: 84px;
                height: 84px;
                margin-bottom: 14px;
            }

            .form-container {
                padding: 8px 20px 28px;
            }
        }
    </style>

</head>

<body>
    <div class="auth-container">
        <!-- Mobile Header Panel -->
        <div class="mobile-header" id="mobileHeader" style="display: none;">
            <div class="mobile-logo-container">
                @php $appSettings = \App\Models\AppSetting::getInstance(); @endphp
                @if($appSettings->app_logo)
                    <img src="{{ asset('storage/' . $appSettings->app_logo) }}" alt="Logo">
                @else
                    <svg fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                    </svg>
                @endif
            </div>
            <h1 id="mobileTitle">SMK ICB Cinta Teknika</h1>
            <p id="mobileDesc">Sistem presensi digital sekolah yang mudah, cepat, dan terpercaya.</p>
        </div>

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
                    <div class="error-message">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div class="success-message">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" id="loginFormElement" novalidate>
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

                <div class="register-link">
                    <p>Belum punya akun? <a href="#" onclick="toggleAuth(); return false;">Daftar sekarang</a></p>
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

                <div class="register-link">
                    <p>Sudah punya akun? <a href="#" onclick="toggleAuth(); return false;">Login sekarang</a></p>
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
            const mobileTitle = document.getElementById('mobileTitle');
            const mobileDesc = document.getElementById('mobileDesc');
            const formsContainer = document.querySelector('.forms-container');
            const authContainer = document.querySelector('.auth-container');

            if (loginForm.classList.contains('hidden')) {
                // Show Login
                panel.classList.remove('register');
                panel.classList.add('login');
                if (authContainer) authContainer.classList.remove('register-mode');
                loginForm.classList.remove('hidden');
                registerForm.classList.remove('visible');
                
                // Update mobile header text
                if (mobileTitle) mobileTitle.textContent = 'SMK ICB Cinta Teknika';
                if (mobileDesc) mobileDesc.textContent = 'Sistem presensi digital sekolah yang mudah, cepat, dan terpercaya.';
                
                panelTitle.textContent = 'Selamat Datang Di Website ICB CINTA TEKNIKA';
                panelText.textContent = 'Aplikasi Presensi Guru ICB Cinta Teknika';
                toggleBtn.textContent = 'Buat Akun';
                document.getElementById('pageTitle').textContent = "Login - {{ config('app.name', 'ICB CT') }}";
                
                // Scroll to top of form
                if (formsContainer) {
                    formsContainer.scrollTop = 0;
                }
                if (loginForm) {
                    loginForm.scrollTop = 0;
                }
            } else {
                // Show Register
                panel.classList.remove('login');
                panel.classList.add('register');
                if (authContainer) authContainer.classList.add('register-mode');
                loginForm.classList.add('hidden');
                registerForm.classList.add('visible');
                
                // Update mobile header text
                if (mobileTitle) mobileTitle.textContent = 'Daftar Akun Baru';
                if (mobileDesc) mobileDesc.textContent = 'Buat akun untuk mulai menggunakan aplikasi';
                
                panelTitle.textContent = 'Daftar Akun Baru';
                panelText.textContent = 'Silahkan register untuk mulai menggunakan aplikasi';
                toggleBtn.textContent = 'Sudah Punya Akun?';
                document.getElementById('pageTitle').textContent = "Register - {{ config('app.name', 'ICB CT') }}";
                
                // Scroll to top of form
                if (formsContainer) {
                    formsContainer.scrollTop = 0;
                }
                if (registerForm) {
                    registerForm.scrollTop = 0;
                }
            }
            
            // Scroll page to top on mobile (for better UX)
            window.scrollTo(0, 0);
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

        // Auto-focus email input on page load
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('login-email');
            if (emailInput) {
                emailInput.focus();
            }
        });
    </script>
</body>

</html>
