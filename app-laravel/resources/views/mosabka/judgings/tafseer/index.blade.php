<!-- resources/views/modules/mosabka/judgings/tafseer/index.blade.php -->
@include('mosabka::judgings.quran.header')
@php 
    $safeIndex = is_numeric($currentIndex) ? (int)$currentIndex : 0; 
    
    // Performance Notes Content as requested by user
    $demoNotes = [
        ['id' => 101, 'note' => 'قصر المد', 'category_name' => 'مدود'],
        ['id' => 102, 'note' => 'زيادة المد', 'category_name' => 'مدود'],
        ['id' => 103, 'note' => 'عدم التفريق بين المدود', 'category_name' => 'مدود'],
        ['id' => 104, 'note' => 'ترك المد اللازم', 'category_name' => 'مدود'],
        ['id' => 105, 'note' => 'تفاوت المد المتصل', 'category_name' => 'مدود'],
        ['id' => 106, 'note' => 'نقص الغنة', 'category_name' => 'غنن'],
        ['id' => 107, 'note' => 'غنة في غير موضعها', 'category_name' => 'غنن'],
        ['id' => 108, 'note' => 'إطالة الغنة', 'category_name' => 'غنن'],
        ['id' => 109, 'note' => 'عدم إظهار الغنة', 'category_name' => 'غنن'],
        ['id' => 110, 'note' => 'خطأ في المخرج', 'category_name' => 'مخارج'],
        ['id' => 111, 'note' => 'عدم تمييز الحروف المتقاربة', 'category_name' => 'مخارج'],
        ['id' => 112, 'note' => 'ضعف إخراج المستعلية', 'category_name' => 'مخارج'],
        ['id' => 113, 'note' => 'ترك القلقلة', 'category_name' => 'صفات'],
        ['id' => 114, 'note' => 'ضعف الهمس', 'category_name' => 'صفات'],
        ['id' => 115, 'note' => 'عدم تفخيم المفخم', 'category_name' => 'صفات'],
        ['id' => 116, 'note' => 'ترقيق المفخم', 'category_name' => 'صفات'],
        ['id' => 117, 'note' => 'وقف قبيح', 'category_name' => 'وقف'],
        ['id' => 118, 'note' => 'وصل ما يجب قطعه', 'category_name' => 'وقف'],
        ['id' => 119, 'note' => 'ضعف الوقف والابتداء', 'category_name' => 'وقف'],
        ['id' => 120, 'note' => 'تردد', 'category_name' => 'أداء'],
        ['id' => 121, 'note' => 'سرعة في القراءة', 'category_name' => 'أداء'],
        ['id' => 122, 'note' => 'خلط بين الآيات', 'category_name' => 'أداء'],
        ['id' => 123, 'note' => 'عدم الترتيل', 'category_name' => 'أداء'],
        ['id' => 124, 'note' => 'ترتيل ممتاز', 'category_name' => 'أداء'],
        ['id' => 125, 'note' => 'إدغام ناقص', 'category_name' => 'إدغام'],
        ['id' => 126, 'note' => 'إدغام في غير موضعه', 'category_name' => 'إدغام'],
        ['id' => 127, 'note' => 'ترك الإدغام', 'category_name' => 'إدغام'],
    ];
    $notes = collect($demoNotes)->map(fn($n) => (object)$n);
    $categories = $notes->groupBy('category_name');
@endphp


<style>
    /* CSS Variables for New Palette */
    :root {
        --color-navy: var(--color-secondary);
        --color-gold: var(--color-primary);
        --color-gold-dark: var(--color-primary-dark);
        --color-cream: var(--color-bg-main);
        --color-border: #e2e8f0;
        --color-text-navy: var(--color-text-primary);
        --color-text-muted: var(--color-text-secondary);
    }

    /* Layout & Structure */

    .tafseer-scroll-area::-webkit-scrollbar {
        width: 8px;
    }
    .tafseer-scroll-area::-webkit-scrollbar-track {
        background-color: #f1f5f9;
    }
    .tafseer-scroll-area::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 4px;
    }

    /* Column Styles */
    .questions-sidebar {
        width: 320px;
        background: white;
        border-left: 1px solid var(--color-border);
        display: flex;
        flex-direction: column;
    }

    .judging-sidebar {
        width: 380px;
        background: var(--color-navy);
        color: white;
        display: flex;
        flex-direction: column;
    }

    .main-content {
        flex: 1;
        background: var(--color-cream);
        display: flex;
        flex-direction: column;
        border-right: 1px solid var(--color-border);
    }

    /* Question Pill (Sidebar) */
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

    /* Scoring Components */
    .score-input {
        background: transparent;
        border: none;
        color: white;
        font-size: 2.5rem;
        font-weight: 800;
        width: 100px;
        text-align: center;
    }
    .score-input:focus {
        outline: none;
    }

    /* Cards */
    .premium-card {
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        border: 1px solid var(--color-border);
        padding: 2rem;
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out forwards;
    }

    #toggle-control-panel,
    #toggle-control-panel-mobile {
        transition: all 0.3s ease;
    }

    #toggle-control-panel.active svg {
        transform: scaleX(1) !important;
    }

    #toggle-control-panel-mobile.active svg {
        transform: scaleX(-1) !important;
    }

    #toggle-control-panel:hover {
        transform: translateX(2px);
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

    .question-item .shrink-0 {
        width: 28px !important;
        height: 28px !important;
        font-size: 13px !important;
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

    /* Remove default margins and padding */
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow-x: hidden;
    }

    body {
        padding-top: var(--judging-header-offset, 80px) !important;
    }

    /* Make sure main container takes full height */
    #main-container {
        min-height: 100vh;
    }

    /* Score controls responsive */
    @media (max-width: 640px) {
        .score-input {
            font-size: 0.75rem !important;
            padding: 0 2px;
        }
    }

    @media (max-width: 380px) {
        .score-input {
            font-size: 0.6875rem !important;
        }
    }
</style>

