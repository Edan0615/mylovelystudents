<script setup>
import { ref } from 'vue'
import HeaderBar from './components/HeaderBar.vue'
import CounterDemo from './components/CounterDemo.vue'
import TodoListDemo from './components/TodoListDemo.vue'
import CourseInfoCard from './components/CourseInfoCard.vue'

// 父組件狀態
const courseLikes = ref(12)
const studentName = ref('蘇品樺')
const feedbackLogs = ref([])

// 處理子組件點讚事件
const handleLikeClick = () => {
  courseLikes.value++
}

// 處理子組件送出心得 Emit 事件
const handleFeedbackSent = (feedback) => {
  feedbackLogs.value.unshift(feedback)
}
</script>

<template>
  <div class="app-wrapper">
    <!-- 頁頭組件 -->
    <HeaderBar 
      title="Vue 3 Composition API 實戰教學 Demo" 
      subtitle="專案二 · 第二堂課基礎範例" 
    />

    <main class="container">
      <!-- 導覽簡介 -->
      <section class="intro-box">
        <h2 class="intro-title">👋 歡迎來到 Vue 3 Composition API 專案範例</h2>
        <p class="intro-desc">
          本專案已幫你配置好 <strong>Vite + Vue 3 <code>&lt;script setup&gt;</code></strong> 語法結構，並內建了三個核心觀念示範組件：
        </p>
        <ul class="feature-list">
          <li><strong>1. 響應式核心：</strong> <code>ref()</code>, <code>reactive()</code>, <code>computed()</code>, <code>watch()</code></li>
          <li><strong>2. 經典 UI 互動：</strong> <code>v-model</code> 雙向綁定, <code>v-for</code> 列表, <code>v-if</code> 條件判斷, LocalStorage 儲存</li>
          <li><strong>3. 組件化通訊：</strong> 父傳子 <code>Props</code>, 子傳父 <code>Emits</code> 事件機制</li>
        </ul>
      </section>

      <!-- 核心功能展示區塊 -->
      <CounterDemo />
      
      <TodoListDemo />

      <CourseInfoCard 
        course-title="Vue.js 現代前端開發與核心概念"
        :student-name="studentName"
        :likes="courseLikes"
        @like-click="handleLikeClick"
        @feedback-sent="handleFeedbackSent"
      />

      <!-- 父組件收到的 Emit 事件日誌 -->
      <div v-if="feedbackLogs.length > 0" class="card feedback-logs">
        <h3 class="card-title">📡 父組件即時接收到的 Emit 事件紀錄</h3>
        <ul class="log-list">
          <li v-for="(log, idx) in feedbackLogs" :key="idx" class="log-item">
            <span class="log-time">[{{ log.time }}]</span>
            <strong>{{ log.student }}</strong> 說：
            <span class="log-text">"{{ log.text }}"</span>
          </li>
        </ul>
      </div>
    </main>

    <footer class="app-footer">
      <p>專案二 第二堂課 Vue 3 Composition API 範例專案 | Created with ❤️ for 蘇品樺</p>
    </footer>
  </div>
</template>

<style scoped>
.app-wrapper {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

main {
  flex: 1;
}

.intro-box {
  background: white;
  border-left: 4px solid var(--primary);
  border-radius: 12px;
  padding: 1.5rem;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.intro-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: #0f172a;
  margin-bottom: 0.5rem;
}

.intro-desc {
  font-size: 0.9rem;
  color: #475569;
  margin-bottom: 0.75rem;
}

.feature-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
  font-size: 0.85rem;
  color: #334155;
  padding-left: 0.5rem;
}

.feedback-logs {
  background: #fafafa;
  border-color: #cbd5e1;
}

.log-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: 1rem;
}

.log-item {
  font-size: 0.85rem;
  background: white;
  padding: 0.6rem 0.8rem;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}

.log-time {
  color: #94a3b8;
  font-family: var(--font-mono);
  font-size: 0.75rem;
  margin-right: 0.5rem;
}

.log-text {
  color: #0f172a;
  font-style: italic;
}

.app-footer {
  text-align: center;
  padding: 2rem 1rem;
  font-size: 0.75rem;
  color: #94a3b8;
  border-top: 1px solid #e2e8f0;
  margin-top: 3rem;
  background: white;
}
</style>
