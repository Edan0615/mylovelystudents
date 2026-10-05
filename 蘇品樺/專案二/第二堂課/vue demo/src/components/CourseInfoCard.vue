<script setup>
import { ref } from 'vue'

// 1. 宣告接收的 Props (從父組件傳入)
const props = defineProps({
  courseTitle: {
    type: String,
    required: true
  },
  instructor: {
    type: String,
    default: '講師'
  },
  studentName: {
    type: String,
    default: '蘇品樺'
  },
  likes: {
    type: Number,
    default: 0
  }
})

// 2. 宣告發送的 Emits (自訂事件回傳給父組件)
const emit = defineEmits(['like-click', 'feedback-sent'])

const comment = ref('')

const sendFeedback = () => {
  if (!comment.value.trim()) return
  // 發送事件帶參數給父組件
  emit('feedback-sent', {
    student: props.studentName,
    text: comment.value.trim(),
    time: new Date().toLocaleTimeString()
  })
  comment.value = ''
}
</script>

<template>
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">
        <span class="icon">🎓</span> 3. 組件溝通 (Props & Emits)
      </h2>
      <span class="badge">父子通訊</span>
    </div>

    <p class="card-description">
      展示父子組件資料傳遞：父傳子使用 <code>props</code>，子傳父發送事件使用 <code>emit</code>。
    </p>

    <!-- 課程卡片內容 -->
    <div class="course-badge-card">
      <div class="course-meta">
        <span class="course-tag">專案二 · 第二堂課</span>
        <h3 class="course-name">{{ courseTitle }}</h3>
        <p class="student-info">學生姓名：<strong>{{ studentName }}</strong> | 授課講師：{{ instructor }}</p>
      </div>

      <!-- 按讚按鈕 (觸發 emit) -->
      <button class="like-button" @click="emit('like-click')">
        ❤️ 給這堂課點讚 ({{ likes }})
      </button>
    </div>

    <!-- 反饋輸入 (子傳父 emit 測試) -->
    <div class="feedback-section">
      <label class="feedback-label">課堂學習心得或發問 (會發送 emit 給父組件)：</label>
      <div class="feedback-input-group">
        <input 
          v-model="comment" 
          type="text" 
          placeholder="寫下你的學習心得..." 
          class="input"
          @keyup.enter="sendFeedback"
        >
        <button class="btn btn-secondary" @click="sendFeedback">
          送出 Emit
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.course-badge-card {
  background: linear-gradient(135deg, #f0fdf4 0%, #e6f4ea 100%);
  border: 1px solid #bbf7d0;
  border-radius: 12px;
  padding: 1.25rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.25rem;
}

.course-tag {
  display: inline-block;
  font-size: 0.7rem;
  font-weight: 700;
  color: #15803d;
  background: #dcfce7;
  padding: 0.15rem 0.5rem;
  border-radius: 4px;
  margin-bottom: 0.25rem;
}

.course-name {
  font-size: 1.1rem;
  font-weight: 800;
  color: #0f172a;
}

.student-info {
  font-size: 0.8rem;
  color: #475569;
}

.like-button {
  background: #ffffff;
  border: 1px solid #86efac;
  color: #166534;
  font-weight: 700;
  font-size: 0.85rem;
  padding: 0.6rem 1.2rem;
  border-radius: 9999px;
  cursor: pointer;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease;
}

.like-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 10px rgba(34, 197, 94, 0.2);
  background: #f0fdf4;
}

.feedback-section {
  background: #f8fafc;
  padding: 1rem;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.feedback-label {
  display: block;
  font-size: 0.8rem;
  font-weight: 600;
  color: #475569;
  margin-bottom: 0.5rem;
}

.feedback-input-group {
  display: flex;
  gap: 0.5rem;
}
</style>
