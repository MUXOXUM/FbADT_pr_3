@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="card">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
          <div>
            <h1 class="mb-1">Репозиторий статей NASA</h1>
            <p class="text-on-surface-variant mb-0">Открытые научные данные NASA Open Science Data Repository</p>
          </div>
        </div>
          <code class="ms-1">{{ $src }}</code>
      </div>

      <div class="card bg-surface-variant p-3 mb-4">
        <div class="row g-3 align-items-end">
          <div class="col-auto">
            <label class="form-label text-on-surface-variant mb-2">Поиск по</label>
            <select id="searchCol" class="form-select">
              <option value="1">dataset_id</option>
              <option value="2" selected>Название</option>
              <option value="4">Обновлено</option>
            </select>
          </div>
          <div class="col">
            <label class="form-label text-on-surface-variant mb-2">Запрос</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Введите текст для поиска...">
          </div>
          <div class="col-auto d-flex align-items-end">
            <button type="button" id="clearSearch" class="btn btn-outline btn-sm">
              Сбросить
            </button>
          </div>
        </div>
      </div>

      <div class="table-responsive rounded-3 border border-outline overflow-hidden">
        <table class="table table-hover mb-0" id="dataTable">
          <thead>
            <tr>
              <th data-sort="0" style="cursor:pointer; width:5%" class="ps-4">
                #
              </th>
              <th data-sort="1" style="cursor:pointer" class="text-nowrap">
                dataset_id
              </th>
              <th data-sort="2" style="cursor:pointer">
                Название
              </th>
              <th style="width:10%" class="text-nowrap">
                REST_URL
              </th>
              <th data-sort="4" style="cursor:pointer" class="text-nowrap">
                Обновлено
              </th>
              <th data-sort="5" style="cursor:pointer" class="text-nowrap">
                Добавлено
              </th>
              <th style="width:8%">
                Данные
              </th>
            </tr>
          </thead>
          <tbody>
          @forelse($items as $row)
            <tr>
              <td class="ps-4">
                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                  {{ $row['id'] }}
                </span>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <code class="bg-surface rounded-2 px-2 py-1">{{ $row['dataset_id'] ?? '—' }}</code>
                </div>
              </td>
              <td class="text-truncate" style="max-width:380px;">
                <div class="d-flex align-items-center gap-2">
                  <span title="{{ $row['title'] ?? '—' }}">{{ $row['title'] ?? '—' }}</span>
                </div>
              </td>
              <td>
                @if(!empty($row['rest_url']))
                  <a href="{{ $row['rest_url'] }}" target="_blank" rel="noopener" 
                     class="btn btn-sm btn-outline d-flex align-items-center gap-1 px-3">
                    <i class="bi bi-box-arrow-up-right"></i>
                    <span>Открыть</span>
                  </a>
                @else
                  <span class="badge bg-surface text-on-surface-variant border border-outline rounded-pill px-3 py-1">
                    Нет
                  </span>
                @endif
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span>{{ $row['updated_at'] ?? '—' }}</span>
                </div>
              </td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <span>{{ $row['inserted_at'] ?? '—' }}</span>
                </div>
              </td>
              <td class="pe-4">
                <button class="btn btn-sm btn-outline d-flex align-items-center gap-1 px-3" 
                        data-bs-toggle="collapse" 
                        data-bs-target="#json-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
                  <span>JSON</span>
                </button>
              </td>
            </tr>
            <tr class="collapse bg-surface" id="json-{{ $row['id'] }}-{{ md5($row['dataset_id'] ?? (string)$row['id']) }}">
              <td colspan="7" class="p-3 pe-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-on-surface-variant">
                    <i class="bi bi-data me-1"></i> Необработанные данные
                  </span>
                  <button class="btn btn-sm btn-outline" onclick="copyToClipboard(this, '{{ json_encode($row['raw'] ?? [], JSON_HEX_APOS) }}')">
                    <i class="bi bi-clipboard me-1"></i> Копировать
                  </button>
                </div>
                <pre class="mb-0 small p-3 rounded-3">{{ json_encode($row['raw'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-5">
                <div class="d-flex flex-column align-items-center">
                  <i class="bi bi-database-x fs-1 text-on-surface-variant mb-3"></i>
                  <p class="text-on-surface-variant mb-0">Данные не найдены</p>
                </div>
              </td>
            </tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const table = document.getElementById('dataTable');
  const tbody = table.querySelector('tbody');
  const searchInput = document.getElementById('searchInput');
  const columnSelect = document.getElementById('searchCol');
  const clearSearch = document.getElementById('clearSearch');
  const sortDirections = {};

  // Поиск
  searchInput.addEventListener('input', () => {
    const query = searchInput.value.toLowerCase().trim();
    const colIndex = parseInt(columnSelect.value);
    
    tbody.querySelectorAll('tr:not(.collapse)').forEach(row => {
      const cell = row.cells[colIndex];
      const cellText = cell ? cell.textContent.toLowerCase() : '';
      const matches = cellText.includes(query);
      row.style.display = matches ? '' : 'none';
      
      // Показать/скрыть связанные collapse-строки
      const collapseId = row.querySelector('button[data-bs-target]')?.getAttribute('data-bs-target');
      if (collapseId) {
        const collapseRow = document.querySelector(`tr${collapseId}`);
        if (collapseRow) collapseRow.style.display = matches ? '' : 'none';
      }
    });
  });

  // Сброс поиска
  clearSearch.addEventListener('click', () => {
    searchInput.value = '';
    searchInput.dispatchEvent(new Event('input'));
  });

  // Сортировка
  table.querySelectorAll('th[data-sort]').forEach(header => {
    header.addEventListener('click', () => {
      const col = parseInt(header.dataset.sort);
      const isAscending = sortDirections[col] = !sortDirections[col];

      table.querySelectorAll('th[data-sort]').forEach(h => {
        h.classList.remove('active-sort');
        delete h.dataset.dir;
      });

      header.classList.add('active-sort');
      header.dataset.dir = isAscending ? 'asc' : 'desc';

      const rows = Array.from(tbody.querySelectorAll('tr:not(.collapse)'));

      rows.sort((rowA, rowB) => {
        const valA = rowA.cells[col]?.textContent.trim() || '';
        const valB = rowB.cells[col]?.textContent.trim() || '';
        const comparison = valA.localeCompare(valB, undefined, { numeric: true, sensitivity: 'base' });
        return isAscending ? comparison : -comparison;
      });

      rows.forEach(r => tbody.appendChild(r));
    });
  });
});

function copyToClipboard(button, jsonText) {
  navigator.clipboard.writeText(jsonText).then(() => {
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-check me-1"></i> Скопировано';
    button.classList.add('btn-success');
    setTimeout(() => {
      button.innerHTML = originalText;
      button.classList.remove('btn-success');
    }, 2000);
  });
}
</script>
@endsection