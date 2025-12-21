@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="card">
    <div class="card-body p-4">
      @if($page)
        <div class="d-flex align-items-center gap-3 mb-4">
          <div>
            <h1 class="mb-2">{{ $page->title }}</h1>
            @if($page->created_at)
              <div class="d-flex align-items-center gap-2 text-on-surface-variant">
                <i class="bi bi-calendar"></i>
                <span>Создано: {{ $page->created_at->format('d.m.Y H:i') }}</span>
                @if($page->updated_at && $page->updated_at != $page->created_at)
                  <i class="bi bi-arrow-repeat ms-3"></i>
                  <span>Обновлено: {{ $page->updated_at->format('d.m.Y H:i') }}</span>
                @endif
              </div>
            @endif
          </div>
        </div>
        
        <div class="page-content bg-surface-variant rounded-3 p-4">
          {{-- Экранируем контент для защиты от XSS --}}
          <div class="content-body">{{ $page->body }}</div>
        </div>
      @else
        <div class="d-flex flex-column align-items-center py-5">
          <i class="bi bi-search fs-1 text-on-surface-variant mb-3"></i>
          <h4 class="text-on-surface mb-2">Страница не найдена</h4>
          <p class="text-on-surface-variant text-center mb-4">
            Запрошенная страница не существует или была удалена.
          </p>
          <a href="/dashboard" class="btn btn-primary d-flex align-items-center gap-2 px-4">
            <i class="bi bi-arrow-left"></i>
            <span>Вернуться на главную</span>
          </a>
        </div>
      @endif
    </div>
  </div>
</div>

<style>
  .page-content {
    line-height: 1.8;
    font-size: 1.1rem;
    color: var(--md-on-surface-variant);
  }
  
  .page-content h2,
  .page-content h3,
  .page-content h4 {
    color: var(--md-on-surface);
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 500;
  }
  
  .page-content p {
    margin-bottom: 1.5rem;
  }
  
  .page-content code {
    background: var(--md-surface);
    border: 1px solid var(--md-outline);
    border-radius: var(--md-radius-small);
    padding: 0.2rem 0.4rem;
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 0.9em;
  }
  
  .page-content pre {
    background: var(--md-surface);
    border: 1px solid var(--md-outline);
    border-radius: var(--md-radius);
    padding: 1.5rem;
    overflow-x: auto;
    margin: 1.5rem 0;
  }
  
  .page-content blockquote {
    border-left: 4px solid var(--md-primary);
    padding-left: 1.5rem;
    margin: 1.5rem 0;
    color: var(--md-on-surface-variant);
    font-style: italic;
  }
  
  .page-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--md-radius);
    margin: 1.5rem 0;
  }
</style>
@endsection