@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="d-flex align-items-center gap-3 mb-5">
    <div>
      <h1 class="mb-1">Телеметрия</h1>
      <p class="text-on-surface-variant mb-0">Мониторинг данных в реальном времени</p>
    </div>
  </div>

  <!-- График -->
  <div class="card mb-5">
    <div class="card-header bg-surface-variant border-bottom border-outline py-3">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-lightning-charge-fill fs-4 text-primary"></i>
        <h4 class="mb-0 text-on-surface">Напряжение и температура</h4>
      </div>
    </div>
    <div class="card-body p-4">
      <div class="chart-container position-relative" style="height: 400px;">
        <canvas id="telemetryChart"></canvas>
      </div>
    </div>
  </div>

  <div class="text-center mb-5">
    <div class="d-flex flex-wrap justify-content-center gap-3">
      <a href="/telemetry/export/csv" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-3">
        <i class="bi bi-file-earmark-arrow-down"></i>
        <div class="d-flex flex-column align-items-start">
          <span class="fw-bold">Скачать CSV</span>
          <small class="opacity-75">Все данные таблицы</small>
        </div>
      </a>
      <a href="/telemetry/export/excel" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-3">
        <i class="bi bi-file-earmark-excel"></i>
        <div class="d-flex flex-column align-items-start">
          <span class="fw-bold">Скачать Excel</span>
          <small class="opacity-75">С форматированием</small>
        </div>
      </a>
    </div>
  </div>
  
  <!-- Таблица -->
  <div class="card">
    <div class="card-header bg-surface-variant border-bottom border-outline py-3">
      <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-table fs-4 text-primary"></i>
          <h4 class="mb-0 text-on-surface">Данные телеметрии</h4>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
          <i class="bi bi-collection me-1"></i> {{ count($records) }} записей
        </span>
      </div>
    </div>
    <div class="card-body p-4">
      <div class="card bg-surface-variant p-3 mb-4">
        <div class="row g-3 align-items-center">
          <div class="col-auto">
            <label class="form-label text-on-surface-variant mb-2">Поиск по</label>
            <select id="searchCol" class="form-select">
              <option value="2">Дата записи</option>
              <option value="3">Напряжение</option>
              <option value="4">Температура</option>
              <option value="7" selected>Исходный файл</option>
            </select>
          </div>
          <div class="col">
            <label class="form-label text-on-surface-variant mb-2">Запрос</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Введите текст для поиска...">
          </div>
        </div>
      </div>

      <div class="table-responsive rounded-3 border border-outline overflow-hidden">
        <table class="table table-hover mb-0" id="dataTable">
          <thead>
            <tr>
              <th class="ps-4">№</th>
              <th data-sort="1" style="cursor:pointer" class="text-nowrap">
                ID телеметрии
              </th>
              <th data-sort="2" style="cursor:pointer" class="text-nowrap">
                Дата записи
              </th>
              <th data-sort="3" style="cursor:pointer">
                Напряжение
              </th>
              <th data-sort="4" style="cursor:pointer">
                Температура
              </th>
              <th data-sort="5" style="cursor:pointer" class="text-nowrap">
                Статус миссии
              </th>
              <th class="text-nowrap">
                Активность
              </th>
              <th data-sort="7" style="cursor:pointer" class="text-nowrap">
                Исходный файл
              </th>
            </tr>
          </thead>
          <tbody>
            @forelse($records as $record)
              <tr>
                <td class="ps-4">
                  <span class="badge bg-surface text-on-surface-variant border border-outline rounded-pill px-3 py-1">
                    {{ $record->id }}
                  </span>
                </td>
                <td>
                  <code class="bg-surface rounded-2 px-2 py-1">{{ $record->telemetry_id ?? '—' }}</code>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <span>{{ $record->recorded_at }}</span>
                  </div>
                </td>
                <td>
                  <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                    {{ $record->voltage }} В
                  </span>
                </td>
                <td>
                  <span class="badge {{ $record->temp > 30 ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' }} rounded-pill px-3 py-1">
                    {{ $record->temp }} °C
                  </span>
                </td>
                <td>
                  <span class="badge bg-surface text-on-surface-variant border border-outline rounded-pill px-3 py-1">
                    {{ $record->mission_status ?? '—' }}
                  </span>
                </td>
                <td>
                  <span class="badge {{ $record->is_active ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }} rounded-pill px-3 py-1">
                    {{ $record->is_active ? 'АКТИВЕН' : 'НЕАКТИВЕН' }}
                  </span>
                </td>
                <td class="pe-4">
                  <div class="d-flex align-items-center gap-2">
                    <span class="text-truncate" style="max-width: 200px;">{{ $record->source_file ?? '—' }}</span>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-5">
                  <div class="d-flex flex-column align-items-center">
                    <i class="bi bi-database-x fs-1 text-on-surface-variant mb-3"></i>
                    <p class="text-on-surface-variant mb-0">Нет данных для отображения</p>
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
  const tbody = document.querySelector('#dataTable tbody');
  const rows = Array.from(tbody.querySelectorAll('tr'));
  const table = document.getElementById('dataTable');
  const searchInput = document.getElementById('searchInput');
  const colSelect = document.getElementById('searchCol');
  let sortDir = {};

  // Данные для графика
  const allTimes = [];
  const voltages = [];
  const temps = [];

  rows.forEach(tr => {
    if (tr.cells.length >= 8) {
      const time = tr.cells[2].textContent.trim();
      allTimes.push(time);
      voltages.push(parseFloat(tr.cells[3].textContent) || 0);
      temps.push(parseFloat(tr.cells[4].textContent) || 0);
    }
  });

  if (allTimes.length > 0) {
    // Упрощаем подписи осей X - показываем только каждую 5-ю метку
    const displayLabels = allTimes.map((time, index) => {
      if (index === 0 || index === allTimes.length - 1 || index % 5 === 0) {
        return time;
      }
      return '';
    });

    const ctx = document.getElementById('telemetryChart').getContext('2d');
    
    // Настройки по умолчанию для Chart.js
    Chart.defaults.color = '#212529'; // Белый цвет для всех текстов
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.2)'; // Белая сетка с прозрачностью
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: displayLabels,
        datasets: [
          {
            label: 'Напряжение',
            data: voltages,
            borderColor: '#4A6FA5', // Синий цвет для напряжения
            backgroundColor: 'rgba(74, 111, 165, 0.1)',
            tension: 0.4,
            borderWidth: 2,
            pointRadius: 0, // Убираем точки
            pointHoverRadius: 4, // Показываем только при наведении
            fill: false,
            yAxisID: 'y'
          },
          {
            label: 'Температура',
            data: temps,
            borderColor: '#E74C3C', // Красный цвет для температуры
            backgroundColor: 'rgba(231, 76, 60, 0.1)',
            tension: 0.4,
            borderWidth: 2,
            borderDash: [5, 5], // Пунктирная линия для температуры
            pointRadius: 0, // Убираем точки
            pointHoverRadius: 4, // Показываем только при наведении
            fill: false,
            yAxisID: 'y1'
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          intersect: false,
          mode: 'index'
        },
        plugins: {
          legend: {
            display: true,
            position: 'top',
            labels: {
              color: '#212529', 
              font: {
                size: 12,
                family: 'system-ui'
              },
              padding: 20,
              usePointStyle: true,
              pointStyle: 'line'
            }
          },
          tooltip: {
            backgroundColor: 'rgba(30, 30, 30, 0.95)',
            titleColor: '#ffffff',
            bodyColor: '#ffffff',
            borderColor: 'rgba(255, 255, 255, 0.2)',
            borderWidth: 1,
            cornerRadius: 6,
            padding: 12,
            displayColors: true,
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                if (context.parsed.y !== null) {
                  const value = context.parsed.y;
                  const unit = context.datasetIndex === 0 ? ' В' : ' °C';
                  label += value.toFixed(2) + unit;
                }
                return label;
              },
              title: (items) => {
                const index = items[0].dataIndex;
                return allTimes[index] || 'Время не указано';
              }
            }
          }
        },
        scales: {
          x: {
            grid: {
              color: 'rgba(255, 255, 255, 0.1)', // Белая сетка с прозрачностью
              drawBorder: false,
              borderDash: [3, 3] // Пунктирная сетка
            },
            ticks: {
              color: '#212529', // Белые подписи оси X
              maxRotation: 45,
              font: {
                size: 11,
                family: 'system-ui'
              },
              callback: function(value, index) {
                // Показываем только непустые метки
                return this.getLabelForValue(value) || null;
              }
            },
            border: {
              color: 'rgba(255, 255, 255, 0.2)' // Белая рамка оси
            }
          },
          y: {
            type: 'linear',
            display: true,
            position: 'left',
            title: {
              display: true,
              text: 'Напряжение (В)',
              color: '#212529', // Белый заголовок оси
              font: {
                size: 12,
                family: 'system-ui'
              }
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.1)', // Белая сетка с прозрачностью
              drawBorder: false,
              borderDash: [3, 3] // Пунктирная сетка
            },
            ticks: {
              color: '#212529', // Белые подписи оси Y
              font: {
                size: 11,
                family: 'system-ui'
              },
              callback: function(value) {
                return value.toFixed(1) + ' В';
              }
            },
            border: {
              color: 'rgba(255, 255, 255, 0.2)' // Белая рамка оси
            },
            min: function(context) {
              const minVoltage = Math.min(...voltages);
              return Math.floor(minVoltage - 0.5);
            },
            max: function(context) {
              const maxVoltage = Math.max(...voltages);
              return Math.ceil(maxVoltage + 0.5);
            }
          },
          y1: {
            type: 'linear',
            display: true,
            position: 'right',
            title: {
              display: true,
              text: 'Температура (°C)',
              color: '#212529', // Белый заголовок оси
              font: {
                size: 12,
                family: 'system-ui'
              }
            },
            grid: {
              color: 'rgba(255, 255, 255, 0.05)', // Более светлая сетка для правой оси
              drawBorder: false,
              drawOnChartArea: true, // Включаем сетку на области графика
              borderDash: [3, 3] // Пунктирная сетка
            },
            ticks: {
              color: '#212529', // Белые подписи оси Y1
              font: {
                size: 11,
                family: 'system-ui'
              },
              callback: function(value) {
                return value.toFixed(1) + ' °C';
              }
            },
            border: {
              color: 'rgba(255, 255, 255, 0.2)' // Белая рамка оси
            },
            min: function(context) {
              const minTemp = Math.min(...temps);
              return Math.floor(minTemp - 2);
            },
            max: function(context) {
              const maxTemp = Math.max(...temps);
              return Math.ceil(maxTemp + 2);
            }
          }
        },
        elements: {
          line: {
            tension: 0.4
          }
        }
      }
    });
  }

  // Поиск
  searchInput.addEventListener('input', () => {
    const query = searchInput.value.toLowerCase();
    const col = parseInt(colSelect.value);

    tbody.querySelectorAll('tr').forEach(row => {
      const text = row.cells[col]?.textContent.toLowerCase() || '';
      row.style.display = text.includes(query) ? '' : 'none';
    });
  });

  // Сортировка
  table.querySelectorAll('th[data-sort]').forEach(th => {
    th.addEventListener('click', () => {
      const col = parseInt(th.dataset.sort);
      const isAscending = sortDir[col] = !sortDir[col];

      table.querySelectorAll('th[data-sort]').forEach(h => {
        h.removeAttribute('data-dir');
        h.classList.remove('active-sort');
      });
      
      th.setAttribute('data-dir', isAscending ? 'asc' : 'desc');
      th.classList.add('active-sort');

      const rowsArray = Array.from(tbody.querySelectorAll('tr'));
      rowsArray.sort((a, b) => {
        const aVal = a.cells[col]?.textContent.trim() || '';
        const bVal = b.cells[col]?.textContent.trim() || '';
        const comparison = aVal.localeCompare(bVal, undefined, { numeric: true });
        return isAscending ? comparison : -comparison;
      });

      rowsArray.forEach(r => tbody.appendChild(r));
    });
  });
});
</script>
@endsection