<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Verifikasi OTP - Koleksi Buku</title>
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
        .otp-input {
            text-align: center;
            font-size: 24px;
            letter-spacing: 10px;
            font-weight: bold;
        }
        .verification-icon {
            font-size: 80px;
            color: #b66dff;
            margin-bottom: 20px;
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
                                <i class="mdi mdi-shield-lock-outline verification-icon"></i>
                                <h3>Verifikasi OTP</h3>
                                <p class="text-muted">Masukkan kode OTP 6 digit</p>
                            </div>
                            
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="mdi mdi-alert-circle"></i> {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="mdi mdi-check-circle"></i> {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="mdi mdi-alert"></i> {{ $errors->first() }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form class="pt-3" method="POST" action="{{ url('/verify-otp') }}">
                                @csrf
                                
                                <div class="form-group">
                                    <label class="text-center d-block mb-3">
                                        <small class="text-muted">
                                            <i class="mdi mdi-email-outline"></i> 
                                            Kode OTP telah disimpan di database. Silakan cek atau hubungi admin untuk mendapatkan kode OTP.
                                        </small>
                                    </label>
                                    <input type="text" 
                                           class="form-control form-control-lg otp-input @error('otp') is-invalid @enderror" 
                                           name="otp" 
                                           placeholder="000000" 
                                           maxlength="6" 
                                           pattern="[0-9]{6}"
                                           required 
                                           autofocus
                                           inputmode="numeric">
                                    @error('otp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mt-4 d-grid gap-2">
                                    <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                                        <i class="mdi mdi-check"></i> VERIFIKASI
                                    </button>
                                </div>

                                <div class="text-center mt-4">
                                    <a href="{{ url('/login') }}" class="text-muted">
                                        <i class="mdi mdi-arrow-left"></i> Kembali ke Login
                                    </a>
                                </div>

                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <i class="mdi mdi-information"></i> Kode OTP berlaku untuk sesi ini saja.
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
    <script>
        // Auto focus dan format input OTP
        document.querySelector('input[name="otp"]').addEventListener('input', function(e) {
            // Hanya izinkan angka
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
