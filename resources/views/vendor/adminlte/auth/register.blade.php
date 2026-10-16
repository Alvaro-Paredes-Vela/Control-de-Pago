@extends('adminlte::auth.auth-page', ['authType' => 'register'])

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');

    $loginUrl = $layoutHelper->makeUrl($loginUrl);
    $registerUrl = $layoutHelper->makeUrl($registerUrl);
@endphp

{{-- ===================== ESTILOS ===================== --}}
@push('css')
    @verbatim
        <style>
            /* ---------- Fondo (igual que el login y la presentación Expofinor) ---------- */
            html body.register-page,
            html body.login-page {
                background: #04060b !important;
                min-height: 100vh;
                overflow-x: hidden;
                overflow-y: auto;
            }

            #auth-bg {
                --c1: #1261FB;
                --c2: #29B6FF;
                position: fixed;
                inset: 0;
                z-index: 0;
                overflow: hidden;
                pointer-events: none;
                background: radial-gradient(120% 90% at 50% 38%, #0D1B2E 0%, #0A0F1C 55%, #04060b 100%);
            }

            #auth-bg .blob {
                position: absolute;
                width: 62vmax;
                height: 62vmax;
                border-radius: 50%;
                filter: blur(100px);
                opacity: .30;
                mix-blend-mode: screen;
                will-change: transform;
            }

            #auth-bg .b1 {
                background: var(--c1);
                top: -25vmax;
                left: -20vmax;
                animation: auth-drift1 30s ease-in-out infinite alternate;
            }

            #auth-bg .b2 {
                background: var(--c2);
                bottom: -30vmax;
                right: -22vmax;
                animation: auth-drift2 36s ease-in-out infinite alternate;
            }

            #auth-bg .b3 {
                background: radial-gradient(circle, var(--c2), var(--c1));
                width: 40vmax;
                height: 40vmax;
                top: 20%;
                left: 35%;
                opacity: .13;
                animation: auth-drift3 26s ease-in-out infinite alternate;
            }

            @keyframes auth-drift1 {
                to {
                    transform: translate(28vmax, 16vmax) scale(1.25);
                }
            }

            @keyframes auth-drift2 {
                to {
                    transform: translate(-30vmax, -14vmax) scale(1.2);
                }
            }

            @keyframes auth-drift3 {
                to {
                    transform: translate(-22vmax, 12vmax) scale(.8) rotate(90deg);
                }
            }

            #auth-bg .floor {
                position: absolute;
                left: -50%;
                width: 200%;
                bottom: -6%;
                height: 52%;
                transform: perspective(420px) rotateX(64deg);
                transform-origin: 50% 100%;
                background-image:
                    linear-gradient(color-mix(in srgb, var(--c2) 50%, transparent) 1px, transparent 1px),
                    linear-gradient(90deg, color-mix(in srgb, var(--c2) 50%, transparent) 1px, transparent 1px);
                background-size: 72px 72px;
                animation: auth-floor 5.2s linear infinite;
                -webkit-mask-image: linear-gradient(to top, #000 4%, transparent 78%);
                mask-image: linear-gradient(to top, #000 4%, transparent 78%);
                opacity: .34;
            }

            @keyframes auth-floor {
                to {
                    background-position: 0 72px;
                }
            }

            #auth-bg canvas {
                position: absolute;
                inset: 0;
            }

            #auth-bg .sweep {
                position: absolute;
                inset: -20%;
                mix-blend-mode: screen;
                background: linear-gradient(105deg, transparent 43%, rgba(255, 255, 255, .055) 50%, transparent 57%);
                transform: translateX(-120%);
                animation: auth-sweep 13s ease-in-out infinite;
            }

            @keyframes auth-sweep {

                0%,
                22% {
                    transform: translateX(-120%);
                }

                68%,
                100% {
                    transform: translateX(120%);
                }
            }

            #auth-bg .grain {
                position: absolute;
                inset: -50%;
                opacity: .06;
                mix-blend-mode: overlay;
                background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='220' height='220'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>");
                animation: auth-grain .7s steps(6) infinite;
            }

            @keyframes auth-grain {
                0% {
                    transform: translate(0, 0);
                }

                20% {
                    transform: translate(-4%, 3%);
                }

                40% {
                    transform: translate(3%, -5%);
                }

                60% {
                    transform: translate(-5%, -2%);
                }

                80% {
                    transform: translate(4%, 4%);
                }

                100% {
                    transform: translate(0, 0);
                }
            }

            #auth-bg .vignette {
                position: absolute;
                inset: 0;
                background: radial-gradient(ellipse at center, transparent 50%, rgba(0, 0, 0, .74) 100%);
            }

            .register-box,
            .login-box {
                position: relative;
                z-index: 1;
                animation: auth-rise .7s cubic-bezier(.2, .8, .2, 1) both;
            }

            @keyframes auth-rise {
                from {
                    opacity: 0;
                    transform: translateY(18px) scale(.98);
                }

                to {
                    opacity: 1;
                    transform: none;
                }
            }

            /* ---------- Logo: cuadrado, sin borde, esquinas redondeadas ---------- */
            .register-logo,
            .login-logo {
                margin-bottom: 1.5rem;
                display: flex;
                justify-content: center;
                overflow: visible;
            }

            .register-logo a,
            .login-logo a {
                display: inline-flex;
                align-items: center;
                gap: clamp(.6rem, 2.5vw, 1rem);
                white-space: nowrap;
                font-size: clamp(1.35rem, 5.2vw, 2rem);
                font-weight: 300;
                line-height: 1.1;
                letter-spacing: .06em;
                text-decoration: none;
                background: linear-gradient(90deg, #ffffff 0%, #bae6fd 45%, #38bdf8 100%);
                -webkit-background-clip: text;
                background-clip: text;
                -webkit-text-fill-color: transparent;
                filter: drop-shadow(0 4px 18px rgba(56, 189, 248, .35));
                transition: filter .3s ease;
            }

            .register-logo a:hover,
            .login-logo a:hover {
                filter: drop-shadow(0 4px 24px rgba(56, 189, 248, .6));
            }

            .register-logo b,
            .login-logo b {
                font-weight: 700;
            }

            .register-logo img,
            .login-logo img {
                width: clamp(56px, 16vw, 90px);
                height: clamp(56px, 16vw, 90px);
                border: 0 !important;
                padding: 0 !important;
                background: transparent !important;
                border-radius: 22px !important;
                box-shadow: 0 10px 40px rgba(37, 99, 235, .35);
                object-fit: cover;
            }

            /* ---------- Tarjeta (glass) ---------- */
            .register-box .card,
            .login-box .card {
                background: rgba(15, 23, 42, .55);
                backdrop-filter: blur(16px) saturate(140%);
                -webkit-backdrop-filter: blur(16px) saturate(140%);
                border: 1px solid rgba(255, 255, 255, .12);
                border-top: 2px solid rgba(96, 165, 250, .8);
                border-radius: 1.1rem;
                box-shadow: 0 25px 60px rgba(0, 0, 0, .45);
                color: #e2e8f0;
            }

            .register-box .card-header,
            .login-box .card-header {
                background: transparent;
                border-bottom: 0;
                padding-top: 1.5rem;
            }

            .auth-title {
                display: block;
                text-align: center;
                font-size: 1.15rem;
                font-weight: 600;
                color: #f1f5f9;
            }

            .register-box .card-body,
            .login-box .card-body {
                background: transparent;
                padding: 1.25rem 1.75rem 1.9rem;
                color: #e2e8f0;
            }

            /* ---------- Inputs ---------- */
            .auth-form .input-group-text,
            .auth-form .form-control,
            .btn-toggle-password {
                background: rgba(255, 255, 255, .06);
                border: 1px solid rgba(255, 255, 255, .14);
                color: #cbd5e1;
            }

            .auth-form .input-group-text {
                border-right: 0;
            }

            .auth-form .form-control {
                border-left: 0;
            }

            .auth-form .form-control::placeholder {
                color: #94a3b8;
            }

            .auth-form .form-control:focus {
                background: rgba(255, 255, 255, .1);
                color: #fff;
                border-color: rgba(96, 165, 250, .7);
                box-shadow: none;
            }

            .auth-form .input-group:focus-within .input-group-text,
            .auth-form .input-group:focus-within .btn-toggle-password {
                border-color: rgba(96, 165, 250, .7);
                color: #93c5fd;
            }

            .btn-toggle-password {
                border-left: 0;
            }

            .btn-toggle-password:hover {
                color: #fff;
                background: rgba(255, 255, 255, .12);
            }

            .auth-form .form-control.is-invalid {
                border-color: #f87171;
                background-image: none;
            }

            .auth-form .invalid-feedback {
                color: #fca5a5;
            }

            /* ---------- Botones ---------- */
            .btn-auth {
                padding: .65rem 1rem;
                font-weight: 600;
                border: 0;
                border-radius: .7rem;
                background: linear-gradient(135deg, #2563eb, #0ea5e9);
                box-shadow: 0 8px 24px rgba(37, 99, 235, .4);
                transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
            }

            .btn-auth:hover {
                transform: translateY(-1px);
                filter: brightness(1.08);
                box-shadow: 0 12px 30px rgba(37, 99, 235, .55);
            }

            .btn-auth-secondary {
                padding: .6rem 1rem;
                border-radius: .7rem;
                border-color: rgba(255, 255, 255, .25);
                color: #e2e8f0;
                transition: background .2s ease, border-color .2s ease;
            }

            .btn-auth-secondary:hover {
                background: rgba(255, 255, 255, .1);
                border-color: rgba(255, 255, 255, .5);
                color: #fff;
            }

            /* ---------- Separador ---------- */
            .auth-divider {
                display: flex;
                align-items: center;
                gap: .75rem;
                margin: .25rem 0 1rem;
                font-size: .8rem;
                color: #94a3b8;
            }

            .auth-divider::before,
            .auth-divider::after {
                content: "";
                flex: 1;
                height: 1px;
                background: rgba(255, 255, 255, .12);
            }

            /* ---------- Responsivo ---------- */
            .register-box,
            .login-box {
                width: 100%;
                max-width: 420px;
                margin-inline: auto;
                padding-inline: 12px;
            }

            @media (max-width: 575.98px) {

                .register-logo,
                .login-logo {
                    margin-bottom: 1rem;
                }

                .register-logo a,
                .login-logo a {
                    flex-direction: column;
                    gap: .6rem;
                    white-space: normal;
                    text-align: center;
                    font-size: 1.5rem;
                }

                .register-box .card-body,
                .login-box .card-body {
                    padding: 1rem 1.1rem 1.4rem;
                }

                .auth-title {
                    font-size: 1.05rem;
                }

                #auth-bg .floor {
                    height: 38%;
                }
            }

            @media (max-height: 560px) {

                .register-logo a,
                .login-logo a {
                    flex-direction: row;
                    font-size: 1.2rem;
                }

                .register-logo img,
                .login-logo img {
                    width: 48px;
                    height: 48px;
                    border-radius: 14px !important;
                }
            }

            @media (prefers-reduced-motion: reduce) {

                .register-box,
                .login-box,
                #auth-bg .blob,
                #auth-bg .floor,
                #auth-bg .sweep,
                #auth-bg .grain {
                    animation: none;
                }
            }
        </style>
    @endverbatim
