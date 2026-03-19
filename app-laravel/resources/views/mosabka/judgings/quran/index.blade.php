@include('mosabka::judgings.quran.header')

<style>
    /* CSS Variables for New Palette */
    :root {
        --color-navy: #1e2540;
        --color-gold: #e0b57b;
        --color-gold-dark: #c99d5f;
        --color-cream: #f8f7f2;
        --color-mushaf-bg: #fffef8;
        --color-border: #e4e6ef;
        --color-text-navy: #30355a;
        --color-text-muted: #7e8299;
    }

    /* Layout & Scrolling */
    #main-layout {
        height: calc(100vh - 64px);
        margin-top: 64px;
        background-color: var(--color-cream);
        display: flex;
        overflow: hidden;
    }

    .quran-scroll-area::-webkit-scrollbar {
        width: 8px;
    }
    .quran-scroll-area::-webkit-scrollbar-track {
        background-color: var(--color-mushaf-bg);
    }
    .quran-scroll-area::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
        border: 2px solid var(--color-mushaf-bg);
    }

    /* Left Sidebar: Questions */
    .question-pill {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        border-radius: 1.25rem;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background-color: transparent;
        margin-bottom: 0.5rem;
    }
    .question-pill:hover {
        background-color: #f1f5f9;
        transform: translateY(-2px);
    }
    .question-pill.active {
        background-color: var(--color-cream);
        border-color: rgba(224, 181, 123, 0.3);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }
    .status-circle {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: bold;
        border: 1.5px solid var(--color-border);
        color: var(--color-text-muted);
        background-color: white;
    }
    .active .status-circle {
        border-color: var(--color-gold);
        background-color: var(--color-gold);
        color: white;
    }
    .completed .status-circle {
        background-color: #10b981;
        border-color: #10b981;
        color: white;
    }

    /* Score Box Mini Components */
    .score-mini-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 1rem;
        padding: 0.5rem;
        text-align: center;
    }
    .score-mini-label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: rgba(255, 255, 255, 0.5);
        margin-bottom: 0.25rem;
    }
    .score-mini-value {
        font-size: 1rem;
        font-weight: bold;
        color: white;
    }

    /* Deduction Buttons — Nafes Stepper Style */
    .deductions-grid button {
        height: 3.5rem;
        border-radius: 0.875rem;
        font-weight: 800;
        font-size: 1.125rem;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .deductions-grid button:active {
        transform: scale(0.95);
    }
    .btn-deduct-0 { background-color: #f0fdf4; color: #16a34a; border-color: #dcfce7 !important; }
    .btn-deduct-0.active { background-color: #16a34a; color: white; border-color: #16a34a !important; box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25); }
    
    .btn-deduct-penalty { background-color: #fef2f2; color: #dc2626; border-color: #fee2e2 !important; }
    .btn-deduct-penalty.active { background-color: #dc2626; color: white; border-color: #dc2626 !important; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25); }

    /* ═══ Nafes Tajweed/Adaa Deduction Circles ═══ */
    .nafes-deduction-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.625rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .nafes-deduction-row:last-child { border-bottom: none; }
    .nafes-deduction-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--color-text-navy);
    }
    .nafes-deduction-controls {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .nafes-deduction-value {
        font-size: 1.1rem;
        font-weight: 800;
        min-width: 2.5rem;
        text-align: center;
        color: var(--color-text-navy);
    }
    .nafes-circle-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
    }
    .nafes-circle-btn.btn-plus {
        color: #16a34a;
        border-color: #bbf7d0;
    }
    .nafes-circle-btn.btn-plus:hover {
        background: #16a34a;
        color: white;
        border-color: #16a34a;
        box-shadow: 0 2px 8px rgba(22,163,74,0.25);
    }
    .nafes-circle-btn.btn-minus {
        color: #dc2626;
        border-color: #fecaca;
    }
    .nafes-circle-btn.btn-minus:hover {
        background: #dc2626;
        color: white;
        border-color: #dc2626;
        box-shadow: 0 2px 8px rgba(220,38,38,0.25);
    }

    /* ═══ Nafes Note Tags (Chips) ═══ */
    .note-tag {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.35rem 0.875rem;
        border-radius: 9999px;
        background-color: var(--color-cream);
        border: 1.5px solid var(--color-border);
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.2s;
        cursor: default;
    }
    .note-tag .note-tag-remove {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: #94a3b8;
        cursor: pointer;
        transition: all 0.15s;
        background: transparent;
        border: none;
        padding: 0;
    }
    .note-tag .note-tag-remove:hover {
        background: #fee2e2;
        color: #ef4444;
    }
    .note-tag:hover {
        border-color: var(--color-gold);
        background-color: rgba(224, 181, 123, 0.08);
    }

    /* Mushaf Design System */
    .mushaf-card {
        background: white;
        border: 1px solid var(--color-border);
        border-radius: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }
    .mushaf-content-area {
        background-color: var(--color-mushaf-bg);
        font-family: 'Lateef', serif;
        position: relative;
    }
    .mushaf-text-display {
        font-size: 32px;
        line-height: 2.2;
        text-align: justify;
        padding: 3rem;
        color: #222;
        direction: rtl;
    }

    /* Scrollbars for Sidebars */
    .sidebar-scroller::-webkit-scrollbar {
        width: 4px;
    }
    .sidebar-scroller::-webkit-scrollbar-track {
        background: transparent;
    }
    .sidebar-scroller::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 2px;
    }

    /* Animations */
    @keyframes slideInUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .animate-in {
        animation: slideInUp 0.4s cubic-bezier(0.2, 0, 0.4, 1) forwards;
    }

    /* Modal Categories Grid */
    .notes-categories-grid .category-section {
        background: white;
        border: 1px solid #f1f5f9;
        border-radius: 1.25rem;
        padding: 1.25rem;
    }
    .category-title {
        color: var(--color-gold-dark);
        font-size: 0.8rem;
        font-weight: bold;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .category-title::before {
        content: '•';
        color: var(--color-gold);
        font-size: 1.2rem;
        font-weight: 900;
    }
    .notes-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .note-btn {
        padding: 0.4rem 0.875rem;
        background-color: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.2s;
        color: #475569;
    }
    .note-btn:hover {
        background-color: var(--color-cream);
        border-color: var(--color-gold);
        color: var(--color-gold-dark);
    }
    .note-btn.active {
        background-color: var(--color-gold);
        color: white;
        border-color: var(--color-gold);
        box-shadow: 0 2px 8px rgba(224, 181, 123, 0.3);
    }

    .note-item-card:hover {
        background: #fff;
        border-color: #cbd5e1;
        transform: translateX(-2px); /* حركة بسيطة لليسار */
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    /* منطقة النص والأيقونة */
    .note-item-content {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1; /* يأخذ باقي المساحة */
    }

    /* أيقونة صغيرة وأنيقة */
    .note-item-icon {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #3b82f6;
        background: rgba(59, 130, 246, 0.1);
        border-radius: 50%; /* دائرة كاملة */
        font-size: 11px;
        flex-shrink: 0;
    }

    .note-item-text {
        color: #334155;
        font-size: 0.9rem;
        font-weight: 500;
        line-height: 1.5;
    }

    /* زر الحذف - ناعم ويظهر بوضوح عند الهوفر */
    .note-item-remove {
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        border: 1px solid transparent;
        color: #94a3b8; /* لون رمادي باهت افتراضياً */
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-right: 8px;
    }

    /* عند تمرير الماوس على الزر */
    .note-item-remove:hover {
        background-color: #fee2e2;
        color: #ef4444;
        border-color: #fecaca;
    }

    .note-item-remove i {
        font-size: 12px;
    }

    /* Empty state */
    .notes-empty-state {
        text-align: center;
        padding: 24px 12px;
        color: #94a3b8;
        font-size: 12px;
    }

    .notes-empty-state i {
        font-size: 32px;
        margin-bottom: 8px;
        opacity: 0.5;
    }

    /* 5. تخصيص شريط التمرير (Scrollbar) ليصبح أنحف */
    .select2-results__options::-webkit-scrollbar {
        width: 6px;
    }

    .select2-results__options::-webkit-scrollbar-track {
        background: transparent;
    }

    .select2-results__options::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
        border: 2px solid transparent;
        background-clip: content-box;
    }

    .select2-results__options::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }

    /* تنسيق الرسالة عند عدم وجود نتائج */
    .select2-results__message {
        text-align: center !important;
        color: #94a3b8 !important;
        padding: 15px !important;
        font-size: 0.85rem !important;
    }

    /* إصلاح اتجاه العناصر في RTL */
    .select2-container[dir="rtl"] .select2-selection--multiple .select2-selection__choice {
        margin-left: 4px !important;
        margin-right: 0 !important;
    }

    /* إخفاء زر الحذف الكبير (X) في Select2 multiple */
    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        display: none !important;
    }

    /* ═══ Nafes Notes Section Header ═══ */
    .nafes-notes-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .nafes-notes-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--color-text-navy);
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .nafes-notes-title i {
        color: var(--color-gold);
        font-size: 0.9rem;
    }
    .nafes-filter-toggle {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        border: 1.5px solid var(--color-border);
        background: white;
        color: var(--color-text-muted);
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .nafes-filter-toggle:hover,
    .nafes-filter-toggle.active {
        background: var(--color-gold);
        color: white;
        border-color: var(--color-gold);
    }
    .nafes-search-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1.5px solid var(--color-border);
        border-radius: 0.75rem;
        font-size: 0.8rem;
        background: white;
        color: var(--color-text-navy);
        transition: border-color 0.2s;
    }
    .nafes-search-input:focus {
        outline: none;
        border-color: var(--color-gold);
        box-shadow: 0 0 0 3px rgba(224, 181, 123, 0.1);
    }
    .nafes-search-input::placeholder {
        color: #b0b5c3;
    }

    /* ═══ Nafes Recommendations Card ═══ */
    .nafes-reco-card {
        background: white;
        border: 1.5px solid var(--color-border);
        border-radius: 1rem;
        padding: 0.875rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .nafes-reco-card:hover {
        border-color: var(--color-gold);
        box-shadow: 0 2px 8px rgba(224, 181, 123, 0.12);
    }
    .nafes-reco-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--color-text-navy);
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
    .nafes-reco-title i {
        color: var(--color-gold);
    }

    /* ═══ Nafes Relief Button ═══ */
    .nafes-relief-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        width: 100%;
        padding: 0.85rem 1rem;
        background-color: #fdfcf5; /* Light cream */
        border: 1.5px solid #eadbba; /* Light gold border */
        color: #b58c50; /* Dark gold text */
        border-radius: 0.85rem;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 6px rgba(224, 181, 123, 0.15);
    }
    .nafes-relief-btn .relief-icon {
        position: absolute;
        right: 1.25rem;
        font-size: 1.1rem;
        color: #e0b678; /* Gold icon */
    }
    .nafes-relief-btn:hover {
        background-color: #f7eed7;
        border-color: #dcb67d;
        transform: translateY(-1px);
    }
    .nafes-relief-btn:active {
        transform: scale(0.98);
    }

    /* ═══ Nafes Question Sidebar Enhancements ═══ */
    .question-pill .question-meta {
        font-size: 9px;
        color: #94a3b8;
        margin-top: 2px;
    }
    .question-pill .qiraa-badge {
        font-size: 8px;
        padding: 1px 6px;
        border-radius: 9999px;
        background: rgba(224, 181, 123, 0.15);
        color: var(--color-gold-dark);
        font-weight: 600;
        display: inline-block;
        margin-top: 3px;
    }
    .questions-section-separator {
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--color-gold-dark);
        padding: 0.5rem 1rem;
        margin-top: 0.5rem;
        border: 1.5px solid rgba(224, 181, 123, 0.3);
        border-radius: 0.75rem;
        background: rgba(224, 181, 123, 0.08);
        text-align: center;
    }

    /* ═══ Nafes Modal Enhancements ═══ */
    .nafes-modal-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.8rem;
        background-color: #f8f7f2;
        color: #475569;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }
    .nafes-modal-chip:hover {
        background-color: #f1ebd8;
    }
    .nafes-modal-chip.active {
        background-color: var(--color-gold);
        color: white;
        border-color: var(--color-gold-dark);
        box-shadow: 0 4px 10px rgba(224, 181, 123, 0.4);
    }
</style>

