<template>
    <div class="chart-container">
        <div class="background-animation">
            <div class="gradient-orb orb-1"></div>
            <div class="gradient-orb orb-2"></div>
            <div class="gradient-orb orb-3"></div>
        </div>

        <div class="container-fluid px-4 py-5">
            <div class="text-center mb-5 fade-in">
                <h1 class="display-4 fw-bold text-white mb-3">
                    <span class="gradient-text">📊 每日熱量統計</span>
                </h1>
                <p class="lead text-white-50">追蹤您的每日熱量攝取趨勢</p>
            </div>

            <div class="glass-card control-panel mb-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex gap-2">
                        <button v-for="period in periods" 
                                :key="period.days"
                                class="btn-period" 
                                :class="{ active: currentDays === period.days }"
                                @click="loadData(period.days)">
                            {{ period.label }}
                        </button>
                    </div>
                    <div class="stats-summary">
                        <div class="stat-item">
                            <span class="stat-label">平均每日</span>
                            <span class="stat-value">{{ avgCalories }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">總熱量</span>
                            <span class="stat-value">{{ totalCalories }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="glass-card chart-card">
                <div class="card-header-custom">
                    <h3 class="mb-0">
                        <span class="icon-wrapper">📈</span>
                        熱量趨勢圖
                    </h3>
                </div>
                <div class="card-body-custom">
                    <div class="chart-wrapper">
                        <canvas ref="chartCanvas"></canvas>
                    </div>
                </div>
            </div>

            <div class="glass-card table-card mt-4">
                <div class="card-header-custom">
                    <h3 class="mb-0">
                        <span class="icon-wrapper">📋</span>
                        詳細記錄
                    </h3>
                </div>
                <div class="card-body-custom">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th style="color: rgba(255, 255, 255, 0.9);">日期</th>
                                    <th style="color: rgba(255, 255, 255, 0.9);">熱量 (大卡)</th>
                                    <th style="color: rgba(255, 255, 255, 0.9);">狀態</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loading">
                                    <td colspan="3" class="text-center text-muted">載入中...</td>
                                </tr>
                                <tr v-else-if="chartData.length === 0">
                                    <td colspan="3" class="text-center text-muted">尚無數據</td>
                                </tr>
                                <tr v-else v-for="(item, index) in chartData" :key="index">
                                    <td style="color: rgba(255, 255, 255, 0.9);">{{ formatDate(item.date) }}</td>
                                    <td style="color: rgba(255, 255, 255, 0.9);">
                                        <strong>{{ item.calories.toLocaleString() }}</strong> 大卡
                                    </td>
                                    <td>
                                        <span :class="getStatusClass(item.calories)">{{ getStatusText(item.calories) }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

export default {
    name: 'CaloriesChart',
    data() {
        return {
            chart: null,
            chartData: [],
            currentDays: 7,
            loading: false,
            periods: [
                { days: 7, label: '最近 7 天' },
                { days: 14, label: '最近 14 天' },
                { days: 30, label: '最近 30 天' },
                { days: 90, label: '最近 90 天' }
            ]
        };
    },
    computed: {
        avgCalories() {
            if (this.chartData.length === 0) return '-';
            const total = this.chartData.reduce((sum, item) => sum + item.calories, 0);
            const avg = total / this.chartData.length;
            return Math.round(avg).toLocaleString() + ' 大卡';
        },
        totalCalories() {
            if (this.chartData.length === 0) return '-';
            const total = this.chartData.reduce((sum, item) => sum + item.calories, 0);
            return total.toLocaleString() + ' 大卡';
        }
    },
    mounted() {
        this.loadData(7);
    },
    methods: {
        async loadData(days) {
            this.currentDays = days;
            this.loading = true;
            
            try {
                const response = await axios.get('/api/calories/daily', {
                    params: { days }
                });
                
                if (response.data.success) {
                    this.chartData = response.data.data;
                    this.updateChart();
                }
            } catch (error) {
                console.error('載入數據失敗:', error);
            } finally {
                this.loading = false;
            }
        },
        updateChart() {
            if (this.chart) {
                this.chart.destroy();
            }
            
            const ctx = this.$refs.chartCanvas;
            if (!ctx) return;
            
            const labels = this.chartData.map(item => {
                const date = new Date(item.date);
                return date.toLocaleDateString('zh-TW', { month: 'short', day: 'numeric' });
            });
            
            const calories = this.chartData.map(item => item.calories);
            
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: '每日熱量 (大卡)',
                        data: calories,
                        borderColor: 'rgb(102, 126, 234)',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: 'rgb(102, 126, 234)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: 'rgba(255, 255, 255, 0.9)',
                                font: { size: 14, weight: 'bold' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgb(102, 126, 234)',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return '熱量: ' + context.parsed.y.toLocaleString() + ' 大卡';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: 'rgba(255, 255, 255, 0.7)', font: { size: 12 } },
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)',
                                font: { size: 12 },
                                callback: function(value) {
                                    return value.toLocaleString() + ' 大卡';
                                }
                            },
                            grid: { color: 'rgba(255, 255, 255, 0.1)' }
                        }
                    }
                }
            });
        },
        formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('zh-TW', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                weekday: 'short'
            });
        },
        getStatusClass(calories) {
            if (calories === 0) return 'text-muted';
            if (calories < 1200) return 'text-info';
            if (calories > 2500) return 'text-warning';
            return 'text-success';
        },
        getStatusText(calories) {
            if (calories === 0) return '無記錄';
            if (calories < 1200) return '偏低';
            if (calories > 2500) return '偏高';
            return '正常';
        }
    },
    beforeUnmount() {
        if (this.chart) {
            this.chart.destroy();
        }
    }
};
</script>

<style scoped>
.chart-container {
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
    padding: 2rem 0;
}

.control-panel {
    padding: 1.5rem 2rem;
}

.btn-period {
    padding: 0.5rem 1.5rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
}

.btn-period:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
}

.btn-period.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: transparent;
    color: white;
}

.stats-summary {
    display: flex;
    gap: 2rem;
}

.stat-item {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.stat-label {
    color: rgba(255, 255, 255, 0.7);
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}

.stat-value {
    color: white;
    font-size: 1.5rem;
    font-weight: 700;
}

.chart-wrapper {
    height: 400px;
    position: relative;
}

.table-card .table {
    color: rgba(255, 255, 255, 0.9) !important;
    margin-bottom: 0;
}

.table-card .table thead th {
    border-bottom: 2px solid rgba(255, 255, 255, 0.2);
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 600;
    padding: 1rem;
}

.table-card .table tbody td {
    padding: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    vertical-align: middle;
    color: rgba(255, 255, 255, 0.9) !important;
}

.table-card .table tbody tr:hover {
    background: rgba(255, 255, 255, 0.05);
}

.text-info {
    color: #0dcaf0 !important;
}

.text-success {
    color: #198754 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-muted {
    color: rgba(255, 255, 255, 0.6) !important;
}

@media (max-width: 768px) {
    .chart-container {
        padding: 1rem 0;
    }
    
    .control-panel {
        padding: 1rem;
    }
    
    .stats-summary {
        flex-direction: column;
        gap: 1rem;
        width: 100%;
        margin-top: 1rem;
    }
    
    .stat-item {
        align-items: flex-start;
    }
    
    .chart-wrapper {
        height: 300px;
    }
}
</style>

