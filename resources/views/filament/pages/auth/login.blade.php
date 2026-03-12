<x-filament-panels::page.simple>
    @if (filament()->hasRegistration())
        <x-slot name="subheading">
            {{ __('filament-panels::pages/auth/login.actions.register.before') }}
            
            {{ $this->registerAction }}
        </x-slot>
    @endif

    <div class="fi-custom-login-wrapper">
        <div class="login-left">
            <div class="login-left-bg"></div>
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>
            <div class="login-content">
                <img src="{{ asset('assets/images/logo-white.png') }}" alt="TrustEdu ERP Logo" class="brand-logo" />
                <h1 class="brand-title">Welcome to TrustEdu ERP</h1>
                <p class="brand-subtitle">
                    The modern, scalable, and intelligent platform for managing educational operations with absolute ease.
                </p>
                <div class="brand-features">
                    <span class="feature-badge">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg> Secure Access
                    </span>
                    <span class="feature-badge">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg> Lightning Fast
                    </span>
                </div>
            </div>
        </div>
        <div class="login-right">
            <div class="login-form-container">
                <div class="mobile-logo-wrapper">
                    <img src="{{ asset('assets/images/logo-color.png') }}" class="mobile-logo" alt="Logo">
                </div>
                <h2 class="form-title">Sign in to Admin</h2>
                <p class="form-subtitle">Welcome back! Please enter your details.</p>
                
                <div class="form-inner">
                    {{ $this->content }}
                </div>
                
                <a href="/" class="back-link">
                    &larr; Back to website
                </a>
            </div>
        </div>
    </div>

    <style>
        /* Override default simple layout from Filament */
        body { margin: 0; padding: 0; overflow-x: hidden; }
        .fi-simple-main {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            height: 100vh !important;
            display: block !important;
            background: #f8fafc;
        }
        .fi-logo { display: none !important; }
        main > header, main > section {
            display: none !important;
        }

        /* Full Screen Overlay wrapper */
        .fi-custom-login-wrapper {
            position: fixed;
            inset: 0;
            display: flex;
            background: #f8fafc;
            z-index: 9999;
            font-family: inherit;
        }

        /* LEFT SIDE - Branding and Animations */
        .login-left {
            display: none;
            position: relative;
            background: #1e1b4b; /* Deep Indigo BG */
            overflow: hidden;
            align-items: center;
            justify-content: center;
        }
        @media (min-width: 1024px) {
            .login-left {
                display: flex;
                width: 50%;
            }
        }
        
        .login-left-bg {
            position: absolute;
            inset: 0;
            background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=2070');
            background-size: cover;
            background-position: center;
            opacity: 0.15;
            mix-blend-mode: overlay;
        }

        /* Animated Blobs / Orbs */
        .orb {
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: #4f46e5;
            mix-blend-mode: multiply;
            filter: blur(120px);
            opacity: 0.6;
            animation: orbFloat 10s infinite;
        }
        .orb-1 { top: -15%; left: -10%; background: #2563eb; }
        .orb-2 { bottom: -10%; right: -20%; background: #9333ea; animation-delay: 3s; filter: blur(140px); }
        .orb-3 { bottom: 30%; left: 30%; background: #db2777; width: 350px; height: 350px; filter: blur(100px); animation-delay: 6s; opacity: 0.4; }

        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(40px, -60px) scale(1.1); }
            66% { transform: translate(-30px, 30px) scale(0.9); }
            100% { transform: translate(0, 0) scale(1); }
        }

        /* Left Side Content */
        .login-content {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 0 4rem;
            color: #fff;
        }
        .brand-logo {
            height: 100px;
            margin: 0 auto 2rem auto;
            transform: translateY(20px);
            opacity: 0;
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.3));
        }
        .brand-title {
            font-size: 2.75rem;
            font-weight: 800;
            margin-bottom: 1rem;
            letter-spacing: -0.025em;
            transform: translateY(20px);
            opacity: 0;
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards 0.2s;
        }
        .brand-subtitle {
            font-size: 1.125rem;
            color: #c7d2fe;
            line-height: 1.7;
            max-width: 28rem;
            margin: 0 auto;
            transform: translateY(20px);
            opacity: 0;
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards 0.4s;
        }
        .brand-features {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            transform: translateY(20px);
            opacity: 0;
            animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards 0.6s;
        }
        .feature-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .feature-badge svg { height: 16px; margin-right: 6px; }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* RIGHT SIDE - Login Form Wrapper */
        .login-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow-y: auto;
            padding: 2rem;
            background: #fff;
        }
        @media (min-width: 1024px) {
            .login-right {
                background: transparent;
            }
            .login-form-container {
                max-width: 440px;
                margin: 0 auto;
            }
        }
        
        .login-form-container {
            width: 100%;
            max-width: 400px;
            margin: auto;
            position: relative;
            z-index: 10;
        }
        
        .mobile-logo-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        @media (min-width: 1024px) {
            .mobile-logo-wrapper { display: none; }
        }
        .mobile-logo { height: 64px; }

        .form-title {
            font-size: 1.875rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
            text-align: center;
        }
        @media (min-width: 1024px) {
            .form-title { text-align: left; }
        }
        .form-subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 2rem;
            text-align: center;
        }
        @media (min-width: 1024px) {
            .form-subtitle { text-align: left; margin-bottom: 2.5rem; }
        }

        /* Form Glass/Card styling */
        .form-inner {
            background: #ffffff;
            padding: 2.5rem;
            border-radius: 1.25rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            border: 1px solid #f8fafc;
            transform: translateY(0);
            transition: all 0.4s ease;
        }
        .form-inner:hover {
            box-shadow: 0 25px 30px -5px rgba(0, 0, 0, 0.08), 0 10px 15px -6px rgba(0, 0, 0, 0.03);
        }

        /* Button Customization (overriding Filament partially if needed) */
        .fi-btn-primary {
            transition: all 0.3s ease;
        }
        .fi-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.3);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 2rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #6366f1;
            text-decoration: none;
            transition: color 0.2s;
        }
        .back-link:hover { color: #4338ca; }
    </style>
</x-filament-panels::page.simple>
