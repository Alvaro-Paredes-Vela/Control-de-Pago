{{-- resources/views/dashboard.blade.php --}}

@extends('adminlte::page')

@section('title', 'Dashboard | Centro Interno')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/admin-brand.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Manrope:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        /* ───────── Tokens (paleta tomada del logo) ───────── */
        .ci-dash {
            --ci-navy-900: #061a3d;
            --ci-navy-700: #0b2a5b;
            --ci-navy-500: #123f82;
            --ci-silver: #c9ced6;
            --ci-cyan: #1a9fd8;
            --ci-cyan-soft: #6fd3ff;

            --ci-surface: #ffffff;
            --ci-text: #12233f;
            --ci-muted: #66758c;
            --ci-line: #e4e9f1;
            --ci-chip: #e8f3fb;
            --ci-pos: #13805a;
            --ci-neg: #c0392b;
            --ci-shadow: 0 1px 2px rgba(11, 42, 91, .05), 0 8px 24px rgba(11, 42, 91, .06);

            font-family: 'Manrope', system-ui, -apple-system, 'Segoe UI', sans-serif;
            color: var(--ci-text);
            max-width: 1280px;
            margin: 0 auto;
            padding-bottom: 8px;
        }

        .dark-mode .ci-dash,
        [data-bs-theme="dark"] .ci-dash {
            --ci-surface: #1b2434;
            --ci-text: #e6ecf5;
            --ci-muted: #94a2b9;
            --ci-line: #2a3549;
            --ci-chip: rgba(111, 211, 255, .12);
            --ci-cyan: #4cc9f5;
            --ci-pos: #3ecf9a;
            --ci-neg: #ff7a6b;
            --ci-shadow: 0 1px 2px rgba(0, 0, 0, .3), 0 8px 24px rgba(0, 0, 0, .25);
        }

        .ci-dash *,
        .ci-dash *::before,
        .ci-dash *::after {
            box-sizing: border-box;
        }

        .ci-dash a:focus-visible {
            outline: 2px solid var(--ci-cyan);
            outline-offset: 3px;
        }

        .ci-serif {
            font-family: 'Fraunces', Georgia, serif;
        }

        /* ───────── Hero ───────── */
        .ci-hero {
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 32px;
            padding: 40px 48px;
            border-radius: 20px;
            color: #fff;
            background:
                radial-gradient(120% 140% at 100% 0%, rgba(26, 159, 216, .35) 0%, rgba(26, 159, 216, 0) 55%),
                linear-gradient(135deg, var(--ci-navy-900) 0%, var(--ci-navy-700) 58%, var(--ci-navy-500) 100%);
            box-shadow: 0 18px 40px rgba(6, 26, 61, .28);
            margin-bottom: 20px;
        }

        /* Retícula de puntos, evoca los circuitos del logo */
        .ci-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(111, 211, 255, .22) 1px, transparent 1.5px);
            background-size: 22px 22px;
            -webkit-mask-image: linear-gradient(90deg, transparent 30%, #000 100%);
            mask-image: linear-gradient(90deg, transparent 30%, #000 100%);
            pointer-events: none;
        }

        .ci-hero-body {
            position: relative;
            z-index: 1;
            min-width: 0;
        }

        .ci-hero-meta {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }

        .ci-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 600;
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .18);
            color: #e8f1ff;
        }

        .ci-pill--role {
            background: rgba(111, 211, 255, .16);
            border-color: rgba(111, 211, 255, .4);
            color: var(--ci-cyan-soft);
        }

        .ci-hero h1 {
            font-family: 'Fraunces', Georgia, serif;
            font-weight: 600;
            font-size: clamp(1.8rem, 3.4vw, 2.7rem);
            line-height: 1.12;
            letter-spacing: -.01em;
            margin: 0 0 12px;
            color: #fff;
        }

        .ci-hero p {
            margin: 0;
            max-width: 52ch;
            font-size: .98rem;
            line-height: 1.6;
            color: rgba(232, 241, 255, .78);
        }

        .ci-hero-logo {
            position: relative;
            z-index: 1;
            width: 240px;
            height: 240px;
            display: grid;
            place-items: center;
        }

        .ci-hero-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            filter: drop-shadow(0 0 26px rgba(26, 159, 216, .5)) drop-shadow(0 10px 22px rgba(0, 0, 0, .35));
        }

        /* ───────── Franja de indicadores ───────── */
        .ci-kpis {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: var(--ci-line);
            border: 1px solid var(--ci-line);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--ci-shadow);
            margin-bottom: 20px;
        }

        .ci-kpi {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 24px 26px;
            background: var(--ci-surface);
            min-width: 0;
        }

        .ci-chip {
            flex: none;
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1.3rem;
            color: var(--ci-cyan);
            background: var(--ci-chip);
        }

        .ci-kpi-value {
            font-family: 'Fraunces', Georgia, serif;
            font-weight: 600;
            font-size: 1.85rem;
            line-height: 1.1;
            letter-spacing: -.01em;
            color: var(--ci-text);
            white-space: nowrap;
        }

        .ci-kpi-label {
            margin-top: 4px;
            font-size: .84rem;
            font-weight: 500;
            color: var(--ci-muted);
        }

        /* ───────── Paneles ───────── */
        .ci-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .ci-grid--split {
            grid-template-columns: 5fr 7fr;
        }

        .ci-panel {
            background: var(--ci-surface);
            border: 1px solid var(--ci-line);
            border-radius: 16px;
            box-shadow: var(--ci-shadow);
            padding: 28px;
            min-width: 0;
        }

        .ci-panel-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 22px;
            font-family: 'Fraunces', Georgia, serif;
            font-weight: 600;
            font-size: 1.2rem;
            color: var(--ci-text);
        }

        .ci-panel-title i {
            color: var(--ci-cyan);
            font-size: 1.15rem;
        }

        /* ───────── Resumen financiero ───────── */
        .ci-balance-label {
            font-size: .84rem;
            font-weight: 500;
            color: var(--ci-muted);
        }

        .ci-balance {
            font-family: 'Fraunces', Georgia, serif;
            font-weight: 600;
            font-size: 2.5rem;
            line-height: 1.1;
            letter-spacing: -.02em;
            margin: 4px 0 20px;
            color: var(--ci-text);
        }

        .ci-balance.is-negative {
            color: var(--ci-neg);
        }

        .ci-bar {
            display: flex;
            height: 10px;
            border-radius: 999px;
            overflow: hidden;
            background: var(--ci-line);
            margin-bottom: 20px;
        }

        .ci-bar-in {
            background: var(--ci-pos);
        }

        .ci-bar-out {
            background: var(--ci-neg);
        }

        .ci-flow {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 13px 0;
            border-top: 1px solid var(--ci-line);
            font-size: .93rem;
        }

        .ci-flow-name {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--ci-muted);
            font-weight: 500;
        }

        .ci-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
        }

        .ci-flow strong {
            font-weight: 700;
            white-space: nowrap;
        }

        .ci-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            padding: 10px 18px;
            border-radius: 10px;
            font-size: .88rem;
            font-weight: 600;
            text-decoration: none !important;
            color: #fff !important;
            background: var(--ci-navy-700);
            transition: background .2s ease;
        }

        .ci-btn:hover {
            background: var(--ci-cyan);
        }

        /* ───────── Accesos rápidos ───────── */
        .ci-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .ci-action {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px;
            border: 1px solid var(--ci-line);
            border-radius: 12px;
            color: var(--ci-text) !important;
            text-decoration: none !important;
            transition: border-color .2s ease, background .2s ease;
        }

        .ci-action:hover {
            border-color: var(--ci-cyan);
            background: var(--ci-chip);
        }

        .ci-action .ci-chip {
            width: 42px;
            height: 42px;
            font-size: 1.15rem;
        }

        .ci-action-title {
            display: block;
            font-size: .95rem;
            font-weight: 700;
            line-height: 1.25;
        }

        .ci-action-desc {
            display: block;
            margin-top: 2px;
            font-size: .8rem;
            line-height: 1.35;
            color: var(--ci-muted);
        }

        /* ───────── Nota informativa ───────── */
        .ci-note {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 22px 26px;
            background: var(--ci-surface);
            border: 1px solid var(--ci-line);
            border-left: 4px solid var(--ci-cyan);
            border-radius: 12px;
            box-shadow: var(--ci-shadow);
        }

        .ci-note i {
            font-size: 1.4rem;
            color: var(--ci-cyan);
            line-height: 1.3;
        }

        .ci-note h2 {
            margin: 0 0 4px;
            font-family: 'Fraunces', Georgia, serif;
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--ci-text);
        }

        .ci-note p {
            margin: 0;
            max-width: 75ch;
            font-size: .88rem;
            line-height: 1.6;
            color: var(--ci-muted);
        }

        /* ───────── Responsive ───────── */
        @media (max-width: 1199px) {
            .ci-kpis {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 991px) {
            .ci-grid--split {
                grid-template-columns: 1fr;
            }

            .ci-hero {
                padding: 32px 28px;
            }
        }

        @media (max-width: 640px) {
            .ci-hero {
                grid-template-columns: 1fr;
                justify-items: start;
                gap: 24px;
                padding: 26px 22px;
            }

            .ci-hero-logo {
                order: -1;
                width: 140px;
                height: 140px;
            }

            .ci-kpis,
            .ci-actions {
                grid-template-columns: 1fr;
            }

            .ci-panel {
                padding: 22px;
            }

            .ci-balance {
                font-size: 2rem;
            }
        }
    </style>
@endpush

{{-- El título vive en el hero, así que no se usa la cabecera por defecto --}}
@section('content_header')
@stop

@section('content')

    @php
        $user = Auth::user();
        $esFinanzas = in_array($user->rol, ['administrador', 'tesorero']);

        $ingresos = (float) ($totalIngresos ?? 0);
        $egresos = (float) ($totalEgresos ?? 0);
        $saldo = (float) ($saldoDisponible ?? 0);
        $flujo = $ingresos + $egresos;
        $pctIngresos = $flujo > 0 ? round(($ingresos / $flujo) * 100, 1) : 0;
        $pctEgresos = $flujo > 0 ? round(100 - $pctIngresos, 1) : 0;
    @endphp

    <div class="ci-dash">

        {{-- HERO --}}
        <section class="ci-hero">
            <div class="ci-hero-body">
                <div class="ci-hero-meta">
                    <span class="ci-pill ci-pill--role">
                        <i class="bi bi-shield-check"></i>
                        {{ ucfirst($user->rol) }}
                    </span>
                    <span class="ci-pill">
                        <i class="bi bi-calendar3"></i>
                        {{ now()->locale('es')->translatedFormat('l, d \d\e F \d\e Y') }}
                    </span>
                </div>

                <h1>Bienvenido, {{ $user->name }}</h1>

                <p>
                    Centro Interno de Ingeniería en Sistemas, FINOR – UAGRM.
                    Gestiona las actividades, las inscripciones y los pagos desde un solo lugar.
                </p>
            </div>

            <div class="ci-hero-logo">
                <img src="{{ asset('img/sistemas.png') }}"
                    alt="Logo de la Carrera de Ingeniería en Sistemas, FINOR – UAGRM">
            </div>
        </section>

        {{-- INDICADORES --}}
        <section class="ci-kpis" aria-label="Indicadores generales">

            <div class="ci-kpi">
                <span class="ci-chip"><i class="bi bi-mortarboard-fill"></i></span>
                <div>
                    <div class="ci-kpi-value">{{ number_format($totalEstudiantes ?? 0) }}</div>
                    <div class="ci-kpi-label">Estudiantes registrados</div>
                </div>
            </div>

            <div class="ci-kpi">
                <span class="ci-chip"><i class="bi bi-calendar2-event-fill"></i></span>
                <div>
                    <div class="ci-kpi-value">{{ number_format($totalActividades ?? 0) }}</div>
                    <div class="ci-kpi-label">Actividades registradas</div>
                </div>
            </div>

            <div class="ci-kpi">
                <span class="ci-chip"><i class="bi bi-journal-check"></i></span>
                <div>
                    <div class="ci-kpi-value">{{ number_format($totalInscripciones ?? 0) }}</div>
                    <div class="ci-kpi-label">Inscripciones activas</div>
                </div>
            </div>

            <div class="ci-kpi">
                <span class="ci-chip"><i class="bi bi-cash-coin"></i></span>
                <div>
                    <div class="ci-kpi-value">Bs {{ number_format($totalPagos ?? 0, 2) }}</div>
                    <div class="ci-kpi-label">Pagos confirmados</div>
                </div>
            </div>

        </section>

        {{-- FINANZAS + ACCESOS --}}
        <div class="ci-grid {{ $esFinanzas ? 'ci-grid--split' : '' }}">

            @if ($esFinanzas)
                <section class="ci-panel">
                    <h2 class="ci-panel-title">
                        <i class="bi bi-wallet2"></i>
                        Resumen financiero
                    </h2>

                    <div class="ci-balance-label">Saldo disponible</div>
                    <div class="ci-balance {{ $saldo < 0 ? 'is-negative' : '' }}">
                        Bs {{ number_format($saldo, 2) }}
                    </div>

                    <div class="ci-bar" role="img"
                        aria-label="Ingresos {{ $pctIngresos }}% y egresos {{ $pctEgresos }}%">
                        <span class="ci-bar-in" style="width: {{ $pctIngresos }}%"></span>
                        <span class="ci-bar-out" style="width: {{ $pctEgresos }}%"></span>
                    </div>

                    <div class="ci-flow">
                        <span class="ci-flow-name">
                            <span class="ci-dot" style="background: var(--ci-pos)"></span>
                            Total de ingresos
                        </span>
                        <strong style="color: var(--ci-pos)">Bs {{ number_format($ingresos, 2) }}</strong>
                    </div>

                    <div class="ci-flow">
                        <span class="ci-flow-name">
                            <span class="ci-dot" style="background: var(--ci-neg)"></span>
                            Total de egresos
                        </span>
                        <strong style="color: var(--ci-neg)">Bs {{ number_format($egresos, 2) }}</strong>
                    </div>

                    <a href="{{ route('movimientos-caja.index') }}" class="ci-btn">
                        <i class="bi bi-arrow-left-right"></i>
                        Ver movimientos de caja
                    </a>
                </section>
            @endif

            <section class="ci-panel">
                <h2 class="ci-panel-title">
                    <i class="bi bi-lightning-charge-fill"></i>
                    Accesos rápidos
                </h2>

                <div class="ci-actions">

                    <a href="{{ route('estudiantes.create') }}" class="ci-action">
                        <span class="ci-chip"><i class="bi bi-person-plus-fill"></i></span>
                        <span>
                            <span class="ci-action-title">Registrar estudiante</span>
                            <span class="ci-action-desc">Agrega un estudiante nuevo</span>
                        </span>
                    </a>

                    <a href="{{ route('actividades.create') }}" class="ci-action">
                        <span class="ci-chip"><i class="bi bi-calendar-plus-fill"></i></span>
                        <span>
                            <span class="ci-action-title">Crear actividad</span>
                            <span class="ci-action-desc">Taller, torneo, paseo o conferencia</span>
                        </span>
                    </a>

                    <a href="{{ route('inscripciones.create') }}" class="ci-action">
                        <span class="ci-chip"><i class="bi bi-journal-plus"></i></span>
                        <span>
                            <span class="ci-action-title">Inscribir estudiante</span>
                            <span class="ci-action-desc">Asigna un estudiante a una actividad</span>
                        </span>
                    </a>

                    @if ($esFinanzas)
                        <a href="{{ route('pagos.create') }}" class="ci-action">
                            <span class="ci-chip"><i class="bi bi-cash-stack"></i></span>
                            <span>
                                <span class="ci-action-title">Registrar pago</span>
                                <span class="ci-action-desc">Anota el abono de un estudiante</span>
                            </span>
                        </a>
                    @endif

                </div>
            </section>

        </div>

        {{-- NOTA --}}
        <aside class="ci-note">
            <i class="bi bi-info-circle-fill"></i>
            <div>
                <h2>Control de actividades y pagos</h2>
                <p>
                    Administra conferencias, talleres, torneos y paseos. Registra inscripciones,
                    controla los abonos de cada estudiante y consulta los movimientos económicos
                    del Centro Interno.
                </p>
            </div>
        </aside>

    </div>

@stop
