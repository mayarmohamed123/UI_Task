<script>
    // Initialize window.allAnswers IMMEDIATELY (before DOMContentLoaded) to ensure it's available early
    (function() {
        const totalQuestions = {{ count($InterpretationQuestion ?? []) }};
        const participantId = {{ $participant_id }};

        // Initialize window.allAnswers array immediately
        window.allAnswers = [];

        // Load from localStorage synchronously
        for (let i = 0; i < totalQuestions; i++) {
            try {
                const key = `judging-tafseer-data-${participantId}-${i}`;
                const data = localStorage.getItem(key);
                if (data) {
                    const parsed = JSON.parse(data);
                    window.allAnswers.push(parsed);
                    console.log(`[Tafseer Early Init] ✅ Loaded question ${i} from localStorage:`, {
                        hasNoteIds: !!parsed.note_ids,
                        hasNoteTexts: !!parsed.note_texts,
                        noteIdsLength: parsed.note_ids ? (Array.isArray(parsed.note_ids) ? parsed.note_ids.length : 0) : 0,
                        noteTextsLength: parsed.note_texts ? (Array.isArray(parsed.note_texts) ? parsed.note_texts.length : 0) : 0
                    });
                } else {
                    window.allAnswers.push(null);
                }
            } catch (e) {
                console.error(`[Tafseer Early Init] ❌ Error loading question ${i}:`, e);
                window.allAnswers.push(null);
            }
        }

        console.log('[Tafseer Early Init] ✅ window.allAnswers initialized early with', window.allAnswers.length, 'questions');
    })();

    document.addEventListener("DOMContentLoaded", function () {

        // Make sure these variables are available globally
        window.currentIndex = 0;
        window.totalQuestions = {{ count($InterpretationQuestion ?? []) }};
        window.questionModified = []; // Track if each question has been modified
        for (let i = 0; i < window.totalQuestions; i++) {
            window.questionModified.push(false);
        }
        
        // اسم المجال التالي للفروع متعددة المجالات - يجب أن يكون متاحاً بشكل عام
        window.nextFieldName = @json($next_field_name ?? null);
        console.log('[Tafseer Footer] Next field name from backend:', window.nextFieldName);
        console.log('[Tafseer Footer] Next field name type:', typeof window.nextFieldName);
        console.log('[Tafseer Footer] Next field name is null?', window.nextFieldName === null);
        console.log('[Tafseer Footer] Next field name is undefined?', window.nextFieldName === undefined);

        // ═══════════════════════════════════════════════════════════════
        // localStorage Helper Functions for Hadith
        // ═══════════════════════════════════════════════════════════════
        function getLocalStorageKey(index) {
            return `judging-tafseer-data-{{ $participant_id }}-${index}`;
        }

        function saveToLocalStorage(index, data) {
            try {
                const key = getLocalStorageKey(index);
                console.log(`[Tafseer Storage] 💾 Saving data for question ${index}:`, {
                    key: key,
                    note_ids: data.note_ids,
                    note_texts: data.note_texts,
                    note_idsLength: data.note_ids ? (Array.isArray(data.note_ids) ? data.note_ids.length : 0) : 0,
                    note_textsLength: data.note_texts ? (Array.isArray(data.note_texts) ? data.note_texts.length : 0) : 0,
                    alert_before_fat7: data.alert_before_fat7,
                    fat7_points: data.fat7_points
                });
                localStorage.setItem(key, JSON.stringify(data));
                console.log(`[Tafseer Storage] ✅ Successfully saved to localStorage with key:`, key);
            } catch (e) {
                console.error('[Tafseer Storage] ❌ Failed to save to localStorage:', e);
            }
        }

        function loadFromLocalStorage(index) {
            try {
                const key = getLocalStorageKey(index);
                console.log(`[Tafseer Storage] 🔍 Loading from localStorage:`, {
                    index: index,
                    key: key
                });
                const data = localStorage.getItem(key);
                console.log(`[Tafseer Storage] 📦 Raw data from localStorage:`, data ? 'exists' : 'not found');

                if (data) {
                    const parsed = JSON.parse(data);
                    console.log(`[Tafseer Storage] ✅ Parsed data:`, {
                        hasNoteIds: !!parsed.note_ids,
                        hasNoteTexts: !!parsed.note_texts,
                        note_ids: parsed.note_ids,
                        note_texts: parsed.note_texts,
                        note_idsLength: parsed.note_ids ? (Array.isArray(parsed.note_ids) ? parsed.note_ids.length : 0) : 0,
                        note_textsLength: parsed.note_texts ? (Array.isArray(parsed.note_texts) ? parsed.note_texts.length : 0) : 0,
                        alert_before_fat7: parsed.alert_before_fat7,
                        fat7_points: parsed.fat7_points
                    });
                    return parsed;
                }

                console.log(`[Tafseer Storage] ⚠️ No data found for key:`, key);
                return null;
            } catch (e) {
                console.error('[Tafseer Storage] ❌ Failed to load from localStorage:', e);
                return null;
            }
        }

        function clearLocalStorage() {
            try {
                // Clear all question data
                for (let i = 0; i < window.totalQuestions; i++) {
                    const key = getLocalStorageKey(i);
                    localStorage.removeItem(key);
                }
                console.log('[Tafseer Storage] Cleared all judging data from localStorage');
            } catch (e) {
                console.warn('[Tafseer Storage] Failed to clear localStorage:', e);
            }
        }

        // Make functions globally available
        window.saveToLocalStorage = saveToLocalStorage;
        window.loadFromLocalStorage = loadFromLocalStorage;
        window.clearLocalStorage = clearLocalStorage;

        // Toggle new note input
        document.querySelectorAll(".add-note-btn").forEach(button => {
            button.addEventListener("click", function (e) {
                e.preventDefault();
                let wrapper = this.closest(".mb-4").querySelector(".new-note-wrapper");
                wrapper.classList.toggle("hidden");
            });
        });

        // Removed bulk save button and handler as requested

        // Navigation buttons
        const prevBtn = document.querySelector(".prev-btn");
        const nextBtn = document.querySelector(".next-btn");
        const footerText = document.getElementById("footer-text");

        // Initialize the footer text
        updateFooterText();
        let isTransitioning = false;

        // 1. تحديث دالة switchToQuestion لضمان الترتيب الصحيح
        function switchToQuestion(index) {
            if (index < 0 || index >= window.totalQuestions || isTransitioning) return;
        
            isTransitioning = true; // تفعيل الحماية
        
            console.log('[Tafseer Footer Switch] 🔄 switchToQuestion called', {
                index: index,
                IS_HEAD: window.IS_HEAD,
                totalQuestions: window.totalQuestions
            });
        
            // 1. حفظ السؤال الحالي (قبل الانتقال) باستخدام الفهرس الحالي
            const oldIndex = window.currentIndex !== undefined ? window.currentIndex : 0;
            if (oldIndex !== index) {
                console.log('[Tafseer Footer Switch] Saving current question before switching:', {
                    oldIndex: oldIndex,
                    newIndex: index
                });
                saveCurrentAnswerForIndex(oldIndex);
            }
        
            // 2. تغيير المؤشر (Index)
            window.currentIndex = index;
        
            // 3. تحديث الواجهة (إظهار السطر الجديد في القائمة والخطوات)
            if (typeof updateView === 'function') {
                updateView(); 
            }
        
            // 4. تحميل بيانات السؤال الجديد (مع تصفير الحقل إجبارياً إذا كان جديداً)
            loadQuestionData(index);
            
            // 5. Auto-reveal question for head of committee when switching to it
            // الأسئلة تظهر تلقائياً لأعضاء اللجنة بمجرد انتقال رئيس اللجنة إليها
            if (window.IS_HEAD) {
                const questionsData = document.querySelectorAll('#questions-data > div');
                const qId = questionsData[index] ? parseInt(questionsData[index].dataset.questionId) : null;
                const currentRevealedIds = window.revealedQuestionIds || [];
                const revealedIdsArray = Array.isArray(currentRevealedIds) ? currentRevealedIds : [];
                const isAlreadyRevealed = qId && revealedIdsArray.includes(qId);
                
                console.log('[Tafseer Footer Switch] 🔍 Auto-reveal check', {
                    IS_HEAD: window.IS_HEAD,
                    qId: qId,
                    index: index,
                    currentRevealedIds: revealedIdsArray,
                    isAlreadyRevealed: isAlreadyRevealed,
                    willReveal: qId && !isAlreadyRevealed,
                    autoRevealFunctionExists: typeof window.autoRevealQuestionForHead === 'function'
                });
                
                if (qId && !isAlreadyRevealed) {
                    console.log('[Tafseer Footer Switch] 🔓 Head switched to question, auto-revealing...', { 
                        qId, 
                        index
                    });
                    
                    // Automatically reveal the question without needing button click
                    setTimeout(() => {
                        if (typeof window.autoRevealQuestionForHead === 'function') {
                            console.log('[Tafseer Footer Switch] ✅ Calling autoRevealQuestionForHead');
                            window.autoRevealQuestionForHead(qId);
                        } else {
                            console.warn('[Tafseer Footer Switch] ⚠️ autoRevealQuestionForHead function not available yet, will retry...');
                            // Retry after a short delay in case the function isn't loaded yet
                            setTimeout(() => {
                                if (typeof window.autoRevealQuestionForHead === 'function') {
                                    console.log('[Tafseer Footer Switch] ✅ Calling autoRevealQuestionForHead (retry)');
                                    window.autoRevealQuestionForHead(qId);
                                } else {
                                    console.error('[Tafseer Footer Switch] ❌ autoRevealQuestionForHead function not found after retry');
                                }
                            }, 500);
                        }
                    }, 300);
                } else if (qId && isAlreadyRevealed) {
                    console.log('[Tafseer Footer Switch] ⏭️ Question already revealed, skipping auto-reveal', { qId });
                } else if (!qId) {
                    console.warn('[Tafseer Footer Switch] ⚠️ Cannot auto-reveal: qId is null', { index });
                }
            }
            
            // 6. تحديث حالة الزر (رئيس اللجنة)
            if (typeof window.updateRevealButtonState === 'function') {
                setTimeout(() => window.updateRevealButtonState(), 50);
            }
        
            // فك الحماية بعد استقرار الواجهة
            setTimeout(() => {
                isTransitioning = false;
            }, 100);
        }
        
        // 2. تحديث دالة loadQuestionData في الفوتر لتتطابق مع منطق الاستقلال
        function loadQuestionData(index) {
            const currentForm = document.getElementById('current-answer-form');
            if (!currentForm) return;
        
            const maxScorePerQ = parseFloat("{{ $gradeQuestion }}") || 10;
            const scoreInput = document.getElementById('score-input');
            
            // جلب البيانات المحفوظة من المصفوفة العالمية لهذا السؤال المحدد فقط
            // نستخدم الفهرس المحدد وليس window.currentIndex
            let savedData = (window.allAnswers && window.allAnswers[index]) ? window.allAnswers[index] : null;
            
            console.log('[Tafseer loadQuestionData] Loading question data', {
                index: index,
                currentIndex: window.currentIndex,
                hasSavedData: !!savedData,
                savedScore: savedData ? savedData.score : null,
                maxScorePerQ: maxScorePerQ
            });
        
            if (scoreInput) {
                // المنطق: إذا كان هناك درجة مخزنة سابقاً لهذا السؤال تحديداً، اعرضها
                // إذا لم يكن هناك بيانات محفوظة أو لم تكن هناك درجة محفوظة، ابدأ من الدرجة الكاملة
                // Important: Explicitly check for null/undefined/empty string, but allow 0 values (0 is a valid score)
                const hasValidScore = savedData && 
                    typeof savedData.score !== 'undefined' && 
                    savedData.score !== null && 
                    savedData.score !== '';
                
                if (hasValidScore) {
                    // استخدم الدرجة المحفوظة لهذا السؤال المحدد (حتى لو كانت 0)
                    scoreInput.value = savedData.score;
                    console.log('[Tafseer loadQuestionData] ✅ Using saved score:', savedData.score);
                } else {
                    // غير ذلك، ابدأ من الدرجة الكاملة (لا توريث من أسئلة أخرى)
                    scoreInput.value = maxScorePerQ;
                    console.log('[Tafseer loadQuestionData] ✅ Using max score (no saved data):', maxScorePerQ);
                    
                    // تهيئة كائن السؤال في المصفوفة بالدرجة الكاملة فقط إذا لم يكن موجوداً
                    // لا نكتب قيمة في المصفوفة هنا حتى لا نحفظ قيمة افتراضية إذا لم يتم تعديلها
                }
            }
        
            // تحميل الملاحظات المحفوظة للسؤال الحالي فقط
            if (typeof window.loadSavedNotes === 'function') {
                window.loadSavedNotes(index);
            }
        }

        // Navigation functions
        function goToPrevious(e) {
            if (e) e.preventDefault();
            if (window.currentIndex > 0) {
                // Force save current data before navigation
                saveCurrentAnswerForIndex(window.currentIndex, true);
                window.currentIndex--;

                // Check if question is revealed and rebuild if needed
                const IS_HEAD = window.IS_HEAD || false;
                const questionsData = document.querySelectorAll('#questions-data > div');
                const qId = questionsData[window.currentIndex] ? parseInt(questionsData[window.currentIndex].dataset.questionId) : null;
                const currentRevealedIds = window.revealedQuestionIds || [];
                const isRevealed = IS_HEAD || (qId && currentRevealedIds.includes(qId));

                updateView();
                loadQuestionData(window.currentIndex);

                // If question is revealed, rebuild content
                if (isRevealed && !IS_HEAD && typeof window.rebuildQuestionContent === 'function') {
                    setTimeout(() => {
                        window.rebuildQuestionContent(window.currentIndex);
                        setTimeout(() => {
                            const questionDiv = document.getElementById('question-' + window.currentIndex);
                            if (questionDiv) {
                                const lockedView = questionDiv.querySelector('.unified-content');
                                if (lockedView) lockedView.remove();
                                const lockMsg = document.getElementById('member-locked-warning');
                                if (lockMsg) lockMsg.classList.add('hidden');
                                const formContent = document.getElementById('current-form-content');
                                if (formContent) formContent.classList.remove('hidden');
                            }
                        }, 100);
                    }, 200);
                }

                // Ensure reveal button reflects the new question state
                if (typeof window.updateRevealButtonState === 'function') {
                    setTimeout(() => window.updateRevealButtonState(), 100);
                }
            }
        }

        function showSaveConfirmation() {
            const modal = document.getElementById('save-confirmation-modal');
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        }

        function hideSaveConfirmation() {
            const modal = document.getElementById('save-confirmation-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function goToNext(e) {
            if (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            console.log('[Tafseer Footer goToNext] Called. currentIndex:', window.currentIndex, 'totalQuestions:', window.totalQuestions);

            // Use switchToQuestion from index.blade.php if available, otherwise use local logic
            if (typeof window.switchToQuestion === 'function') {
                if (window.currentIndex < window.totalQuestions - 1) {
                    console.log('[Tafseer Footer goToNext] Using switchToQuestion from index.blade.php');
                    window.switchToQuestion(window.currentIndex + 1);
                } else if (window.currentIndex === window.totalQuestions - 1) {
                    console.log('[Tafseer Footer goToNext] Last question - showing save confirmation');
                    // Force save current data before showing save confirmation
                    saveCurrentAnswerForIndex(window.currentIndex, true);
                    showSaveConfirmation();
                }
                return;
            }

            // Fallback to local logic if switchToQuestion is not available
            if (window.currentIndex < window.totalQuestions - 1) {
                // Force save current data before navigation
                saveCurrentAnswerForIndex(window.currentIndex, true);
                window.currentIndex++;

                // Check if question is revealed and rebuild if needed
                const IS_HEAD = window.IS_HEAD || false;
                const questionsData = document.querySelectorAll('#questions-data > div');
                const qId = questionsData[window.currentIndex] ? parseInt(questionsData[window.currentIndex].dataset.questionId) : null;
                const currentRevealedIds = window.revealedQuestionIds || [];
                const isRevealed = IS_HEAD || (qId && currentRevealedIds.includes(qId));

                updateView();
                loadQuestionData(window.currentIndex);

                // If question is revealed, rebuild content
                if (isRevealed && !IS_HEAD && typeof window.rebuildQuestionContent === 'function') {
                    setTimeout(() => {
                        window.rebuildQuestionContent(window.currentIndex);
                        setTimeout(() => {
                            const questionDiv = document.getElementById('question-' + window.currentIndex);
                            if (questionDiv) {
                                const lockedView = questionDiv.querySelector('.unified-content');
                                if (lockedView) lockedView.remove();
                                const lockMsg = document.getElementById('member-locked-warning');
                                if (lockMsg) lockMsg.classList.add('hidden');
                                const formContent = document.getElementById('current-form-content');
                                if (formContent) formContent.classList.remove('hidden');
                            }
                        }, 100);
                    }, 200);
                } else if (IS_HEAD && isRevealed) {
                    // إذا كان السؤال الحالي مكشوفاً كرئيس، فكّ قفل الواجهة فوراً
                    setTimeout(() => {
                        const lockMsg = document.getElementById('member-locked-warning');
                        if (lockMsg) { lockMsg.classList.add('hidden'); }
                        const formContent = document.getElementById('current-form-content');
                        if (formContent) { formContent.classList.remove('hidden'); }
                    }, 50);
                }

                // Ensure reveal button reflects the new question state
                if (typeof window.updateRevealButtonState === 'function') {
                    setTimeout(() => window.updateRevealButtonState(), 100);
                }
            } else if (window.currentIndex === window.totalQuestions - 1) {
                // Force save current data before showing save confirmation
                saveCurrentAnswerForIndex(window.currentIndex, true);
                showSaveConfirmation();
            }
        }

        function updateView() {
            // For members: rebuild revealed questions BEFORE showing them
            // But only rebuild the CURRENT question, not all revealed questions
            if (!window.IS_HEAD) {
                const questionsData = document.querySelectorAll('#questions-data > div');
                if (questionsData[window.currentIndex]) {
                    const qId = parseInt(questionsData[window.currentIndex].dataset.questionId);
                    const revealedIds = window.revealedQuestionIds || [];
                    const isRevealed = revealedIds.includes(qId);

                    console.log('[Tafseer UpdateView] Checking question', {
                        index: window.currentIndex,
                        qId: qId,
                        isRevealed: isRevealed
                    });

                    // Only rebuild the current question if it's revealed and not already unlocked
                    if (isRevealed && typeof window.rebuildQuestionContent === 'function') {
                        const questionDiv = document.getElementById('question-' + window.currentIndex);
                        if (questionDiv) {
                            const hasUnlockedView = questionDiv.querySelector('.question-content') !== null;
                            const hasLockedView = questionDiv.querySelector('.unified-content') !== null;

                            // Only rebuild if it's still locked
                            if (hasLockedView && !hasUnlockedView) {
                                console.log('[Tafseer UpdateView] Rebuilding current question before show');
                                // استخدام setTimeout للتأكد من أن العناصر جاهزة
                                setTimeout(() => {
                                    window.rebuildQuestionContent(window.currentIndex);
                                    // إزالة رسالة "في انتظار رئيس اللجنة" وإظهار المحتوى
                                    setTimeout(() => {
                                        const lockedView = questionDiv.querySelector('.unified-content');
                                        if (lockedView) {
                                            lockedView.remove();
                                        }
                                        const lockMsg = document.getElementById('member-locked-warning');
                                        if (lockMsg) {
                                            lockMsg.classList.add('hidden');
                                        }
                                        const formContent = document.getElementById('current-form-content');
                                        if (formContent) {
                                            formContent.classList.remove('hidden');
                                        }
                                    }, 50);
                                }, 100);
                            } else if (hasUnlockedView) {
                                // إذا كان المحتوى مفتوحاً بالفعل، تأكد من إظهار النموذج
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
                            }
                        }
                    }
                }
            }

            // Update the active question (AFTER rebuild)
            const steps = document.querySelectorAll("[data-step]");
            steps.forEach((step, index) => {
                const shouldShow = index === window.currentIndex;
                step.classList.toggle("hidden", !shouldShow);
                step.classList.toggle("fade-in", shouldShow);
            });

            // For HEAD: Ensure reveal button exists in current question (like Quran)
            if (window.IS_HEAD) {
                const currentQuestionDiv = document.getElementById('question-' + window.currentIndex);
                if (currentQuestionDiv) {
                    let revealContainer = currentQuestionDiv.querySelector('#reveal-btn-in-question');

                    // If reveal container doesn't exist, add it to the question header
                    if (!revealContainer) {
                        const questionContent = currentQuestionDiv.querySelector('.question-content');
                        if (questionContent) {
                            const questionHeader = questionContent.querySelector('.mb-8 > .flex.items-center');
                            if (questionHeader && !questionHeader.classList.contains('justify-between')) {
                                questionHeader.classList.add('justify-between');
                                revealContainer = document.createElement('div');
                                revealContainer.id = 'reveal-btn-in-question';
                                questionHeader.appendChild(revealContainer);
                            }
                        }
                    }

                    // Inject button if container exists but button doesn't
                    if (revealContainer && !revealContainer.querySelector('#reveal-question-btn')) {
                        revealContainer.innerHTML = `
                            <button id="reveal-question-btn" type="button" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5">
                                <i class="fas fa-eye text-xs"></i>
                                <span>إظهار للجميع</span>
                            </button>`;
                    }

                    // Update button state
                    if (typeof window.updateRevealButtonState === 'function') {
                        setTimeout(() => window.updateRevealButtonState(), 50);
                    }
                }
            }

            // Update the footer text
            updateFooterText();

            // Update button states
            updateButtonStates();

            // Update question highlighting in sidebar
            updateQuestionHighlight();
        }

        function updateFooterText() {
            if (footerText) {
                footerText.textContent = `السؤال ${window.currentIndex + 1} من أصل ${window.totalQuestions}`;
            }
        }

        function updateButtonStates() {
            if (prevBtn) {
                prevBtn.disabled = window.currentIndex === 0;
                prevBtn.classList.toggle('opacity-50', window.currentIndex === 0);
            }
            
            if (nextBtn) {
                const isLast = window.currentIndex === window.totalQuestions - 1;
                const revealedCount = window.revealedQuestionIds ? window.revealedQuestionIds.length : 0;
                
                // إذا كان العضو في السؤال الأخير ولكن لم تظهر كل الأسئلة بعد
                if (isLast && !window.IS_HEAD && (revealedCount < window.totalQuestions)) {
                    nextBtn.innerHTML = 'بانتظار إظهار الأسئلة... <i class="fas fa-lock ms-2"></i>';
                    nextBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    nextBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    nextBtn.disabled = true;
                } else {
                    nextBtn.disabled = false;
                    nextBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    nextBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    if (isLast) {
                        // إذا كان هناك مجال تالي في الفرع متعدد المجالات، اعرض "حفظ والانتقال لمجال [اسم المجال]"
                        console.log('[Tafseer Footer] Updating save button text', {
                            isLast: isLast,
                            nextFieldName: window.nextFieldName,
                            hasNextField: !!window.nextFieldName
                        });
                        if (window.nextFieldName) {
                            nextBtn.innerHTML = `حفظ والانتقال لمجال ${window.nextFieldName} <i class="fas fa-save ms-2"></i>`;
                        } else {
                            nextBtn.innerHTML = 'إنهاء وحفظ <i class="fas fa-save ms-2"></i>';
                        }
                    } else {
                        nextBtn.innerHTML = 'التالي <i class="fas fa-arrow-left ms-2"></i>';
                    }
                }
            }
        }

        function updateQuestionHighlight() {
            const questionItems = document.querySelectorAll('.question-item');
            questionItems.forEach((item) => {
                const itemIndex = parseInt(item.getAttribute('data-question-index') || '-1');
                const numberBadge = item.querySelector('.flex-shrink-0');
                const titleElement = item.querySelector('h4');

                if (itemIndex === window.currentIndex) {
                    // Selected state (like Quran)
                    item.classList.add('bg-secondary', 'text-white', 'shadow-sm', 'border-secondary');
                    item.classList.remove('bg-slate-50', 'text-slate-600', 'border-slate-200');
                    if (numberBadge) {
                        numberBadge.classList.add('bg-white', 'text-secondary');
                        numberBadge.classList.remove('bg-slate-200', 'text-slate-500');
                    }
                    if (titleElement) {
                        titleElement.classList.add('text-blue-100');
                        titleElement.classList.remove('text-slate-800');
                    }
                } else {
                    // Unselected state (like Quran)
                    item.classList.remove('bg-secondary', 'text-white', 'shadow-sm', 'border-secondary');
                    item.classList.add('bg-slate-50', 'text-slate-600', 'border-slate-200');
                    if (numberBadge) {
                        numberBadge.classList.remove('bg-white', 'text-secondary');
                        numberBadge.classList.add('bg-slate-200', 'text-slate-500');
                    }
                    if (titleElement) {
                        titleElement.classList.remove('text-blue-100');
                        titleElement.classList.add('text-slate-800');
                    }
                }
            });
        }

        // Event listeners for navigation
        if (prevBtn) {
            prevBtn.addEventListener("click", goToPrevious);
            prevBtn.addEventListener("keydown", function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    goToPrevious();
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                goToNext(e);
            });

            nextBtn.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    e.stopPropagation();
                    goToNext();
                }
            });
        }

        // Modal event listeners
        const cancelSaveBtn = document.getElementById('cancel-save-btn');
        const confirmSaveBtn = document.getElementById('confirm-save-btn');

        if (cancelSaveBtn) {
            cancelSaveBtn.addEventListener('click', hideSaveConfirmation);
        }

        if (confirmSaveBtn) {
            confirmSaveBtn.addEventListener('click', async function() {
                confirmSaveBtn.disabled = true;
                confirmSaveBtn.innerHTML = '<i class="fas fa-spinner fa-spin ms-2"></i> جاري الحفظ...';

                // استخدام try-catch شامل لضمان عدم ظهور أي error
                let result = null;
                try {
                    result = await saveAllEvaluations();
                } catch (error) {
                    // إذا حدث أي error، نحوله إلى result object
                    console.error('Error in saveAllEvaluations:', error);
                    const isInfoMessage = error.message && (error.message.includes('الانتظار') || error.message.includes('متبقي'));
                    result = {
                        success: false,
                        type: isInfoMessage ? 'info' : 'error',
                        title: isInfoMessage ? 'تنبيه' : 'خطأ',
                        message: error.message || 'حدث خطأ أثناء حفظ التقييم',
                        notificationType: isInfoMessage ? 'info' : 'error'
                    };
                }
                
                // إغلاق الـ modal وإعادة تعيين الزر أولاً دائماً
                confirmSaveBtn.disabled = false;
                confirmSaveBtn.innerHTML = '<i class="fas fa-save ms-2"></i> تأكيد الحفظ';
                hideSaveConfirmation();
                
                // معالجة النتيجة
                if (result && typeof result === 'object') {
                    // التحقق من نجاح الحفظ - يجب أن يكون success === true و redirect موجود
                    if (result.success === true && result.redirect && !result.type) {
                        // نجح الحفظ - redirect فقط إذا كان هناك redirect URL
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 1500);
                    } else if (result.success === false || result.type) {
                        // فشل الحفظ أو type موجود - عرض الرسالة فقط بدون إعادة توجيه
                        setTimeout(() => {
                            if (typeof showCustomNotification === 'function') {
                                showCustomNotification(
                                    result.title || 'تنبيه',
                                    result.message || 'حدث خطأ أثناء حفظ التقييم',
                                    result.notificationType || 'info',
                                    8000
                                );
                            }
                        }, 300);
                        // لا نعيد التوجيه - نبقى في نفس الصفحة
                        return;
                    } else if (result.success === true && !result.redirect) {
                        // نجح الحفظ لكن بدون redirect - لا نفعل شيء، نبقى في نفس الصفحة
                        if (typeof showCustomNotification === 'function') {
                            showCustomNotification('نجاح', result.message || 'تم حفظ التحكيم بنجاح', 'success', 3000);
                        }
                        return;
                    }
                } else if (result && (result === true || typeof result === 'string')) {
                    // إذا كان result === true أو string، نعيد التوجيه فقط إذا كان string
                    if (typeof result === 'string') {
                        setTimeout(() => {
                            window.location.href = result;
                        }, 1500);
                    } else {
                        // إذا كان result === true فقط، لا نعيد التوجيه
                        if (typeof showCustomNotification === 'function') {
                            showCustomNotification('نجاح', 'تم حفظ التحكيم بنجاح', 'success', 3000);
                        }
                        return;
                    }
                }
                // إذا كان result === null أو false أو undefined، لا نفعل شيء
            });
        }

        // Close modal when clicking outside
        const modal = document.getElementById('save-confirmation-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    hideSaveConfirmation();
                }
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(event) {
            // Only handle arrow keys if not in an input field
            if (event.target.tagName !== 'INPUT' && event.target.tagName !== 'TEXTAREA') {
                if (event.key === 'ArrowLeft') {
                    goToPrevious(event);
                } else if (event.key === 'ArrowRight') {
                    goToNext(event);
                }
            }
        });

        // تعديل دالة الحفظ لضمان حفظ الدرجة الرقمية
        function saveCurrentAnswerForIndex(index) {
            // نمنع الحفظ إذا كان الاندكس غير معرف أو خارج النطاق
            if (index === undefined || index === null || index < 0) return;
        
            const currentForm = document.getElementById('current-answer-form');
            const scoreInput = document.getElementById('score-input');
            if (!currentForm || !scoreInput) return;
        
            // قراءة الدرجة الحالية من الصندوق
            const currentScore = parseFloat(scoreInput.value) || 0;
            
            const questionsData = document.querySelectorAll('#questions-data > div');
            const actualQuestionId = questionsData[index] ? questionsData[index].dataset.questionId : null;
            
            console.log('[Tafseer Save] 💾 Saving answer for index:', {
                index: index,
                currentIndex: window.currentIndex,
                scoreInputValue: scoreInput.value,
                currentScore: currentScore,
                questionId: actualQuestionId
            });
        
            // تخزين البيانات "فقط" للاندكس المطلوب
            const answer = {
                question_id: actualQuestionId,
                score: currentScore,
                note_ids: JSON.parse(currentForm.querySelector('#note-ids')?.value || "[]"),
                note_texts: JSON.parse(currentForm.querySelector('#note-texts')?.value || "[]"),
                alert_before_fat7: currentForm.querySelector('[name="alert_before_fat7"]')?.value || 0,
                fat7_points: currentForm.querySelector('[name="fat7_points"]')?.value || 0
            };
        
            window.allAnswers[index] = answer;
            saveToLocalStorage(index, answer);
            
            console.log('[Tafseer Save] ✅ Saved answer for index', index, ':', {
                question_id: answer.question_id,
                score: answer.score
            });
            
            // تحديث إجمالي الدرجات في الهيدر
            if (typeof updateFinalScoreDisplayTafseer === 'function') {
                updateFinalScoreDisplayTafseer();
            }
        }

        // Save current answer function
        function saveCurrentAnswer() {
            console.log('[Tafseer Save] 🔄 saveCurrentAnswer called, currentIndex:', window.currentIndex);
            if (typeof window.currentIndex === 'undefined' || window.currentIndex === null) {
                console.warn('[Tafseer Save] ⚠️ currentIndex is undefined, using 0');
                window.currentIndex = 0;
            }
            saveCurrentAnswerForIndex(window.currentIndex);
        }

        // 1. دالة نقص الدرجة (النظيفة)
        function decreaseScore() {
            const scoreInput = document.getElementById('score-input');
            if (!scoreInput) return;
        
            const currentValue = parseFloat(scoreInput.value) || 0;
            const step = 0.5;
            const newValue = Math.max(0, currentValue - step);
            
            scoreInput.value = newValue; // تحديث الخانة الحالية فقط
            saveCurrentAnswer(); // حفظ في مصفوفة الأسئلة (allAnswers)
            updateFinalScoreDisplayTafseer(); // تحديث المجموع الكلي بالهيدر
        }
        
        // 2. دالة زيادة الدرجة (النظيفة)
        function increaseScore() {
            const scoreInput = document.getElementById('score-input');
            if (!scoreInput) return;
        
            const currentValue = parseFloat(scoreInput.value) || 0;
            const maxValue = parseFloat(scoreInput.max) || {{ $gradeQuestion ?? 10 }};
            const step = 0.5;
            const newValue = Math.min(maxValue, currentValue + step);
            
            scoreInput.value = newValue; // تحديث الخانة الحالية فقط
            saveCurrentAnswer(); 
            updateFinalScoreDisplayTafseer();
        }

        // Make functions global
        // Make switchToQuestion globally available and ensure it includes auto-reveal logic
        window.switchToQuestion = switchToQuestion;
        console.log('[Tafseer Footer] ✅ switchToQuestion set on window with auto-reveal support');
        window.updateView = updateView;
        window.saveCurrentAnswer = saveCurrentAnswer;
        window.saveCurrentAnswerForIndex = saveCurrentAnswerForIndex; // Make this available too
        window.loadQuestionData = loadQuestionData; // Make loadQuestionData available globally
        window.decreaseScore = decreaseScore;
        window.increaseScore = increaseScore;

        // Ensure allAnswers array is properly initialized (it should already be initialized early, but merge with any new data)
        if (!Array.isArray(window.allAnswers)) {
            window.allAnswers = [];
        }

        // Ensure allAnswers has the correct length
        while (window.allAnswers.length < window.totalQuestions) {
            window.allAnswers.push(null);
        }

        // ════════════════════════════════════════════════════════════════
        // Prepare data for loading saved evaluations from database
        // ════════════════════════════════════════════════════════════════
        @php
            $evaluationsArray = isset($existingEvaluations) && $existingEvaluations ? $existingEvaluations->toArray() : [];
            $questionFieldNameValue = isset($questionFieldName) ? $questionFieldName : null;
            $notesMapArray = isset($notes) && $notes ? $notes->keyBy('id')->map(function($note) { return $note->note; })->toArray() : [];
        @endphp
        @if(isset($questionFieldNameValue) && isset($notes))
            window.existingEvaluationsData = {
                evaluations: @json($evaluationsArray),
                questionFieldName: "{{ $questionFieldNameValue }}",
                notesMap: @json($notesMapArray)
            };
            console.log('[Tafseer Init] existingEvaluationsData prepared:', {
                evaluationsCount: @json(count($evaluationsArray)),
                questionFieldName: "{{ $questionFieldNameValue }}",
                notesCount: @json(count($notesMapArray))
            });
        @else
            console.log('[Tafseer Init] ⚠️ existingEvaluationsData not prepared - missing data:', {
                hasQuestionFieldName: {{ isset($questionFieldName) ? 'true' : 'false' }},
                hasNotes: {{ isset($notes) ? 'true' : 'false' }}
            });
        @endif

        // ════════════════════════════════════════════════════════════════
        // Function to load saved evaluations from database
        // This must run AFTER DOM is ready to access question elements
        // ════════════════════════════════════════════════════════════════
        function loadSavedEvaluationsFromDatabase() {
            if (!window.existingEvaluationsData) {
                console.log('[Tafseer Init] No existingEvaluationsData available');
                return false;
            }

            const evaluations = window.existingEvaluationsData.evaluations;
            const questionFieldName = window.existingEvaluationsData.questionFieldName;
            const notesMap = window.existingEvaluationsData.notesMap || {};
            const questionsData = document.querySelectorAll('#questions-data > div');
            
            // Ensure evaluations is an array
            let evaluationsArray = [];
            if (evaluations) {
                if (Array.isArray(evaluations)) {
                    evaluationsArray = evaluations;
                } else if (typeof evaluations === 'object') {
                    evaluationsArray = Object.values(evaluations);
                }
            }
            
            const isEditMode = window.location.search.includes('edit_start_field');
            
            console.log('[Tafseer Init] Loading existing evaluations from database:', {
                evaluationsCount: evaluationsArray.length,
                questionFieldName: questionFieldName,
                notesCount: notesMap ? Object.keys(notesMap).length : 0,
                questionsDataLength: questionsData.length,
                evaluationsType: Array.isArray(evaluations) ? 'array' : typeof evaluations,
                evaluations: evaluationsArray,
                isEditMode: isEditMode
            });
            
            if (questionsData.length === 0) {
                console.warn('[Tafseer Init] ⚠️ questionsData is empty, DOM may not be ready yet');
                return false;
            }
            
            // Map existing evaluations by question_id
            const evaluationsByQuestionId = {};
            evaluationsArray.forEach(eval => {
                const questionId = eval[questionFieldName];
                if (questionId) {
                    evaluationsByQuestionId[questionId] = eval;
                }
            });
            
            console.log('[Tafseer Init] Mapped evaluations by question_id:', {
                evaluationsByQuestionId: evaluationsByQuestionId,
                questionIds: Object.keys(evaluationsByQuestionId).map(id => ({
                    id: parseInt(id),
                    score: evaluationsByQuestionId[id].score
                }))
            });
            
            let loadedCount = 0;
            
            // Load saved scores into allAnswers array
            // Log the order of questions in DOM
            const questionsOrder = Array.from(questionsData).map((div, idx) => ({
                index: idx,
                questionId: parseInt(div.dataset.questionId)
            }));
            console.log('[Tafseer Init] Questions order in DOM:', questionsOrder);
            
            questionsData.forEach((questionDiv, index) => {
                const questionId = parseInt(questionDiv.dataset.questionId);
                if (questionId && evaluationsByQuestionId[questionId]) {
                    const evaluation = evaluationsByQuestionId[questionId];
                    
                    // Use the saved SCORE from database (not the calculated final score)
                    // This is the score the judge gave, before any deductions
                    // Important: Check for null/undefined explicitly, not using || to preserve 0 values
                    const savedScore = (evaluation.score !== null && evaluation.score !== undefined) 
                        ? parseFloat(evaluation.score) 
                        : null;
                    
                    console.log(`[Tafseer Init] Found saved evaluation for question ${index}:`, {
                        questionId: questionId,
                        savedScore: savedScore,
                        evaluation: evaluation
                    });
                    
                    // Get note text from note_id if available
                    let noteIds = [];
                    let noteTexts = [];
                    if (evaluation.note_id) {
                        const noteId = parseInt(evaluation.note_id);
                        noteIds = [noteId];
                        // Get note text from notesMap
                        if (notesMap[noteId]) {
                            noteTexts = [notesMap[noteId]];
                        }
                    }
                    
                    // Build answer object with saved data
                    const answer = {
                        question_id: questionId,
                        score: savedScore, // This is the actual score saved by the judge
                        note_ids: noteIds,
                        note_texts: noteTexts,
                        alert_before_fat7: evaluation.alert_new_position ? parseFloat(evaluation.alert_new_position) : 0,
                        fat7_points: evaluation.fat7 ? parseFloat(evaluation.fat7) : 0
                    };
                    
                    // In edit mode, database data has priority over localStorage
                    // localStorage should only be used for unsaved changes, not to override saved scores
                    const localStorageData = loadFromLocalStorage(index);
                    const isEditMode = window.location.search.includes('edit_start_field');
                    
                    if (isEditMode) {
                        // Edit mode: Use database data as primary source (it's the saved truth)
                        // Only merge non-score fields from localStorage if they exist
                        if (localStorageData && localStorageData.question_id === questionId) {
                            // Merge localStorage data but keep database score
                            window.allAnswers[index] = {
                                ...answer,
                                // Keep database score, but allow localStorage to update other fields if needed
                                score: savedScore, // Force use database score
                                note_ids: localStorageData.note_ids && localStorageData.note_ids.length > 0 ? localStorageData.note_ids : noteIds,
                                note_texts: localStorageData.note_texts && localStorageData.note_texts.length > 0 ? localStorageData.note_texts : noteTexts
                            };
                            console.log(`[Tafseer Init] ✅ Edit mode - DB score priority for question ${index}:`, {
                                questionId: questionId,
                                dbScore: savedScore,
                                localStorageScore: localStorageData.score,
                                finalScore: window.allAnswers[index].score,
                                usingDbScore: true
                            });
                        } else {
                            // Use database data directly
                            window.allAnswers[index] = answer;
                            console.log(`[Tafseer Init] ✅ Edit mode - Loaded DB evaluation for question ${index}:`, {
                                questionId: questionId,
                                score: savedScore,
                                noteIds: noteIds,
                                noteTexts: noteTexts
                            });
                        }
                    } else {
                        // Normal mode: localStorage has priority for recent unsaved changes
                        if (localStorageData && localStorageData.score !== undefined && localStorageData.score !== null) {
                            // Merge: localStorage has priority for score if it exists
                            window.allAnswers[index] = { ...answer, ...localStorageData };
                            console.log(`[Tafseer Init] ✅ Normal mode - Merged DB + localStorage for question ${index}:`, {
                                dbScore: savedScore,
                                localStorageScore: localStorageData.score,
                                finalScore: window.allAnswers[index].score
                            });
                        } else {
                            // Use database data
                            window.allAnswers[index] = answer;
                            console.log(`[Tafseer Init] ✅ Normal mode - Loaded DB evaluation for question ${index}:`, {
                                questionId: questionId,
                                score: savedScore,
                                noteIds: noteIds,
                                noteTexts: noteTexts
                            });
                        }
                    }
                    
                    // Also save to localStorage for consistency (with correct data)
                    saveToLocalStorage(index, window.allAnswers[index]);
                    loadedCount++;
                }
            });
            
            console.log(`[Tafseer Init] ✅ Loaded ${loadedCount} evaluations from database`);
            return loadedCount > 0;
        }

        // Reload from localStorage to ensure we have the latest data (in case it was updated)
        // In edit mode, database data has priority - don't overwrite it with localStorage
        const isEditMode = window.location.search.includes('edit_start_field');
        
        for (let i = 0; i < window.totalQuestions; i++) {
            // Skip if we already loaded from database (to avoid overwriting)
            if (window.allAnswers[i] && typeof window.allAnswers[i] === 'object' && window.allAnswers[i].question_id && window.allAnswers[i].score !== undefined) {
                // In edit mode, if we have database data, don't overwrite with localStorage
                if (isEditMode) {
                    continue; // Skip - database data is the source of truth
                }
            }
            
            const savedData = loadFromLocalStorage(i);
            if (savedData) {
                // In edit mode, only use localStorage if we don't have database data
                if (isEditMode && window.allAnswers[i] && typeof window.allAnswers[i] === 'object' && window.allAnswers[i].question_id) {
                    // We have database data, skip localStorage
                    continue;
                }
                
                // Merge with existing data if it exists, otherwise replace
                if (window.allAnswers[i] && typeof window.allAnswers[i] === 'object') {
                    // Merge: keep existing data but update with localStorage data (only if not in edit mode with DB data)
                    window.allAnswers[i] = { ...window.allAnswers[i], ...savedData };
                } else {
                    window.allAnswers[i] = savedData;
                }
            } else if (window.allAnswers[i] === undefined) {
                window.allAnswers[i] = null;
            }
        }
        console.log('[Tafseer Init] ✅ allAnswers initialized/updated:', {
            length: window.allAnswers.length,
            hasData: window.allAnswers.some(a => a !== null),
            data: window.allAnswers
        });

        // Initialize the view
        updateView();

        // Load saved evaluations from database FIRST, then load question data
        // Add a delay to ensure DOM is ready
        setTimeout(() => {
            // Try to load saved evaluations from database with retry mechanism
            let retryCount = 0;
            const maxRetries = 5;
            
            const tryLoadEvaluations = () => {
                const evaluationsLoaded = loadSavedEvaluationsFromDatabase();
                
                if (evaluationsLoaded) {
                    console.log('[Tafseer Init] ✅ Database evaluations loaded successfully');
                    // Now load the first question data (will use saved scores if available)
                    loadQuestionData(0);
                } else if (retryCount < maxRetries) {
                    retryCount++;
                    console.log(`[Tafseer Init] ⚠️ Retrying to load evaluations (attempt ${retryCount}/${maxRetries})...`);
                    setTimeout(tryLoadEvaluations, 200);
                } else {
                    console.log('[Tafseer Init] ⚠️ No database evaluations found or DOM not ready after retries');
                    // Load question data anyway (will show default scores)
                    loadQuestionData(0);
                }
            };
            
            tryLoadEvaluations();

            // Also explicitly call loadSavedNotes after a short delay to ensure it runs
            setTimeout(() => {
                if (typeof window.loadSavedNotes === 'function') {
                    console.log('[Tafseer Footer] Explicitly calling loadSavedNotes after loadQuestionData');
                    window.loadSavedNotes(0);
                }
            }, 300);

            // Auto-reveal first question for head when page loads
            // إظهار السؤال الأول تلقائياً لرئيس اللجنة عند تحميل الصفحة
            setTimeout(() => {
                if (window.IS_HEAD) {
                    const questionsData = document.querySelectorAll('#questions-data > div');
                    if (questionsData.length > 0) {
                        const firstQuestionDiv = questionsData[0];
                        const firstQId = firstQuestionDiv ? parseInt(firstQuestionDiv.dataset.questionId) : null;
                        const currentRevealedIds = window.revealedQuestionIds || [];
                        const revealedIdsArray = Array.isArray(currentRevealedIds) ? currentRevealedIds : [];
                        const isAlreadyRevealed = firstQId && revealedIdsArray.includes(firstQId);

                        console.log('[Tafseer Init] 🔍 Auto-reveal first question check', {
                            IS_HEAD: window.IS_HEAD,
                            firstQId: firstQId,
                            currentRevealedIds: revealedIdsArray,
                            isAlreadyRevealed: isAlreadyRevealed,
                            willReveal: firstQId && !isAlreadyRevealed
                        });

                        if (firstQId && !isAlreadyRevealed) {
                            console.log('[Tafseer Init] 🔓 Head loaded page, auto-revealing first question...', { firstQId });
                            setTimeout(() => {
                                if (typeof window.autoRevealQuestionForHead === 'function') {
                                    console.log('[Tafseer Init] ✅ Calling autoRevealQuestionForHead for first question');
                                    window.autoRevealQuestionForHead(firstQId);
                                } else {
                                    console.warn('[Tafseer Init] ⚠️ autoRevealQuestionForHead not available yet, will retry...');
                                    setTimeout(() => {
                                        if (typeof window.autoRevealQuestionForHead === 'function') {
                                            console.log('[Tafseer Init] ✅ Calling autoRevealQuestionForHead for first question (retry)');
                                            window.autoRevealQuestionForHead(firstQId);
                                        } else {
                                            console.error('[Tafseer Init] ❌ autoRevealQuestionForHead not found after retry');
                                        }
                                    }, 1000);
                                }
                            }, 500);
                        } else if (firstQId && isAlreadyRevealed) {
                            console.log('[Tafseer Init] ⏭️ First question already revealed, skipping auto-reveal', { firstQId });
                        }
                    }
                }
            }, 800);
        }, 100);

        // Initialize questions data - keep them as null initially
        setTimeout(() => {
            // Ensure allAnswers is properly initialized with null values
            for (let i = 0; i < window.totalQuestions; i++) {
                if (window.allAnswers[i] === undefined) {
                    window.allAnswers[i] = null;
                }
            }

            // Load the first question data (will show default grade since no saved data)
            loadQuestionData(0);

            // Initialize notes for first question (like Quran)
            setTimeout(() => {
                console.log('[Tafseer Init] Loading saved notes for first question');
                const savedData = window.allAnswers && window.allAnswers[0] ? window.allAnswers[0] : null;

                if (savedData) {
                    const currentForm = document.getElementById('current-answer-form');
                    if (currentForm) {
                        const initNoteIdsField = currentForm.querySelector('#note-ids');
                        const initNoteTextsField = currentForm.querySelector('#note-texts');

                        if (initNoteIdsField && initNoteTextsField) {
                            try {
                                const savedNoteIds = savedData.note_ids ? (Array.isArray(savedData.note_ids) ? savedData.note_ids : JSON.parse(savedData.note_ids)) : [];
                                const savedNoteTexts = savedData.note_texts ? (Array.isArray(savedData.note_texts) ? savedData.note_texts : JSON.parse(savedData.note_texts)) : [];

                                initNoteIdsField.value = JSON.stringify(savedNoteIds);
                                initNoteTextsField.value = JSON.stringify(savedNoteTexts);

                                // Function to apply notes with retries
                                const applyNotesWithRetry = (retryCount = 0) => {
                                    const maxRetries = 5;

                                    // Update Select2 with saved notes
                                    if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
                                        const $select = window.jQuery('#unified-note-select');
                                        if ($select.length && $select.data('select2')) {
                                            // Select2 is ready
                                            $select.val(savedNoteIds.map(String)).trigger('change');
                                            console.log('[Tafseer Footer] ✅ Notes applied to Select2');
                                        } else if (retryCount < maxRetries) {
                                            // Select2 not ready yet, retry
                                            setTimeout(() => applyNotesWithRetry(retryCount + 1), 200);
                                            return;
                                        }
                                    }

                                    // Update display
                                    if (typeof window.updateSelectedNotesDisplay === 'function') {
                                        window.updateSelectedNotesDisplay(savedNoteIds, savedNoteTexts);
                                        console.log('[Tafseer Footer] ✅ Notes display updated');
                                    } else if (retryCount < maxRetries) {
                                        setTimeout(() => applyNotesWithRetry(retryCount + 1), 200);
                                        return;
                                    }
                                };

                                // Apply notes immediately and with retries
                                applyNotesWithRetry();
                                setTimeout(() => applyNotesWithRetry(), 300);
                                setTimeout(() => applyNotesWithRetry(), 600);
                                setTimeout(() => applyNotesWithRetry(), 1000);

                                // Also trigger loadSavedNotes if available
                                setTimeout(() => {
                                    if (typeof window.loadSavedNotes === 'function') {
                                        window.loadSavedNotes(0);
                                    }
                                }, 1500);
                            } catch (e) {
                                console.error('[Tafseer Init] Error loading notes:', e);
                            }
                        }
                    }
                }
            }, 600);
        }, 100);

        // Add event listeners to form fields for auto-save
        setTimeout(() => {
            const currentForm = document.getElementById('current-answer-form');
            if (currentForm) {
                const inputs = currentForm.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    const handleEvent = function() {
                        // إذا كنا ننتقل بين الأسئلة، نمنع الحفظ التلقائي فوراً
                        if (isTransitioning) return;
                        
                        if (window.questionModified) {
                            window.questionModified[window.currentIndex] = true;
                        }
                        saveCurrentAnswer();
                    };
                    input.addEventListener('change', handleEvent);
                    input.addEventListener('input', handleEvent);
                });
            }
        }, 500);

        // Add click handlers for question items
        document.addEventListener('click', function(e) {
            const questionItem = e.target.closest('.question-item');
            if (questionItem) {
                const index = parseInt(questionItem.dataset.questionIndex);
                if (!isNaN(index)) {
                    switchToQuestion(index);
                }
            }
        });

        // Function to save all evaluations
        async function saveAllEvaluations() {
            if (!window.IS_HEAD) {
                const revealedCount = window.revealedQuestionIds ? window.revealedQuestionIds.length : 0;
                
                if (revealedCount < window.totalQuestions) {
                    showCustomNotification('تنبيه', 'لا يمكنك الحفظ حتى تظهر كل الأسئلة من رئيس اللجنة', 'error');
                    return false;
                }
        
                const totalCount = window.totalQuestions;
        
                if (revealedCount < totalCount) {
                    showCustomNotification(
                        'تنبيه', 
                        `لا يمكنك حفظ التحكيم حتى يظهر رئيس اللجنة جميع الأسئلة. (المتاح حالياً: ${revealedCount} من ${totalCount})`, 
                        'error',
                        5000
                    );
                    return false;
                }
            }
    
            // Save current answer first
            saveCurrentAnswer();

            const evaluations = [];

            // Use the allAnswers array that should contain data from all questions
            // Each question has its own independent score
            if (typeof window.allAnswers !== 'undefined' && window.allAnswers.length > 0) {
                const questionsData = document.querySelectorAll('#questions-data > div');
                window.allAnswers.forEach((answer, index) => {
                    // Get the actual question_id from DOM if answer exists but question_id is missing
                    const questionId = answer && answer.question_id 
                        ? parseInt(answer.question_id) 
                        : (questionsData[index] ? parseInt(questionsData[index].dataset.questionId) : null);
                    
                    if (questionId) {
                        // Use saved score if exists, otherwise use max score (each question is independent)
                        const maxScorePerQ = parseFloat("{{ $gradeQuestion }}") || 10;
                        const savedScore = (answer && typeof answer.score !== 'undefined' && answer.score !== null && answer.score !== '') 
                            ? parseFloat(answer.score) 
                            : maxScorePerQ;
                        
                        const evaluation = {
                            question_id: questionId,
                            score: savedScore,
                            note_id: answer && answer.note_id && answer.note_id !== "" ? parseInt(answer.note_id) : null,
                            note: answer && answer.note_text && answer.note_text !== "" ? answer.note_text : null
                        };

                        // Add hadith-specific fields if type is hadith
                        if ('{{ $type ?? "" }}' === 'hadith') {
                            evaluation.alert_before_fat7 = answer && answer.alert_before_fat7 ? parseFloat(answer.alert_before_fat7) : 0;
                            evaluation.fat7_points = answer && answer.fat7_points ? parseFloat(answer.fat7_points) : 0;
                        }

                        evaluations.push(evaluation);
                    }
                });
            } else {
                // Fallback: try to get data from current form only
                const currentForm = document.getElementById('current-answer-form');
                if (currentForm) {
                    const questionId = currentForm.dataset.questionId;
                    const scoreInput = currentForm.querySelector("input[name='score']");
                    const noteSelect = currentForm.querySelector("select[name='note_id']");
                    const noteInput = currentForm.querySelector("input[name='note']");

                    const evaluation = {
                        question_id: questionId,
                        score: parseFloat(scoreInput?.value) || 0,
                        note_id: noteSelect?.value || null,
                        note: noteInput?.value?.trim() || null
                    };

                    // Add hadith-specific fields if type is hadith
                    if ('{{ $type ?? "" }}' === 'hadith') {
                        const alertInput = currentForm.querySelector("input[name='alert_before_fat7']");
                        const fat7Input = currentForm.querySelector("input[name='fat7_points']");
                        evaluation.alert_before_fat7 = parseFloat(alertInput?.value) || 0;
                        evaluation.fat7_points = parseFloat(fat7Input?.value) || 0;
                    }

                    evaluations.push(evaluation);
                }
            }

            // قراءة edit_start_field من URL إذا كان موجوداً
            const urlParams = new URLSearchParams(window.location.search);
            const editStartField = urlParams.get('edit_start_field');
            const committeeId = urlParams.get('committee_id');

            const data = {
                participant_id: {{ $participant_id }},
                competition_version_branch_id: {{ $competition_version_branch_id ?? 0 }},
                judging_form_setting_id: {{ $judging_form_setting_id ?? 0 }},
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
            console.log('Evaluations:', evaluations);

            try {
                const response = await fetch("{{ route('judgings.tafseer.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(data)
                });

                let result;
                try {
                    result = await response.json();
                } catch (jsonError) {
                    // إذا فشل تحويل JSON، نعتبره خطأ في الخادم
                    console.error('Failed to parse JSON response:', jsonError);
                    return {
                        success: false,
                        type: 'error',
                        title: 'خطأ',
                        message: 'حدث خطأ في الاتصال بالخادم',
                        notificationType: 'error'
                    };
                }

                console.log('Response status:', response.status);
                console.log('Response result:', result);

                // التحقق من result.type أولاً قبل التحقق من response.ok
                // إذا كان type: 'info'، لا نعتبره خطأ حتى لو كان success: false
                if (result.success === true) {
                    // Clear localStorage after successful save
                    clearLocalStorage();
                    console.log('[Tafseer Storage] Cleared localStorage after successful final save');
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
                    const title = messageType === 'info' ? 'تنبيه' : 'خطأ';
                    
                    console.log('[Tafseer Finalize] Save not completed:', result);
                    
                    // لا نعرض الرسالة هنا - سنعرضها في الـ event listener بعد إغلاق الـ modal
                    // نرجع object يحتوي على معلومات الرسالة
                    return {
                        success: false,
                        type: messageType,
                        title: title,
                        message: result.message || result.error || 'حدث خطأ أثناء حفظ التقييم',
                        notificationType: notificationType
                    };
                }
            } catch (error) {
                console.error('Error saving evaluations:', error);
                
                // لا نرمي error أبداً - نرجع object يحتوي على معلومات الخطأ
                // التحقق من أن الخطأ ليس بسبب عدم اكتمال حفظ الأعضاء
                if (error.message && (error.message.includes('الانتظار') || error.message.includes('متبقي'))) {
                    // هذا تنبيه وليس خطأ
                    return {
                        success: false,
                        type: 'info',
                        title: 'تنبيه',
                        message: error.message,
                        notificationType: 'info'
                    };
                }
                
                // للأخطاء الحقيقية (مثل network errors)
                return {
                    success: false,
                    type: 'error',
                    title: 'خطأ',
                    message: error.message || 'حدث خطأ أثناء حفظ التقييم',
                    notificationType: 'error'
                };
            }
        }

        // Initialize notification polling
        startNotificationPolling();
    });

    // Notification System Functions
    let notificationCheckInterval;
    let lastNotificationCount = 0;

    function startNotificationPolling() {
        // Initialize notification count first
        checkForNotifications();
        // Check for notifications every 3 seconds
        notificationCheckInterval = setInterval(checkForNotifications, 3000);
    }

    async function checkForNotifications() {
        try {
            const response = await fetch('/api/notifications/unread-count', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                const data = await response.json();
                const currentCount = data.unread_count || 0;

                // Check if there are new notifications
                if (currentCount > lastNotificationCount) {
                    const newNotifications = currentCount - lastNotificationCount;
                    console.log(`New notifications: ${newNotifications}`);

                    // Load and display new notifications
                    await loadLatestNotifications();
                }

                lastNotificationCount = currentCount;
            }
        } catch (error) {
            console.error('Error checking notifications:', error);
        }
    }

    async function loadLatestNotifications() {
        try {
            const response = await fetch('/api/notifications/latest', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin'
            });

            if (response.ok) {
                const data = await response.json();
                const notifications = data.notifications || [];

                // Display each new notification
                notifications.forEach(notification => {
                    if (notification.type === 'relief_request') {
                        displayReliefRequestNotification(notification);
                    }
                });
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }

    function displayReliefRequestNotification(notification) {
        // Create notification popup
        const popup = document.createElement('div');
        popup.className = 'fixed top-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border-l-4 border-orange-500 p-4 transform transition-all duration-300';
        popup.style.transform = 'translateX(100%)';

        popup.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-hand-paper text-orange-500 text-xl"></i>
            </div>
            <div class="me-3 w-0 flex-1">
                <p class="text-sm font-medium text-gray-900 dark:text-white">طلب تخفيف جديد</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-300">
                    ${notification.data.judge_name} يطلب تخفيف للمتسابق ${notification.data.participant_name}
                </p>
                <p class="mt-1 text-xs text-gray-400">
                    درجة التخفيف: ${notification.data.grade}
                </p>
                <div class="mt-3 flex gap-2">
                    <button onclick="approveReliefFromNotification('${notification.id}', '${notification.data.request_id}')"
                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">
                        <i class="fas fa-check ms-1"></i> موافقة
                    </button>
                    <button onclick="denyReliefFromNotification('${notification.id}', '${notification.data.request_id}')"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                        <i class="fas fa-times ms-1"></i> رفض
                    </button>
                    <button onclick="markNotificationAsRead('${notification.id}'); this.parentElement.parentElement.parentElement.parentElement.remove()"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-xs">
                        <i class="fas fa-times ms-1"></i> إغلاق
                    </button>
                </div>
            </div>
        </div>
    `;

        document.body.appendChild(popup);

        // Animate in
        setTimeout(() => {
            popup.style.transform = 'translateX(0)';
        }, 10);

        // Auto remove after 10 seconds
        setTimeout(() => {
            if (popup.parentNode) {
                popup.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (popup.parentNode) {
                        popup.remove();
                    }
                }, 300);
            }
        }, 10000);
    }

    async function approveReliefFromNotification(notificationId, requestId) {
        try {
            // Mark notification as read first
            await markNotificationAsRead(notificationId);

            // Approve the relief request
            const response = await fetch('/api/relief-requests/approve', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ request_id: requestId })
            });

            const result = await response.json();

            if (result.success) {
                showCustomNotification('تمت الموافقة', 'تمت الموافقة على طلب التخفيف بنجاح', 'success', 3000);

                // After approval, set the button to "تمت الموافقة" for clarity
                const reliefBtn = document.getElementById('request-relief-btn');
                const reliefBtnText = document.getElementById('relief-btn-text');

                if (reliefBtn && reliefBtnText) {
                    // Keep button disabled and show "تمت الموافقة"
                    reliefBtn.disabled = true;
                    reliefBtn.classList.add('cursor-not-allowed');
                    reliefBtnText.textContent = 'تمت الموافقة';
                    reliefBtn.className = 'bg-green-600 text-white font-bold py-2 px-6 rounded-lg text-base cursor-not-allowed';
                    reliefBtn.style.pointerEvents = 'none';
                    reliefBtn.style.userSelect = 'none';

                    console.log('Button set to "تمت الموافقة" after approval');
                }

                // Reload pending relief requests for real-time update
                if (typeof loadPendingReliefRequests === 'function') {
                    setTimeout(() => {
                        loadPendingReliefRequests();
                    }, 1000);
                }

                // Trigger an approval status check to update other clients
                if (typeof checkReliefRequestStatusRealTime === 'function') {
                    setTimeout(() => {
                        checkReliefRequestStatusRealTime();
                    }, 500);
                }
            } else {
                showCustomNotification('فشل في الموافقة', result.message || 'حدث خطأ أثناء الموافقة على الطلب', 'error', 3000);
            }
        } catch (error) {
            console.error('Error approving relief request:', error);
            showCustomNotification('خطأ في الاتصال', 'حدث خطأ أثناء الاتصال بالخادم', 'error', 3000);
        }
    }

    async function denyReliefFromNotification(notificationId, requestId) {
        const abortController = new AbortController();
        const timeoutId = setTimeout(() => abortController.abort(), 15000); // 15 seconds timeout

        try {
            console.log('[Relief Deny] Denying from notification', { notificationId, requestId });

            // Mark notification as read first
            await markNotificationAsRead(notificationId);

            // Deny the relief request with default reason
            const response = await fetch('/api/relief-requests/deny', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    request_id: requestId,
                    rejection_reason: 'رفض من لوحة التحكيم'
                }),
                signal: abortController.signal
            });

            clearTimeout(timeoutId);

            let result;
            try {
                const raw = await response.text();
                result = raw ? JSON.parse(raw) : {};
            } catch (_) {
                result = { success: false, error: 'invalid_json' };
            }

            if (result.success) {
                showCustomNotification('تم الرفض', 'تم رفض طلب التخفيف', 'warning', 3000);

                // Reset button immediately for ALL committee members (real-time)
                const reliefBtn = document.getElementById('request-relief-btn');
                const reliefBtnText = document.getElementById('relief-btn-text');

                if (reliefBtn && reliefBtnText) {
                    reliefBtn.disabled = false;
                    reliefBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    reliefBtnText.textContent = 'طلب تخفيف';
                    reliefBtn.className = 'bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold py-2 px-6 rounded-lg text-base transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50';
                    reliefBtn.style.pointerEvents = 'auto';
                    reliefBtn.style.userSelect = 'auto';

                    // Reset the relief request exists flag to ensure button works properly
                    if (typeof window !== 'undefined' && typeof window.reliefRequestExists !== 'undefined') {
                        window.reliefRequestExists = false;
                    }

                    // Re-attach the click event listener immediately
                    if (typeof attachReliefButtonListener === 'function') {
                        attachReliefButtonListener();
                    } else {
                        // Fallback: direct event attachment if function not available
                        reliefBtn.removeEventListener('click', handleReliefButtonClick);
                        reliefBtn.addEventListener('click', handleReliefButtonClick);
                        console.log('Fallback: Direct event listener attached');
                    }

                    console.log('Button reset to initial state immediately for ALL committee members after denial');
                }

                // Clear localStorage for ALL users to allow re-sending
                const userId = '{{ auth()->id() }}';
                const participantId = '{{ $participant_id }}';
                const competitionBranchId = '{{ $competition_version_branch_id }}';
                const reliefKey = `relief-request-sent-${userId}-${participantId}-${competitionBranchId}`;
                localStorage.removeItem(reliefKey);

                // Also clear any other relief-related keys for this participant/competition
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key && key.includes('relief-request-sent') && key.includes(participantId) && key.includes(competitionBranchId)) {
                        localStorage.removeItem(key);
                        console.log('Cleared additional relief key:', key);
                    }
                }

                console.log('Cleared localStorage after denial for key:', reliefKey);

                // Refresh the pending requests list to update UI
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
            }
        } catch (error) {
            clearTimeout(timeoutId);
            console.error('[Relief Deny] Error:', error);

            if (error.name === 'AbortError') {
                showCustomNotification('انتهت مهلة الاتصال', 'استغرق الطلب وقتاً طويلاً. يرجى المحاولة مرة أخرى.', 'error', 4000);
            } else {
                showCustomNotification('خطأ في الاتصال', 'حدث خطأ أثناء الاتصال بالخادم', 'error', 3000);
            }
        }
    }

    async function markNotificationAsRead(notificationId) {
        try {
            await fetch('/api/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ notification_id: notificationId })
            });
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    }

    // Custom notification function
    function showCustomNotification(title, message, type = 'info', duration = 3000) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg border-l-4 p-4 transform transition-all duration-300 ${
            type === 'success' ? 'border-green-500' :
                type === 'error' ? 'border-red-500' :
                    type === 'warning' ? 'border-yellow-500' : 'border-blue-500'
        }`;

        const iconClass = type === 'success' ? 'fa-check-circle text-green-500' :
            type === 'error' ? 'fa-exclamation-circle text-red-500' :
                type === 'warning' ? 'fa-exclamation-triangle text-yellow-500' :
                    'fa-info-circle text-blue-500';

        // إذا كانت الرسالة تحتوي على HTML، نستخدم innerHTML، وإلا نستخدم textContent
        const messageElement = message.includes('<div') 
            ? `<div class="mt-1 text-sm text-gray-500 dark:text-gray-300" style="text-align: right; direction: rtl;">${message}</div>`
            : `<p class="mt-1 text-sm text-gray-500 dark:text-gray-300">${message.replace(/\n/g, '<br>')}</p>`;
        
        notification.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas ${iconClass} text-xl"></i>
            </div>
            <div class="me-3 w-0 flex-1">
                <p class="text-sm font-medium text-gray-900 dark:text-white">${title}</p>
                ${messageElement}
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
</script>

<!-- نظام إظهار الأسئلة التدريجي -->
@include('mosabka::judgings.tafseer.reveal-system')

<!-- Removed duplicate navigation buttons (prev/next) to avoid double footers -->