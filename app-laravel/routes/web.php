<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/judgings', function () {
    return "Judging List View";
})->name('judgings.index');

Route::get('/tafseer', function () {
    // Mock user for header.blade.php
    $user = new \App\Models\User(['full_name' => 'المستخدم التجريبي']);
    Auth::setUser($user);

    $mockQuestions = collect([
        (object)[
            'id' => 101,
            'question_text' => 'ما هو سبب نزول سورة الكهف؟',
            'answer_text' => 'نزلت رداً على أسئلة قريش عن أهل الكهف وذي القرنين والروح.',
            'book_name' => 'تفسير ابن كثير',
            'page_number' => '45'
        ],
        (object)[
            'id' => 102,
            'question_text' => 'تحدث عن معاني الإيمان في سورة العصر.',
            'answer_text' => 'تشمل الإيمان والعمل الصالح والتواصي بالحق والتواصي بالصبر.',
            'book_name' => 'تفسير الطبري',
            'page_number' => '12'
        ]
    ]);

    return view('mosabka::judgings.tafseer.index', [
        'participant_id' => 123,
        'competition_version_branch_id' => 456,
        'is_head' => true,
        'is_edit_mode' => false,
        'type' => 'interpretation',
        'revealedQuestionIds' => collect([101]),
        'totalScore' => 0,
        'gradeQuestion' => 10.0,
        'alerts_before_fat7' => 3,
        'alert_penalty' => 0.5,
        'fat7_penalty' => 2.0,
        'InterpretationQuestion' => $mockQuestions,
        'notes' => collect([]),
        'judging_form_setting_id' => 789,
        'existingEvaluations' => collect([]),
        'questionFieldName' => 'question_id',
        'participant_name' => 'متسابق تجريبي',
        'juzz_range' => '1-5',
        'next_field_name' => null,
        'studentDetail' => (object)[
            'competitionVersionBranch' => (object)[
                'name' => 'فرع التفسير',
                'competitionVersion' => (object)[
                    'version_name' => 'المسابقة الكبرى'
                ]
            ],
            'competitionParticipant' => (object)[
                'full_name' => 'متسابق تجريبي'
            ]
        ]
    ]);
})->name('judgings.tafseer.index');

Route::post('/tafseer/store', function () {
    return response()->json(['success' => true, 'message' => 'Saved successfully']);
})->name('judgings.tafseer.store');

Route::get('/quran', function () {
    // Mock user for header
    $user = new \App\Models\User(['full_name' => 'المستخدم التجريبي (قرآن)']);
    Auth::setUser($user);

    $mockQuestions = [
        [
            'question' => (object)[
                'id' => 1,
                'surah' => 'البقرة',
                'surah_number' => 2,
                'question_text' => 'الم (1) ذَلِكَ الْكِتَابُ لَا رَيْبَ فِيهِ هُدًى لِلْمُتَّقِينَ (2)',
                'start_ayah_number' => 1,
                'end_ayah_number' => 5,
                'end_surah' => 'البقرة'
            ],
            'ayahs' => [],
            'pages' => [],
            'page' => 1,
            'page_range' => '1-1',
            'highlight' => []
        ],
        [
            'question' => (object)[
                'id' => 2,
                'surah' => 'آل عمران',
                'surah_number' => 3,
                'question_text' => 'الم (1) اللَّهُ لَا إِلَهَ إِلَّا هُوَ الْحَيُّ الْقَيُّومُ (2)',
                'start_ayah_number' => 1,
                'end_ayah_number' => 5,
                'end_surah' => 'آل عمران'
            ],
            'ayahs' => [],
            'pages' => [],
            'page' => 50,
            'page_range' => '50-50',
            'highlight' => []
        ]
    ];

    $notes = collect([
        (object)['id' => 1, 'note' => 'خطأ في التجويد'],
        (object)['id' => 2, 'note' => 'لحن جلي'],
        (object)['id' => 3, 'note' => 'تنبيه (فتح)'],
    ]);

    return view('mosabka::judgings.quran.index', [
        'total_score' => 100,
        'participant_id' => 1,
        'competition_version_branch_id' => 1,
        'alert_before_fat7' => 3,
        'alert_new_position_penalty' => 0.5,
        'fat7_penalty' => 2.0,
        'tajweed_score' => 50,
        'performance_score' => 50,
        'tajweed_per_question' => 10,
        'tajweed_penalty' => 1,
        'performance_per_question' => 10,
        'performance_penalty' => 1,
        'alert_same_position_penalty' => 0.5,
        'alert_penalty' => 0.5,
        'questions_count' => count($mockQuestions),
        'score_per_question' => 100,
        'notes' => $notes,
        'questions' => $mockQuestions,
        'judging_form_setting_id' => 999,
        'is_edit_mode' => false,
        'is_head' => true,
        'revealedQuestionIds' => collect([1, 2]),
        'currentIndex' => 0,
        'next_field_name' => 'interpretation',
        'existingEvaluations' => collect([]),
        'studentDetail' => (object)[
            'competitionVersionBranch' => (object)[
                'name' => 'فرع القرآن الكريم',
                'competitionVersion' => (object)[
                    'version_name' => 'المسابقة الكبرى'
                ]
            ],
            'competitionParticipant' => (object)[
                'full_name' => 'متسابق تجريبي'
            ]
        ],
        'participant_name' => 'متسابق تجريبي',
        'juzz_range' => '1-30',
        'type' => 'quran',
    ]);
});

Route::post('/quran/store', function () {
    return response()->json(['success' => true, 'message' => 'تم الحفظ بنجاح', 'redirect' => route('judgings.index')]);
})->name('quranjudgings.store');
