<template>
  <div class="card glass-card border-0 rounded-4 overflow-hidden text-dark">
    <div class="card-header bg-transparent text-center py-4 border-0">
      <h1 class="h3 mb-0 fw-bold tracking-wide">目前天氣</h1>
    </div>
    <div class="card-body text-center p-5">
      
      <div v-if="weather" class="mb-5 fade-in">
        <div class="weather-icon-container mb-3">
          <i :class="`bi ${getWeatherIcon(weather.weathercode)} display-1 text-primary drop-shadow`"></i>
        </div>
        <div class="display-1 fw-bold text-dark mb-2 temp-text">
          {{ weather.temperature }}<span class="fs-1 align-top fw-light">°C</span>
        </div>
        <h3 class="text-secondary mb-4 fw-light letter-spacing-2">{{ getWeatherDescription(weather.weathercode) }}</h3>
        
        <div class="d-flex justify-content-center gap-4 text-secondary small">
          <div class="d-flex align-items-center">
            <i class="bi bi-geo-alt-fill me-2"></i>
            <span>緯度: {{ location.latitude.toFixed(2) }}, 經度: {{ location.longitude.toFixed(2) }}</span>
          </div>
          <div class="d-flex align-items-center">
            <i class="bi bi-wind me-2"></i>
            <span>風速: {{ weather.windspeed }} km/h</span>
          </div>
        </div>
      </div>

      <div v-else-if="loading" class="my-5">
        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
          <span class="visually-hidden">載入中...</span>
        </div>
        <p class="mt-2 text-secondary">正在連接氣象衛星...</p>
      </div>

      <div v-else-if="error" class="alert alert-danger-glass fade-in d-inline-block px-4 py-3 rounded-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ error }}
      </div>

      <div v-else class="my-5 text-secondary">
        <div class="icon-pulse mb-4">
          <i class="bi bi-cloud-sun fs-1 d-block text-primary"></i>
        </div>
        <p class="fw-light">探索您身邊的大氣狀況。</p>
      </div>

      <button 
        @click="fetchWeather" 
        class="btn btn-glass btn-lg w-100 rounded-pill mt-3 py-3 fw-semibold letter-spacing-1"
        :disabled="loading"
      >
        <span v-if="loading">處理中...</span>
        <span v-else>
          <i class="bi bi-search me-2"></i>查詢天氣預報
        </span>
      </button>
    </div>
    <div class="card-footer bg-transparent text-center py-3 text-muted small border-0">
      資料來源: <a href="https://open-meteo.com/" target="_blank" class="text-primary text-decoration-none fw-bold">Open-Meteo</a>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const weather = ref(null)
const location = ref(null)
const loading = ref(false)
const error = ref(null)

// WMO Weather interpretation codes (WW)
const weatherCodes = {
  0: '晴朗無雲',
  1: '大致晴朗',
  2: '局部多雲', 
  3: '陰天',
  45: '有霧',
  48: '霧氣凝結',
  51: '毛毛雨',
  53: '中度毛毛雨',
  55: '綿密毛毛雨',
  56: '凍雨',
  57: '大凍雨',
  61: '小雨',
  63: '中雨',
  65: '大雨',
  66: '冷雨',
  67: '大冷雨',
  71: '小雪',
  73: '中雪',
  75: '大雪',
  77: '雪粒',
  80: '陣雨',
  81: '中陣雨',
  82: '強陣雨',
  85: '陣雪',
  86: '大陣雪',
  95: '雷雨',
  96: '雷雨伴隨冰雹',
  99: '強烈雷雨伴隨大冰雹'
}

const getWeatherDescription = (code) => {
  return weatherCodes[code] || '未知天氣'
}

const getWeatherIcon = (code) => {
  if (code === 0) return 'bi-sun-fill text-warning'
  if (code === 1 || code === 2) return 'bi-cloud-sun-fill text-warning'
  if (code === 3) return 'bi-cloud-fill text-secondary'
  if (code >= 45 && code <= 48) return 'bi-cloud-haze2-fill text-secondary'
  if (code >= 51 && code <= 67) return 'bi-cloud-drizzle-fill text-primary'
  if (code >= 71 && code <= 77) return 'bi-snow2 text-info'
  if (code >= 80 && code <= 82) return 'bi-cloud-rain-heavy-fill text-primary'
  if (code >= 85 && code <= 86) return 'bi-snow3 text-info'
  if (code >= 95) return 'bi-cloud-lightning-rain-fill text-warning'
  return 'bi-cloud-fill text-secondary'
}

const fetchWeather = () => {
  loading.value = true
  error.value = null
  weather.value = null

  if (!navigator.geolocation) {
    error.value = "您的瀏覽器不支援地理定位功能。"
    loading.value = false
    return
  }

  navigator.geolocation.getCurrentPosition(
    async (position) => {
      try {
        const lat = position.coords.latitude
        const lon = position.coords.longitude
        location.value = { latitude: lat, longitude: lon }
        
        // Fetch weather data from Open-Meteo API
        const response = await fetch(
          `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true`
        )
        
        if (!response.ok) {
          throw new Error('無法取得天氣資料')
        }

        const data = await response.json()
        weather.value = data.current_weather
      } catch (err) {
        error.value = "取得天氣資料失敗，請再試一次。"
        console.error(err)
      } finally {
        loading.value = false
      }
    },
    (err) => {
      error.value = "無法取得您的位置，請允許存取位置資訊。"
      loading.value = false
      console.error(err)
    }
  )
}
</script>

<style scoped>
.glass-card {
  background: rgba(255, 255, 255, 0.7); /* More opaque white for light theme */
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.8) !important;
  box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15); /* Softer shadow */
  transition: transform 0.3s ease;
}

.glass-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.2);
}

.alert-danger-glass {
  background: rgba(220, 53, 69, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(220, 53, 69, 0.2);
  color: #dc3545;
}

.btn-glass {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); /* Purple gradient button */
  border: none;
  color: white;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-glass:hover:not(:disabled) {
  transform: scale(1.02);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
  opacity: 0.95;
}

.btn-glass:disabled {
  background: #ccc;
  cursor: not-allowed;
  box-shadow: none;
}

.drop-shadow {
  filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
}

.tracking-wide {
  letter-spacing: 0.1em;
}

.letter-spacing-1 {
  letter-spacing: 1px;
}
.letter-spacing-2 {
  letter-spacing: 2px;
}

.icon-pulse i {
  animation: pulse 2s infinite ease-in-out;
}

@keyframes pulse {
  0% { transform: scale(1); opacity: 0.8; }
  50% { transform: scale(1.1); opacity: 1; }
  100% { transform: scale(1); opacity: 0.8; }
}

.fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.temp-text {
  /* text-shadow: 2px 2px 4px rgba(0,0,0,0.1); */ /* Removed heavy shadow for clean look */
}
</style>
