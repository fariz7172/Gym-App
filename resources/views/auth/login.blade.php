<!DOCTYPE html>
<html lang="id" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GymPro</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        :root {
            --primary: #6366F1;
            --primary-dark: #4F46E5;
            --secondary: #22D3EE;
        }
        
        .dark { --bg: #0F172A; --card: rgba(30, 41, 59, 0.8); --text: #F1F5F9; --text-muted: #94A3B8; --border: rgba(255,255,255,0.1); }
        :not(.dark) { --bg: #F8FAFC; --card: rgba(255, 255, 255, 0.9); --text: #1E293B; --text-muted: #64748B; --border: rgba(0,0,0,0.1); }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated Background */
        .bg-gradient {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, var(--primary) 0%, #9333EA 50%, var(--secondary) 100%);
            opacity: 0.1;
        }
        
        .bg-pattern {
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%236366F1' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        
        .floating-shapes { position: fixed; inset: 0; overflow: hidden; pointer-events: none; }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: float 20s ease-in-out infinite;
        }
        
        .shape-1 { width: 500px; height: 500px; background: var(--primary); opacity: 0.3; top: -10%; left: -10%; }
        .shape-2 { width: 400px; height: 400px; background: var(--secondary); opacity: 0.2; bottom: -10%; right: -10%; animation-delay: -5s; }
        .shape-3 { width: 300px; height: 300px; background: #F472B6; opacity: 0.2; top: 50%; left: 50%; animation-delay: -10s; }
        
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            25% { transform: translate(30px, -30px) rotate(5deg); }
            50% { transform: translate(-20px, 20px) rotate(-5deg); }
            75% { transform: translate(20px, 40px) rotate(3deg); }
        }
        
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
        }
        
        .login-card {
            background: var(--card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            margin-bottom: 1rem;
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.4);
        }
        
        .logo-text {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .logo-subtitle {
            color: var(--text-muted);
            margin-top: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            color: var(--text-muted);
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
        
        .form-input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            background: var(--bg);
            border: 2px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        
        .form-input::placeholder { color: var(--text-muted); }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.25rem;
        }
        
        .password-toggle:hover { color: var(--primary); }
        
        .remember-forgot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
        }
        
        .checkbox-wrapper input {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
        }
        
        .checkbox-wrapper span { color: var(--text-muted); }
        
        .forgot-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .forgot-link:hover { text-decoration: underline; }
        
        .btn-login {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }
        
        .btn-login:active { transform: translateY(0); }
        
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #EF4444;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .theme-toggle {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 50%;
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-muted);
            transition: all 0.3s ease;
            z-index: 100;
        }
        
        .theme-toggle:hover {
            background: var(--primary);
            color: white;
        }
        
        /* Decorative elements */
        .deco-line {
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 2px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="bg-gradient"></div>
    <div class="bg-pattern"></div>
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <button class="theme-toggle" @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)">
        <i class="fas" :class="darkMode ? 'fa-sun' : 'fa-moon'" style="font-size: 1.25rem;"></i>
    </button>
    
    <div class="login-container">
        <div class="login-card">
            <div class="deco-line"></div>
            
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <div class="logo-text">GymPro</div>
                <p class="logo-subtitle">Sistem Manajemen Gym Modern</p>
            </div>
            
            @if($errors->any())
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif
            
            <form method="POST" action="{{ route('login') }}" x-data="{ showPassword: false }">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input 
                            type="email" 
                            name="email" 
                            class="form-input" 
                            placeholder="Masukkan email Anda"
                            value="{{ old('email') }}"
                            required
                            autofocus
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input 
                            :type="showPassword ? 'text' : 'password'" 
                            name="password" 
                            class="form-input" 
                            placeholder="Masukkan password"
                            required
                        >
                        <button type="button" class="password-toggle" @click="showPassword = !showPassword">
                            <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>
                
                <div class="remember-forgot">
                    <label class="checkbox-wrapper">
                        <input type="checkbox" name="remember">
                        <span>Ingat saya</span>
                    </label>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt" style="margin-right: 0.5rem;"></i>
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>
</html>
