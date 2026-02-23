<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LasaWheels Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .sidebar {
            width: 240px;
            background: #d7cec7;          /* main color - replaces #0F172A */
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
        }

        .main-content {
            margin-left: 240px;
            min-height: 100vh;
            background-color: #f7f5f3;    /* secondary color - added for main background */
        }

        .sidebar .nav-link {
            color: #212529;                /* dark text for contrast on light sidebar */
            border-radius: 10px;
            padding: 10px 12px;
        }

        .sidebar .nav-link:hover {
            background: #f7f5f3;           /* secondary color - replaces rgba(255,255,255,0.10) */
            color: #212529;
        }

        .sidebar .nav-link.active {
            background: #f7f5f3;           /* secondary color - replaces rgba(245,158,11,0.20) */
            color: #212529;
        }

        @media (max-width: 768px) {
            .sidebar { position: static; width: 100%; min-height: auto; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
