<template>
    <div class="analyses-container py-5">
        <div class="container px-4">
            <div class="text-center mb-5 fade-in">
                <h1 class="display-4 fw-bold text-dark mb-3">
                    <span class="gradient-text">📋 分析記錄</span>
                </h1>
                <p class="lead text-muted">管理您的所有食物分析記錄</p>
            </div>

            <div v-if="loading" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="text-muted mt-3">載入中...</p>
            </div>

            <div v-else-if="analyses.length > 0" class="row g-4">
                <div v-for="analysis in analyses" :key="analysis.id" class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden simple-card">
                        <button class="btn-delete" @click="deleteAnalysis(analysis.id)" title="刪除">
                            <span>×</span>
                        </button>

                        <div v-if="analysis.image_path" class="card-img-top position-relative" style="height: 200px; background-color: #f8f9fa;">
                            <img :src="getImageUrl(analysis.image_path)" alt="食物圖片" class="w-100 h-100 object-fit-cover">
                        </div>

                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex align-items-center text-muted small mb-3">
                                <span class="me-2">📅</span>
                                <span>{{ formatDate(analysis.created_at) }}</span>
                            </div>

                            <div v-if="analysis.total_calories" class="mb-3">
                                <div class="d-flex align-items-baseline">
                                    <span class="fs-2 fw-bold text-primary me-1">{{ formatNumber(analysis.total_calories) }}</span>
                                    <span class="text-muted small">大卡</span>
                                </div>
                            </div>

                            <div v-if="analysis.foods && analysis.foods.length > 0" class="d-flex align-items-center text-dark fw-bold mb-3 small">
                                <span class="me-2">🍽️</span>
                                <span>{{ analysis.foods.length }} 種食物</span>
                            </div>

                            <div class="mt-auto pt-3 border-top">
                                <div class="d-flex flex-wrap gap-2">
                                    <template v-if="analysis.foods && analysis.foods.length > 0">
                                        <span v-for="(food, index) in analysis.foods.slice(0, 3)" 
                                              :key="index" 
                                              class="badge bg-light text-dark border fw-normal">
                                            {{ food.name || '未知' }}
                                        </span>
                                        <span v-if="analysis.foods.length > 3" class="badge bg-light text-primary border fw-normal">
                                            +{{ analysis.foods.length - 3 }}
                                        </span>
                                    </template>
                                    <span v-else class="text-muted small">無食物數據</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="card border-0 shadow-sm rounded-4 p-5 text-center mt-4">
                <div class="py-4">
                    <div class="fs-1 mb-3 text-muted">📭</div>
                    <h3 class="text-dark fw-bold mb-2">尚無分析記錄</h3>
                    <p class="text-muted mb-4">開始上傳食物照片來建立您的第一筆記錄吧！</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'FoodAnalysesList',
    data() {
        return {
            analyses: [],
            loading: true
        };
    },
    mounted() {
        this.loadAnalyses();
    },
    methods: {
        async loadAnalyses() {
            this.loading = true;
            try {
                const response = await axios.get('/api/food-analyses', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (response.data.success) {
                    this.analyses = response.data.data || [];
                }
            } catch (error) {
                console.error('載入記錄失敗:', error);
                this.analyses = [];
            } finally {
                this.loading = false;
            }
        },
        async deleteAnalysis(id) {
            if (!confirm('確定要刪除這筆記錄嗎？此操作無法復原。')) {
                return;
            }

            try {
                const response = await axios.delete(`/food-analyses/${id}`);
                if (response.data.success) {
                    this.analyses = this.analyses.filter(a => a.id !== id);
                } else {
                    alert('刪除失敗：' + (response.data.message || '未知錯誤'));
                }
            } catch (error) {
                console.error('刪除錯誤:', error);
                alert('刪除失敗，請稍後再試');
            }
        },
        getImageUrl(path) {
            return `/storage/${path}`;
        },
        formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleString('zh-TW', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        formatNumber(num) {
            return Number(num).toLocaleString();
        }
    }
};
</script>

<style scoped>
.simple-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.simple-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0,0,0,0.1) !important;
}

.btn-delete {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid #dee2e6;
    color: #dc3545;
    font-size: 20px;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    transition: all 0.2s ease;
    opacity: 0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.simple-card:hover .btn-delete {
    opacity: 1;
}

.btn-delete:hover {
    background: #dc3545;
    color: white;
    transform: scale(1.1);
}

.gradient-text {
    background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.fade-in {
    animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
