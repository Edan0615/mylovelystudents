@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
            <div class="col-md-8 text-center">
                <h1 class="display-3 fw-bold mb-4 text-dark">
                    歡迎來到 PhotoFood
                </h1>
                <p class="lead text-muted mb-5 fs-3">
                    透過 AI 智能分析，輕鬆紀錄您的每一餐。<br>
                    拍照、上傳，立即獲得熱量與營養資訊。
                </p>

                <div class="d-flex justify-content-center gap-3">
                    @auth
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm">
                            開始使用
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-bold shadow-sm">
                            立即登入
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-lg px-5 py-3 rounded-pill fw-bold">
                            註冊帳號
                        </a>
                    @endauth
                </div>

                <div class="row mt-5 pt-5 g-4">
                    <div class="col-md-4">
                        <div class="p-4 border-0 rounded-4 shadow-sm bg-white h-100">
                            <div class="fs-1 mb-3">📷</div>
                            <h3 class="h5 fw-bold text-dark">拍照識別</h3>
                            <p class="text-muted small mb-0">只需拍下食物照片，AI 自動幫您識別食物種類。</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 border-0 rounded-4 shadow-sm bg-white h-100">
                            <div class="fs-1 mb-3">⚡</div>
                            <h3 class="h5 fw-bold text-dark">熱量分析</h3>
                            <p class="text-muted small mb-0">精準計算熱量與營養成分，讓您吃得更健康。</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 border-0 rounded-4 shadow-sm bg-white h-100">
                            <div class="fs-1 mb-3">📊</div>
                            <h3 class="h5 fw-bold text-dark">健康紀錄</h3>
                            <p class="text-muted small mb-0">長期追蹤飲食習慣，生成可視化健康報表。</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
