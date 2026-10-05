<script setup>
import { ref, reactive, computed, watch } from 'vue'

// 1. 基本響應式狀態 (ref 用於純值：數字、字串、布林)
const count = ref(0)
const step = ref(1)

// 2. 物件響應式狀態 (reactive 用於物件/陣列)
const userState = reactive({
  lastUpdated: '尚未操作',
  totalClicks: 0
})

// 3. 計算屬性 (computed - 具有快取效果)
const doubleCount = computed(() => count.value * 2)
const isPositive = computed(() => count.value > 0)
const statusClass = computed(() => {
  if (count.value > 0) return 'text-success'
  if (count.value < 0) return 'text-danger'
  return 'text-neutral'
})

// 4. 偵聽器 (watch - 監聽數值變化執行 Side Effect)
watch(count, (newVal, oldVal) => {
  userState.lastUpdated = new Date().toLocaleTimeString()
  userState.totalClicks++
  console.log(`[Watch 觸發] count 從 ${oldVal} 變更為 ${newVal}`)
})

// 5. 操作函式 (Methods)
const increment = () => {
  count.value += step.value
}

const decrement = () => {
  count.value -= step.value
}

const reset = () => {
  count.value = 0
}
</script>

<template>
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">
        <span class="icon">🔢</span> 1. 響應式計數器 (ref / reactive / computed / watch)
      </h2>
      <span class="badge">Composition API</span>
    </div>
    
    <p class="card-description">
      示範 Vue 3 的核心概念：<code>ref()</code> 基本數值、<code>reactive()</code> 物件狀態、<code>computed()</code> 計算屬性與 <code>watch()</code> 數據偵聽。
    </p>

    <!-- 數值顯示 -->
    <div class="counter-display">
      <div class="count-box">
        <span class="label">當前 Count</span>
        <span class="value" :class="statusClass">{{ count }}</span>
      </div>
      <div class="count-box">
        <span class="label">雙倍 (computed)</span>
        <span class="value">{{ doubleCount }}</span>
      </div>
      <div class="count-box">
        <span class="label">總點擊次數 (reactive)</span>
        <span class="value">{{ userState.totalClicks }}</span>
      </div>
    </div>

    <!-- 步長設定與操作按鈕 -->
    <div class="controls-wrapper">
      <div class="step-control">
        <label for="step-input">每次增減步長：</label>
        <input 
          id="step-input" 
          v-model.number="step" 
          type="number" 
          min="1" 
          max="10" 
          class="input step-input"
        >
      </div>

      <div class="btn-group">
        <button class="btn btn-secondary" @click="decrement">- 減少 {{ step }}</button>
        <button class="btn btn-danger" @click="reset">重置 0</button>
        <button class="btn btn-primary" @click="increment">+ 增加 {{ step }}</button>
      </div>
    </div>

    <!-- 狀態資訊與 Watch 紀錄 -->
    <div class="info-footer">
      <small>最後更新時間 (Watch 觸發)：<strong>{{ userState.lastUpdated }}</strong></small>
    </div>
  </div>
</template>

<style scoped>
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.counter-display {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 1rem;
  margin: 1.5rem 0;
}

.count-box {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 1rem;
  text-align: center;
}

.count-box .label {
  display: block;
  font-size: 0.75rem;
  color: #64748b;
  font-weight: 600;
  margin-bottom: 0.25rem;
}

.count-box .value {
  font-size: 1.75rem;
  font-weight: 800;
  font-family: var(--font-mono);
  color: #0f172a;
}

.text-success { color: #10b981 !important; }
.text-danger { color: #ef4444 !important; }
.text-neutral { color: #64748b !important; }

.controls-wrapper {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  padding-top: 1rem;
  border-top: 1px solid #f1f5f9;
}

.step-control {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: #475569;
}

.step-input {
  width: 70px;
  padding: 0.4rem 0.6rem;
  text-align: center;
}

.btn-group {
  display: flex;
  gap: 0.5rem;
}

.info-footer {
  margin-top: 1rem;
  font-size: 0.75rem;
  color: #94a3b8;
  text-align: right;
}
</style>
