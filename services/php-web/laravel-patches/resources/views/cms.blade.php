@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center gap-3 mb-5">
    <div>
      <h1 class="mb-1">CMS — Управление контентом</h1>
      <p class="text-on-surface-variant mb-0">Блоки контента для отображения на сайте</p>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header bg-primary bg-opacity-10 border-bottom border-outline py-3">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-door-open fs-4 text-primary"></i>
            <h4 class="mb-0 text-on-surface">Добро пожаловать</h4>
          </div>
        </div>
        <div class="card-body p-4">
          @if($cmsWelcome)
            <div class="cms-content">
              {{ $cmsWelcome }} <!-- {!! $cmsWelcome !!} -->
            </div>
          @else
            <div class="d-flex flex-column align-items-center py-5">
              <i class="bi bi-file-earmark-x fs-1 text-on-surface-variant mb-3"></i>
              <p class="text-on-surface-variant mb-0">Блок контента не найден</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="card">
        <div class="card-header bg-danger bg-opacity-10 border-bottom border-outline py-3">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
            <h4 class="mb-0 text-on-surface">Тестовый блок</h4>
          </div>
        </div>
        <div class="card-body p-4">
          @if($cmsUnsafe)
            <div class="cms-content">
              {{ $cmsUnsafe }} <!-- {!! $cmsUnsafe !!} -->
            </div>
          @else
            <div class="d-flex flex-column align-items-center py-5">
              <i class="bi bi-file-earmark-x fs-1 text-on-surface-variant mb-3"></i>
              <p class="text-on-surface-variant mb-0">Блок контента не найден</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .cms-content {
    line-height: 1.7;
    font-size: 1.1rem;
    color: var(--md-on-surface-variant);
  }
  .cms-content p {
    margin-bottom: 1rem;
  }
  .cms-content h3, .cms-content h4 {
    color: var(--md-primary);
    margin-top: 1.5rem;
    font-weight: 500;
  }
  .cms-content code {
    background: var(--md-surface);
    border: 1px solid var(--md-outline);
    border-radius: var(--md-radius-small);
    padding: 0.2rem 0.4rem;
    font-family: 'Monaco', 'Menlo', monospace;
  }
  .cms-content pre {
    background: var(--md-surface);
    border: 1px solid var(--md-outline);
    border-radius: var(--md-radius-small);
    padding: 1rem;
    overflow-x: auto;
    margin: 1rem 0;
  }
</style>
@endsection