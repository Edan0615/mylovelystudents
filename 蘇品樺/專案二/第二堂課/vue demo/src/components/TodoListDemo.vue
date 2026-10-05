<script setup>
import { ref, computed, watch, onMounted } from 'vue'

// 1. Todo List 狀態管理 (使用 localStorage 自動存取)
const STORAGE_KEY = 'vue-demo-todo-list'

const todos = ref([
  { id: 1, text: '學習 Vue 3 Composition API 基礎觀點', done: true },
  { id: 2, text: '理解 ref() 與 reactive() 差異', done: true },
  { id: 3, text: '實作 Single File Component (SFC) 組件拆分', done: false },
  { id: 4, text: '理解 Vite 的熱重載 (HMR) 速度優勢', done: false }
])

const newTodoText = ref('')
const currentFilter = ref('all') // 'all' | 'active' | 'completed'

// 2. 從 localStorage 讀取初始化資料
onMounted(() => {
  const saved = localStorage.getItem(STORAGE_KEY)
  if (saved) {
    try {
      todos.value = JSON.parse(saved)
    } catch (e) {
      console.error('解析 localStorage 失敗:', e)
    }
  }
})

// 3. 自動寫入 localStorage (deep watch 監聽陣列內部物件變更)
watch(
  todos,
  (newVal) => {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(newVal))
  },
  { deep: true }
)

// 4. 計算屬性 (Filter & Stats)
const filteredTodos = computed(() => {
  if (currentFilter.value === 'active') {
    return todos.value.filter(t => !t.done)
  }
  if (currentFilter.value === 'completed') {
    return todos.value.filter(t => t.done)
  }
  return todos.value
})

const remainingCount = computed(() => {
  return todos.value.filter(t => !t.done).length
})

const completedCount = computed(() => {
  return todos.value.filter(t => t.done).length
})

// 5. 操作方法 (Methods)
const addTodo = () => {
  const text = newTodoText.value.trim()
  if (!text) return
  
  todos.value.unshift({
    id: Date.now(),
    text,
    done: false
  })
  
  newTodoText.value = ''
}

const removeTodo = (id) => {
  todos.value = todos.value.filter(t => t.id !== id)
}

const clearCompleted = () => {
  todos.value = todos.value.filter(t => !t.done)
}
</script>

<template>
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">
        <span class="icon">📝</span> 2. 待辦事項清單 (v-model / v-for / v-if / Computed / LocalStorage)
      </h2>
      <span class="badge">雙向綁定</span>
    </div>

    <p class="card-description">
      展示列表渲染 <code>v-for</code>、雙向資料綁定 <code>v-model</code>、分類篩選計算 <code>computed</code> 與資料持久化。
    </p>

    <!-- 輸入區 -->
    <form class="add-todo-form" @submit.prevent="addTodo">
      <input 
        v-model="newTodoText"
        type="text" 
        placeholder="輸入欲學習或執行的任務..."
        class="input todo-input"
      >
      <button type="submit" class="btn btn-primary">
        + 新增任務
      </button>
    </form>

    <!-- 篩選 Tabs & 狀態列 -->
    <div class="toolbar">
      <div class="filter-tabs">
        <button 
          class="tab-btn" 
          :class="{ active: currentFilter === 'all' }"
          @click="currentFilter = 'all'"
        >
          全部 ({{ todos.length }})
        </button>
        <button 
          class="tab-btn" 
          :class="{ active: currentFilter === 'active' }"
          @click="currentFilter = 'active'"
        >
          進行中 ({{ remainingCount }})
        </button>
        <button 
          class="tab-btn" 
          :class="{ active: currentFilter === 'completed' }"
          @click="currentFilter = 'completed'"
        >
          已完成 ({{ completedCount }})
        </button>
      </div>

      <button 
        v-if="completedCount > 0" 
        class="btn btn-secondary btn-sm" 
        @click="clearCompleted"
      >
        清除已完成項
      </button>
    </div>

    <!-- 列表展示 -->
    <ul v-if="filteredTodos.length > 0" class="todo-list">
      <li 
        v-for="item in filteredTodos" 
        :key="item.id" 
        class="todo-item"
        :class="{ done: item.done }"
      >
        <label class="todo-checkbox-label">
          <input 
            v-model="item.done" 
            type="checkbox" 
            class="checkbox"
          >
          <span class="todo-text">{{ item.text }}</span>
        </label>
        
        <button 
          class="delete-btn" 
          title="刪除" 
          @click="removeTodo(item.id)"
        >
          ✕
        </button>
      </li>
    </ul>

    <!-- 無資料提示 -->
    <div v-else class="empty-state">
      <p>🎉 目前沒有符合這個條件的待辦事項！</p>
    </div>
  </div>
</template>

<style scoped>
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.add-todo-form {
  display: flex;
  gap: 0.5rem;
  margin: 1.25rem 0;
}

.todo-input {
  flex: 1;
}

.toolbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #e2e8f0;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.filter-tabs {
  display: flex;
  gap: 0.25rem;
  background: #f1f5f9;
  padding: 0.25rem;
  border-radius: 8px;
}

.tab-btn {
  border: none;
  background: transparent;
  padding: 0.35rem 0.75rem;
  font-size: 0.75rem;
  font-weight: 600;
  color: #64748b;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.tab-btn.active {
  background: white;
  color: #0f172a;
  box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.btn-sm {
  font-size: 0.75rem;
  padding: 0.35rem 0.75rem;
}

.todo-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.todo-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.75rem 1rem;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.todo-item:hover {
  background: #f1f5f9;
}

.todo-item.done {
  opacity: 0.65;
  background: #fafafa;
}

.todo-checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  cursor: pointer;
  flex: 1;
}

.checkbox {
  width: 1.1rem;
  height: 1.1rem;
  accent-color: #42b883;
  cursor: pointer;
}

.todo-text {
  font-size: 0.9rem;
  color: #1e293b;
}

.todo-item.done .todo-text {
  text-decoration: line-through;
  color: #64748b;
}

.delete-btn {
  border: none;
  background: transparent;
  color: #94a3b8;
  font-size: 1rem;
  cursor: pointer;
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.delete-btn:hover {
  color: #ef4444;
  background: #fee2e2;
}

.empty-state {
  text-align: center;
  padding: 2rem;
  color: #94a3b8;
  font-size: 0.875rem;
  background: #f8fafc;
  border-radius: 8px;
  border: 1px dashed #cbd5e1;
}
</style>
