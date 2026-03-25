<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Vendor Registration' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fc;
        }
        .register-wrapper {
            min-height: 100vh;
            padding: 40px 0;
        }
        .register-card {
            border: 0;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        }
        .step-circle {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }
        .step-active {
            background: #0d1b3d;
            color: #fff;
        }
        .step-done {
            background: #198754;
            color: #fff;
        }
        .step-pending {
            background: #e9ecef;
            color: #6c757d;
        }
        .upload-box {
            border: 2px dashed #ced4da;
            border-radius: 16px;
            padding: 20px;
            background: #fff;
        }
        .section-title {
            font-weight: 700;
            color: #0d1b3d;
        }
    </style>
</head>
<body>
    <div class="container register-wrapper">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="text-center mb-4">
                    <h1 class="fw-bold">Vendor Registration</h1>
                    <p class="text-muted mb-0">Join LasaWheels as a verified vehicle provider</p>
                </div>

                @include('auth.vendor-register.partials.progress', ['currentStep' => $currentStep ?? 1])

                @if(session('success'))
                    <div class="alert alert-success rounded-4 mt-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger rounded-4 mt-4">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card register-card mt-4">
                    <div class="card-body p-4 p-md-5">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
