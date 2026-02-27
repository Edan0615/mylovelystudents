<template>
    <div class="food-analyzer-component">
        <div class="container-fluid px-4 py-5">
            <!-- 主標題區域 -->
            <div class="text-center mb-5 fade-in">
                <h1 class="display-3 fw-bold text-dark mb-3">
                    <span class="gradient-text">AI 食物熱量分析</span>
                </h1>
                <p class="lead text-muted">拍攝或上傳食物照片，立即獲得專業的營養分析</p>
            </div>

            <!-- 主要內容區域 -->
            <div class="row g-4 justify-content-center">
                <!-- 左側：上傳區域 -->
                <div class="col-lg-5">
                    <div class="clean-card upload-card h-100">
                        <div class="card-header-custom border-bottom p-4">
                            <h3 class="mb-0 fs-4 fw-bold text-dark">
                                <span class="me-2">📷</span>
                                上傳食物照片
                            </h3>
                        </div>
                        <div class="card-body-custom p-4">
                            <!-- 上傳區域 -->
                            <div class="upload-zone" 
                                 :class="{ 'drag-over': isDragOver }"
                                 @click="triggerFileInput"
                                 @dragover.prevent="handleDragOver"
                                 @dragleave="handleDragLeave"
                                 @drop.prevent="handleDrop">
                                <div class="upload-content">
                                    <div class="upload-icon mb-3">📸</div>
                                    <h5 class="mb-2 fw-bold text-dark">拖放圖片或點擊上傳</h5>
                                    <p class="text-muted small mb-0">支援 JPG, PNG, GIF (最大 10MB)</p>
                                </div>
                                <input type="file" 
                                       ref="fileInput" 
                                       @change="handleFileSelect" 
                                       accept="image/*" 
                                       class="d-none">
                            </div>

                            <!-- 或使用相機 -->
                            <div class="divider my-4 text-muted">
                                <span>或</span>
                            </div>

                            <!-- 拍照按鈕 -->
                            <button type="button" class="btn btn-outline-primary w-100 py-3 rounded-3 fw-bold mb-3 d-flex align-items-center justify-content-center gap-2" @click="openCamera">
                                <span>📷</span>
                                <span>開啟相機拍照</span>
                            </button>

                            <!-- 圖片預覽 -->
                            <div v-if="previewImage" class="image-preview-container position-relative mb-3">
                                <div class="preview-wrapper rounded-3 overflow-hidden shadow-sm">
                                    <img :src="previewImage" alt="預覽" class="w-100 d-block object-fit-cover" style="max-height: 300px;">
                                    <button type="button" class="btn-remove position-absolute top-0 end-0 m-2 btn btn-danger rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" @click="removeImage">
                                        <span>×</span>
                                    </button>
                                </div>
                            </div>

                            <!-- 分析按鈕 -->
                            <button type="button" 
                                    class="btn btn-primary w-100 py-3 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" 
                                    :disabled="!imageFile || isAnalyzing"
                                    @click="analyzeFood">
                                <span v-if="isAnalyzing" class="spinner-border spinner-border-sm" role="status"></span>
                                <span v-else>🔍</span>
                                <span>{{ isAnalyzing ? '分析中...' : '分析食物熱量' }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 右側：結果區域 -->
                <div class="col-lg-7">
                    <div class="clean-card result-card h-100">
                        <div class="card-header-custom border-bottom p-4">
                            <h3 class="mb-0 fs-4 fw-bold text-dark">
                                <span class="me-2">📊</span>
                                分析結果
                            </h3>
                        </div>
                        <div class="card-body-custom p-4 result-body">
                            <div v-if="!analysisResult" class="result-placeholder d-flex flex-column align-items-center justify-content-center text-muted" style="min-height: 300px;">
                                <div class="fs-1 mb-3">🍽️</div>
                                <p>上傳圖片後點擊「分析食物熱量」按鈕</p>
                            </div>
                            <div v-else class="result-content">
                                <div v-if="analysisResult.success">
                                    <div class="result-header mb-4">
                                        <div class="badge bg-success bg-gradient px-4 py-2 rounded-pill fs-6 fw-normal">
                                            <span class="me-1">✅</span> 分析完成
                                        </div>
                                    </div>
                                    <div class="result-layout">
                                        <div class="analysis-section mb-4">
                                            <h4 class="section-title border-bottom pb-2 mb-3 text-dark fw-bold">📝 詳細分析</h4>
                                            <div class="analysis-content text-dark lh-lg" v-html="formattedAnalysis"></div>
                                        </div>
                                        <div v-if="analysisResult.foods && analysisResult.foods.length > 0" class="foods-section">
                                            <h4 class="section-title border-bottom pb-2 mb-3 text-dark fw-bold">🍽️ 食物熱量清單</h4>
                                            <div class="food-items d-flex flex-column gap-2 mb-4">
                                                <div v-for="(food, index) in analysisResult.foods" :key="index" class="food-item bg-light p-3 rounded-3 d-flex align-items-center justify-content-between border">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">{{ index + 1 }}</span>
                                                        <span class="fw-bold text-dark">{{ food.name || '未知食物' }}</span>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="fs-5 fw-bold text-primary">{{ food.calories || 0 }}</span>
                                                        <span class="text-muted small ms-1">大卡</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="analysisResult.total_calories" class="total-calories bg-primary bg-gradient text-white p-4 rounded-4 text-center shadow-sm">
                                                <div class="text-uppercase small opacity-75 mb-1">總熱量</div>
                                                <div class="display-4 fw-bold">{{ Math.round(analysisResult.total_calories) }} <span class="fs-4">大卡</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="alert alert-danger rounded-3 border-0 shadow-sm">
                                    <strong>❌ 分析失敗</strong><br>
                                    {{ analysisResult.message || '請稍後再試' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 相機 Modal -->
        <div class="modal fade" 
             :class="{ 'show': showCameraModal, 'd-block': showCameraModal }" 
             id="cameraModal" 
             tabindex="-1" 
             v-if="showCameraModal"
             @click.self="closeCamera">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">📷 拍照</h5>
                        <button type="button" class="btn-close" @click="closeCamera" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-4">
                        <div class="rounded-4 overflow-hidden bg-black position-relative" style="min-height: 300px;">
                            <video ref="videoElement" autoplay playsinline class="w-100 h-100 object-fit-cover"></video>
                        </div>
                        <canvas ref="canvasElement" class="d-none"></canvas>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 justify-content-center">
                        <button type="button" class="btn btn-secondary px-4 rounded-pill" @click="closeCamera">取消</button>
                        <button type="button" class="btn btn-primary px-5 rounded-pill" @click="capturePhoto">
                            <span class="me-2">📷</span>拍照
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showCameraModal" class="modal-backdrop fade show" @click="closeCamera"></div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'FoodAnalyzer',
    data() {
        return {
            imageFile: null,
            previewImage: null,
            isDragOver: false,
            isAnalyzing: false,
            analysisResult: null,
            showCameraModal: false,
            stream: null,
        };
    },
    computed: {
        formattedAnalysis() {
            if (!this.analysisResult?.analysis) return '';
            
            let cleanAnalysis = this.analysisResult.analysis;
            // 移除 JSON 部分
            cleanAnalysis = cleanAnalysis.replace(/\[[\s\S]*?\]/g, '');
            cleanAnalysis = cleanAnalysis.replace(/\{[\s\S]*?"name"[\s\S]*?"calories"[\s\S]*?\}/g, '');
            
            // 格式化
            return cleanAnalysis
                .replace(/###\s*(.+?)(?=\n|$)/g, '<h4 style="color: #212529; font-weight: 700; margin-top: 1.5rem; margin-bottom: 0.75rem;">$1</h4>')
                .replace(/##\s*(.+?)(?=\n|$)/g, '<h3 style="color: #212529; font-weight: 700; margin-top: 2rem; margin-bottom: 1rem;">$1</h3>')
                .replace(/\d+\.\s*(.+?)(?=\n|$)/g, '<p style="color: #495057; margin-bottom: 0.75rem; line-height: 1.8;"><strong style="color: #212529;">$1</strong></p>')
                .replace(/\n/g, '<br>');
        }
    },
    methods: {
        triggerFileInput() {
            this.$refs.fileInput?.click();
        },
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.handleImageFile(file);
            }
        },
        handleDragOver(event) {
            event.preventDefault();
            this.isDragOver = true;
        },
        handleDragLeave() {
            this.isDragOver = false;
        },
        handleDrop(event) {
            this.isDragOver = false;
            const file = event.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                this.handleImageFile(file);
            }
        },
        handleImageFile(file) {
            this.imageFile = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.previewImage = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        removeImage() {
            this.imageFile = null;
            this.previewImage = null;
            this.analysisResult = null;
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },
        async openCamera() {
            this.showCameraModal = true;
            await this.$nextTick();
            
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                if (this.$refs.videoElement) {
                    this.$refs.videoElement.srcObject = this.stream;
                }
            } catch (error) {
                alert('無法開啟相機：' + error.message);
                this.closeCamera();
            }
        },
        closeCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }
            this.showCameraModal = false;
        },
        capturePhoto() {
            const video = this.$refs.videoElement;
            const canvas = this.$refs.canvasElement;
            
            if (!video || !canvas) return;
            
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0);
            
            canvas.toBlob((blob) => {
                this.imageFile = new File([blob], 'photo.jpg', { type: 'image/jpeg' });
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImage = e.target.result;
                };
                reader.readAsDataURL(blob);
                this.closeCamera();
            }, 'image/jpeg', 0.9);
        },
        async analyzeFood() {
            if (!this.imageFile) return;
            
            this.isAnalyzing = true;
            this.analysisResult = null;
            
            const formData = new FormData();
            formData.append('image', this.imageFile);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            
            try {
                const response = await axios.post('/api/food/analyze', formData);
                this.analysisResult = response.data;
            } catch (error) {
                this.analysisResult = {
                    success: false,
                    message: error.response?.data?.message || error.message || '發生錯誤'
                };
            } finally {
                this.isAnalyzing = false;
            }
        }
    },
    beforeUnmount() {
        this.closeCamera();
    }
};
</script>

<style scoped>
.clean-card {
    background: #ffffff;
    border-radius: 1rem;
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
}

.upload-zone {
    border: 2px dashed #dee2e6;
    border-radius: 1rem;
    padding: 3rem 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.upload-zone:hover,
.upload-zone.drag-over {
    border-color: #4e73df;
    background: #e9ecef;
}

.upload-icon {
    font-size: 4rem;
    display: block;
}

.divider {
    text-align: center;
    position: relative;
    font-size: 0.9rem;
}

.divider::before,
.divider::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 45%;
    height: 1px;
    background: #dee2e6;
}

.divider::before {
    left: 0;
}

.divider::after {
    right: 0;
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

.gradient-text {
    background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
</style>
