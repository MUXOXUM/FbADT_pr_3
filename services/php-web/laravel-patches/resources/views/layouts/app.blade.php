<!doctype html>
<html lang="ru" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Space - Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <style>
    :root {
      --md-surface: #121212;
      --md-surface-variant: #1e1e1e;
      --md-primary: #bb86fc;
      --md-primary-variant: #3700b3;
      --md-secondary: #03dac6;
      --md-on-surface: #ffffff;
      --md-on-surface-variant: #b0b0b0;
      --md-outline: #3a3a3a;
      --md-elevation-1: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
      --md-elevation-2: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
      --md-elevation-3: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
      --md-radius: 12px;
      --md-radius-small: 8px;
    }

    body {
      background: var(--md-surface);
      color: var(--md-on-surface-variant);
      min-height: 100vh;
      font-family: 'Roboto', 'Segoe UI', system-ui, sans-serif;
    }

    /* Анимации */
    @keyframes fadeUpSmooth {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .fade-in {
      animation: fadeUpSmooth 0.8s ease-out both;
    }

    .fade-in-delay-1 { animation-delay: 0.15s; }
    .fade-in-delay-2 { animation-delay: 0.3s; }
    .fade-in-delay-3 { animation-delay: 0.45s; }
    .fade-in-delay-4 { animation-delay: 0.6s; }

    /* Навигация */
    .navbar {
      background: var(--md-surface) !important;
      border-bottom: 1px solid var(--md-outline);
      padding: 0.75rem 0;
    }

    .navbar-brand {
      font-weight: 500;
      font-size: 1.25rem;
      color: var(--md-on-surface) !important;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .navbar-brand::before {
      font-size: 1.5rem;
    }

    .nav-link {
      color: var(--md-on-surface-variant) !important;
      font-weight: 500;
      padding: 0.75rem 1rem !important;
      border-radius: var(--md-radius-small);
      margin: 0 0.125rem;
      position: relative;
      overflow: hidden;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-link::before {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      width: 0;
      height: 3px;
      background: var(--md-primary);
      border-radius: 3px 3px 0 0;
      transform: translateX(-50%);
      transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-link:hover {
      color: var(--md-on-surface) !important;
      background: rgba(255, 255, 255, 0.05);
      transform: translateY(-1px);
    }

    .nav-link:hover::before {
      width: 80%;
    }

    .nav-link.active {
      color: var(--md-primary) !important;
      background: rgba(187, 134, 252, 0.08);
    }

    .nav-link.active::before {
      width: 100%;
    }

    /* Карточки */
    .card {
      background: var(--md-surface-variant);
      border: 1px solid var(--md-outline);
      border-radius: var(--md-radius);
      box-shadow: var(--md-elevation-1);
      transition: box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card:hover {
      box-shadow: var(--md-elevation-2);
    }

    /* Кнопки */
    .btn-primary {
      background: var(--md-primary);
      color: #000000;
      border: none;
      border-radius: 20px;
      padding: 0.625rem 1.5rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      box-shadow: var(--md-elevation-1);
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-primary:hover {
      background: var(--md-primary);
      box-shadow: var(--md-elevation-2);
      transform: translateY(-1px);
    }

    .btn-primary:active {
      transform: translateY(0);
      box-shadow: var(--md-elevation-1);
    }

    /* Формы */
    .form-control {
      background: var(--md-surface-variant);
      border: 1px solid var(--md-outline);
      color: var(--md-on-surface);
      border-radius: var(--md-radius-small);
      padding: 0.75rem 1rem;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .form-control:focus {
      background: var(--md-surface-variant);
      border-color: var(--md-primary);
      color: var(--md-on-surface);
      box-shadow: 0 0 0 2px rgba(187, 134, 252, 0.15);
      outline: none;
    }

    /* Таблицы */
    .table {
      color: var(--md-on-surface);
      border-color: var(--md-outline);
    }

    .table thead th {
      background: var(--md-surface-variant);
      border-bottom: 2px solid var(--md-outline);
      color: var(--md-on-surface);
      font-weight: 500;
      padding: 1rem;
    }

    .table tbody tr {
      border-bottom: 1px solid var(--md-outline);
      transition: background-color 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .table tbody tr:hover {
      background: rgba(255, 255, 255, 0.04);
    }

    .table tbody td {
      padding: 1rem;
      vertical-align: middle;
    }

    /* Сортировка таблиц */
    th[data-sort]::after { content: '⇅'; opacity: 0.4; margin-left: 0.4em; }
    th[data-dir="asc"]::after { content: '↑'; opacity: 1; }
    th[data-dir="desc"]::after { content: '↓'; opacity: 1; }

    /* Заголовки */
    h1, h2, h3, h4, h5, h6 {
      color: var(--md-on-surface);
      font-weight: 500;
    }

    /* Цвета текста */
    .text-primary {
      color: var(--md-primary) !important;
    }

    .text-secondary {
      color: var(--md-secondary) !important;
    }

    .cursor-pointer {
      cursor: pointer;
    }

    /* Details */
    details {
      background: var(--md-surface-variant);
      border: 1px solid var(--md-outline);
      border-radius: var(--md-radius);
      padding: 1rem;
      margin-top: 1rem;
    }

    summary {
      color: var(--md-primary);
      font-weight: 500;
      list-style: none;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    summary::before {
      content: '▶';
      font-size: 0.75rem;
      transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    details[open] summary::before {
      transform: rotate(90deg);
    }

    /* Pre/Code */
    pre, code {
      background: var(--md-surface);
      border: 1px solid var(--md-outline);
      border-radius: var(--md-radius-small);
      padding: 1rem;
      margin: 0;
      overflow-x: auto;
      font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    }

    /* Карта Leaflet */
    #map {
      height: 340px;
      border-radius: var(--md-radius);
      border: 1px solid var(--md-outline);
      overflow: hidden;
      z-index: 1;
    }

    .leaflet-container {
      background: var(--md-surface) !important;
    }

    .leaflet-control {
      background: var(--md-surface-variant) !important;
      border: 1px solid var(--md-outline) !important;
      color: var(--md-on-surface) !important;
    }

    .leaflet-popup-content {
      background: var(--md-surface-variant) !important;
      color: var(--md-on-surface) !important;
    }

    /* Графики Chart.js */
    .chart-container {
      position: relative;
      height: 800px;
      background: var(--md-surface-variant);
      border-radius: var(--md-radius);
      border: 1px solid var(--md-outline);
      padding: 1rem;
    }
  
    /* Новые стили для предотвращения выхода текста за границы */
    .table-responsive {
      overflow-x: auto;
    }

    /* Улучшенное обрезание текста */
    .text-truncate-fix {
      display: inline-block;
      max-width: 100%;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      vertical-align: middle;
    }

    canvas {
      border-radius: var(--md-radius-small);
    }

    /* Спиннер */
    .spinner-border {
      border-color: var(--md-primary) transparent var(--md-primary) transparent;
    }

    /* Бэйджи */
    .badge {
      font-weight: 500;
      letter-spacing: 0.3px;
    }
  </style>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <nav class="navbar navbar-expand-lg mb-4">
    <div class="container">
      <span class="navbar-brand">Space Dashboard</span>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <div class="navbar-nav ms-auto gap-1">
          <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="/dashboard">
            <i class="bi bi-speedometer2 me-1"></i> Dashboard
          </a>
          <a class="nav-link {{ request()->is('astro') ? 'active' : '' }}" href="/astro">
            <i class="bi bi-stars me-1"></i> AstronomyAPI
          </a>
          <a class="nav-link {{ request()->is('iss') ? 'active' : '' }}" href="/iss">
            <i class="bi bi-database me-1"></i> ISS
          </a>
          <a class="nav-link {{ request()->is('osdr') ? 'active' : '' }}" href="/osdr">
            <i class="bi bi-database me-1"></i> OSDR
          </a>
          <a class="nav-link {{ request()->is('telemetry') ? 'active' : '' }}" href="/telemetry">
            <i class="bi bi-graph-up me-1"></i> Telemetry
          </a>
          <a class="nav-link {{ request()->is('cms') ? 'active' : '' }}" href="/cms">
            <i class="bi bi-gear me-1"></i> CMS
          </a>
        </div>
      </div>
    </div>
  </nav>

  <main class="container pb-5">
    @yield('content')
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>