<main id="main-layout" class="flex flex-col xl:flex-row flex-1 animate-in min-h-[calc(100vh-var(--judging-header-offset,80px))] xl:h-[calc(100vh-var(--judging-header-offset,80px))] xl:overflow-hidden w-full max-w-[1920px] mx-auto" dir="rtl">
    <!-- RIGHT SIDEBAR: Scoring & Notes -->
    <aside id="control-panel" class="w-full xl:w-[420px] 2xl:w-[480px] bg-white h-auto xl:h-full flex flex-col overflow-visible xl:overflow-hidden border-slate-200 shrink-0">
        <!-- Score Summary box -->
        <div class="p-6 bg-[#1e2540] m-6 mb-2 rounded-2xl text-white shadow-xl relative overflow-hidden group shrink-0">
            <div class="absolute -top-12 -left-12 w-32 h-32 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition-colors"></div>
            
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-slate-400 mb-4 opacity-80 uppercase tracking-widest text-center">ملخص الدرجات</p>
                <div class="flex items-baseline justify-center gap-2 mb-6">
                    <span id="final-score-display" class="text-6xl font-bold tracking-tight text-white">{{ $total_score ?? 100 }}</span>
                    <span class="text-xl text-slate-500">/ 100</span>
                    <span id="deduction-info" class="text-sm text-red-400 font-bold me-2"></span>
                </div>
                
                <!-- Mini boxes (Tafseer) -->
                <div class="grid grid-cols-4 gap-2">
                    <div class="score-mini-card">
                        <p class="score-mini-label text-slate-400">الحفظ</p>
                        <p id="mini-score-hifz" class="score-mini-value">-</p>
                    </div>
                    <div class="score-mini-card">
                        <p class="score-mini-label text-slate-400">التجويد</p>
                         <p id="mini-score-tajweed" class="score-mini-value">66.0</p>
                    </div>
                    <div class="score-mini-card">
                        <p class="score-mini-label text-slate-400">الأداء</p>
                        <p id="mini-score-adaa" class="score-mini-value">18.5</p>
                    </div>
                    <div class="score-mini-card">
                        <p class="score-mini-label text-slate-400">الدراية</p>
                        <p id="mini-score-dirayah" class="score-mini-value font-bold text-yellow-400">{{ $gradeQuestion ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <form class="answer-form flex flex-col flex-1 overflow-hidden" id="current-answer-form"
              data-question-id="{{ isset($InterpretationQuestion[0]) ? $InterpretationQuestion[0]->id : '' }}"
              data-participant-id="{{ $participant_id ?? '' }}">

            <div class="flex-1 overflow-y-auto px-6 py-4 sidebar-scroller flex flex-col gap-6">
                <!-- درجة الدراية (Deduction Buttons) -->
                <div>
                     <div class="flex items-center gap-2 mb-3">
                         <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_6px_rgba(16,185,129,0.4)]"></span>
                         <h4 class="text-sm font-bold text-[#1e2540]">درجة الدراية</h4>
                     </div>
                     <div class="grid grid-cols-5 gap-2 dirayah-scores" id="ui-dirayah-score">
                         <!-- Standard Score Options -->
                         @foreach([2.0, 1.5, 1.0, 0.5, 0.0] as $opt)
                             <button type="button" class="score-opt-btn py-2.5 rounded-xl text-sm font-bold transition-all border border-slate-100 bg-[#fdfdfb] text-slate-500 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200 {{ ($gradeQuestion ?? 0) == $opt ? 'active-score' : '' }}"
                                     data-val="{{ $opt }}"
                                     onclick="setDirayahScore({{ $opt }}, this)">
                                 {{ $opt }}
                             </button>
                         @endforeach
                     </div>
                     <input type="hidden" name="score" id="score-input" value="{{ $gradeQuestion ?? 0 }}">
                </div>

                <!-- Notes Section — Nafes Style -->
                <div>
                    <!-- Nafes Notes Header -->
                    <div class="nafes-notes-header">
                        <div class="nafes-notes-title cursor-pointer" onclick="showAllNotesModal()">
                            <i class="far fa-file-alt text-yellow-500"></i>
                            ملاحظات تجويدية وأدائية
                        </div>
                        <button type="button" onclick="showAllNotesModal()" class="nafes-btn-all group/notes">
                             الكل <i class="fas fa-bars text-[9px]"></i>
                        </button>
                    </div>
                    
                    <div id="notes-container" class="min-h-[60px] flex flex-wrap gap-2 py-4 content-start">
                        <!-- Current selected notes will appear here -->
                        <div class="notes-empty-state w-full text-center py-4 text-slate-300">
                            <i class="fas fa-info-circle text-xs mb-1"></i>
                            <p class="text-[10px] font-bold">لا توجد ملاحظات مختارة</p>
                        </div>
                    </div>
                    <input type="hidden" name="note_ids" id="note-ids">
                    <input type="hidden" name="note_texts" id="note-texts">
                    
                    <!-- Notes Search/Select (Nafes UI) -->
                    <div class="mt-2 text-start">
                         <div class="nafes-search-input-container">
                             <input type="text" 
                                    id="notes-quick-search" 
                                    class="nafes-search-input" 
                                    placeholder="ابحث أو أضف ملاحظة..."
                                    onclick="showAllNotesModal()">
                         </div>
                    </div>
                </div>

                <!-- Nafes Recommendations Card -->
                <div class="p-4 bg-[#fdfdfb] rounded-xl shadow-[0_2px_8px_rgba(0,0,0,0.02)] border border-slate-100 mt-auto cursor-pointer flex items-center gap-2 hover:border-yellow-400 transition-all hover:bg-white group/reco text-[#1e2540]">
                    <div class="w-8 h-8 rounded-lg bg-yellow-500/10 flex items-center justify-center text-yellow-600 group-hover/reco:scale-110 transition-transform">
                        <i class="fas fa-pen-fancy text-xs"></i>
                    </div>
                    <span class="text-sm font-bold">ملاحظات وتوصيات</span>
                </div>
            </div>

            <!-- Nafes Bottom Action Area: Relief Request -->
            <div class="p-4 bg-white border-t border-slate-100 shrink-0">
                <button class="nafes-relief-btn" onclick="toggleReliefBox()">
                    طلب التخفيف
                    <i class="fas fa-asterisk relief-icon"></i>
                </button>
            </div>

            <!-- Bottom Action Block (السؤال التالي) -->
            <div class="p-6 pt-4 border-t border-slate-100 bg-white shadow-[0_-4px_10px_rgba(0,0,0,0.02)] relative z-10 shrink-0 mt-auto">
                <button type="button" id="next-tafseer-btn" class="w-full flex justify-between items-center px-6 py-4 rounded-xl bg-[#1e2540] text-white font-bold hover:bg-[#2d375e] transition-all shadow hover:shadow-lg active:scale-95 next-btn">
                    <span class="text-sm tracking-wider" id="next-btn-text">{{ isset($InterpretationQuestion) && count($InterpretationQuestion) === 1 ? 'إنهاء وحفظ' : 'السؤال التالي' }}</span>
                    <i class="fas fa-arrow-left text-xs opacity-70"></i>
                </button>
            </div>
        </form>
    </aside>

    <!-- Relief Modal (Nafes Design) -->
    <div id="relief-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-100 hidden items-center justify-center p-4 transition-all duration-300">
        <div id="relief-modal-content" class="bg-white rounded-[32px] shadow-2xl w-full max-w-[400px] overflow-hidden opacity-0 translate-y-4 scale-95 transition-all duration-300">
             <!-- Modal Header -->
             <div class="p-8 pb-4 text-center">
                 <div class="flex justify-center items-center gap-2 mb-2">
                     <h3 class="text-xl font-extrabold text-[#111827] flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-500 shadow-[0_0_8px_rgba(234,179,8,0.5)]"></span>
                        طلب التخفيف
                     </h3>
                 </div>
                 <p class="text-[13px] leading-relaxed text-slate-400 font-bold px-4">
                    ستطلب موافقة المحكمين الآخرين على التخفيف عن هذا الطالب.
                 </p>
             </div>
             
             <!-- Modal Body -->
             <div class="px-8 py-6 space-y-6">
                 <!-- Custom Grade Select -->
                 <div class="relative" id="relief-grade-select-container">
                    <button type="button" onclick="toggleReliefGradeOptions()" class="w-full bg-[#f8f7f2] border-0 rounded-2xl py-4 px-6 flex items-center justify-between text-slate-600 font-bold transition-all hover:bg-[#f2f1eb]" id="relief-grade-trigger">
                        <span id="selected-relief-grade-text">اختر الدرجة...</span>
                        <i class="fas fa-chevron-down text-xs text-slate-400 transition-transform duration-300" id="relief-grade-chevron"></i>
                    </button>
                    
                    <!-- Dropdown Options -->
                    <div id="relief-grade-options" class="absolute left-0 right-0 bottom-full mb-2 bg-[#f8f7f2] border border-slate-200 rounded-2xl shadow-xl overflow-hidden hidden z-10 transition-all">
                        <div class="bg-slate-500 text-white py-3 px-6 text-sm font-bold text-center">
                            اختر الدرجة...
                        </div>
                        <div class="max-h-[240px] overflow-y-auto sidebar-scroller">
                            @foreach(['%60', '%55', '%50', '%45', '%40', '%35', '%30'] as $grade)
                                <button type="button" onclick="selectReliefGrade('{{ $grade }}')" class="w-full py-3.5 px-6 text-center text-slate-600 font-extrabold hover:bg-slate-100 transition-colors border-b border-slate-100 last:border-0">
                                    {{ $grade }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <input type="hidden" id="relief-grade" name="relief_grade" value="">
                 </div>

                 <!-- Action Buttons -->
                 <div class="flex items-center gap-3">
                    <button type="button" id="request-relief-submit-btn" class="flex-1 py-4 bg-[#f8f7f2] hover:bg-[#f2f1eb] text-slate-700 rounded-2xl font-extrabold text-[15px] transition-all active:scale-[0.98] border border-slate-200 shadow-sm">
                        إرسال الطلب
                    </button>
                    <button type="button" onclick="toggleReliefBox()" class="w-[80px] py-4 bg-white hover:bg-slate-50 text-slate-400 rounded-2xl font-bold text-sm transition-all border border-slate-100 active:scale-95">
                        إلغاء
                    </button>
                 </div>
             </div>
        </div>
    </div>

    <!-- MIDDLE COLUMN: Question Content -->
    <div class="main-content w-full xl:flex-1 bg-[#f8f7f2] flex flex-col h-auto xl:h-full lg:order-2 order-2 min-w-0 overflow-visible xl:overflow-hidden border-b xl:border-b-0 xl:border-l border-slate-200">
        <div class="flex-1 overflow-y-auto py-8 px-4 md:px-6 tafseer-scroll-area min-h-[50vh] xl:min-h-0" id="questions-wrapper">
             @if(isset($InterpretationQuestion) && count($InterpretationQuestion) > 0)
                @foreach ($InterpretationQuestion as $index => $question)
                    @php
                        $isHeadBlade = ($is_head ?? false);
                        $isEditModeBlade = ($is_edit_mode ?? false);
                        $revealedSet = isset($revealedQuestionIds) ? collect($revealedQuestionIds) : collect();
                        $isQuestionRevealed = $isHeadBlade || $revealedSet->contains($question->id);
                        $shouldShowInitially = $index === 0;
                        $shouldShowRevealedContent = $isHeadBlade || ($isQuestionRevealed && ($isEditModeBlade || $index === 0));
                    @endphp
                    <div id="question-{{ $index }}"
                        class="question-step animate-fade-in {{ $shouldShowInitially ? '' : 'hidden' }}"
                        data-step="{{ $index }}"
                        data-question-id="{{ $question->id }}">

                        @if($shouldShowRevealedContent)
                            <div>
                                <!-- Nafes Styled Question Card -->
                                <div class="p-6 bg-white rounded-2xl shadow-sm border border-slate-100 relative mb-6">
                                    <span class="absolute top-6 right-6 w-2.5 h-2.5 rounded-full bg-yellow-500 shadow-[0_0_8px_rgba(234,179,8,0.4)]"></span>
                                    <div class="pe-6">
                                        <span class="text-[10px] font-bold text-slate-400 mb-1 flex uppercase tracking-wider items-center gap-2">
                                            السؤال {{ $index + 1 }}
                                        </span>
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-bold text-[#1e2540] leading-relaxed">{{ $question->question_text }}</h3>
                                            @if($is_head ?? false)
                                                <div id="reveal-btn-in-question" class="shrink-0 ml-4"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Nafes Styled Answer Card -->
                                <div class="p-6 bg-white rounded-2xl shadow-sm border border-slate-100 relative mb-6">
                                    <span class="absolute top-6 right-6 w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]"></span>
                                    <div class="pe-6">
                                        <span class="text-[10px] font-bold text-slate-400 mb-2 block uppercase tracking-wider">الإجابة المتوقعة</span>
                                        <div class="text-[15px] font-medium text-slate-600 leading-relaxed">{{ $question->answer_text }}</div>
                                        @if ($question->book_name)
                                            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center gap-2 text-xs font-bold text-slate-400">
                                                <i class="fas fa-book"></i>
                                                <span>المصدر: {{ $question->book_name }}</span>
                                                <span class="mx-2 text-slate-200">|</span>
                                                <span>الصفحة: {{ $question->page_number }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="unified-content text-center py-16 bg-white rounded-2xl shadow-sm border border-slate-100 mb-6">
                                <div class="w-20 h-20 mx-auto bg-[#f8f7f2] rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-lock text-3xl text-slate-300"></i>
                                </div>
                                <h3 class="text-xl font-bold text-[#1e2540] mb-2">بانتظار رئيس اللجنة</h3>
                                <p class="text-sm text-slate-400 font-medium max-w-sm mx-auto">سيظهر السؤال والجواب هنا بمجرد أن يقوم رئيس اللجنة بإظهاره.</p>
                            </div>
                        @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-16 bg-white rounded-2xl border border-slate-100 shadow-sm m-6">
                        <div class="w-20 h-20 mx-auto bg-[#f8f7f2] rounded-full flex items-center justify-center mb-6">
                            <i class="fas fa-ghost text-3xl text-slate-300"></i>
                        </div>
                        <h3 class="text-xl font-bold text-[#1e2540] mb-2">لا توجد أسئلة متاحة</h3>
                        <p class="text-sm text-slate-400 font-medium max-w-sm mx-auto">لم يتم العثور على أسئلة تخص هذا المتسابق في هذا الفرع.</p>
                    </div>
                @endif
        </div>
    </div>

    <!-- LEFT SIDEBAR: Questions -->
    <aside id="questions-sidebar" class="w-full xl:w-[300px] 2xl:w-[320px] border-b xl:border-b-0 xl:border-r border-slate-200 bg-white flex flex-col h-auto xl:h-full sidebar-scroller shrink-0">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-[#fdfdfb]">
            <h2 class="font-bold text-[#1e2540]">الأسئلة</h2>
            <span class="text-xs px-2 py-1 bg-slate-100 text-slate-500 rounded-full font-bold">
                {{ count($InterpretationQuestion ?? []) }}/<span id="questions-current-count">1</span>
            </span>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-3 max-h-[40vh] xl:max-h-none sidebar-scroller" id="questions-list-container">
            @if(isset($InterpretationQuestion) && count($InterpretationQuestion) > 0)
                @foreach ($InterpretationQuestion as $index => $q)
                    @php
                        $showInList = ($is_head ?? false) || (isset($revealedQuestionIds) && collect($revealedQuestionIds)->contains($q->id));
                    @endphp
                    @if($showInList)
                        <div class="question-pill relative rounded-2xl border border-slate-100 p-4 transition-all cursor-pointer bg-slate-50 hover:bg-white hover:shadow-md hover:border-slate-200 group {{ $index == 0 ? 'active' : '' }}"
                             data-question-index="{{ $index }}"
                             data-question-id="{{ $q->id }}"
                             onclick="if(window.switchToQuestion) window.switchToQuestion({{ $index }})">
                            <span class="status-indicator"></span>
                            <div class="flex items-start gap-4">
                                <span class="number-badge w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-xs font-bold text-slate-400 group-hover:text-[#1e2540] transition-colors border border-slate-100 shrink-0">
                                    {{ $index + 1 }}
                                </span>
                                <div>
                                    <h4 class="font-bold text-sm text-[#1e2540] mb-0.5">السؤال {{ $index + 1 }}</h4>
                                    <p class="text-[10px] text-slate-500 leading-tight">
                                        {{ \Illuminate\Support\Str::limit($q->question_text, 40) }}
                                    </p>
                                    @if ($q->book_name)
                                        <p class="question-meta"><i class="fas fa-book text-[8px]"></i> {{ $q->book_name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </aside>

    <!-- All Notes Modal (Nafes Design) -->
    @php $categories = isset($notes) ? $notes->groupBy('category_name') : collect(); @endphp
    <div id="all-notes-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-100 hidden flex-col items-center justify-center p-6 transition-opacity" dir="rtl">
        <div id="all-notes-modal-content" class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl h-[85vh] flex flex-col overflow-hidden relative opacity-0 translate-y-4 scale-95 transition-all duration-300">
             <!-- Modal Header -->
             <div class="p-5 border-b border-slate-100 flex justify-center items-center relative">
                 <button type="button" onclick="closeAllNotesModal()" class="absolute right-6 w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-lg"></i>
                 </button>
                 <h3 class="text-lg font-bold text-[#1e2540] flex items-center justify-center gap-2">
                     <i class="far fa-file-alt text-yellow-500"></i> جميع الملاحظات
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
                             <h4 class="text-[11px] font-bold text-slate-400 mb-3 flex items-center gap-1.5 justify-start">
                                 <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 block shadow-[0_0_6px_rgba(234,179,8,0.5)]"></span> {{ $catName ?: 'ملاحظات عامة' }}
                             </h4>
                             <div class="flex flex-wrap gap-2 justify-start">
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
                     <button type="button" onclick="if(document.getElementById('modal-new-note-input').value.trim() !== '') { /* Logic to add new note could go here */ document.getElementById('modal-new-note-input').value=''; }" class="px-6 py-3 bg-[#f8f7f2] hover:bg-[#e4e2de] text-slate-600 rounded-xl font-bold text-sm transition-colors border border-slate-200 shadow-sm flex items-center gap-2">
                         <i class="fas fa-plus text-xs"></i> إضافة
                     </button>
                     <input type="text" id="modal-new-note-input" class="flex-1 bg-[#f8f7f2] border-0 rounded-xl py-3 px-4 text-sm focus:ring-1 focus:ring-yellow-500 text-slate-600 font-bold placeholder-slate-400 transition-shadow" placeholder="أضف ملاحظة جديدة...">
                 </div>
                 <div class="mt-4 text-xs font-bold text-slate-500 text-center">
                     الملاحظات المحددة: <span id="modal-selected-count" class="text-slate-700">0</span>
                 </div>
             </div>
        </div>
    </div>

    <script>
        window.showAllNotesModal = function() {
            const modal = document.getElementById('all-notes-modal');
            const content = document.getElementById('all-notes-modal-content');
            if (modal && content) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
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
                content.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
                content.classList.add('opacity-0', 'translate-y-4', 'scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.style.overflow = '';
                }, 300);
            }
        };

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

        // Modal logic
        function filterModalNotes(searchVal) {
            const query = searchVal.trim().toLowerCase();
            const categories = document.querySelectorAll('.modal-category-block');
            
            categories.forEach(block => {
                let hasVisibleNotes = false;
                const chips = block.querySelectorAll('.nafes-modal-chip');
                
                chips.forEach(chip => {
                    const text = chip.getAttribute('data-note-text');
                    if(text && text.includes(query)) {
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

        // Toggle note select mock if not globally defined
        if(typeof window.toggleNoteSelect === 'undefined') {
            window.toggleNoteSelect = function(id) {
                const btn = document.getElementById('modal-note-card-' + id);
                if(btn) btn.classList.toggle('active');
            }
        }
    </script>

<!-- Hidden question data for JavaScript -->
<div id="questions-data" style="display: none;">
    @if($type === 'interpretation' && isset($InterpretationQuestion) && count($InterpretationQuestion) > 0)
        @foreach ($InterpretationQuestion as $index => $q)
            <div data-question-index="{{ $index }}" data-question-id="{{ $q->id }}" data-question-text="{{ $q->question_text }}"
                data-answer-text="{{ $q->answer_text }}" data-book-name="{{ $q->book_name ?? '' }}"
                data-page-number="{{ $q->page_number ?? '' }}" data-max-score="{{ $gradeQuestion ?? '0' }}">
            </div>
        @endforeach
    @elseif($type === 'hadith' && isset($InterpretationQuestion) && count($InterpretationQuestion) > 0)
        @foreach ($InterpretationQuestion as $index => $q)
            <div data-question-index="{{ $index }}" data-question-id="{{ $q->id }}" data-question-text="{{ $q->question_text }}"
                data-answer-text="{{ $q->answer_text }}" data-book-name="{{ $q->book_name ?? '' }}"
                data-page-number="{{ $q->page_number ?? '' }}" data-max-score="{{ $gradeQuestion ?? '0' }}">
            </div>
        @endforeach
    @elseif($type === 'dirayah' && isset($InterpretationQuestion) && count($InterpretationQuestion) > 0)
        @foreach ($InterpretationQuestion as $index => $q)
            <div data-question-index="{{ $index }}" data-question-id="{{ $q->id }}" data-question-text="{{ $q->question_text }}"
                data-answer-text="{{ $q->answer_text }}" data-book-name="{{ $q->book_name ?? '' }}"
                data-page-number="{{ $q->page_number ?? '' }}" data-max-score="{{ $gradeQuestion ?? '0' }}">
            </div>
        @endforeach
    @else
        <div data-question-index="0" data-question-id="0" data-question-text="No questions found"
            data-answer-text="No questions found" data-book-name="" data-page-number="" data-max-score="0"></div>
    @endif
</div>
</main>

<!-- Save Confirmation Modal -->
<div id="save-confirmation-modal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div
                    class="shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="me-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">تأكيد الحفظ النهائي</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">هل أنت متأكد من حفظ التقييم وإنهاء الجلسة؟</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" id="cancel-save-btn"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-times ms-2"></i> إلغاء
                </button>
                <button type="button" id="confirm-save-btn"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-check ms-2"></i> نعم، احفظ التقييم
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    // Define IS_HEAD globally so it can be accessed by all functions
    const IS_HEAD = {{ ($is_head ?? false) ? 'true' : 'false' }};
    // Set IS_HEAD on window immediately for use in footer.blade.php
    window.IS_HEAD = IS_HEAD;
    console.log('[Tafseer Init] IS_HEAD set on window:', window.IS_HEAD);

    // Log jQuery availability for debugging
    console.log('[Tafseer Init] 🔍 Checking jQuery availability:', {
        window_jQuery: typeof window.jQuery !== 'undefined',
        window_dollar: typeof window.$ !== 'undefined',
        jQuery_version: typeof window.jQuery !== 'undefined' ? window.jQuery.fn.jquery : 'N/A',
        hasSelect2: typeof window.jQuery !== 'undefined' && typeof window.jQuery.fn.select2 === 'function'
    });

    // Ensure jQuery is available globally
    if (typeof window.jQuery !== 'undefined' && typeof window.$ === 'undefined') {
        window.$ = window.jQuery;
        console.log('[Tafseer Init] ✅ Set window.$ = window.jQuery');
    }

    document.addEventListener('DOMContentLoaded', () => {
        console.log('[Tafseer Init] 🚀 DOMContentLoaded fired for Tafseer page');
        console.log('[Tafseer Init] 📋 Type from server:', '{{ $type }}');

        // Double-check jQuery after DOM is loaded
        if (typeof window.jQuery === 'undefined') {
            console.error('[Tafseer Init] ❌ jQuery is not loaded! This will cause errors.');
        } else {
            console.log('[Tafseer Init] ✅ jQuery is loaded:', window.jQuery.fn.jquery);
        }

        const questionsData = document.querySelectorAll('#questions-data > div');
        const questionContent = document.getElementById('question-content');
        const currentAnswerForm = document.getElementById('current-answer-form');
        // Use window.totalQuestions if available, otherwise fallback to questionsData.length
        const totalQuestions = window.totalQuestions || questionsData.length;

        console.log('Questions data loaded:', {
            type: '{{ $type }}',
            totalQuestions: totalQuestions,
            questionsData: questionsData,
            questionContent: questionContent,
            currentAnswerForm: currentAnswerForm
        });


        // Make currentIndex global so it can be accessed by reveal system
        window.currentIndex = 0;
        // Initialize window.totalQuestions - use questionsData.length as fallback
        const calculatedTotal = questionsData.length;
        window.totalQuestions = window.totalQuestions || calculatedTotal;
        console.log('[Tafseer Index] Initialized window.totalQuestions:', window.totalQuestions, 'calculatedTotal:', calculatedTotal, 'questionsData.length:', questionsData.length);
        let currentIndex = 0;
        let allAnswers = [];

        // Wait for footer.blade.php to load localStorage functions and initialize window.allAnswers
        setTimeout(() => {
            if (typeof window.allAnswers !== 'undefined' && window.allAnswers.length > 0) {
                allAnswers = window.allAnswers;
                console.log('[Tafseer Index] Using allAnswers from footer.blade.php:', allAnswers);

                // Reload first question data with saved values
                if (allAnswers[0]) {
                    const alertInput = currentAnswerForm.querySelector('[name="alert_before_fat7"]');
                    const fat7Input = currentAnswerForm.querySelector('[name="fat7_points"]');

                    if (alertInput && allAnswers[0].alert_before_fat7) {
                        alertInput.value = allAnswers[0].alert_before_fat7;
                        console.log('[Tafseer Index] Restored alert_before_fat7 (count):', allAnswers[0].alert_before_fat7);
                    }

                    if (fat7Input && allAnswers[0].fat7_points) {
                        fat7Input.value = allAnswers[0].fat7_points;
                        console.log('[Tafseer Index] Restored fat7_points (count):', allAnswers[0].fat7_points);
                    }

                    // إعادة بناء صفوف التنبيه/الفتح للحديث مثل القرآن
                    if ('{{ $type }}' === 'hadith' && typeof window.hadithRebuildAlertOpenRows === 'function') {
                        window.hadithRebuildAlertOpenRows();
                    }
                }
            }
        }, 200);

        console.log('[Tafseer Index] Initial allAnswers (will be updated):', allAnswers);

        // Modal functionality
        const saveModal = document.getElementById('save-confirmation-modal');
        const cancelSaveBtn = document.getElementById('cancel-save-btn');
        const confirmSaveBtn = document.getElementById('confirm-save-btn');

        // Show modal
        function showSaveConfirmationModal() {
            saveModal.classList.remove('hidden');
            saveModal.classList.add('flex');
        }

        // Hide modal
        function hideSaveConfirmationModal() {
            saveModal.classList.add('hidden');
            saveModal.classList.remove('flex');
        }

        // Cancel button
        cancelSaveBtn.addEventListener('click', hideSaveConfirmationModal);

        // Confirm save button - Event listener removed to avoid duplicate calls
        // The event listener in footer.blade.php will handle the save action
        // This prevents duplicate AJAX requests when saving in edit mode

        // Close modal when clicking outside
        saveModal.addEventListener('click', (e) => {
            if (e.target === saveModal) {
                hideSaveConfirmationModal();
            }
        });

        // Switch to question at given index
        function switchToQuestion(index) {
            // Use window.totalQuestions if available, otherwise fallback to totalQuestions
            const total = window.totalQuestions || totalQuestions;
            console.log('switchToQuestion called with index:', index, 'totalQuestions:', total, 'window.totalQuestions:', window.totalQuestions);

            if (index < 0 || index >= total) {
                console.log('Invalid index, returning. Index:', index, 'Total:', total);
                return;
            }

            // حفظ السؤال الحالي قبل الانتقال باستخدام الفهرس الحالي
            const oldIndex = currentIndex !== undefined ? currentIndex : (window.currentIndex !== undefined ? window.currentIndex : 0);
            if (typeof window.saveCurrentAnswerForIndex === 'function') {
                window.saveCurrentAnswerForIndex(oldIndex);
            } else if (typeof saveCurrentAnswer === 'function') {
                saveCurrentAnswer();
            }

            // تغيير المؤشر بعد الحفظ
            currentIndex = index;
            window.currentIndex = index; // Update global reference
            console.log('Switching to question:', index, 'currentIndex:', currentIndex);

            // Check if question is revealed (for members) - use window.revealedQuestionIds for real-time updates
            const questionsData = document.querySelectorAll('#questions-data > div');
            const qId = questionsData[index] ? parseInt(questionsData[index].dataset.questionId) : null;
            const currentRevealedIds = window.revealedQuestionIds || @json(($revealedQuestionIds ?? collect())->values());
            const isRevealed = IS_HEAD || (qId && currentRevealedIds.includes(qId));

            console.log('[Tafseer Switch] Question check', {
                index: index,
                qId: qId,
                IS_HEAD: IS_HEAD,
                isRevealed: isRevealed,
                currentRevealedIds: currentRevealedIds,
                windowRevealedQuestionIds: window.revealedQuestionIds,
                currentRevealedIdsType: typeof currentRevealedIds,
                currentRevealedIdsIsArray: Array.isArray(currentRevealedIds),
                qIdInArray: qId ? currentRevealedIds.includes(qId) : false
            });

            // Load question first (shows/hides divs)
            loadQuestion(index);
            
            // أيضاً استدعاء loadQuestionData من footer.blade.php إذا كانت متاحة
            if (typeof window.loadQuestionData === 'function') {
                setTimeout(() => {
                    window.loadQuestionData(index);
                }, 50);
            }

            // If question is revealed, rebuild content AFTER loading (like Quran)
            if (isRevealed && !IS_HEAD) {
                console.log('[Tafseer Switch] Question is revealed, rebuilding content at index:', index);
                // استخدام setTimeout للتأكد من أن loadQuestion انتهى
                setTimeout(() => {
                    if (typeof window.rebuildQuestionContent === 'function') {
                        window.rebuildQuestionContent(index);
                    }
                    // إزالة رسالة "في انتظار رئيس اللجنة" وإظهار المحتوى
                    setTimeout(() => {
                        const questionDiv = document.getElementById('question-' + index);
                        if (questionDiv) {
                            // إزالة المحتوى المقفل إذا كان موجوداً
                            const lockedView = questionDiv.querySelector('.unified-content');
                            if (lockedView) {
                                lockedView.remove();
                            }
                            // إخفاء رسالة "في انتظار رئيس اللجنة"
                            const lockMsg = document.getElementById('member-locked-warning');
                            if (lockMsg) {
                                lockMsg.classList.add('hidden');
                            }
                            // إظهار النموذج
                            const formContent = document.getElementById('current-form-content');
                            if (formContent) {
                                formContent.classList.remove('hidden');
                            }
                            // التأكد من أن المحتوى المفتوح موجود
                            const unlockedView = questionDiv.querySelector('.question-content');
                            if (!unlockedView) {
                                console.warn('[Tafseer Switch] Unlocked view not found, rebuilding again');
                                if (typeof window.rebuildQuestionContent === 'function') {
                                    window.rebuildQuestionContent(index);
                                }
                            }
                        }
                    }, 100);
                }, 150);
            }
            updateQuestionHighlight();
            updateFooter();
            // تم إزالة فرض الدرجة الموحدة - الآن كل سؤال له درجة منفصلة
            // يتم تحميل الدرجة في loadQuestionData بناءً على البيانات المحفوظة

            // تحديث المجموع النهائي بعد الانتقال (ليظل مجموع عام لكل الأسئلة)
            if (typeof window.updateFinalScoreDisplayTafseer === 'function') {
                window.updateFinalScoreDisplayTafseer();
            }

            // Auto-reveal question for head of committee when switching to it
            // الأسئلة تظهر تلقائياً لأعضاء اللجنة بمجرد انتقال رئيس اللجنة إليها
            if (IS_HEAD && qId) {
                // Ensure currentRevealedIds is an array
                const revealedIdsArray = Array.isArray(currentRevealedIds) ? currentRevealedIds : [];
                const isAlreadyRevealed = revealedIdsArray.includes(qId);
                
                console.log('[Tafseer Switch] 🔍 Auto-reveal check', {
                    IS_HEAD: IS_HEAD,
                    qId: qId,
                    currentRevealedIds: currentRevealedIds,
                    revealedIdsArray: revealedIdsArray,
                    isAlreadyRevealed: isAlreadyRevealed,
                    willReveal: !isAlreadyRevealed
                });
                
                if (!isAlreadyRevealed) {
                    console.log('[Tafseer Switch] 🔓 Head switched to question, auto-revealing...', { qId, index });
                    // Automatically reveal the question without needing button click
                    setTimeout(() => {
                        if (typeof window.autoRevealQuestionForHead === 'function') {
                            console.log('[Tafseer Switch] ✅ Calling autoRevealQuestionForHead via window');
                            window.autoRevealQuestionForHead(qId);
                        } else if (typeof autoRevealQuestionForHead === 'function') {
                            console.log('[Tafseer Switch] ✅ Calling autoRevealQuestionForHead directly');
                            autoRevealQuestionForHead(qId);
                        } else {
                            console.error('[Tafseer Switch] ❌ autoRevealQuestionForHead function not found!');
                        }
                    }, 200);
                } else {
                    console.log('[Tafseer Switch] ⏭️ Question already revealed, skipping auto-reveal', { qId });
                }
            }

            // Update reveal button state for the current question
            if (typeof updateRevealButtonState === 'function') {
                setTimeout(() => {
                    updateRevealButtonState();
                }, 150);
            }
        }

        // ابحث عن هذه الدالة في ملف index.blade.php وقم بتحديثها
        function loadQuestion(index) {
            if (index >= questionsData.length) return;
        
            // إظهار السكاشن الخاصة بالسؤال
            const allQuestionDivs = document.querySelectorAll('.question-step');
            allQuestionDivs.forEach((div, i) => {
                div.classList.toggle('hidden', i !== index);
            });
        
            const questionData = questionsData[index];
            const scoreInput = document.getElementById('score-input');
            const maxScore = parseFloat("{{ $gradeQuestion }}") || 10;
        
            if (currentAnswerForm) {
                currentAnswerForm.dataset.questionId = questionData.dataset.questionId;
            }
        
            if (scoreInput) {
                // التحقق من المصفوفة العالمية لهذا السؤال المحدد فقط
                // نتحقق من window.allAnswers[index] مباشرة وليس من window.allAnswers[currentIndex]
                let data = (window.allAnswers && window.allAnswers[index]) ? window.allAnswers[index] : null;
                
                console.log('[Tafseer loadQuestion] Loading question', {
                    index: index,
                    hasData: !!data,
                    savedScore: data ? data.score : null,
                    maxScore: maxScore
                });
                
                // إذا كان هناك درجة محفوظة لهذا السؤال المحدد، استخدمها
                // إذا لم يكن هناك بيانات، ابدأ من الدرجة الكاملة (كل سؤال منفصل)
                // التحقق من أن data.score موجود وليس null وليس undefined (حتى لو كان 0)
                if (data && typeof data.score !== 'undefined' && data.score !== null) {
                    // استخدم الدرجة المحفوظة حتى لو كانت 0
                    scoreInput.value = data.score;
                    console.log('[Tafseer loadQuestion] Using saved score:', data.score);
                } else {
                    // ابدأ من الدرجة الكاملة لهذا السؤال (لا توريث من أسئلة أخرى)
                    scoreInput.value = maxScore;
                    console.log('[Tafseer loadQuestion] Using max score (no saved data):', maxScore);
                }
            }
        }

        // Update question highlighting in the list
        function updateQuestionHighlight() {
            const questionItems = document.querySelectorAll('.question-item');
            console.log('updateQuestionHighlight called, currentIndex:', currentIndex, 'questionItems:', questionItems.length);

            questionItems.forEach((item, i) => {
                // Remove all state classes
                item.classList.remove('bg-blue-900', 'text-white', 'shadow-sm', 'border-blue-900');
                item.classList.remove('bg-slate-50', 'text-slate-600', 'border-slate-200');

                // Update number badge
                const badge = item.querySelector('.flex-shrink-0');
                if (badge) {
                    badge.classList.remove('bg-white', 'text-blue-900', 'bg-slate-200', 'text-slate-500');
                }

                // Update title
                const title = item.querySelector('h4');
                if (title) {
                    title.classList.remove('text-blue-100', 'text-slate-800');
                }

                if (i === currentIndex) {
                    // Active state
                    item.classList.add('bg-blue-900', 'text-white', 'shadow-sm', 'border-blue-900');
                    if (badge) badge.classList.add('bg-white', 'text-blue-900');
                    if (title) title.classList.add('text-blue-100');
                } else {
                    // Inactive state
                    item.classList.add('bg-slate-50', 'text-slate-600', 'border-slate-200');
                    if (badge) badge.classList.add('bg-slate-200', 'text-slate-500');
                    if (title) title.classList.add('text-slate-800');
                }
            });
        }

        // Toggle current evaluation form
        function toggleCurrentEvaluationForm() {
            const formContent = document.getElementById('current-form-content');
            const toggleIcon = document.getElementById('current-toggle-icon');

            if (formContent.classList.contains('hidden')) {
                formContent.classList.remove('hidden');
                toggleIcon.classList.remove('fa-chevron-down');
                toggleIcon.classList.add('fa-chevron-up');
            } else {
                formContent.classList.add('hidden');
                toggleIcon.classList.remove('fa-chevron-up');
                toggleIcon.classList.add('fa-chevron-down');
            }
        }

        // Update footer text and next button
        function updateFooter() {
            const footerText = document.getElementById('footer-text');
            const nextBtn = document.querySelector('.next-btn');
            const prevBtn = document.querySelector('.prev-btn');

            if (!footerText || !nextBtn) {
                return;
            }

            // Use window.totalQuestions if available, otherwise fallback to totalQuestions
            const total = window.totalQuestions || totalQuestions;
            footerText.textContent = `السؤال ${currentIndex + 1} من أصل ${total}`;
            nextBtn.dataset.isLast = currentIndex === total - 1 ? 'true' : 'false';
            nextBtn.innerHTML = currentIndex === total - 1 ?
                '<span id="next-btn-text">إنهاء وحفظ</span> <i class="fas fa-save ms-2"></i>' :
                '<span id="next-btn-text">التالي</span> <i class="fas fa-arrow-left ms-2"></i>';

            // Update previous button state
            if (prevBtn) {
                if (currentIndex === 0) {
                    prevBtn.disabled = true;
                    prevBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    prevBtn.classList.remove('hover:bg-slate-300');
                } else {
                    prevBtn.disabled = false;
                    prevBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    prevBtn.classList.add('hover:bg-slate-300');
                }
            }
        }

        // Save current question to local array
        function saveCurrentAnswer() {
            // Use window.saveCurrentAnswer from footer.blade.php if available
            if (typeof window.saveCurrentAnswer === 'function') {
                window.saveCurrentAnswer();
                return;
            }

            // Fallback if footer.blade.php not loaded yet
            if (!currentAnswerForm) {
                return;
            }

            const scoreInput = currentAnswerForm.querySelector('[name="score"]');
            const noteIdsInput = currentAnswerForm.querySelector('[name="note_ids"]');
            const noteTextsInput = currentAnswerForm.querySelector('[name="note_texts"]');
            const alertInput = currentAnswerForm.querySelector('[name="alert_before_fat7"]');
            const fat7Input = currentAnswerForm.querySelector('[name="fat7_points"]');

            // Get multiple notes
            let noteIds = [];
            let noteTexts = [];
            if (noteIdsInput && noteIdsInput.value) {
                try {
                    noteIds = JSON.parse(noteIdsInput.value);
                } catch (e) {
                    noteIds = [];
                }
            }
            if (noteTextsInput && noteTextsInput.value) {
                try {
                    noteTexts = JSON.parse(noteTextsInput.value);
                } catch (e) {
                    noteTexts = [];
                }
            }

            const answer = {
                question_id: currentAnswerForm.dataset.questionId,
                note_ids: noteIds,
                note_texts: noteTexts,
                alert_before_fat7: alertInput ? alertInput.value || "0" : "0",
                fat7_points: fat7Input ? fat7Input.value || "0" : "0"
            };

            // Only add score for non-hadith types
            if (scoreInput) {
                answer.score = parseFloat(scoreInput.value) || 0;
            } else {
                // For hadith type, set default score to 0
                answer.score = 0;
            }

            // Save to window.allAnswers (shared مع الفوتر) + النسخة المحلية
            // IMPORTANT: Don't reset window.allAnswers if it's already initialized - preserve existing data
            if (!Array.isArray(window.allAnswers)) {
                window.allAnswers = [];
                // Ensure proper length if reset was needed
                while (window.allAnswers.length < window.totalQuestions) {
                    window.allAnswers.push(null);
                }
            }
            // Ensure array has correct length before setting
            while (window.allAnswers.length <= currentIndex) {
                window.allAnswers.push(null);
            }
            window.allAnswers[currentIndex] = answer;
            allAnswers[currentIndex] = answer;

            console.log('[Tafseer Index] Saved answer for question', currentIndex, ':', answer);

            // تحديث المجموع النهائي مباشرة بعد الحفظ
            if (typeof window.updateFinalScoreDisplayTafseer === 'function') {
                window.updateFinalScoreDisplayTafseer();
            }
        }

        // Custom notification function
        function showCustomNotification(title, message, type = 'success', duration = 4000) {
            const existingNotification = document.querySelector('.custom-notification');
            if (existingNotification) {
                existingNotification.remove();
            }

            const notification = document.createElement('div');
            notification.className = 'custom-notification';

            let bgGradient = 'linear-gradient(135deg, #10B981, #059669)';
            let iconClass = 'fas fa-check-circle';

            if (type === 'warning') {
                bgGradient = 'linear-gradient(135deg, #F59E0B, #D97706)';
                iconClass = 'fas fa-exclamation-triangle';
            } else if (type === 'error') {
                bgGradient = 'linear-gradient(135deg, #EF4444, #DC2626)';
                iconClass = 'fas fa-times-circle';
            }

            notification.style.background = bgGradient;

            notification.innerHTML = `
            <div class="notification-header">
                <div class="notification-icon">
                    <i class="${iconClass}"></i>
                </div>
                <div class="notification-title">${title}</div>
                <button class="notification-close" onclick="this.parentElement.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="notification-message">${message}</div>
        `;

            document.body.appendChild(notification);

            setTimeout(() => notification.classList.add('show'), 100);
            setTimeout(() => {
                notification.classList.add('hide');
                setTimeout(() => notification.remove(), 400);
            }, duration);
        }

        // Note: switchToQuestion is already set globally in footer.blade.php with auto-reveal support
        // Don't override it here to preserve the auto-reveal functionality
        // window.switchToQuestion = switchToQuestion; // Commented out - using version from footer.blade.php instead

        // Handle add note button
        const addNoteBtn = currentAnswerForm.querySelector('.add-note-btn');
        const newNoteWrapper = currentAnswerForm.querySelector('.new-note-wrapper');
        const newNoteInput = currentAnswerForm.querySelector('.new-note-input');
        const saveNewNoteBtn = currentAnswerForm.querySelector('.save-new-note-btn');
        const cancelNewNoteBtn = currentAnswerForm.querySelector('.cancel-new-note-btn');

        if (addNoteBtn) {
            addNoteBtn.addEventListener('click', () => {
                newNoteWrapper.classList.toggle('hidden');
                if (!newNoteWrapper.classList.contains('hidden')) {
                    newNoteInput.value = '';
                    newNoteInput.focus();
                }
            });
        }

        // Save new note button
        if (saveNewNoteBtn) {
            saveNewNoteBtn.addEventListener('click', async () => {
                const noteText = newNoteInput.value.trim();
                if (!noteText) {
                    showCustomNotification('خطأ', 'يرجى كتابة نص الملاحظة', 'warning', 2000);
                    return;
                }

                // Disable button during save
                saveNewNoteBtn.disabled = true;
                saveNewNoteBtn.innerHTML = '<i class="fas fa-spinner fa-spin ms-1"></i> جاري الحفظ...';

                try {
                    const notesStoreUrl = `{{ rtrim(url('/'), '/') }}/api/notes/store`;

                    const response = await fetch(notesStoreUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ note: noteText })
                    });

                    const result = await response.json();

                    if (result.success && result.note) {
                        const newNote = { id: result.note.id, text: result.note.note };
                        NOTES_SOURCE.push(newNote);
                        ensureOptionExists(newNote);
                        noteSelect.value = String(newNote.id);
                        noteIdField.value = newNote.id;
                        noteInput.value = newNote.text || '';
                        showCustomNotification('تم الحفظ', 'تمت إضافة الملاحظة الجديدة بنجاح', 'success', 2500);
                        if (typeof saveCurrentAnswer === 'function') {
                            saveCurrentAnswer();
                        }
                    } else {
                        const message = (result && result.message) ? result.message : 'فشل في حفظ الملاحظة';
                        throw new Error(message);
                    }
                } catch (error) {
                    console.error('Error creating note:', error);
                    const errorMessage = error?.message ? error.message : String(error);
                    const debugInfo = `رابط الطلب: ${notesStoreUrl}`;
                    showCustomNotification('خطأ', `حدث خطأ أثناء حفظ الملاحظة:\n${errorMessage}\n${debugInfo}`, 'error', 6000);
                } finally {
                    noteCreateBtn.disabled = false;
                    noteCreateBtn.classList.remove('opacity-60', 'cursor-not-allowed');
                    noteCreateBtn.innerHTML = '<i class="fas fa-plus"></i>';
                }
            });
        }

        // Cancel new note button
        if (cancelNewNoteBtn) {
            cancelNewNoteBtn.addEventListener('click', () => {
                newNoteWrapper.classList.add('hidden');
                newNoteInput.value = '';
            });
        }

        // Request relief button with real-time notification
        document.getElementById('request-relief-btn').addEventListener('click', async () => {
            // Disable button immediately to prevent multiple clicks
            const reliefBtn = document.getElementById('request-relief-btn');
            const reliefBtnText = document.getElementById('relief-btn-text');
            reliefBtn.disabled = true;

            // Change button appearance to show loading (like Quran page)
            reliefBtnText.textContent = 'جاري الإرسال...';
            reliefBtn.className = 'bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200';

            try {
                // قراءة القيمة من السليكتور مباشرة إذا كان موجوداً، وإلا من hiddenInput
                const reliefGradeSelect = document.getElementById('relief-grade-select');
                const reliefGrade = reliefGradeSelect ? reliefGradeSelect.value : document.getElementById('relief-grade').value;
                const reliefReason = document.getElementById('relief-reason').value;

                console.log('[Relief Request] Grade value:', {
                    fromSelect: reliefGradeSelect ? reliefGradeSelect.value : null,
                    fromHidden: document.getElementById('relief-grade').value,
                    finalValue: reliefGrade
                });

                // Validate grade selection
                if (!reliefGrade) {
                    showNotification('error', 'يرجى تحديد درجة التخفيف في إعدادات نموذج التحكيم أولاً');
                    // Reset button to initial state
                    reliefBtnText.textContent = 'إرسال طلب التخفيف';
                    reliefBtn.className = 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50';
                    reliefBtn.disabled = false;
                    reliefBtn.style.pointerEvents = 'auto';
                    reliefBtn.style.userSelect = 'auto';
                    return;
                }

                // Prepare the request data to match the Quran page format
                const requestData = {
                    participant_id: '{{ $participant_id }}',
                    judge_id: '{{ auth()->id() }}',
                    competition_version_branch_id: '{{ $competition_version_branch_id }}',
                    judging_form_setting_id: '{{ $judging_form_setting_id ?? "" }}',
                    grade: reliefGrade,
                    reason: reliefReason || 'طلب تخفيف من المحكم أثناء تقييم التفسير',
                    judge_name: '{{ auth()->user()->full_name ?? "المحكم الحالي" }}',
                    participant_name: '{{ $participant_name ?? "متسابق التفسير" }}'
                };

                console.log('Sending relief request data:', requestData);

                // Send the request to the same endpoint as the Quran page
                const response = await fetch('/api/relief-requests/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(requestData)
                });

                if (response.ok) {
                    const result = await response.json();
                    console.log('API Response:', result);

                    // Show success state on button - keep as "تم الطلب مسبقاً" like Quran
                    reliefBtnText.textContent = 'تم الطلب مسبقاً';
                    reliefBtn.className = 'bg-yellow-500 text-white font-bold py-2 px-6 rounded-lg text-base cursor-not-allowed';
                    reliefBtn.disabled = true;
                    reliefBtn.style.pointerEvents = 'none';
                    reliefBtn.style.userSelect = 'none';

                    // Show success notification
                    showNotification('success', 'تم إرسال طلب التخفيف بنجاح. سيتم إشعار رئيس اللجنة للمراجعة.');

                    // Store in localStorage to prevent resending
                    const reliefKey = `relief-request-sent-{{ auth()->id() }}-{{ $participant_id }}-{{ $competition_version_branch_id }}`;
                    localStorage.setItem(reliefKey, 'true');
                    console.log('Saved relief request state with key:', reliefKey);

                    // Refresh pending list immediately for real-time behavior
                    if (typeof loadPendingReliefRequests === 'function') {
                        loadPendingReliefRequests();
                    }

                } else {
                    console.error('API Error Response:', response.status, response.statusText);
                    const errorText = await response.text();
                    console.error('API Error Details:', errorText);
                    throw new Error('فشل في إرسال الطلب');
                }
            } catch (error) {
                console.error('Error sending relief request:', error);

                // Show error state on button
                reliefBtnText.textContent = 'فشل الإرسال ✗';
                reliefBtn.className = 'bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 animate-pulse';

                // Show error notification
                showNotification('error', 'حدث خطأ أثناء إرسال طلب التخفيف. حاول مرة أخرى.');

                // Reset button after 3 seconds (only for errors)
                setTimeout(() => {
                    reliefBtnText.textContent = 'طلب تخفيف';
                    reliefBtn.className = 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50';
                    reliefBtn.disabled = false;
                }, 3000);
            }
        });

        // Save all evaluations
        async function saveAllEvaluations() {
            // Save current answer before processing
            saveCurrentAnswer();

            const evaluations = [];

            // Collect all answers
            allAnswers.forEach((answer, index) => {
                if (answer && answer.question_id) {
                    const evaluation = {
                        question_id: parseInt(answer.question_id),
                        score: parseFloat(answer.score) || 0,
                        note_ids: answer.note_ids && Array.isArray(answer.note_ids) ? answer.note_ids.map(id => parseInt(id)) : [],
                        notes: answer.note_texts && Array.isArray(answer.note_texts) ? answer.note_texts : []
                    };

                    // Add alert/fat7 data for hadith
                    if ('{{ $type }}' === 'hadith') {
                        evaluation.alert_before_fat7 = parseFloat(answer.alert_before_fat7) || 0;
                        evaluation.fat7_points = parseFloat(answer.fat7_points) || 0;
                    }

                    evaluations.push(evaluation);
                }
            });

            console.log('Collected evaluations:', evaluations);
            console.log('All answers array:', allAnswers);

            if (evaluations.length === 0) {
                showNotification('error', 'لا توجد بيانات للحفظ');
                return false;
            }

            // قراءة edit_start_field من URL إذا كان موجوداً
            const urlParams = new URLSearchParams(window.location.search);
            const editStartField = urlParams.get('edit_start_field');
            const committeeId = urlParams.get('committee_id');

            const data = {
                participant_id: {{ $participant_id }},
                competition_version_branch_id: {{ $competition_version_branch_id }},
                judging_form_setting_id: {{ $judging_form_setting_id }},
                field: '{{ $type }}', // هذا مهم جداً ليعرف السيرفر أي مجال انتهى
                evaluations: JSON.stringify(evaluations),
                request_relief: 0,
                _token: "{{ csrf_token() }}"
            };

            // إضافة edit_start_field إذا كان موجوداً في URL
            if (editStartField) {
                data.edit_start_field = editStartField;
            }

            // إضافة committee_id إذا كان موجوداً في URL
            if (committeeId) {
                data.committee_id = committeeId;
            }

            console.log('Sending data:', data);

            try {
                const response = await fetch('{{ route("judgings.tafseer.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success === true) {
                    showNotification('success', result.message || 'تم حفظ التقييم بنجاح');
                    // Return redirect URL only if provided, otherwise return object with success: true but no redirect
                    if (result.redirect) {
                        return result.redirect;
                    }
                    // إذا لم يكن هناك redirect، نرجع object مع success: true لكن بدون redirect
                    return {
                        success: true,
                        redirect: null
                    };
                } else {
                    // إذا كان success: false، نتحقق من type
                    const messageType = result.type || 'error';
                    const notificationType = messageType === 'info' ? 'info' : 'error';
                    
                    // لا نرمي error - نرجع object يحتوي على معلومات الرسالة
                    return {
                        success: false,
                        type: messageType,
                        title: messageType === 'info' ? 'تنبيه' : 'خطأ',
                        message: result.message || result.error || 'حدث خطأ أثناء حفظ التقييم',
                        notificationType: notificationType
                    };
                }
            } catch (error) {
                console.error('Error saving evaluations:', error);
                
                // لا نرمي error - نرجع object يحتوي على معلومات الخطأ
                const isInfoMessage = error.message && (error.message.includes('الانتظار') || error.message.includes('متبقي'));
                return {
                    success: false,
                    type: isInfoMessage ? 'info' : 'error',
                    title: isInfoMessage ? 'تنبيه' : 'خطأ',
                    message: error.message || 'حدث خطأ أثناء حفظ التقييم',
                    notificationType: isInfoMessage ? 'info' : 'error'
                };
            }
        }

        // Make functions global
        // Note: switchToQuestion is already set globally in footer.blade.php with auto-reveal support
        // Don't override it here to preserve the auto-reveal functionality
        // window.switchToQuestion = switchToQuestion; // Commented out - using version from footer.blade.php instead
        window.toggleCurrentEvaluationForm = toggleCurrentEvaluationForm;

        // Initial setup - question content is already loaded from server
        if (totalQuestions > 0) {
            updateQuestionHighlight();
            updateFooter();

            // Initialize warnings display for hadith
            if ('{{ $type }}' === 'hadith') {
                updateWarningsDisplay();
            }

            // Initialize reveal button (like Quran)
            if (typeof updateRevealButtonState === 'function') {
                setTimeout(() => {
                    updateRevealButtonState();
                }, 300);
            }
        }

        // Initialize relief request system
        if (totalQuestions > 0) {
            initializeReliefRequestSystem();

            // Initialize hadith alert/fat7 system
            console.log('Type is:', '{{ $type }}');
            if ('{{ $type }}' === 'hadith') {
                console.log('Initializing Hadith system...');
                initializeHadithSystem();
            } else {
                console.log('Not Hadith type, skipping Hadith system');
            }
        }

        // Test if buttons exist immediately
        setTimeout(() => {
            const warningBtns = document.querySelectorAll('.warning-btn');
            const openBtns = document.querySelectorAll('.open-btn');
            console.log('Immediate check - Found warning buttons:', warningBtns.length);
            console.log('Immediate check - Found open buttons:', openBtns.length);

            // Removed auto click on warning button to keep initial values at 0

            // Test if current-answer-form exists
            const currentAnswerForm = document.getElementById('current-answer-form');
            console.log('Immediate check - Current answer form exists:', !!currentAnswerForm);
            if (currentAnswerForm) {
                const warningBtnsInForm = currentAnswerForm.querySelectorAll('.warning-btn');
                const openBtnsInForm = currentAnswerForm.querySelectorAll('.open-btn');
                console.log('Immediate check - Warning buttons in form:', warningBtnsInForm.length);
                console.log('Immediate check - Open buttons in form:', openBtnsInForm.length);

                // Test click on first warning button in form
                // Removed auto click on warning button in form

                // Test click on first open button in form
                // Removed auto click on open button in form

                // Test if inputs exist
                const alertInput = currentAnswerForm.querySelector('[name="alert_before_fat7"]');
                const fat7Input = currentAnswerForm.querySelector('[name="fat7_points"]');
                console.log('Immediate check - Alert input exists:', !!alertInput);
                console.log('Immediate check - Fat7 input exists:', !!fat7Input);
                if (alertInput) {
                    console.log('Immediate check - Alert input value:', alertInput.value);
                }
                if (fat7Input) {
                    console.log('Immediate check - Fat7 input value:', fat7Input.value);
                }

                // Test if data-penalty exists
                const warningBtn = currentAnswerForm.querySelector('.warning-btn');
                const openBtn = currentAnswerForm.querySelector('.open-btn');
                if (warningBtn) {
                    console.log('Immediate check - Warning button data-penalty:', warningBtn.dataset.penalty);
                }
                if (openBtn) {
                    console.log('Immediate check - Open button data-penalty:', openBtn.dataset.penalty);
                }

                // Test if data-max-alerts exists
                const formSection = currentAnswerForm.querySelector('[data-max-alerts]');
                if (formSection) {
                    console.log('Immediate check - Form section data-max-alerts:', formSection.dataset.maxAlerts);
                }

                // Test if current-warnings exists
                const warningsDisplay = document.getElementById('current-warnings');
                console.log('Immediate check - Warnings display exists:', !!warningsDisplay);
                if (warningsDisplay) {
                    console.log('Immediate check - Warnings display text:', warningsDisplay.textContent);
                }

                // Test if alertsCount is defined
                console.log('Immediate check - alertsCount value:', alertsCount);

                // Test if updateWarningsDisplay function exists
                console.log('Immediate check - updateWarningsDisplay function exists:', typeof updateWarningsDisplay);

                // Test if saveCurrentAnswer function exists
                console.log('Immediate check - saveCurrentAnswer function exists:', typeof saveCurrentAnswer);

                // Test if showCustomNotification function exists
                console.log('Immediate check - showCustomNotification function exists:', typeof showCustomNotification);

                // Test if allAnswers is defined
                console.log('Immediate check - allAnswers is defined:', typeof allAnswers);
                console.log('Immediate check - allAnswers length:', allAnswers ? allAnswers.length : 'undefined');

                // Test if currentIndex is defined
                console.log('Immediate check - currentIndex value:', currentIndex);

                // Test if totalQuestions is defined
                console.log('Immediate check - totalQuestions value:', totalQuestions);

                // Test if questionsData is defined
                console.log('Immediate check - questionsData is defined:', typeof questionsData);
                console.log('Immediate check - questionsData length:', questionsData ? questionsData.length : 'undefined');

                // Test if questionContent is defined
                console.log('Immediate check - questionContent is defined:', typeof questionContent);
                console.log('Immediate check - questionContent exists:', !!questionContent);

                // Test if currentAnswerForm is defined
                console.log('Immediate check - currentAnswerForm is defined:', typeof currentAnswerForm);
                console.log('Immediate check - currentAnswerForm exists:', !!currentAnswerForm);
            }
        }, 100);

        // Test if buttons exist immediately
        setTimeout(() => {
            const warningBtns = document.querySelectorAll('.warning-btn');
            const openBtns = document.querySelectorAll('.open-btn');
            console.log('Immediate check - Found warning buttons:', warningBtns.length);
            console.log('Immediate check - Found open buttons:', openBtns.length);

            if (warningBtns.length > 0) {
                console.log('Skip auto click on warning button');
            }

            // Test if current-answer-form exists
            const currentAnswerForm = document.getElementById('current-answer-form');
            console.log('Immediate check - Current answer form exists:', !!currentAnswerForm);
            if (currentAnswerForm) {
                const warningBtnsInForm = currentAnswerForm.querySelectorAll('.warning-btn');
                const openBtnsInForm = currentAnswerForm.querySelectorAll('.open-btn');
                console.log('Immediate check - Warning buttons in form:', warningBtnsInForm.length);
                console.log('Immediate check - Open buttons in form:', openBtnsInForm.length);

                // Test click on first warning button in form
                if (warningBtnsInForm.length > 0) {
                    console.log('Skip auto click on warning button in form');
                }

                // Test click on first open button in form
                if (openBtnsInForm.length > 0) {
                    console.log('Skip auto click on open button in form');
                }

                // Test if inputs exist
                const alertInput = currentAnswerForm.querySelector('[name="alert_before_fat7"]');
                const fat7Input = currentAnswerForm.querySelector('[name="fat7_points"]');
                console.log('Immediate check - Alert input exists:', !!alertInput);
                console.log('Immediate check - Fat7 input exists:', !!fat7Input);
                if (alertInput) {
                    console.log('Immediate check - Alert input value:', alertInput.value);
                }
                if (fat7Input) {
                    console.log('Immediate check - Fat7 input value:', fat7Input.value);
                }

                // Test if data-penalty exists
                const warningBtn = currentAnswerForm.querySelector('.warning-btn');
                const openBtn = currentAnswerForm.querySelector('.open-btn');
                if (warningBtn) {
                    console.log('Immediate check - Warning button data-penalty:', warningBtn.dataset.penalty);
                }
                if (openBtn) {
                    console.log('Immediate check - Open button data-penalty:', openBtn.dataset.penalty);
                }

                // Test if data-max-alerts exists
                const formSection = currentAnswerForm.querySelector('[data-max-alerts]');
                if (formSection) {
                    console.log('Immediate check - Form section data-max-alerts:', formSection.dataset.maxAlerts);
                }

                // Test if current-warnings exists
                const warningsDisplay = document.getElementById('current-warnings');
                console.log('Immediate check - Warnings display exists:', !!warningsDisplay);
                if (warningsDisplay) {
                    console.log('Immediate check - Warnings display text:', warningsDisplay.textContent);
                }

                // Test if alertsCount is defined
                console.log('Immediate check - alertsCount value:', alertsCount);

                // Test if updateWarningsDisplay function exists
                console.log('Immediate check - updateWarningsDisplay function exists:', typeof updateWarningsDisplay);

                // Test if saveCurrentAnswer function exists
                console.log('Immediate check - saveCurrentAnswer function exists:', typeof saveCurrentAnswer);

                // Test if showCustomNotification function exists
                console.log('Immediate check - showCustomNotification function exists:', typeof showCustomNotification);

                // Test if allAnswers is defined
                console.log('Immediate check - allAnswers is defined:', typeof allAnswers);
                console.log('Immediate check - allAnswers length:', allAnswers ? allAnswers.length : 'undefined');

                // Test if currentIndex is defined
                console.log('Immediate check - currentIndex value:', currentIndex);

                // Test if totalQuestions is defined
                console.log('Immediate check - totalQuestions value:', totalQuestions);

                // Test if questionsData is defined
                console.log('Immediate check - questionsData is defined:', typeof questionsData);
                console.log('Immediate check - questionsData length:', questionsData ? questionsData.length : 'undefined');

                // Test if questionContent is defined
                console.log('Immediate check - questionContent is defined:', typeof questionContent);
                console.log('Immediate check - questionContent exists:', !!questionContent);

                // Test if currentAnswerForm is defined
                console.log('Immediate check - currentAnswerForm is defined:', typeof currentAnswerForm);
                console.log('Immediate check - currentAnswerForm exists:', !!currentAnswerForm);
            }
        }, 100);

        // Test if buttons exist
        setTimeout(() => {
            const warningBtns = document.querySelectorAll('.warning-btn');
            const openBtns = document.querySelectorAll('.open-btn');
            console.log('Found warning buttons:', warningBtns.length);
            console.log('Found open buttons:', openBtns.length);

            // Test click on first warning button
            // Removed auto click on warning button
        }, 1000);

        // Navigation buttons event listeners are handled in footer.blade.php
        // This ensures consistency and avoids duplicate event listeners

        // Test if buttons exist after loadQuestion
        setTimeout(() => {
            const warningBtns = document.querySelectorAll('.warning-btn');
            const openBtns = document.querySelectorAll('.open-btn');
            console.log('After loadQuestion - Found warning buttons:', warningBtns.length);
            console.log('After loadQuestion - Found open buttons:', openBtns.length);

            // Test click on first warning button
            // Removed auto click on warning button after loadQuestion

            // Test click on first open button
            // Removed auto click on open button after loadQuestion

            // Test if current-answer-form exists
            const currentAnswerForm = document.getElementById('current-answer-form');
            console.log('Current answer form exists:', !!currentAnswerForm);
            if (currentAnswerForm) {
                console.log('Current answer form HTML:', currentAnswerForm.innerHTML);
            }

            // Test if warning buttons exist in current-answer-form
            if (currentAnswerForm) {
                const warningBtnsInForm = currentAnswerForm.querySelectorAll('.warning-btn');
                const openBtnsInForm = currentAnswerForm.querySelectorAll('.open-btn');
                console.log('Warning buttons in form:', warningBtnsInForm.length);
                console.log('Open buttons in form:', openBtnsInForm.length);

                // Test click on first warning button in form
                // Removed auto click on warning button in form

                // Test click on first open button in form
                // Removed auto click on open button in form

                // Test if inputs exist
                const alertInput = currentAnswerForm.querySelector('[name="alert_before_fat7"]');
                const fat7Input = currentAnswerForm.querySelector('[name="fat7_points"]');
                console.log('Alert input exists:', !!alertInput);
                console.log('Fat7 input exists:', !!fat7Input);
                if (alertInput) {
                    console.log('Alert input value:', alertInput.value);
                }
                if (fat7Input) {
                    console.log('Fat7 input value:', fat7Input.value);
                }

                // Test if data-penalty exists
                const warningBtn = currentAnswerForm.querySelector('.warning-btn');
                const openBtn = currentAnswerForm.querySelector('.open-btn');
                if (warningBtn) {
                    console.log('Warning button data-penalty:', warningBtn.dataset.penalty);
                }
                if (openBtn) {
                    console.log('Open button data-penalty:', openBtn.dataset.penalty);
                }

                // Test if data-max-alerts exists
                const formSection = currentAnswerForm.querySelector('[data-max-alerts]');
                if (formSection) {
                    console.log('Form section data-max-alerts:', formSection.dataset.maxAlerts);
                }

                // Test if current-warnings exists
                const warningsDisplay = document.getElementById('current-warnings');
                console.log('Warnings display exists:', !!warningsDisplay);
                if (warningsDisplay) {
                    console.log('Warnings display text:', warningsDisplay.textContent);
                }

                // Test if alertsCount is defined
                console.log('alertsCount value:', alertsCount);

                // Test if updateWarningsDisplay function exists
                console.log('updateWarningsDisplay function exists:', typeof updateWarningsDisplay);

                // Test if saveCurrentAnswer function exists
                console.log('saveCurrentAnswer function exists:', typeof saveCurrentAnswer);

                // Test if showCustomNotification function exists
                console.log('showCustomNotification function exists:', typeof showCustomNotification);

                // Test if allAnswers is defined
                console.log('allAnswers is defined:', typeof allAnswers);
                console.log('allAnswers length:', allAnswers ? allAnswers.length : 'undefined');

                // Test if currentIndex is defined
                console.log('currentIndex value:', currentIndex);

                // Test if totalQuestions is defined
                console.log('totalQuestions value:', totalQuestions);

                // Test if questionsData is defined
                console.log('questionsData is defined:', typeof questionsData);
                console.log('questionsData length:', questionsData ? questionsData.length : 'undefined');

                // Test if questionContent is defined
                console.log('questionContent is defined:', typeof questionContent);
                console.log('questionContent exists:', !!questionContent);

                // Test if currentAnswerForm is defined
                console.log('currentAnswerForm is defined:', typeof currentAnswerForm);
                console.log('currentAnswerForm exists:', !!currentAnswerForm);
            }
        }, 2000);
    });

    // 1. دوال التحكم في الدرجة (للسؤال الحالي فقط)
    function decreaseScore() {
        const scoreInput = document.getElementById('score-input');
        if (!scoreInput) return;
    
        const currentValue = parseFloat(scoreInput.value) || 0;
        const step = 0.5;
        const newValue = Math.max(0, currentValue - step);
        
        scoreInput.value = newValue; 
        saveCurrentAnswer(); // سيحفظ القيمة في window.allAnswers[currentIndex] فقط
        updateFinalScoreDisplayTafseer(); // يحدث إجمالي درجات المتسابق في الهيدر
    }
    
    function increaseScore() {
        const scoreInput = document.getElementById('score-input');
        if (!scoreInput) return;
    
        const currentValue = parseFloat(scoreInput.value) || 0;
        const maxValue = parseFloat(scoreInput.max) || {{ $gradeQuestion ?? 10 }};
        const step = 0.5;
        const newValue = Math.min(maxValue, currentValue + step);
        
        scoreInput.value = newValue;
        saveCurrentAnswer();
        updateFinalScoreDisplayTafseer();
    }
    // لم نعد بحاجة لجمع باقي الدرجات لأنها درجة موحدة

    // نظام التنبيهات والفتح للحديث (مطابق للقرآن)
    function createAlertRow(rowIndex, initialAlerts = 0, initialOpened = false) {
        const container = document.getElementById('alert-open-rows-container');
        const form = document.getElementById('current-answer-form');
        if (!container || !form) return;

        const maxAlerts = parseInt(container.dataset.maxAlerts || '3');

        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 px-3 py-2 border-b border-gray-100 dark:border-gray-700 last:border-0 fade-in';
        row.setAttribute('data-row-index', rowIndex);

        const openBtnClass = initialOpened
            ? 'bg-red-50 text-red-400 cursor-not-allowed border border-red-100'
            : 'bg-orange-50 text-orange-600 hover:bg-orange-100 border border-orange-200';
        const openBtnText = initialOpened ? 'تم الفتح' : 'فتح';
        const alertBtnState = initialOpened ? 'opacity-60 cursor-default' : '';
        const alertBtnDisabled = initialOpened ? 'disabled' : '';

        // تحديد حجم زر التنبيه: كبير للصف الأول، عادي للباقي
        const isFirstRow = rowIndex === 1;
        const alertBtnSize = isFirstRow
            ? 'py-3 px-4 text-base'
            : 'py-2 px-3 text-sm';
        const alertLabelSize = isFirstRow
            ? 'text-xl font-bold'
            : 'text-base font-bold';
        const alertTextSize = isFirstRow
            ? 'text-base font-bold'
            : 'text-sm font-bold';
        const alertGap = isFirstRow ? 'gap-4' : 'gap-3';

        // بناء HTML الصف - الترتيب الجديد: التنبيه (حجم كبير للصف الأول) | الفتح (برتقالي، حجم أقل) | الاستعادة (آخر)
        row.innerHTML = `
            <button type="button" ${alertBtnDisabled}
                    class="alert-row-btn bg-amber-50 border border-amber-100 rounded-lg ${alertBtnSize} flex items-center justify-center ${alertGap} ${alertBtnState}" style="order: 1; flex: 1.2;">
                <span class="text-amber-800 ${alertTextSize} cursor-pointer hover:text-amber-900 transition-colors select-none">تنبيه</span>
                <span class="text-amber-900 ${alertLabelSize} alert-label cursor-pointer hover:text-amber-950 transition-colors select-none">${initialAlerts}</span>
            </button>
            <button type="button" ${initialOpened ? 'disabled' : ''}
                    class="open-row-btn py-1.5 px-2 rounded-lg font-bold flex items-center justify-center gap-1 transition-all text-xs ${openBtnClass}" style="order: 2; flex: 0.75;">
                ${openBtnText}
            </button>
            <button type="button"
                    class="undo-row-btn w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors" style="order: 3;">
                <i class="fas fa-undo text-[10px]"></i>
            </button>
        `;

        let rowAlerts = initialAlerts;
        let rowOpened = initialOpened;

        const alertBtn = row.querySelector('.alert-row-btn');
        const alertLabel = row.querySelector('.alert-label'); // الآن يحتوي على العدد فقط
        const openBtn = row.querySelector('.open-row-btn');
        const undoBtn = row.querySelector('.undo-row-btn');

        const alertInput = form.querySelector('[name="alert_new_position"]');
        const fat7Input = form.querySelector('[name="fat7_points"]');

        const applyOpenState = () => {
            rowOpened = true;
            openBtn.classList.remove('bg-orange-50', 'text-orange-600', 'hover:bg-orange-100', 'border-orange-200');
            openBtn.classList.add('bg-red-50', 'text-red-400', 'cursor-not-allowed', 'border-red-100');
            openBtn.innerHTML = 'تم الفتح';
            openBtn.disabled = true;
            alertBtn.classList.add('opacity-60', 'cursor-default');
            alertBtn.disabled = true;
            if (alertLabel) {
                alertLabel.classList.remove('cursor-pointer', 'hover:text-amber-900');
                alertLabel.classList.add('cursor-default', 'opacity-60');
                alertLabel.style.pointerEvents = 'none';
            }
        };

        const addAlert = (e) => {
            e.stopPropagation();
            if (rowOpened) return;

            rowAlerts++;
            // تحديث العدد فقط (كلمة "تنبيه" ثابتة)
            if (alertLabel) {
                alertLabel.textContent = rowAlerts;
            }

            const currentTotalAlerts = parseInt(alertInput.value || '0') || 0;
            alertInput.value = currentTotalAlerts + 1;

            const currentRowNum = Array.from(container.children).indexOf(row) + 1;
            const totalRows = container.children.length;

            // إذا كان في آخر صف، نولد صف جديد تلقائياً
            if (currentRowNum === totalRows) {
                createAlertRow(totalRows + 1);
            }

            if (maxAlerts > 0 && rowAlerts >= maxAlerts) {
                const currentFat7 = parseInt(fat7Input.value || '0') || 0;
                fat7Input.value = currentFat7 + 1;
                applyOpenState();
            }

            if (typeof window.updateFinalScoreDisplayTafseer === 'function') {
                window.updateFinalScoreDisplayTafseer();
            }
        };

        // حدث: الضغط على كلمة "تنبيه" نفسها لإضافة تنبيه
        if (alertLabel) {
            alertLabel.addEventListener('click', addAlert);
        }

        // أيضاً يمكن الضغط على زر التنبيه كامل لإضافة تنبيه
        if (alertBtn) {
            alertBtn.addEventListener('click', (e) => {
                // فقط إذا لم يتم النقر على العناصر الأخرى
                if (e.target === alertBtn || e.target === alertLabel || e.target.closest('.alert-row-btn') === alertBtn) {
                    addAlert(e);
                }
            });
        }

        openBtn.addEventListener('click', () => {
            if (rowOpened) return;

            const currentFat7 = parseInt(fat7Input.value || '0') || 0;
            fat7Input.value = currentFat7 + 1;
            applyOpenState();

            const currentRowNum = Array.from(container.children).indexOf(row) + 1;
            const totalRows = container.children.length;
            if (currentRowNum === totalRows) {
                createAlertRow(totalRows + 1);
            }

            if (typeof window.updateFinalScoreDisplayTafseer === 'function') {
                window.updateFinalScoreDisplayTafseer();
            }
        });

        undoBtn.addEventListener('click', () => {
            if (rowAlerts <= 0 && !rowOpened) return;

            const currentRowNum = Array.from(container.children).indexOf(row) + 1;
            const totalRows = container.children.length;

            if (rowAlerts > 0) {
                rowAlerts--;
                // تحديث العدد فقط (كلمة "تنبيه" ثابتة)
                if (alertLabel) {
                    alertLabel.textContent = rowAlerts;
                }
                const currentTotalAlerts = parseInt(alertInput.value || '0') || 0;
                alertInput.value = Math.max(0, currentTotalAlerts - 1);
            }

            if (rowOpened) {
                const currentFat7 = parseInt(fat7Input.value || '0') || 0;
                fat7Input.value = Math.max(0, currentFat7 - 1);
                rowOpened = false;

                openBtn.disabled = false;
                openBtn.classList.remove('bg-red-50', 'text-red-400', 'cursor-not-allowed', 'border-red-100');
                openBtn.classList.add('bg-orange-50', 'text-orange-600', 'hover:bg-orange-100', 'border-orange-200');
                openBtn.innerHTML = 'فتح';
                alertBtn.disabled = false;
                alertBtn.classList.remove('opacity-60', 'cursor-default');
                if (alertLabel) {
                    alertLabel.classList.add('cursor-pointer', 'hover:text-amber-900');
                    alertLabel.classList.remove('cursor-default', 'opacity-60');
                    alertLabel.style.pointerEvents = '';
                }
            }

            // بعد التراجع، نفحص حالة الصف الحالي
            const hasAlerts = rowAlerts > 0;
            const hasOpened = rowOpened;

            // إذا كان الصف فارغ (0 تنبيهات و 0 فتح) وليس الصف الأول، نحذفه
            if (!hasAlerts && !hasOpened && currentRowNum > 1) {
                row.remove();

                // بعد الحذف، نفحص الصفوف المتبقية من النهاية للبداية
                // نحذف أي صف فارغ حتى نصل للصف الأول
                setTimeout(() => {
                    const allRows = Array.from(container.children);
                    for (let i = allRows.length - 1; i >= 1; i--) {
                        const checkRow = allRows[i];
                        const checkAlertLabel = checkRow.querySelector('.alert-label');
                        const checkOpenBtn = checkRow.querySelector('.open-row-btn');

                        if (checkAlertLabel && checkOpenBtn) {
                            const checkAlerts = parseInt(checkAlertLabel.textContent || '0') || 0;
                            const checkIsOpened = checkOpenBtn.textContent.trim() === 'تم الفتح';

                            if (checkAlerts === 0 && !checkIsOpened) {
                                checkRow.remove();
                            } else {
                                break; // توقف عند أول صف غير فارغ
                            }
                        }
                    }
                }, 10);
            }
            // إذا وصل للصف الأول وكان هناك تنبيه أو فتح، نولد سطر جديد
            else if (currentRowNum === 1 && (hasAlerts || hasOpened)) {
                const remainingRows = Array.from(container.children);
                if (remainingRows.length === 1) {
                    createAlertRow(2, 0, false);
                }
            }

            if (typeof window.updateFinalScoreDisplayTafseer === 'function') {
                window.updateFinalScoreDisplayTafseer();
            }
        });

        container.appendChild(row);
        container.scrollTop = container.scrollHeight;
    }

    // نظام الحفظ والاسترجاع للحديث (مشابه للقرآن)
    let hadithQuestionsAlertHistory = {};

    // دالة حفظ حالة بوكس التنبيهات/الفتح للحديث
    function saveHadithQuestionState(indexOverride) {
        const indexKey = typeof indexOverride === 'number' ? indexOverride : 0;
        console.log('[Hadith Alerts] saveHadithQuestionState() called for index', indexKey);

        const container = document.getElementById('alert-open-rows-container');
        if (!container) return;

        const rows = [];
        const allRows = container.querySelectorAll('.flex.items-center');
        console.log(`[Hadith Alerts] saveHadithQuestionState - Found ${allRows.length} rows in DOM`);

        allRows.forEach((row, index) => {
            const alertBtn = row.querySelector('.alert-row-btn');
            const openBtn = row.querySelector('.open-row-btn');
            const alertLabel = row.querySelector('.alert-label');

            let alertsCount = 0;
            if (alertLabel) {
                alertsCount = parseInt(alertLabel.textContent.trim()) || 0;
            }

            const isOpened = openBtn ? (openBtn.textContent.trim() === 'تم الفتح' || openBtn.disabled === true) : false;

            console.log(`[Hadith Alerts] Row ${index + 1}: alerts=${alertsCount}, opened=${isOpened}, openBtnText="${openBtn ? openBtn.textContent.trim() : 'N/A'}"`);

            if (alertsCount > 0 || isOpened) {
                rows.push({
                    alerts: alertsCount,
                    opened: isOpened === true
                });
                console.log(`[Hadith Alerts] Saved row ${index + 1}: alerts=${alertsCount}, opened=${isOpened}`);
            } else {
                console.log(`[Hadith Alerts] Skipped empty row ${index + 1}`);
            }
        });

        console.log(`[Hadith Alerts] Total saved rows: ${rows.length}`);

        const form = document.getElementById('current-answer-form');
        let alertTotal = 0;
        let fat7Total = 0;

        if (form) {
            // للحديث نستخدم alert_before_fat7 بدلاً من alert_new_position
            const alertInput = form.querySelector('[name="alert_before_fat7"]') || form.querySelector('[name="alert_new_position"]');
            const fat7Input = form.querySelector('[name="fat7_points"]');
            alertTotal = alertInput ? alertInput.value : 0;
            fat7Total = fat7Input ? fat7Input.value : 0;
        }

        hadithQuestionsAlertHistory[indexKey] = {
            rows: rows,
            totalAlerts: alertTotal,
            totalFat7: fat7Total
        };

        console.log(`[Hadith Alerts] Saved state for Q${indexKey}:`, hadithQuestionsAlertHistory[indexKey]);
    }

    // دالة استرجاع حالة بوكس التنبيهات/الفتح للحديث
    function restoreHadithQuestionState(index) {
        console.log('[Hadith Alerts] restoreHadithQuestionState() called for index', index);

        const container = document.getElementById('alert-open-rows-container');
        const form = document.getElementById('current-answer-form');
        if (!container || !form) return;

        // للحديث نستخدم alert_before_fat7 بدلاً من alert_new_position
        const alertInput = form.querySelector('[name="alert_before_fat7"]') || form.querySelector('[name="alert_new_position"]');
        const fat7Input = form.querySelector('[name="fat7_points"]');

        container.innerHTML = '';
        console.log(`[Hadith Alerts] restoreHadithQuestionState - Cleared container, now has ${container.children.length} rows`);

        const hasHistory = Object.prototype.hasOwnProperty.call(hadithQuestionsAlertHistory, index);
        console.log('[Hadith Alerts]   hasHistory:', hasHistory, 'keys:', Object.keys(hadithQuestionsAlertHistory));

        if (hasHistory) {
            const data = hadithQuestionsAlertHistory[index];

            if (alertInput) alertInput.value = data.totalAlerts;
            if (fat7Input) fat7Input.value = data.totalFat7;

            let nonEmptyRows = data.rows ? data.rows
                .filter(row => row.alerts > 0 || row.opened === true)
                .map(row => ({
                    alerts: row.alerts || 0,
                    opened: row.opened === true
                })) : [];

            // إزالة الصفوف المكررة: إذا كان هناك صفان بنفس عدد التنبيهات، نحتفظ بالصف المفتوح فقط
            const uniqueRows = [];
            const seenAlerts = new Map();

            nonEmptyRows.forEach(row => {
                const existingRow = seenAlerts.get(row.alerts);

                if (!existingRow) {
                    seenAlerts.set(row.alerts, row);
                    uniqueRows.push(row);
                } else if (row.opened === true && existingRow.opened === false) {
                    const index = uniqueRows.indexOf(existingRow);
                    if (index >= 0) {
                        uniqueRows[index] = row;
                        seenAlerts.set(row.alerts, row);
                    }
                }
            });
            nonEmptyRows = uniqueRows;

            console.log(`[Hadith Alerts] restoreHadithQuestionState - Total rows in data: ${data.rows ? data.rows.length : 0}, Non-empty: ${nonEmptyRows.length}`);
            console.log(`[Hadith Alerts] Non-empty rows (after deduplication):`, nonEmptyRows);

            if (nonEmptyRows.length > 0) {
                nonEmptyRows.forEach((rowState, i) => {
                    console.log(`[Hadith Alerts] Creating row ${i + 1}: alerts=${rowState.alerts}, opened=${rowState.opened}`);
                    createAlertRow(i + 1, rowState.alerts, rowState.opened);
                });
                const lastRow = nonEmptyRows[nonEmptyRows.length - 1];
                if (lastRow && (lastRow.alerts > 0 || lastRow.opened === true)) {
                    console.log(`[Hadith Alerts] Adding empty row at the end`);
                    createAlertRow(nonEmptyRows.length + 1, 0, false);
                }
            } else {
                console.log(`[Hadith Alerts] No saved rows, creating empty row`);
                createAlertRow(1, 0, false);
            }

        } else {
            // لا يوجد تاريخ في الذاكرة: نعيد بناء الصفوف من القيم الإجمالية
            const maxAlerts = parseInt(container.dataset.maxAlerts || '3');
            const totalAlerts = alertInput ? parseInt(alertInput.value || '0') || 0 : 0;
            const totalFat7 = fat7Input ? parseInt(fat7Input.value || '0') || 0 : 0;

            console.log('[Hadith Alerts]   no history, rebuilding from totals', {
                index,
                totalAlerts,
                totalFat7,
                maxAlerts
            });

            let remainingAlerts = totalAlerts;
            let remainingOpens = totalFat7;
            const builtRows = [];
            let rowIndex = 1;

            if (totalAlerts === 0 && totalFat7 === 0) {
                createAlertRow(1, 0, false);
                builtRows.push({ alerts: 0, opened: false });
            } else {
                while (remainingOpens > 0) {
                    const alertsForThisOpen = maxAlerts > 0 ? Math.min(remainingAlerts, maxAlerts) : 0;
                    createAlertRow(rowIndex++, alertsForThisOpen, true);
                    builtRows.push({ alerts: alertsForThisOpen, opened: true });

                    remainingOpens--;
                    remainingAlerts = Math.max(0, remainingAlerts - alertsForThisOpen);
                }

                if (remainingAlerts > 0) {
                    createAlertRow(rowIndex, remainingAlerts, false);
                    builtRows.push({ alerts: remainingAlerts, opened: false });
                }

                if (totalAlerts > 0 || totalFat7 > 0) {
                    const lastRowIndex = builtRows.length > 0 ? builtRows.length + 1 : 1;
                    createAlertRow(lastRowIndex, 0, false);
                }
            }

            hadithQuestionsAlertHistory[index] = {
                rows: builtRows,
                totalAlerts,
                totalFat7
            };
        }

        if (typeof window.updateFinalScoreDisplayTafseer === 'function') {
            window.updateFinalScoreDisplayTafseer();
        }
    }

    // دالة إعادة بناء الصفوف من القيم الإجمالية (للحديث)
    function hadithRebuildAlertOpenRows() {
        const container = document.getElementById('alert-open-rows-container');
        const form = document.getElementById('current-answer-form');
        if (!container || !form) return;

        // للحديث نستخدم alert_before_fat7 بدلاً من alert_new_position
        const alertInput = form.querySelector('[name="alert_before_fat7"]') || form.querySelector('[name="alert_new_position"]');
        const fat7Input = form.querySelector('[name="fat7_points"]');

        container.innerHTML = '';

        const maxAlerts = parseInt(container.dataset.maxAlerts || '3');
        const totalAlerts = alertInput ? parseInt(alertInput.value || '0') || 0 : 0;
        const totalFat7 = fat7Input ? parseInt(fat7Input.value || '0') || 0 : 0;

        console.log('[Hadith Rebuild] Rebuilding from totals', {
            totalAlerts,
            totalFat7,
            maxAlerts
        });

        let remainingAlerts = totalAlerts;
        let remainingOpens = totalFat7;
        let rowIndex = 1;

        if (totalAlerts === 0 && totalFat7 === 0) {
            createAlertRow(1, 0, false);
        } else {
            while (remainingOpens > 0) {
                const alertsForThisOpen = maxAlerts > 0 ? Math.min(remainingAlerts, maxAlerts) : 0;
                createAlertRow(rowIndex++, alertsForThisOpen, true);
                remainingOpens--;
                remainingAlerts = Math.max(0, remainingAlerts - alertsForThisOpen);
            }

            if (remainingAlerts > 0) {
                createAlertRow(rowIndex, remainingAlerts, false);
            }

            if (totalAlerts > 0 || totalFat7 > 0) {
                const lastRowIndex = rowIndex + 1;
                createAlertRow(lastRowIndex, 0, false);
            }
        }

        if (typeof window.updateFinalScoreDisplayTafseer === 'function') {
            window.updateFinalScoreDisplayTafseer();
        }
    }

    // جعل الدوال متاحة عالمياً
    window.hadithRebuildAlertOpenRows = hadithRebuildAlertOpenRows;
    window.saveHadithQuestionState = saveHadithQuestionState;
    window.restoreHadithQuestionState = restoreHadithQuestionState;

    // Initialize Hadith system
    document.addEventListener('DOMContentLoaded', function() {
        if ('{{ $type }}' === 'hadith') {
            // Initialize first row
            setTimeout(() => {
                createAlertRow(1, 0, false);
            }, 500);
        }
    });

    // Relief Request System Functions
    function initializeReliefRequestSystem() {
        // Check eligibility on page load
        if (typeof checkReliefRequestEligibility === 'function') {
            checkReliefRequestEligibility();
        }

        // Load pending relief requests
        if (typeof loadPendingReliefRequests === 'function') {
            loadPendingReliefRequests();
        }

        // Set up relief request button using attachReliefButtonListener (like Quran)
        if (typeof attachReliefButtonListener === 'function') {
            attachReliefButtonListener();
        } else {
            // Fallback: attach directly if function doesn't exist
            const reliefBtn = document.getElementById('request-relief-btn');
            if (reliefBtn) {
                reliefBtn.addEventListener('click', handleReliefButtonClick);
            }
        }
    }

    // Function to send relief request notifications (separate, non-blocking)
    async function sendReliefRequestNotifications(reliefData) {
        try {
            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Create AbortController for timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 8000); // 8 seconds timeout

            let response;
            try {
                response = await fetch('/api/relief-requests/send-notifications', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(reliefData),
                    signal: controller.signal
                });
                clearTimeout(timeoutId);
            } catch (error) {
                clearTimeout(timeoutId);
                if (error.name === 'AbortError') {
                    console.warn('Notification send timeout (non-blocking)');
                    return;
                }
                throw error;
            }

            if (response.ok) {
                const result = await response.json();
                console.log('Notifications sent to committee members successfully:', result);
            } else {
                console.error('Failed to send notifications to committee members:', response.status, response.statusText);
            }
        } catch (error) {
            console.error('Error sending notifications to committee members (non-blocking):', error);
        }
    }

    // Function to send relief request (deprecated - use handleReliefButtonClick instead)
    async function sendReliefRequest() {
        // قراءة القيمة من السليكتور مباشرة إذا كان موجوداً، وإلا من hiddenInput
        const reliefGradeSelect = document.getElementById('relief-grade-select');
        const reliefGrade = reliefGradeSelect ? reliefGradeSelect.value : document.getElementById('relief-grade').value;
        const reliefReason = document.getElementById('relief-reason').value;

        console.log('[Relief Request] Grade value:', {
            fromSelect: reliefGradeSelect ? reliefGradeSelect.value : null,
            fromHidden: document.getElementById('relief-grade').value,
            finalValue: reliefGrade
        });
        const reliefBtn = document.getElementById('request-relief-btn');
        const reliefBtnText = document.getElementById('relief-btn-text');

        if (!reliefGrade) {
            showCustomNotification(
                'درجة التخفيف غير محددة',
                'يرجى تحديد درجة التخفيف في إعدادات نموذج التحكيم أولاً',
                'warning',
                3000
            );
            return;
        }

        // No need to check for previous approvals - users can approve multiple relief requests
        // Only check for existing pending requests

        // Check if there's already a pending relief request for this participant
        const hasExistingRelief = await checkIfExistingReliefRequest();
        if (hasExistingRelief) {
            resetReliefButton();
            return;
        }

        // Show loading state
        reliefBtn.disabled = true;
        reliefBtnText.innerHTML = '<i class="fas fa-spinner fa-spin ms-2"></i> جاري الإرسال...';

        // Set sending state to prevent real-time updates from interfering
        reliefSendingInProgress = true;
        reliefLockedState = 'sending';

        try {
            const requestData = {
                participant_id: '{{ $participant_id }}',
                judge_id: '{{ auth()->id() }}',
                competition_version_branch_id: '{{ $competition_version_branch_id }}',
                judging_form_setting_id: '{{ $judging_form_setting_id }}',
                grade: reliefGrade,
                reason: reliefReason,
                _token: '{{ csrf_token() }}'
            };

            const response = await fetch('/api/relief-requests/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(requestData)
            });

            const result = await response.json();

            if (result.success) {
                showCustomNotification(
                    'تم إرسال طلب التخفيف',
                    `تم إرسال طلب التخفيف بنجاح (${reliefGrade}). سيتم إشعار أعضاء اللجنة.`,
                    'success',
                    5000
                );

                // Disable the button and show "تم الطلب مسبقاً" like Quran page
                reliefBtn.disabled = true;
                reliefBtnText.textContent = 'تم الطلب مسبقاً';
                reliefBtn.className = 'bg-yellow-500 text-white font-bold py-2 px-6 rounded-lg text-base cursor-not-allowed';
                reliefBtn.style.pointerEvents = 'none';
                reliefBtn.style.userSelect = 'none';

                // Store in localStorage to prevent duplicate requests
                const storageKey = `relief-request-sent-{{ auth()->id() }}-{{ $participant_id }}-{{ $competition_version_branch_id ?? 0 }}`;
                localStorage.setItem(storageKey, 'true');

                // Reload pending requests
                loadPendingReliefRequests();

                // Send notifications to committee members
                try {
                    const notificationData = {
                        request_id: result.request_id,
                        judge_name: '{{ auth()->user()->full_name ?? "محكم" }}',
                        participant_name: '{{ $participant_name ?? "متسابق" }}',
                        grade: reliefGrade,
                        reason: reliefReason,
                        competition_version_branch_id: '{{ $competition_version_branch_id }}',
                        _token: '{{ csrf_token() }}'
                    };

                    const notificationResponse = await fetch('/api/relief-requests/send-notifications', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(notificationData)
                    });

                    if (notificationResponse.ok) {
                        console.log('Notifications sent successfully');
                    } else {
                        console.error('Failed to send notifications');
                    }
                } catch (error) {
                    console.error('Error sending notifications:', error);
                }
            } else {
                showCustomNotification(
                    'فشل في إرسال الطلب',
                    result.message || 'حدث خطأ أثناء إرسال طلب التخفيف',
                    'error',
                    5000
                );
                resetReliefButton();
            }
        } catch (error) {
            console.error('Error sending relief request:', error);
            showCustomNotification(
                'خطأ في الاتصال',
                'حدث خطأ أثناء الاتصال بالخادم',
                'error',
                5000
            );
            resetReliefButton();
        }
    }

    // Function to reset relief button
    function resetReliefButton() {
        const reliefBtn = document.getElementById('request-relief-btn');
        const reliefBtnText = document.getElementById('relief-btn-text');

        if (reliefBtn && reliefBtnText) {
            reliefBtn.disabled = false;
            reliefBtnText.textContent = 'إرسال طلب التخفيف';
            reliefBtn.className = 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50';
        }
    }

    // Function to load pending relief requests (real-time like Quran page)
    async function loadPendingReliefRequests() {
        try {
            console.log('Loading pending relief requests...');
            const competitionVersionBranchId = '{{ $competition_version_branch_id ?? 0 }}';
            const response = await fetch(`/api/relief-requests/pending?competition_version_branch_id=${competitionVersionBranchId}&t=${Date.now()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                const rawText = await response.text();
                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (e) {
                    console.error('Pending relief requests: JSON parse failed. Raw:', rawText);
                    return;
                }
                console.log('Pending relief requests loaded:', data);
                const list = Array.isArray(data) ? data : (data.requests || []);
                displayPendingReliefRequests(list);
                // ensure button reflects current global state
                if (typeof checkReliefRequestEligibility === 'function') {
                    checkReliefRequestEligibility();
                }
            } else {
                console.error('Failed to load pending relief requests:', response.status, response.statusText);
            }
        } catch (error) {
            console.error('Error loading pending relief requests:', error);
        }
    }

    // Function to display pending relief requests (same UI/logic as Quran page)
    function displayPendingReliefRequests(requests) {
        console.log('Displaying pending relief requests:', requests);
        const container = document.getElementById('relief-requests-list');
        const section = document.getElementById('pending-relief-requests');

        if (!container || !section) {
            console.error('Container or section not found');
            return;
        }

        if (!requests || requests.length === 0) {
            section.classList.add('hidden');
            console.log('No relief requests found, section hidden');
            return;
        }

        console.log(`Found ${requests.length} pending relief requests`);
        container.innerHTML = '';
        section.classList.remove('hidden');

        const currentUserId = '{{ auth()->id() }}';
        requests.forEach(request => {
            const isOwnRequest = (request.is_own_request !== undefined) ? request.is_own_request : (String(request.judge_id) === String(currentUserId));
            const bgColor = isOwnRequest ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-700' : 'bg-orange-50 dark:bg-orange-900/20 border-orange-200 dark:border-orange-700';
            const iconColor = isOwnRequest ? 'text-blue-600' : 'text-orange-600';
            const badgeColor = isOwnRequest ? 'bg-blue-100 text-blue-800' : 'bg-orange-100 text-orange-800';

            const requestDiv = document.createElement('div');
            requestDiv.className = `${bgColor} border rounded-lg p-3`;
            const fieldLabel = (function () {
                const t = '{{ $type ?? "interpretation" }}';
                if (t === 'hadith') return 'الحديث';
                if (t === 'dirayah') return 'الدراية';
                if (t === 'interpretation') return 'التفسير';
                return 'القرآن';
            })();

            requestDiv.innerHTML = `
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <i class="fas ${isOwnRequest ? 'fa-user-check' : 'fa-user'} ${iconColor} ms-2"></i>
                            <span class="font-medium text-gray-900 dark:text-white">${request.judge_name}</span>
                            ${isOwnRequest ? '<span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium me-2">طلبك</span>' : ''}
                            <span class="text-sm text-gray-500 dark:text-gray-400 me-2">يطلب تخفيف</span>
                            <span class="${badgeColor} px-2 py-1 rounded text-xs font-medium me-2">${request.grade || 'غير محدد'}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 me-2">لـ</span>
                            <span class="font-medium text-gray-900 dark:text-white">${request.participant_name}</span>
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                            <i class="fas fa-clock ms-1"></i>
                            ${request.created_at}
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300 mb-3 break-words">
                            ${request.reason || ('طلب تخفيف من المحكم أثناء تقييم ' + fieldLabel)}
                        </div>
                    </div></div>
                    <div class="mt-3 flex items-center justify-center gap-3">
                        ${!isOwnRequest ? `
                              <button type=\"button\" onclick=\"approveReliefRequest('${request.id}')\"\n                                     class=\"bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center\">\n                                <i class=\"fas fa-check ms-1\"></i>\n                                موافقة\n                            </button>\n                            <button type=\"button\" onclick=\"denyReliefRequest('${request.id}')\"\n                                    class=\"bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center\">\n                                <i class=\"fas fa-times ms-1\"></i>\n                                رفض\n                            </button>\n                        ` : `\n                             <span class=\"bg-gray-100 text-gray-600 px-4 py-2 rounded-lg text-sm font-medium flex items-center\">\n                                 <i class=\"fas fa-info-circle ms-1\"></i>\n                                 طلبك\n                             </span>\n                         `}
                          <button type=\"button\" onclick=\"viewReliefRequestDetails('${request.id}')\"\n                                 class=\"bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center\">\n                             <i class=\"fas fa-eye ms-1\"></i>\n                             تفاصيل\n                         </button>
                    </div>

            `;
            container.appendChild(requestDiv);
        });
    }

    // Load relief settings and real-time pending list when page loads
    document.addEventListener('DOMContentLoaded', function () {
        console.log('DOMContentLoaded fired - Starting tafseer relief request system');

        // Always attach the click event listener on page load
        attachReliefButtonListener();

        // Initial loads
        loadPendingReliefRequests();
        checkReliefRequestEligibility();
        loadReliefSettings();

        // Poll periodically like Quran page
        setInterval(loadPendingReliefRequests, 10000);
        // Re-check eligibility frequently so all judges see button state update without refresh
        setInterval(checkReliefRequestEligibility, 5000);
        // Real-time relief request status checking (like Quran page)
        setInterval(checkReliefRequestStatusRealTime, 2000);
        // Optionally check notifications separately (reuse Quran cadence)
        if (typeof checkForNotifications === 'function') {
            setInterval(checkForNotifications, 5000);
        }
    });

    // Function to view relief request details
    // Prevent multiple simultaneous requests
    let isLoadingDetails = false;

    async function viewReliefRequestDetails(requestId) {
        // Prevent multiple simultaneous requests
        if (isLoadingDetails) {
            console.log('Already loading details, please wait...');
            return;
        }

        try {
            isLoadingDetails = true;
            const correlationId = `${Date.now()}_${Math.random().toString(36).slice(2, 8)}`;
            console.log('Fetching relief request details for ID:', requestId, 'CID:', correlationId);

            // Show loading modal
            showReliefDetailsModal(requestId, null, true);

            // Fetch request details (add cache-busting)
            const response = await fetch(`/api/relief-requests/details/${requestId}?t=${Date.now()}&cid=${encodeURIComponent(correlationId)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                credentials: 'same-origin'
            });

            console.log('Response status:', response.status, 'CID:', correlationId);
            console.log('Response headers:', response.headers);

            if (response.ok) {
                // Robust parse with fallback logging
                const rawText = await response.text();
                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (e) {
                    console.error('Relief details: JSON parse failed. Raw:', rawText, 'CID:', correlationId);
                    showCustomNotification('خطأ', `استجابة غير صالحة من الخادم أثناء تحميل تفاصيل الطلب (CID: ${correlationId})`, 'error', 4000);
                    hideReliefDetailsModal();
                    return;
                }
                console.log('Response data:', data);

                if (data.success && data.request) {
                    showReliefDetailsModal(requestId, data.request, false);
                } else {
                    console.error('Invalid response structure:', data, 'CID:', correlationId);
                    showCustomNotification('خطأ', (data.message || 'فشل في تحميل تفاصيل الطلب') + ` (CID: ${correlationId})`, 'error', 4000);
                    hideReliefDetailsModal();
                }
            } else {
                const errorText = await response.text();
                console.error('Failed to load relief request details:', {
                    status: response.status,
                    statusText: response.statusText,
                    response: errorText,
                    cid: correlationId
                });
                let extra = '';
                try {
                    const parsed = JSON.parse(errorText);
                    extra = parsed.message || parsed.error || '';
                } catch (_) {
                    extra = (errorText || '').slice(0, 200);
                }
                showCustomNotification('خطأ', `فشل في تحميل تفاصيل الطلب (${response.status})${extra ? ' - ' + extra : ''} (CID: ${correlationId})`, 'error', 4000);
                hideReliefDetailsModal();
            }
        } catch (error) {
            console.error('Error loading relief request details:', error);
            showCustomNotification('خطأ', 'حدث خطأ أثناء تحميل تفاصيل الطلب (انظر وحدة التحكم)', 'error', 4000);
            hideReliefDetailsModal();
        } finally {
            isLoadingDetails = false;
        }
    }

    // Function to show relief details modal
    function showReliefDetailsModal(requestId, requestData, isLoading) {
        const modal = document.getElementById('relief-details-modal');
        const content = document.getElementById('relief-details-content');

        // If modal or content missing, create a basic fallback container
        if (!modal) {
            console.warn('relief-details-modal not found. Creating fallback modal.');
            const fallback = document.createElement('div');
            fallback.id = 'relief-details-modal';
            fallback.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            fallback.innerHTML = '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto"><div class="p-6"><div id="relief-details-content"></div></div></div>';
            document.body.appendChild(fallback);
        }

        const contentEl = document.getElementById('relief-details-content');
        if (!contentEl) {
            console.error('relief-details-content not found. Aborting showReliefDetailsModal.');
            return;
        }

        if (isLoading) {
            // Always rebuild loading content to ensure the element exists
            contentEl.innerHTML = '<div id="relief-loading" class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div><p class="text-gray-600 dark:text-gray-400">جاري تحميل التفاصيل...</p></div>';
            const loadingEl = document.getElementById('relief-loading');
            if (loadingEl) {
                loadingEl.style.display = 'block';
            }
        } else if (requestData) {
            const loadingEl = document.getElementById('relief-loading');
            if (loadingEl) loadingEl.style.display = 'none';

            const isOwnRequest = requestData.is_own_request;
            const statusBadge = requestData.status === 'approved' ?
                '<span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">موافق عليه</span>' :
                requestData.status === 'denied' ?
                '<span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">مرفوض</span>' :
                '<span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">معلق</span>';

            content.innerHTML = `
                <div class="space-y-4">
                    <!-- Request Status -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-gray-900 dark:text-white">حالة الطلب</h4>
                            ${statusBadge}
                        </div>
                        ${requestData.required_approvals ? `
                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">الموافقات:</span>
                                <span class="font-medium">${requestData.current_approvals || 0} / ${requestData.required_approvals}</span>
                            </div>
                        </div>
                        ` : ''}
                    </div>

                    <!-- Judge Information -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-3 flex items-center">
                            <i class="fas fa-user ms-2"></i>
                            معلومات المحكم
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div><span class="font-medium">الاسم:</span> ${requestData.judge_name}</div>
                            <div><span class="font-medium">معرف المحكم:</span> ${requestData.judge_id}</div>
                            <div><span class="font-medium">نوع الطلب:</span> ${isOwnRequest ? 'طلبك' : 'طلب من محكم آخر'}</div>
                        </div>
                    </div>

                    <!-- Participant Information -->
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                        <h4 class="font-semibold text-green-900 dark:text-green-300 mb-3 flex items-center">
                            <i class="fas fa-user-graduate ms-2"></i>
                            معلومات المتسابق
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            <div><span class="font-medium">اسم المتسابق:</span> ${requestData.participant_name}</div>
                            <div><span class="font-medium">معرف المتسابق:</span> ${requestData.participant_id}</div>
                        </div>
                    </div>

                    <!-- Request Details -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                        <h4 class="font-semibold text-yellow-900 dark:text-yellow-300 mb-3 flex items-center">
                            <i class="fas fa-file-alt ms-2"></i>
                            تفاصيل الطلب
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div><span class="font-medium">درجة التخفيف المطلوبة:</span>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">${requestData.grade}</span>
                            </div>
                            <div><span class="font-medium">تاريخ الطلب:</span> ${new Date(requestData.created_at).toLocaleString('ar-SA')}</div>
                            ${requestData.reason ? `
                                <div>
                                    <span class="font-medium">سبب الطلب:</span>
                                    <div class="mt-1 p-2 bg-white dark:bg-gray-800 rounded border text-gray-700 dark:text-gray-300">
                                        ${requestData.reason}
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>



                    ${requestData.approvals && requestData.approvals.length > 0 ? `
                        <!-- Approvals Information -->
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg">
                            <h4 class="font-semibold text-indigo-900 dark:text-indigo-300 mb-3 flex items-center">
                                <i class="fas fa-check-circle ms-2"></i>
                                الموافقات والرفض
                            </h4>
                            <div class="space-y-2">
                                ${requestData.approvals.map(approval => `
                                    <div class="flex items-center justify-between p-2 bg-white dark:bg-gray-800 rounded border">
                                        <div class="flex items-center gap-2">
                                            <i class="fas ${approval.status === 'approved' ? 'fa-check text-green-600' : 'fa-times text-red-600'}"></i>
                                            <span class="font-medium">${approval.judge_name}</span>
                                        </div>
                                        <span class="text-xs text-gray-500">${new Date(approval.updated_at).toLocaleString('ar-SA')}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        }

        const modalEl = document.getElementById('relief-details-modal');
        if (modalEl) modalEl.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Function to hide relief details modal
    function hideReliefDetailsModal() {
        const modal = document.getElementById('relief-details-modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Function to approve relief request
    async function approveReliefRequest(requestId) {
        try {
            // Optimistic UI: remove card immediately and show success toast
            const card = document.querySelector(`[data-request-id="${requestId}"]`) || null;
            if (card) {
                card.style.opacity = '0.5';
            }

            // Create AbortController for timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 15000); // 15 seconds timeout

            let response;
            try {
                response = await fetch('/api/relief-requests/approve', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ request_id: requestId }),
                    signal: controller.signal
                });
                clearTimeout(timeoutId);
            } catch (error) {
                clearTimeout(timeoutId);
                if (error.name === 'AbortError') {
                    showCustomNotification('خطأ في الاتصال', 'انتهت مهلة الاتصال. يرجى المحاولة مرة أخرى.', 'error', 3000);
                    if (card) card.style.opacity = '1';
                    return;
                }
                throw error;
            }

            if (!response.ok) {
                const errorText = await response.text();
                console.error('API Error Response:', response.status, response.statusText, errorText);
                throw new Error('فشل في الموافقة على الطلب');
            }

            const result = await response.json();

            if (result.success) {
                showCustomNotification('تمت الموافقة', 'تمت الموافقة على طلب التخفيف بنجاح', 'success', 2000);

                // Remove the card instantly
                if (card && card.parentNode) {
                    card.parentNode.removeChild(card);
                }

                // After approval, keep the button as "تم الطلب مسبقاً" for all judges
                const reliefBtn = document.getElementById('request-relief-btn');
                const reliefBtnText = document.getElementById('relief-btn-text');

                if (reliefBtn && reliefBtnText) {
                    // Keep button disabled and show "تم الطلب مسبقاً"
                    reliefBtn.disabled = true;
                    reliefBtn.classList.add('cursor-not-allowed');
                    reliefBtnText.textContent = 'تم الطلب مسبقاً';
                    reliefBtn.className = 'bg-yellow-500 text-white font-bold py-2 px-6 rounded-lg text-base cursor-not-allowed';
                    reliefBtn.style.pointerEvents = 'none';
                    reliefBtn.style.userSelect = 'none';

                    console.log('Button kept as "تم الطلب مسبقاً" after approval');
                }

                // Refresh list and re-check eligibility so all judges see updated state
                if (typeof loadPendingReliefRequests === 'function') {
                    setTimeout(() => {
                loadPendingReliefRequests();
                    }, 1000);
                }

                // Force real-time status check to update button for all members
                if (typeof checkReliefRequestStatusRealTime === 'function') {
                    setTimeout(() => {
                        checkReliefRequestStatusRealTime();
                    }, 500);
                }
            } else {
                showCustomNotification('فشل في الموافقة', result.message || 'حدث خطأ أثناء الموافقة على الطلب', 'error', 3000);
                // revert opacity if failed
                if (card) card.style.opacity = '1';
            }
        } catch (error) {
            console.error('Error approving relief request:', error);
            showCustomNotification('خطأ في الاتصال', 'حدث خطأ أثناء الاتصال بالخادم', 'error', 3000);
        }
    }

    // Function to deny relief request
    async function denyReliefRequest(requestId) {
        const abortController = new AbortController();
        const timeoutId = setTimeout(() => abortController.abort(), 15000); // 15 seconds timeout

        try {
            const card = document.querySelector(`[data-request-id="${requestId}"]`) || null;
            if (card) card.style.opacity = '0.5';

            console.log('[Relief Deny] Sending deny request', { requestId });
            const response = await fetch('/api/relief-requests/deny', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    request_id: requestId,
                    rejection_reason: 'رفض من لوحة التحكيم'
                }),
                signal: abortController.signal
            });

            clearTimeout(timeoutId);

            const result = await response.json();

            if (result.success) {
                showCustomNotification('تم الرفض', 'تم رفض طلب التخفيف بنجاح', 'success', 2000);

                // Remove the card instantly
                if (card && card.parentNode) {
                    card.parentNode.removeChild(card);
                }

                // After denial, reset button for all judges
                const reliefBtn = document.getElementById('request-relief-btn');
                const reliefBtnText = document.getElementById('relief-btn-text');

                if (reliefBtn && reliefBtnText) {
                    reliefBtn.disabled = false;
                    reliefBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    reliefBtnText.textContent = 'طلب تخفيف';
                    reliefBtn.className = 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50';
                    reliefBtn.style.pointerEvents = 'auto';
                    reliefBtn.style.userSelect = 'auto';

                    console.log('Button reset to initial state after denial');
                }

                if (typeof loadPendingReliefRequests === 'function') {
                    setTimeout(() => {
                loadPendingReliefRequests();
                    }, 1000);
                }

                // Force real-time status check to update button for all members
                if (typeof checkReliefRequestStatusRealTime === 'function') {
                    setTimeout(() => {
                        checkReliefRequestStatusRealTime();
                    }, 500);
                }
            } else {
                showCustomNotification('فشل في الرفض', result.message || 'حدث خطأ أثناء رفض الطلب', 'error', 3000);
                if (card) card.style.opacity = '1';
            }
        } catch (error) {
            clearTimeout(timeoutId);
            console.error('[Relief Deny] Error:', error);

            if (error.name === 'AbortError') {
                showCustomNotification('انتهت مهلة الاتصال', 'استغرق الطلب وقتاً طويلاً. يرجى المحاولة مرة أخرى.', 'error', 4000);
            } else {
                showCustomNotification('خطأ في الاتصال', 'حدث خطأ أثناء الاتصال بالخادم', 'error', 3000);
            }

            const card = document.querySelector(`[data-request-id="${requestId}"]`) || null;
            if (card) card.style.opacity = '1';
        }
    }

    // Function to check if user has approved or denied any relief requests
    async function checkIfUserHasApprovedOrDeniedRelief() {
        try {
            const competitionVersionBranchId = '{{ $competition_version_branch_id }}';
            const response = await fetch(`/api/relief-requests/check-approval-status?competition_version_branch_id=${competitionVersionBranchId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                const data = await response.json();
                return {
                    hasAction: data.has_approved_or_denied,
                    lastAction: data.last_action,
                    lastActionDate: data.last_action_date
                };
            } else {
                console.error('Failed to check approval status');
                return { hasAction: false };
            }
        } catch (error) {
            console.error('Error checking approval status:', error);
            return { hasAction: false };
        }
    }

    // Function to check if there's an existing relief request for this participant
    async function checkIfExistingReliefRequest() {
        try {
            const participantId = '{{ $participant_id }}';
            const competitionVersionBranchId = '{{ $competition_version_branch_id }}';
            const response = await fetch(`/api/relief-requests/check-existing?participant_id=${participantId}&competition_version_branch_id=${competitionVersionBranchId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                const data = await response.json();
                return data.has_existing;
            } else {
                console.error('Failed to check existing relief request');
                return false;
            }
        } catch (error) {
            console.error('Error checking existing relief request:', error);
            return false;
        }
    }

    // Function to attach relief button click listener
    function attachReliefButtonListener() {
            const reliefBtn = document.getElementById('request-relief-btn');
            const reliefBtnText = document.getElementById('relief-btn-text');

        if (reliefBtn && reliefBtnText) {
            // Remove any existing event listeners
            reliefBtn.removeEventListener('click', handleReliefButtonClick);

            // Add the click event listener
            reliefBtn.addEventListener('click', handleReliefButtonClick);

            console.log('Relief button click listener attached');
        }
    }

    // Function to handle relief button click
    async function handleReliefButtonClick() {
        const reliefBtn = document.getElementById('request-relief-btn');
        const reliefBtnText = document.getElementById('relief-btn-text');

        if (!reliefBtn || !reliefBtnText) return;

        // If a send is already in progress, ignore additional clicks
        if (reliefSendingInProgress) {
            console.log('[Relief Request] Send already in progress, ignoring click');
            return;
        }

        // Disable button immediately to prevent multiple clicks
        reliefBtn.disabled = true;
        reliefSendingInProgress = true;
        reliefLockedState = 'sending';
        // Mark as existing immediately to avoid realtime from flipping state back
        reliefRequestExists = true;

        // قراءة القيمة من السليكتور مباشرة إذا كان موجوداً، وإلا من hiddenInput
        const reliefGradeSelect = document.getElementById('relief-grade-select');
        const reliefGrade = reliefGradeSelect ? reliefGradeSelect.value : document.getElementById('relief-grade').value;
        const reliefReason = document.getElementById('relief-reason').value;

        console.log('[Relief Request] Grade value:', {
            fromSelect: reliefGradeSelect ? reliefGradeSelect.value : null,
            fromHidden: document.getElementById('relief-grade').value,
            finalValue: reliefGrade
        });

        if (!reliefGrade) {
                showCustomNotification(
                'درجة التخفيف غير محددة',
                'يرجى تحديد درجة التخفيف في إعدادات نموذج التحكيم أولاً',
                'warning',
                3000
            );
            // Reset button to initial state
            reliefBtnText.textContent = 'طلب تخفيف';
            reliefBtn.className = 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50';
            reliefBtn.disabled = false;
            reliefBtn.style.pointerEvents = 'auto';
            reliefBtn.style.userSelect = 'auto';

            // Reset sending state
            reliefSendingInProgress = false;
            reliefLockedState = null;
            return;
        }

        // Only check for existing pending requests
        const hasExistingRelief = await checkIfExistingReliefRequest();
        if (hasExistingRelief) {
            // Reset button to initial state
            reliefBtnText.textContent = 'طلب تخفيف';
            reliefBtn.className = 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50';
            reliefBtn.disabled = false;
            reliefBtn.style.pointerEvents = 'auto';
            reliefBtn.style.userSelect = 'auto';

            // Reset sending state
            reliefSendingInProgress = false;
            reliefLockedState = null;
            return;
        }

        // Show loading state
        reliefBtn.disabled = true;
        reliefBtnText.innerHTML = '<i class="fas fa-spinner fa-spin ms-2"></i> جاري الإرسال...';

        // Set sending state to prevent real-time updates from interfering
        reliefSendingInProgress = true;
        reliefLockedState = 'sending';

        // Change button appearance to show loading
        const originalText = reliefBtnText.textContent;
        reliefBtnText.textContent = 'جاري الإرسال...';
        reliefBtn.className = 'bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200';

        try {
            const requestData = {
                participant_id: '{{ $participant_id }}',
                judge_id: '{{ auth()->id() }}',
                competition_version_branch_id: '{{ $competition_version_branch_id }}',
                judging_form_setting_id: '{{ $judging_form_setting_id }}',
                grade: reliefGrade,
                reason: reliefReason || 'طلب تخفيف من المحكم أثناء تقييم التفسير',
                judge_name: '{{ auth()->user()->full_name ?? "المحكم الحالي" }}',
                participant_name: '{{ $participant_name ?? "متسابق" }}',
                _token: '{{ csrf_token() }}'
            };

            console.log('Sending relief request data:', requestData);

            // Create AbortController for timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 seconds timeout

            let response;
            try {
                response = await fetch('/api/relief-requests/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(requestData),
                    signal: controller.signal
                });
                clearTimeout(timeoutId);
            } catch (error) {
                clearTimeout(timeoutId);
                if (error.name === 'AbortError') {
                    throw new Error('انتهت مهلة الاتصال. يرجى المحاولة مرة أخرى.');
                }
                throw error;
            }

            if (!response.ok) {
                const errorText = await response.text();
                console.error('API Error Response:', response.status, response.statusText, errorText);
                throw new Error('فشل في إرسال الطلب');
            }

            const result = await response.json();
            console.log('API Response:', result);

            if (result.success) {
                // Show success as "already requested" like other fields
                reliefBtnText.textContent = 'تم الطلب مسبقاً';
                reliefBtn.className = 'bg-yellow-500 text-white font-bold py-2 px-6 rounded-lg text-base cursor-not-allowed';
                reliefBtn.disabled = true;
                reliefBtn.style.pointerEvents = 'none';
                reliefBtn.style.userSelect = 'none';

                // Mark that we found an existing request
                reliefRequestExists = true;
                reliefSendingInProgress = false; // Completed successfully; button is disabled now
                reliefLockedState = 'requested';

                // Show notification
                showCustomNotification(
                    'تم إرسال طلب التخفيف',
                    `تم إرسال طلب التخفيف بنجاح بدرجة ${reliefGrade}. سيتم إشعار أعضاء اللجنة للمراجعة.`,
                    'success',
                    5000
                );

                // Send notifications to committee members (non-blocking)
                const notificationData = {
                    request_id: result.request_id,
                    judge_name: '{{ auth()->user()->full_name ?? "محكم" }}',
                    participant_name: '{{ $participant_name ?? "متسابق" }}',
                    grade: reliefGrade,
                    reason: reliefReason || 'طلب تخفيف من المحكم أثناء تقييم التفسير',
                    competition_version_branch_id: '{{ $competition_version_branch_id }}',
                    judging_form_setting_id: '{{ $judging_form_setting_id }}',
                    _token: '{{ csrf_token() }}'
                };

                sendReliefRequestNotifications(notificationData).catch(err => {
                    console.error('Error sending notifications (non-blocking):', err);
                });

                // Store the success state to prevent re-sending (user-specific)
                const userId = '{{ auth()->id() }}';
                const participantId = '{{ $participant_id }}';
                const competitionBranchId = '{{ $competition_version_branch_id }}';
                const reliefKey = `relief-request-sent-${userId}-${participantId}-${competitionBranchId}`;
                localStorage.setItem(reliefKey, 'true');
                console.log('Saved relief request state for user:', userId, 'with key:', reliefKey);

                // Reload pending relief requests to show the new request immediately
                setTimeout(() => {
                    if (typeof loadPendingReliefRequests === 'function') {
                        loadPendingReliefRequests();
                    }
                }, 1000);

                // Re-check relief request eligibility to update button state
                if (typeof checkReliefRequestEligibility === 'function') {
                    setTimeout(() => {
                        checkReliefRequestEligibility();
                    }, 1500);
                }
            } else {
                console.error('API Error Response:', result);
                showCustomNotification(
                    'فشل في إرسال الطلب',
                    result.message || 'حدث خطأ أثناء إرسال طلب التخفيف',
                    'error',
                    5000
                );
                // Reset button after error
                reliefBtnText.textContent = 'فشل الإرسال ✗';
                reliefBtn.className = 'bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 animate-pulse';

                // Reset button after 3 seconds (only for errors)
                setTimeout(() => {
                    reliefBtnText.textContent = 'طلب تخفيف';
                    reliefBtn.className = 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50';
                    reliefBtn.disabled = false;
                    reliefBtn.style.pointerEvents = 'auto';
                    reliefBtn.style.userSelect = 'auto';
                    reliefSendingInProgress = false;
                    reliefLockedState = 'idle';
                    reliefRequestExists = false;
                }, 3000);
            }
        } catch (error) {
            console.error('Error sending relief request:', error);

            // Show error state on button
            reliefBtnText.textContent = 'فشل الإرسال ✗';
            reliefBtn.className = 'bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 animate-pulse';

            showCustomNotification(
                'خطأ في الإرسال',
                error.message || 'حدث خطأ أثناء إرسال طلب التخفيف. حاول مرة أخرى.',
                'error',
                5000
            );

            // Reset button after 3 seconds (only for errors)
            setTimeout(() => {
                reliefBtnText.textContent = 'طلب تخفيف';
                reliefBtn.className = 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50';
                reliefBtn.disabled = false;
                reliefBtn.style.pointerEvents = 'auto';
                reliefBtn.style.userSelect = 'auto';
                reliefSendingInProgress = false;
                reliefLockedState = 'idle';
                reliefRequestExists = false;
            }, 3000);
        }
    }

    // Function to reset relief button to initial state
    function resetReliefButtonToInitial() {
        const reliefBtn = document.getElementById('request-relief-btn');
        const reliefBtnText = document.getElementById('relief-btn-text');

        if (reliefBtn && reliefBtnText) {
            reliefBtn.disabled = false;
            reliefBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            reliefBtnText.textContent = 'طلب تخفيف';
            reliefBtn.className = 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50';
            reliefBtn.style.pointerEvents = 'auto';
            reliefBtn.style.userSelect = 'auto';

            // Re-attach the click event listener
            attachReliefButtonListener();

            console.log('Relief button reset to initial state and event listener re-attached');
        }
    }

    // Real-time relief request status checker FOR THIS SPECIFIC PARTICIPANT
    async function checkReliefRequestStatusRealTime() {
        try {
            const reliefBtn = document.getElementById('request-relief-btn');
            const reliefBtnText = document.getElementById('relief-btn-text');

            if (!reliefBtn || !reliefBtnText) {
                return; // Don't check if button elements don't exist
            }

            // Do not change UI while a send is in progress
            if (reliefSendingInProgress) {
                return;
            }

            // If UI is in locked 'sending' state, do not flip it
            if (reliefLockedState === 'sending') {
                return;
            }

            const participantId = '{{ $participant_id }}';
            const competitionVersionBranchId = '{{ $competition_version_branch_id }}';
            const fieldType = '{{ $judgingFormSetting->field ?? "interpretation" }}'; // Get field for multi-field relief check

            // Check relief request status for THIS SPECIFIC participant in THIS SPECIFIC branch and THIS SPECIFIC field
            const response = await fetch(`/api/relief-requests/check-status?participant_id=${participantId}&competition_version_branch_id=${competitionVersionBranchId}&field_type=${fieldType}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                const data = await response.json();
                if (data.status === 'pending' && !reliefBtn.disabled) {
                    // Update button for THIS participant only
                    reliefBtnText.textContent = 'تم الطلب مسبقاً';
                    reliefBtn.className = 'bg-yellow-500 text-white font-bold py-2 px-6 rounded-lg text-base cursor-not-allowed';
                    reliefBtn.disabled = true;
                    reliefBtn.style.pointerEvents = 'none';
                    reliefBtn.style.userSelect = 'none';

                    // Mark that we found an existing request to avoid future checks
                    reliefRequestExists = true;

                    console.log('Real-time update: Relief PENDING for participant', participantId, 'in branch', competitionVersionBranchId);
                } else if (data.status === 'approved') {
                    // Update button to show "تمت الموافقة" for THIS participant only
                    reliefBtnText.textContent = 'تمت الموافقة';
                    reliefBtn.className = 'bg-green-600 text-white font-bold py-2 px-6 rounded-lg text-base cursor-not-allowed';
                    reliefBtn.disabled = true;
                    reliefBtn.style.pointerEvents = 'none';
                    reliefBtn.style.userSelect = 'none';

                    // Mark that we found an approved request
                    reliefRequestExists = true;

                    console.log('Real-time update: Relief APPROVED for participant', participantId, 'in branch', competitionVersionBranchId);
                } else if ((data.status === 'denied' || data.status === null) && (reliefBtn.disabled || reliefRequestExists) && reliefLockedState !== 'sending') {
                    // If request was denied or no request exists for THIS participant, re-enable the button
                    resetReliefButtonToInitial();
                    console.log('Real-time update: No relief for participant', participantId, 'in branch', competitionVersionBranchId);
                }
            }
        } catch (error) {
            console.error('Error checking relief request status in real-time:', error);
        }
    }

    // Function to check if user can send relief requests
    async function checkReliefRequestEligibility() {
        try {
            const participantId = '{{ $participant_id }}';
            const competitionVersionBranchId = '{{ $competition_version_branch_id }}';
            const reliefBtn = document.getElementById('request-relief-btn');
            const reliefBtnText = document.getElementById('relief-btn-text');

            // Determine field type for multi-field committees
            const judgingFormSettingId = '{{ $judging_form_setting_id ?? '' }}';
            let fieldType = '{{ $judgingFormSetting->field ?? "interpretation" }}'; // Get from server

            // Check relief request status (pending, approved, denied)
            // Include field_type for multi-field committees to get field-specific relief status
            const response = await fetch(`/api/relief-requests/check-status?participant_id=${participantId}&competition_version_branch_id=${competitionVersionBranchId}&field_type=${fieldType}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                const data = await response.json();
                if (data.status === 'pending' && reliefBtn && reliefBtnText) {
                    // Disable button for THIS participant only (pending)
                    reliefBtn.disabled = true;
                    reliefBtn.classList.add('cursor-not-allowed');
                    reliefBtnText.textContent = 'تم الطلب مسبقاً';
                    reliefBtn.className = 'bg-yellow-500 text-white font-bold py-2 px-6 rounded-lg text-base cursor-not-allowed';
                    reliefBtn.style.pointerEvents = 'none';
                    reliefBtn.style.userSelect = 'none';
                    reliefRequestExists = true;
                    console.log('Relief eligibility: PENDING for participant', participantId, 'in branch', competitionVersionBranchId);
                } else if (data.status === 'approved' && reliefBtn && reliefBtnText) {
                    // Show "تمت الموافقة" for THIS participant only (approved)
                    reliefBtn.disabled = true;
                    reliefBtn.classList.add('cursor-not-allowed');
                    reliefBtnText.textContent = 'تمت الموافقة';
                    reliefBtn.className = 'bg-green-600 text-white font-bold py-2 px-6 rounded-lg text-base cursor-not-allowed';
                    reliefBtn.style.pointerEvents = 'none';
                    reliefBtn.style.userSelect = 'none';
                    reliefRequestExists = true;
                    console.log('Relief eligibility: APPROVED for participant', participantId, 'in branch', competitionVersionBranchId);
                } else if ((data.status === 'denied' || data.status === null)) {
                    // Enable the relief request button (no existing request or denied)
                    resetReliefButtonToInitial();
                    console.log('Relief button enabled - no existing request found or request was denied');
                }
            } else {
                // Fallback: check using old method
                const hasExisting = await checkIfExistingReliefRequest();
                if (hasExisting && reliefBtn && reliefBtnText) {
                    reliefBtn.disabled = true;
                    reliefBtn.classList.add('cursor-not-allowed');
                    reliefBtnText.textContent = 'تم الطلب مسبقاً';
                    reliefBtn.className = 'bg-yellow-500 text-white font-bold py-2 px-6 rounded-lg text-base cursor-not-allowed';
                    reliefBtn.style.pointerEvents = 'none';
                    reliefBtn.style.userSelect = 'none';
                } else {
                    resetReliefButtonToInitial();
                }
            }
        } catch (error) {
            console.error('Error checking relief request eligibility:', error);
        }
    }

    // Global variables for real-time relief status tracking (like Quran page)
    let reliefSendingInProgress = false;
    let reliefLockedState = null; // 'sending', 'approved', or null
    let reliefRequestExists = false;

    // Custom notification function
    function showCustomNotification(title, message, type = 'info', duration = 3000) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border-l-4 p-4 transform transition-all duration-300 ${type === 'success' ? 'border-green-500' :
                type === 'error' ? 'border-red-500' :
                    type === 'warning' ? 'border-yellow-500' : 'border-blue-500'
        }`;

        const iconClass = type === 'success' ? 'fa-check-circle text-green-500' :
            type === 'error' ? 'fa-exclamation-circle text-red-500' :
                type === 'warning' ? 'fa-exclamation-triangle text-yellow-500' :
                    'fa-info-circle text-blue-500';

        notification.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas ${iconClass} text-xl"></i>
            </div>
            <div class="me-3 w-0 flex-1">
                <p class="text-sm font-medium text-gray-900 dark:text-white">${title}</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">${message}</p>
            </div>
            <div class="flex-shrink-0">
                <button class="bg-white dark:bg-gray-800 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none" onclick="this.parentElement.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;

        document.body.appendChild(notification);

        // Auto remove after duration
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, duration);
    }

    // Notification function
    function showNotification(type, message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;

        notification.textContent = message;
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);

        // Remove after delay
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';

            // Remove from DOM after animation
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Cache for relief settings to avoid multiple requests
    let reliefSettingsCache = null;

    // Function to load relief settings from competition branch
    async function loadReliefSettings() {
        try {
            // Check cache first
            if (reliefSettingsCache) {
                document.getElementById('relief-grade').value = reliefSettingsCache.relief_grade;
                document.getElementById('relief-grade-text').textContent = `${reliefSettingsCache.relief_grade}`;
                return;
            }

            const competitionBranchId = new URLSearchParams(window.location.search).get('competition_version_branch_id');

            if (!competitionBranchId) {
                console.error('No competition branch ID found');
                document.getElementById('relief-grade-text').textContent = 'غير محدد';
                return;
            }

            // Determine the field type from the URL or page context
            let fieldType = 'interpretation'; // Default for tafseer page
            if (window.location.pathname.includes('quran')) {
                fieldType = 'quran';
            } else if (window.location.pathname.includes('hadith')) {
                fieldType = 'hadith';
            } else if (window.location.pathname.includes('dirayah')) {
                fieldType = 'dirayah';
            }

            const judgingFormSettingId = '{{ $judging_form_setting_id ?? '' }}';
            const qs = new URLSearchParams({
                field: fieldType,
                judging_form_setting_id: judgingFormSettingId || ''
            }).toString();
            const response = await fetch(`/api/competition-branch/${competitionBranchId}/relief-settings?${qs}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && data.relief_grade) {
                    // Cache the settings
                    reliefSettingsCache = {
                        relief_grade: data.relief_grade,
                        required_approvals: data.required_approvals_for_relief
                    };

                    const maxGrade = parseInt(data.relief_grade || 0, 10);
                    const hiddenInput = document.getElementById('relief-grade');
                    const displayBox = document.getElementById('relief-grade-display');
                    const textSpan = document.getElementById('relief-grade-text');

                    if (Number.isFinite(maxGrade) && maxGrade > 10) {
                        // حفظ القيمة المختارة من المستخدم قبل إعادة إنشاء السليكتور
                        const existingSelect = document.getElementById('relief-grade-select');
                        let savedUserValue = null;
                        if (existingSelect) {
                            // حفظ القيمة المختارة من السليكتور نفسه
                            const currentSelectValue = existingSelect.value;
                            if (currentSelectValue && currentSelectValue !== String(maxGrade)) {
                                // إذا كانت القيمة المختارة مختلفة عن القيمة الافتراضية من الاستمارة
                                savedUserValue = currentSelectValue;
                            } else if (hiddenInput && hiddenInput.value && hiddenInput.value !== String(maxGrade)) {
                                // أو من hiddenInput إذا كان مختلفاً
                                savedUserValue = hiddenInput.value;
                            }
                        }

                        // بناء سيلكتور 10..maxGrade بخطوة 10 (مطابق للقرآن)
                        const selectId = 'relief-grade-select';
                        const select = document.createElement('select');
                        select.id = selectId;
                        select.className = 'w-full rounded-lg text-base px-4 py-2.5 bg-gradient-to-r from-green-50 to-green-100 dark:from-gray-700 dark:to-gray-800 border-0 text-green-700 dark:text-green-300 font-bold focus:ring-2 focus:ring-green-500 focus:outline-none cursor-pointer transition-all duration-200 hover:shadow-md';

                        for (let v = 10; v <= maxGrade; v += 10) {
                            const opt = document.createElement('option');
                            opt.value = String(v);
                            opt.textContent = String(v);
                            // لا نعين selected تلقائياً للدرجة المحددة في الاستمارة
                            // بدلاً من ذلك، نستخدم القيمة المحفوظة من المستخدم أو القيمة الافتراضية
                            if (savedUserValue && v === parseInt(savedUserValue, 10)) {
                                opt.selected = true;
                            } else if (!savedUserValue && v === maxGrade) {
                                // فقط إذا لم يكن هناك قيمة محفوظة من المستخدم
                                opt.selected = true;
                            }
                            select.appendChild(opt);
                        }

                        if (displayBox) {
                            displayBox.innerHTML = '';
                            displayBox.classList.remove('bg-gray-100', 'dark:bg-gray-700', 'h-10');
                            displayBox.classList.add('bg-transparent');
                            displayBox.appendChild(select);
                        }

                        // تعيين القيمة في hiddenInput: القيمة المحفوظة من المستخدم أو القيمة الافتراضية
                        if (hiddenInput) {
                            hiddenInput.value = savedUserValue || String(maxGrade);
                        }

                        // تعيين القيمة في السليكتور لتتطابق مع hiddenInput
                        if (savedUserValue) {
                            select.value = savedUserValue;
                        }

                        select.addEventListener('change', () => {
                            if (hiddenInput) {
                                hiddenInput.value = select.value;
                                console.log('[Relief Grade Select] Value changed:', {
                                    selectValue: select.value,
                                    hiddenInputValue: hiddenInput.value
                                });
                            }
                        });
                    } else {
                        if (hiddenInput) hiddenInput.value = String(maxGrade || 10);
                        if (textSpan) textSpan.textContent = String(maxGrade || 10);
                    }
                } else {
                    document.getElementById('relief-grade-text').textContent = 'غير محدد في الإعدادات';
                }
            } else {
                console.error('Failed to load relief settings');
                document.getElementById('relief-grade-text').textContent = 'خطأ في التحميل';
            }
        } catch (error) {
            console.error('Error loading relief settings:', error);
            document.getElementById('relief-grade-text').textContent = 'خطأ في التحميل';
        }
    }

    // Load relief settings when page loads
    document.addEventListener('DOMContentLoaded', function () {
        loadReliefSettings();
    });
