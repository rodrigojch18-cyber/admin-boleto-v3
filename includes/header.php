<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Boleto Admin Pro — Dashboard de auditoría de boletos y participantes">
    <title><?= APP_NAME ?> — Dashboard</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                    colors: {
                        dark: {
                            900: '#0a0a0f',
                            800: '#12121a',
                            700: '#1a1a27',
                            600: '#222233',
                            500: '#2a2a3d',
                        },
                        accent: {
                            DEFAULT: '#6366f1',
                            light: '#818cf8',
                            dark: '#4f46e5',
                            glow: 'rgba(99, 102, 241, 0.3)',
                        },
                        success: '#10b981',
                        danger: '#ef4444',
                        warning: '#f59e0b',
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-dark-900 text-gray-100 font-sans antialiased min-h-screen">

    <!-- Navigation -->
    <nav class="sticky top-0 z-50 backdrop-blur-xl bg-dark-900/80 border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-accent to-purple-500 flex items-center justify-center shadow-lg shadow-accent/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight"><?= APP_NAME ?></h1>
                        <p class="text-[10px] text-gray-500 -mt-0.5 tracking-widest uppercase">Auditoría Dashboard</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-500">v<?= APP_VERSION ?></span>
                    <div class="w-2 h-2 rounded-full bg-success animate-pulse"></div>
                </div>
            </div>
        </div>
    </nav>
