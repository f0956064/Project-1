<!doctype html>
<html lang="en">
  @include('front.components.stylesheets')
  <style>
    :root {
        --primary-bg: #0d0d0d;
        --card-bg: #1a1a1a;
        --purple-grad-start: #7b1fa2;
        --purple-grad-end: #4a148c;
        --accent-pink: #ff4081;
        --text-main: #ffffff;
        --text-muted: rgba(255, 255, 255, 0.7);
    }

    body {
        background-color: var(--primary-bg);
        color: var(--text-main);
        font-family: 'Roboto', sans-serif;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .download-container {
        max-width: 1200px;
        margin: 0 auto;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* Top Bar */
    .contact-bar {
        background-color: rgba(74, 20, 140, 0.9);
        backdrop-filter: blur(10px);
        padding: 12px 20px;
        display: flex;
        justify-content: center;
        gap: 30px;
        font-size: 14px;
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .contact-item {
        display: flex;
        align-items: center;
        gap: 8px;
        transition: opacity 0.2s;
        text-decoration: none;
        color: inherit;
    }

    .contact-item:hover { opacity: 0.8; }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(180deg, var(--purple-grad-start) 0%, var(--purple-grad-end) 100%);
        padding: 60px 20px;
        text-align: center;
        position: relative;
        border-bottom-left-radius: 50% 30px;
        border-bottom-right-radius: 50% 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .app-logo {
        width: 100px;
        height: 100px;
        margin-bottom: 25px;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
    }

    .hero-title {
        font-size: clamp(24px, 5vw, 42px);
        font-weight: 900;
        margin-bottom: 15px;
        line-height: 1.2;
        letter-spacing: -0.5px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    .hero-subtitle {
        font-size: clamp(16px, 2vw, 20px);
        color: var(--text-muted);
        margin-bottom: 40px;
    }

    .btn-download {
        background-color: var(--accent-pink);
        color: #fff;
        padding: 16px 45px;
        border-radius: 50px;
        font-weight: 800;
        text-decoration: none !important;
        display: inline-block;
        box-shadow: 0 6px 20px rgba(255, 64, 129, 0.4);
        font-size: 20px;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .btn-download:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 10px 25px rgba(255, 64, 129, 0.6);
        background-color: #ff5c93;
        color: #fff;
    }

    /* Floating Icon */
    .floating-icon-container {
        display: flex;
        justify-content: center;
        margin-top: -50px;
        margin-bottom: 30px;
        position: relative;
        z-index: 10;
    }

    .floating-icon {
        background: #fff;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        animation: bounce 2s infinite;
    }

    .floating-icon img { width: 60px; }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* Games Section */
    .games-section {
        padding: 40px 20px;
        flex-grow: 1;
        max-width: 1000px;
        margin: 0 auto;
        width: 100%;
    }

    .games-title {
        text-align: center;
        font-size: 28px;
        margin-bottom: 40px;
        font-weight: 700;
        position: relative;
        display: inline-block;
        left: 50%;
        transform: translateX(-50%);
    }

    .games-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 3px;
        background: var(--accent-pink);
        border-radius: 2px;
    }

    .games-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }

    .game-card {
        background: linear-gradient(145deg, #7b1fa2, #6a1b9a);
        padding: 30px 20px;
        border-radius: 24px;
        text-align: center;
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 120px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.05);
        cursor: default;
    }

    .game-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(123, 31, 162, 0.4);
        border-color: rgba(255,255,255,0.2);
    }

    /* Footer */
    .footer {
        background-color: var(--purple-grad-end);
        padding: 25px;
        text-align: center;
        font-size: 14px;
        margin-top: 50px;
        color: rgba(255,255,255,0.6);
        border-top: 1px solid rgba(255,255,255,0.1);
    }

    /* Professional Touch: Background Elements */
    .bg-blobs {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        opacity: 0.15;
    }

    .blob {
        position: absolute;
        width: 400px;
        height: 400px;
        background: var(--purple-grad-start);
        filter: blur(80px);
        border-radius: 50%;
    }

    .blob-1 { top: -100px; right: -100px; }
    .blob-2 { bottom: -100px; left: -100px; background: var(--accent-pink); }

    /* Mobile adjustments */
    @media (max-width: 600px) {
        .contact-bar { gap: 15px; font-size: 13px; }
        .hero-section { padding: 40px 15px; }
        .games-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
        .game-card { min-height: 100px; padding: 15px; font-size: 15px; border-radius: 18px; }
    }
  </style>
  <body>
    <!-- Abstract background decorations -->
    <div class="bg-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div class="download-container">
        <!-- Contact Bar -->
        <div class="contact-bar">
            <!-- <a href="tel:+919733956086" class="contact-item">
                <i class="material-icons" style="font-size: 18px;">call</i>
                <span>+91 97339 56086</span>
            </a>
            <a href="https://wa.me/919733956086" target="_blank" class="contact-item">
                <i class="fa fa-whatsapp" style="font-size: 18px; color: #25D366;"></i>
                <span>WhatsApp</span>
            </a> -->
        </div>

        <!-- Hero Section -->
        <div class="hero-section">
            <!-- <div class="logo-container">
                <img src="{{ asset('img/logo-light.png') }}" alt="Logo" class="app-logo">
            </div> -->
            <h1 class="hero-title">Play Popular Fatafat Games And Win Unlimited Cash</h1>
            <p class="hero-subtitle">Experience the thrill anytime, anywhere!</p>
            <!-- Direct Download URL Transformation -->
            <a href="https://drive.google.com/uc?export=download&id=1tQ9r2ZdbLEDXg6FLCV0EVCl5TddU6Mfj" class="btn-download">
                <i class="fa fa-download mr-2"></i> Download App Now
            </a>
        </div>

        <!-- Available Games -->
        <div class="games-section">
            <h2 class="games-title">Available Games</h2>
            <div class="games-grid">
                @foreach($games as $game)
                    <div class="game-card">{{ $game->name }}</div>
                @endforeach
                @if($games->isEmpty())
                    <div class="game-card" style="grid-column: span 2;">Stay Tuned for More!</div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            &copy; {{ date('Y') }} {{ \Config::get('settings.company_name') }}. Built for Excellence. All Rights Reserved.
        </div>
    </div>

    @include('front.components.scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  </body>
</html>