</script>

<script>
    // 2. تحديث المجموع النهائي في الهيدر (جمع درجات كل الأسئلة)
    function updateFinalScoreDisplayTafseer() {
        const finalScoreElement = document.getElementById('final-score-display');
        if (!finalScoreElement) return;
    
        const maxScorePerQuestion = parseFloat("{{ $gradeQuestion }}") || 0;
        let totalEarned = 0;
    
        for (let i = 0; i < window.totalQuestions; i++) {
            const ans = window.allAnswers[i];
            if (ans && ans.score !== undefined && ans.score !== null) {
                // إذا كان السؤال مقيماً، نجمع درجته (مثلاً 8)
                totalEarned += parseFloat(ans.score);
            } else {
                // إذا لم يتم تقييمه بعد، نفترض الدرجة الكاملة (10)
                totalEarned += maxScorePerQuestion;
            }
        }
        finalScoreElement.textContent = totalEarned.toFixed(1);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // استدعاء أولي
        updateFinalScoreDisplayTafseer();
        // تهيئة سيلكتور المتسابق على نفس نمط القران (select2 إذا متاح)
        try {
            const sel = document.getElementById('participant-select');
            const hasSelect2 = () => window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.select2 === 'function';
            if (sel && hasSelect2()) {
                const $ = window.jQuery;
                $(sel).select2({
                    placeholder: 'اختر متسابقاً',
                    allowClear: false,
                    width: '100%',
                    language: {
                        noResults: function () { return 'لا توجد نتائج'; },
                        searching: function () { return 'جاري البحث...'; }
                    },
                });
            }
        } catch (_) {}
        // حدّث عند تغيّر المدخلات ذات الصلة
        document.querySelectorAll('[name="alert_before_fat7"], [name="fat7_points"], [name="score"]').forEach(input => {
            input.addEventListener('input', updateFinalScoreDisplayTafseer);
            input.addEventListener('change', updateFinalScoreDisplayTafseer);
        });
        // اجعلها متاحة عالمياً
        window.updateFinalScoreDisplayTafseer = updateFinalScoreDisplayTafseer;

        // تم إزالة فرض الدرجة الموحدة - الآن كل سؤال له درجة منفصلة
        // يتم تحميل الدرجة في loadQuestionData بناءً على البيانات المحفوظة لكل سؤال
        // event listener للنقر على عنصر السؤال يتم التعامل معه في footer.blade.php
        // تهيئة نظام الملاحظات (مطابق للقرآن)
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
    });
