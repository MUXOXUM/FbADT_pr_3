@extends('layouts.app')

@section('content')
<div class="container pb-5">
  <div class="d-flex align-items-center gap-3 mb-5">
    <div>
      <h1 class="mb-1">Астрономические события</h1>
      <p class="text-on-surface-variant mb-0">Данные предоставлены AstronomyAPI</p>
    </div>
  </div>

  <div class="card p-4 mb-4">
    <h5 class="mb-4 text-on-surface">Параметры запроса</h5>
    <form id="astroForm" class="row g-3 align-items-end">
      <div class="col-12 col-sm-6 col-md-4 col-lg-auto">
        <label class="form-label text-on-surface-variant mb-2">Широта</label>
        <input type="number" step="0.0001" class="form-control" name="lat" value="55.7558" placeholder="Введите широту">
      </div>
      <div class="col-12 col-sm-6 col-md-4 col-lg-auto">
        <label class="form-label text-on-surface-variant mb-2">Долгота</label>
        <input type="number" step="0.0001" class="form-control" name="lon" value="37.6176" placeholder="Введите долготу">
      </div>
      <div class="col-12 col-sm-6 col-md-4 col-lg-auto">
        <label class="form-label text-on-surface-variant mb-2">Высота (м)</label>
        <input type="number" class="form-control" name="elevation" value="150" placeholder="Высота">
      </div>
      <div class="col-12 col-sm-6 col-md-4 col-lg-auto">
        <label class="form-label text-on-surface-variant mb-2">Время</label>
        <input type="time" class="form-control" name="time" value="12:00">
      </div>
      <div class="col-12 col-sm-6 col-md-4 col-lg-auto">
        <label class="form-label text-on-surface-variant mb-2">Дни</label>
        <input type="number" min="1" max="365" class="form-control" name="days" value="7">
      </div>
      <div class="col-12 col-md-4 col-lg-auto d-flex align-items-end">
        <button class="btn btn-primary w-100" type="submit">
          <i class="bi bi-search me-2"></i> Показать события
        </button>
      </div>
    </form>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th class="ps-4">№</th>
              <th>Тело</th>
              <th>Событие</th>
              <th>Время (UTC)</th>
              <th class="pe-4">Дополнительно</th>
            </tr>
          </thead>
          <tbody id="astroBody">
            <tr>
              <td colspan="5" class="text-center py-5">
                <div class="d-flex flex-column align-items-center">
                  <i class="bi bi-search fs-1 text-on-surface-variant mb-3"></i>
                  <p class="text-on-surface-variant mb-0">Введите параметры для поиска событий</p>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-4">
    <details>
      <summary class="d-flex align-items-center gap-2">
        <span>Показать JSON-ответ</span>
      </summary>
      <div class="mt-3">
        <pre id="astroRaw" class="p-3 mb-0"></pre>
      </div>
    </details>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('astroForm');
  const body = document.getElementById('astroBody');
  const raw  = document.getElementById('astroRaw');

  function normalize(node){
    const name = node.name || node.body || node.object || node.target || '';
    const type = node.type || node.event_type || node.category || node.kind || '';
    const when = node.time || node.date || node.occursAt || node.peak || node.instant || '';
    const extra = node.magnitude || node.mag || node.altitude || node.note || '';
    return {name, type, when, extra};
  }

  function collect(root){
    const rows = [];
    (function dfs(x){
      if (!x || typeof x !== 'object') return;
      if (Array.isArray(x)) { x.forEach(dfs); return; }
      if ((x.type || x.event_type || x.category) && (x.name || x.body || x.object || x.target)) {
        rows.push(normalize(x));
      }
      Object.values(x).forEach(dfs);
    })(root);
    return rows;
  }

  async function load(q){
    body.innerHTML = `
      <tr>
        <td colspan="5" class="text-center py-5">
          <div class="d-flex flex-column align-items-center">
            <div class="spinner-border mb-3"></div>
            <p class="text-on-surface-variant mb-0">Загрузка данных...</p>
          </div>
        </td>
      </tr>
    `;
    
    const url = '/api/astro/events?' + new URLSearchParams(q).toString();
    try{
      const r  = await fetch(url);
      const js = await r.json();
      raw.textContent = JSON.stringify(js, null, 2);

      const rows = collect(js);
      if (!rows.length) {
        body.innerHTML = `
          <tr>
            <td colspan="5" class="text-center py-5">
              <div class="d-flex flex-column align-items-center">
                <i class="bi bi-inbox fs-1 text-on-surface-variant mb-3"></i>
                <p class="text-on-surface-variant mb-0">События не найдены</p>
              </div>
            </td>
          </tr>
        `;
        return;
      }
      
      body.innerHTML = rows.slice(0,200).map((r,i)=>`
        <tr>
          <td class="ps-4">
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">${i+1}</span>
          </td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <span>${r.name || '—'}</span>
            </div>
          </td>
          <td>${r.type || '—'}</td>
          <td>
            <div class="d-inline-flex align-items-center gap-2 bg-surface rounded-pill px-3 py-1">
              <code class="bg-transparent p-0">${r.when || '—'}</code>
            </div>
          </td>
          <td class="pe-4">
            ${r.extra ? `
              <span class="badge bg-surface text-on-surface border border-outline rounded-pill px-3 py-1">
                ${r.extra}
              </span>
            ` : '—'}
          </td>
        </tr>
      `).join('');
    }catch(e){
      body.innerHTML = `
        <tr>
          <td colspan="5" class="text-center py-5">
            <div class="d-flex flex-column align-items-center">
              <i class="bi bi-exclamation-triangle fs-1 text-danger mb-3"></i>
              <p class="text-danger mb-0">Ошибка загрузки данных</p>
            </div>
          </td>
        </tr>
      `;
    }
  }

  form.addEventListener('submit', ev=>{
    ev.preventDefault();
    load(Object.fromEntries(new FormData(form).entries()));
  });

  load({lat: form.lat.value, lon: form.lon.value, elevation: form.elevation.value, time: form.time.value, days: form.days.value});
});
</script>
@endsection