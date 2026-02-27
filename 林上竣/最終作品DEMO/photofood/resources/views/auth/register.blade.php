@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <h2 class="fw-bold text-dark mb-2">{{ __('Create Account') }}</h2>
                            <p class="text-muted">{{ __('Join us to start analyzing your food') }}</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="mb-4">
                                <label for="name"
                                    class="form-label text-muted small fw-bold text-uppercase">{{ __('Name') }}</label>
                                <input id="name" type="text"
                                    class="form-control form-control-lg bg-light border-0 @error('name') is-invalid @enderror"
                                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                    placeholder="Your Name">

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="email"
                                    class="form-label text-muted small fw-bold text-uppercase">{{ __('Email Address') }}</label>
                                <input id="email" type="email"
                                    class="form-control form-control-lg bg-light border-0 @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email"
                                    placeholder="name@example.com">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password"
                                    class="form-label text-muted small fw-bold text-uppercase">{{ __('Password') }}</label>
                                <input id="password" type="password"
                                    class="form-control form-control-lg bg-light border-0 @error('password') is-invalid @enderror"
                                    name="password" required autocomplete="new-password" placeholder="••••••••">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password-confirm"
                                    class="form-label text-muted small fw-bold text-uppercase">{{ __('Confirm Password') }}</label>
                                <input id="password-confirm" type="password"
                                    class="form-control form-control-lg bg-light border-0" name="password_confirmation"
                                    required autocomplete="new-password" placeholder="••••••••">
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold py-3 rounded-3 shadow-sm">
                                    {{ __('Register') }}
                                </button>
                            </div>

                            <div class="text-center">
                                <p class="mb-0 text-muted">
                                    {{ __('Already have an account?') }}
                                    <a href="{{ route('login') }}"
                                        class="text-primary fw-bold text-decoration-none">{{ __('Login Here') }}</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-control:focus {
            box-shadow: none;
            background-color: #fff;
            border: 1px solid #dee2e6 !important;
        }

        .btn-primary {
            background-color: #4e73df;
            /* Modern blue */
            border-color: #4e73df;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4) !important;
        }
    </style>
@endsection