@endpush

{{-- ===================== SCRIPT ===================== --}}
@push('js')
    @verbatim
        <script>
            (() => {
                'use strict';

                /* ---------- Fondo Expofinor: blobs + piso + partículas + glifos + estrellas fugaces ---------- */
                const bg = document.createElement('div');
                bg.id = 'auth-bg';
                bg.innerHTML = `
                    <div class="blob b1"></div><div class="blob b2"></div><div class="blob b3"></div>
                    <div class="floor"></div>
                    <canvas></canvas>
                    <div class="sweep"></div>
                    <div class="grain"></div>
                    <div class="vignette"></div>`;
                document.body.prepend(bg);

                const cv = bg.querySelector('canvas');
                const c = cv.getContext('2d');
                const calm = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? .3 : 1;
                const RGB = '41,182,255';
                const G = ['</>', '{ }', '[ ]', '01', '=>', '10', '( )', '&&', '#', '0x', '::'];

                let W, H, DPR, pts = [],
                    glyphs = [],
                    streaks = [],
                    streakT = 3000,
                    last = 0;

                const newGlyph = (init) => ({
                    x: Math.random() * W,
                    y: init ? Math.random() * H : H + 30,
                    v: Math.random() * .35 + .1,
                    s: Math.random() * 12 + 11,
                    a: Math.random() * .14 + .06,
                    t: G[(Math.random() * G.length) | 0],
                    ph: Math.random() * 6
                });

                function resize() {
                    DPR = Math.min(window.devicePixelRatio || 1, 2);
                    W = window.innerWidth;
                    H = window.innerHeight;
                    cv.width = W * DPR;
                    cv.height = H * DPR;
                    cv.style.width = W + 'px';
                    cv.style.height = H + 'px';
                    c.setTransform(DPR, 0, 0, DPR, 0, 0);

                    const n = Math.max(40, Math.min(100, Math.round(W * H / 18000)));
                    pts = Array.from({
                        length: n
                    }, () => ({
                        x: Math.random() * W,
                        y: Math.random() * H,
                        vx: (Math.random() - .5) * .28,
                        vy: -Math.random() * .28 - .04,
                        r: Math.random() * 1.6 + .5
                    }));
                    glyphs = Array.from({
                        length: 18
                    }, () => newGlyph(true));
                }

                function frame(now) {
                    const dt = Math.min(now - last, 60);
                    last = now;
                    c.clearRect(0, 0, W, H);

                    // Partículas
                    for (const p of pts) {
                        p.x += p.vx * calm * dt / 16;
                        p.y += p.vy * calm * dt / 16;
                        if (p.y < -10) {
                            p.y = H + 10;
                            p.x = Math.random() * W;
                        }
                        if (p.x < -10) p.x = W + 10;
                        if (p.x > W + 10) p.x = -10;
                        c.beginPath();
                        c.arc(p.x, p.y, p.r, 0, 6.283);
                        c.fillStyle = `rgba(${RGB},.7)`;
                        c.fill();
                    }

                    // Líneas entre partículas cercanas
                    c.lineWidth = 1;
                    for (let i = 0; i < pts.length; i++) {
                        for (let j = i + 1; j < pts.length; j++) {
                            const dx = pts[i].x - pts[j].x,
                                dy = pts[i].y - pts[j].y,
                                d = dx * dx + dy * dy;
                            if (d < 16900) {
                                c.strokeStyle = `rgba(${RGB},${(1 - d / 16900) * .22})`;
                                c.beginPath();
                                c.moveTo(pts[i].x, pts[i].y);
                                c.lineTo(pts[j].x, pts[j].y);
                                c.stroke();
                            }
                        }
                    }

                    // Glifos de código flotantes
                    for (const g of glyphs) {
                        g.y -= g.v * calm * dt / 16;
                        g.ph += dt * .0013;
                        if (g.y < -30) Object.assign(g, newGlyph(false));
                        c.font = `${g.s}px Consolas, monospace`;
                        c.fillStyle = `rgba(${RGB},${g.a * (.7 + .3 * Math.sin(g.ph))})`;
                        c.fillText(g.t, g.x, g.y);
                    }

                    // Estrellas fugaces
                    streakT -= dt;
                    if (streakT <= 0) {
                        streaks.push({
                            x: Math.random() * W * .7,
                            y: Math.random() * H * .45,
                            life: 0
                        });
                        streakT = 4500 + Math.random() * 5000;
                    }
                    streaks = streaks.filter(s => s.life < 1);
                    for (const s of streaks) {
                        s.life += dt / 1300;
                        const k = s.life,
                            x = s.x + k * 420,
                            y = s.y + k * 190;
                        const gr = c.createLinearGradient(x - 150, y - 68, x, y);
                        gr.addColorStop(0, `rgba(${RGB},0)`);
                        gr.addColorStop(1, `rgba(255,255,255,${.75 * (1 - k)})`);
                        c.strokeStyle = gr;
                        c.lineWidth = 1.6;
                        c.beginPath();
                        c.moveTo(x - 150, y - 68);
                        c.lineTo(x, y);
                        c.stroke();
                    }

                    requestAnimationFrame(frame);
                }

                window.addEventListener('resize', resize);
                resize();
                requestAnimationFrame((t) => {
                    last = t;
                    frame(t);
                });

                /* ---------- Mostrar / ocultar contraseña (ambos campos) ---------- */
                document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const input = document.querySelector(btn.dataset.togglePassword);
                        const icon = btn.querySelector('i');
                        const show = input.type === 'password';

                        input.type = show ? 'text' : 'password';
                        icon.classList.toggle('bi-eye', !show);
                        icon.classList.toggle('bi-eye-slash', show);
                    });
                });
            })
            ();
        </script>
    @endverbatim