</script>
<script>
    // Relief Modal functions (Nafes Design)
    function toggleReliefBox() {
        const modal = document.getElementById('relief-modal');
        const content = document.getElementById('relief-modal-content');
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                content.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
                content.classList.add('opacity-100', 'translate-y-0', 'scale-100');
            }, 10);
        } else {
            content.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
            content.classList.add('opacity-0', 'translate-y-4', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 300);
        }
    }

    window.toggleReliefGradeOptions = function() {
        const options = document.getElementById('relief-grade-options');
        const chevron = document.getElementById('relief-grade-chevron');
        if (options.classList.contains('hidden')) {
            options.classList.remove('hidden');
            chevron.classList.add('rotate-180');
            const handler = (e) => {
                if (!document.getElementById('relief-grade-select-container').contains(e.target)) {
                    options.classList.add('hidden');
                    chevron.classList.remove('rotate-180');
                    document.removeEventListener('click', handler);
                }
            };
            setTimeout(() => document.addEventListener('click', handler), 10);
        } else {
            options.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }

    window.selectReliefGrade = function(grade) {
        document.getElementById('selected-relief-grade-text').textContent = grade;
        document.getElementById('relief-grade').value = grade.replace('%', '');
        window.toggleReliefGradeOptions();
        const btn = document.getElementById('request-relief-submit-btn');
        if (btn) {
            btn.classList.add('bg-yellow-50', 'border-yellow-200');
        }
    }

    // Close modal on overlay click
    document.addEventListener('DOMContentLoaded', () => {
        const reliefModal = document.getElementById('relief-modal');
        if (reliefModal) {
            reliefModal.addEventListener('click', function(e) {
                if (e.target === reliefModal) toggleReliefBox();
            });
        }
    });
</script>
<script>
    // Notes system (matching Quran page)
    const NOTES_SOURCE = @json($notes->map(function ($note) {
        return ['id' => $note->id, 'text' => $note->note];
    })->values());

    function normalizeNoteText(text) {
        return (text || '').trim();
    }
    function findNoteById(noteId) {
        if (!noteId && noteId !== 0) return null;
        const numericId = Number(noteId);
        return NOTES_SOURCE.find(note => Number(note.id) === numericId) || null;
    }
    function findNoteByText(noteText) {
        const normalized = normalizeNoteText(noteText).toLowerCase();
        if (!normalized) return null;
        return NOTES_SOURCE.find(note => (note.text || '').trim().toLowerCase() === normalized) || null;
    }

    // Define updateSelectedNotesDisplay globally before initializeNoteInput
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
            firstChild: displayDiv.firstElementChild ? 'exists' : 'none',
            containerVisible: container.style.display !== 'none'
        });
    }

    // Make updateSelectedNotesDisplay globally accessible
    window.updateSelectedNotesDisplay = updateSelectedNotesDisplay;

    function initializeNoteInput() {
        if (window.noteInputInitialized) return;

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
        }

        function updateNotesFromSelect2() {
            if (!hasSelect2Support()) {
                console.warn('[Tafseer Notes Update] ⚠️ Select2 not supported, skipping updateNotesFromSelect2');
                return;
            }

            const $select = window.jQuery(unifiedNoteSelect);
            const selectedIds = $select.val() || [];
            const selectedTexts = selectedIds.map(id => {
                const note = findNoteById(id);
                return note ? note.text : '';
            });

            console.log('[Tafseer Notes Update] 📝 updateNotesFromSelect2 called:', {
                selectedIds: selectedIds,
                selectedTexts: selectedTexts,
                idsLength: selectedIds.length,
                textsLength: selectedTexts.length
            });

            updateNotesFields(selectedIds, selectedTexts);
            updateSelectedNotesDisplay(selectedIds, selectedTexts);

            // IMPORTANT: Save after updating fields to ensure notes are persisted
            console.log('[Tafseer Notes Update] 💾 Triggering saveCurrentAnswer after fields update');

            // Save directly to localStorage immediately as backup
            try {
                const currentIndex = typeof window.currentIndex !== 'undefined' ? window.currentIndex : 0;
                const participantId = '{{ $participant_id }}';
                const storageKey = `judging-tafseer-data-${participantId}-${currentIndex}`;

                // Get current data from localStorage or create new
                let currentData = {};
                const existingData = localStorage.getItem(storageKey);
                if (existingData) {
                    try {
                        currentData = JSON.parse(existingData);
                    } catch (e) {
                        console.warn('[Tafseer Notes Update] Failed to parse existing localStorage data:', e);
                    }
                }

                // Update with new notes
                currentData.note_ids = selectedIds;
                currentData.note_texts = selectedTexts;

                // Also get question_id from form if available
                const currentForm = document.getElementById('current-answer-form');
                if (currentForm && currentForm.dataset.questionId) {
                    currentData.question_id = currentForm.dataset.questionId;
                }

                // Save to localStorage immediately
                localStorage.setItem(storageKey, JSON.stringify(currentData));
                console.log('[Tafseer Notes Update] 💾 Saved notes directly to localStorage:', {
                    key: storageKey,
                    noteIds: selectedIds,
                    noteTexts: selectedTexts
                });

                // Also update window.allAnswers if it exists
                if (typeof window.allAnswers !== 'undefined' && Array.isArray(window.allAnswers)) {
                    if (!window.allAnswers[currentIndex]) {
                        window.allAnswers[currentIndex] = {};
                    }
                    window.allAnswers[currentIndex].note_ids = selectedIds;
                    window.allAnswers[currentIndex].note_texts = selectedTexts;
                    if (currentData.question_id) {
                        window.allAnswers[currentIndex].question_id = currentData.question_id;
                    }
                    console.log('[Tafseer Notes Update] 💾 Updated window.allAnswers:', window.allAnswers[currentIndex]);
                }
            } catch (e) {
                console.error('[Tafseer Notes Update] ❌ Error saving to localStorage:', e);
            }

            const attemptSave = (retryCount = 0) => {
                const maxRetries = 20; // Increased retries to wait for footer.blade.php
                if (typeof window.saveCurrentAnswer === 'function') {
                    console.log('[Tafseer Notes Update] ✅ Calling window.saveCurrentAnswer');
                    try {
                        window.saveCurrentAnswer();
                    } catch (e) {
                        console.error('[Tafseer Notes Update] ❌ Error calling saveCurrentAnswer:', e);
                    }
                } else if (typeof saveCurrentAnswer === 'function') {
                    console.log('[Tafseer Notes Update] ✅ Calling saveCurrentAnswer (local)');
                    try {
                        saveCurrentAnswer();
                    } catch (e) {
                        console.error('[Tafseer Notes Update] ❌ Error calling saveCurrentAnswer (local):', e);
                    }
                } else if (retryCount < maxRetries) {
                    console.log(`[Tafseer Notes Update] ⏳ saveCurrentAnswer not available yet, retrying (${retryCount + 1}/${maxRetries})...`);
                    setTimeout(() => attemptSave(retryCount + 1), 300);
                } else {
                    console.warn('[Tafseer Notes Update] ⚠️ saveCurrentAnswer function not available after retries. Direct localStorage save already completed.');
                    // Last resort: try to save directly
                    try {
                        if (typeof window.saveCurrentAnswerForIndex === 'function' && typeof window.currentIndex !== 'undefined') {
                            console.log('[Tafseer Notes Update] 🔧 Using saveCurrentAnswerForIndex directly');
                            window.saveCurrentAnswerForIndex(window.currentIndex);
                        }
                    } catch (e) {
                        console.error('[Tafseer Notes Update] ❌ Error in direct save:', e);
                    }
                }
            };
            setTimeout(() => attemptSave(), 100);
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
                    updateSelectedNotesDisplay(currentIds, currentTexts);

                    // IMPORTANT: Save after selecting existing note - use updateNotesFromSelect2 which handles saving
                    console.log('[Tafseer Create Note] 💾 Triggering updateNotesFromSelect2 for saving');
                    updateNotesFromSelect2(); // This will handle saving

                    if (hasSelect2Support()) {
                        const $select = window.jQuery(unifiedNoteSelect);
                        $select.val(currentIds).trigger('change');
                    } else {
                        const option = unifiedNoteSelect.querySelector(`option[value="${existing.id}"]`);
                        if (option) option.selected = true;
                    }
                }

                showCustomNotification('معلومة', 'الملاحظة موجودة بالفعل، تم اختيارها تلقائياً', 'info', 2500);
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
                    updateSelectedNotesDisplay(currentIds, currentTexts);

                    // IMPORTANT: Save after creating and selecting new note - use updateNotesFromSelect2 which handles saving
                    console.log('[Tafseer Create Note] 💾 Triggering updateNotesFromSelect2 for saving');
                    updateNotesFromSelect2(); // This will handle saving

                    if (hasSelect2Support()) {
                        const $select = window.jQuery(unifiedNoteSelect);
                        $select.prop('disabled', false);
                        $select.val(currentIds).trigger('change');
                    } else {
                        const option = unifiedNoteSelect.querySelector(`option[value="${newNote.id}"]`);
                        if (option) option.selected = true;
                    }

                    showCustomNotification('تم الحفظ', 'تمت إضافة الملاحظة الجديدة بنجاح', 'success', 2500);
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

            // Store jQuery reference for use in callbacks
            const jQueryRef = window.jQuery;

            $unifiedSelect.select2({
                tags: true,
                multiple: true,
                placeholder: unifiedNoteSelect.dataset.placeholder || 'ابحث عن ملاحظات أو اكتب جديدة...',
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
                    const term = jQueryRef.trim(params.term);
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
                        return jQueryRef('<div class="d-flex align-items-center justify-content-end" style="direction: rtl;"><i class="fas fa-plus me-2" style="color: #059669; font-size: 1.1em; text-shadow: 0 0 8px rgba(5, 150, 105, 0.4);"></i><span class="add-text" style="color: #059669 !important; font-weight: 700;">إضافة:</span> <span style="font-weight: 500;">' + data.text + '</span></div>');
                    }

                    return jQueryRef('<div class="d-flex align-items-center justify-content-end" style="direction: rtl;"><i class="fas fa-comment-dots me-2" style="color: #64748b;"></i>' + data.text + '</div>');
                },
                templateSelection: function(data) {
                    if (data.newTag) {
                        return jQueryRef('<span class="d-flex align-items-center justify-content-end"><i class="fas fa-plus me-2" style="color: #059669; font-size: 1.1em;"></i><span style="color: #059669; font-weight: 600;">' + data.text + '</span></span>');
                    }
                    return jQueryRef('<span class="d-flex align-items-center justify-content-end"><i class="fas fa-sticky-note me-2" style="color: #3b82f6;"></i>' + data.text + '</span>');
                },
                dropdownParent: $unifiedSelect.closest('.note-combo')
            });

            $unifiedSelect.on('select2:select', async function (e) {
                console.log('[Tafseer Select2] 🎯 select2:select event fired:', {
                    data: e.params.data,
                    isNewTag: e.params.data.newTag
                });

                const data = e.params.data;

                if (data.newTag) {
                    const noteText = data.text;
                    console.log('[Tafseer Select2] ➕ Creating new note:', noteText);
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
                            updateSelectedNotesDisplay(currentIds, currentTexts);
                            $unifiedSelect.val(currentIds).trigger('change');
                        }
                    } else {
                        // New note created successfully - update and save
                        console.log('[Tafseer Select2] ✅ New note created, updating fields');
                        updateNotesFromSelect2();
                    }
                } else {
                    // Existing note selected - update fields with all selected notes
                    console.log('[Tafseer Select2] 📝 Existing note selected, calling updateNotesFromSelect2');
                    updateNotesFromSelect2();
                }
            });

            $unifiedSelect.on('select2:unselect', function (e) {
                console.log('[Tafseer Select2] 🗑️ select2:unselect event fired:', {
                    data: e.params.data
                });
                // Note unselected - update fields
                updateNotesFromSelect2();
            });

            $unifiedSelect.on('select2:clear', function () {
                console.log('[Tafseer Select2] 🧹 select2:clear event fired');
                updateNotesFields([], []);
                updateSelectedNotesDisplay([], []);
                // Save will be called by updateNotesFromSelect2 if it's used
                // But for clear, we need to save explicitly
                setTimeout(() => {
                    if (typeof window.saveCurrentAnswer === 'function') {
                        console.log('[Tafseer Select2] 💾 Calling saveCurrentAnswer after clear');
                        window.saveCurrentAnswer();
                    }
                }, 100);
            });

            // Load saved notes after Select2 is initialized
            setTimeout(() => {
                if (typeof loadSavedNotes === 'function') {
                    console.log('[Select2 Init] Loading saved notes after Select2 initialization');
                    loadSavedNotes(0);
                }
            }, 50);

        } else {
            // Fallback for browsers without Select2
            unifiedNoteSelect.addEventListener('change', function() {
                const selectedOptions = Array.from(unifiedNoteSelect.selectedOptions);
                const selectedIds = selectedOptions.map(opt => opt.value);
                const selectedTexts = selectedOptions.map(opt => {
                    const note = findNoteById(opt.value);
                    return note ? note.text : opt.text;
                });

                updateNotesFields(selectedIds, selectedTexts);
                updateSelectedNotesDisplay(selectedIds, selectedTexts);

                if (typeof saveCurrentAnswer === 'function') {
                    saveCurrentAnswer();
                }
            });
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
                updateSelectedNotesDisplay(currentIds, currentTexts);

                if (hasSelect2Support()) {
                    const $select = window.jQuery(unifiedNoteSelect);
                    $select.val(currentIds).trigger('change');
                } else {
                    const option = unifiedNoteSelect.querySelector(`option[value="${noteId}"]`);
                    if (option) option.selected = false;
                    unifiedNoteSelect.dispatchEvent(new Event('change'));
                }

                if (typeof saveCurrentAnswer === 'function') {
                    saveCurrentAnswer();
                }
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
                hasData: typeof window.allAnswers !== 'undefined' && Array.isArray(window.allAnswers) && window.allAnswers[questionIndex] ? 'yes' : 'no',
                questionIndex: questionIndex
            });

            // Try to get data from allAnswers first
            if (typeof window.allAnswers !== 'undefined' && Array.isArray(window.allAnswers) && window.allAnswers.length > questionIndex) {
                const savedData = window.allAnswers[questionIndex];
                console.log('[Notes] 📦 Saved data from allAnswers:', savedData);

                if (savedData && savedData !== null && typeof savedData === 'object' && (savedData.note_ids || savedData.note_texts)) {
                    try {
                        // Handle both array and JSON string formats
                        if (savedData.note_ids) {
                            if (Array.isArray(savedData.note_ids)) {
                                noteIds = savedData.note_ids;
                            } else if (typeof savedData.note_ids === 'string') {
                                noteIds = JSON.parse(savedData.note_ids || '[]');
                            }
                        }

                        if (savedData.note_texts) {
                            if (Array.isArray(savedData.note_texts)) {
                                noteTexts = savedData.note_texts;
                            } else if (typeof savedData.note_texts === 'string') {
                                noteTexts = JSON.parse(savedData.note_texts || '[]');
                            }
                        }

                        // Ensure arrays are valid
                        if (!Array.isArray(noteIds)) noteIds = [];
                        if (!Array.isArray(noteTexts)) noteTexts = [];

                        // Only mark as loaded if we actually have data
                        if (noteIds.length > 0 || noteTexts.length > 0) {
                            loaded = true;
                            source = 'allAnswers';
                            console.log('[Notes] ✅ Loaded from allAnswers:', {
                                noteIds,
                                noteTexts,
                                noteIdsLength: noteIds.length,
                                noteTextsLength: noteTexts.length
                            });
                        } else {
                            console.log('[Notes] ⚠️ allAnswers data exists but arrays are empty');
                        }
                    } catch (e) {
                        console.error('[Notes] ❌ Error parsing allAnswers data:', e, savedData);
                    }
                } else if (savedData && savedData !== null) {
                    console.log('[Notes] ⚠️ allAnswers data exists but no note_ids or note_texts:', {
                        hasNoteIds: !!savedData?.note_ids,
                        hasNoteTexts: !!savedData?.note_texts,
                        savedDataKeys: savedData ? Object.keys(savedData) : []
                    });
                } else {
                    console.log('[Notes] ⚠️ allAnswers[' + questionIndex + '] is null or undefined');
                }
            } else {
                console.log('[Notes] ⚠️ allAnswers not ready yet or questionIndex out of bounds:', {
                    exists: typeof window.allAnswers !== 'undefined',
                    isArray: typeof window.allAnswers !== 'undefined' && Array.isArray(window.allAnswers),
                    length: typeof window.allAnswers !== 'undefined' && Array.isArray(window.allAnswers) ? window.allAnswers.length : 0,
                    questionIndex: questionIndex
                });
            }

            // Priority 2: Try loading from localStorage if not loaded yet
            if (!loaded) {
                const participantId = '{{ $participant_id }}';
                const storageKey = `judging-tafseer-data-${participantId}-${questionIndex}`;

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
                        exists: typeof updateSelectedNotesDisplay === 'function'
                    });

                    if (typeof updateSelectedNotesDisplay === 'function') {
                        try {
                            console.log('[Notes] 🎨 Calling updateSelectedNotesDisplay with:', { noteIds, noteTexts });
                            updateSelectedNotesDisplay(noteIds, noteTexts);

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
                    if (typeof updateSelectedNotesDisplay === 'function') {
                        updateSelectedNotesDisplay([], []);
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

    // Make loadSavedNotes and updateSelectedNotesDisplay globally accessible
    window.loadSavedNotes = loadSavedNotes;
    window.updateSelectedNotesDisplay = updateSelectedNotesDisplay;

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

    // نظام درجة تفسير "موحّدة" عبر كل الأسئلة مع تخزين محلي
    const TAFSEER_TOTAL_SCORE = {{ $totalScore ?? 0 }};
    const TAFSEER_QUESTIONS_COUNT = {{ $questionsCount ?? (isset($InterpretationQuestion) ? count($InterpretationQuestion) : 0) }};
    const TAFSEER_POSSIBLE_PER_QUESTION = {{ $gradeQuestion ?? 0 }};

    // ═══════════════════════════════════════════════════════════════
    // نظام إظهار الأسئلة - مطابق للقرآن
    // ═══════════════════════════════════════════════════════════════

    // IS_HEAD is already defined globally at the top of the script
    const BRANCH_ID = {{ (int) ($competition_version_branch_id ?? 0) }};
    const PARTICIPATION_ID = {{ (int) ($participant_id ?? 0) }};
    const FIELD_TYPE = '{{ $type ?? "interpretation" }}';
    let revealedQuestionIds = @json(($revealedQuestionIds ?? collect())->values());
    let isRevealing = false;
    
    // حفظ القيمة الأولية لاستخدامها في وضع التعديل
    const initialRevealedQuestionIds = [...revealedQuestionIds];
    const isEditMode = {{ isset($isEditMode) && $isEditMode ? 'true' : 'false' }};

    // Make variables globally accessible
    window.revealedQuestionIds = revealedQuestionIds;
    window.initialRevealedQuestionIds = initialRevealedQuestionIds; // حفظ القيمة الأولية
    window.IS_HEAD = IS_HEAD;
    window.BRANCH_ID = BRANCH_ID;
    window.PARTICIPATION_ID = PARTICIPATION_ID;
    window.FIELD_TYPE = FIELD_TYPE;
    window.isRevealing = isRevealing;

    console.log('[Tafseer Reveal] Init', { 
        IS_HEAD, 
        revealedQuestionIds, 
        initialRevealedQuestionIds,
        isEditMode,
        BRANCH_ID, 
        PARTICIPATION_ID, 
        FIELD_TYPE 
    });

    // Reveal button for head: inject in question header (initial load)
    document.addEventListener('DOMContentLoaded', () => {
        if (IS_HEAD) {
            setTimeout(() => {
                try {
                    const revealBtnContainer = document.getElementById('reveal-btn-in-question');
                    console.log('[Tafseer Reveal] Initial button injection - container found:', !!revealBtnContainer);

                    if (revealBtnContainer) {
                        revealBtnContainer.innerHTML = `
                            <button id="reveal-question-btn" type="button" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5" style="display: none;">
                                <i class="fas fa-eye text-xs"></i>
                                <span>إظهار للجميع</span>
                            </button>`;
                        console.log('[Tafseer Reveal] Initial button injected successfully (hidden - auto-reveal active)');
                        updateRevealButtonState();
                    }
                } catch (e) {
                    console.warn('[Tafseer Reveal] Button injection failed (initial)', e);
                }
            }, 500);
        }
        
        // في وضع التعديل، تحديث قائمة الأسئلة عند تحميل الصفحة
        // للتأكد من أن جميع الأسئلة المحكمة تظهر في القائمة
        if (isEditMode && typeof updateRevealedListUI === 'function') {
            setTimeout(() => {
                // استخدام القائمة الأولية من الـ backend التي تحتوي على جميع الأسئلة المحكمة
                const initialRevealedIds = window.initialRevealedQuestionIds || initialRevealedQuestionIds || [];
                // تحديث window.revealedQuestionIds بالقائمة الصحيحة
                window.revealedQuestionIds = initialRevealedIds;
                revealedQuestionIds = initialRevealedIds;
                
                console.log('[Tafseer Edit Mode] Updating questions list on page load', {
                    initialRevealedIds: initialRevealedIds,
                    windowRevealedQuestionIds: window.revealedQuestionIds,
                    questionsDataCount: document.querySelectorAll('#questions-data > div').length,
                    sidebarItemsCount: document.querySelectorAll('#questions-list-container .question-item').length
                });
                
                // تحديث القائمة الجانبية بجميع الأسئلة المحكمة
                updateRevealedListUI(initialRevealedIds);
            }, 500); // تقليل الوقت لضمان التنفيذ قبل الـ polling
        }
    });

    // Function to update reveal button state
    function updateRevealButtonState() {
        if (!IS_HEAD) {
            return;
        }

        const questionsData = document.querySelectorAll('#questions-data > div');
        const currentIdx = window.currentIndex || 0;

        if (!questionsData[currentIdx]) {
            console.log('[Tafseer Reveal] No question data at currentIndex:', currentIdx);
            return;
        }

        const qId = parseInt(questionsData[currentIdx].dataset.questionId);
    const btn = document.getElementById('reveal-question-btn');

        console.log('[Tafseer Reveal] updateRevealButtonState called', {
            currentIndex: currentIdx,
            qId,
            btnFound: !!btn,
            revealedIds: revealedQuestionIds
        });

        if (!btn) {
            console.warn('[Tafseer Reveal] ❌ Button #reveal-question-btn not found in DOM!');
            // Try to find it in questionContent
            const questionContent = document.getElementById('question-content');
            if (questionContent) {
                const btnInContent = questionContent.querySelector('#reveal-question-btn');
                console.log('[Tafseer Reveal] Button in questionContent:', !!btnInContent);
            }
            return;
        }

        // استخدام window.revealedQuestionIds للتأكد من المزامنة
        const currentRevealedIds = window.revealedQuestionIds || revealedQuestionIds || [];
        const isRevealed = currentRevealedIds.includes(qId);
        
        console.log('[Tafseer Reveal] updateRevealButtonState check', {
            qId: qId,
            isRevealed: isRevealed,
            currentRevealedIds: currentRevealedIds,
            revealedQuestionIds: revealedQuestionIds,
            windowRevealedQuestionIds: window.revealedQuestionIds
        });
        
        if (isRevealed) {
            btn.className = 'px-3 py-1.5 bg-green-50 text-green-600 border border-green-200 rounded-lg text-xs font-medium cursor-not-allowed flex items-center gap-1.5';
            btn.innerHTML = '<i class="fas fa-check-circle text-xs"></i><span>تم الإظهار</span>';
            btn.disabled = true;
            btn.style.pointerEvents = 'none';
            btn.style.display = 'none'; // Hide button when revealed (auto-reveal is active)
            console.log('[Tafseer Reveal] ✅ Button updated to REVEALED state for question (hidden - auto-reveal active)', qId);
        } else {
            // Keep button hidden by default since auto-reveal is active
            // Button is kept in DOM for manual reveal if needed (fallback)
            btn.style.display = 'none';
            btn.className = 'px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5';
            btn.innerHTML = '<i class="fas fa-eye text-xs"></i><span>إظهار للجميع</span>';
            btn.disabled = false;
            btn.style.pointerEvents = 'auto';
            console.log('[Tafseer Reveal] ✅ Button updated to NOT REVEALED state for question', qId);
        }
    }

    // Make updateRevealButtonState available globally
    window.updateRevealButtonState = updateRevealButtonState;

    // Auto-reveal function for head - saves to DB without UI notifications
    // Use window variables to ensure it works from anywhere
    async function autoRevealQuestionForHead(qId) {
        // Get variables from window if not available locally
        const branchId = window.BRANCH_ID || BRANCH_ID;
        const participationId = window.PARTICIPATION_ID || PARTICIPATION_ID;
        const fieldType = window.FIELD_TYPE || FIELD_TYPE;
        const currentRevealedIds = window.revealedQuestionIds || revealedQuestionIds || [];
        
        // Check if already revealing (use window to track across scopes)
        if (window.isRevealing === true) {
            console.log('[Tafseer Auto-Reveal] Already revealing a question, skipping');
            return;
        }

        // Check if already revealed
        if (currentRevealedIds.includes(qId)) {
            console.log('[Tafseer Auto-Reveal] Question already revealed, skipping', { qId });
            return;
        }

        console.log('[Tafseer Auto-Reveal] Auto-revealing question for head', {
            qId,
            branchId,
            participationId,
            fieldType,
            currentRevealedIds
        });

        window.isRevealing = true;

        // Update revealed list
        const updatedRevealedIds = [...currentRevealedIds];
        if (!updatedRevealedIds.includes(qId)) {
            updatedRevealedIds.push(qId);
        }
        window.revealedQuestionIds = updatedRevealedIds;
        
        // Also update local variable if it exists
        if (typeof revealedQuestionIds !== 'undefined') {
            revealedQuestionIds = updatedRevealedIds;
        }

        // Save to database silently
        try {
            const res = await fetch('{{ url("/api/judgings/tafseer/reveals") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    competition_version_branch_id: branchId,
                    competition_participation_id: participationId,
                    question_id: qId,
                    field_type: fieldType
                })
            });
            const data = await res.json();

            if (data && data.success) {
                console.log('[Tafseer Auto-Reveal] ✅ Question auto-revealed successfully', { qId });
                // Update UI list without notification
                if (typeof updateRevealedListUI === 'function') {
                    updateRevealedListUI(window.revealedQuestionIds);
                }
                // Update button state
                if (typeof window.updateRevealButtonState === 'function') {
                    setTimeout(() => window.updateRevealButtonState(), 100);
                }
            } else {
                console.error('[Tafseer Auto-Reveal] ❌ Failed to reveal', { qId, data });
                // Remove from list on failure
                const index = updatedRevealedIds.indexOf(qId);
                if (index > -1) {
                    updatedRevealedIds.splice(index, 1);
                    window.revealedQuestionIds = updatedRevealedIds;
                    if (typeof revealedQuestionIds !== 'undefined') {
                        revealedQuestionIds = updatedRevealedIds;
                    }
                }
            }
        } catch (err) {
            console.error('[Tafseer Auto-Reveal] ❌ API error', err);
            // Remove from list on error
            const index = updatedRevealedIds.indexOf(qId);
            if (index > -1) {
                updatedRevealedIds.splice(index, 1);
                window.revealedQuestionIds = updatedRevealedIds;
                if (typeof revealedQuestionIds !== 'undefined') {
                    revealedQuestionIds = updatedRevealedIds;
                }
            }
        } finally {
            setTimeout(() => {
                window.isRevealing = false;
            }, 300);
        }
    }

    // Make autoRevealQuestionForHead available globally
    window.autoRevealQuestionForHead = autoRevealQuestionForHead;

    async function revealCurrentQuestion() {
        // منع الإظهار المتعدد في نفس الوقت
        if (isRevealing) {
            console.log('[Tafseer Reveal] Already revealing a question, please wait');
            return;
        }

        const questionsData = document.querySelectorAll('#questions-data > div');
        const qId = parseInt(questionsData[currentIndex].dataset.questionId);
        console.log('[Tafseer Reveal] Starting reveal process', {
            qId,
            BRANCH_ID,
            PARTICIPATION_ID,
            FIELD_TYPE,
            currentIndex
        });

        isRevealing = true;
    const btn = document.getElementById('reveal-question-btn');

        // ✨ Optimistic UI Update - تحديث فوري قبل انتظار السيرفر
        if (btn) {
        btn.disabled = true;
            btn.className = 'px-3 py-1.5 bg-green-50 text-green-600 border border-green-200 rounded-lg text-xs font-medium cursor-not-allowed flex items-center gap-1.5';
            btn.innerHTML = '<i class="fas fa-check-circle text-xs"></i><span>تم الإظهار</span>';
            btn.style.pointerEvents = 'none';
        }

        // إضافة السؤال للقائمة فوراً وتحديث window.revealedQuestionIds
        if (!revealedQuestionIds.includes(qId)) {
            revealedQuestionIds.push(qId);
        }
        // تحديث window.revealedQuestionIds للتأكد من المزامنة مع جميع الملفات
        window.revealedQuestionIds = revealedQuestionIds;
        console.log('[Tafseer Reveal] Updated revealedQuestionIds:', revealedQuestionIds, 'window.revealedQuestionIds:', window.revealedQuestionIds);

        // إظهار إشعار النجاح فوراً
        if (typeof showCustomNotification === 'function') {
            showCustomNotification('تم الإظهار', 'تم إظهار السؤال لباقي الأعضاء بنجاح', 'success', 2000);
        }

        // إرسال الطلب للسيرفر في الخلفية
        try {
            const res = await fetch('{{ url("/api/judgings/tafseer/reveals") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                    competition_version_branch_id: BRANCH_ID,
                    competition_participation_id: PARTICIPATION_ID,
                    question_id: qId,
                    field_type: FIELD_TYPE
            })
        });
            const data = await res.json();
            console.log('[Tafseer Reveal] API response received', { data });

            // في حالة النجاح، تأكد من تحديث حالة الزر
            if (data && data.success) {
                // تحديث window.revealedQuestionIds للتأكد من المزامنة
                window.revealedQuestionIds = revealedQuestionIds;
                
                // تحديث حالة الزر بعد النجاح للتأكد من أنه يبقى في حالة "تم الإظهار"
                setTimeout(() => {
                    if (typeof updateRevealButtonState === 'function') {
                        updateRevealButtonState();
                    } else {
                        // Fallback: تحديث الزر مباشرة
                        const btn = document.getElementById('reveal-question-btn');
                        if (btn) {
                            btn.disabled = true;
                            btn.className = 'px-3 py-1.5 bg-green-50 text-green-600 border border-green-200 rounded-lg text-xs font-medium cursor-not-allowed flex items-center gap-1.5';
                            btn.innerHTML = '<i class="fas fa-check-circle text-xs"></i><span>تم الإظهار</span>';
                            btn.style.pointerEvents = 'none';
                        }
                    }
                }, 100);
            }

            // في حالة الفشل فقط، نعيد الزر لحالته الأصلية
            if (!data || !data.success) {
                console.error('[Tafseer Reveal] Server returned failure');
                const index = revealedQuestionIds.indexOf(qId);
                if (index > -1) {
                    revealedQuestionIds.splice(index, 1);
                }
                if (btn) {
                    btn.disabled = false;
                    btn.className = 'px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5';
                    btn.innerHTML = '<i class="fas fa-eye text-xs"></i><span>إظهار للجميع</span>';
                    btn.style.pointerEvents = 'auto';
                }
                if (typeof showCustomNotification === 'function') {
                    showCustomNotification('فشل الإظهار', (data && data.message) || 'حدث خطأ، حاول مرة أخرى', 'error', 3000);
                }
            }
        } catch (err) {
            console.error('[Tafseer Reveal] API error', err);
            const index = revealedQuestionIds.indexOf(qId);
            if (index > -1) {
                revealedQuestionIds.splice(index, 1);
            }
            if (btn) {
                btn.disabled = false;
                btn.className = 'px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5';
                btn.innerHTML = '<i class="fas fa-eye text-xs"></i><span>إظهار للجميع</span>';
                btn.style.pointerEvents = 'auto';
            }
            if (typeof showCustomNotification === 'function') {
                showCustomNotification('خطأ', 'تعذر الاتصال بالخادم، حاول مرة أخرى', 'error', 3000);
            }
        } finally {
            setTimeout(() => {
                isRevealing = false;
                console.log('[Tafseer Reveal] Lock released');
            }, 500);
        }
    }

    // Event listener for reveal button (HEAD only)
    if (IS_HEAD) {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('#reveal-question-btn');
            if (btn && !btn.disabled && !isRevealing) {
                revealCurrentQuestion();
            }
        });
    }

    // Make reveal functions globally accessible
    window.revealCurrentQuestion = revealCurrentQuestion;

    // Function to rebuild question content (unlock it for members when revealed)
    function rebuildQuestionContent(index) {
        const questionsData = document.querySelectorAll('#questions-data > div');
        const qData = questionsData[index];
        if (!qData) {
            console.warn('[Tafseer Reveal] Question data not found for index:', index);
            return;
        }

        const questionDiv = document.getElementById('question-' + index);
        if (!questionDiv) {
            console.warn('[Tafseer Reveal] Question div not found for index:', index);
            return;
        }

        const questionText = qData.dataset.questionText || '';
        const answerText = qData.dataset.answerText || '';
        const bookName = qData.dataset.bookName || '';
        const pageNumber = qData.dataset.pageNumber || '';

        // Only rebuild if the question is currently locked
        const hasLockedView = questionDiv.querySelector('.unified-content') !== null;
        const hasUnlockedView = questionDiv.querySelector('.question-content') !== null;

        if (hasUnlockedView && !hasLockedView) {
            console.log('[Tafseer Reveal] Question', index + 1, 'already unlocked, skipping rebuild');
            return;
        }

        // Remove locked view if exists BEFORE rebuilding
        const lockedView = questionDiv.querySelector('.unified-content');
        if (lockedView) {
            lockedView.remove();
        }

        // Rebuild content with unlocked view
        const newContent = '<div class="question-content">' +
            '<div class="mb-8">' +
                '<div class="flex items-center justify-between mb-4">' +
                    '<div class="flex items-center">' +
                        '<i class="fas fa-question-circle text-primary ms-2 text-xl"></i>' +
                        '<h2 class="text-xl font-bold text-primary">السؤال ' + (index + 1) + '</h2>' +
                    '</div>' +
                    (IS_HEAD ? '<div id="reveal-btn-in-question"></div>' : '') +
                '</div>' +
                '<div class="text-gray-800 dark:text-gray-200 text-lg leading-relaxed">' +
                    questionText +
                '</div>' +
            '</div>' +
            '<div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-700 border-r-4 border-green-500 rounded-lg p-6">' +
                '<div class="flex items-center mb-4">' +
                    '<i class="fas fa-lightbulb text-green-600 dark:text-green-400 ms-2 text-xl"></i>' +
                    '<h2 class="text-xl font-bold text-green-600 dark:text-green-400">الجواب</h2>' +
                '</div>' +
                '<div class="text-gray-800 dark:text-gray-200 text-lg leading-relaxed">' +
                    answerText +
                '</div>' +
                (bookName ? '<div class="mt-4 p-3 bg-green-100 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg">' +
                    '<div class="flex items-center text-sm text-green-800 dark:text-green-200">' +
                        '<i class="fas fa-book ms-2"></i>' +
                        '<span><strong>المصدر:</strong> ' + bookName + ' - صفحة ' + pageNumber + '</span>' +
                    '</div>' +
                '</div>' : '') +
            '</div>' +
        '</div>';

        questionDiv.innerHTML = newContent;

        // إخفاء رسالة "في انتظار رئيس اللجنة" وإظهار النموذج
        setTimeout(() => {
            const lockMsg = document.getElementById('member-locked-warning');
            if (lockMsg) {
                lockMsg.classList.add('hidden');
            }
            const formContent = document.getElementById('current-form-content');
            if (formContent) {
                formContent.classList.remove('hidden');
            }
        }, 50);
        questionDiv.classList.add('fade-in');

        console.log('[Tafseer Reveal] Rebuilt and unlocked question content:', index + 1);
    }

    // Make rebuildQuestionContent available globally
    window.rebuildQuestionContent = rebuildQuestionContent;

    // Function to update questions list UI when new questions are revealed
    function updateRevealedListUI(revealedIds) {
        const container = document.getElementById('questions-list-container');
        if (!container) {
            console.warn('[Tafseer UI Update] ⚠️ Container #questions-list-container not found!');
            return;
        }

        console.log('[Tafseer UI Update] 🎨 Updating questions list', {
            revealed_ids: revealedIds,
            revealed_count: revealedIds.length
        });

        const questionsData = document.querySelectorAll('#questions-data > div');

        // Update visibility of question items in the list
        questionsData.forEach((qData, index) => {
            const qId = parseInt(qData.dataset.questionId);
            const shouldShow = IS_HEAD || revealedIds.includes(qId);

            // Find existing question item
            const existingItem = container.querySelector(`.question-item[data-question-id="${qId}"]`);

            if (shouldShow && !existingItem) {
                // Add new question item if it should be visible but doesn't exist
                const questionItem = createQuestionListItem(index, qId, qData);
                container.appendChild(questionItem);
                console.log('[Tafseer UI Update] ✅ Added new question item', { index: index + 1, qId });
            } else if (!shouldShow && existingItem) {
                // Remove question item if it should be hidden
                existingItem.remove();
                console.log('[Tafseer UI Update] 🗑️ Removed question item', { index: index + 1, qId });
            }
        });

        // Update highlights
        if (typeof updateQuestionHighlight === 'function') {
            updateQuestionHighlight();
        }

        console.log('[Tafseer UI Update] ✅ Questions list updated', {
            total_items_now: container.querySelectorAll('.question-item').length
        });
    }

    // Helper function to create question list item
    function createQuestionListItem(index, qId, qData) {
        const div = document.createElement('div');
        div.className = 'question-item relative overflow-hidden rounded-lg p-2 cursor-pointer transition-all duration-300 border ' +
                        (index === currentIndex ? 'bg-blue-900 text-white shadow-sm border-blue-900' : 'bg-slate-50 text-slate-600 border-slate-200');
        div.setAttribute('data-question-index', index);
        div.setAttribute('data-question-id', qId);
        div.onclick = function() { if (window.switchToQuestion) window.switchToQuestion(index); };

        const questionText = qData.dataset.questionText || '';
        const truncatedText = questionText.length > 40 ? questionText.slice(0, 40) + '...' : questionText;

        div.innerHTML = `
            <div class="flex items-start gap-2">
                <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center font-bold text-[10px] ${index === currentIndex ? 'bg-white text-blue-900' : 'bg-slate-200 text-slate-500'}">
                    ${index + 1}
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-xs mb-0 ${index === currentIndex ? 'text-blue-100' : 'text-slate-800'}">
                        سؤال ${index + 1}
                    </h4>
                    <p class="text-[10px] leading-snug opacity-90 font-light">
                        ${truncatedText}
                    </p>
                </div>
            </div>
        `;

        return div;
    }

    // Polling for members to fetch revealed questions
    if (!IS_HEAD) {
        const pollRevealStatus = async () => {
            try {
                const url = `{{ url('/api/judgings/tafseer/reveals/status') }}?competition_version_branch_id=${BRANCH_ID}&competition_participation_id=${PARTICIPATION_ID}&field_type=${FIELD_TYPE}`;
                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();

                console.log('[Tafseer Polling] 🔄 Status check response', {
                    revealed_count: data.revealed ? data.revealed.length : 0,
                    revealed_ids: data.revealed,
                    timestamp: new Date().toLocaleTimeString()
                });

                if (Array.isArray(data.revealed)) {
                    const prev = revealedQuestionIds.slice();
                    const apiRevealedIds = data.revealed.map(Number);
                    
                    // في وضع التعديل، ندمج الأسئلة من API مع الأسئلة الأولية المحكمة
                    // لأن API قد لا يحتوي على جميع الأسئلة المحكمة (مثل الأسئلة التي لم يتم إظهارها عبر QuestionReveal)
                    if (isEditMode && window.initialRevealedQuestionIds) {
                        // دمج الأسئلة من API مع الأسئلة الأولية (بدون تكرار)
                        const initialIds = window.initialRevealedQuestionIds || initialRevealedQuestionIds;
                        const mergedIds = [...new Set([...initialIds, ...apiRevealedIds])];
                        revealedQuestionIds = mergedIds;
                        console.log('[Tafseer Polling] Edit mode - merged revealed IDs', {
                            initial_ids: initialIds,
                            api_ids: apiRevealedIds,
                            merged_ids: mergedIds
                        });
                    } else {
                        // في الوضع العادي، نستخدم فقط الأسئلة من API
                        revealedQuestionIds = apiRevealedIds;
                    }
                    
                    window.revealedQuestionIds = revealedQuestionIds; // Update global reference

                    // Log any changes
                    if (prev.length !== revealedQuestionIds.length) {
                        // تحديث القائمة الجانبية
                        updateRevealedListUI(revealedQuestionIds);
    
                        // 👇 هذا هو السطر الذي أضفناه لتحديث حالة زر "إنهاء وحفظ" فوراً
                        if (typeof updateButtonStates === 'function') {
                            updateButtonStates();
                        }
                    
                        console.log('[Tafseer Polling] 🔔 Change detected!', {
                            previous_count: prev.length,
                            new_count: revealedQuestionIds.length,
                            previous_ids: prev,
                            new_ids: revealedQuestionIds
                        });
                    }

                    // Update questions list UI
                    updateRevealedListUI(revealedQuestionIds);

                    // If current question became revealed now, reload it
                    const questionsData = document.querySelectorAll('#questions-data > div');
                    const currentIdx = window.currentIndex || 0;

                    if (!questionsData[currentIdx]) {
                        console.warn('[Tafseer Polling] ⚠️ No question data at currentIndex:', currentIdx);
                        return;
                    }

                    const qId = parseInt(questionsData[currentIdx].dataset.questionId);
                    const wasRevealed = prev.includes(qId);
                    const isNowRevealed = revealedQuestionIds.includes(qId);

                    if (!wasRevealed && isNowRevealed) {
                        console.log('[Tafseer Polling] ✨ Current question unlocked by head!', {
                            qId,
                            currentIndex: currentIdx,
                            questionNumber: currentIdx + 1
                        });

                        // Rebuild question content to show unlocked version
                        if (typeof window.rebuildQuestionContent === 'function') {
                            console.log('[Tafseer Polling] 🔨 Rebuilding question content...');
                            window.rebuildQuestionContent(currentIdx);
                        }

                        // Reload question
                        if (typeof window.switchToQuestion === 'function') {
                            console.log('[Tafseer Polling] 🔄 Reloading question...');
                            window.switchToQuestion(currentIdx);
                        }

                        // Show notification
                        if (typeof showCustomNotification === 'function') {
                            showCustomNotification('سؤال جديد', `تم إظهار السؤال ${currentIdx + 1} من قبل رئيس اللجنة`, 'success', 4000);
                        }
                    } else if (prev.length === 0 && revealedQuestionIds.length > 0) {
                        // First question revealed - auto-open it
                        console.log('[Tafseer Polling] 🔓 First question revealed, auto-opening');
                        if (revealedQuestionIds.length > 0) {
                            const firstRevealedId = revealedQuestionIds[0];
                            const firstIndex = Array.from(questionsData).findIndex(q =>
                                parseInt(q.dataset.questionId) === firstRevealedId
                            );
                            if (firstIndex >= 0 && typeof window.switchToQuestion === 'function') {
                                console.log('[Tafseer Polling] 📖 Opening first revealed question:', firstIndex + 1);
                                window.switchToQuestion(firstIndex);
                            }
                        }
                    }
                }
            } catch (e) {
                console.warn('[Tafseer Reveal] Polling error', e);
            }
        };

        console.log('[Tafseer Polling] 🚀 Starting polling for member (every 200ms)');
        pollRevealStatus();
        setInterval(pollRevealStatus, 200);
    }
