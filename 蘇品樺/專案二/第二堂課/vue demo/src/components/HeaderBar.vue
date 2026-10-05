<script setup>
// Composition API: Props 宣告
defineProps({
  title: {
    type: String,
    default: 'Vue 3 Composition API 基礎專案 demo'
  },
  subtitle: {
    type: String,
    default: '蘇品樺 - 專案二 第二堂課'
  }
})
</script>

<template>
  <header class="header">
    <div class="header-content">
      <div class="logo-wrapper">
        <svg class="vue-logo" viewBox="0 0 128 128" width="40" height="40">
          <path fill="#42b883" d="M78.8,10L64,35.4L49.2,10H0l64,110L128,10H78.8z"/>
          <path fill="#35495e" d="M78.8,10L64,35.4L49.2,10H25.6L64,76.7L102.4,10H78.8z"/>
        </svg>
        <div>
          <h1 class="header-title">{{ title }}</h1>
          <p class="header-subtitle">{{ subtitle }}</p>
        </div>
      </div>
      <div class="tags">
        <span class="badge">Vue 3.x</span>
        <span class="badge">&lt;script setup&gt;</span>
        <span class="badge">Vite</span>
      </div>
    </div>
  </header>
</template>

<style scoped>
.header {
  background: white;
  border-bottom: 1px solid #e2e8f0;
  padding: 1.25rem 0;
  margin-bottom: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.header-content {
  max-width: 1080px;
  margin: 0 auto;
  padding: 0 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
}

.logo-wrapper {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.header-title {
  font-size: 1.25rem;
  font-weight: 800;
  color: #0f172a;
}

.header-subtitle {
  font-size: 0.85rem;
  color: #64748b;
}

.tags {
  display: flex;
  gap: 0.5rem;
}
</style>