@endpush

{{-- ===================== VISTA ===================== --}}
@section('auth_header')
    <span class="auth-title">Crea tu cuenta</span>
@stop

@section('auth_body')
    <form action="{{ $registerUrl }}" method="post" class="auth-form" novalidate>
        @csrf

        {{-- Nombre completo --}}
        <label for="name" class="visually-hidden">Nombre completo</label>
        <div class="input-group mb-3">
            <span class="input-group-text">
                <i class="bi bi-person-fill {{ config('adminlte.classes_auth_icon', '') }}"></i>
            </span>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}" placeholder="Nombre completo" autocomplete="name" autofocus>

            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Correo electrónico --}}
        <label for="email" class="visually-hidden">Correo electrónico</label>
        <div class="input-group mb-3">
            <span class="input-group-text">
                <i class="bi bi-envelope {{ config('adminlte.classes_auth_icon', '') }}"></i>
            </span>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="Correo electrónico" autocomplete="email">

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Contraseña --}}
        <label for="password" class="visually-hidden">Contraseña</label>
        <div class="input-group mb-3">
            <span class="input-group-text">
                <i class="bi bi-lock-fill {{ config('adminlte.classes_auth_icon', '') }}"></i>
            </span>
            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña"
                autocomplete="new-password">
            <button type="button" class="btn btn-toggle-password" data-toggle-password="#password"
                aria-label="Mostrar u ocultar contraseña">
                <i class="bi bi-eye"></i>
            </button>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Confirmar contraseña --}}
        <label for="password_confirmation" class="visually-hidden">Confirmar contraseña</label>
        <div class="input-group mb-4">
            <span class="input-group-text">
                <i class="bi bi-shield-lock-fill {{ config('adminlte.classes_auth_icon', '') }}"></i>
            </span>
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirmar contraseña"
                autocomplete="new-password">
            <button type="button" class="btn btn-toggle-password" data-toggle-password="#password_confirmation"
                aria-label="Mostrar u ocultar confirmación de contraseña">
                <i class="bi bi-eye"></i>
            </button>

            @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Botón registrarse --}}
        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-auth {{ config('adminlte.classes_auth_btn', 'btn-primary') }}">
                <i class="bi bi-person-plus me-1"></i>
                Registrarme
            </button>
        </div>
    </form>

    <div class="auth-divider"><span>¿Ya tienes una cuenta?</span></div>

    {{-- Botón volver al login --}}
    <div class="d-grid">
        <a href="{{ $loginUrl }}" class="btn btn-outline-light btn-auth-secondary">
            <i class="bi bi-box-arrow-in-right me-1"></i>
            Iniciar sesión
        </a>
    </div>
@stop