<main id="main-layout" class="animate-in flex flex-col xl:flex-row flex-1 min-h-[calc(100vh-80px)] xl:h-[calc(100vh-80px)] xl:overflow-hidden w-full max-w-[1920px] mx-auto">
    <!-- LEFT SIDEBAR: Questions -->
    <aside id="questions-sidebar" class="w-full xl:w-[300px] 2xl:w-[320px] border-b xl:border-b-0 xl:border-l border-slate-200 bg-white flex flex-col h-auto xl:h-full sidebar-scroller shrink-0">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-[#fdfdfb] shrink-0">
            <h2 class="font-bold text-[#1e2540]">الأسئلة</h2>
            <span class="text-xs px-2 py-1 bg-slate-100 text-slate-500 rounded-full font-bold">
                {{ count($questions) }}/<span id="questions-current-count">{{ $currentIndex + 1 }}</span>
            </span>
        </div>
        <div id="questions-list-container" class="flex-1 overflow-y-auto p-4 space-y-2 bg-[#fdfdfb] max-h-[40vh] xl:max-h-none">
            @foreach ($questions as $index => $q)
                <div onclick="switchToQuestion({{ $index }})" 
                     id="question-pill-{{ $index }}"
                     class="question-pill {{ $index === $currentIndex ? 'active' : '' }} group">
                    <div class="status-circle {{ isset($allAnswers[$index]) ? 'completed' : '' }}">
                        <span id="question-num-{{ $index }}">{{ $index + 1 }}</span>
                        <i class="fas fa-check {{ isset($allAnswers[$index]) ? '' : 'hidden' }} text-[10px]" id="check-{{ $index }}"></i>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <h4 class="text-sm font-bold text-[#30355a] truncate">سورة {{ $q['question']->surah }}</h4>
                        <div class="question-meta truncate">
                            @if(isset($q['question']->start_ayah_number) && isset($q['question']->end_ayah_number))
                                من الآية {{ $q['question']->start_ayah_number }} إلى {{ $q['question']->end_ayah_number }}
                            @else
                                {{ \Illuminate\Support\Str::limit($q['question']->question_text, 30) }}
                            @endif
                        </div>
                        @if(isset($q['question']->qiraa_name) && !empty($q['question']->qiraa_name))
                            <span class="qiraa-badge">{{ $q['question']->qiraa_name }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Nafes Bottom Action Area -->
        <div class="p-4 bg-white border-t border-slate-100 shrink-0">
            <button class="nafes-relief-btn" onclick="toggleReliefBox()">
                طلب التخفيف
                <i class="fas fa-asterisk relief-icon"></i>
            </button>
        </div>
    </aside>

    <!-- CENTER: Mushaf & Question -->
    <section id="question-content" class="w-full xl:flex-1 bg-[#f8f7f2] flex flex-col h-auto xl:h-full overflow-visible xl:overflow-hidden border-b xl:border-b-0 xl:border-l xl:border-r border-slate-200">
        <!-- Question Text Card -->
        <div class="p-6 bg-white m-6 mb-2 rounded-2xl shadow-sm border border-slate-100 relative">
            <span class="absolute top-6 right-6 w-2.5 h-2.5 rounded-full bg-yellow-500 shadow-[0_0_8px_rgba(234,179,8,0.4)]"></span>
            <div class="pe-6">
                <span class="text-[10px] font-bold text-slate-400 mb-1 block uppercase tracking-wider">نص السؤال</span>
                <h3 id="current-question-text" class="text-lg font-bold text-[#1e2540] leading-relaxed">{{ $questions[$currentIndex]['question']->question_text }}</h3>
            </div>
        </div>
        
        <!-- Mushaf Container -->
        <div class="flex-1 bg-white mx-4 md:mx-6 mb-4 md:mb-6 rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col min-h-[50vh] xl:min-h-0">
             <div class="quran-scroll-area flex-1 overflow-y-auto p-6 md:p-8 xl:p-10 bg-[#fffef8]">
                 <div id="mushaf-rendered-content" class="mushaf-text-display">
                     <!-- Loading content -->
                     <div class="flex items-center justify-center h-full text-slate-300">
                         <i class="fas fa-spinner fa-spin text-3xl"></i>
                     </div>
                 </div>
             </div>
             <!-- Nafes Bottom Navigation -->
             <div class="p-4 border-t border-slate-100 bg-white flex justify-between items-center relative z-10 w-full rounded-b-2xl">
                 <button type="button" onclick="if(typeof window.switchToQuestion === 'function' && window.currentIndex > 0) window.switchToQuestion(window.currentIndex - 1)" class="flex items-center gap-2 px-6 py-2.5 rounded-xl border border-slate-200 text-slate-500 font-bold hover:bg-slate-50 hover:text-[#1e2540] transition-colors active:scale-95 shadow-sm prev-btn">
                     <i class="fas fa-chevron-right text-[10px]"></i>
                     <span class="text-xs uppercase tracking-wider">السؤال السابق</span>
                 </button>
                 <button type="button" onclick="if(typeof window.switchToQuestion === 'function' && window.currentIndex < (window.allAnswers ? window.allAnswers.length : 10) - 1) window.switchToQuestion(window.currentIndex + 1)" class="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#1e2540] text-white font-bold hover:bg-[#2d375e] transition-colors shadow hover:shadow-md active:scale-95 next-btn">
                     <span class="text-xs uppercase tracking-wider">السؤال التالي</span>
                     <i class="fas fa-chevron-left text-[10px]"></i>
                 </button>
             </div>
        </div>
    </section>

    <!-- RIGHT SIDEBAR: Scoring & Notes -->
    <aside id="scores-sidebar" class="w-full xl:w-[420px] 2xl:w-[480px] bg-white h-auto xl:h-full flex flex-col overflow-visible xl:overflow-hidden shrink-0">
        <!-- Score Summary box -->
        <div class="p-6 bg-[#1e2540] m-6 mb-2 rounded-2xl text-white shadow-xl relative overflow-hidden group">
            <div class="absolute -top-12 -left-12 w-32 h-32 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition-colors"></div>
            
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-slate-400 mb-4 opacity-80 uppercase tracking-widest">ملخص الدرجات</p>
                <div class="flex items-baseline justify-center gap-2 mb-6">
                    <span id="final-score-display" class="text-6xl font-bold tracking-tight text-white">{{ $total_score }}</span>
                    <span class="text-xl text-slate-500">/ 100</span>
                    <span id="deduction-info" class="text-sm text-red-400 font-bold me-2"></span>
                </div>
                
                <!-- Mini boxes -->
                <div class="grid grid-cols-4 gap-2">
                    <div class="score-mini-card">
                        <p class="score-mini-label text-slate-400">الحفظ</p>
                        <p id="mini-score-hifz" class="score-mini-value">0.0</p>
                    </div>
                    <div class="score-mini-card">
                        <p class="score-mini-label text-slate-400">التجويد</p>
                         <p id="mini-score-tajweed" class="score-mini-value">0.0</p>
                    </div>
                    <div class="score-mini-card">
                        <p class="score-mini-label text-slate-400">الأداء</p>
                        <p id="mini-score-adaa" class="score-mini-value">0.0</p>
                    </div>
                    <div class="score-mini-card">
                        <p class="score-mini-label text-slate-400">البداية</p>
                        <p id="mini-score-bidaya" class="score-mini-value">0.0</p>
                    </div>
                </div>
            </div>
        </div>

        <form id="current-answer-form" class="flex flex-col flex-1 overflow-hidden" 
              data-question-id="{{ $questions[$currentIndex]['question']->id }}"
              data-participant-id="{{ $participant_id }}">
            
            <!-- Deduction Buttons Section -->
            <div class="px-6 py-4 border-b border-slate-100 bg-[#fdfdfb]">
                 <input type="hidden" name="alert_same_position" value="0">
                 <input type="hidden" name="alert_new_position" value="0">
                 <input type="hidden" name="fat7_points" value="0">
                 
                 <p class="text-[10px] font-bold text-slate-500 me-1 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                     <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                     مجال الحفظ
                 </p>
                 
                 <!-- Nafes Stepper Container -->
                 <div class="bg-white border rounded-xl overflow-hidden mb-4">
                     <div class="bg-gray-50 flex py-2 px-3 border-b text-[10px] font-bold text-slate-400 text-center">
                         <div class="w-6">م</div>
                         <div class="flex-1">السؤال</div>
                         <div style="flex: 0.7"></div>
                     </div>
                     <div id="alert-open-rows-container" class="max-h-[160px] overflow-y-auto w-full sidebar-scroller" data-max-alerts="2">
                         <!-- JS will populate rows automatically via createAlertRow() -->
                     </div>
                 </div>

                 <!-- Tajweed & Adaa (Nafes UI) -->
                 <div class="grid grid-cols-2 gap-3 mt-4">
                    <!-- Tajweed -->
                    <div class="bg-white border border-slate-200 text-center rounded-xl p-3 shadow-sm hover:border-yellow-400 transition-colors">
                         <p class="text-[10.5px] text-slate-400 font-bold mb-3 uppercase tracking-wider">التجويد</p>
                         <div class="flex items-center justify-between">
                              <button type="button" class="w-8 h-8 rounded-full border border-red-200 bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all active:scale-95 shadow-sm">
                                  <i class="fas fa-minus text-[10px]"></i>
                              </button>
                              <span class="font-extrabold text-xl text-slate-700 font-mono tracking-tighter" id="ui-tajweed-score">0.0</span>
                              <button type="button" class="w-8 h-8 rounded-full border border-green-200 bg-green-50 text-green-500 flex items-center justify-center hover:bg-green-500 hover:text-white transition-all active:scale-95 shadow-sm">
                                  <i class="fas fa-plus text-[10px]"></i>
                              </button>
                         </div>
                    </div>
                    <!-- Adaa -->
                    <div class="bg-white border border-slate-200 text-center rounded-xl p-3 shadow-sm hover:border-yellow-400 transition-colors">
                         <p class="text-[10.5px] text-slate-400 font-bold mb-3 uppercase tracking-wider">الأداء</p>
                         <div class="flex items-center justify-between">
                              <button type="button" class="w-8 h-8 rounded-full border border-red-200 bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all active:scale-95 shadow-sm">
                                  <i class="fas fa-minus text-[10px]"></i>
                              </button>
                              <span class="font-extrabold text-xl text-slate-700 font-mono tracking-tighter" id="ui-adaa-score">0.0</span>
                              <button type="button" class="w-8 h-8 rounded-full border border-green-200 bg-green-50 text-green-500 flex items-center justify-center hover:bg-green-500 hover:text-white transition-all active:scale-95 shadow-sm">
                                  <i class="fas fa-plus text-[10px]"></i>
                              </button>
                         </div>
                    </div>
                 </div>

                 <!-- Keep Old Manual Buttons Hidden for backward compatibility -->
                 <div class="deductions-grid hidden gap-2" id="deduction-buttons-container">
                     <button type="button" onclick="setManualDeduction(0)" class="btn-deduct-0 active">0.0</button>
                     <button type="button" onclick="setManualDeduction(0.5)" class="btn-deduct-penalty">0.5</button>
                     <button type="button" onclick="setManualDeduction(1.0)" class="btn-deduct-penalty">1.0</button>
                     <button type="button" onclick="setManualDeduction(1.5)" class="btn-deduct-penalty">1.5</button>
                     <button type="button" onclick="setManualDeduction(2.0)" class="btn-deduct-penalty">2.0</button>
                 </div>
            </div>

            <!-- Notes Section — Nafes Style -->
            <div class="flex-1 flex flex-col p-6 overflow-hidden">
                <!-- Nafes Notes Header -->
                <div class="nafes-notes-header cursor-pointer hover:bg-slate-50 transition-colors rounded-lg p-1 -mx-1 group/notes" onclick="showAllNotesModal()">
                    <h3 class="nafes-notes-title mt-1">
                        <i class="fas fa-clipboard-list"></i>
                        ملاحظات تجويدية وأدائية
                    </h3>
                    <div class="flex items-center gap-2">
                        <button type="button" class="text-[10px] font-bold text-slate-400 group-hover/notes:text-yellow-600 transition-colors bg-slate-50 px-2.5 py-1.5 rounded-lg border border-slate-200/60 shadow-sm pointer-events-none">
                            <i class="fas fa-bars" style="font-size:9px"></i> الكل
                        </button>
                    </div>
                </div>
                
                <!-- Tag Area (Selected Notes) -->
                <div id="selected-notes-tags" class="flex flex-wrap gap-2 mb-3 min-h-[32px]">
                    <!-- Display selected note tags here -->
                    <p class="text-xs text-slate-400 italic">لا توجد ملاحظات مختارة...</p>
                </div>
                
                <!-- Hidden Notes Storage -->
                <select id="unified-note-select" multiple class="hidden" dir="rtl">
                    @foreach ($notes ?? [] as $note)
                        <option value="{{ $note->id }}">{{ $note->note }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="note_ids" id="note-ids">
                <input type="hidden" name="note_texts" id="note-texts">
                <div id="selected-notes-display" class="hidden"></div> <!-- Fallback for original logic -->

                <!-- Search Input -->
                <div class="mb-3">
                    <input type="text" class="nafes-search-input" placeholder="ابحث أو أضف ملاحظة..." id="notes-search-input" oninput="filterNotesList(this.value)">
                </div>

                <!-- Fast selection suggested notes -->
                <div class="space-y-3 overflow-y-auto sidebar-scroller flex-1" id="notes-categories-list">
                    @php
                        $categories = $notes->groupBy('category_name');
                    @endphp
                    @foreach($categories as $catName => $catNotes)
                        <div class="category-block">
                             <p class="category-title">{{ $catName ?: 'ملاحظات عامة' }}</p>
                             <div class="flex flex-wrap gap-2">
                                 @foreach($catNotes->take(5) as $note)
                                     <button type="button" onclick="toggleNoteSelect({{ $note->id }})" class="note-btn" id="note-card-{{ $note->id }}">
                                         {{ $note->note }}
                                     </button>
                                 @endforeach
                             </div>
                        </div>
                    @endforeach
                    @if(isset($isEditModeBlade) && $isEditModeBlade)
                        @php
                            \Log::info('[Quran View] Questions display summary', [
                                'is_edit_mode' => true,
                                'total_questions' => $totalQuestions,
                                'shown_questions_count' => $shownQuestionsCount,
                                'question_ids' => collect($questions)->pluck('question.id')->toArray()
                            ]);
                        @endphp
                    @endif
                </div>

                <!-- Nafes Recommendations Card -->
                <div class="nafes-reco-card mt-3" onclick="showRecommendationsModal()">
                    <div class="nafes-reco-title">
                        <i class="fas fa-pen-fancy"></i>
                        ملاحظات وتوصيات
                    </div>
                </div>
            </div>
        </form>
    </aside>

    <!-- Hidden question data for JavaScript -->
    <div id="questions-data" style="display: none;">
        @foreach ($questions as $index => $q)
            <div data-question-index="{{ $index }}" data-question-id="{{ $q['question']->id }}"
                 data-surah="{{ $q['question']->surah }}" data-surah-number="{{ $q['question']->surah_number }}"
                 data-question-text="{{ $q['question']->question_text }}" data-page="{{ $q['page'] }}"
                 data-page-range="{{ $q['page_range'] ?? '' }}" data-pages="{{ json_encode($q['pages']) }}"
                 data-ayahs="{{ json_encode($q['ayahs']) }}" data-highlight="{{ json_encode($q['highlight']) }}"
                 data-start-ayah-number="{{ $q['question']->start_ayah_number ?? '' }}"
                 data-end-ayah-number="{{ $q['question']->end_ayah_number ?? '' }}"
                 data-end-surah="{{ $q['question']->end_surah ?? '' }}">
            </div>
        @endforeach
    </div>

    <!-- Modals -->
    <!-- Relief Modal -->
    <div id="relief-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-in">
             <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                 <h3 class="font-bold text-[#1e2540]">طلب التخفيف من اللجنة</h3>
                 <button type="button" onclick="toggleReliefBox()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
             </div>
             <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">درجة التخفيف المطلوبة</label>
                    <div id="relief-grade-display" class="w-full">
                         <!-- Will be filled by JS -->
                    </div>
                    <input type="hidden" id="relief-grade" name="relief_grade" value="">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide">السبب أو المسوغ</label>
                    <textarea id="relief-reason" class="w-full rounded-2xl border border-slate-200 p-4 text-sm focus:ring-2 focus:ring-yellow-500/20" rows="3" placeholder="لماذا تطلب التخفيف لهذه اللجنة؟"></textarea>
                </div>
                <button type="button" id="request-relief-btn" class="w-full py-4 bg-yellow-600 hover:bg-yellow-700 text-white rounded-2xl font-bold shadow-lg shadow-yellow-900/10 active:scale-95 transition-all">
                    إرسال الطلب للجنة
                </button>
             </div>
        </div>
    </div>

    <!-- All Notes Modal (Nafes Design) -->
    <div id="all-notes-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[100] hidden flex-col items-center justify-center p-6 transition-opacity">
        <div id="all-notes-modal-content" class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl h-[85vh] flex flex-col overflow-hidden relative opacity-0 translate-y-4 scale-95 transition-all duration-300">
             <!-- Modal Header -->
             <div class="p-5 border-b border-slate-100 flex justify-center items-center relative">
                 <button type="button" onclick="closeAllNotesModal()" class="absolute left-6 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                 </button>
                 <h3 class="text-lg font-bold text-[#1e2540] flex items-center justify-center gap-2">
                     <i class="far fa-file-alt text-yellow-500"></i> جميع الملاحظات الأدائية
                 </h3>
             </div>
             
             <!-- Modal Content Area -->
             <div class="flex-1 overflow-y-auto p-6 sidebar-scroller flex flex-col px-8">
                 <!-- Search Input -->
                 <div class="relative mb-6 shrink-0">
                     <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400"><i class="fas fa-search text-xs"></i></span>
                     <input type="text" id="modal-notes-search" oninput="filterModalNotes(this.value)" class="w-full bg-[#f8f7f2] border-0 rounded-xl py-3 pr-10 pl-4 text-sm focus:ring-1 focus:ring-yellow-500 text-slate-600 font-bold placeholder-slate-400 transition-shadow" placeholder="ابحث في الملاحظات...">
                 </div>

                 <!-- Categories Grid -->
                 <div class="space-y-6 flex-1" id="modal-notes-list">
                    @foreach($categories as $catName => $catNotes)
                        <div class="modal-category-block">
                             <h4 class="text-[11px] font-bold text-slate-400 mb-3 flex items-center gap-1.5 justify-end">
                                 {{ $catName ?: 'ملاحظات عامة' }} <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 block shadow-[0_0_6px_rgba(234,179,8,0.5)]"></span>
                             </h4>
                             <div class="flex flex-wrap gap-2 justify-end">
                                 @foreach($catNotes as $note)
                                     <button type="button" onclick="toggleNoteSelect({{ $note->id }}); updateModalChipVisual(this)" class="nafes-modal-chip active-state-check" id="modal-note-card-{{ $note->id }}" data-note-text="{{ mb_strtolower($note->note) }}">
                                         <span class="check-icon hidden text-white ml-0.5"><i class="fas fa-check text-[10px]"></i></span>
                                         <span class="note-text">{{ $note->note }}</span>
                                     </button>
                                 @endforeach
                             </div>
                        </div>
                    @endforeach
                 </div>
             </div>
             
             <!-- Modal Footer -->
             <div class="p-6 bg-white border-t border-slate-100 shrink-0">
                 <div class="flex items-center gap-3 w-full">
                     <input type="text" id="modal-new-note-input" class="flex-1 bg-[#f8f7f2] border-0 rounded-xl py-3 px-4 text-sm focus:ring-1 focus:ring-yellow-500 text-slate-600 font-bold placeholder-slate-400 transition-shadow" placeholder="أضف ملاحظة جديدة...">
                     <button type="button" onclick="if(document.getElementById('modal-new-note-input').value.trim() !== '') { /* Logic to add new note could go here */ document.getElementById('modal-new-note-input').value=''; }" class="px-6 py-3 bg-[#f8f7f2] hover:bg-[#e4e2de] text-slate-600 rounded-xl font-bold text-sm transition-colors border border-slate-200 shadow-sm flex items-center gap-2">
                         <i class="fas fa-plus text-xs"></i> إضافة
                     </button>
                 </div>
                 <div class="mt-4 text-xs font-bold text-slate-500 text-center">
                     الملاحظات المحددة: <span id="modal-selected-count" class="text-slate-700">0</span>
                 </div>
             </div>
        </div>
    </div>

    <script>
        // Modal logic
        function filterModalNotes(searchVal) {
            const query = searchVal.trim().toLowerCase();
            const categories = document.querySelectorAll('.modal-category-block');
            
            categories.forEach(block => {
                let hasVisibleNotes = false;
                const chips = block.querySelectorAll('.nafes-modal-chip');
                
                chips.forEach(chip => {
                    const text = chip.getAttribute('data-note-text');
                    if(text.includes(query)) {
                        chip.style.display = 'inline-flex';
                        hasVisibleNotes = true;
                    } else {
                        chip.style.display = 'none';
                    }
                });
                
                block.style.display = hasVisibleNotes ? 'block' : 'none';
            });
        }
        
        function updateModalChipVisual(btn) {
            // Toggles the local Check icon and active background logic visually
            // toggleNoteSelect() does the actual selection state in the parent logic.
            setTimeout(() => {
                const isActive = btn.classList.contains('active');
                const checkIcon = btn.querySelector('.check-icon');
                if(isActive) {
                    if(checkIcon) checkIcon.classList.remove('hidden');
                } else {
                    if(checkIcon) checkIcon.classList.add('hidden');
                }
                updateModalCountDisplay();
            }, 50);
        }

        function updateModalCountDisplay() {
            setTimeout(() => {
                const activeCount = document.querySelectorAll('#modal-notes-list .nafes-modal-chip.active').length;
                const countSpan = document.getElementById('modal-selected-count');
                if(countSpan) countSpan.textContent = activeCount;
            }, 100);
        }
    </script>
</main>

@include('mosabka::judgings.quran.footer')

<script>
    // Score calculation configuration
    window.SCORE_CONFIG = {
        totalQuestions: {{ $questions_count ?? count($questions) ?? 0 }},
        totalScore: {{ $total_score ?? 0 }},
        scorePerQuestion: {{ $score_per_question ?? 0 }},
        tajweedTotal: {{ $tajweed_score ?? 0 }},
        tajweedPerQuestion: {{ $tajweed_per_question ?? 0 }},
        tajweedPenalty: {{ $tajweed_penalty ?? 1 }},
        performanceTotal: {{ $performance_score ?? 0 }},
        performancePerQuestion: {{ $performance_per_question ?? 0 }},
        performancePenalty: {{ $performance_penalty ?? 1 }},
        alertSamePenalty: {{ $alert_same_position_penalty ?? $alert_new_position_penalty ?? $alert_penalty ?? 0 }},
        alertNewPenalty: {{ $alert_new_position_penalty ?? $alert_same_position_penalty ?? $alert_penalty ?? 0 }},
        fat7Penalty: {{ $fat7_penalty ?? 0 }},
        participantId: {{ $participant_id ?? 'null' }},
        branchId: {{ $competition_version_branch_id ?? 'null' }},
        settingId: {{ $judging_form_setting_id ?? 'null' }},
        type: '{{ $type ?? 'quran' }}'
    };

    const SCORE_CONFIG = window.SCORE_CONFIG;

    const NOTES_SOURCE = @json($notes->map(function ($note) {
        return ['id' => $note->id, 'text' => $note->note];
    })->values());

    function normalizeNoteText(text) {
        return (text || '').trim();
    }

    function findNoteById(noteId) {
        if (!noteId && noteId !== 0) {
            return null;
        }
        const numericId = Number(noteId);
        return NOTES_SOURCE.find(note => Number(note.id) === numericId) || null;
    }

    function findNoteByText(noteText) {
        const normalized = normalizeNoteText(noteText).toLowerCase();
        if (!normalized) {
            return null;
        }
        return NOTES_SOURCE.find(note => (note.text || '').trim().toLowerCase() === normalized) || null;
    }

    function initializeNoteInput() {
        if (window.noteInputInitialized) {
            return;
        }

        const unifiedNoteSelect = document.getElementById('unified-note-select');
        const noteIdsField = document.getElementById('note-ids');
        const noteTextsField = document.getElementById('note-texts');

        if (!unifiedNoteSelect || !noteIdsField || !noteTextsField) {
            return;
        }

        const hasSelect2Support = () => window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.select2 === 'function';

        function ensureOptionExists(note) {
            if (!note || !unifiedNoteSelect) {
                return;
            }
            const exists = Array.from(unifiedNoteSelect.options).some(option => String(option.value) === String(note.id));
            if (!exists) {
                const newOption = document.createElement('option');
                newOption.value = note.id;
                newOption.textContent = note.text || '';
                unifiedNoteSelect.appendChild(newOption);
            }
        }

        // Helper functions for multiple notes (defined before use)
        function getSelectedNoteIds() {
            try {
                const value = noteIdsField.value;
                return value ? JSON.parse(value) : [];
            } catch (e) {
                return [];
            }
        }

        function getSelectedNoteTexts() {
            try {
                const value = noteTextsField.value;
                return value ? JSON.parse(value) : [];
            } catch (e) {
                return [];
            }
        }

        function updateNotesFields(ids, texts) {
            noteIdsField.value = JSON.stringify(ids);
            noteTextsField.value = JSON.stringify(texts);
            console.log('[Notes Fields] 💾 Fields updated:', {
                noteIdsField: noteIdsField.value,
                noteTextsField: noteTextsField.value,
                idsLength: ids ? ids.length : 0,
                textsLength: texts ? texts.length : 0
            });
        }

        function updateNotesFromSelect2() {
            if (!hasSelect2Support()) return;

            const $select = window.jQuery(unifiedNoteSelect);
            const selectedIds = $select.val() || [];
            const selectedTexts = selectedIds.map(id => {
                const note = findNoteById(id);
                return note ? note.text : '';
            });

            console.log('[Notes Update] 📝 updateNotesFromSelect2 called:', {
                selectedIds: selectedIds,
                selectedTexts: selectedTexts,
                idsLength: selectedIds.length,
                textsLength: selectedTexts.length
            });

            updateNotesFields(selectedIds, selectedTexts);

            // Verify fields were updated
            const noteIdsField = document.getElementById('note-ids');
            const noteTextsField = document.getElementById('note-texts');
            console.log('[Notes Update] ✅ Fields updated:', {
                noteIdsFieldValue: noteIdsField ? noteIdsField.value : 'N/A',
                noteTextsFieldValue: noteTextsField ? noteTextsField.value : 'N/A'
            });

            // Update display immediately
            if (typeof window.updateSelectedNotesDisplay === 'function') {
                window.updateSelectedNotesDisplay(selectedIds, selectedTexts);
            } else if (typeof updateSelectedNotesDisplay === 'function') {
                updateSelectedNotesDisplay(selectedIds, selectedTexts);
            }

            // IMPORTANT: Save after updating fields to ensure notes are persisted
            console.log('[Notes Update] 💾 Triggering saveCurrentAnswer after fields update');
            const attemptSave = (retryCount = 0) => {
                const maxRetries = 5;
                if (typeof window.saveCurrentAnswer === 'function') {
                    console.log('[Notes Update] ✅ Calling saveCurrentAnswer');
                    window.saveCurrentAnswer();
                } else if (typeof saveCurrentAnswer === 'function') {
                    console.log('[Notes Update] ✅ Calling saveCurrentAnswer (local)');
                    saveCurrentAnswer();
                } else if (retryCount < maxRetries) {
                    console.log(`[Notes Update] ⏳ saveCurrentAnswer not available yet, retrying (${retryCount + 1}/${maxRetries})...`);
                    setTimeout(() => attemptSave(retryCount + 1), 200);
                } else {
                    console.warn('[Notes Update] ⚠️ saveCurrentAnswer function not available after retries');
                }
            };
            setTimeout(() => attemptSave(), 100);
        }

        // Helper function for search input filter
        window.filterNotesList = function(searchText) {
            const list = document.getElementById('notes-categories-list');
            if (!list) return;
            
            const term = searchText.trim().toLowerCase();
            const categories = list.querySelectorAll('.category-block');
            
            categories.forEach(cat => {
                const buttons = cat.querySelectorAll('.note-btn');
                let catHasVisible = false;
                
                buttons.forEach(btn => {
                    const text = btn.textContent.trim().toLowerCase();
                    if (term === '' || text.includes(term)) {
                        btn.style.display = '';
                        catHasVisible = true;
                    } else {
                        btn.style.display = 'none';
                    }
                });
                
                cat.style.display = catHasVisible ? '' : 'none';
            });
        };

        // Modal UI triggers
        window.showAllNotesModal = function() {
            const modal = document.getElementById('all-notes-modal');
            const content = document.getElementById('all-notes-modal-content');
            if (modal && content) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
                // Trigger animation
                setTimeout(() => {
                    content.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
                    content.classList.add('opacity-100', 'translate-y-0', 'scale-100');
                }, 10);
            }
        };

        window.closeAllNotesModal = function() {
            const modal = document.getElementById('all-notes-modal');
            const content = document.getElementById('all-notes-modal-content');
            if (modal && content) {
                // Reverse animation
                content.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
                content.classList.add('opacity-0', 'translate-y-4', 'scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.style.overflow = '';
                }, 300);
            }
        };

        // Close on backdrop click
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('all-notes-modal');
            if(modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeAllNotesModal();
                    }
                });
            }
        });

        window.showRecommendationsModal = function() {
            window.showAllNotesModal();
        };

        function updateSelectedNotesDisplay(ids, texts) {
            console.log('[Display] 🎨 updateSelectedNotesDisplay called:', {
                ids: ids,
                texts: texts,
                idsLength: ids ? ids.length : 0,
                textsLength: texts ? texts.length : 0,
                timestamp: new Date().toISOString()
            });

            // 1. Update Tags Display
            const tagsContainer = document.getElementById('selected-notes-tags');
            if (tagsContainer) {
                if (!ids || ids.length === 0) {
                    tagsContainer.innerHTML = '<p class="text-xs text-slate-400 italic">لا توجد ملاحظات مختارة...</p>';
                } else {
                    const html = ids.map((id, index) => {
                        const text = texts[index] || findNoteById(id)?.text || 'ملاحظة غير معروفة';
                        
                        // Create a temporary element to safely escape HTML
                        const div = document.createElement('div');
                        div.textContent = text;
                        const escapedText = div.innerHTML;

                        return `
                            <span class="note-tag group">
                                ${escapedText}
                                <button type="button" onclick="removeNote('${id}', event)" class="note-tag-remove" title="حذف">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        `;
                    }).join('');
                    
                    tagsContainer.innerHTML = html;
                }
            }

            // 2. Update Fast Selection Buttons styling
            const allNoteButtons = document.querySelectorAll('.note-btn');
            allNoteButtons.forEach(btn => {
                // Ensure ID format matches between button ID and stored data
                const btnId = String(btn.id.replace('note-card-', '').replace('modal-note-card-', ''));
                if (ids && ids.includes(btnId)) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Update All Notes Modal buttons if it exists
            const modalNoteButtons = document.querySelectorAll('#all-notes-modal .active-state-check');
            modalNoteButtons.forEach(btn => {
                const btnId = String(btn.id.replace('modal-note-card-', ''));
                if (ids && ids.includes(btnId)) {
                    btn.classList.add('active');
                    btn.classList.add('bg-yellow-100');
                    btn.classList.add('border-yellow-300');
                } else {
                    btn.classList.remove('active');
                    btn.classList.remove('bg-yellow-100');
                    btn.classList.remove('border-yellow-300');
                }
            });

            const displayDiv = document.getElementById('selected-notes-display');
            const container = document.getElementById('selected-notes-container');
            
            if (!displayDiv || !container) {
                console.warn('[Display] Container not ready yet, skipping update.');
                return; 
            }
    
            const countBadge = document.getElementById('notes-count');

            console.log('[Display] 📋 Display div check:', {
                exists: !!displayDiv,
                containerExists: !!container,
                countBadgeExists: !!countBadge
            });

            if (!displayDiv || !container) {
                console.error('[Display] ❌ Display div or container not found!');
                return;
            }

            // Update count badge
            if (countBadge) {
                countBadge.textContent = ids.length;
            }

            if (ids.length === 0) {
                console.log('[Display] 📭 No notes to display, hiding container...');
                displayDiv.innerHTML = '<div class="notes-empty-state"><i class="fas fa-inbox"></i><div>لم يتم اختيار أي ملاحظات بعد</div></div>';
                container.style.display = 'none';
                console.log('[Display] ✅ Display cleared');
                return;
            }

            // Show container
            container.style.display = 'block';

            console.log('[Display] 📝 Generating HTML for', ids.length, 'notes...');
            const html = ids.map((id, index) => {
                const text = texts[index] || '';
                // Escape HTML to prevent XSS
                const escapedText = text.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                console.log(`[Display] 📄 Note ${index + 1}:`, { id, text: escapedText });
                return `
                    <div class="note-item-card">
                        <div class="note-item-content">
                            <div class="note-item-icon">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <div class="note-item-text">${escapedText}</div>
                        </div>
                        <button type="button" onclick="removeNote('${id}', event)" class="note-item-remove" title="إزالة الملاحظة">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
            }).join('');

            console.log('[Display] 🎯 Setting innerHTML, length:', html.length);
            displayDiv.innerHTML = html;

            // Verify it was set
            console.log('[Display] ✅ HTML set. Verification:', {
                innerHTMLLength: displayDiv.innerHTML.length,
                childrenCount: displayDiv.children.length,
                firstChild: displayDiv.firstElementChild ? 'exists' : 'none'
            });
        }
        
        // Make function available on window immediately after definition
        if (typeof window !== 'undefined') {
            window.updateSelectedNotesDisplay = updateSelectedNotesDisplay;
        }

        async function createAndSelectNewNote(noteText) {
            const rawText = normalizeNoteText(noteText);

            if (!rawText) {
                showCustomNotification('تنبيه', 'اكتب الملاحظة أولاً قبل إضافتها', 'warning', 2500);
                return false;
            }

            // Check if note already exists
            const existing = findNoteByText(rawText);
            if (existing) {
                ensureOptionExists(existing);

                // Add to current selection
                const currentIds = getSelectedNoteIds();
                const currentTexts = getSelectedNoteTexts();

                // Check if already selected
                if (!currentIds.includes(String(existing.id))) {
                    currentIds.push(String(existing.id));
                    currentTexts.push(existing.text || '');
                    updateNotesFields(currentIds, currentTexts);
                    if (typeof window.updateSelectedNotesDisplay === 'function') {
                        window.updateSelectedNotesDisplay(currentIds, currentTexts);
                    } else {
                    updateSelectedNotesDisplay(currentIds, currentTexts);
                    }

                    if (hasSelect2Support()) {
                        const $select = window.jQuery(unifiedNoteSelect);
                        $select.val(currentIds).trigger('change');
                    } else {
                        const option = unifiedNoteSelect.querySelector(`option[value="${existing.id}"]`);
                        if (option) option.selected = true;
                    }
                }

                showCustomNotification('معلومة', 'الملاحظة موجودة بالفعل، تم اختيارها تلقائياً', 'info', 2500);
                console.log('[Create Note - Existing] 💾 Triggering saveCurrentAnswer');
                const attemptSave = (retryCount = 0) => {
                    const maxRetries = 5;
                    if (typeof window.saveCurrentAnswer === 'function') {
                        console.log('[Create Note - Existing] ✅ Calling saveCurrentAnswer');
                        window.saveCurrentAnswer();
                    } else if (typeof saveCurrentAnswer === 'function') {
                        console.log('[Create Note - Existing] ✅ Calling saveCurrentAnswer (local)');
                    saveCurrentAnswer();
                    } else if (retryCount < maxRetries) {
                        console.log(`[Create Note - Existing] ⏳ saveCurrentAnswer not available yet, retrying (${retryCount + 1}/${maxRetries})...`);
                        setTimeout(() => attemptSave(retryCount + 1), 200);
                    } else {
                        console.warn('[Create Note - Existing] ⚠️ saveCurrentAnswer function not available after retries');
                }
                };
                setTimeout(() => attemptSave(), 100);
                return true;
            }

            try {
                // Show loading state on select2
                if (hasSelect2Support()) {
                    const $select = window.jQuery(unifiedNoteSelect);
                    $select.prop('disabled', true);
                    $select.next('.select2-container').find('.select2-selection__rendered').html('<i class="fas fa-spinner fa-spin ms-1"></i> جاري الحفظ...');
                }

                const notesStoreUrl = `{{ rtrim(url('/'), '/') }}/api/notes/store`;

                const response = await fetch(notesStoreUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ note: rawText })
                });

                const result = await response.json();

                if (result.success && result.note) {
                    const newNote = { id: result.note.id, text: result.note.note };
                    NOTES_SOURCE.push(newNote);
                    ensureOptionExists(newNote);

                    // Add to current selection
                    const currentIds = getSelectedNoteIds();
                    const currentTexts = getSelectedNoteTexts();
                    currentIds.push(String(newNote.id));
                    currentTexts.push(newNote.text || '');

                    updateNotesFields(currentIds, currentTexts);
                    if (typeof window.updateSelectedNotesDisplay === 'function') {
                        window.updateSelectedNotesDisplay(currentIds, currentTexts);
                    } else {
                    updateSelectedNotesDisplay(currentIds, currentTexts);
                    }

                    if (hasSelect2Support()) {
                        const $select = window.jQuery(unifiedNoteSelect);
                        $select.prop('disabled', false);
                        $select.val(currentIds).trigger('change');
                    } else {
                        const option = unifiedNoteSelect.querySelector(`option[value="${newNote.id}"]`);
                        if (option) option.selected = true;
                    }

                    showCustomNotification('تم الحفظ', 'تمت إضافة الملاحظة الجديدة بنجاح', 'success', 2500);

                    console.log('[Create Note - New] 💾 Triggering saveCurrentAnswer');
                    const attemptSave = (retryCount = 0) => {
                        const maxRetries = 5;
                        if (typeof window.saveCurrentAnswer === 'function') {
                            console.log('[Create Note - New] ✅ Calling saveCurrentAnswer');
                            window.saveCurrentAnswer();
                        } else if (typeof saveCurrentAnswer === 'function') {
                            console.log('[Create Note - New] ✅ Calling saveCurrentAnswer (local)');
                        saveCurrentAnswer();
                        } else if (retryCount < maxRetries) {
                            console.log(`[Create Note - New] ⏳ saveCurrentAnswer not available yet, retrying (${retryCount + 1}/${maxRetries})...`);
                            setTimeout(() => attemptSave(retryCount + 1), 200);
                        } else {
                            console.warn('[Create Note - New] ⚠️ saveCurrentAnswer function not available after retries');
                    }
                    };
                    setTimeout(() => attemptSave(), 100);
                    return true;
                } else {
                    const message = (result && result.message) ? result.message : 'فشل في حفظ الملاحظة';
                    throw new Error(message);
                }
            } catch (error) {
                console.error('Error creating note:', error);
                const errorMessage = error?.message ? error.message : String(error);
                showCustomNotification('خطأ', `حدث خطأ أثناء حفظ الملاحظة: ${errorMessage}`, 'error', 6000);

                // Reset select2 state
                if (hasSelect2Support()) {
                    const $select = window.jQuery(unifiedNoteSelect);
                    $select.prop('disabled', false);
                    $select.val('').trigger('change');
                }
                return false;
            }
        }

        if (hasSelect2Support()) {
            const $ = window.jQuery;
            const $unifiedSelect = $(unifiedNoteSelect);

            $unifiedSelect.select2({
                tags: true,
                multiple: true,
                placeholder: unifiedNoteSelect.dataset.placeholder || 'اختر أو اكتب ملاحظات...',
                allowClear: true,
                width: '100%',
                dir: 'rtl',
                closeOnSelect: false,
                language: {
                    noResults: function () { return 'لا توجد ملاحظات'; },
                    searching: function () { return 'جاري البحث...'; },
                    inputTooShort: function () { return 'اكتب للبحث أو إضافة ملاحظة جديدة'; }
                },
                createTag: function (params) {
                    const term = $.trim(params.term);
                    if (term === '') {
                        return null;
                    }

                    // Check if note already exists
                    const existing = findNoteByText(term);
                    if (existing) {
                        return null; // Don't create tag for existing notes
                    }

                    return {
                        id: 'new:' + term,
                        text: term,
                        newTag: true
                    };
                },
                templateResult: function(data) {
                    if (data.loading) {
                        return data.text;
                    }

                    if (data.newTag) {
                        return $('<div class="d-flex align-items-center justify-content-end" style="direction: rtl;"><i class="fas fa-plus me-2" style="color: #059669; font-size: 1.1em; text-shadow: 0 0 8px rgba(5, 150, 105, 0.4);"></i><span class="add-text" style="color: #059669 !important; font-weight: 700;">إضافة:</span> <span style="font-weight: 500;">' + data.text + '</span></div>');
                    }

                    return $('<div class="d-flex align-items-center justify-content-end" style="direction: rtl;"><i class="fas fa-comment-dots me-2" style="color: #64748b;"></i>' + data.text + '</div>');
                },
                templateSelection: function(data) {
                    if (data.newTag) {
                        return $('<span style="color: #059669; font-weight: 600;">' + data.text + '</span>');
                    }
                    return $('<span>' + data.text + '</span>');
                },
                dropdownParent: $unifiedSelect.closest('.note-combo')
            });

            // Apply custom styles to Select2 container after initialization
            setTimeout(function() {
                const select2Container = $unifiedSelect.next('.select2-container');
                const select2Selection = select2Container.find('.select2-selection');
                if (select2Selection.length) {
                    select2Selection.css({
                        'height': '36px',
                        'border': '1px solid #cbd5e1',
                        'border-radius': '8px',
                        'min-height': '36px'
                    });
                    select2Selection.find('.select2-selection__rendered').css({
                        'line-height': '36px',
                        'padding-right': '12px',
                        'padding-left': '12px',
                        'font-size': '14px'
                    });
                    select2Selection.find('.select2-selection__arrow').css({
                        'height': '34px'
                    });
                }

                // Load saved notes after Select2 is initialized
                setTimeout(() => {
                    if (typeof loadSavedNotes === 'function') {
                        console.log('[Select2 Init] Loading saved notes after Select2 initialization');
                        loadSavedNotes(0);
                    }
                }, 50);
            }, 100);

            // Use select2:selecting for immediate update when clicking on an option
            $unifiedSelect.on('select2:selecting', async function (e) {
                const data = e.params.data;

                if (data.newTag) {
                    // For new tags, we'll handle it in select2:select
                    return;
                } else {
                    // For existing notes, update immediately
                    setTimeout(() => {
                        updateNotesFromSelect2();
                    }, 10);
                }
            });

            $unifiedSelect.on('select2:select', async function (e) {
                const data = e.params.data;

                if (data.newTag) {
                    const noteText = data.text;
                    const success = await createAndSelectNewNote(noteText);
                    if (!success) {
                        // Remove from selection if creation failed
                        const currentIds = getSelectedNoteIds();
                        const index = currentIds.indexOf(data.id);
                        if (index > -1) {
                            currentIds.splice(index, 1);
                            const currentTexts = getSelectedNoteTexts();
                            currentTexts.splice(index, 1);
                            updateNotesFields(currentIds, currentTexts);
                            if (typeof window.updateSelectedNotesDisplay === 'function') {
                                window.updateSelectedNotesDisplay(currentIds, currentTexts);
                            } else {
                                updateSelectedNotesDisplay(currentIds, currentTexts);
                            }
                            $unifiedSelect.val(currentIds).trigger('change');
                        }
                    }
                } else {
                    // Existing note selected - update fields with all selected notes
                    updateNotesFromSelect2();
                    // Note: saveCurrentAnswer is now called inside updateNotesFromSelect2
                }
            });

            $unifiedSelect.on('select2:unselect', function (e) {
                // Note unselected - update fields
                updateNotesFromSelect2();
                // Note: saveCurrentAnswer is now called inside updateNotesFromSelect2
            });

            // Also listen to change event for immediate update
            $unifiedSelect.on('change', function () {
                // Update display when selection changes
                updateNotesFromSelect2();
            });

            $unifiedSelect.on('select2:clear', function () {
                updateNotesFields([], []);
                if (typeof window.updateSelectedNotesDisplay === 'function') {
                    window.updateSelectedNotesDisplay([], []);
                } else {
                updateSelectedNotesDisplay([], []);
                }
                console.log('[Notes Clear] 💾 Triggering saveCurrentAnswer after clear');
                const attemptSave = (retryCount = 0) => {
                    const maxRetries = 5;
                    if (typeof window.saveCurrentAnswer === 'function') {
                        console.log('[Notes Clear] ✅ Calling saveCurrentAnswer');
                        window.saveCurrentAnswer();
                    } else if (typeof saveCurrentAnswer === 'function') {
                        console.log('[Notes Clear] ✅ Calling saveCurrentAnswer (local)');
                    saveCurrentAnswer();
                    } else if (retryCount < maxRetries) {
                        console.log(`[Notes Clear] ⏳ saveCurrentAnswer not available yet, retrying (${retryCount + 1}/${maxRetries})...`);
                        setTimeout(() => attemptSave(retryCount + 1), 200);
                    } else {
                        console.warn('[Notes Clear] ⚠️ saveCurrentAnswer function not available after retries');
                }
                };
                setTimeout(() => attemptSave(), 100);
            });

        } else {
            // Fallback for browsers without Select2
            if (unifiedNoteSelect) {
                unifiedNoteSelect.addEventListener('change', function() {
                    const selectedOptions = Array.from(unifiedNoteSelect.selectedOptions);
                    const selectedIds = selectedOptions.map(opt => opt.value);
                    const selectedTexts = selectedOptions.map(opt => {
                        const note = findNoteById(opt.value);
                        return note ? note.text : opt.text;
                    });

                    updateNotesFields(selectedIds, selectedTexts);
                    if (typeof window.updateSelectedNotesDisplay === 'function') {
                        window.updateSelectedNotesDisplay(selectedIds, selectedTexts);
                    } else if (typeof updateSelectedNotesDisplay === 'function') {
                        updateSelectedNotesDisplay(selectedIds, selectedTexts);
                    }

                // Save after updating fields
                console.log('[Notes Update] 💾 Triggering saveCurrentAnswer (non-Select2)');
                const attemptSave = (retryCount = 0) => {
                    const maxRetries = 5;
                    if (typeof window.saveCurrentAnswer === 'function') {
                        console.log('[Notes Update] ✅ Calling saveCurrentAnswer (non-Select2)');
                        window.saveCurrentAnswer();
                    } else if (typeof saveCurrentAnswer === 'function') {
                        console.log('[Notes Update] ✅ Calling saveCurrentAnswer (non-Select2, local)');
                    saveCurrentAnswer();
                    } else if (retryCount < maxRetries) {
                        console.log(`[Notes Update] ⏳ saveCurrentAnswer not available yet, retrying (${retryCount + 1}/${maxRetries})...`);
                        setTimeout(() => attemptSave(retryCount + 1), 200);
                    } else {
                        console.warn('[Notes Update] ⚠️ saveCurrentAnswer function not available (non-Select2) after retries');
                    }
                };
                setTimeout(() => attemptSave(), 100);
                });
            } else {
                console.warn('[Notes] ⚠️ unifiedNoteSelect element not found, cannot attach event listener');
            }
        }

        // Make removeNote function globally accessible
        window.removeNote = function(noteId, event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const currentIds = getSelectedNoteIds();
            const currentTexts = getSelectedNoteTexts();
            const index = currentIds.indexOf(String(noteId));

            if (index > -1) {
                currentIds.splice(index, 1);
                currentTexts.splice(index, 1);
                updateNotesFields(currentIds, currentTexts);
                if (typeof window.updateSelectedNotesDisplay === 'function') {
                    window.updateSelectedNotesDisplay(currentIds, currentTexts);
                } else {
                    updateSelectedNotesDisplay(currentIds, currentTexts);
                }

                if (hasSelect2Support()) {
                    const $select = window.jQuery(unifiedNoteSelect);
                    $select.val(currentIds).trigger('change');
                } else {
                    const option = unifiedNoteSelect.querySelector(`option[value="${noteId}"]`);
                    if (option) option.selected = false;
                    unifiedNoteSelect.dispatchEvent(new Event('change'));
                }

                // Also save directly to ensure it's saved
                console.log('[Remove Note] 💾 Triggering saveCurrentAnswer');
                const attemptSave = (retryCount = 0) => {
                    const maxRetries = 5;
                    if (typeof window.saveCurrentAnswer === 'function') {
                        console.log('[Remove Note] ✅ Calling saveCurrentAnswer');
                        window.saveCurrentAnswer();
                    } else if (typeof saveCurrentAnswer === 'function') {
                        console.log('[Remove Note] ✅ Calling saveCurrentAnswer (local)');
                        saveCurrentAnswer();
                    } else if (retryCount < maxRetries) {
                        console.log(`[Remove Note] ⏳ saveCurrentAnswer not available yet, retrying (${retryCount + 1}/${maxRetries})...`);
                        setTimeout(() => attemptSave(retryCount + 1), 200);
                    } else {
                        console.warn('[Remove Note] ⚠️ saveCurrentAnswer function not available after retries');
                    }
                };
                setTimeout(() => attemptSave(), 100);
            }
        };

        window.noteInputInitialized = true;
    }

    // Function to load saved notes for current question
    function loadSavedNotes(questionIndex = 0) {
        console.log('═══════════════════════════════════════════════════════════');
        console.log('[Notes] 🔍 loadSavedNotes called for question', questionIndex);
        console.log('[Notes] Timestamp:', new Date().toISOString());

        const noteIdsField = document.getElementById('note-ids');
        const noteTextsField = document.getElementById('note-texts');
        const unifiedNoteSelect = document.getElementById('unified-note-select');

        console.log('[Notes] 📋 Fields check:', {
            noteIdsField: !!noteIdsField,
            noteTextsField: !!noteTextsField,
            unifiedNoteSelect: !!unifiedNoteSelect
        });

        if (!noteIdsField || !noteTextsField) {
            console.error('[Notes] ❌ Fields not found!', {
                noteIdsField: !!noteIdsField,
                noteTextsField: !!noteTextsField
            });
            return false;
        }

        let noteIds = [];
        let noteTexts = [];
        let loaded = false;
        let source = 'none';

        try {
            // Priority 1: Check if allAnswers is available from footer script
            console.log('[Notes] 🔍 Checking allAnswers:', {
                exists: typeof window.allAnswers !== 'undefined',
                isArray: typeof window.allAnswers !== 'undefined' && Array.isArray(window.allAnswers),
                length: typeof window.allAnswers !== 'undefined' && Array.isArray(window.allAnswers) ? window.allAnswers.length : 0,
                hasData: typeof window.allAnswers !== 'undefined' && Array.isArray(window.allAnswers) && window.allAnswers[questionIndex] ? 'yes' : 'no'
            });

            if (typeof window.allAnswers !== 'undefined' && Array.isArray(window.allAnswers) && window.allAnswers[questionIndex]) {
                const savedData = window.allAnswers[questionIndex];
                console.log('[Notes] 📦 Saved data from allAnswers:', savedData);

                if (savedData && (savedData.note_ids || savedData.note_texts)) {
                    try {
                        noteIds = savedData.note_ids ? (Array.isArray(savedData.note_ids) ? savedData.note_ids : JSON.parse(savedData.note_ids || '[]')) : [];
                        noteTexts = savedData.note_texts ? (Array.isArray(savedData.note_texts) ? savedData.note_texts : JSON.parse(savedData.note_texts || '[]')) : [];
                        loaded = true;
                        source = 'allAnswers';
                        console.log('[Notes] ✅ Loaded from allAnswers:', {
                            noteIds,
                            noteTexts,
                            noteIdsLength: noteIds.length,
                            noteTextsLength: noteTexts.length
                        });
                    } catch (e) {
                        console.error('[Notes] ❌ Error parsing allAnswers data:', e);
                    }
                } else {
                    console.log('[Notes] ⚠️ allAnswers data exists but no note_ids or note_texts:', {
                        hasNoteIds: !!savedData?.note_ids,
                        hasNoteTexts: !!savedData?.note_texts
                    });
                }
            }

            // Priority 2: Try loading from localStorage if not loaded yet
            if (!loaded) {
                const participantId = '{{ $participant_id }}';
                const storageKey = `judging-data-${participantId}-${questionIndex}`;

                console.log('[Notes] 🔍 Checking localStorage:', {
                    participantId: participantId,
                    storageKey: storageKey
                });

                const savedData = localStorage.getItem(storageKey);
                console.log('[Notes] 📦 localStorage data:', savedData ? 'exists' : 'not found');

                if (savedData) {
                    try {
                        const parsed = JSON.parse(savedData);
                        console.log('[Notes] 📋 Parsed localStorage data:', parsed);

                        if (parsed.note_ids || parsed.note_texts) {
                            try {
                                noteIds = parsed.note_ids ? (Array.isArray(parsed.note_ids) ? parsed.note_ids : JSON.parse(parsed.note_ids || '[]')) : [];
                                noteTexts = parsed.note_texts ? (Array.isArray(parsed.note_texts) ? parsed.note_texts : JSON.parse(parsed.note_texts || '[]')) : [];
                                loaded = true;
                                source = 'localStorage';
                                console.log('[Notes] ✅ Loaded from localStorage:', {
                                    noteIds,
                                    noteTexts,
                                    noteIdsLength: noteIds.length,
                                    noteTextsLength: noteTexts.length
                                });
                            } catch (e) {
                                console.error('[Notes] ❌ Error parsing note arrays:', e);
                            }
                        } else {
                            console.log('[Notes] ⚠️ localStorage data exists but no note_ids or note_texts');
                        }
                    } catch (e) {
                        console.error('[Notes] ❌ Error parsing localStorage data:', e, savedData);
                    }
                } else {
                    console.log('[Notes] ⚠️ No data found in localStorage for key:', storageKey);
                }
            }

            console.log('[Notes] 📊 Final loaded data:', {
                loaded: loaded,
                source: source,
                noteIds: noteIds,
                noteTexts: noteTexts,
                noteIdsLength: noteIds.length,
                noteTextsLength: noteTexts.length
            });

            // Ensure arrays are valid and same length
            if (!Array.isArray(noteIds)) {
                console.warn('[Notes] ⚠️ noteIds is not an array:', noteIds);
                noteIds = [];
            }
            if (!Array.isArray(noteTexts)) {
                console.warn('[Notes] ⚠️ noteTexts is not an array:', noteTexts);
                noteTexts = [];
            }

            console.log('[Notes] 📏 Before length fix:', {
                noteIdsLength: noteIds.length,
                noteTextsLength: noteTexts.length
            });

            if (noteIds.length !== noteTexts.length) {
                const minLength = Math.min(noteIds.length, noteTexts.length);
                console.warn('[Notes] ⚠️ Length mismatch, fixing:', {
                    noteIdsLength: noteIds.length,
                    noteTextsLength: noteTexts.length,
                    minLength: minLength
                });
                noteIds = noteIds.slice(0, minLength);
                noteTexts = noteTexts.slice(0, minLength);
            }

            console.log('[Notes] 📏 After length fix:', {
                noteIdsLength: noteIds.length,
                noteTextsLength: noteTexts.length,
                noteIds: noteIds,
                noteTexts: noteTexts
            });

            // Update hidden fields
            noteIdsField.value = JSON.stringify(noteIds);
            noteTextsField.value = JSON.stringify(noteTexts);

            console.log('[Notes] ✅ Hidden fields updated:', {
                noteIdsFieldValue: noteIdsField.value,
                noteTextsFieldValue: noteTextsField.value
            });

            // Function to apply notes to UI with better retry logic
            const applyNotesToUI = (retryCount = 0) => {
                const maxRetries = 10;
                const hasNotes = noteIds.length > 0 && noteIds.length === noteTexts.length;

                console.log(`[Notes] 🎨 applyNotesToUI called (attempt ${retryCount + 1}/${maxRetries + 1}):`, {
                    hasNotes: hasNotes,
                    noteIdsLength: noteIds.length,
                    noteTextsLength: noteTexts.length,
                    noteIds: noteIds,
                    noteTexts: noteTexts
                });

                if (hasNotes) {
                    console.log(`[Notes] 📝 Has notes, applying to UI (attempt ${retryCount + 1}):`, { noteIds, noteTexts });

                    // Update Select2 if available
                    const hasJQuery = typeof window.jQuery !== 'undefined';
                    const hasSelect2 = hasJQuery && typeof window.jQuery.fn.select2 === 'function';

                    console.log('[Notes] 🔍 Select2 check:', {
                        hasJQuery: hasJQuery,
                        hasSelect2: hasSelect2,
                        unifiedNoteSelect: !!unifiedNoteSelect
                    });

                    if (hasSelect2 && unifiedNoteSelect) {
                        const $select = window.jQuery(unifiedNoteSelect);
                        console.log('[Notes] 🔍 Select2 element check:', {
                            found: $select.length > 0,
                            hasData: $select.data('select2') ? true : false
                        });

                        if ($select.length) {
                            if ($select.data('select2')) {
                                // Select2 is initialized
                                try {
                                    const stringIds = noteIds.map(String);
                                    console.log('[Notes] 🎯 Setting Select2 value:', stringIds);
                                    $select.val(stringIds).trigger('change');
                                    console.log('[Notes] ✅ Notes set in Select2 successfully');

                                    // Verify it was set
                                    const currentVal = $select.val();
                                    console.log('[Notes] 🔍 Select2 current value after setting:', currentVal);
                                } catch (e) {
                                    console.error('[Notes] ❌ Error setting Select2 value:', e);
                                    console.error('[Notes] Error stack:', e.stack);
                                }
                            } else {
                                console.log(`[Notes] ⏳ Select2 not initialized yet (attempt ${retryCount + 1}), will retry...`);
                                if (retryCount < maxRetries) {
                                    setTimeout(() => applyNotesToUI(retryCount + 1), 200);
                                    return;
                                } else {
                                    console.warn('[Notes] ⚠️ Max retries reached, Select2 still not initialized');
                                }
                            }
                        } else {
                            console.warn('[Notes] ⚠️ Select element not found by jQuery');
                        }
                    } else if (unifiedNoteSelect && retryCount < maxRetries) {
                        console.log(`[Notes] ⏳ jQuery/Select2 not ready yet (attempt ${retryCount + 1}), will retry...`);
                        setTimeout(() => applyNotesToUI(retryCount + 1), 200);
                        return;
                    } else if (!unifiedNoteSelect) {
                        console.error('[Notes] ❌ unifiedNoteSelect element not found!');
                    }

                    // Update display - always try this
                    console.log('[Notes] 🖥️ Checking updateSelectedNotesDisplay function:', {
                        exists: typeof window.updateSelectedNotesDisplay === 'function'
                    });

                    if (typeof window.updateSelectedNotesDisplay === 'function') {
                        try {
                            console.log('[Notes] 🎨 Calling updateSelectedNotesDisplay with:', { noteIds, noteTexts });
                            window.updateSelectedNotesDisplay(noteIds, noteTexts);

                            // Verify display was updated
                            const displayDiv = document.getElementById('selected-notes-display');
                            console.log('[Notes] ✅ Display updated. Display div:', {
                                exists: !!displayDiv,
                                innerHTMLLength: displayDiv ? displayDiv.innerHTML.length : 0
                            });
                        } catch (e) {
                            console.error('[Notes] ❌ Error updating display:', e);
                            console.error('[Notes] Error stack:', e.stack);
                        }
                    } else {
                        console.warn(`[Notes] ⚠️ updateSelectedNotesDisplay function not found (attempt ${retryCount + 1})`);
                        if (retryCount < maxRetries) {
                            setTimeout(() => applyNotesToUI(retryCount + 1), 200);
                            return;
                        }
                    }
                } else {
                    console.log('[Notes] 📭 No notes to apply, clearing display');
                    // No notes - clear display
                    if (typeof window.updateSelectedNotesDisplay === 'function') {
                        window.updateSelectedNotesDisplay([], []);
                        console.log('[Notes] ✅ Display cleared');
                    }
                    if (window.jQuery && typeof window.jQuery.fn.select2 === 'function' && unifiedNoteSelect) {
                        const $select = window.jQuery(unifiedNoteSelect);
                        if ($select.length && $select.data('select2')) {
                            $select.val(null).trigger('change');
                        }
                    }
                }

                console.log('[Notes] 🏁 applyNotesToUI completed (attempt', retryCount + 1, ')');
            };

            // Apply immediately and with multiple retries
            console.log('[Notes] 🚀 Starting to apply notes to UI with multiple retries...');
            applyNotesToUI();
            setTimeout(() => {
                console.log('[Notes] 🔄 Retry 1 (100ms)');
                applyNotesToUI();
            }, 100);
            setTimeout(() => {
                console.log('[Notes] 🔄 Retry 2 (300ms)');
                applyNotesToUI();
            }, 300);
            setTimeout(() => {
                console.log('[Notes] 🔄 Retry 3 (600ms)');
                applyNotesToUI();
            }, 600);
            setTimeout(() => {
                console.log('[Notes] 🔄 Retry 4 (1000ms)');
                applyNotesToUI();
            }, 1000);
            setTimeout(() => {
                console.log('[Notes] 🔄 Retry 5 (2000ms)');
                applyNotesToUI();
            }, 2000);

            console.log('[Notes] ✅ loadSavedNotes function completed. Return value:', loaded);
            console.log('═══════════════════════════════════════════════════════════');
            return loaded;
        } catch (error) {
            console.error('[Notes] ❌ Error loading saved notes:', error);
            console.error('[Notes] Error stack:', error.stack);
            console.error('[Notes] Error details:', {
                message: error.message,
                name: error.name
            });
            console.log('═══════════════════════════════════════════════════════════');
            return false;
        }
    }

    // Make loadSavedNotes globally accessible
    window.loadSavedNotes = loadSavedNotes;

    // Debug: Log penalty values (for development only)
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('Penalty values:', {
            tajweedPenalty: SCORE_CONFIG.tajweedPenalty,
            performancePenalty: SCORE_CONFIG.performancePenalty
        });
    }

    // ════════════════════════════════════════════════════════════════
    // Toggle Relief Box
    // ════════════════════════════════════════════════════════════════
    function toggleReliefBox() {
        const reliefBox = document.getElementById('relief-box');
        const toggleIcon = document.getElementById('relief-toggle-icon');

        if (reliefBox.classList.contains('hidden')) {
            // إظهار البوكس
            reliefBox.classList.remove('hidden');
            setTimeout(() => {
                reliefBox.classList.remove('scale-95', 'opacity-0');
                reliefBox.classList.add('scale-100', 'opacity-100');
            }, 10);

            // تدوير الأيقونة
            if (toggleIcon) {
                toggleIcon.classList.add('rotate-180');
            }
        } else {
            // إخفاء البوكس
            reliefBox.classList.remove('scale-100', 'opacity-100');
            reliefBox.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                reliefBox.classList.add('hidden');
            }, 300);

            // إرجاع الأيقونة
            if (toggleIcon) {
                toggleIcon.classList.remove('rotate-180');
            }
        }
    }

    // ════════════════════════════════════════════════════════════════
    // Load Relief Settings
    // ════════════════════════════════════════════════════════════════
    // Cache for relief settings to avoid multiple requests
    let reliefSettingsCache = null;

    // Function to load relief settings from competition branch
    async function loadReliefSettings() {
        try {
            const branchId = '{{ $competition_version_branch_id }}';
            const settingId = '{{ $judging_form_setting_id }}';
            const response = await fetch(`/api/competition-branch/${branchId}/relief-settings?field=quran&judging_form_setting_id=${settingId}`);
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.relief_grade) {
                    const maxGrade = parseInt(data.relief_grade);
                    const displayBox = document.getElementById('relief-grade-display');
                    const hiddenInput = document.getElementById('relief-grade');
    
                    // بناء السيلكتور مثل التفسير
                    const select = document.createElement('select');
                    select.id = 'relief-grade-actual-select';
                    select.className = 'w-full rounded-lg text-sm px-3 py-1 bg-white border border-slate-200 text-emerald-700 font-bold focus:ring-2 focus:ring-emerald-500';
    
                    for (let v = 10; v <= maxGrade; v += 10) {
                        const opt = document.createElement('option');
                        opt.value = v;
                        opt.textContent = v;
                        if (v === maxGrade) opt.selected = true;
                        select.appendChild(opt);
                    }
    
                    displayBox.innerHTML = '';
                    displayBox.appendChild(select);
                    hiddenInput.value = maxGrade;
    
                    select.addEventListener('change', (e) => {
                        hiddenInput.value = e.target.value;
                    });
                }
            }
        } catch (error) { console.error('Error loading relief settings:', error); }
    }
    
    // 2. دالة إرسال الطلب (المحرك الرئيسي)
    async function handleReliefButtonClick() {
        const reliefBtn = document.getElementById('request-relief-btn');
        const reliefBtnText = document.getElementById('relief-btn-text');
        
        // قراءة الدرجة من السيلكتور المولد
        const actualSelect = document.getElementById('relief-grade-actual-select');
        const selectedGrade = actualSelect ? actualSelect.value : document.getElementById('relief-grade').value;
        const reason = document.getElementById('relief-reason').value;
    
        if (!selectedGrade || selectedGrade === "0") {
            showCustomNotification('تنبيه', 'يرجى اختيار درجة التخفيف أولاً', 'warning');
            return;
        }
    
        reliefBtn.disabled = true;
        reliefBtnText.textContent = 'جاري الإرسال...';
    
        try {
            const response = await fetch('/api/relief-requests/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    participant_id: '{{ $participant_id }}', // المعرف من جدول participations
                    competition_version_branch_id: '{{ $competition_version_branch_id }}',
                    judging_form_setting_id: '{{ $judging_form_setting_id }}',
                    grade: selectedGrade, // الدرجة التي اختارها المحكم حالياً
                    reason: reason || 'طلب تخفيف من محكم القرآن',
                    judge_name: '{{ auth()->user()->full_name }}',
                    participant_name: '{{ $participant_name }}',
                    field_type: 'quran'
                })
            });
    
            const result = await response.json();
            if (result.success) {
                showCustomNotification('تم بنجاح', 'تم إرسال طلب التخفيف وإشعار اللجنة');
                
                // تحديث شكل الزر مثل التفسير
                reliefBtn.disabled = true;
                reliefBtnText.textContent = 'تم الطلب مسبقاً';
                reliefBtn.className = 'bg-yellow-500 text-white font-bold py-1.5 px-4 rounded-lg text-sm cursor-not-allowed';
                
                // إرسال الإشعارات للأعضاء (Socket/Notification)
                sendReliefNotifications(result.request_id, selectedGrade);
                
                // تحديث القائمة فوراً
                loadPendingReliefRequests();
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            showCustomNotification('خطأ', error.message, 'error');
            reliefBtn.disabled = false;
            reliefBtnText.textContent = 'إرسال الطلب';
        }
    }
    
    // 3. دالة جلب الطلبات المعلقة (لضمان ظهورها عند رئيس اللجنة)
    async function loadPendingReliefRequests() {
        const branchId = '{{ $competition_version_branch_id }}';
        const response = await fetch(`/api/relief-requests/pending?competition_version_branch_id=${branchId}`);
        if (response.ok) {
            const data = await response.json();
            const list = Array.isArray(data) ? data : (data.requests || []);
            displayPendingReliefRequests(list);
        }
    }
    
    // استدعاء الدوال عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        loadReliefSettings();
        loadPendingReliefRequests();
        
        const btn = document.getElementById('request-relief-btn');
        if (btn) {
            btn.addEventListener('click', handleReliefButtonClick);
        }
        
        // فحص دوري كل 10 ثوانٍ (مثل الدراية)
        setInterval(loadPendingReliefRequests, 10000);
    });

    // Load relief settings when page loads
    document.addEventListener('DOMContentLoaded', function () {
        loadReliefSettings();
    });

    // Object to store scores for each question
    let questionScores = {};
    let questionModified = []; // Track if each question has been modified

    // Global scores (shared across all questions)
    let globalTajweedRemaining = SCORE_CONFIG.tajweedTotal;
    let globalPerformanceRemaining = SCORE_CONFIG.performanceTotal;

    // Per-question deductions (how much was deducted on each question)
    let perQuestionTajweedDeduction = Array(SCORE_CONFIG.totalQuestions).fill(0);
    let perQuestionPerformanceDeduction = Array(SCORE_CONFIG.totalQuestions).fill(0);

    // LocalStorage helper for global scores
    function getGlobalScoresStorageKey() {
        return `judging-global-scores-{{ $participant_id }}`;
    }
    
    function displayPendingReliefRequests(requests) {
        const container = document.getElementById('relief-requests-list');
        const section = document.getElementById('pending-relief-requests');
        if (!container || !section) return;
    
        if (!requests || requests.length === 0) {
            section.classList.add('hidden');
            return;
        }
    
        section.classList.remove('hidden');
        container.innerHTML = '';
    
        requests.forEach(request => {
            const div = document.createElement('div');
            div.className = 'bg-orange-50 border border-orange-100 p-2 rounded-lg mb-2 text-xs';
            div.innerHTML = `
                <div class="flex justify-between items-center">
                    <span class="font-bold text-orange-800">${request.judge_name}</span>
                    <span class="bg-orange-200 px-2 py-0.5 rounded text-orange-900">${request.grade}</span>
                </div>
                <div class="text-orange-700 mt-1">${request.participant_name}</div>
            `;
            container.appendChild(div);
        });
    }

    function saveGlobalScoresToLocalStorage() {
        try {
            const key = getGlobalScoresStorageKey();
            const data = {
                tajweed: globalTajweedRemaining,
                performance: globalPerformanceRemaining
            };
            localStorage.setItem(key, JSON.stringify(data));
            console.log('[Storage] ✅ Saved global scores:', data);
        } catch (e) {
            console.warn('Failed to save global scores to localStorage:', e);
        }
    }

    function loadGlobalScoresFromLocalStorage() {
        try {
            const key = getGlobalScoresStorageKey();
            const data = localStorage.getItem(key);
            if (data) {
                const parsed = JSON.parse(data);
                console.log('[Storage] ✅ Loaded global scores:', parsed);
                return parsed;
            }
            return null;
        } catch (e) {
            console.warn('Failed to load global scores from localStorage:', e);
            return null;
        }
    }

    // LocalStorage helper for per-question deductions
    function getPerQuestionDeductionsStorageKey() {
        return `judging-per-question-deductions-{{ $participant_id }}`;
    }

    function savePerQuestionDeductionsToLocalStorage() {
        try {
            const key = getPerQuestionDeductionsStorageKey();
            const data = {
                tajweed: perQuestionTajweedDeduction,
                performance: perQuestionPerformanceDeduction
            };
            localStorage.setItem(key, JSON.stringify(data));
            console.log('[Storage] ✅ Saved per-question deductions:', data);
        } catch (e) {
            console.warn('Failed to save per-question deductions to localStorage:', e);
        }
    }

    function loadPerQuestionDeductionsFromLocalStorage() {
        try {
            const key = getPerQuestionDeductionsStorageKey();
            const data = localStorage.getItem(key);
            if (data) {
                const parsed = JSON.parse(data);
                console.log('[Storage] ✅ Loaded per-question deductions:', parsed);
                return parsed;
            }
            return null;
        } catch (e) {
            console.warn('Failed to load per-question deductions from localStorage:', e);
            return null;
        }
    }

    // Legacy storage (kept for backward compatibility)
    function getScoresStorageKey() {
        return `judging-scores-{{ $participant_id }}`;
    }

    function saveScoresToLocalStorage() {
        try {
            const key = getScoresStorageKey();
            localStorage.setItem(key, JSON.stringify(questionScores));
        } catch (e) {
            console.warn('Failed to save scores to localStorage:', e);
        }
    }

    function loadScoresFromLocalStorage() {
        try {
            const key = getScoresStorageKey();
            const data = localStorage.getItem(key);
            return data ? JSON.parse(data) : null;
        } catch (e) {
            console.warn('Failed to load scores from localStorage:', e);
            return null;
        }
    }

    // Initialize scores for all questions
    function initializeQuestionScores() {
        console.log('[Quran Init] 🚀 Starting initializeQuestionScores...');
        
        // استخدام البيانات المحملة من footer.blade.php إذا كانت متوفرة
        if (window.backendPerQuestionTajweedDeductions && window.backendPerQuestionPerformanceDeductions) {
            console.log('[Quran Init] ✅ Using backend deductions from footer.blade.php');
            perQuestionTajweedDeduction = [...window.backendPerQuestionTajweedDeductions];
            perQuestionPerformanceDeduction = [...window.backendPerQuestionPerformanceDeductions];
            
            const totalTajweedDeducted = window.backendTotalTajweedDeduction || 0;
            const totalPerformanceDeducted = window.backendTotalPerformanceDeduction || 0;
            
            window.globalTajweedRemaining = Math.max(0, SCORE_CONFIG.tajweedTotal - totalTajweedDeducted);
            window.globalPerformanceRemaining = Math.max(0, SCORE_CONFIG.performanceTotal - totalPerformanceDeducted);
            
            console.log('[Quran Init] ✅ Loaded from backend:', {
                tajweedRemaining: window.globalTajweedRemaining,
                performanceRemaining: window.globalPerformanceRemaining,
                perQuestionTajweed: perQuestionTajweedDeduction,
                perQuestionPerformance: perQuestionPerformanceDeduction
            });
        } else if (window.allAnswers && window.allAnswers.length > 0) {
            // Fallback: استخدام البيانات من allAnswers
            console.log('[Quran Init] ⚠️ Backend deductions not available, loading from allAnswers...');
            
            perQuestionTajweedDeduction = Array(SCORE_CONFIG.totalQuestions).fill(0);
            perQuestionPerformanceDeduction = Array(SCORE_CONFIG.totalQuestions).fill(0);
            
            let totalTajweedDeducted = 0;
            let totalPerformanceDeducted = 0;
        
            window.allAnswers.forEach((ans, index) => {
                if (ans) {
                    const tDeduct = parseFloat(ans.tajweed_score || 0);
                    const pDeduct = parseFloat(ans.performance_score || 0);
                    
                    perQuestionTajweedDeduction[index] = tDeduct;
                    perQuestionPerformanceDeduction[index] = pDeduct;
                    
                    totalTajweedDeducted += tDeduct;
                    totalPerformanceDeducted += pDeduct;
                }
            });
        
            // تحديث الرصيد العالمي المتبقي بناءً على ما تم خصمه سابقاً
            window.globalTajweedRemaining = Math.max(0, SCORE_CONFIG.tajweedTotal - totalTajweedDeducted);
            window.globalPerformanceRemaining = Math.max(0, SCORE_CONFIG.performanceTotal - totalPerformanceDeducted);
            
            console.log('[Quran Init] ✅ Loaded from allAnswers:', {
                tajweedRemaining: window.globalTajweedRemaining,
                performanceRemaining: window.globalPerformanceRemaining,
                perQuestionTajweed: perQuestionTajweedDeduction,
                perQuestionPerformance: perQuestionPerformanceDeduction
            });
        } else {
            // Final fallback: جلب التقييمات القادمة من الكنترولر (قاعدة البيانات)
            console.log('[Quran Init] ⚠️ Loading from controller...');
            const existingEvaluationsRaw = @json($existingEvaluations ?? collect());
            
            // Convert array to object keyed by question_id for easy lookup
            const existingEvaluations = {};
            if (Array.isArray(existingEvaluationsRaw)) {
                existingEvaluationsRaw.forEach(eval => {
                    const questionId = String(eval.quran_question_id || eval.question_id || '');
                    if (questionId) {
                        existingEvaluations[questionId] = eval;
                    }
                });
            } else if (existingEvaluationsRaw && typeof existingEvaluationsRaw === 'object') {
                // Already an object, use as is
                Object.assign(existingEvaluations, existingEvaluationsRaw);
            }
            
            console.log('[Quran Init] Existing evaluations mapped:', existingEvaluations);
            
            perQuestionTajweedDeduction = Array(SCORE_CONFIG.totalQuestions).fill(0);
            perQuestionPerformanceDeduction = Array(SCORE_CONFIG.totalQuestions).fill(0);
            
            let totalTajweedDeducted = 0;
            let totalPerformanceDeducted = 0;
        
            const questionsData = document.querySelectorAll('#questions-data > div');
            questionsData.forEach((qEl, index) => {
                const qId = String(qEl.dataset.questionId);
                if (existingEvaluations[qId]) {
                    const eval = existingEvaluations[qId];
                    // في القرآن: tajweed_score و performance_score يخزنان قيمة الخصم
                    const tDeduct = parseFloat(eval.tajweed_score || 0);
                    const pDeduct = parseFloat(eval.performance_score || 0);
                    
                    perQuestionTajweedDeduction[index] = tDeduct;
                    perQuestionPerformanceDeduction[index] = pDeduct;
                    
                    totalTajweedDeducted += tDeduct;
                    totalPerformanceDeducted += pDeduct;
                }
            });
        
            // تحديث الرصيد العالمي المتبقي بناءً على ما تم خصمه سابقاً
            window.globalTajweedRemaining = Math.max(0, SCORE_CONFIG.tajweedTotal - totalTajweedDeducted);
            window.globalPerformanceRemaining = Math.max(0, SCORE_CONFIG.performanceTotal - totalPerformanceDeducted);
        }
        
        window.perQuestionTajweedDeduction = perQuestionTajweedDeduction;
        window.perQuestionPerformanceDeduction = perQuestionPerformanceDeduction;
    
        updateScoreDisplay(window.currentIndex || 0);
        
        console.log('[Quran Init] ✅ Completed. Final state:', {
            tajweedRemaining: window.globalTajweedRemaining,
            performanceRemaining: window.globalPerformanceRemaining,
            perQuestionTajweed: perQuestionTajweedDeduction,
            perQuestionPerformance: perQuestionPerformanceDeduction
        });
    }

    // Function to update score display based on current question
    function updateScoreDisplay(currentQuestionIndex) {
        const tajweedInput = document.getElementById('global-tajweed');
        const performanceInput = document.getElementById('global-performance');
    
        // عرض الرصيد المتبقي العالمي (وليس درجة السؤال الواحد)
        if (tajweedInput) {
            tajweedInput.value = `${window.globalTajweedRemaining}/${SCORE_CONFIG.tajweedTotal}`;
        }
    
        if (performanceInput) {
            performanceInput.value = `${window.globalPerformanceRemaining}/${SCORE_CONFIG.performanceTotal}`;
        }
    }

    // Function to change question score (increment/decrement by penalty value)
    function changeQuestionScore(inputId, change, maxScore) {
        const input = document.getElementById(inputId);
        if (!input) return;
    
        const currentQuestionIndex = window.currentIndex; 
        let penaltyValue = (inputId === 'global-tajweed') ? SCORE_CONFIG.tajweedPenalty : SCORE_CONFIG.performancePenalty;
        let totalPossible = (inputId === 'global-tajweed') ? SCORE_CONFIG.tajweedTotal : SCORE_CONFIG.performanceTotal;
    
        if (inputId === 'global-tajweed') {
            if (change > 0) { // زر الزائد (+) لإلغاء خصم تم مسبقاً
                // نأكد أولاً أن هذا السؤال هو الذي تسبب في الخصم
                if (window.perQuestionTajweedDeduction[currentQuestionIndex] > 0) {
                    window.globalTajweedRemaining = Math.min(window.globalTajweedRemaining + penaltyValue, totalPossible);
                    window.perQuestionTajweedDeduction[currentQuestionIndex] -= penaltyValue;
                }
            } else { // زر الناقص (-) لإضافة خصم جديد
                if (window.globalTajweedRemaining >= penaltyValue) {
                    window.globalTajweedRemaining = Math.max(window.globalTajweedRemaining - penaltyValue, 0);
                    window.perQuestionTajweedDeduction[currentQuestionIndex] = (window.perQuestionTajweedDeduction[currentQuestionIndex] || 0) + penaltyValue;
                }
            }
        } else { // الأداء
            if (change > 0) {
                if (window.perQuestionPerformanceDeduction[currentQuestionIndex] > 0) {
                    window.globalPerformanceRemaining = Math.min(window.globalPerformanceRemaining + penaltyValue, totalPossible);
                    window.perQuestionPerformanceDeduction[currentQuestionIndex] -= penaltyValue;
                }
            } else {
                if (window.globalPerformanceRemaining >= penaltyValue) {
                    window.globalPerformanceRemaining = Math.max(window.globalPerformanceRemaining - penaltyValue, 0);
                    window.perQuestionPerformanceDeduction[currentQuestionIndex] = (window.perQuestionPerformanceDeduction[currentQuestionIndex] || 0) + penaltyValue;
                }
            }
        }
    
        // حفظ الرصيد العالمي والخصومات في التخزين المحلي فوراً
        saveGlobalScoresToLocalStorage();
        savePerQuestionDeductionsToLocalStorage();
    
        // تحديث العرض النصي (50/50)
        updateScoreDisplay(currentQuestionIndex);
        
        // حفظ بيانات السؤال الحالي
        if (typeof saveCurrentAnswer === 'function') saveCurrentAnswer();
        
        // تحديث المجموع النهائي الكبير
        updateTotalScoreDisplay();
    }

    // Function to get current question index
    function getCurrentQuestionIndex() {
        // Look for active question item
        const activeQuestion = document.querySelector('.question-item.bg-primary');
        if (activeQuestion) {
            return parseInt(activeQuestion.getAttribute('data-question-index')) || 0;
        }
        return 0;
    }

    /**
     * تحديث عرض المجموع النهائي بناءً على جميع الأسئلة
     * المنطق: خصم التنبيهات والفتح فقط، مع استبعاد التنبيهات التي تحولت لفتح
     */
//     function updateTotalScoreDisplay() {
//     const finalScoreElement = document.getElementById('final-score-display');
//     const deductionInfoElement = document.getElementById('deduction-info');
//     if (!finalScoreElement) return;

//     const initialTotalScore = {{ $total_score }};
//     const fat7Penalty = {{ $fat7_penalty }};
//     const alertPrice = {{ $alert_new_position_penalty }};
//     const maxAlertsLimit = {{ $alert_before_fat7 }}; // القيمة 2

//     let totalDeductions = 0;
//     const currentIdx = window.currentIndex;

//     if (Array.isArray(window.allAnswers)) {
//         window.allAnswers.forEach((ans, index) => {
//             let alerts = 0;
//             let fat7 = 0;

//             // جلب البيانات: من الـ DOM للسؤال الحالي، ومن الذاكرة للأسئلة الأخرى
//             if (index === currentIdx) {
//                 const form = document.getElementById('current-answer-form');
//                 if (form) {
//                     alerts = parseInt(form.querySelector('[name="alert_new_position"]')?.value || 0);
//                     fat7 = parseInt(form.querySelector('[name="fat7_points"]')?.value || 0);
//                 }
//             } else if (ans) {
//                 alerts = parseInt(ans.alert_new_position || 0);
//                 fat7 = parseInt(ans.fat7_points || 0);
//             }

//             // --- المعادلة الصحيحة: طرح التنبيهات التي أدت لفتح ---
//             const consumed = fat7 * maxAlertsLimit;
//             const netAlerts = Math.max(0, alerts - consumed);

//             const qPenalty = (netAlerts * alertPrice) + (fat7 * fat7Penalty);
//             totalDeductions += qPenalty;
//         });
//     }

//     const finalScore = Math.max(0, initialTotalScore - totalDeductions);
//     finalScoreElement.textContent = finalScore.toFixed(1);
    
//     if (deductionInfoElement) {
//         deductionInfoElement.textContent = totalDeductions > 0 ? `(-${totalDeductions.toFixed(1)})` : '';
//     }
// }

    // ============================================================
    //  نظام إدارة التنبيهات والفتح المنفصل لكل سؤال
    // ============================================================

    // 1. مخزن البيانات: لتخزين حالة التنبيهات والفتح لكل سؤال على حدة
    //  المفتاح هو currentIndex القادم من سكربت الفوتر
    let questionsAlertHistory = {};

    // دالة لحفظ حالة السؤال الحالي قبل الانتقال لسؤال آخر
    // نستقبل رقم السؤال الحالي صراحة من سكربت الفوتر (currentIndex)
    function saveCurrentQuestionState(indexOverride) {
        // إذا كنا في حالة استعادة، لا نحفظ
        if (isRestoring) {
            console.log('[Alerts] ⏸️ Skipping save - currently restoring');
            return;
        }
        
        const indexKey = typeof indexOverride === 'number' ? indexOverride : (window.currentIndex || 0);
        console.log('[Alerts] 💾 saveCurrentQuestionState() called for index', indexKey);

        const container = document.getElementById('alert-open-rows-container');
        if (!container) {
            console.log('[Alerts] ⚠️ Container not found, skipping save');
            return; // إذا لم يوجد البوكس لا تفعل شيئاً
        }

        const rows = [];
        // المرور على كل صف وتخزين حالته (عدد التنبيهات، وهل هو مفتوح)
        const allRows = container.querySelectorAll('.flex.items-center[data-row-index]');
        console.log(`[Alerts] 💾 saveCurrentQuestionState - Found ${allRows.length} rows in DOM for Q${indexKey}`);

        allRows.forEach((row, index) => {
            const alertBtn = row.querySelector('.alert-row-btn');
            const openBtn = row.querySelector('.open-row-btn');
            const alertLabel = row.querySelector('.alert-label');

            // استخراج الرقم من alertLabel (الآن يحتوي فقط على الرقم)
            let alertsCount = 0;
            if (alertLabel) {
                alertsCount = parseInt(alertLabel.textContent.trim()) || 0;
            }

            // التحقق مما إذا كان زر الفتح مفعلاً (نص "تم الفتح")
            const isOpened = openBtn ? (openBtn.textContent.trim() === 'تم الفتح' || openBtn.disabled === true) : false;

            console.log(`[Alerts] 💾 Row ${index + 1}: alerts=${alertsCount}, opened=${isOpened}, openBtnText="${openBtn ? openBtn.textContent.trim() : 'N/A'}"`);

            // نحفظ فقط الصفوف التي لها تنبيهات أو فتح (نحذف الصفوف الفارغة)
            // ملاحظة: الصف الفارغ الأخير لا يُحفظ لأنه يُضاف تلقائياً عند الاستعادة
            if (alertsCount > 0 || isOpened) {
                rows.push({
                    alerts: alertsCount,
                    opened: isOpened === true // تأكد من أن opened boolean
                });
                console.log(`[Alerts] 💾 Saved row ${index + 1}: alerts=${alertsCount}, opened=${isOpened}`);
            } else {
                // تخطي الصفوف الفارغة (خاصة الصف الأخير الذي يُضاف تلقائياً)
                console.log(`[Alerts] 💾 Skipped empty row ${index + 1}`);
            }
        });

        console.log(`[Alerts] 💾 Total saved rows: ${rows.length} for Q${indexKey}`);

        // حفظ القيم الإجمالية (المستخدمة في الحسابات الخلفية)
        const form = document.getElementById('current-answer-form');
        let alertTotal = 0;
        let fat7Total = 0;

        if (form) {
            const alertInput = form.querySelector('[name="alert_new_position"]');
            const fat7Input = form.querySelector('[name="fat7_points"]');
            alertTotal = alertInput ? parseInt(alertInput.value) || 0 : 0;
            fat7Total = fat7Input ? parseInt(fat7Input.value) || 0 : 0;
        }

        // ⚠️ مهم جداً: إذا لم نجد صفوف في DOM ولكن هناك بيانات محفوظة مسبقاً، نحافظ عليها
        // هذا يمنع فقدان البيانات عند استدعاء saveCurrentQuestionState قبل restoreQuestionState
        let rowsToSave = rows;
        // التحقق من وجود بيانات محفوظة حتى لو كانت قيم الحقول 0 (مثل التحميل الأولي)
        const hasSavedData = (Object.prototype.hasOwnProperty.call(questionsAlertHistory, indexKey) && 
                              questionsAlertHistory[indexKey].rows && 
                              Array.isArray(questionsAlertHistory[indexKey].rows) && 
                              questionsAlertHistory[indexKey].rows.length > 0) ||
                             (window.allAnswers && window.allAnswers[indexKey] && 
                              ((window.allAnswers[indexKey].alert_rows && Array.isArray(window.allAnswers[indexKey].alert_rows) && window.allAnswers[indexKey].alert_rows.length > 0) ||
                               (parseInt(window.allAnswers[indexKey].alert_new_position) || 0) > 0 || 
                               (parseInt(window.allAnswers[indexKey].fat7_points) || 0) > 0));
        
        if (rows.length === 0 && ((alertTotal > 0 || fat7Total > 0) || hasSavedData)) {
            // تحقق من وجود بيانات محفوظة مسبقاً في questionsAlertHistory
            if (Object.prototype.hasOwnProperty.call(questionsAlertHistory, indexKey)) {
                const existingData = questionsAlertHistory[indexKey];
                if (existingData.rows && Array.isArray(existingData.rows) && existingData.rows.length > 0) {
                    console.log(`[Alerts] 💾 ⚠️ Found 0 rows in DOM but existing history has ${existingData.rows.length} rows for Q${indexKey}, preserving existing data`);
                    rowsToSave = JSON.parse(JSON.stringify(existingData.rows));
                    // استخدام القيم من الحقول إذا كانت موجودة، وإلا استخدام القيم المحفوظة
                    alertTotal = alertTotal > 0 ? alertTotal : (parseInt(existingData.totalAlerts) || 0);
                    fat7Total = fat7Total > 0 ? fat7Total : (parseInt(existingData.totalFat7) || 0);
                }
            } else if (window.allAnswers && window.allAnswers[indexKey]) {
                // تحقق من وجود بيانات في allAnswers
                const savedData = window.allAnswers[indexKey];
                if (savedData.alert_rows && Array.isArray(savedData.alert_rows) && savedData.alert_rows.length > 0) {
                    console.log(`[Alerts] 💾 ⚠️ Found 0 rows in DOM but allAnswers has ${savedData.alert_rows.length} rows for Q${indexKey}, preserving existing data`);
                    rowsToSave = JSON.parse(JSON.stringify(savedData.alert_rows));
                    // استخدام القيم من الحقول إذا كانت موجودة، وإلا استخدام القيم المحفوظة
                    alertTotal = alertTotal > 0 ? alertTotal : (parseInt(savedData.alert_new_position) || 0);
                    fat7Total = fat7Total > 0 ? fat7Total : (parseInt(savedData.fat7_points) || 0);
                } else if ((parseInt(savedData.alert_new_position) || 0) > 0 || (parseInt(savedData.fat7_points) || 0) > 0) {
                    // إذا لم تكن alert_rows موجودة ولكن القيم الإجمالية موجودة، نبني الصفوف
                    console.log(`[Alerts] 💾 ⚠️ Found 0 rows in DOM but allAnswers has totals for Q${indexKey}, rebuilding rows from totals`);
                    const totalAlerts = parseInt(savedData.alert_new_position) || 0;
                    const totalFat7 = parseInt(savedData.fat7_points) || 0;
                    rowsToSave = [];
                    
                    // بناء صفوف الفتح أولاً
                    for(let i=0; i < totalFat7; i++) {
                        rowsToSave.push({ alerts: 0, opened: true });
                    }
                    
                    // بناء صفوف التنبيهات
                    let alertsRemaining = totalAlerts;
                    while (alertsRemaining > 0) {
                        const alertsInThisRow = Math.min(alertsRemaining, 1);
                        rowsToSave.push({ alerts: alertsInThisRow, opened: false });
                        alertsRemaining -= alertsInThisRow;
                    }
                    
                    // استخدام القيم من الحقول إذا كانت موجودة، وإلا استخدام القيم المحفوظة
                    alertTotal = alertTotal > 0 ? alertTotal : totalAlerts;
                    fat7Total = fat7Total > 0 ? fat7Total : totalFat7;
                }
            }
        }

        // تخزين كل شيء في الكائن questionsAlertHistory مفهرساً برقم السؤال
        // استخدام نسخة عميقة لتجنب التعديل على المرجع الأصلي
        questionsAlertHistory[indexKey] = {
            rows: JSON.parse(JSON.stringify(rowsToSave)), // نسخة عميقة
            totalAlerts: String(alertTotal),
            totalFat7: String(fat7Total)
        };

        console.log(`[Alerts] 💾 ✅ Saved state for Q${indexKey}:`, questionsAlertHistory[indexKey]);
    }

    // متغير لمنع الحفظ أثناء الاستعادة
    let isRestoring = false;

    // دالة لاسترجاع حالة سؤال معين عند الانتقال إليه
    function restoreQuestionState(index) {
        const container = document.getElementById('alert-open-rows-container');
        const form = document.getElementById('current-answer-form');
        if (!container || !form) return;
        
        // تعيين علامة الاستعادة لمنع الحفظ التلقائي
        isRestoring = true;
    
        // جلب الحقول المخفية التي تخزن الأرقام النهائية
        const alertInput = form.querySelector('[name="alert_new_position"]');
        const fat7Input = form.querySelector('[name="fat7_points"]');
    
        // 1. مسح كافة الصفوف الظاهرة في البوكس
        container.innerHTML = '';
    
        // 2. محاولة جلب البيانات من questionsAlertHistory أولاً
        let alertValue = "0";
        let fat7Value = "0";
        let rows = [];
        
        if (Object.prototype.hasOwnProperty.call(questionsAlertHistory, index)) {
            const data = questionsAlertHistory[index];
            alertValue = String(data.totalAlerts || 0);
            fat7Value = String(data.totalFat7 || 0);
            rows = data.rows || [];
            console.log(`[Restore] ✅ Found history for Q${index}:`, data);
        } else if (window.allAnswers && window.allAnswers[index]) {
            // إذا لم يكن هناك history، استخدم البيانات من allAnswers مباشرة
            const savedData = window.allAnswers[index];
            alertValue = String(savedData.alert_new_position || 0);
            fat7Value = String(savedData.fat7_points || 0);
            
            // إذا كانت تفاصيل الصفوف محفوظة، استخدمها مباشرة
            if (savedData.alert_rows && Array.isArray(savedData.alert_rows) && savedData.alert_rows.length > 0) {
                rows = savedData.alert_rows;
                console.log(`[Restore] ✅ Using saved alert_rows from allAnswers for Q${index}:`, rows);
            } else {
                // Fallback: إعادة بناء الصفوف من القيم الإجمالية
                // ملاحظة: هذا تقريبي فقط - الأفضل هو حفظ alert_rows في قاعدة البيانات
                const fat7Remaining = parseInt(fat7Value) || 0;
                let alertsRemaining = parseInt(alertValue) || 0;
                const maxAlerts = {{ $alert_before_fat7 ?? 3 }};
                
                console.log(`[Restore] 🔄 Building rows from totals for Q${index}:`, {
                    alertValue,
                    fat7Value,
                    fat7Remaining,
                    alertsRemaining,
                    maxAlerts
                });
                
                // إعادة توليد الصفوف
                // كل صف فتح يجب أن يكون alerts: 0 لأنه تم فتحه بالفعل
                for(let i=0; i < fat7Remaining; i++) {
                    rows.push({ alerts: 0, opened: true });
                }
                
                // تقسيم التنبيهات المتبقية على صفوف منفصلة (كل صف 1 تنبيه)
                // هذا يحل مشكلة ظهور 2 تنبيه في صف واحد
                while (alertsRemaining > 0) {
                    const alertsInThisRow = Math.min(alertsRemaining, 1); // كل صف 1 تنبيه فقط
                    rows.push({ alerts: alertsInThisRow, opened: false });
                    alertsRemaining -= alertsInThisRow;
                }
            }
            
            // حفظ في questionsAlertHistory للمرة القادمة
            questionsAlertHistory[index] = {
                rows: rows,
                totalAlerts: alertValue,
                totalFat7: fat7Value
            };
            
            console.log(`[Restore] ✅ Built and saved history for Q${index}:`, questionsAlertHistory[index]);
        }
    
        // 3. استعادة الأرقام في الحقول المخفية
        if (alertInput) alertInput.value = alertValue;
        if (fat7Input) fat7Input.value = fat7Value;
    
        // 4. إعادة بناء الصفوف - استخدام نسخة من rows لتجنب التعديل على الأصل
        const rowsToRestore = JSON.parse(JSON.stringify(rows)); // نسخة عميقة
        console.log(`[Restore] 🔨 Building ${rowsToRestore.length} rows for Q${index}:`, rowsToRestore);
        
        if (rowsToRestore && rowsToRestore.length > 0) {
            rowsToRestore.forEach((rowState, i) => {
                console.log(`[Restore] Creating row ${i + 1}:`, rowState);
                createAlertRow(i + 1, rowState.alerts, rowState.opened);
            });
            // إضافة صف فارغ في النهاية للاستخدام فقط إذا لم يكن هناك صف فارغ بالفعل
            // التحقق من أن آخر صف ليس فارغاً قبل إضافة صف فارغ جديد
            const lastRow = rowsToRestore[rowsToRestore.length - 1];
            const shouldAddEmptyRow = !(lastRow && lastRow.alerts === 0 && !lastRow.opened);
            if (shouldAddEmptyRow) {
                console.log(`[Restore] Adding empty row ${rowsToRestore.length + 1}`);
                createAlertRow(rowsToRestore.length + 1, 0, false);
            } else {
                console.log(`[Restore] Skipping empty row - last row is already empty`);
            }
        } else {
            // ابدأ بصف واحد فارغ
            console.log(`[Restore] No rows to restore, creating single empty row`);
            createAlertRow(1, 0, false);
        }
    
        // 5. تحديث المجموع الظاهر على الشاشة (بدون حفظ تلقائي)
        if (typeof window.updateTotalScoreDisplay === 'function') {
            window.updateTotalScoreDisplay();
        }
        
        // التحقق من عدد الصفوف بعد الاستعادة
        const finalRows = container.querySelectorAll('.flex.items-center[data-row-index]').length;
        console.log(`[Restore] ✅ Restored Q${index}: alerts=${alertValue}, fat7=${fat7Value}, savedRows=${rows.length}, finalRows=${finalRows}`);
        
        // التحقق من أن البيانات المستعادة صحيحة
        const restoredRows = [];
        container.querySelectorAll('.flex.items-center[data-row-index]').forEach((row, i) => {
            const alertLabel = row.querySelector('.alert-label');
            const openBtn = row.querySelector('.open-row-btn');
            const alertsCount = alertLabel ? parseInt(alertLabel.textContent.trim()) || 0 : 0;
            const isOpened = openBtn ? (openBtn.textContent.trim() === 'تم الفتح' || openBtn.disabled === true) : false;
            restoredRows.push({ alerts: alertsCount, opened: isOpened });
        });
        console.log(`[Restore] 🔍 Verification - Restored rows state:`, restoredRows);
        
        // إلغاء علامة الاستعادة بعد الانتهاء (بعد تأكيد أن كل شيء تم بشكل صحيح)
        setTimeout(() => {
            isRestoring = false;
            console.log(`[Restore] ✅ Restoration complete, isRestoring set to false`);
        }, 200);
    }

    // دالة إنشاء صف واحد (تم تعديلها لتقبل القيم الأولية للاسترجاع)
    // function createAlertRow(rowIndex, initialAlerts = 0, initialOpened = false) {
    //     const container = document.getElementById('alert-open-rows-container');
    //     const form = document.getElementById('current-answer-form');
    //     if (!container || !form) return;

    //     const maxAlerts = parseInt(container.dataset.maxAlerts || '3');

    //     // إنشاء عنصر الصف
    //     const row = document.createElement('div');
    //     row.className = 'flex items-center gap-2 px-3 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0 fade-in';
    //     row.setAttribute('data-row-index', rowIndex); // إضافة معرف للصف

    //     // تحديد خصائص الأزرار بناءً على الحالة المسترجعة
    //     const openBtnClass = initialOpened
    //         ? 'bg-red-50 text-red-400 cursor-not-allowed border border-red-100'
    //         : 'bg-orange-50 text-orange-600 hover:bg-orange-100 border border-orange-200';

    //     const openBtnText = initialOpened ? 'تم الفتح' : 'فتح';
    //     const alertBtnState = initialOpened ? 'opacity-60 cursor-default' : '';
    //     const alertBtnDisabled = initialOpened ? 'disabled' : '';
    //     const openBtnDisabled = initialOpened ? 'disabled' : '';

    //     // تحديد حجم زر التنبيه: كبير للصف الأول، عادي للباقي
    //     const isFirstRow = rowIndex === 1;
    //     const alertBtnSize = isFirstRow
    //         ? 'py-3 px-4 text-base'
    //         : 'py-2 px-3 text-sm';
    //     const alertLabelSize = isFirstRow
    //         ? 'text-xl font-bold'
    //         : 'text-base font-bold';
    //     const alertTextSize = isFirstRow
    //         ? 'text-base font-bold'
    //         : 'text-sm font-bold';
    //     const alertGap = isFirstRow ? 'gap-4' : 'gap-3';

    //     // بناء HTML الصف - الترتيب الجديد: رقم الصف | التنبيه (حجم كبير للصف الأول) | الفتح (برتقالي، حجم أقل) | الاستعادة (آخر)
    //     row.innerHTML = `
    //         <div class="w-6 text-center text-xs text-slate-500 font-medium row-number" style="order: 1;">${rowIndex}</div>
    //         <button type="button" ${alertBtnDisabled}
    //                 class="alert-row-btn bg-amber-50 border border-amber-100 rounded-lg ${alertBtnSize} flex items-center justify-center ${alertGap} ${alertBtnState}" style="order: 2; flex: 1.2;">
    //             <span class="text-amber-800 ${alertTextSize} cursor-pointer hover:text-amber-900 transition-colors select-none">تنبيه</span>
    //             <span class="text-amber-900 ${alertLabelSize} alert-label cursor-pointer hover:text-amber-950 transition-colors select-none">${initialAlerts}</span>
    //         </button>
    //         <button type="button" ${openBtnDisabled}
    //                 class="open-row-btn py-1.5 px-2 rounded-lg font-bold flex items-center justify-center gap-1 transition-all text-xs ${openBtnClass}" style="order: 3; flex: 0.75;">
    //             ${openBtnText}
    //         </button>
    //         <button type="button"
    //                 class="undo-row-btn w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" style="order: 4;">
    //             <i class="fas fa-undo text-[10px]"></i>
    //         </button>
    //     `;

    //     // تعريف المتغيرات المحلية للتحكم في حالة هذا الصف
    //     let rowAlerts = initialAlerts;
    //     let rowOpened = initialOpened;

    //     const alertBtn = row.querySelector('.alert-row-btn');
    //     const alertLabel = row.querySelector('.alert-label');
    //     const openBtn = row.querySelector('.open-row-btn');
    //     const undoBtn = row.querySelector('.undo-row-btn');

    //     const alertInput = form.querySelector('[name="alert_new_position"]');
    //     const fat7Input = form.querySelector('[name="fat7_points"]');

    //     // دالة مساعدة لتطبيق حالة الفتح
    //     const applyOpenState = () => {
    //         rowOpened = true;
    //         openBtn.classList.remove('bg-orange-50', 'text-orange-600', 'hover:bg-orange-100', 'border-orange-200');
    //         openBtn.classList.add('bg-red-50', 'text-red-400', 'cursor-not-allowed', 'border-red-100');
    //         openBtn.innerHTML = 'تم الفتح';
    //         openBtn.disabled = true;
    //         alertBtn.classList.add('opacity-60', 'cursor-default');
    //         alertBtn.disabled = true;
    //         if (alertLabel) {
    //             alertLabel.classList.remove('cursor-pointer', 'hover:text-amber-900');
    //             alertLabel.classList.add('cursor-default', 'opacity-60');
    //             alertLabel.style.pointerEvents = 'none';
    //         }
    //     };

    //     // دالة مشتركة لإضافة تنبيه
    //     const addAlert = (e) => {
    //         e.stopPropagation();
    //         if (rowOpened) return;

    //         rowAlerts++;
    //         // تحديث العدد فقط (كلمة "تنبيه" ثابتة)
    //         if (alertLabel) {
    //             alertLabel.textContent = rowAlerts;
    //         }

    //         // تحديث الإجمالي العام
    //         const currentTotalAlerts = parseInt(alertInput.value || '0') || 0;
    //         alertInput.value = currentTotalAlerts + 1;

    //         // إذا كان في آخر صف، نولد صف جديد تلقائياً
    //         const currentRowNum = parseInt(row.querySelector('.row-number').textContent);
    //         const totalRows = container.children.length;

    //         if (currentRowNum === totalRows) {
    //             createAlertRow(totalRows + 1);
    //         }

    //         // إذا وصل للحد الأقصى للتنبيهات، يتحول لفتح تلقائياً
    //         if (maxAlerts > 0 && rowAlerts >= maxAlerts) {
    //             const currentFat7 = parseInt(fat7Input.value || '0') || 0;
    //             fat7Input.value = currentFat7 + 1;
    //             applyOpenState();
    //         }

    //         if (typeof window.updateTotalScoreDisplay === 'function') {
    //             window.updateTotalScoreDisplay();
    //         }
    //     };

    //     // حدث: الضغط على كلمة "تنبيه" نفسها لإضافة تنبيه
    //     if (alertLabel) {
    //         alertLabel.addEventListener('click', addAlert);
    //     }

    //     // أيضاً يمكن الضغط على زر التنبيه كامل لإضافة تنبيه
    //     if (alertBtn) {
    //         alertBtn.addEventListener('click', (e) => {
    //             // فقط إذا لم يتم النقر على العناصر الأخرى
    //             if (e.target === alertBtn || e.target === alertLabel || e.target.closest('.alert-row-btn') === alertBtn) {
    //                 addAlert(e);
    //             }
    //         });
    //     }

    //     // حدث: الضغط على زر الفتح
    //     openBtn.addEventListener('click', () => {
    //         if (rowOpened) return;

    //         const currentFat7 = parseInt(fat7Input.value || '0') || 0;
    //         fat7Input.value = currentFat7 + 1;
    //         applyOpenState();

    //         // عند الفتح، ننشئ صفاً جديداً إذا كنا في آخر صف
    //         const currentRowNum = parseInt(row.querySelector('.row-number').textContent);
    //         const totalRows = container.children.length;
    //         if (currentRowNum === totalRows) {
    //             createAlertRow(totalRows + 1);
    //         }

    //         if (typeof window.updateTotalScoreDisplay === 'function') {
    //             window.updateTotalScoreDisplay();
    //         }
    //     });

    //     // حدث: الضغط على زر التراجع
    //     undoBtn.addEventListener('click', () => {
    //         if (rowAlerts <= 0 && !rowOpened) return;

    //         const currentRowNum = parseInt(row.querySelector('.row-number').textContent);
    //         const totalRows = container.children.length;

    //         // التراجع عن التنبيهات
    //         if (rowAlerts > 0) {
    //             rowAlerts--;
    //             // تحديث العدد فقط (كلمة "تنبيه" ثابتة)
    //             if (alertLabel) {
    //                 alertLabel.textContent = rowAlerts;
    //             }
    //             const currentTotalAlerts = parseInt(alertInput.value || '0') || 0;
    //             alertInput.value = Math.max(0, currentTotalAlerts - 1);
    //         }

    //         // التراجع عن الفتح
    //         if (rowOpened) {
    //             const currentFat7 = parseInt(fat7Input.value || '0') || 0;
    //             fat7Input.value = Math.max(0, currentFat7 - 1);
    //             rowOpened = false;

    //             // إعادة تفعيل الأزرار
    //             openBtn.disabled = false;
    //             openBtn.classList.remove('bg-red-50', 'text-red-400', 'cursor-not-allowed', 'border-red-100');
    //             openBtn.classList.add('bg-orange-50', 'text-orange-600', 'hover:bg-orange-100', 'border-orange-200');
    //             openBtn.innerHTML = 'فتح';
    //             alertBtn.disabled = false;
    //             alertBtn.classList.remove('opacity-60', 'cursor-default');
    //             if (alertLabel) {
    //                 alertLabel.classList.add('cursor-pointer', 'hover:text-amber-900');
    //                 alertLabel.classList.remove('cursor-default', 'opacity-60');
    //                 alertLabel.style.pointerEvents = '';
    //             }
    //         }

    //         // بعد التراجع، نفحص حالة الصف الحالي
    //         const hasAlerts = rowAlerts > 0;
    //         const hasOpened = rowOpened;

    //         // إذا كان الصف فارغ (0 تنبيهات و 0 فتح) وليس الصف الأول، نحذفه
    //         if (!hasAlerts && !hasOpened && currentRowNum > 1) {
    //             row.remove();

    //             // إعادة ترقيم الصفوف المتبقية
    //             const remainingRows = Array.from(container.children);
    //             remainingRows.forEach((remainingRow, index) => {
    //                 const rowNumberEl = remainingRow.querySelector('.row-number');
    //                 if (rowNumberEl) {
    //                     rowNumberEl.textContent = index + 1;
    //                 }
    //             });

    //             // بعد الحذف، نفحص الصفوف المتبقية من النهاية للبداية
    //             // نحذف أي صف فارغ حتى نصل للصف الأول
    //             setTimeout(() => {
    //                 const allRows = Array.from(container.children);
    //                 for (let i = allRows.length - 1; i >= 1; i--) {
    //                     const checkRow = allRows[i];
    //                     const checkAlertLabel = checkRow.querySelector('.alert-label');
    //                     const checkOpenBtn = checkRow.querySelector('.open-row-btn');

    //                     if (checkAlertLabel && checkOpenBtn) {
    //                         const checkAlerts = parseInt(checkAlertLabel.textContent || '0') || 0;
    //                         const checkIsOpened = checkOpenBtn.textContent.trim() === 'تم الفتح';

    //                         if (checkAlerts === 0 && !checkIsOpened) {
    //                             checkRow.remove();
    //                         } else {
    //                             break; // توقف عند أول صف غير فارغ
    //                         }
    //                     }
    //                 }

    //                 // إعادة ترقيم الصفوف بعد الحذف
    //                 const finalRows = Array.from(container.children);
    //                 finalRows.forEach((finalRow, index) => {
    //                     const rowNumberEl = finalRow.querySelector('.row-number');
    //                     if (rowNumberEl) {
    //                         rowNumberEl.textContent = index + 1;
    //                     }
    //                 });
    //             }, 10);
    //         }
    //         // إذا وصل للصف الأول وكان هناك تنبيه أو فتح، نولد سطر جديد
    //         else if (currentRowNum === 1 && (hasAlerts || hasOpened)) {
    //             const remainingRows = Array.from(container.children);
    //             if (remainingRows.length === 1) {
    //                 createAlertRow(2, 0, false);
    //             }
    //         }

    //         if (typeof window.updateTotalScoreDisplay === 'function') {
    //             window.updateTotalScoreDisplay();
    //         }
    //     });

    //     container.appendChild(row);
    //     container.scrollTop = container.scrollHeight;
    // }
    
    // دالة مساعدة لمزامنة الصفوف مع الحقول المخفية (تمنع التكرار والأخطاء)
// 1. دالة المزامنة المحدثة (التي تحسب الصافي فقط)
// 1. دالة المزامنة (تأكد أنها تحسب الصافي: الصف المفتوح يُحسب كـ "فتح" فقط ويُصفر تنبيهاته)
function syncAlertSystemToInputs() {
    const container = document.getElementById('alert-open-rows-container');
    const form = document.getElementById('current-answer-form');
    if (!container || !form) return;

    const alertInput = form.querySelector('[name="alert_new_position"]');
    const fat7Input = form.querySelector('[name="fat7_points"]');

    let netAlerts = 0; 
    let totalFat7 = 0;

    const allRows = container.querySelectorAll('.flex.items-center[data-row-index]');
    allRows.forEach(row => {
        const label = row.querySelector('.alert-label');
        const openBtn = row.querySelector('.open-row-btn');
        
        const rowAlertsCount = parseInt(label.textContent.trim()) || 0;
        const isOpened = openBtn && (openBtn.disabled === true || openBtn.textContent.trim() === 'تم الفتح');

        if (isOpened) {
            totalFat7 += 1;
            // لا نجمع التنبيهات هنا لأنها تحولت لفتح
        } else {
            netAlerts += rowAlertsCount;
        }
    });

    alertInput.value = netAlerts;
    fat7Input.value = totalFat7;

    if (typeof window.updateTotalScoreDisplay === 'function') {
        window.updateTotalScoreDisplay();
    }
    // لا نحفظ تلقائياً أثناء الاستعادة
    if (typeof window.saveCurrentAnswer === 'function' && !isRestoring) {
        window.saveCurrentAnswer();
    }
}

// 2. دالة إنشاء الصفوف (معدلة لإظهار الصف التالي فور الضغط على تنبيه)
function createAlertRow(rowIndex, initialAlerts = 0, initialOpened = false) {
    const container = document.getElementById('alert-open-rows-container');
    if (!container) return;

    const maxAlerts = parseInt(container.dataset.maxAlerts || '2');
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 px-3 py-2 border-b border-gray-100 last:border-0 fade-in';
    row.setAttribute('data-row-index', rowIndex);

    const openBtnClass = initialOpened ? 'bg-red-50 text-red-400 border-red-100' : 'bg-orange-50 text-orange-600 border-orange-200';

    row.innerHTML = `
        <div class="w-6 text-center text-xs text-slate-500 row-number">${rowIndex}</div>
        <button type="button" class="alert-row-btn bg-amber-50 border border-amber-100 rounded-lg py-2 flex-1 flex items-center justify-center gap-3 ${initialOpened ? 'opacity-60' : ''}" ${initialOpened ? 'disabled' : ''}>
            <span class="text-amber-800 font-bold">تنبيه</span>
            <span class="text-amber-900 font-bold alert-label">${initialAlerts}</span>
        </button>
        <button type="button" class="open-row-btn py-1.5 px-2 rounded-lg font-bold text-xs border ${openBtnClass}" ${initialOpened ? 'disabled' : ''} style="flex: 0.7;">
            ${initialOpened ? 'تم الفتح' : 'فتح'}
        </button>
        <button type="button" class="undo-row-btn w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200">
            <i class="fas fa-undo text-[10px]"></i>
        </button>
    `;

    const alertBtn = row.querySelector('.alert-row-btn');
    const alertLabel = row.querySelector('.alert-label');
    const openBtn = row.querySelector('.open-row-btn');
    const undoBtn = row.querySelector('.undo-row-btn');

    // حدث النقر على التنبيه
    alertBtn.addEventListener('click', () => {
        let current = parseInt(alertLabel.textContent);
        if (current < maxAlerts) {
            alertLabel.textContent = current + 1;

            // --- الإضافة الجديدة: إظهار الصف التالي فوراً ---
            const totalRows = container.querySelectorAll('.flex.items-center[data-row-index]').length;
            const currentRowIdx = parseInt(row.getAttribute('data-row-index'));
            
            // إذا كان هذا هو الصف الأخير، أنشئ صفاً جديداً تحتة
            if (currentRowIdx === totalRows) {
                createAlertRow(totalRows + 1);
            }
            // ----------------------------------------------

            if (parseInt(alertLabel.textContent) >= maxAlerts) {
                openBtn.click(); // سيتحول لفتح تلقائياً إذا وصل للحد
            }
        }
        syncAlertSystemToInputs();
    });

    // حدث الفتح
    openBtn.addEventListener('click', () => {
        openBtn.textContent = 'تم الفتح';
        openBtn.disabled = true;
        openBtn.className = 'open-row-btn py-1.5 px-2 rounded-lg font-bold text-xs border bg-red-50 text-red-400 border-red-100';
        alertBtn.disabled = true;
        alertBtn.classList.add('opacity-60');
        
        // التحقق من إنشاء صف جديد عند الفتح (في حال لم يتم إنشاؤه عند التنبيه)
        const totalRows = container.querySelectorAll('.flex.items-center[data-row-index]').length;
        if (parseInt(row.getAttribute('data-row-index')) === totalRows) {
            createAlertRow(totalRows + 1);
        }
        
        syncAlertSystemToInputs();
    });

    // حدث التراجع
    undoBtn.addEventListener('click', () => {
        if (openBtn.disabled) {
            openBtn.disabled = false;
            openBtn.textContent = 'فتح';
            openBtn.className = 'open-row-btn py-1.5 px-2 rounded-lg font-bold text-xs border bg-orange-50 text-orange-600 border-orange-200';
            alertBtn.disabled = false;
            alertBtn.classList.remove('opacity-60');
        } else if (parseInt(alertLabel.textContent) > 0) {
            alertLabel.textContent = parseInt(alertLabel.textContent) - 1;
        }

        // حذف الصف إذا أصبح فارغاً تماماً وليس الصف الأول
        if (parseInt(alertLabel.textContent) === 0 && !openBtn.disabled && parseInt(row.getAttribute('data-row-index')) > 1) {
            row.remove();
            
            // إعادة ترقيم الصفوف المتبقية لضمان الترتيب
            container.querySelectorAll('.flex.items-center[data-row-index]').forEach((r, i) => {
                r.setAttribute('data-row-index', i + 1);
                r.querySelector('.row-number').textContent = i + 1;
            });
        }
        syncAlertSystemToInputs();
    });

    container.appendChild(row);
    // تمرير تلقائي للأسفل لرؤية الصف الجديد
    container.scrollTop = container.scrollHeight;
}

// 3. دالة تحديث المجموع (أصبحت بسيطة لأن المدخلات أصبحت صافية)
function updateTotalScoreDisplay() {
    const finalScoreElement = document.getElementById('final-score-display');
    const deductionInfoElement = document.getElementById('deduction-info');
    if (!finalScoreElement) return;

    // Ensure SCORE_CONFIG is available, provide fallbacks
    const config = window.SCORE_CONFIG || {};
    const initialTotalScore = config.totalScore || {{ $total_score }};
    const alertSamePenalty = config.alertSamePenalty || config.alertNewPenalty || 0;
    const alertNewPenalty = config.alertNewPenalty || config.alertSamePenalty || 0;
    const fat7Penalty = config.fat7Penalty || 0;

    let totalDeductions = 0;
    const currentIdx = window.currentIndex;

    if (Array.isArray(window.allAnswers)) {
        window.allAnswers.forEach((ans, index) => {
            let alertsSame = 0;
            let alertsNew = 0;
            let fat7 = 0;

            if (index === currentIdx) {
                const form = document.getElementById('current-answer-form');
                if (form) {
                    alertsSame = parseInt(form.querySelector('[name="alert_same_position"]')?.value || 0);
                    alertsNew = parseInt(form.querySelector('[name="alert_new_position"]')?.value || 0);
                    fat7 = parseInt(form.querySelector('[name="fat7_points"]')?.value || 0);
                }
            } else if (ans) {
                alertsSame = parseInt(ans.alert_same_position || 0);
                alertsNew = parseInt(ans.alert_new_position || 0);
                fat7 = parseInt(ans.fat7_points || 0);
            }

            totalDeductions += (alertsSame * alertSamePenalty) + (alertsNew * alertNewPenalty) + (fat7 * fat7Penalty);
        });
    }

    const finalScore = Math.max(0, initialTotalScore - totalDeductions);
    finalScoreElement.textContent = finalScore.toFixed(1);
    if (deductionInfoElement) deductionInfoElement.textContent = totalDeductions > 0 ? `(-${totalDeductions.toFixed(1)})` : '';
}

    // Initialize everything when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeQuestionScores();
        initializeNoteInput();

        // Function to attempt loading notes
        const attemptLoadNotes = (attemptName = 'unknown') => {
            console.log(`[Notes Load Attempt] 🎯 ${attemptName} - Checking loadSavedNotes function:`, {
                exists: typeof loadSavedNotes === 'function',
                timestamp: new Date().toISOString()
            });

            if (typeof loadSavedNotes === 'function') {
                console.log(`[Notes Load Attempt] ✅ Calling loadSavedNotes from ${attemptName}...`);
                loadSavedNotes(0);
            } else {
                console.warn(`[Notes Load Attempt] ⚠️ loadSavedNotes function not found in ${attemptName}`);
            }
        };

        console.log('[Notes] 📋 Setting up multiple load attempts...');

        // Load saved notes multiple times to ensure it works
        // Immediate attempt (after DOM is ready)
        setTimeout(() => attemptLoadNotes('Immediate (100ms)'), 100);

        // After Select2 initialization
        setTimeout(() => attemptLoadNotes('After Select2 Init (400ms)'), 400);

        // After footer script loads allAnswers
        setTimeout(() => attemptLoadNotes('After allAnswers (800ms)'), 800);

        // Final attempts
        setTimeout(() => attemptLoadNotes('Final Attempt 1 (1500ms)'), 1500);
        setTimeout(() => attemptLoadNotes('Final Attempt 2 (2500ms)'), 2500);

        // Also listen for when allAnswers becomes available
        console.log('[Notes] 🔄 Starting interval to check for allAnswers availability...');
        const checkAndLoadNotes = setInterval(() => {
            const hasAllAnswers = typeof window.allAnswers !== 'undefined';
            const hasLoadFunction = typeof loadSavedNotes === 'function';

            console.log('[Notes] 🔍 Interval check:', {
                hasAllAnswers: hasAllAnswers,
                hasLoadFunction: hasLoadFunction,
                allAnswersLength: hasAllAnswers && Array.isArray(window.allAnswers) ? window.allAnswers.length : 0
            });

            if (hasAllAnswers && hasLoadFunction) {
                console.log('[Notes] ✅ allAnswers is now available! Loading notes...');
                attemptLoadNotes('Interval Check');
                clearInterval(checkAndLoadNotes);
                console.log('[Notes] 🛑 Interval cleared');
            }
        }, 100);

        // Clear interval after 5 seconds
        setTimeout(() => {
            clearInterval(checkAndLoadNotes);
            console.log('[Notes] ⏰ Interval cleared after 5 seconds');
        }, 5000);

        // Also try loading when window is fully loaded
        window.addEventListener('load', () => {
            console.log('[Notes] 📄 Window fully loaded event fired');
            setTimeout(() => {
                if (typeof loadSavedNotes === 'function') {
                    console.log('[Notes] 🎯 Window Load - Loading saved notes after full page load');
                    loadSavedNotes(0);
                } else {
                    console.warn('[Notes] ⚠️ Window Load - loadSavedNotes function not found');
                }
            }, 500);
        });

        updateScoreDisplay(0);
        updateTotalScoreDisplay();

        // تهيئة حالة بوكس التنبيهات للسؤال الأول
        restoreQuestionState(0);

        // تحميل إعدادات طلب التخفيف
        if (typeof loadReliefSettings === 'function') {
            loadReliefSettings();
        }

        // Add listeners to penalty inputs to update total score
        document.querySelectorAll('[name="alert_same_position"], [name="alert_new_position"], [name="fat7_points"]').forEach(input => {
            input.addEventListener('input', updateTotalScoreDisplay);
        });

        // Make functions and variables globally accessible
        window.questionScores = questionScores;
        window.questionModified = questionModified;
        window.globalTajweedRemaining = globalTajweedRemaining;
        window.globalPerformanceRemaining = globalPerformanceRemaining;
        window.perQuestionTajweedDeduction = perQuestionTajweedDeduction;
        window.perQuestionPerformanceDeduction = perQuestionPerformanceDeduction;
        window.updateScoreDisplay = updateScoreDisplay;
        window.changeQuestionScore = changeQuestionScore;
        window.updateTotalScoreDisplay = updateTotalScoreDisplay;
        window.saveCurrentQuestionState = saveCurrentQuestionState;
        window.restoreQuestionState = restoreQuestionState;
        window.createAlertRow = createAlertRow;
        window.updateSelectedNotesDisplay = updateSelectedNotesDisplay;
        window.loadSavedNotes = loadSavedNotes;
        
        // Wait for questionsAlertHistory and backend deductions to be ready (built in footer.blade.php)
        const waitForAlertHistory = (retryCount = 0) => {
            const maxRetries = 15;
            const hasAlertHistory = window.questionsAlertHistory && Object.keys(window.questionsAlertHistory).length > 0;
            const hasBackendDeductions = window.backendPerQuestionTajweedDeductions && window.backendPerQuestionPerformanceDeductions;
            const hasAllAnswers = window.allAnswers && window.allAnswers.length > 0;
            
            if (hasAlertHistory && (hasBackendDeductions || hasAllAnswers)) {
                console.log('[Init] ✅ All data ready, initializing...', {
                    hasAlertHistory,
                    hasBackendDeductions,
                    hasAllAnswers
                });
                initializeQuestionScores(); // استعادة درجات التجويد/الأداء من قاعدة البيانات
                restoreQuestionState(0);    // استعادة صفوف التنبيهات والفتح للسؤال الأول
                updateTotalScoreDisplay();  // تحديث إجمالي الدرجة في الهيدر
                
                // إذا كان هناك نظام ملاحظات تأكد من تحميله أيضاً
                if (typeof loadSavedNotes === 'function') {
                    loadSavedNotes(0);
                }
            } else if (retryCount < maxRetries) {
                console.log(`[Init] ⏳ Waiting for data (attempt ${retryCount + 1}/${maxRetries})...`, {
                    hasAlertHistory,
                    hasBackendDeductions,
                    hasAllAnswers
                });
                setTimeout(() => waitForAlertHistory(retryCount + 1), 150);
            } else {
                console.warn('[Init] ⚠️ Data not ready after retries, initializing anyway...', {
                    hasAlertHistory,
                    hasBackendDeductions,
                    hasAllAnswers
                });
                initializeQuestionScores();
                restoreQuestionState(0);
                updateTotalScoreDisplay();
                if (typeof loadSavedNotes === 'function') {
                    loadSavedNotes(0);
                }
            }
        };
        
        setTimeout(() => waitForAlertHistory(), 300); 
    });

    // Toggle Control Panel Function
    function toggleControlPanel() {
        const panel = document.getElementById('control-panel');
        const toggleBtnDesktop = document.getElementById('toggle-control-panel');
        const toggleBtnMobile = document.getElementById('toggle-control-panel-mobile');
        const backdrop = document.getElementById('panel-backdrop');

        if (!panel) return;

        const isMobile = window.innerWidth < 1024;

        if (isMobile) {
            // Mobile behavior: overlay
            const isVisible = panel.classList.contains('mobile-visible');

            if (isVisible) {
                // Hide panel
                panel.classList.remove('mobile-visible');
                if (backdrop) backdrop.classList.remove('visible');
                if (toggleBtnMobile) toggleBtnMobile.classList.remove('active');
                // Enable body scroll
                document.body.style.overflow = '';
            } else {
                // Show panel
                panel.classList.add('mobile-visible');
                if (backdrop) backdrop.classList.add('visible');
                if (toggleBtnMobile) toggleBtnMobile.classList.add('active');
                // Disable body scroll
                document.body.style.overflow = 'hidden';
            }
        } else {
            // Desktop behavior: sidebar collapse
            const isHidden = panel.classList.contains('hidden-panel');

            if (isHidden) {
                // Show panel
                panel.classList.remove('hidden-panel');
                if (toggleBtnDesktop) toggleBtnDesktop.classList.remove('active');
            } else {
                // Hide panel
                panel.classList.add('hidden-panel');
                if (toggleBtnDesktop) toggleBtnDesktop.classList.add('active');
            }
        }
    }

    // Close panel when resizing from mobile to desktop
    window.addEventListener('resize', function() {
        const panel = document.getElementById('control-panel');
        const backdrop = document.getElementById('panel-backdrop');
        const isMobile = window.innerWidth < 1024;

        if (!isMobile && panel) {
            // Remove mobile classes when switching to desktop
            panel.classList.remove('mobile-visible');
            if (backdrop) backdrop.classList.remove('visible');
            document.body.style.overflow = '';
        }
    });

    // Toggle Questions List Function
    function toggleQuestionsList() {
        const list = document.getElementById('questions-list-container');
        const icon = document.getElementById('questions-list-icon');

        if (!list || !icon) return;

        const isHidden = list.style.maxHeight === '0px' || list.style.display === 'none';

        if (isHidden) {
            // Show list
            list.style.maxHeight = '';
            list.style.opacity = '1';
            list.style.display = 'flex';
            icon.style.transform = 'rotate(0deg)';
        } else {
            // Hide list
            list.style.maxHeight = '0px';
            list.style.opacity = '0';
            list.style.display = 'none';
            icon.style.transform = 'rotate(-90deg)';
        }
    }

    // --- Relief Notification Helpers ---
    function sendReliefNotifications(requestId, grade) {
        console.log("Relief notification triggered for request:", requestId);
        // Future Socket.io integration
    }
    function sendReliefNotifications(requestId, grade) {
        console.log("Relief notification triggered for request:", requestId);
        // يمكنك ربطها بـ Socket.io هنا مستقبلاً
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        // 1. تحميل الإعدادات وبناء السيلكتور
        loadReliefSettings();
        
        // 2. تحميل الطلبات المعلقة وتحديثها دورياً
        loadPendingReliefRequests();
        setInterval(loadPendingReliefRequests, 10000);

        // 3. مستمع حدث النقر الموحد (استخدام Delegation لمنع التكرار)
        document.body.addEventListener('click', async function (e) {
            const reliefBtn = e.target.closest('#request-relief-btn');
            if (!reliefBtn || reliefBtn.disabled) return; // منع النقر المزدوج إذا كان الزر معطلاً
            
            e.preventDefault();

            const actualSelect = document.getElementById('relief-grade-actual-select');
            let selectedGrade = actualSelect ? actualSelect.value : document.getElementById('relief-grade').value;
            const reason = document.getElementById('relief-reason').value.trim();
            const committeeId = '{{ $judging_committee_id ?? "" }}' || new URLSearchParams(window.location.search).get('committee_id');

            if (!selectedGrade || selectedGrade === "0" || selectedGrade === "") {
                showCustomNotification('تنبيه', 'يرجى اختيار درجة التخفيف أولاً', 'warning');
                return;
            }

            // تعطيل الزر فوراً لمنع الإرسال المتكرر
            reliefBtn.disabled = true;
            const originalBtnHtml = reliefBtn.innerHTML;
            reliefBtn.innerHTML = '<i class="fas fa-spinner fa-spin ms-1"></i> جاري الإرسال...';

            try {
                const response = await fetch('/api/relief-requests/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        participant_id: '{{ $participant_id }}',
                        competition_version_branch_id: '{{ $competition_version_branch_id }}',
                        judging_committee_id: committeeId,
                        grade: selectedGrade, 
                        reason: reason || 'طلب تخفيف من محكم القرآن', // سبب موحد
                        participant_name: '{{ $participant_name }}',
                        field_type: 'quran'
                    })
                });

                const result = await response.json();
                if (result.success) {
                    showCustomNotification('تم بنجاح', 'تم إرسال الطلب بنجاح');
                    reliefBtn.innerHTML = '<i class="fas fa-check ms-1"></i> تم الإرسال مسبقاً';
                    reliefBtn.className = 'bg-yellow-500 text-white font-bold py-2 px-6 rounded-lg text-base cursor-not-allowed w-full';
                    
                    // تحديث القائمة فوراً
                    loadPendingReliefRequests();
                    
                    setTimeout(() => { 
                        if(typeof toggleReliefBox === 'function') toggleReliefBox(); 
                    }, 1500);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showCustomNotification('خطأ', error.message, 'error');
                reliefBtn.disabled = false;
                reliefBtn.innerHTML = originalBtnHtml;
            }
        });
    });

    // دالة تحميل الإعدادات (تأكد من وجود نسخة واحدة منها فقط)
    async function loadReliefSettings() {
        try {
            const branchId = '{{ $competition_version_branch_id }}';
            const settingId = '{{ $judging_form_setting_id }}';
            const response = await fetch(`/api/competition-branch/${branchId}/relief-settings?field=quran&judging_form_setting_id=${settingId}`);
            const data = await response.json();
            
            if (data.success && data.relief_grade) {
                const maxGrade = parseInt(data.relief_grade);
                const displayBox = document.getElementById('relief-grade-display');
                if (!displayBox) return;

                const select = document.createElement('select');
                select.id = 'relief-grade-actual-select';
                select.className = 'w-full rounded-lg text-sm px-3 py-1 bg-white border border-slate-200 text-emerald-700 font-bold focus:ring-2 focus:ring-emerald-500';

                for (let v = 10; v <= maxGrade; v += 10) {
                    const opt = document.createElement('option');
                    opt.value = v;
                    opt.textContent = v;
                    if (v === maxGrade) opt.selected = true;
                    select.appendChild(opt);
                }
                displayBox.innerHTML = '';
                displayBox.appendChild(select);
                document.getElementById('relief-grade').value = maxGrade;
            }
        } catch (e) { console.error('Relief settings error:', e); }
    }

    // 1. دالة جلب وعرض الطلبات بتنسيق "التفسير"
    // 1. عرض الطلبات بنفس استايل التفسير بالضبط
