<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - Koleksi Buku</title>
    <link rel="stylesheet" href="{{ asset('template/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
    <style>
        .auth .auth-form-light {
            background: #fff;
            padding: 40px;
        }
        .brand-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .brand-logo i {
            font-size: 60px;
            background: linear-gradient(to right, #da8cff, #9a55ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .brand-logo h3 {
            color: #6C7293;
            margin-top: 10px;
            font-weight: 500;
        }
        .form-control:focus {
            border-color: #b66dff;
            box-shadow: 0 0 0 0.2rem rgba(182, 109, 255, 0.25);
        }
    </style>
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth">
                <div class="row flex-grow">
                    <div class="col-lg-4 mx-auto">
                        <div class="auth-form-light text-left">
                            <div class="brand-logo">
                                <i class="mdi mdi-book-open-page-variant"></i>
                                <h3>Koleksi Buku</h3>
                            </div>
                            
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="mdi mdi-alert"></i> {{ $errors->first() }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form class="pt-3" method="POST" action="{{ route('login') }}">
                                @csrf
                                
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           name="email" placeholder="Masukkan email" value="{{ old('email') }}" required autofocus>
                                </div>
                                
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                           name="password" placeholder="Masukkan password" required>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <label class="form-check-label text-muted">
                                        <input type="checkbox" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }}> 
                                        Ingat saya
                                    </label>
                                </div>
                                
                                <div class="mt-3 d-grid gap-2">
                                    <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                                        LOGIN
                                    </button>
                                </div>

                                <div class="text-center my-3">
                                    <span class="text-muted">atau</span>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="{{ url('/auth/google') }}" class="btn btn-outline-danger btn-lg font-weight-medium auth-form-btn">
                                        <i class="mdi mdi-google"></i> Login dengan Google
                                    </a>
                                </div>

                                <div class="text-center mt-4 font-weight-light">
                                    <small class="text-muted">
                                        <i class="mdi mdi-information"></i> Silakan login dengan akun yang sudah terdaftar.
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('template/assets/vendors/js/vendor.bundle.base.js') }}"></script>
</body>
</html>
