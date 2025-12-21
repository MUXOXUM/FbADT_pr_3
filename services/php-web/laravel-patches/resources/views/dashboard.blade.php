@extends('layouts.app')

@section('content')
<div class="container py-4">
  <!-- Основная информация МКС -->
  <div class="row g-4 mb-4">
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-body p-4 text-center">
          <div class="d-flex align-items-center justify-content-center gap-3 mb-3">
            <div class="text-start">
              <div class="text-on-surface-variant mb-1">Скорость МКС</div>
              <div class="display-4 fw-bold text-on-surface" id="issSpeed">
                <span class="text-muted">—</span>
              </div>
            </div>
          </div>
          <div class="text-on-surface-variant">километров в час</div>
        </div>
      </div>
    </div>
    
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-body p-4 text-center">
          <div class="d-flex align-items-center justify-content-center gap-3 mb-3">
            <div class="text-start">
              <div class="text-on-surface-variant mb-1">Высота МКС</div>
              <div class="display-4 fw-bold text-on-surface" id="issAlt">
                <span class="text-muted">—</span>
              </div>
            </div>
          </div>
          <div class="text-on-surface-variant">километров над Землей</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Карта МКС -->
  <div class="card mb-4">
    <div class="card-header bg-surface-variant border-bottom border-outline py-3">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-globe-americas fs-4 text-primary"></i>
        <h4 class="mb-0 text-on-surface">МКС — положение и траектория</h4>
      </div>
    </div>
    <div class="card-body p-4">
      <div id="map" class="rounded-3 border border-outline overflow-hidden" style="height: 400px;">
        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-surface">
          <div class="text-center">
            <div class="spinner-border text-primary mb-3"></div>
            <p class="text-on-surface-variant">Инициализация карты...</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Графики МКС -->
  <div class="row g-4 mb-4">
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header bg-surface-variant border-bottom border-outline py-3">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-graph-up fs-4 text-primary"></i>
            <h5 class="mb-0 text-on-surface">Динамика скорости</h5>
          </div>
        </div>
        <div class="card-body p-3">
          <div class="position-relative" style="height: 200px;">
            <canvas id="issSpeedChart"></canvas>
            <div id="speedChartLoader" class="w-100 h-100 d-flex align-items-center justify-content-center position-absolute top-0 start-0 bg-surface">
              <div class="text-center">
                <div class="spinner-border spinner-border-sm text-primary mb-2"></div>
                <p class="small text-on-surface-variant mb-0">Получение данных...</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="col-12 col-md-6">
      <div class="card">
        <div class="card-header bg-surface-variant border-bottom border-outline py-3">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-bar-chart-line fs-4 text-primary"></i>
            <h5 class="mb-0 text-on-surface">Изменение высоты</h5>
          </div>
        </div>
        <div class="card-body p-3">
          <div class="position-relative" style="height: 200px;">
            <canvas id="issAltChart"></canvas>
            <div id="altChartLoader" class="w-100 h-100 d-flex align-items-center justify-content-center position-absolute top-0 start-0 bg-surface">
              <div class="text-center">
                <div class="spinner-border spinner-border-sm text-primary mb-2"></div>
                <p class="small text-on-surface-variant mb-0">Получение данных...</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- JWST Превью -->
  <div class="card mb-4">
    <div class="card-header bg-surface-variant border-bottom border-outline py-3">
      <div class="d-flex align-items-center gap-2">
        <i class="bi bi-telescope fs-4 text-primary"></i>
        <h4 class="mb-0 text-on-surface">JWST — выбранное наблюдение</h4>
      </div>
    </div>
    <div class="card-body p-4">
      <div id="jwstPreview" class="bg-surface-variant rounded-3 border border-outline d-flex align-items-center justify-content-center overflow-hidden" style="height: 500px;">
        <div class="text-center p-5">
          <i class="bi bi-image fs-1 text-outline mb-3"></i>
          <p class="text-on-surface-variant mb-0">Выберите любое изображение из галереи ниже</p>
        </div>
      </div>
    </div>
  </div>

  <!-- JWST Галерея -->
  <div class="card">
    <div class="card-header bg-surface-variant border-bottom border-outline py-3">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-images fs-4 text-primary"></i>
          <h4 class="mb-0 text-on-surface">JWST — последние изображения</h4>
        </div>
        <form id="jwstFilterForm" class="d-flex flex-wrap gap-2 align-items-center">
          <select class="form-select" name="source" id="filterSource" style="width: 140px;">
            <option value="jpg" selected>Все JPG</option>
            <option value="suffix">По суффиксу</option>
            <option value="program">По программе</option>
          </select>
          <input type="text" class="form-control" name="suffix" id="suffixField" placeholder="_cal / _thumb" style="width: 140px; display: none;">
          <input type="text" class="form-control" name="program" id="programField" placeholder="Программа ID" style="width: 140px; display: none;">
          <select class="form-select" name="instrument" style="width: 140px;">
            <option value="">Любой инструмент</option>
            <option>NIRCam</option>
            <option>MIRI</option>
            <option>NIRISS</option>
            <option>NIRSpec</option>
            <option>FGS</option>
          </select>
          <select class="form-select" name="perPage" style="width: 100px;">
            <option>12</option>
            <option selected>24</option>
            <option>36</option>
            <option>48</option>
          </select>
          <button class="btn btn-primary d-flex align-items-center gap-2 px-3" type="submit">
            <i class="bi bi-search"></i>
            <span>Показать</span>
          </button>
        </form>
      </div>
    </div>
    
    <div class="card-body p-4">
      <div class="position-relative">
        <button class="btn btn-outline rounded-circle position-absolute top-50 start-0 translate-middle-y z-3 shadow border-0" 
                style="left: 10px; width: 40px; height: 40px;" id="prevBtn">
          <i class="bi bi-chevron-left"></i>
        </button>
        <button class="btn btn-outline rounded-circle position-absolute top-50 end-0 translate-middle-y z-3 shadow border-0" 
                style="right: 10px; width: 40px; height: 40px;" id="nextBtn">
          <i class="bi bi-chevron-right"></i>
        </button>

        <div id="galleryTrack" class="d-flex gap-3 overflow-auto pb-3 px-4" style="scrollbar-width: none; -ms-overflow-style: none;">
          <div class="d-flex align-items-center justify-content-center" style="min-height: 280px; min-width: 100%;">
            <div class="text-center">
              <div class="spinner-border text-primary mb-3"></div>
              <p class="text-on-surface-variant">Загрузка галереи...</p>
            </div>
          </div>
        </div>
      </div>

      <div id="galleryInfo" class="d-flex justify-content-between align-items-center mt-3 text-on-surface-variant">
        <div>
          <i class="bi bi-info-circle me-1"></i>
          <span id="sourceInfo">Загрузка...</span>
        </div>
        <div>
          <i class="bi bi-collection me-1"></i>
          <span id="countInfo">0</span> изображений
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Стили для галереи JWST */
  .jwst-thumb {
    flex: 0 0 260px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  .jwst-thumb:hover { 
    transform: translateY(-8px); 
    box-shadow: var(--md-elevation-3);
  }
  
  .jwst-thumb img {
    width: 100%; 
    height: 260px; 
    object-fit: cover; 
    border-radius: 12px; 
    border: 1px solid var(--md-outline);
    background: var(--md-surface);
  }
  
  .jwst-thumb-cap {
    font-size: 0.85rem; 
    margin-top: 0.8rem; 
    color: var(--md-on-surface-variant); 
    text-align: center;
    padding: 0.5rem;
    line-height: 1.3;
  }
  
  /* Стили для карты */
  .leaflet-container {
    font-family: inherit !important;
    background: var(--md-surface) !important;
  }
  
  /* Скрываем скроллбар галереи */
  #galleryTrack::-webkit-scrollbar {
    display: none;
  }
  
  /* Стили для маркера МКС */
  .iss-marker {
    background: var(--md-primary);
    border-radius: 50%;
    padding: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0 0 4px rgba(187, 134, 252, 0.3);
  }
  
  .iss-marker i {
    color: white;
    font-size: 16px;
  }
  
  /* Адаптивность формы фильтра */
  @media (max-width: 1200px) {
    #jwstFilterForm {
      gap: 1rem;
    }
    
    #jwstFilterForm .form-select,
    #jwstFilterForm .form-control {
      width: 100% !important;
      max-width: 200px;
    }
  }
  
  @media (max-width: 768px) {
    #jwstFilterForm {
      flex-direction: column;
      align-items: stretch;
    }
    
    #jwstFilterForm .form-select,
    #jwstFilterForm .form-control {
      max-width: 100%;
    }
    
    .jwst-thumb {
      flex: 0 0 220px;
    }
    
    .jwst-thumb img {
      height: 220px;
    }
    
    #map {
      height: 300px !important;
    }
    
    #jwstPreview {
      height: 350px !important;
    }
  }
  
  @media (max-width: 576px) {
    .display-4 {
      font-size: 2.5rem;
    }
    
    .jwst-thumb {
      flex: 0 0 180px;
    }
    
    .jwst-thumb img {
      height: 180px;
    }
  }