async function loadPendingReliefRequests() {
    try {
        const branchId = '{{ $competition_version_branch_id }}';
        const currentUserId = '{{ auth()->id() }}';
        const response = await fetch(`/api/relief-requests/pending?competition_version_branch_id=${branchId}&t=${Date.now()}`);
        
        if (response.ok) {
            const data = await response.json();
            const list = Array.isArray(data) ? data : (data.requests || []);
            
            const container = document.getElementById('relief-requests-list');
            const section = document.getElementById('pending-relief-requests');
            if (!container || !section) return;

            if (list.length === 0) {
                section.classList.add('hidden');
                return;
            }

            section.classList.remove('hidden');
            container.innerHTML = '';

            list.forEach(request => {
                const isOwnRequest = (String(request.user_id) === String(currentUserId));
                const bgColor = isOwnRequest ? 'bg-blue-50 border-blue-200' : 'bg-orange-50 border-orange-200';
                const iconColor = isOwnRequest ? 'text-blue-600' : 'text-orange-600';
                const badgeColor = isOwnRequest ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800';

                const requestDiv = document.createElement('div');
                requestDiv.className = `${bgColor} border rounded-lg p-3 mb-2 shadow-sm`;
                
                requestDiv.innerHTML = `
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <i class="fas ${isOwnRequest ? 'fa-user-check' : 'fa-user'} ${iconColor} ms-2"></i>
                                <span class="font-medium text-gray-900 dark:text-white">${request.judge_name}</span>
                                ${isOwnRequest ? '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium me-2">طلبك</span>' : ''}
                                <span class="text-sm text-gray-500 dark:text-gray-400 me-2">يطلب تخفيف</span>
                                <span class="${badgeColor} px-2 py-1 rounded text-xs font-medium me-2">${request.grade}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 me-2">لـ</span>
                                <span class="font-medium text-gray-900 dark:text-white">${request.participant_name}</span>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300 mb-3 break-words">
                                ${request.reason || 'طلب تخفيف من المحكم أثناء تقييم القرآن'}
                            </div>
                            <div class="flex items-center justify-center gap-3">
                                ${!isOwnRequest ? `
                                    <button type="button" onclick="approveReliefRequest('${request.id}')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-all">
                                        <i class="fas fa-check ms-1"></i> موافقة
                                    </button>
                                    <button type="button" onclick="denyReliefRequest('${request.id}')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-all">
                                        <i class="fas fa-times ms-1"></i> رفض
                                    </button>
                                ` : `
                                    <span class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium flex items-center"><i class="fas fa-info-circle ms-1"></i> طلبك</span>
                                `}
                                <button type="button" onclick="viewReliefRequestDetails('${request.id}')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-all">
                                    <i class="fas fa-eye ms-1"></i> تفاصيل
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.appendChild(requestDiv);
            });
        }
    } catch (e) { console.error('Load requests error:', e); }
}

// 2. الموافقة الفورية (بدون رسالة تأكيد)
async function approveReliefRequest(requestId) {
    try {
        const res = await fetch('/api/relief-requests/approve', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ request_id: requestId })
        });
        const data = await res.json();
        if(data.success) {
            showCustomNotification('تمت الموافقة', 'تمت الموافقة وتطبيق التخفيف فوراً');
            loadPendingReliefRequests(); // تحديث القائمة
        }
    } catch (e) { console.error(e); }
}

// 3. الرفض الفوري (بدون رسالة تأكيد أو سبب)
async function denyReliefRequest(requestId) {
    try {
        const res = await fetch('/api/relief-requests/deny', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ request_id: requestId, rejection_reason: 'تم الرفض من لجنة التحكيم' })
        });
        const data = await res.json();
        if(data.success) {
            showCustomNotification('تم الرفض', 'تم رفض الطلب بنجاح');
            loadPendingReliefRequests(); // تحديث القائمة
        }
    } catch (e) { console.error(e); }
}

// 4. التفاصيل بنفس استايل التفسير
async function viewReliefRequestDetails(requestId) {
    const content = document.getElementById('relief-details-content');
    content.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-blue-600"></i><p class="mt-2">جاري التحميل...</p></div>';
    document.getElementById('relief-details-modal').classList.remove('hidden');
    
    try {
        const res = await fetch(`/api/relief-requests/details/${requestId}`);
        const data = await res.json();
        if(data.success) {
            const req = data.request;
            content.innerHTML = `
                <div class="space-y-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-3 flex items-center"><i class="fas fa-user ms-2"></i> معلومات الطلب</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div><span class="font-medium">المحكم:</span> ${req.judge_name}</div>
                            <div><span class="font-medium">المتسابق:</span> ${req.participant_name}</div>
                            <div><span class="font-medium">الدرجة:</span> <span class="bg-orange-100 text-orange-800 px-2 py-0.5 rounded">${req.grade}</span></div>
                            <div><span class="font-medium">التوقيت:</span> ${new Date(req.created_at).toLocaleString('ar-SA')}</div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="font-semibold mb-2 flex items-center"><i class="fas fa-comment-alt ms-2"></i> السبب المذكور:</h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300">${req.reason || 'لا يوجد سبب محدد'}</p>
                    </div>
                </div>
            `;
        }
    } catch (e) { content.innerHTML = '<div class="text-red-500 p-4">خطأ في تحميل البيانات</div>'; }
}

function hideReliefDetailsModal() {
    document.getElementById('relief-details-modal').classList.add('hidden');
}
</script>