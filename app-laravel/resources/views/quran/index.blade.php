@include('mosabka::judgings.quran.header')

<style>

    /* تخصيص شريط التمرير داخل بوكس القرآن */
.quran-scroll-area::-webkit-scrollbar {
    width: 8px;
    background-color: transparent;
}

.quran-scroll-area::-webkit-scrollbar-track {
    background-color: #f1f5f9;
    border-radius: 4px;
    margin: 4px 0;
}

.quran-scroll-area::-webkit-scrollbar-thumb {
    background-color: #cbd5e1;
    border-radius: 4px;
    border: 2px solid #f1f5f9;
}

.quran-scroll-area::-webkit-scrollbar-thumb:hover {
    background-color: #94a3b8;
}

/* تخصيص شريط التمرير داخل بوكس الأسئلة */
#questions-list-container {
    scrollbar-width: thin; /* For Firefox */
    scrollbar-color: #94a3b8 #f1f5f9; /* thumb and track color for Firefox */
}

#questions-list-container::-webkit-scrollbar {
    width: 8px;
}

#questions-list-container::-webkit-scrollbar-track {
    background-color: #f1f5f9;
    border-radius: 4px;
}

#questions-list-container::-webkit-scrollbar-thumb {
    background-color: #94a3b8;
    border-radius: 4px;
    transition: background-color 0.2s ease;
}

#questions-list-container::-webkit-scrollbar-thumb:hover {
    background-color: #64748b;
}

/* تكبير حجم الخط والعناصر في بوكس الأسئلة */
.question-item {
    padding: 14px 12px !important;
    min-height: 70px;
}

.question-item h4 {
    font-size: 15px !important;
    font-weight: 700 !important;
    margin-bottom: 6px !important;
}

.question-item p {
    font-size: 13px !important;
    line-height: 1.6 !important;
}

.question-item .flex-shrink-0 {
    width: 28px !important;
    height: 28px !important;
    font-size: 13px !important;
}

