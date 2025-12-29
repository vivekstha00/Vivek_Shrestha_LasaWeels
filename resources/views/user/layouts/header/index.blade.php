<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LasaWheels</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root{
            --brand-primary: #0F172A;
            --bg-secondary: #F5F7FA;
            --btn-primary: #F59E0B;
            --btn-primary-hover: #D97706;
            --btn-secondary: #334155;
            --text-primary: #020617;
        }

        body{ color: var(--text-primary); }

        .site-navbar{ background: var(--brand-primary); }

        .btn-main{
            background: var(--btn-primary);
            border-color: var(--btn-primary);
            color: #fff;
        }
        .btn-main:hover{
            background: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
            color: #fff;
        }

        .card-soft{
            border: 0;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(2,6,23,0.08);
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('js')
</head>