</script>
<style>
    /* Unified Content Area Styles */
    .unified-content {
        min-height: 400px;
    }
    /* Score compact modern style */
    .score-card {
        background: linear-gradient(135deg, #f8fafc, #eef2f7);
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 10px 12px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
    }
    .dark .score-card {
        background: linear-gradient(135deg, #1f2937, #111827);
        border-color: #374151;
        box-shadow: 0 2px 6px rgba(0,0,0,0.25);
    }
    .score-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9999px;
        transition: transform .15s ease, box-shadow .2s ease, filter .2s ease;
        color: #fff;
        font-size: 14px;
    }
    .score-btn.plus { background: #10b981; }
    .score-btn.plus:hover { filter: brightness(1.05); box-shadow: 0 2px 8px rgba(16,185,129,.35); transform: translateY(-1px); }
    .score-btn.minus { background: #ef4444; }
    .score-btn.minus:hover { filter: brightness(1.05); box-shadow: 0 2px 8px rgba(239,68,68,.35); transform: translateY(-1px); }
    .score-input {
        flex: 1;
        height: 40px;
        text-align: center;
        font-weight: 700;
        letter-spacing: .3px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #f3f6fb;
        color: #111827;
        outline: none;
    }
    .score-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,.12);
        background: #ffffff;
    }
    .dark .score-input {
        border-color: #374151;
        background: #1f2937;
        color: #f9fafb;
    }
    .dark .score-input:focus {
        box-shadow: 0 0 0 3px rgba(59,130,246,.25);
        background: #111827;
    }

    .question-section {
        transition: all 0.3s ease;
    }

    .question-section:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }

    .answer-section {
        transition: all 0.3s ease;
    }

    .answer-section:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        transform: translateY(-1px);
    }

    /* Question text styling - larger and clearer */
    .question-section .text-gray-800.dark\:text-gray-200.leading-relaxed {
        font-size: 1.125rem;
        line-height: 1.75;
        font-weight: 500;
        color: #1f2937;
    }

    .dark .question-section .text-gray-800.dark\:text-gray-200.leading-relaxed {
        color: #f3f4f6;
    }

    /* Answer text styling */
    .answer-section .text-gray-800.dark\:text-gray-200.leading-relaxed {
        font-size: 1.125rem;
        line-height: 1.75;
        font-weight: 400;
        color: #1f2937;
    }

    .dark .answer-section .text-gray-800.dark\:text-gray-200.leading-relaxed {
        color: #f3f4f6;
    }

    /* Enhanced readability for text */
    .question-section,
    .answer-section {
        font-family: 'Segoe UI', 'Noto Sans Arabic', 'Arial Unicode MS', sans-serif;
    }

    /* Question styling */
    .question-section h3 {
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .dark .question-section h3 {
        border-bottom-color: #374151;
    }

    /* Answer section styling */
    .answer-section h3 {
        border-bottom: 2px solid #10b981;
        padding-bottom: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .dark .answer-section h3 {
        border-bottom-color: #059669;
    }

    /* Source info styling */
    .question-section .bg-green-100,
    .answer-section .bg-green-100 {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    }

    .dark .question-section .bg-green-900,
    .dark .answer-section .bg-green-900 {
        background: linear-gradient(135deg, #064e3b, #065f46);
    }

    /* Responsive adjustments */
    @media (max-width: 1024px) {
        .unified-content {
            space-y-4;
        }

        .question-section,
        .answer-section {
            padding: 1rem;
        }

        .question-section .text-gray-800.dark\:text-gray-200.leading-relaxed,
        .answer-section .text-gray-800.dark\:text-gray-200.leading-relaxed {
            font-size: 1rem;
        }
    }

    @media (max-width: 768px) {

        .question-section .text-gray-800.dark\:text-gray-200.leading-relaxed,
        .answer-section .text-gray-800.dark\:text-gray-200.leading-relaxed {
            font-size: 0.95rem;
        }
    }

    /* Modal Styles */
    #save-confirmation-modal {
        backdrop-filter: blur(4px);
    }

    #save-confirmation-modal .notification-header {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    #save-confirmation-modal .notification-icon {
        flex-shrink-0;
        width: 3rem;
        height: 3rem;
        background: linear-gradient(135deg, #10B981, #059669);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #save-confirmation-modal .notification-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
    }

    .dark #save-confirmation-modal .notification-title {
        color: #ffffff;
    }

    #save-confirmation-modal .notification-message {
        font-size: 0.875rem;
        color: #6B7280;
    }

    .dark #save-confirmation-modal .notification-message {
        color: #9CA3AF;
    }

    /* Modal animations */
    #save-confirmation-modal>div {
        animation: modalSlideIn 0.3s ease-out;
    }

    @keyframes modalSlideIn {
        from {
            opacity: 0;
            transform: scale(0.9) translateY(-20px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    /* Loading spinner animation */
    .fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Button hover effects */
    #confirm-save-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }

    #cancel-save-btn:hover {
        transform: translateY(-1px);
    }

    /* Score Control Buttons */
    .score-controls {
        margin-top: 0.5rem;
    }

    .score-controls button {
        transition: all 0.2s ease;
    }

    .score-controls button:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .score-controls button:active {
        transform: translateY(0);
    }

    /* Tafseer specific responsive adjustments */
    @media (max-width: 1024px) {
        aside {
            width: auto;
            min-width: 12rem;
        }
    }

    @media (max-width: 768px) {
        aside {
            position: static;
            margin-bottom: 1rem;
        }
    }

    /* Relief Details Modal Styles */
    #relief-details-modal {
        backdrop-filter: blur(4px);
    }

    #relief-details-modal .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    #relief-details-modal .bg-blue-50 {
        border-left: 4px solid #3b82f6;
    }

    #relief-details-modal .bg-green-50 {
        border-left: 4px solid #10b981;
    }

    #relief-details-modal .bg-yellow-50 {
        border-left: 4px solid #f59e0b;
    }

    #relief-details-modal .bg-purple-50 {
        border-left: 4px solid #8b5cf6;
    }

    #relief-details-modal .bg-indigo-50 {
        border-left: 4px solid #6366f1;
    }

    .dark #relief-details-modal .bg-blue-900\/20 {
        border-left: 4px solid #60a5fa;
    }

    .dark #relief-details-modal .bg-green-900\/20 {
        border-left: 4px solid #34d399;
    }

    .dark #relief-details-modal .bg-yellow-900\/20 {
        border-left: 4px solid #fbbf24;
    }

    .dark #relief-details-modal .bg-purple-900\/20 {
        border-left: 4px solid #a78bfa;
    }

    .dark #relief-details-modal .bg-indigo-900\/20 {
        border-left: 4px solid #818cf8;
    }


    /* ═══════════════════════════════════════════════════════════════ */
    /* Reveal Button Styles - نظام إظهار الأسئلة */
    /* ═══════════════════════════════════════════════════════════════ */
    #reveal-btn-wrapper {
        animation: fadeInUp 0.3s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #reveal-question-btn {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        font-size: 15px;
        padding: 10px 20px;
    }

    #reveal-question-btn:not(:disabled):hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    #reveal-question-btn:disabled {
        opacity: 1;
    }

    #reveal-question-btn.bg-green-600 {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    #reveal-question-btn.bg-purple-600:hover {
        background: linear-gradient(135deg, #7c3aed 0%, #8b5cf6 100%);
    }

    .dark #reveal-question-btn {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
    }

    /* Nafes Chips Style */
    .nafes-note-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: #fef9c3; /* yellow-100 */
        border: 1px solid #fde047; /* yellow-300 */
        border-radius: 9999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
        color: #854d0e; /* yellow-800 */
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }
    .nafes-note-chip:hover {
        background-color: #fef08a; /* yellow-200 */
        border-color: #facc15; /* yellow-400 */
    }
    .nafes-note-chip .remove-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: rgba(133, 77, 14, 0.1);
        color: #854d0e;
        cursor: pointer;
        font-size: 8px;
    }
    .nafes-note-chip .remove-btn:hover {
        background-color: rgba(133, 77, 14, 0.2);
    }
    .nafes-notes-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }
    .nafes-notes-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 800;
        color: #1e2540;
        font-size: 14px;
    }
    .nafes-notes-title i {
        color: #eab308; /* yellow-500 */
        font-size: 12px;
    }
    .nafes-btn-all {
        background-color: #fdfdfb;
        border: 1px solid rgba(226, 232, 240, 0.6);
        color: #94a3b8;
        font-weight: 800;
        font-size: 10px;
        padding: 6px 10px;
        border-radius: 10px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .nafes-btn-all:hover {
        background-color: #f8fafc;
        color: #ca8a04;
        border-color: #fde047;
    }
    .nafes-search-input-container {
        background-color: white;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        padding: 4px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }
    .nafes-search-input {
        width: 100%;
        background: transparent;
        border: none;
        padding: 8px 12px;
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }
    .nafes-search-input::placeholder {
        color: #cbd5e1;
    }
    .nafes-search-input:focus {
        outline: none;
    }

    /* Relief Button Styles */
    .nafes-relief-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        width: 100%;
        padding: 0.85rem 1rem;
        background-color: #fdfcf5;
        border: 1.5px solid var(--color-primary);
        color: var(--color-primary-dark);
        border-radius: 0.85rem;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 6px rgba(212, 175, 55, 0.15);
    }
    .nafes-relief-btn .relief-icon {
        position: absolute;
        right: 1.25rem;
        font-size: 1.1rem;
        color: #e0b678;
    }
    .nafes-relief-btn:hover {
        background-color: #f7eed7;
        border-color: #dcb67d;
        transform: translateY(-1px);
    }
    .nafes-relief-btn:active {
        transform: scale(0.98);
    }