</style>

<script>
// Утилита для безопасной загрузки
const ApiUtils = {
  async fetchWithRetry(url, options = {}, retries = 3) {
    for (let i = 0; i < retries; i++) {
      try {
        const response = await fetch(url, {
          ...options,
          headers: {
            'Accept': 'application/json',
            ...options.headers
          }
        });
        
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}`);
        }
        
        return await response.json();
      } catch (error) {
        if (i === retries - 1) throw error;
        await new Promise(resolve => setTimeout(resolve, 1000 * (i + 1)));
      }
    }
  }
};

// Главная функция инициализации
document.addEventListener('DOMContentLoaded', async function () {
  console.log('🚀 Инициализация dashboard...');
  
  try {
    // Инициализируем МКС компоненты
    await initializeISS();
    
    // Инициализируем JWST галерею
    await initializeJWST();
    
    console.log('✅ Dashboard успешно инициализирован');
  } catch (error) {
    console.error('❌ Ошибка инициализации dashboard:', error);
    showError('Ошибка загрузки данных. Пожалуйста, обновите страницу.');
  }
});

// Инициализация МКС
async function initializeISS() {
  console.log('🛰️ Инициализация МКС...');
  
  try {
    // Инициализация карты
    const mapElement = document.getElementById('map');
    if (!mapElement) throw new Error('Элемент карты не найден');
    
    // Проверяем, загружена ли библиотека Leaflet
    if (typeof L === 'undefined') {
      console.warn('⚠️ Leaflet не загружен');
      mapElement.innerHTML = `
        <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-surface-variant">
          <i class="bi bi-exclamation-triangle fs-1 text-warning mb-3"></i>
          <p class="text-on-surface-variant text-center">Не удалось загрузить карту</p>
        </div>
      `;
      return;
    }
    
    // Начальные координаты (Москва по умолчанию)
    const initialLat = 55.7558;
    const initialLon = 37.6176;
    
    // Инициализация карты
    const map = L.map(mapElement, {
      center: [initialLat, initialLon],
      zoom: 3,
      zoomControl: false
    });
    
    // Добавляем контрол зума
    L.control.zoom({ position: 'topright' }).addTo(map);
    
    // Используем надежный тайл-провайдер
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors',
      maxZoom: 19
    }).addTo(map);
    
    // Создаем трек МКС
    const pathLine = L.polyline([], {
      color: 'var(--md-primary)',
      weight: 3,
      opacity: 0.7
    }).addTo(map);
    
    // Создаем маркер МКС
    const issIcon = L.divIcon({
      html: '<div class="iss-marker"><i class="bi bi-satellite"></i></div>',
      className: '',
      iconSize: [40, 40],
      iconAnchor: [20, 20]
    });
    
    const issMarker = L.marker([initialLat, initialLon], { icon: issIcon }).addTo(map);
    
    // Добавляем всплывающую подсказку
    issMarker.bindPopup(`
      <div class="text-dark p-2">
        <strong>Международная космическая станция</strong><br>
        Широта: ${initialLat.toFixed(4)}°<br>
        Долгота: ${initialLon.toFixed(4)}°
      </div>
    `);
    
    // Инициализация графиков
    const charts = initializeCharts();
    
    // Функция обновления данных МКС
    async function updateIssData() {
      try {
        console.log('📡 Обновление данных МКС...');
        const data = await ApiUtils.fetchWithRetry('/api/iss/last');
        const payload = data.payload || {};
        
        if (payload.latitude && payload.longitude) {
          const lat = parseFloat(payload.latitude);
          const lon = parseFloat(payload.longitude);
          const newPosition = [lat, lon];
          
          // Обновляем маркер
          issMarker.setLatLng(newPosition);
          
          // Обновляем трек
          const currentPath = pathLine.getLatLngs();
          currentPath.push(newPosition);
          
          // Ограничиваем длину трека
          if (currentPath.length > 50) {
            currentPath.shift();
          }
          
          pathLine.setLatLngs(currentPath);
          
          // Плавное перемещение карты
          map.panTo(newPosition, {
            animate: true,
            duration: 0.5
          });
          
          // Обновляем всплывающую подсказку
          issMarker.setPopupContent(`
            <div class="text-dark p-2">
              <strong>Международная космическая станция</strong><br>
              Широта: ${lat.toFixed(4)}°<br>
              Долгота: ${lon.toFixed(4)}°<br>
              Высота: ${payload.altitude || 0} км<br>
              Скорость: ${payload.velocity || 0} км/ч<br>
              <small>Обновлено: ${new Date().toLocaleTimeString()}</small>
            </div>
          `);
        }
        
        // Обновляем числовые значения
        updateDisplayValues(payload);
        
        // Обновляем графики
        updateChartsData(payload, charts);
        
      } catch (error) {
        console.warn('⚠️ Не удалось обновить данные МКС:', error.message);
        // Используем демо-данные при ошибке
        const demoPayload = {
          velocity: Math.floor(Math.random() * 1000) + 27000,
          altitude: Math.floor(Math.random() * 50) + 400,
          latitude: initialLat + (Math.random() - 0.5) * 10,
          longitude: initialLon + (Math.random() - 0.5) * 10
        };
        
        updateDisplayValues(demoPayload);
        updateChartsData(demoPayload, charts);
      }
    }
    
    // Функция обновления отображаемых значений
    function updateDisplayValues(payload) {
      const speedElement = document.getElementById('issSpeed');
      const altElement = document.getElementById('issAlt');
      
      if (payload.velocity) {
        speedElement.textContent = Math.round(payload.velocity).toLocaleString();
        speedElement.classList.remove('text-muted');
      }
      
      if (payload.altitude) {
        altElement.textContent = Math.round(payload.altitude).toLocaleString();
        altElement.classList.remove('text-muted');
      }
    }
    
    // Первоначальное обновление
    await updateIssData();
    
    // Периодическое обновление
    const updateInterval = {{ $issEverySeconds ?? 120 }} * 1000;
    setInterval(updateIssData, updateInterval);
    
    console.log('✅ МКС инициализирована');
    
  } catch (error) {
    console.error('❌ Ошибка инициализации МКС:', error);
    document.getElementById('map').innerHTML = `
      <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center bg-surface-variant">
        <i class="bi bi-exclamation-triangle fs-1 text-danger mb-3"></i>
        <p class="text-on-surface-variant text-center mb-2">Не удалось загрузить карту</p>
        <button class="btn btn-sm btn-outline mt-2" onclick="initializeISS()">
          <i class="bi bi-arrow-clockwise me-1"></i> Повторить
        </button>
      </div>
    `;
  }
}

// Инициализация графиков
function initializeCharts() {
  console.log('📊 Инициализация графиков...');
  
  try {
    // Проверяем, загружена ли библиотека Chart.js
    if (typeof Chart === 'undefined') {
      console.warn('⚠️ Chart.js не загружен');
      return null;
    }
    
    // Настройки Chart.js для темной темы
    Chart.defaults.color = 'var(--md-on-surface-variant)';
    Chart.defaults.borderColor = 'var(--md-outline)';
    
    // Получаем canvas элементы
    const speedCanvas = document.getElementById('issSpeedChart');
    const altCanvas = document.getElementById('issAltChart');
    
    if (!speedCanvas || !altCanvas) {
      throw new Error('Canvas элементы не найдены');
    }
    
    // Создаем график скорости
    const speedChart = new Chart(speedCanvas, {
      type: 'line',
      data: {
        labels: [],
        datasets: [{
          data: [],
          borderColor: 'var(--md-primary)',
          backgroundColor: 'rgba(187, 134, 252, 0.1)',
          tension: 0.4,
          fill: true,
          pointRadius: 0,
          pointHoverRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: { display: false },
          y: {
            grid: { color: 'var(--md-outline)' },
            ticks: { color: 'var(--md-on-surface-variant)' }
          }
        }
      }
    });
    
    // Создаем график высоты
    const altChart = new Chart(altCanvas, {
      type: 'line',
      data: {
        labels: [],
        datasets: [{
          data: [],
          borderColor: 'var(--md-secondary)',
          backgroundColor: 'rgba(3, 218, 198, 0.1)',
          tension: 0.4,
          fill: true,
          pointRadius: 0,
          pointHoverRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: { display: false },
          y: {
            grid: { color: 'var(--md-outline)' },
            ticks: { color: 'var(--md-on-surface-variant)' }
          }
        }
      }
    });
    
    // Скрываем загрузчики
    document.getElementById('speedChartLoader').style.display = 'none';
    document.getElementById('altChartLoader').style.display = 'none';
    
    console.log('✅ Графики инициализированы');
    
    return { speedChart, altChart };
    
  } catch (error) {
    console.error('❌ Ошибка инициализации графиков:', error);
    return null;
  }
}

// Обновление данных графиков
function updateChartsData(payload, charts) {
  if (!charts) return;
  
  const now = new Date();
  const timeLabel = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}`;
  
  // Обновляем график скорости
  charts.speedChart.data.labels.push(timeLabel);
  charts.speedChart.data.datasets[0].data.push(payload.velocity || 0);
  
  // Обновляем график высоты
  charts.altChart.data.labels.push(timeLabel);
  charts.altChart.data.datasets[0].data.push(payload.altitude || 0);
  
  // Ограничиваем количество точек
  const maxPoints = 15;
  
  if (charts.speedChart.data.labels.length > maxPoints) {
    charts.speedChart.data.labels.shift();
    charts.speedChart.data.datasets[0].data.shift();
    charts.altChart.data.labels.shift();
    charts.altChart.data.datasets[0].data.shift();
  }
  
  // Обновляем графики
  charts.speedChart.update('none');
  charts.altChart.update('none');
}

// Инициализация JWST галереи
async function initializeJWST() {
  console.log('🔭 Инициализация JWST галереи...');
  
  const gallery = document.getElementById('galleryTrack');
  const sourceInfo = document.getElementById('sourceInfo');
  const countInfo = document.getElementById('countInfo');
  const filterForm = document.getElementById('jwstFilterForm');
  const sourceSelect = document.getElementById('filterSource');
  const suffixInput = document.getElementById('suffixField');
  const programInput = document.getElementById('programField');
  const previewBlock = document.getElementById('jwstPreview');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  
  if (!gallery || !previewBlock) {
    console.warn('⚠️ Элементы галереи не найдены');
    return;
  }
  
  // Утилита для безопасной загрузки изображений
  function createImageWithFallback(src, alt) {
    const img = new Image();
    img.loading = 'lazy';
    img.alt = alt;
    img.className = 'rounded-3';
    img.style.cssText = 'width: 100%; height: 260px; object-fit: cover; background: var(--md-surface);';
    
    // Fallback при ошибке загрузки
    img.onerror = function() {
      this.src = `data:image/svg+xml;base64,${btoa(`
        <svg width="260" height="260" viewBox="0 0 260 260" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="260" height="260" fill="%231e1e1e" rx="12"/>
          <rect x="40" y="40" width="180" height="180" rx="8" fill="%233a3a3a"/>
          <circle cx="130" cy="100" r="25" fill="%23555"/>
          <rect x="80" y="150" width="100" height="35" rx="4" fill="%23555"/>
        </svg>
      `)}`;
    };
    
    img.src = src;
    return img;
  }
  
  // Отображение превью
  function displayPreview(photo) {
    if (!photo || !photo.url) return;
    
    const img = createImageWithFallback(photo.url, photo.caption || 'JWST Image');
    img.style.maxHeight = '350px';
    img.style.maxWidth = '90%';
    img.style.objectFit = 'contain';
    
    previewBlock.innerHTML = '';
    const container = document.createElement('div');
    container.className = 'h-100 w-100 d-flex flex-column justify-content-center align-items-center p-4';
    
    container.appendChild(img);
    
    const caption = document.createElement('p');
    caption.className = 'mt-4 fs-5 text-on-surface text-center';
    caption.textContent = photo.caption || 'Без названия';
    container.appendChild(caption);
    
    if (photo.link || photo.url) {
      const linkBtn = document.createElement('a');
      linkBtn.href = photo.link || photo.url;
      linkBtn.target = '_blank';
      linkBtn.rel = 'noopener noreferrer';
      linkBtn.className = 'btn btn-outline mt-3 d-flex align-items-center gap-2';
      linkBtn.innerHTML = '<i class="bi bi-box-arrow-up-right"></i><span>Скачать</span>';
      container.appendChild(linkBtn);
    }
    
    previewBlock.appendChild(container);
  }
  
  // Переключение полей фильтра
  function toggleFilterFields() {
    const isSuffix = sourceSelect.value === 'suffix';
    const isProgram = sourceSelect.value === 'program';
    
    suffixInput.style.display = isSuffix ? 'inline-block' : 'none';
    programInput.style.display = isProgram ? 'inline-block' : 'none';
  }
  
  sourceSelect.addEventListener('change', toggleFilterFields);
  toggleFilterFields();
  
  // Загрузка и отображение галереи
  async function loadGallery(params = {}) {
    try {
      sourceInfo.textContent = 'Загрузка...';
      countInfo.textContent = '0';
      
      // Используем параметры по умолчанию
      const requestParams = new URLSearchParams({
        source: 'jpg',
        perPage: '24',
        ...params
      });
      
      const apiUrl = `/api/jwst/feed?${requestParams.toString()}`;
      const galleryData = await ApiUtils.fetchWithRetry(apiUrl);
      
      const items = galleryData.items || [];
      
      // Очищаем галерею
      gallery.innerHTML = '';
      
      if (items.length === 0) {
        gallery.innerHTML = `
          <div class="d-flex align-items-center justify-content-center" style="min-height: 280px; min-width: 100%;">
            <div class="text-center">
              <i class="bi bi-images fs-1 text-on-surface-variant mb-3"></i>
              <p class="text-on-surface-variant">Изображения не найдены</p>
            </div>
          </div>`;
      } else {
        items.forEach((photo, index) => {
          const thumbContainer = document.createElement('div');
          thumbContainer.className = 'jwst-thumb';
          thumbContainer.title = photo.caption || `Изображение ${index + 1}`;
          
          const img = createImageWithFallback(
            photo.url, 
            photo.caption || `JWST Image ${index + 1}`
          );
          
          const captionDiv = document.createElement('div');
          captionDiv.className = 'jwst-thumb-cap';
          captionDiv.textContent = (photo.caption || `Изображение ${index + 1}`).substring(0, 60);
          if ((photo.caption || '').length > 60) captionDiv.textContent += '...';
          
          thumbContainer.appendChild(img);
          thumbContainer.appendChild(captionDiv);
          
          // Обработчик клика для превью
          thumbContainer.addEventListener('click', () => displayPreview(photo));
          
          gallery.appendChild(thumbContainer);
        });
      }
      
      sourceInfo.textContent = `Источник: ${galleryData.source || 'неизвестен'}`;
      countInfo.textContent = items.length.toString();
      
      // Показываем первое изображение как превью
      if (items.length > 0) {
        displayPreview(items[0]);
      }
      
      console.log(`✅ Загружено ${items.length} изображений JWST`);
      
    } catch (error) {
      console.error('❌ Ошибка загрузки галереи JWST:', error);
      
      gallery.innerHTML = `
        <div class="d-flex align-items-center justify-content-center" style="min-height: 280px; min-width: 100%;">
          <div class="text-center text-danger">
            <i class="bi bi-exclamation-triangle fs-1 mb-3"></i>
            <p class="mb-2">Ошибка загрузки изображений</p>
            <button class="btn btn-sm btn-outline" onclick="loadGallery()">
              <i class="bi bi-arrow-clockwise"></i> Повторить
            </button>
          </div>
        </div>`;
    }
  }
  
  // Обработка формы фильтра
  filterForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const formData = new FormData(filterForm);
    const params = Object.fromEntries(formData.entries());
    loadGallery(params);
  });
  
  // Кнопки навигации
  if (prevBtn) {
    prevBtn.onclick = () => {
      gallery.scrollBy({ left: -300, behavior: 'smooth' });
    };
  }
  
  if (nextBtn) {
    nextBtn.onclick = () => {
      gallery.scrollBy({ left: 300, behavior: 'smooth' });
    };
  }
  
  // Начальная загрузка
  await loadGallery({ source: 'jpg', perPage: 24 });
  
  console.log('✅ JWST галерея инициализирована');
}

// Вспомогательная функция для отображения ошибок
function showError(message) {
  const errorDiv = document.createElement('div');
  errorDiv.className = 'alert alert-danger position-fixed bottom-0 end-0 m-3';
  errorDiv.style.zIndex = '9999';
  errorDiv.innerHTML = `
    <div class="d-flex align-items-center gap-2">
      <i class="bi bi-exclamation-triangle"></i>
      <span>${message}</span>
    </div>
  `;
  document.body.appendChild(errorDiv);
  
  setTimeout(() => {
    errorDiv.remove();
  }, 5000);
}
</script>
@endsection