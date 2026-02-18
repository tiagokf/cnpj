<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consulta de CNPJ - Sistema de Busca</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --bg-primary: #111424;
            --bg-deep: #0a0c17;
            --bg-surface: #161a2e;
            --bg-surface-hover: #1c2038;
            --bg-elevated: #1e2240;
            --accent: #0EE57F;
            --accent-soft: rgba(14, 229, 127, 0.08);
            --accent-medium: rgba(14, 229, 127, 0.15);
            --accent-glow: rgba(14, 229, 127, 0.25);
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-tertiary: #64748b;
            --border-subtle: rgba(255, 255, 255, 0.06);
            --border-medium: rgba(255, 255, 255, 0.1);
            --danger: #ef4444;
            --danger-soft: rgba(239, 68, 68, 0.12);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-deep);
            min-height: 100vh;
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
        }

        #bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        .mono { font-family: 'JetBrains Mono', monospace; }

        .container {
            position: relative;
            z-index: 1;
            max-width: 740px;
            margin: 0 auto;
            padding: 3.5rem 1.25rem 3rem;
        }

        /* Search section (logo integrated) */
        .search-section {
            background: rgba(22, 26, 46, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            overflow: hidden;
            box-shadow:
                0 1px 3px rgba(0, 0, 0, 0.3),
                0 8px 32px rgba(0, 0, 0, 0.25);
        }

        .search-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 1.75rem;
            border-bottom: 1px solid var(--border-subtle);
        }

        .search-header img {
            height: 32px;
            width: auto;
            opacity: 0.85;
            transition: opacity 0.2s;
        }

        .search-header img:hover { opacity: 1; }

        .search-header-label {
            font-size: 0.675rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-tertiary);
        }

        .search-body {
            padding: 1.35rem 1.75rem 1.5rem;
        }

        .search-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 0.65rem;
            letter-spacing: 0.01em;
        }

        .search-row {
            display: flex;
            gap: 0.65rem;
            align-items: stretch;
        }

        .search-input {
            flex: 1;
            background: var(--bg-deep);
            border: 1.5px solid var(--border-medium);
            border-radius: 10px;
            padding: 0.7rem 1rem;
            color: var(--text-primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.05rem;
            letter-spacing: 1px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .search-input::placeholder {
            color: var(--text-tertiary);
            opacity: 0.6;
        }

        .search-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-soft);
        }

        .btn-search {
            background: var(--accent);
            color: var(--bg-deep);
            border: none;
            border-radius: 10px;
            padding: 0.7rem 1.35rem;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-search:hover {
            background: #0cd96f;
            box-shadow: 0 4px 16px rgba(14, 229, 127, 0.3);
        }

        .btn-search:active { transform: scale(0.97); }
        .btn-search:disabled { opacity: 0.4; cursor: not-allowed; transform: none; box-shadow: none; }
        .btn-search svg { width: 17px; height: 17px; flex-shrink: 0; stroke-width: 2.5; }

        /* Error */
        .error-bar {
            background: var(--danger-soft);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 8px;
            padding: 0.6rem 0.85rem;
            margin-top: 0.65rem;
            color: #fca5a5;
            font-size: 0.82rem;
            display: none;
            align-items: center;
            gap: 0.5rem;
        }

        .error-bar.visible { display: flex; }
        .error-bar svg { flex-shrink: 0; opacity: 0.8; }

        /* Skeleton */
        .skeleton-wrap {
            margin-top: 1.25rem;
            display: none;
        }

        .skeleton-wrap.visible { display: block; }

        .skel-card {
            background: rgba(22, 26, 46, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            overflow: hidden;
        }

        .skel {
            background: linear-gradient(90deg, var(--bg-elevated) 25%, rgba(40, 45, 70, 0.5) 50%, var(--bg-elevated) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.8s ease-in-out infinite;
            border-radius: 6px;
        }

        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Results */
        .results-panel {
            margin-top: 1.25rem;
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.45s ease, transform 0.45s ease;
            display: none;
        }

        .results-panel.visible {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }

        .result-card {
            background: rgba(22, 26, 46, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-subtle);
            border-radius: 14px;
            overflow: hidden;
            box-shadow:
                0 1px 3px rgba(0, 0, 0, 0.3),
                0 8px 32px rgba(0, 0, 0, 0.25);
        }

        /* Accent top stripe */
        .result-card::before {
            content: '';
            display: block;
            height: 2px;
            background: linear-gradient(90deg, var(--accent), rgba(14, 229, 127, 0.2));
        }

        /* Hero */
        .hero {
            padding: 1.35rem 1.5rem 1.15rem;
            background: linear-gradient(180deg, var(--accent-soft), transparent 70%);
        }

        .hero-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 0.65rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.25rem 0.65rem;
            border-radius: 999px;
            font-size: 0.675rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-active {
            background: rgba(14, 229, 127, 0.12);
            color: var(--accent);
            box-shadow: inset 0 0 0 1px rgba(14, 229, 127, 0.2);
        }

        .badge-inactive {
            background: var(--danger-soft);
            color: #f87171;
            box-shadow: inset 0 0 0 1px rgba(239, 68, 68, 0.2);
        }

        .badge-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: currentColor;
        }

        .cnpj-copy {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            color: var(--text-tertiary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            cursor: pointer;
            padding: 0.25rem 0.55rem;
            border-radius: 6px;
            border: 1px solid transparent;
            background: transparent;
            transition: all 0.15s ease;
        }

        .cnpj-copy:hover {
            color: var(--text-primary);
            background: var(--accent-soft);
            border-color: rgba(14, 229, 127, 0.15);
        }

        .cnpj-copy svg { width: 13px; height: 13px; opacity: 0.6; }
        .cnpj-copy:hover svg { opacity: 1; }

        .company-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.35;
            letter-spacing: -0.01em;
        }

        .company-fantasy {
            font-size: 0.82rem;
            color: var(--text-tertiary);
            margin-top: 0.2rem;
            font-weight: 400;
        }

        /* Section divider */
        .divider { border-top: 1px solid var(--border-subtle); }

        /* Metrics row */
        .metrics-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }

        .metric-cell {
            padding: 0.9rem 1.15rem;
            position: relative;
            transition: background 0.2s ease;
        }

        .metric-cell:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 20%;
            height: 60%;
            width: 1px;
            background: var(--border-subtle);
        }

        .metric-cell:hover { background: var(--accent-soft); }

        .lbl {
            font-size: 0.625rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-tertiary);
            margin-bottom: 0.3rem;
        }

        .val {
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--text-primary);
            line-height: 1.4;
        }

        /* Two-column layout */
        .cols-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .col-block {
            padding: 1.1rem 1.35rem;
            position: relative;
            transition: background 0.2s ease;
        }

        .col-block:first-child::after {
            content: '';
            position: absolute;
            right: 0;
            top: 15%;
            height: 70%;
            width: 1px;
            background: var(--border-subtle);
        }

        .col-block:hover { background: var(--accent-soft); }

        .col-header {
            font-size: 0.625rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-tertiary);
            margin-bottom: 0.6rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .col-header svg { width: 13px; height: 13px; color: var(--accent); opacity: 0.6; }

        .col-text {
            font-size: 0.835rem;
            color: var(--text-secondary);
            line-height: 1.7;
        }

        .col-text a {
            color: var(--text-secondary);
            text-decoration: none;
            border-bottom: 1px dashed rgba(148, 163, 184, 0.3);
            transition: all 0.15s;
        }

        .col-text a:hover {
            color: var(--accent);
            border-bottom-color: var(--accent);
        }

        /* Extras row (same structure as metrics) */
        .extras-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            display: none;
        }

        .extras-row.visible { display: grid; }

        .extra-cell {
            padding: 0.8rem 1.15rem;
            position: relative;
            transition: background 0.2s ease;
        }

        .extra-cell:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 20%;
            height: 60%;
            width: 1px;
            background: var(--border-subtle);
        }

        .extra-cell:hover { background: var(--accent-soft); }

        /* Socios */
        .socios-block {
            padding: 1.1rem 1.35rem;
            display: none;
        }

        .socios-block.visible { display: block; }

        .socios-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.1rem;
        }

        .chip {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: var(--bg-deep);
            border: 1px solid var(--border-medium);
            border-radius: 10px;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s ease;
        }

        .chip:hover {
            border-color: var(--accent-glow);
            background: var(--accent-soft);
        }

        .chip-avatar {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: var(--accent-medium);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            flex-shrink: 0;
            letter-spacing: -0.02em;
        }

        .chip-info { min-width: 0; }

        .chip-name {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .chip-detail {
            font-size: 0.675rem;
            color: var(--text-tertiary);
            margin-top: 0.05rem;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .container { padding: 2rem 0.85rem 2rem; }
            .search-header { padding: 1rem 1.25rem; }
            .search-header img { height: 28px; }
            .search-body { padding: 1.1rem 1.25rem 1.25rem; }

            .metrics-row,
            .extras-row {
                grid-template-columns: 1fr 1fr;
            }

            .metrics-row .metric-cell:nth-child(2)::after,
            .extras-row .extra-cell:nth-child(2)::after { display: none; }

            .metrics-row .metric-cell:nth-child(1),
            .metrics-row .metric-cell:nth-child(2),
            .extras-row .extra-cell:nth-child(1),
            .extras-row .extra-cell:nth-child(2) {
                border-bottom: 1px solid var(--border-subtle);
            }

            .cols-2 { grid-template-columns: 1fr; }

            .col-block:first-child::after { display: none; }
            .col-block:first-child { border-bottom: 1px solid var(--border-subtle); }

            .company-name { font-size: 1.05rem; }
            .btn-search span.btn-label { display: none; }
            .btn-search { padding: 0.7rem 0.85rem; }

            .chip-name { max-width: 140px; }
        }

        /* Copied toast */
        .toast {
            position: fixed;
            bottom: 1.5rem;
            left: 50%;
            transform: translateX(-50%) translateY(16px);
            background: var(--bg-surface);
            color: var(--accent);
            border: 1px solid var(--accent-glow);
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.45rem 0.9rem;
            border-radius: 8px;
            opacity: 0;
            transition: opacity 0.25s ease, transform 0.25s ease;
            pointer-events: none;
            z-index: 50;
            box-shadow: 0 4px 20px rgba(14, 229, 127, 0.15);
        }

        .toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    </style>
</head>
<body>
    <canvas id="bg-canvas"></canvas>
    <div class="container">
        <!-- Search -->
        <div class="search-section">
            <div class="search-header">
                <img src="{{ asset('img/logo.png') }}" alt="ti.remoto">
                <span class="search-header-label">Consulta CNPJ</span>
            </div>
            <div class="search-body">
            <label for="cnpj" class="search-label">Digite o CNPJ da empresa</label>
            <form id="cnpj-search-form" class="search-row">
                <input
                    type="text"
                    id="cnpj"
                    name="cnpj"
                    placeholder="00.000.000/0000-00"
                    class="search-input"
                    maxlength="18"
                    autocomplete="off"
                    inputmode="numeric"
                >
                <button type="submit" id="btn-search" class="btn-search">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <span class="btn-label">Buscar</span>
                </button>
            </form>
            <div id="error-bar" class="error-bar">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                <span id="error-message"></span>
            </div>
            </div><!-- /.search-body -->
        </div>

        <!-- Skeleton -->
        <div id="skeleton" class="skeleton-wrap">
            <div class="skel-card">
                <div style="height:2px;background:var(--bg-elevated);"></div>
                <div style="padding:1.35rem 1.5rem 1.15rem;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:0.65rem;">
                        <div class="skel" style="width:72px;height:20px;"></div>
                        <div class="skel" style="width:140px;height:18px;"></div>
                    </div>
                    <div class="skel" style="width:65%;height:22px;margin-bottom:0.4rem;"></div>
                    <div class="skel" style="width:40%;height:14px;"></div>
                </div>
                <div style="border-top:1px solid var(--border-subtle);display:grid;grid-template-columns:repeat(4,1fr);">
                    <div style="padding:0.9rem 1.15rem;"><div class="skel" style="width:45px;height:10px;margin-bottom:0.35rem;"></div><div class="skel" style="width:75%;height:14px;"></div></div>
                    <div style="padding:0.9rem 1.15rem;"><div class="skel" style="width:45px;height:10px;margin-bottom:0.35rem;"></div><div class="skel" style="width:85%;height:14px;"></div></div>
                    <div style="padding:0.9rem 1.15rem;"><div class="skel" style="width:45px;height:10px;margin-bottom:0.35rem;"></div><div class="skel" style="width:50%;height:14px;"></div></div>
                    <div style="padding:0.9rem 1.15rem;"><div class="skel" style="width:45px;height:10px;margin-bottom:0.35rem;"></div><div class="skel" style="width:70%;height:14px;"></div></div>
                </div>
                <div style="border-top:1px solid var(--border-subtle);display:grid;grid-template-columns:1fr 1fr;gap:0;">
                    <div style="padding:1.1rem 1.35rem;">
                        <div class="skel" style="width:56px;height:10px;margin-bottom:0.5rem;"></div>
                        <div class="skel" style="width:100%;height:13px;margin-bottom:0.35rem;"></div>
                        <div class="skel" style="width:75%;height:13px;margin-bottom:0.35rem;"></div>
                        <div class="skel" style="width:45%;height:13px;"></div>
                    </div>
                    <div style="padding:1.1rem 1.35rem;">
                        <div class="skel" style="width:50px;height:10px;margin-bottom:0.5rem;"></div>
                        <div class="skel" style="width:85%;height:13px;margin-bottom:0.35rem;"></div>
                        <div class="skel" style="width:65%;height:13px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div id="results-panel" class="results-panel">
            <div class="result-card">
                <!-- Hero -->
                <div class="hero">
                    <div class="hero-row">
                        <span id="status-badge" class="badge badge-active">
                            <span class="badge-dot"></span>
                            <span id="status-text">Ativa</span>
                        </span>
                        <button id="cnpj-copy-btn" class="cnpj-copy" type="button" title="Copiar CNPJ">
                            <span id="cnpj-display" class="mono">00.000.000/0000-00</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184"/>
                            </svg>
                        </button>
                    </div>
                    <div class="company-name" id="razao-social">Razao Social</div>
                    <div class="company-fantasy" id="nome-fantasia">Nome Fantasia</div>
                </div>

                <!-- Metrics -->
                <div class="divider"></div>
                <div class="metrics-row">
                    <div class="metric-cell">
                        <div class="lbl">Abertura</div>
                        <div class="val mono" id="data-abertura">--</div>
                    </div>
                    <div class="metric-cell">
                        <div class="lbl">CNAE Principal</div>
                        <div class="val" id="cnae">--</div>
                    </div>
                    <div class="metric-cell">
                        <div class="lbl">Porte</div>
                        <div class="val" id="porte">--</div>
                    </div>
                    <div class="metric-cell">
                        <div class="lbl">Natureza Jur.</div>
                        <div class="val" id="natureza-juridica">--</div>
                    </div>
                </div>

                <!-- Address + Contact -->
                <div class="divider"></div>
                <div class="cols-2">
                    <div class="col-block">
                        <div class="col-header">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            Endereço
                        </div>
                        <div class="col-text" id="endereco-block">
                            <div id="endereco-linha1">--</div>
                            <div id="endereco-linha2">--</div>
                            <div id="endereco-cep" class="mono" style="color:var(--text-tertiary);">--</div>
                        </div>
                    </div>
                    <div class="col-block">
                        <div class="col-header">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/></svg>
                            Contato
                        </div>
                        <div class="col-text">
                            <div id="telefone-line">--</div>
                            <div id="email-line">--</div>
                        </div>
                    </div>
                </div>

                <!-- Extras -->
                <div class="divider"></div>
                <div id="extras-row" class="extras-row">
                    <div class="extra-cell">
                        <div class="lbl">Capital Social</div>
                        <div class="val mono" id="capital-social">--</div>
                    </div>
                    <div class="extra-cell">
                        <div class="lbl">Inscr. Estadual</div>
                        <div class="val mono" id="inscricao-estadual">--</div>
                    </div>
                    <div class="extra-cell">
                        <div class="lbl">Simples Nacional</div>
                        <div class="val" id="simples-nacional">--</div>
                    </div>
                    <div class="extra-cell">
                        <div class="lbl">MEI</div>
                        <div class="val" id="mei">--</div>
                    </div>
                </div>

                <!-- Socios -->
                <div id="socios-divider" class="divider" style="display:none;"></div>
                <div id="socios-block" class="socios-block">
                    <div class="col-header" style="margin-bottom:0.5rem;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        Quadro Societário
                    </div>
                    <div id="socios-list" class="socios-chips"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div id="toast" class="toast">CNPJ copiado!</div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const cnpjInput = document.getElementById('cnpj');
        const form = document.getElementById('cnpj-search-form');
        const btn = document.getElementById('btn-search');
        const skeleton = document.getElementById('skeleton');
        const panel = document.getElementById('results-panel');
        const errorBar = document.getElementById('error-bar');
        const errorMsg = document.getElementById('error-message');

        // CNPJ mask
        cnpjInput.addEventListener('input', function(e) {
            let v = e.target.value.replace(/\D/g, '').substring(0, 14);
            if (v.length > 12) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d)/, '$1.$2.$3/$4-$5');
            else if (v.length > 8) v = v.replace(/^(\d{2})(\d{3})(\d{3})(\d)/, '$1.$2.$3/$4');
            else if (v.length > 5) v = v.replace(/^(\d{2})(\d{3})(\d)/, '$1.$2.$3');
            else if (v.length > 2) v = v.replace(/^(\d{2})(\d)/, '$1.$2');
            e.target.value = v;
        });

        // Submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const cnpj = cnpjInput.value.replace(/\D/g, '');
            if (cnpj.length !== 14) { showError('Digite um CNPJ com 14 dígitos.'); return; }

            hideError();
            panel.classList.remove('visible');
            setTimeout(() => { panel.style.display = 'none'; }, 50);
            skeleton.classList.add('visible');
            btn.disabled = true;

            fetch(`/api/cnpj/${cnpj}`)
                .then(r => r.json())
                .then(data => {
                    skeleton.classList.remove('visible');
                    if (data.success) {
                        render(data.data, cnpj);
                    } else {
                        showError(data.message || data.error || 'Erro ao consultar CNPJ.');
                    }
                })
                .catch(() => {
                    skeleton.classList.remove('visible');
                    showError('Erro de conexão. Tente novamente.');
                })
                .finally(() => { btn.disabled = false; });
        });

        function showError(msg) { errorMsg.textContent = msg; errorBar.classList.add('visible'); }
        function hideError() { errorBar.classList.remove('visible'); }

        function render(d, raw) {
            // Status
            const status = d.situacao_cadastral || d.situacao || '';
            const active = status.toLowerCase().includes('ativa') && !status.toLowerCase().includes('inativa');
            const badge = document.getElementById('status-badge');
            badge.className = 'badge ' + (active ? 'badge-active' : 'badge-inactive');
            document.getElementById('status-text').textContent = status || 'N/I';

            // CNPJ
            document.getElementById('cnpj-display').textContent = fmtCNPJ(raw);

            // Hero
            document.getElementById('razao-social').textContent = d.razao_social || 'Não informado';
            const fantasyEl = document.getElementById('nome-fantasia');
            fantasyEl.textContent = d.nome_fantasia || '';
            fantasyEl.style.display = d.nome_fantasia ? 'block' : 'none';

            // Metrics
            document.getElementById('data-abertura').textContent = fmtDate(d.abertura || d.data_inicio_atividade) || '--';
            document.getElementById('cnae').textContent = d.cnae_principal?.descricao || '--';
            document.getElementById('porte').textContent = d.porte || '--';
            document.getElementById('natureza-juridica').textContent = d.natureza_juridica || '--';

            // Address
            const line1Parts = [d.logradouro, d.numero].filter(Boolean).join(', ');
            const compl = d.complemento ? ' - ' + d.complemento : '';
            document.getElementById('endereco-linha1').textContent = line1Parts + compl || '--';
            document.getElementById('endereco-linha2').textContent = [d.bairro, [d.municipio, d.uf].filter(Boolean).join('/')].filter(Boolean).join(', ') || '--';
            document.getElementById('endereco-cep').textContent = fmtCEP(d.cep) || '--';

            // Contact
            const telEl = document.getElementById('telefone-line');
            telEl.innerHTML = d.telefone ? '<span class="mono">' + esc(d.telefone) + '</span>' : '<span style="color:var(--text-tertiary)">Não informado</span>';

            const emailEl = document.getElementById('email-line');
            emailEl.innerHTML = d.email
                ? '<a href="mailto:' + esc(d.email) + '">' + esc(d.email.toLowerCase()) + '</a>'
                : '<span style="color:var(--text-tertiary)">Não informado</span>';

            // Extras
            const hasExtras = d.capital_social || d.inscricao_estadual || d.simples;
            const extrasRow = document.getElementById('extras-row');
            const extrasDivider = extrasRow.previousElementSibling;

            if (hasExtras) {
                extrasRow.classList.add('visible');
                extrasDivider.style.display = '';
                document.getElementById('capital-social').textContent = d.capital_social ? fmtBRL(d.capital_social) : '--';
                document.getElementById('inscricao-estadual').textContent = d.inscricao_estadual || '--';

                if (d.simples) {
                    document.getElementById('simples-nacional').textContent = (d.simples.simples === 'Sim' || d.simples.optante_simples === true || d.simples.simples_optante === true) ? 'Sim' : 'Não';
                    document.getElementById('mei').textContent = (d.simples.mei === 'Sim' || d.simples.optante_mei === true || d.simples.mei_optante === true) ? 'Sim' : 'Não';
                } else {
                    document.getElementById('simples-nacional').textContent = '--';
                    document.getElementById('mei').textContent = '--';
                }
            } else {
                extrasRow.classList.remove('visible');
                extrasDivider.style.display = 'none';
            }

            // Socios
            const sociosBlock = document.getElementById('socios-block');
            const sociosDivider = document.getElementById('socios-divider');
            const sociosList = document.getElementById('socios-list');
            sociosList.innerHTML = '';

            if (d.socios && d.socios.length > 0) {
                sociosBlock.classList.add('visible');
                sociosDivider.style.display = '';
                d.socios.forEach(s => {
                    const name = s.nome || 'N/I';
                    const initials = name.split(' ').filter(w => w.length > 1).slice(0, 2).map(w => w[0]).join('').toUpperCase();
                    const qual = s.qualificacao_socio?.descricao || (typeof s.qualificacao_socio === 'string' ? s.qualificacao_socio : '');
                    const dt = s.data_entrada ? fmtDate(s.data_entrada) : '';
                    const meta = [qual, dt].filter(Boolean).join(' · ');

                    const chip = document.createElement('div');
                    chip.className = 'chip';
                    chip.innerHTML =
                        '<div class="chip-avatar">' + esc(initials || '?') + '</div>' +
                        '<div class="chip-info">' +
                            '<div class="chip-name">' + esc(name) + '</div>' +
                            (meta ? '<div class="chip-detail">' + esc(meta) + '</div>' : '') +
                        '</div>';
                    sociosList.appendChild(chip);
                });
            } else {
                sociosBlock.classList.remove('visible');
                sociosDivider.style.display = 'none';
            }

            // Show
            panel.style.display = 'block';
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    panel.classList.add('visible');
                    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });
        }

        // Copy
        document.getElementById('cnpj-copy-btn').addEventListener('click', function() {
            const text = document.getElementById('cnpj-display').textContent;
            navigator.clipboard.writeText(text).then(() => {
                const t = document.getElementById('toast');
                t.classList.add('show');
                setTimeout(() => t.classList.remove('show'), 1500);
            });
        });

        // Helpers
        function fmtCNPJ(c) {
            return c.length === 14 ? c.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5') : c;
        }

        function fmtDate(s) {
            if (!s) return '';
            if (s.includes('/')) return s;
            const d = new Date(s + 'T00:00:00');
            return isNaN(d) ? s : d.toLocaleDateString('pt-BR');
        }

        function fmtCEP(c) {
            if (!c) return '';
            c = c.replace(/\D/g, '');
            return c.length === 8 ? c.replace(/^(\d{5})(\d{3})$/, '$1-$2') : c;
        }

        function fmtBRL(v) {
            const n = typeof v === 'string' ? parseFloat(v) : v;
            return isNaN(n) ? v : new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(n);
        }

        function esc(s) {
            const el = document.createElement('span');
            el.textContent = s;
            return el.innerHTML;
        }
    });
    </script>

    <!-- Particle network background -->
    <script>
    (function() {
        const canvas = document.getElementById('bg-canvas');
        const ctx = canvas.getContext('2d');
        let w, h, dpr;

        const isMobile = window.innerWidth < 640;
        const COUNT = isMobile ? 55 : 100;
        const LINK_DIST = isMobile ? 140 : 180;
        const LINE_WIDTH = isMobile ? 1 : 1.5;
        const MOUSE_RADIUS = 200;
        const BASE_SPEED = 0.25;

        const mouse = { x: -9999, y: -9999, smooth: { x: -9999, y: -9999 } };

        function resize() {
            dpr = Math.min(window.devicePixelRatio, 2);
            w = window.innerWidth;
            h = window.innerHeight;
            canvas.width = w * dpr;
            canvas.height = h * dpr;
            canvas.style.width = w + 'px';
            canvas.style.height = h + 'px';
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        }

        resize();

        // Each particle has: base position, orbit params, current position
        const nodes = [];
        for (let i = 0; i < COUNT; i++) {
            const x = Math.random() * w;
            const y = Math.random() * h;
            nodes.push({
                x: x, y: y,
                // Orbit-based smooth movement
                baseX: x, baseY: y,
                driftVx: (Math.random() - 0.5) * BASE_SPEED,
                driftVy: (Math.random() - 0.5) * BASE_SPEED,
                // Sine wave orbit for organic feel
                orbitRadius: 20 + Math.random() * 40,
                orbitSpeed: 0.003 + Math.random() * 0.006,
                orbitPhase: Math.random() * Math.PI * 2,
                orbitPhaseY: Math.random() * Math.PI * 2,
                // Visual
                radius: 1.5 + Math.random() * 1.5,
                opacity: 0.3 + Math.random() * 0.5,
                // Mouse displacement
                displaceX: 0,
                displaceY: 0,
            });
        }

        document.addEventListener('mousemove', function(e) {
            mouse.x = e.clientX;
            mouse.y = e.clientY;
        });

        document.addEventListener('mouseleave', function() {
            mouse.x = -9999;
            mouse.y = -9999;
        });

        // Touch support
        document.addEventListener('touchmove', function(e) {
            if (e.touches.length > 0) {
                mouse.x = e.touches[0].clientX;
                mouse.y = e.touches[0].clientY;
            }
        }, { passive: true });

        document.addEventListener('touchend', function() {
            mouse.x = -9999;
            mouse.y = -9999;
        });

        let time = 0;

        function animate() {
            requestAnimationFrame(animate);
            time++;

            ctx.clearRect(0, 0, w, h);

            // Smooth mouse
            mouse.smooth.x += (mouse.x - mouse.smooth.x) * 0.08;
            mouse.smooth.y += (mouse.y - mouse.smooth.y) * 0.08;

            // Update node positions
            for (let i = 0; i < COUNT; i++) {
                const n = nodes[i];

                // Drift base position
                n.baseX += n.driftVx;
                n.baseY += n.driftVy;

                // Wrap around edges with margin
                const margin = 60;
                if (n.baseX < -margin) n.baseX = w + margin;
                if (n.baseX > w + margin) n.baseX = -margin;
                if (n.baseY < -margin) n.baseY = h + margin;
                if (n.baseY > h + margin) n.baseY = -margin;

                // Sine orbit (smooth organic motion)
                const ox = Math.sin(time * n.orbitSpeed + n.orbitPhase) * n.orbitRadius;
                const oy = Math.cos(time * n.orbitSpeed * 0.7 + n.orbitPhaseY) * n.orbitRadius * 0.6;

                // Mouse interaction — gentle push
                const dmx = n.x - mouse.smooth.x;
                const dmy = n.y - mouse.smooth.y;
                const mDist = Math.sqrt(dmx * dmx + dmy * dmy);

                if (mDist < MOUSE_RADIUS && mDist > 1) {
                    const strength = (1 - mDist / MOUSE_RADIUS);
                    const push = strength * strength * 50;
                    n.displaceX += (dmx / mDist) * push * 0.06;
                    n.displaceY += (dmy / mDist) * push * 0.06;
                }

                // Ease displacement back to 0
                n.displaceX *= 0.92;
                n.displaceY *= 0.92;

                // Final position
                n.x = n.baseX + ox + n.displaceX;
                n.y = n.baseY + oy + n.displaceY;
            }

            // Draw lines
            for (let i = 0; i < COUNT; i++) {
                const a = nodes[i];
                for (let j = i + 1; j < COUNT; j++) {
                    const b = nodes[j];
                    const dx = a.x - b.x;
                    const dy = a.y - b.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < LINK_DIST) {
                        const alpha = (1 - dist / LINK_DIST);
                        // Quadratic falloff for smoother fade
                        const opacity = alpha * alpha * 0.35;

                        ctx.beginPath();
                        ctx.moveTo(a.x, a.y);
                        ctx.lineTo(b.x, b.y);
                        ctx.strokeStyle = 'rgba(14, 229, 127, ' + opacity + ')';
                        ctx.lineWidth = LINE_WIDTH * alpha + 0.5;
                        ctx.stroke();
                    }
                }
            }

            // Draw nodes (particles)
            for (let i = 0; i < COUNT; i++) {
                const n = nodes[i];

                // Glow
                ctx.beginPath();
                ctx.arc(n.x, n.y, n.radius + 3, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(14, 229, 127, ' + (n.opacity * 0.08) + ')';
                ctx.fill();

                // Core
                ctx.beginPath();
                ctx.arc(n.x, n.y, n.radius, 0, Math.PI * 2);
                ctx.fillStyle = 'rgba(14, 229, 127, ' + n.opacity + ')';
                ctx.fill();
            }
        }

        animate();

        window.addEventListener('resize', function() {
            resize();
            // Redistribute nodes that are out of bounds
            for (let i = 0; i < COUNT; i++) {
                if (nodes[i].baseX > w) nodes[i].baseX = Math.random() * w;
                if (nodes[i].baseY > h) nodes[i].baseY = Math.random() * h;
            }
        });
    })();
    </script>
</body>
</html>
