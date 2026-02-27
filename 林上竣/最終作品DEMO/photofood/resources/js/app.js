import './bootstrap';
import { createApp } from 'vue';
import axios from 'axios';

// 設定 axios 預設值
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// 導入組件
import FoodAnalyzer from './components/FoodAnalyzer.vue';
import CaloriesChart from './components/CaloriesChart.vue';
import FoodAnalysesList from './components/FoodAnalysesList.vue';

// 等待 DOM 載入完成後初始化
document.addEventListener('DOMContentLoaded', () => {
    try {
        const app = createApp({
            data() {
                return {
                    currentView: 'analyzer'
                };
            },
            mounted() {
                console.log('Vue 應用程式已掛載，currentView:', this.currentView);
                // 檢查組件是否渲染
                this.$nextTick(() => {
                    const analyzer = document.querySelector('food-analyzer');
                    const list = document.querySelector('food-analyses-list');
                    const chart = document.querySelector('calories-chart');
                    console.log('組件檢查:', {
                        analyzer: analyzer ? '存在' : '不存在',
                        list: list ? '存在' : '不存在',
                        chart: chart ? '存在' : '不存在'
                    });
                });
            }
        });

        // 註冊組件
        try {
            app.component('food-analyzer', FoodAnalyzer);
            console.log('food-analyzer 組件已註冊');
        } catch (e) {
            console.error('food-analyzer 組件註冊失敗:', e);
        }
        
        try {
            app.component('calories-chart', CaloriesChart);
            console.log('calories-chart 組件已註冊');
        } catch (e) {
            console.error('calories-chart 組件註冊失敗:', e);
        }
        
        try {
            app.component('food-analyses-list', FoodAnalysesList);
            console.log('food-analyses-list 組件已註冊');
        } catch (e) {
            console.error('food-analyses-list 組件註冊失敗:', e);
        }

        // 掛載應用
        const vueInstance = app.mount('#app');
        
        // 將 Vue 實例暴露到全局，讓 navbar 可以訪問
        window.vueApp = vueInstance;
        
        console.log('Vue 應用程式初始化完成');
    } catch (error) {
        console.error('Vue 應用程式初始化失敗:', error);
    }
});