/* Scrollbar styling for large screens */
@media (min-width: 1024px) {
    body {
        overflow-y: auto; /* Enable vertical scrolling */
        min-height: 100vh;
    }

    #main-container {
        min-height: calc(100vh - 80px);
        overflow: visible;
    }

    #questions-wrapper {
        min-height: 100%;
        overflow: visible;
    }

    #current-question-display {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .unified-content {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
}
    /* Desktop: hide panel behavior */
    @media (min-width: 1024px) {
        .hidden-panel {
            width: 0 !important;
            min-width: 0 !important;
            margin: 0 !important;
            opacity: 0;
            overflow: hidden;
            pointer-events: none;
        }

        #control-panel {
            transition: width 0.3s ease, opacity 0.3s ease, margin 0.3s ease;
        }

        #questions-wrapper {
            transition: max-width 0.3s ease;
        }
    }

    /* Mobile: overlay behavior */
    @media (max-width: 1023px) {
        /* إخفاء الـ control panel افتراضياً */
        #control-panel {
            position: fixed !important;
            top: 64px !important; /* بعد الـ header */
            right: -100% !important; /* مخفي خارج الشاشة */
            width: 85% !important;
            max-width: 380px !important;
            height: calc(100vh - 64px) !important;
            z-index: 100 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            background: white !important;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.2) !important;
            transition: right 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            padding: 16px !important;
        }

        #control-panel.mobile-visible {
            right: 0 !important;
        }

        /* Backdrop overlay */
        #panel-backdrop {
            position: fixed;
            top: 64px;
            left: 0;
            width: 100%;
            height: calc(100vh - 64px);
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
            backdrop-filter: blur(2px);
        }

        #panel-backdrop.visible {
            opacity: 1;
            pointer-events: auto;
        }

        /* Hamburger button for mobile */
        #toggle-control-panel-mobile {
            animation: fadeInScale 0.3s ease-out;
        }

        #toggle-control-panel-mobile.active svg {
            transform: scaleX(-1);
        }

        #questions-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            padding-top: 0 !important;
        }

        /* جعل المحتوى يأخذ كامل العرض */
        #main-container {
            flex-direction: column !important;
        }
    }

    /* Animation for button appearance */
    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.8);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Navigation buttons responsive */
    @media (max-width: 640px) {
        .prev-btn,
        .next-btn {
            min-width: 100px;
            font-size: 0.875rem;
        }

        #footer-text {
            text-align: center;
        }
    }

    @media (max-width: 380px) {
        .prev-btn span,
        .next-btn span {
            font-size: 0.8125rem;
        }
    }

    /* Score controls responsive */
    @media (max-width: 640px) {
        #global-tajweed,
        #global-performance {
            font-size: 0.75rem !important;
            padding: 0 2px;
        }

        /* تأكيد أن النص لا يخرج عن الحدود */
        #global-tajweed,
        #global-performance {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: clip;
        }
    }

    @media (max-width: 380px) {
        #global-tajweed,
        #global-performance {
            font-size: 0.6875rem !important;
        }
    }

    #toggle-control-panel,
    #toggle-control-panel-mobile {
        transition: all 0.3s ease;
    }

    #toggle-control-panel.active svg,
    #toggle-control-panel-mobile.active svg {
        transform: scaleX(-1);
    }

    #toggle-control-panel:hover {
        transform: translateX(-2px);
    }

    #toggle-control-panel-mobile:hover {
        background-color: #f8fafc !important;
    }

    /* تنسيقات قائمة الملاحظات (Select2) لتتوافق مع Tailwind */
    .select2-container {
        width: 100% !important;
        direction: rtl;
    }

    .select2-container .select2-selection--single {
        height: auto !important;
        min-height: 48px !important;
        background-color: #fff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.75rem !important;
        display: flex !important;
        align-items: center !important;
        transition: all 0.3s ease;
        padding: 12px 60px 12px 16px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #334155 !important;
        font-size: 0.875rem !important;
        padding: 0 !important;
        line-height: 1.5 !important;
        text-align: right !important;
        width: 100% !important;
    }

    /* السهم في اليسار */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100% !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: auto !important;
        width: 30px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        display: none !important;
    }

    /* إضافة سهم مخصص */
    .select2-container--default .select2-selection--single .select2-selection__arrow::after {
        content: '' !important;
        display: block !important;
        width: 0 !important;
        height: 0 !important;
        border-left: 5px solid transparent !important;
        border-right: 5px solid transparent !important;
        border-top: 6px solid #64748b !important;
        margin: 0 auto !important;
    }

    /* تدوير السهم عند الفتح */
    .select2-container--open .select2-selection--single .select2-selection__arrow::after {
        transform: rotate(180deg) !important;
        transition: transform 0.2s ease !important;
    }

    /* علامة X في اليسار */
    .select2-container--default .select2-selection--single .select2-selection__clear {
        position: absolute !important;
        left: 30px !important;
        right: auto !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        font-size: 18px !important;
        color: #64748b !important;
        cursor: pointer !important;
        padding: 0 8px !important;
        z-index: 1 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        color: #334155 !important;
    }

    /* حالة التركيز (Focus) */
    .select2-container--open .select2-selection--single {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
    }

    /* تنسيقات الملاحظات المتعددة */
    /* حاوية القائمة بالكامل */
    .selected-notes-container {
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        margin-top: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    /* رأس القائمة */
    .selected-notes-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px dashed #cbd5e1; /* خط متقطع خفيف */
    }

    .selected-notes-header h4 {
        font-weight: 700;
        color: #334155;
        font-size: 0.9rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .selected-notes-header .notes-count {
        background-color: #f1f5f9;
        color: #475569;
        min-width: 24px;
        height: 24px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
        border: 1px solid #e2e8f0;
    }

    /* ============================================
       تنسيق Select2 الحديث - Chips Style مع Auto Height
       ============================================ */

    /* 1. تنسيق الحاوية الرئيسية لتتمدد تلقائياً */
    .select2-container--default .select2-selection--multiple {
        background-color: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.75rem !important;
        min-height: 48px !important; /* أقل ارتفاع */
        height: auto !important; /* السماح بالتمدد */
        padding: 4px 4px 4px 8px !important; /* مسافات داخلية مريحة */
        display: flex !important;
        flex-wrap: wrap !important; /* يسمح بنزول العناصر لسطر جديد */
        align-items: center !important; /* توسيط العناصر عمودياً */
        transition: all 0.2s ease;
    }

    /* 2. تنسيق الكبسولة (الملاحظة) نفسها */
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: linear-gradient(145deg, #eff6ff, #dbeafe) !important;
        border: 1px solid #bfdbfe !important;
        border-radius: 9999px !important; /* جعلها دائرية بالكامل Pill shape */
        color: #1e40af !important;

        /* ضبط الأبعاد والمسافات */
        margin: 3px !important; /* مسافة خارجية بسيطة */
        padding: 4px 12px 4px 32px !important; /* مساحة داخلية (اليسار كبير عشان زر الحذف) */

        font-size: 0.85rem !important;
        font-weight: 600 !important;
        position: relative !important; /* عشان نثبت زر الحذف */
        display: inline-flex !important;
        align-items: center !important;
        height: 32px !important; /* ارتفاع ثابت للكبسولة */
        box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
        float: none !important; /* إلغاء الطفو القديم */
    }

    /* 3. زر الحذف (x) داخل الكبسولة */
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        position: absolute !important;
        left: 5px !important; /* تثبيت في أقصى اليسار */
        top: 50% !important;
        transform: translateY(-50%) !important;

        background-color: #fff !important;
        color: #ef4444 !important;
        border: none !important;
        border-radius: 50% !important;
        width: 20px !important;
        height: 20px !important;

        display: flex !important;
        align-items: center !important;
        justify-content: center !important;

        font-size: 14px !important;
        font-weight: bold !important;
        cursor: pointer !important;
        transition: all 0.2s !important;
        margin-right: 0 !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background-color: #fee2e2 !important;
        color: #b91c1c !important;
    }

    /* 4. ضبط حقل الكتابة (Search Input) ليكون بجانب الكبسولات */
    .select2-container--default .select2-search--inline .select2-search__field {
        margin: 4px !important;
        height: 32px !important;
        line-height: 32px !important;
        font-family: inherit !important;
        font-size: 0.9rem !important;
        min-width: 100px !important; /* مساحة للكتابة */
        border: none !important;
        outline: none !important;
        background: transparent !important;
        color: #1e293b !important;
    }

    .select2-container--default .select2-search--inline .select2-search__field::placeholder {
        color: #94a3b8 !important;
    }

    /* 5. إزالة أي هوامش غريبة من الـ Rendered List الخاصة بـ Select2 */
    .select2-container .select2-selection--multiple .select2-selection__rendered {
        display: contents !important; /* تجاهل عنصر القائمة والتعامل مع الأطفال مباشرة */
        padding: 0 !important;
        margin: 0 !important;
    }

    /* تأثير التركيز على البوكس */
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }

    /* تحسين مظهر placeholder */
    .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
        color: #94a3b8 !important;
        font-size: 0.9rem !important;
        white-space: nowrap !important;
        display: inline-block !important;
        width: 100% !important;
        text-align: center !important;
    }

    /* توسيط placeholder في حاوية Select2 عندما يكون فارغاً */
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
    }

    /* عندما يكون هناك placeholder فقط (لا توجد اختيارات) */
    .select2-container--default .select2-selection--multiple .select2-selection__rendered:not(:has(.select2-selection__choice)) {
        justify-content: center !important;
    }

    /* عندما يكون هناك placeholder فقط */
    .select2-container--default .select2-selection--multiple .select2-selection__rendered:has(.select2-selection__placeholder:only-child) {
        justify-content: center !important;
    }

    /* 1. حاوية القائمة المنسدلة */
    .select2-dropdown {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important; /* حواف دائرية كبيرة */
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.01) !important;
        overflow: hidden !important;
        padding-top: 5px !important;
        padding-bottom: 5px !important;
        z-index: 99999 !important;
    }

    /* 2. حقل البحث داخل القائمة */
    .select2-search--dropdown {
        padding: 8px 10px !important;
    }

    .select2-search__field {
        border-radius: 8px !important;
        border: 1px solid #cbd5e1 !important;
        padding: 8px 12px !important;
        background-color: #f8fafc !important;
        font-size: 0.9rem !important;
    }

    .select2-search__field:focus {
        border-color: #3b82f6 !important;
        outline: none !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1) !important;
    }

    /* 3. حاوية النتائج */
    .select2-results__options {
        padding: 4px !important;
        max-height: 250px !important;
    }

    /* 4. عنصر الخيار الواحد (Option Item) */
    .select2-results__option {
        padding: 10px 14px !important;
        font-size: 0.9rem !important;
        color: #475569 !important;
        border-radius: 8px !important; /* حواف دائرية لكل خيار */
        margin-bottom: 4px !important; /* مسافة بين الخيارات */
        transition: all 0.2s ease !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important; /* لضمان المحاذاة في RTL */
    }

    /* حالة الهوفر (Highlight) - تغيير الخلفية الرمادية القبيحة */
    .select2-results__option--highlighted[aria-selected] {
        background-color: #eff6ff !important; /* أزرق فاتح جداً */
        color: #1d4ed8 !important; /* أزرق غامق */
        font-weight: 600 !important;
        transform: translateX(-2px); /* حركة بسيطة */
    }

    /* الخيار المحدد حالياً - السماح بإعادة الاختيار */
    .select2-results__option[aria-selected="true"] {
        background-color: #e0e7ff !important; /* لون بنفسجي فاتح للملاحظة المختارة */
        color: #3730a3 !important; /* لون بنفسجي غامق */
        cursor: pointer !important; /* السماح بالنقر */
        opacity: 1 !important; /* عدم جعلها باهتة */
        font-weight: 500 !important;
        position: relative !important;
    }

    /* إضافة علامة ✓ للملاحظة المختارة */
    .select2-results__option[aria-selected="true"]::after {
        content: '✓' !important;
        position: absolute !important;
        left: 14px !important;
        color: #6366f1 !important;
        font-weight: bold !important;
        font-size: 1rem !important;
    }

    /* عند hover على الملاحظة المختارة */
    .select2-results__option[aria-selected="true"]:hover {
        background-color: #ddd6fe !important;
        color: #4c1d95 !important;
    }

    /* إخفاء السهم الأساسي للـ select عند عدم استخدام Select2 */
    #unified-note-select {
        cursor: pointer;
    }

    /* تنسيقات الملاحظات المتعددة */
    #selected-notes-display {
        max-height: 320px;
        overflow-y: auto;
        padding: 4px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    #selected-notes-display::-webkit-scrollbar {
        width: 6px;
    }

    #selected-notes-display::-webkit-scrollbar-track {
        background-color: #f1f5f9;
        border-radius: 3px;
    }

    #selected-notes-display::-webkit-scrollbar-thumb {
        background-color: #94a3b8;
        border-radius: 3px;
    }

    #selected-notes-display::-webkit-scrollbar-thumb:hover {
        background-color: #64748b;
    }

    /* كارت الملاحظة الواحد */
    .note-item-card {
        background: #f8fafc; /* خلفية رمادية فاتحة جداً */
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
        position: relative;
        /* إزالة الخط الجانبي السميك وجعله أنيقاً */
        border-right: 3px solid #3b82f6;
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
</style>

<main class="container mx-auto px-4 py-4 mt-16 mb-4 flex flex-col lg:flex-row gap-2" id="main-container">

    <!-- Left Side: Evaluation Panel -->
    <aside id="control-panel" class="w-full lg:w-80 xl:w-[22rem] sticky top-20 h-fit order-1 lg:order-1 transition-all duration-300 flex-shrink-0">
        <div class="flex flex-col gap-3">

        <!-- Warnings (if any) -->
        @if(isset($warnings) && count($warnings) > 0)
            <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-xl shadow-sm">
                <div class="flex items-start gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
                    <div>
                        <h3 class="text-xs font-bold text-yellow-800 mb-1">تنبيهات</h3>
                        <div class="text-xs text-yellow-700">
                            @foreach($warnings as $warning)
                                <p class="mb-1">{{ $warning }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
    @endif

        <!-- Panel Header with Score -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-1.5">
                    <div class="bg-blue-600 text-white p-1 rounded-lg">
                        <i class="fas fa-edit text-sm"></i>
                    </div>
                    <h3 class="font-bold text-lg text-slate-800">تقييم</h3>
                </div>
                <!-- المجموع النهائي -->
                <div class="px-3 py-1 rounded-full border transition-all duration-300" id="final-score-container" style="background: #eff6ff; border-color: #bfdbfe;">
                    <span class="text-xs font-semibold" style="color: #3b82f6;">المجموع</span>
                    <span class="text-lg font-bold font-mono ml-1 transition-all duration-300" style="color: #1e40af;" id="final-score-display">{{ $total_score }}</span>
                    <span class="text-xs transition-all duration-300" style="color: #60a5fa;" id="deduction-info"></span>
                </div>
            </div>
            </div>

            <!-- Form Content -->
        <form class="answer-form flex flex-col gap-3" id="current-answer-form"
                      data-question-id="{{ isset($questions[0]) ? $questions[0]['question']->id : '' }}"
                      data-participant-id="{{ $participant_id }}">

            <!-- نظام التنبيهات -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-3">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                        نظام التنبيهات
                        <i class="fas fa-bell text-amber-500 text-xs"></i>
                    </h3>
                    <p class="text-[10px] text-slate-400">مسموح {{ $alert_before_fat7 }} قبل الفتح</p>
                            </div>

                <!-- الحقول المخفية -->
                            <input type="hidden" name="alert_same_position" value="0">
                <input type="hidden" name="alert_new_position" value="0">
                <input type="hidden" name="fat7_points" value="0">

                <!-- حاوية الصفوف -->
                <div class="bg-slate-50 rounded-lg border border-slate-200 max-h-32 overflow-y-auto p-1.5"
                                 id="alert-open-rows-container"
                                 data-max-alerts="{{ $alert_before_fat7 }}"
                                 data-alert-penalty="{{ $alert_new_position_penalty }}"
                                 data-fat7-penalty="{{ $fat7_penalty }}">
                    <!-- يتم توليد الصفوف ديناميكياً -->
                    </div>
            </div>
                </form>

                    <!-- Controls Section -->
        <div class="grid grid-cols-2 gap-2 sm:gap-3 mt-6">
            <!-- درجة التجويد -->
            <div class="bg-white p-2 sm:p-3 rounded-xl shadow-sm border border-slate-200 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-1 h-full bg-blue-600 opacity-80 rounded-l"></div>
                <h3 class="text-slate-600 font-bold mb-1.5 sm:mb-2 text-center text-xs sm:text-sm">درجة التجويد</h3>
                <div class="flex items-center justify-between gap-1 sm:gap-1.5">
                    <button type="button"
                            class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors active:scale-95"
                            onclick="changeQuestionScore('global-tajweed', 1, {{ $tajweed_score }})">
                        <i class="fas fa-plus text-[10px] sm:text-xs"></i>
                    </button>
                    <div class="flex-1 bg-slate-50 border border-slate-200 rounded-lg py-1 sm:py-1.5 flex items-center justify-center font-mono font-bold text-xs sm:text-base text-slate-800 min-w-0">
                    <input type="text" id="global-tajweed" value="{{ $tajweed_score }}/{{ $tajweed_score }}"
                               class="bg-transparent text-center w-full outline-none text-xs sm:text-base" readonly>
                    </div>
                    <button type="button"
                            class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition-colors active:scale-95"
                            onclick="changeQuestionScore('global-tajweed', -1, {{ $tajweed_score }})">
                        <i class="fas fa-minus text-[10px] sm:text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- درجة الأداء -->
            <div class="bg-white p-2 sm:p-3 rounded-xl shadow-sm border border-slate-200 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-1 h-full bg-indigo-600 opacity-80 rounded-l"></div>
                <h3 class="text-slate-600 font-bold mb-1.5 sm:mb-2 text-center text-xs sm:text-sm">درجة الأداء</h3>
                <div class="flex items-center justify-between gap-1 sm:gap-1.5">
                    <button type="button"
                            class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors active:scale-95"
                            onclick="changeQuestionScore('global-performance', 1, {{ $performance_score }})">
                        <i class="fas fa-plus text-[10px] sm:text-xs"></i>
                    </button>
                    <div class="flex-1 bg-slate-50 border border-slate-200 rounded-lg py-1 sm:py-1.5 flex items-center justify-center font-mono font-bold text-xs sm:text-base text-slate-800 min-w-0">
                    <input type="text" id="global-performance" value="{{ $performance_score }}/{{ $performance_score }}"
                               class="bg-transparent text-center w-full outline-none text-xs sm:text-base" readonly>
                    </div>
                    <button type="button"
                            class="w-7 h-7 sm:w-8 sm:h-8 flex items-center justify-center rounded-lg bg-slate-800 hover:bg-slate-700 text-white transition-colors active:scale-95"
                            onclick="changeQuestionScore('global-performance', -1, {{ $performance_score }})">
                        <i class="fas fa-minus text-[10px] sm:text-xs"></i>
                    </button>
                </div>
                            </div>
                    </div>

            <!-- الملاحظات -->
            <div class="note-combo">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    <i class="fas fa-sticky-note ml-1"></i>
                    اختر أو اكتب ملاحظة
                </label>
                                <select id="unified-note-select" multiple
                        class="w-full bg-white border border-slate-300 text-slate-700 py-3 px-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow cursor-pointer text-sm"
                                        dir="rtl"
                        data-placeholder="اختر أو اكتب ملاحظات...">
                                    @foreach ($notes as $note)
                                        <option value="{{ $note->id }}">{{ $note->note }}</option>
                                    @endforeach
                                </select>
                            <input type="hidden" name="note_ids" id="note-ids">
                            <input type="hidden" name="note_texts" id="note-texts">
                    </div>

        <!-- زر طلب التخفيف -->
        <button type="button"
                class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition-all flex items-center justify-center gap-2 active:scale-[0.98] text-sm"
                onclick="toggleReliefBox()">
            <i class="fas fa-hand-paper transform -scale-x-100 text-sm"></i>
            <span>طلب تخفيف</span>
        </button>
        
        <!-- صندوق التخفيف (يجب أن تكون المعرفات دقيقة) -->
        <div id="relief-box" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl p-3 transition-all duration-300 transform scale-95 opacity-0 mt-2">
            <h3 class="text-sm font-bold text-emerald-700 mb-2 flex items-center gap-1.5">
                <i class="fas fa-hand-paper text-xs"></i>
                طلب التخفيف
            </h3>
        
            <div class="space-y-2">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">درجة التخفيف:</label>
                    <!-- هنا يتم توليد السيلكتور بواسطة JS -->
                    <div id="relief-grade-display" class="w-full rounded-lg text-sm h-8 bg-white border border-slate-200 flex items-center justify-center font-bold text-emerald-600">
                        <span id="relief-grade-text">جاري التحميل...</span>
                    </div>
                    <input type="hidden" id="relief-grade" name="relief_grade" value="">
                </div>
        
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">السبب (اختياري):</label>
                    <textarea id="relief-reason" class="w-full rounded-lg border border-slate-300 text-sm py-1.5 px-2" rows="2" placeholder="اكتب سبب طلب التخفيف..."></textarea>
                </div>
        
                <div class="flex justify-center pt-1">
                    <button id="request-relief-btn" type="button" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-1.5 px-4 rounded-lg text-sm transition-all">
                        <i class="fas fa-hand-paper ml-1 text-xs"></i>
                        <span id="relief-btn-text">إرسال الطلب</span>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- قائمة الطلبات المعلقة (هذا ما يضمن ظهوره عند الرئيس والباقي) -->
        <div id="pending-relief-requests" class="bg-white rounded-xl shadow-sm border border-orange-200 p-3 hidden mt-2">
            <h3 class="text-sm font-bold text-orange-600 mb-2 flex items-center gap-1.5">
                <i class="fas fa-hand-paper text-xs"></i> طلبات التخفيف المعلقة
            </h3>
            <div id="relief-requests-list" class="space-y-2"></div>
        </div>

            <!-- Questions List -->
        <div>
            <div class="flex items-center gap-1 mb-1.5 px-0.5 cursor-pointer" onclick="toggleQuestionsList()">
                <i class="fas fa-list text-blue-600 text-sm transition-transform duration-300" id="questions-list-icon"></i>
                <h2 class="font-bold text-sm text-slate-800">الأسئلة</h2>
            </div>

            <div id="questions-list-container" class="flex flex-col gap-1.5 max-h-[250px] lg:max-h-[300px] xl:max-h-[350px] overflow-y-auto overflow-x-hidden transition-all duration-300">
                    @php
                        $isEditModeBlade = ($is_edit_mode ?? false);
                        $totalQuestions = count($questions);
                        $shownQuestionsCount = 0;
                    @endphp
                    @foreach ($questions as $index => $q)
                        @php 
                            $isHeadBlade = ($is_head ?? false);
                            $revealedSet = isset($revealedQuestionIds) ? collect($revealedQuestionIds) : collect();
                            $isQuestionRevealed = $isHeadBlade || $revealedSet->contains($q['question']->id);
                            
                            // في وضع التعديل، يجب أن تظهر جميع الأسئلة المحددة ($questions)
                            // في الوضع العادي، تظهر فقط الأسئلة المكشوفة
                            if ($isEditModeBlade) {
                                // في وضع التعديل: تظهر جميع الأسئلة المحددة بغض النظر عن كونها محكمة أم لا
                                $showItem = true; // جميع الأسئلة في $questions يجب أن تظهر
                            } else {
                                // في الوضع العادي: تظهر فقط الأسئلة المكشوفة
                                $showItem = $isHeadBlade || $isQuestionRevealed;
                            }
                            if ($showItem) {
                                $shownQuestionsCount++;
                            }
                        @endphp
                        @if($showItem)
                    <div class="question-item relative overflow-hidden rounded-lg p-2 cursor-pointer transition-all duration-300 border {{ $index == 0 ? 'bg-blue-900 text-white shadow-sm border-blue-900' : 'bg-slate-50 text-slate-600 border-slate-200' }}"
                         data-question-index="{{ $index }}"
                         onclick="switchToQuestion({{ $index }})">
                        <div class="flex items-start gap-2">
                            <!-- Number Badge -->
                            <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center font-bold text-[10px] {{ $index == 0 ? 'bg-white text-blue-900' : 'bg-slate-200 text-slate-500' }}">
                                {{ $index + 1 }}
                            </div>
                            <!-- Content -->
                                <div class="flex-1">
                                <h4 class="font-bold text-xs mb-0 {{ $index == 0 ? 'text-blue-100' : 'text-slate-800' }}">
                                    سورة {{ $q['question']->surah }}
                                </h4>
                                <p class="text-[10px] leading-snug opacity-90 font-light">
                                    {{ \Illuminate\Support\Str::limit($q['question']->question_text, 40) }}
                                </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                    @if($isEditModeBlade)
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
            </div>

            </div>
        </div>
    </aside>

    <!-- Backdrop for mobile -->
    <div id="panel-backdrop" onclick="toggleControlPanel()"></div>

    <!-- Toggle Button between panel and content (Desktop) -->
    <div id="toggle-control-panel-container" class="hidden lg:flex items-start pt-2 sticky top-20">
        <button type="button"
                id="toggle-control-panel"
                class="w-8 h-10 bg-white rounded-lg shadow-sm border border-slate-200 flex items-center justify-center hover:bg-slate-50 hover:shadow transition-all duration-300"
                onclick="toggleControlPanel()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-600 transition-transform duration-300">
                <rect x="3" y="3" width="7" height="18" rx="1"></rect>
                <path d="M14 4l6 8-6 8"></path>
            </svg>
        </button>
    </div>

    <!-- Toggle Button for Mobile (Fixed) -->
    <button type="button"
            id="toggle-control-panel-mobile"
            class="lg:hidden fixed w-12 h-12 bg-white rounded-xl shadow-lg border border-slate-200 flex items-center justify-center hover:bg-slate-50 transition-all duration-300"
            style="top: 80px; left: 16px; z-index: 101;"
            onclick="toggleControlPanel()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-600 transition-transform duration-300">
            <rect x="3" y="3" width="7" height="18" rx="1"></rect>
            <path d="M14 4l6 8-6 8"></path>
        </svg>
    </button>

    <!-- Relief Request Details Modal -->
    <div id="relief-details-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-info-circle text-blue-600 ml-2"></i>
                        تفاصيل طلب التخفيف
                    </h3>
                    <button onclick="hideReliefDetailsModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div id="relief-details-content">
                    <!-- Loading state -->
                    <div id="relief-loading" class="text-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-600 dark:text-gray-400">جاري تحميل التفاصيل...</p>
                    </div>

                    <!-- Content will be populated here -->
                </div>

                <div class="flex justify-end mt-6">
                    <button onclick="hideReliefDetailsModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                        إغلاق
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side: Question Content (takes more space) -->
    <div id="questions-wrapper" class="flex-1 space-y-6 order-2 lg:order-2 transition-all duration-300">

        <!-- Current Question Display -->
        <div id="current-question-display" class="card p-4 fade-in">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                <!-- Question Content (takes full width now) -->
                <div class="text-right lg:col-span-3" id="question-content">
                @if (isset($questions[0]))
                    @php
                        $firstQuestion = $questions[0];
                        $firstId = $firstQuestion['question']->id;
                        $isHeadBlade = ($is_head ?? false) ? true : false;
                        $isEditModeBlade = ($is_edit_mode ?? false);
                        $revealedSet = isset($revealedQuestionIds) ? collect($revealedQuestionIds) : collect();
                        $isFirstRevealed = $isHeadBlade || $revealedSet->contains($firstId);
                        
                        // في وضع التعديل، يجب أن يظهر محتوى السؤال الأول دائماً
                        // في الوضع العادي، يظهر فقط إذا كان مكشوفاً
                        if ($isEditModeBlade) {
                            // في وضع التعديل: يظهر محتوى السؤال الأول دائماً
                            $shouldShowFirstContent = true;
                        } else {
                            // في الوضع العادي: يظهر فقط إذا كان مكشوفاً
                            $shouldShowFirstContent = $isHeadBlade || $isFirstRevealed;
                        }
                    @endphp

                    @if($shouldShowFirstContent)
                        <!-- Unified Content Area -->
                        <div class="unified-content space-y-6">
                            <!-- Question Section -->
                            <div class="question-section bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center">
                                    <i class="fas fa-question-circle text-primary ml-2 text-xl"></i>
                                    <h3 class="text-lg font-bold text-primary">السؤال</h3>
                                    </div>
                                    @if($is_head ?? false)
                                        <div id="reveal-btn-in-question"></div>
                                    @endif
                                </div>
                                <div class="text-gray-800 dark:text-gray-200 leading-relaxed">
                                    {{ $firstQuestion['question']->question_text }}
                                </div>
                                @php
                                    $startAyahNum = $firstQuestion['question']->start_ayah_number ?? '';
                                    $endAyahNum = $firstQuestion['question']->end_ayah_number ?? '';
                                    $endSurah = $firstQuestion['question']->end_surah ?? '';
                                    $surah = $firstQuestion['question']->surah ?? '';
                                @endphp
                                @if($startAyahNum || $endAyahNum)
                                    <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-info-circle ml-1"></i>
                                        @if($endSurah && $endSurah !== $surah)
                                            من سورة {{ $surah }} آية {{ $startAyahNum }} إلى سورة {{ $endSurah }} آية {{ $endAyahNum }}
                                        @else
                                            من سورة {{ $surah }} آية {{ $startAyahNum }} إلى آية {{ $endAyahNum }}
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <!-- Quran Text Section - تصميم المصحف -->
                            <div class="mushaf-container relative h-full flex flex-col">
                                <!-- Scrollable Content -->
                                <div class="flex-1 overflow-y-auto pr-2 max-h-[calc(100vh-300px)]">
                                <!-- زخارف الزوايا -->
                                <div class="mushaf-corner top-right"></div>
                                <div class="mushaf-corner top-left"></div>
                                <div class="mushaf-corner bottom-right"></div>
                                <div class="mushaf-corner bottom-left"></div>

                                <!-- الزخارف الجانبية -->
                                <div class="mushaf-decoration right"></div>
                                <div class="mushaf-decoration left"></div>

                                <!-- عنوان السورة -->
                                <div class="mushaf-header">
                                    <i class="fas fa-book-quran ml-2"></i>
                                    سورة {{ $firstQuestion['question']->surah ?? 'البقرة' }}
                                </div>

                                <!-- البسملة (إذا كانت بداية السورة) -->
                                @if(isset($firstQuestion['ayahs'][0]) && $firstQuestion['ayahs'][0]['numberInSurah'] == 1 && $firstQuestion['question']->surah != 'التوبة')
                                    <div class="bismillah-mushaf">
                                        بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ
                                    </div>
                                @endif

                                <!-- سيتم عرض الوجوه القرآنية عبر الصور الديناميكية -->
                                </div> <!-- End of scrollable content -->
                            </div>
                    @else
                        <div class="unified-content space-y-6">
                            <div class="question-section bg-white dark:bg-gray-800 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6 shadow-sm">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-lock text-yellow-600 ml-2 text-xl"></i>
                                    <h3 class="text-lg font-bold text-yellow-700">بانتظار رئيس اللجنة</h3>
                                </div>
                                <div class="text-gray-700 dark:text-gray-300">سيظهر السؤال ونصه هنا بمجرد أن يقوم رئيس اللجنة بإظهاره.</div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

        <!-- Navigation Buttons -->
        <div class="mt-6 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 py-4 px-4 rounded-lg shadow-sm">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4">
                    <div class="text-xs sm:text-sm text-slate-600 order-2 sm:order-1">
                        <span id="footer-text">السؤال {{ $currentIndex + 1 }} من أصل {{ count($questions) }}</span>
                    </div>
                    <div class="flex gap-2 sm:gap-3 w-full sm:w-auto order-1 sm:order-2">
                        <button type="button"
                                class="bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-2 px-3 sm:py-2.5 sm:px-5 rounded-lg prev-btn transition-all flex items-center justify-center gap-1.5 sm:gap-2 text-sm sm:text-base flex-1 sm:flex-initial">
                            <i class="fas fa-arrow-right text-xs sm:text-sm"></i>
                            <span>السابق</span>
                        </button>
                        <button type="button"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 sm:py-2.5 sm:px-5 rounded-lg next-btn transition-all flex items-center justify-center gap-1.5 sm:gap-2 text-sm sm:text-base flex-1 sm:flex-initial">
                            <span id="next-btn-text">
                                @if ($currentIndex + 1 >= count($questions))
                                    إنهاء وحفظ
                                @else
                                    التالي
                                @endif
                            </span>
                        </button>
                    </div>
                </div>
            </div>
    </div>

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
    <!-- Relief Request Details Modal -->
    <div id="relief-details-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white"><i class="fas fa-info-circle text-blue-600 ml-2"></i> تفاصيل طلب التخفيف</h3>
                    <button onclick="hideReliefDetailsModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times text-xl"></i></button>
                </div>
                <div id="relief-details-content"></div>
                <div class="flex justify-end mt-6">
                    <button onclick="hideReliefDetailsModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
</main>


@include('mosabka::judgings.quran.footer')

<script>
    // Score calculation configuration
    window.SCORE_CONFIG = {
        totalQuestions: {{ $questions_count }},
        totalScore: {{ $total_score }},
        scorePerQuestion: {{ $score_per_question }},
        tajweedTotal: {{ $tajweed_score }},
        tajweedPerQuestion: {{ $tajweed_per_question }},
        tajweedPenalty: {{ $tajweed_penalty ?? 1 }},
        performanceTotal: {{ $performance_score }},
        performancePerQuestion: {{ $performance_per_question }},
        performancePenalty: {{ $performance_penalty ?? 1 }},
        alertSamePenalty: {{ $alert_same_position_penalty ?? $alert_new_position_penalty ?? $alert_penalty ?? 0 }},
        alertNewPenalty: {{ $alert_new_position_penalty ?? $alert_same_position_penalty ?? $alert_penalty ?? 0 }},
        fat7Penalty: {{ $fat7_penalty ?? 0 }}
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

        function updateSelectedNotesDisplay(ids, texts) {
            console.log('[Display] 🎨 updateSelectedNotesDisplay called:', {
                ids: ids,
                texts: texts,
                idsLength: ids ? ids.length : 0,
                textsLength: texts ? texts.length : 0,
                timestamp: new Date().toISOString()
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
                    $select.next('.select2-container').find('.select2-selection__rendered').html('<i class="fas fa-spinner fa-spin ml-1"></i> جاري الحفظ...');
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

</script>

<script>
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
            reliefBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري الإرسال...';

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
                    reliefBtn.innerHTML = '<i class="fas fa-check ml-1"></i> تم الإرسال مسبقاً';
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
                                <i class="fas ${isOwnRequest ? 'fa-user-check' : 'fa-user'} ${iconColor} ml-2"></i>
                                <span class="font-medium text-gray-900 dark:text-white">${request.judge_name}</span>
                                ${isOwnRequest ? '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mr-2">طلبك</span>' : ''}
                                <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">يطلب تخفيف</span>
                                <span class="${badgeColor} px-2 py-1 rounded text-xs font-medium mr-2">${request.grade}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 mr-2">لـ</span>
                                <span class="font-medium text-gray-900 dark:text-white">${request.participant_name}</span>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-300 mb-3 break-words">
                                ${request.reason || 'طلب تخفيف من المحكم أثناء تقييم القرآن'}
                            </div>
                            <div class="flex items-center justify-center gap-3">
                                ${!isOwnRequest ? `
                                    <button type="button" onclick="approveReliefRequest('${request.id}')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-all">
                                        <i class="fas fa-check ml-1"></i> موافقة
                                    </button>
                                    <button type="button" onclick="denyReliefRequest('${request.id}')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-all">
                                        <i class="fas fa-times ml-1"></i> رفض
                                    </button>
                                ` : `
                                    <span class="bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium flex items-center"><i class="fas fa-info-circle ml-1"></i> طلبك</span>
                                `}
                                <button type="button" onclick="viewReliefRequestDetails('${request.id}')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center transition-all">
                                    <i class="fas fa-eye ml-1"></i> تفاصيل
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
                        <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-3 flex items-center"><i class="fas fa-user ml-2"></i> معلومات الطلب</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div><span class="font-medium">المحكم:</span> ${req.judge_name}</div>
                            <div><span class="font-medium">المتسابق:</span> ${req.participant_name}</div>
                            <div><span class="font-medium">الدرجة:</span> <span class="bg-orange-100 text-orange-800 px-2 py-0.5 rounded">${req.grade}</span></div>
                            <div><span class="font-medium">التوقيت:</span> ${new Date(req.created_at).toLocaleString('ar-SA')}</div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h4 class="font-semibold mb-2 flex items-center"><i class="fas fa-comment-alt ml-2"></i> السبب المذكور:</h4>
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