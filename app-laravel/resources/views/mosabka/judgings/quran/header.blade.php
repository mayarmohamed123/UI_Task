<!DOCTYPE html>
<html lang="ar" dir="rtl" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @if(isset($type) && $type === 'interpretation')
            نظام تقييم التفسير
        @elseif(isset($type) && $type === 'hadith')
            نظام تقييم الحديث الشريف
        @elseif(isset($type) && $type === 'dirayah')
            نظام تقييم الدراية
        @else
            نظام تقييم القرآن الكريم
        @endif
    </title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Amiri+Quran&family=Tajawal:wght@400;500;700&display=swap"
          rel="stylesheet">

    <!-- خط المصحف العثماني -->
    <link href="https://fonts.googleapis.com/css2?family=Scheherazade+New:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lateef:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- jQuery & Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Ensure jQuery is available globally immediately after loading
        if (typeof jQuery !== 'undefined') {
            window.jQuery = window.$ = jQuery;
            console.log('[Header] ✅ jQuery loaded and set globally:', jQuery.fn.jquery);
        } else {
            console.error('[Header] ❌ jQuery failed to load!');
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Ensure Select2 is available after loading
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 === 'function') {
            console.log('[Header] ✅ Select2 loaded successfully');
        } else {
            console.error('[Header] ❌ Select2 failed to load!');
        }
    </script>
    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        function updateJudgingHeaderOffset() {
            const header = document.querySelector('header.header-bg');
            if (!header) return;
            const height = Math.ceil(header.getBoundingClientRect().height);
            document.documentElement.style.setProperty('--judging-header-offset', height + 'px');
        }

        // Timer Logic
        let timerSeconds = 0;
        function updateTimer() {
            timerSeconds++;
            const mins = Math.floor(timerSeconds / 60).toString().padStart(2, '0');
            const secs = (timerSeconds % 60).toString().padStart(2, '0');
            const timerEl = document.getElementById('header-timer');
            if (timerEl) {
                timerEl.textContent = `${mins}:${secs}`;
            }
        }

        window.addEventListener('load', () => {
            updateJudgingHeaderOffset();
            setInterval(updateTimer, 1000);
        });
        window.addEventListener('resize', updateJudgingHeaderOffset);
        document.addEventListener('DOMContentLoaded', updateJudgingHeaderOffset);
    </script>

    <style>
        :root {
            --gold-primary: var(--color-primary);
            --gold-dark: var(--color-primary-dark);
            --header-bg-color: var(--color-secondary);
            --sidebar-bg-color: var(--color-bg-card);
            --text-muted: var(--color-text-secondary);
        }
        /* Global Body */
        body {
            font-family: var(--font-sans);
            background-color: var(--color-bg-main);
            color: var(--color-text-primary);
            padding-top: var(--judging-header-offset, 80px);
        }

        .dark body {
            background-color: #111827;
            color: #f3f4f6;
        }

        /* Cards */
        .card {
            background-color: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            border: 1px solid #e4e6ef;
        }

        /* Ensure main content never hides behind fixed header */
        #main-container {
            margin-top: 0 !important;
        }

        .dark .card {
            background-color: #1f2a44;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        /* Inputs */
        .form-input {
            background-color: #f8f9fb;
            color: #30355a;
            border: 1px solid #e4e6ef;
            border-radius: 6px;
            padding: 8px;
        }

        .form-input:focus {
            background-color: #ffffff;
            border-color: var(--gold-primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(224, 181, 123, 0.12);
        }

        .dark .form-input {
            background-color: #374151;
            color: #f3f4f6;
            border-color: #4b5563;
        }

        .dark .form-input:focus {
            background-color: #4b5563;
        }

        /* Headers / Footers */
        .header-bg,
        .footer-bg {
            background-color: #ffffff;
            color: var(--text-muted);
            border-bottom: 1px solid #e4e6ef;
        }

        .dark .header-bg,
        .dark .footer-bg {
            background-color: #1f2a44;
            color: #f3f4f6;
            border-bottom: 1px solid #4b5563;
        }

        /* Quran Styling */
        .surah-name {
            color: var(--gold-dark);
            font-family: 'Amiri Quran', serif;
            font-size: 2rem;
        }

        .bismillah {
            color: var(--gold-dark);
            font-family: 'Amiri Quran', serif;
            font-size: 1.8rem;
        }

        /* Question Border */
        .question-border {
            border-right: 4px solid var(--gold-primary);
            padding-right: 12px;
        }

        /* Animation */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Buttons */
        button {
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button:hover {
            transform: translateY(-1px);
        }

        /* Header Enhancements */
        .header-bg {
            background-color: var(--header-bg-color);
        }

        .dark .header-bg {
            background: #0f172a;
        }

        .nav-link {
            position: relative;
            padding-bottom: 4px;
            transition: color 0.3s ease;
        }

        .nav-link:hover::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: 0;
            right: 0;
            background-color: #ffffff;
        }

        .dark .nav-link:hover::after {
            background-color: #f3f4f6;
        }

        .dropdown-menu {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        /* Collapsible Form Styles */
        .form-content {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .form-toggle-icon {
            transition: transform 0.3s ease;
        }

        /* Question Item Hover Effects */
        .question-item {
            transition: all 0.3s ease;
        }

        .question-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* Custom Notification */
        .custom-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            min-width: 300px;
            max-width: 400px;
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .custom-notification.show {
            transform: translateX(0);
            opacity: 1;
        }

        .custom-notification.hide {
            transform: translateX(100%);
            opacity: 0;
        }

        .notification-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 8px;
        }

        .notification-icon {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .notification-close {
            margin-right: auto;
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            opacity: 0.7;
            transition: opacity 0.3s ease;
        }

        .notification-close:hover {
            opacity: 1;
        }

        .notification-title {
            font-weight: bold;
            font-size: 16px;
        }

        .notification-message {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .notification-message div {
            margin: 0;
        }
        
        .notification-message strong {
            font-weight: 700;
            color: inherit;
        }

        /* ════════════════════════════════════════════════════════════════ */
        /* تصميم المصحف - Mushaf Style
        /* ════════════════════════════════════════════════════════════════ */

        @font-face {
            font-family: 'Mushaf';
            font-weight: normal;
            font-style: normal;
            src: local('Scheherazade New'), local('Amiri Quran');
        }

        .mushaf-container {
            background: transparent;
            border: none;
            border-radius: 0;
            box-shadow: none;
            position: relative;
            overflow: visible;
            padding: 0;
        }

        .mushaf-container::before,
        .mushaf-container::after {
            content: none;
        }

        .mushaf-text {
            font-family: 'Lateef', 'Scheherazade New', 'Amiri Quran', serif;
            font-size: 28px;
            line-height: 2.5;
            text-align: justify;
            direction: rtl;
            color: #000000;
            padding: 40px 35px;
            background: #fffef8;
            position: relative;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .dark .mushaf-text {
            color: #f5f5f5;
            background: linear-gradient(to bottom,
                #2d3748 0%,
                #1a202c 50%,
                #1a202c 100%);
        }

        .mushaf-text::before,
        .mushaf-text::after {
            content: '۝';
            position: absolute;
            color: #d4af37;
            font-size: 24px;
            opacity: 0.3;
        }

        .mushaf-text::before {
            top: 10px;
            right: 10px;
        }

        .mushaf-text::after {
            bottom: 10px;
            left: 10px;
        }

        .ayah-span {
            display: inline;
            transition: all 0.3s ease;
        }

        .ayah-span.bg-blue-200 {
            background-color: rgba(191, 219, 254, 0.5) !important;
            border-bottom: 2px solid #3b82f6;
            padding: 2px 4px;
            border-radius: 4px;
        }

        .dark .ayah-span.bg-blue-200 {
            background-color: rgba(29, 78, 216, 0.3) !important;
            border-bottom: 2px solid #60a5fa;
        }

        .mushaf-viewer {
            background: rgba(255, 255, 255, 0.9);
            border-top: 1px solid rgba(212, 175, 55, 0.3);
            padding: 20px 24px;
        }

        .dark .mushaf-viewer {
            background: rgba(18, 24, 38, 0.85);
        }

        .mushaf-viewer-controls button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .mushaf-face-card {
            background: #ffffff;
            border: 1px solid rgba(212, 175, 55, 0.25);
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            min-height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .dark .mushaf-face-card {
            background: rgba(17, 24, 39, 0.9);
            border-color: rgba(212, 175, 55, 0.18);
        }

        .mushaf-face-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 12px;
        }

        .mushaf-face-input {
            width: 88px;
            font-size: 12px;
            text-align: center;
        }

        .mushaf-viewer-message {
            color: #b7791f;
        }

        .dark .mushaf-viewer-message {
            color: #fcd34d;
        }

        .verse-number {
            display: inline-block;
            margin: 0 8px;
            color: #b8860b;
            font-size: 22px;
            font-weight: 700;
            position: relative;
            top: -3px;
            font-family: 'Amiri Quran', serif;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .dark .verse-number {
            color: #fbbf24;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        /* زخرفة جانبية للمصحف */
        .mushaf-decoration {
            position: absolute;
            width: 30px;
            height: 100%;
            background: repeating-linear-gradient(
                0deg,
                #d4af37 0px,
                #d4af37 2px,
                transparent 2px,
                transparent 15px
            );
            opacity: 0.15;
        }

        .mushaf-decoration.left {
            left: 0;
        }

        .mushaf-decoration.right {
            right: 0;
        }

        /* رقم الآية بتصميم المصحف */
        .verse-number::before {
            content: '﴿';
            margin-left: 2px;
        }

        .verse-number::after {
            content: '﴾';
            margin-right: 2px;
        }

        /* Bismillah styling */
        .bismillah-mushaf {
            text-align: center;
            font-family: 'Scheherazade New', 'Amiri Quran', serif;
            font-size: 36px;
            color: #1e4620;
            margin-bottom: 20px;
            padding: 15px;
            border-bottom: 2px solid #d4af37;
            background: linear-gradient(to right,
                transparent,
                rgba(212, 175, 55, 0.1) 20%,
                rgba(212, 175, 55, 0.1) 80%,
                transparent);
        }

        .dark .bismillah-mushaf {
            color: #fbbf24;
            border-bottom-color: #fbbf24;
        }

        /* زخارف إسلامية للزوايا */
        .mushaf-corner {
            position: absolute;
            width: 40px;
            height: 40px;
            background-image:
                linear-gradient(45deg, #d4af37 0%, #d4af37 2px, transparent 2px),
                linear-gradient(-45deg, #d4af37 0%, #d4af37 2px, transparent 2px);
            background-size: 20px 20px;
            background-repeat: no-repeat;
            opacity: 0.4;
        }

        .mushaf-corner.top-right {
            top: 8px;
            right: 8px;
        }

        .mushaf-corner.top-left {
            top: 8px;
            left: 8px;
            transform: scaleX(-1);
        }

        .mushaf-corner.bottom-right {
            bottom: 8px;
            right: 8px;
            transform: scaleY(-1);
        }

        .mushaf-corner.bottom-left {
            bottom: 8px;
            left: 8px;
            transform: scale(-1);
        }

        /* تحسين الخط للتشكيل */
        .mushaf-text {
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Select2 customizations for notes selector */
        .select2-note-container .select2-selection--single {
            min-height: 44px;
            display: flex;
            align-items: center;
            border-radius: 12px;
            border: 1px solid rgba(59, 130, 246, 0.25);
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.05), rgba(59, 130, 246, 0.08));
            padding-inline: 12px;
        }

        .select2-note-container .select2-selection--single .select2-selection__rendered {
            color: #1e3a8a;
            font-weight: 600;
            padding-left: 0;
            padding-right: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .select2-note-container .select2-selection__placeholder {
            color: #64748b;
            font-weight: 500;
        }

        .select2-note-container .select2-selection__arrow {
            top: 50%;
            transform: translateY(-50%);
            left: 12px;
            right: auto;
        }

        .select2-dropdown {
            border-radius: 12px !important;
            border-color: rgba(59, 130, 246, 0.2) !important;
            overflow: hidden;
        }

        .select2-results__option {
            padding: 10px 14px;
            font-size: 0.95rem;
        }

        .select2-results__option--highlighted {
            background: rgba(59, 130, 246, 0.12) !important;
            color: #1e3a8a !important;
        }
    </style>
</head>

<body class="min-h-screen bg-bg-main font-sans text-text-primary xl:overflow-hidden">
<header class="header-bg text-white py-3 px-6 fixed top-0 left-0 right-0 z-50 shadow-lg">
    <div class="flex justify-between items-center max-w-[1920px] mx-auto">
        <!-- Top Left: Committee Head & Participant -->
        @php
            $committeeId = request()->get('committee_id');
            $backUrl = $committeeId
                ? route('judgings.index', ['committee_id' => $committeeId])
                : route('judgings.index');
        @endphp
        <!-- Top Right (RTL Start): Logo & Association Info -->
        <div class="flex items-center gap-3">
            <div class="w-14 h-14 bg-white/5 rounded-xl flex items-center justify-center shadow-lg overflow-hidden border border-white/10 p-1">
                <img src="{{ asset('assets/images/logo-gold.png') }}" onerror="this.src='{{ asset('assets/images/logo.png') }}'" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div class="text-start">
                <h1 class="text-sm font-bold text-white leading-tight">جمعية الماهر القرآنية</h1>
                <p class="text-[10px] text-slate-400">طريقك نحو المهارة</p>
            </div>
        </div>

        <!-- Top Middle: Timer -->
        <div class="bg-black/30 border border-white/10 rounded-full px-4 py-1.5 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-yellow-400 shadow-[0_0_8px_#facc15] animate-pulse"></span>
            <span id="header-timer" class="font-mono text-lg font-medium text-white tracking-widest leading-none">00:00</span>
        </div>

        <!-- Top Left (RTL End): Committee Head & Participant -->
        <div class="flex items-center gap-6">
            <div class="flex flex-col items-end">
               <div class="flex items-center gap-2 mb-1">
                   <span class="text-sm font-bold text-white">{{ $participant_name ?? ($studentDetail->competitionParticipant->full_name ?? 'أحمد بن محمد الكندري') }}</span>
                   <span class="text-[10px] px-1.5 py-0.5 bg-yellow-400/10 text-yellow-400 border border-yellow-400/20 rounded uppercase font-bold tracking-wider">المتسابق</span>
               </div>
               <div class="flex items-center gap-4 text-[11px] text-slate-300">
                    <div class="flex items-center gap-1">
                        <span>{{ $studentDetail->competitionVersionBranch->name ?? 'الأول - القرآن الكريم كاملاً' }}</span>
                        <span class="text-slate-500">:الفرع</span>
                    </div>
                </div>
            </div>

            <div class="h-10 w-px bg-white/10 mx-2"></div>

            <div class="flex items-center gap-3">
                <div class="flex flex-col items-end border-e border-white/10 pe-4">
                    <span class="text-xs text-slate-400">رئيس اللجنة</span>
                    <span class="text-sm font-bold text-white">{{ auth()->user()->full_name ?? 'د. عبد الرحمن السديس' }}</span>
                </div>
                <a href="{{ $backUrl }}" class="w-10 h-10 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-lg transition-colors border border-white/10">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        </div>
    </div>
</header>
