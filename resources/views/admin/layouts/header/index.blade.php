<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LasaWheels</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        :root{
            --brand-primary: #0F172A;
            --bg-secondary: #F5F7FA;
            --btn-primary: #F59E0B;
            --btn-primary-hover: #D97706;
            --btn-secondary: #334155;
            --text-primary: #020617;
        }

        body{
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .admin-navbar{
            background: var(--brand-primary);
        }

        .admin-sidebar{
            background: var(--brand-primary);
        }

        .admin-sidebar .nav-link{
            color: rgba(255,255,255,0.85);
            border-radius: 10px;
            padding: 10px 12px;
        }

        .admin-sidebar .nav-link:hover{
            background: rgba(255,255,255,0.12);
            color: #fff;
        }

        .admin-sidebar .nav-link.active{
            background: rgba(245,158,11,0.18);
            color: #fff;
        }

        .btn-admin-primary{
            background: var(--btn-primary);
            border-color: var(--btn-primary);
            color: #fff;
        }

        .btn-admin-primary:hover{
            background: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: #fff;
        }

        .btn-admin-secondary{
            background: transparent;
            border: 1px solid var(--btn-secondary);
            color: var(--btn-secondary);
        }

        .btn-admin-secondary:hover{
            background: #E5E7EB;
        }

        .admin-card{
            border-radius: 16px;
            border: none;
            box-shadow: 0 6px 18px rgba(2,6,23,0.08);
        }
    </style>
</head>
