<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>লগইন - কীটনাশক ও কৃষি ঔষধ ইনভেন্টরি সফটওয়্যার</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Hind Siliguri', sans-serif;
            background: linear-gradient(135deg, #052C1E 0%, #0F5132 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1rem 3rem rgba(0,0,0,0.3);
            overflow: hidden;
            width: 100%;
            max-width: 440px;
        }
        .login-header {
            background: #052C1E;
            color: #fff;
            padding: 2rem;
            text-align: center;
        }
        .btn-emerald {
            background: #0F5132;
            color: #fff;
            font-weight: 600;
        }
        .btn-emerald:hover {
            background: #198754;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="login-card card bg-white">
    <div class="login-header">
        <div class="mb-2 fs-1 text-warning">
            <i class="fa-solid fa-seedling"></i>
        </div>
        <h4 class="fw-bold mb-1">কীটনাশক ও কৃষি ঔষধ</h4>
        <small class="text-light opacity-75">Inventory Management Software</small>
    </div>
    
    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger mb-3 py-2 fs-7">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-semibold">ইমেইল এড্রেস (Email Address)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" value="admin@agromed.com" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">পাসওয়ার্ড (Password)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" value="password" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label fs-7" for="remember">মনে রাখুন</label>
                </div>
            </div>

            <button type="submit" class="btn btn-emerald w-100 py-2 shadow-sm">
                <i class="fa-solid fa-right-to-bracket me-1"></i> সফটওয়্যারে প্রবেশ করুন
            </button>
        </form>

        <div class="mt-4 pt-3 border-top text-center fs-8 text-muted">
            <p class="mb-1">ডেমো অ্যাকাউন্ট (Default Login):</p>
            <code>admin@agromed.com</code> | <code>password</code>
        </div>
    </div>
</div>

</body>
</html>