</style>

<script>
    function showAllNotesModal() {
        const modal = document.getElementById('all-notes-modal');
        const content = document.getElementById('all-notes-modal-content');
        if (modal && content) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                content.classList.remove('opacity-0', 'translate-y-4', 'scale-95');
                content.classList.add('opacity-100', 'translate-y-0', 'scale-100');
            }, 10);
        }
    }

    function closeAllNotesModal() {
        const modal = document.getElementById('all-notes-modal');
        const content = document.getElementById('all-notes-modal-content');
        if (modal && content) {
            content.classList.remove('opacity-100', 'translate-y-0', 'scale-100');
            content.classList.add('opacity-0', 'translate-y-4', 'scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }, 300);
        }
    }

    function filterModalNotes(searchVal) {
        const query = (searchVal || '').trim().toLowerCase();
        const categories = document.querySelectorAll('.modal-category-block');
        categories.forEach(block => {
            let hasVisibleNotes = false;
            const chips = block.querySelectorAll('.nafes-modal-chip');
            chips.forEach(chip => {
                const text = chip.getAttribute('data-note-text') || '';
                if(query === '' || text.includes(query)) {
                    chip.style.display = 'inline-flex';
                    hasVisibleNotes = true;
                } else {
                    chip.style.display = 'none';
                }
            });
            block.style.display = hasVisibleNotes ? 'block' : 'none';
        });
    }

    function toggleNoteSelect(id) {
        const btn = document.getElementById('modal-note-card-' + id);
        if(!btn) return;
        
        btn.classList.toggle('active');
        updateModalChipVisual(btn);
        
        // Sync with sidebar
        const idsField = document.getElementById('note-ids');
        const textsField = document.getElementById('note-texts');
        if(!idsField || !textsField) return;

        let ids = idsField.value ? idsField.value.split(',') : [];
        let texts = textsField.value ? JSON.parse(textsField.value) : [];

        const idStr = String(id);
        const index = ids.indexOf(idStr);
        
        if (btn.classList.contains('active')) {
            if (index === -1) {
                const note = window.allNotes.find(n => String(n.id) === idStr);
                ids.push(idStr);
                texts.push(note ? note.note : 'ملاحظة');
            }
        } else {
            if (index > -1) {
                ids.splice(index, 1);
                texts.splice(index, 1);
            }
        }

        idsField.value = JSON.stringify(ids);
        textsField.value = JSON.stringify(texts);
        
        updateSelectedNotesDisplay(ids, texts);
    }

    function updateModalChipVisual(btn) {
        if(!btn) return;
        const isActive = btn.classList.contains('active');
        const checkIcon = btn.querySelector('.check-icon');
        if(isActive) {
            if(checkIcon) checkIcon.classList.remove('hidden');
        } else {
            if(checkIcon) checkIcon.classList.add('hidden');
        }
        updateModalCountDisplay();
    }

    function updateModalCountDisplay() {
        const activeCount = document.querySelectorAll('#modal-notes-list .nafes-modal-chip.active').length;
        const countSpan = document.getElementById('modal-selected-count');
        if(countSpan) countSpan.textContent = activeCount;
    }
    
    // Global notes data for UI lookups
    window.allNotes = @json($notes ?? []);
    window.findNoteById = function(id) {
        return window.allNotes.find(n => String(n.id) === String(id));
    };

    function updateSelectedNotesDisplay(ids, texts) {
        const tagsContainer = document.getElementById('notes-container');
        if (tagsContainer) {
            if (!ids || ids.length === 0) {
                tagsContainer.innerHTML = `
                    <div class="notes-empty-state w-full text-center py-4 text-slate-300">
                        <i class="fas fa-info-circle text-xs mb-1"></i>
                        <p class="text-[10px] font-bold">لا توجد ملاحظات مختارة</p>
                    </div>
                `;
            } else {
                const html = ids.map((id, index) => {
                    const note = window.allNotes.find(n => String(n.id) === String(id));
                    const category = note ? note.category_name : '';
                    const baseText = texts[index] || (note ? note.note : 'ملاحظة');
                    
                    const div = document.createElement('div');
                    div.textContent = baseText;
                    const escapedText = div.innerHTML;

                    const label = category ? `${category}: ${escapedText}` : escapedText;

                    return `
                        <div class="nafes-note-chip">
                            <span>${label}</span>
                            <span class="remove-btn" onclick="removeNote('${id}', event)">
                                <i class="fas fa-times"></i>
                            </span>
                        </div>
                    `;
                }).join('');
                
                tagsContainer.innerHTML = html;
            }
        }
    }

    function removeNote(id, event) {
        if (event) event.stopPropagation();
        
        const idsField = document.getElementById('note-ids');
        const textsField = document.getElementById('note-texts');
        if (!idsField || !textsField) return;

        let ids = idsField.value ? idsField.value.split(',') : [];
        let texts = textsField.value ? JSON.parse(textsField.value) : [];

        const index = ids.indexOf(String(id));
        if (index > -1) {
            ids.splice(index, 1);
            texts.splice(index, 1);
            
            idsField.value = JSON.stringify(ids);
            textsField.value = JSON.stringify(texts);
            
            // Sync modal buttons
            const modalBtn = document.getElementById('modal-note-card-' + id);
            if (modalBtn) {
                modalBtn.classList.remove('active');
                const checkIcon = modalBtn.querySelector('.check-icon');
                if (checkIcon) checkIcon.classList.add('hidden');
                updateModalCountDisplay();
            }
            
            updateSelectedNotesDisplay(ids, texts);
        }
    }

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
    </div>
</script>

@include('mosabka::judgings.tafseer.footer')
@include('mosabka::judgings.tafseer.reveal-system')