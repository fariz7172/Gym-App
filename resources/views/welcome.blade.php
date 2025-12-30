<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GymPro - Professional Fitness Management</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;700;900&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --primary: #6366F1;
            --primary-dark: #4F46E5;
            --secondary: #22D3EE;
            --accent: #F472B6;
            --bg-dark: #0F172A;
            --text-light: #F8FAFC;
            --text-gray: #94A3B8;
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-light);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }

        /* Hero Section */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: 
                radial-gradient(circle at 10% 20%, rgba(99, 102, 241, 0.2) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(34, 211, 238, 0.2) 0%, transparent 20%);
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1470&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            opacity: 0.15;
            z-index: -1;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .nav {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 10;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.5rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
        }

        .logo i {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
            border: 1px solid transparent;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.5);
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            backdrop-filter: blur(10px);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
        }

        /* Content */
        .hero-content {
            text-align: center;
            max-width: 800px;
        }
        
        .badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            color: var(--secondary);
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(5px);
        }

        .display-text {
            font-size: 4rem;
            line-height: 1.1;
            font-weight: 900;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #FFF 0%, #94A3B8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.25rem;
            color: var(--text-gray);
            margin-bottom: 2.5rem;
        }

        /* Features/Info Cards */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .info-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            padding: 2rem;
            border-radius: 20px;
            transition: transform 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary);
        }

        .icon-box {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(34, 211, 238, 0.1));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary);
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: white;
        }

        .card-text {
            color: var(--text-gray);
            font-size: 0.95rem;
        }

        .schedule-list {
            list-style: none;
            margin-top: 1rem;
        }

        .schedule-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: var(--text-gray);
            font-size: 0.9rem;
        }
        
        .schedule-item:last-child {
            border-bottom: none;
        }

        .schedule-day {
            color: white;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .display-text {
                font-size: 2.5rem;
            }
            .hero-content {
                padding: 0 1rem;
            }
            .nav {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="container animate-fade-in" style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
            <a href="{{ url('/') }}" class="logo">
                <i class="fas fa-dumbbell"></i>
                <span>GymPro</span>
            </a>
            @if (Route::has('login'))
                <div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline" style="margin-right: 0.5rem;">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            Member Login <i class="fas fa-arrow-right"></i>
                        </a>
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <span class="badge">#1 Fitness Management System</span>
                <h1 class="display-text">Transform Your Body,<br>Transform Your Life</h1>
                <p class="hero-desc">
                    Bergabunglah dengan GymPro untuk pengalaman fitness terbaik. 
                    Fasilitas lengkap, trainer profesional, dan komunitas yang mendukung.
                </p>
            </div>

            <div class="info-grid">
                <!-- Operating Hours -->
                <div class="info-card">
                    <div class="icon-box">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="card-title">Jam Operasional</h3>
                    <ul class="schedule-list">
                        <li class="schedule-item">
                            <span class="schedule-day">Senin - Jumat</span>
                            <span>06:00 - 22:00</span>
                        </li>
                        <li class="schedule-item">
                            <span class="schedule-day">Sabtu</span>
                            <span>07:00 - 20:00</span>
                        </li>
                        <li class="schedule-item">
                            <span class="schedule-day">Minggu & Libur</span>
                            <span>08:00 - 18:00</span>
                        </li>
                    </ul>
                </div>

                <!-- Features -->
                <div class="info-card">
                    <div class="icon-box">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="card-title">Fasilitas Premium</h3>
                    <p class="card-text" style="margin-bottom: 1rem;">
                        Nikmati peralatan fitness modern, ruang ganti nyaman dengan locker personal, 
                        shower air hangat, dan area parkir luas.
                    </p>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="font-size: 0.75rem; padding: 0.25rem 0.75rem; background: rgba(255,255,255,0.05); border-radius: 20px;">Free WIFI</span>
                        <span style="font-size: 0.75rem; padding: 0.25rem 0.75rem; background: rgba(255,255,255,0.05); border-radius: 20px;">AC</span>
                        <span style="font-size: 0.75rem; padding: 0.25rem 0.75rem; background: rgba(255,255,255,0.05); border-radius: 20px;">Sauna</span>
                    </div>
                </div>

                <!-- Contact -->
                <div class="info-card">
                    <div class="icon-box">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    <h3 class="card-title">Lokasi & Kontak</h3>
                    <p class="card-text" style="margin-bottom: 1rem;">
                        Jl. Gym Sehat No. 123<br>
                        Jakarta Selatan, Indonesia
                    </p>
                    <p class="card-text">
                        <i class="fas fa-phone-alt" style="margin-right: 0.5rem; color: var(--secondary);"></i> +62 812-3456-7890
                    </p>
                    <p class="card-text">
                        <i class="fab fa-whatsapp" style="margin-right: 0.5rem; color: var(--secondary);"></i> +62 812-3456-7890
                    </p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
