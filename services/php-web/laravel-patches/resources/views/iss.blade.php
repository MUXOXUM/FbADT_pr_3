@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center gap-3 mb-4">
    <div>
      <h1 class="mb-1">Международная космическая станция</h1>
      <p class="text-on-surface-variant mb-0">Данные в реальном времени</p>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-surface-variant border-bottom border-outline py-3">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-camera fs-4 text-primary"></i>
            <h4 class="mb-0 text-on-surface">Последний снимок</h4>
          </div>
        </div>
        <div class="card-body">
          @if(!empty($last['payload']))
            <div class="list-group list-group-flush">
              <div class="list-group-item bg-transparent border-outline d-flex justify-content-between align-items-center">
                <span class="d-flex align-items-center gap-2">
                  <i class="bi bi-globe text-secondary"></i>
                  <span>Широта</span>
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                  {{ $last['payload']['latitude'] ?? '—' }}
                </span>
              </div>
              <div class="list-group-item bg-transparent border-outline d-flex justify-content-between align-items-center">
                <span class="d-flex align-items-center gap-2">
                  <i class="bi bi-globe text-secondary"></i>
                  <span>Долгота</span>
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                  {{ $last['payload']['longitude'] ?? '—' }}
                </span>
              </div>
              <div class="list-group-item bg-transparent border-outline d-flex justify-content-between align-items-center">
                <span class="d-flex align-items-center gap-2">
                  <i class="bi bi-arrow-up text-secondary"></i>
                  <span>Высота</span>
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                  {{ $last['payload']['altitude'] ?? '—' }} км
                </span>
              </div>
              <div class="list-group-item bg-transparent border-outline d-flex justify-content-between align-items-center">
                <span class="d-flex align-items-center gap-2">
                  <i class="bi bi-speedometer2 text-secondary"></i>
                  <span>Скорость</span>
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                  {{ $last['payload']['velocity'] ?? '—' }} км/ч
                </span>
              </div>
              <div class="list-group-item bg-transparent border-outline d-flex justify-content-between align-items-center">
                <span class="d-flex align-items-center gap-2">
                  <i class="bi bi-clock text-secondary"></i>
                  <span>Время получения</span>
                </span>
                <span class="text-on-surface">{{ $last['fetched_at'] ?? '—' }}</span>
              </div>
            </div>
          @else
            <div class="d-flex flex-column align-items-center py-5">
              <i class="bi bi-wifi-off fs-1 text-on-surface-variant mb-3"></i>
              <p class="text-on-surface-variant mb-0">Нет данных для отображения</p>
            </div>
          @endif
          <div class="mt-4">
            <code class="bg-surface rounded-2 px-3 py-2 d-inline-block">
              {{ $base }}/last
            </code>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-surface-variant border-bottom border-outline py-3">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-graph-up-arrow fs-4 text-primary"></i>
            <h4 class="mb-0 text-on-surface">Тренд движения</h4>
          </div>
        </div>
        <div class="card-body">
          @if(!empty($trend))
            <div class="list-group list-group-flush">
              <div class="list-group-item bg-transparent border-outline d-flex justify-content-between align-items-center">
                <span class="d-flex align-items-center gap-2">
                  <i class="bi bi-activity text-secondary"></i>
                  <span>Движение</span>
                </span>
                <span class="badge {{ ($trend['movement'] ?? false) ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill px-3 py-1">
                  {{ ($trend['movement'] ?? false) ? 'Активно' : 'Нет' }}
                </span>
              </div>
              <div class="list-group-item bg-transparent border-outline d-flex justify-content-between align-items-center">
                <span class="d-flex align-items-center gap-2">
                  <i class="bi bi-arrows-expand text-secondary"></i>
                  <span>Смещение</span>
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                  {{ number_format($trend['delta_km'] ?? 0, 3, '.', ' ') }} км
                </span>
              </div>
              <div class="list-group-item bg-transparent border-outline d-flex justify-content-between align-items-center">
                <span class="d-flex align-items-center gap-2">
                  <i class="bi bi-stopwatch text-secondary"></i>
                  <span>Интервал</span>
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                  {{ $trend['dt_sec'] ?? 0 }} сек
                </span>
              </div>
              <div class="list-group-item bg-transparent border-outline d-flex justify-content-between align-items-center">
                <span class="d-flex align-items-center gap-2">
                  <i class="bi bi-lightning-charge text-secondary"></i>
                  <span>Скорость</span>
                </span>
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                  {{ $trend['velocity_kmh'] ?? '—' }} км/ч
                </span>
              </div>
            </div>
          @else
            <div class="d-flex flex-column align-items-center py-5">
              <i class="bi bi-graph-down fs-1 text-on-surface-variant mb-3"></i>
              <p class="text-on-surface-variant mb-0">Недостаточно данных для анализа тренда</p>
            </div>
          @endif
          <div class="mt-4">
            <code class="bg-surface rounded-2 px-3 py-2 d-inline-block">
              {{ $base }}/iss/trend
            </code>
          </div>
          <div class="mt-4">
            <a class="btn btn-outline d-flex align-items-center gap-2 px-4" href="/osdr">
              <span>Перейти к OSDR</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection