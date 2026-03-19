<script>
    // ═══════════════════════════════════════════════════════════════
    // نظام إظهار الأسئلة - الزر ثابت في HTML
    // ═══════════════════════════════════════════════════════════════

    document.addEventListener('DOMContentLoaded', () => {
        const IS_HEAD = {{ ($is_head ?? false) ? 'true' : 'false' }};
        const BRANCH_ID = {{ (int) ($competition_version_branch_id ?? 0) }};
        const PARTICIPATION_ID = {{ (int) ($participant_id ?? 0) }};
        const FIELD_TYPE = '{{ $type ?? "interpretation" }}';
        const COMMITTEE_ID = (new URLSearchParams(window.location.search)).get('committee_id');

        let revealedQuestionIds = @json(($revealedQuestionIds ?? collect())->values());
        let isRevealing = false;

        console.log('[Tafseer Reveal] Init', { IS_HEAD: IS_HEAD, revealedQuestionIds: revealedQuestionIds });

        // Helper function to find question index by ID
        function getIndexByQuestionId(qId) {
            const questionsData = document.querySelectorAll('#questions-data > div');
            for (let i = 0; i < questionsData.length; i++) {
                if (parseInt(questionsData[i].dataset.questionId) === parseInt(qId)) return i;
            }
            return -1;
        }

        // Function to update question visibility (show newly revealed questions)
        function updateQuestionVisibility(qId) {
            const index = getIndexByQuestionId(qId);
            if (index < 0) {
                console.warn('[Tafseer Reveal] Question ID not found:', qId);
                return;
            }

            console.log('[Tafseer Reveal] Revealing question', { qId: qId, index: index });

            // Add question to sidebar list (if not already there)
            addQuestionToList(index, qId);

            // Verify the question div exists and is in the correct position
            const questionDiv = document.getElementById('question-' + index);
            if (!questionDiv) {
                console.error('[Tafseer Reveal] Question div not found for index:', index);
                return;
            }

            // Verify we're updating the correct div
            const divStep = parseInt(questionDiv.getAttribute('data-step'));
            if (divStep !== index) {
                console.error('[Tafseer Reveal] Position mismatch: div step is', divStep, 'but index is', index);
                return;
            }

            // Rebuild question content (unlock it) - this will only rebuild if still locked
            rebuildQuestionContent(index);

            // Enable form if it's the current question
            const currentIdx = window.currentIndex || 0;
            if (currentIdx === index) {
                // إزالة رسالة "في انتظار رئيس اللجنة" وإظهار المحتوى
                setTimeout(() => {
                    const questionDiv = document.getElementById('question-' + index);
                    if (questionDiv) {
                        const lockedView = questionDiv.querySelector('.unified-content');
                        if (lockedView) {
                            lockedView.remove();
                        }
                    }
                    const formContent = document.getElementById('current-form-content');
                    if (formContent) {
                        formContent.classList.remove('hidden');
                    }
                    const lockMsg = document.getElementById('member-locked-warning');
                    if (lockMsg) {
                        lockMsg.classList.add('hidden');
                    }
                }, 50);
            }
        }

        // Function to rebuild question content (unlock it)
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

            // Verify we're updating the correct div by checking data-step attribute
            const divStep = parseInt(questionDiv.getAttribute('data-step'));
            if (divStep !== index) {
                console.error('[Tafseer Reveal] Mismatch: div step is', divStep, 'but index is', index);
                return;
            }

            const questionText = qData.dataset.questionText || '';
            const answerText = qData.dataset.answerText || '';
            const bookName = qData.dataset.bookName || '';
            const pageNumber = qData.dataset.pageNumber || '';

            // Only rebuild if the question is currently locked (has locked view)
            // If it's already unlocked, don't rebuild to avoid duplication
            const hasLockedView = questionDiv.querySelector('.unified-content') !== null;
            const hasUnlockedView = questionDiv.querySelector('.question-content') !== null;

            if (hasUnlockedView && !hasLockedView) {
                console.log('[Tafseer Reveal] Question', index + 1, 'already unlocked, skipping rebuild');
                return;
            }

            // Rebuild content with unlocked view - replace only the inner content
            // Add reveal button container for head (like Quran)
            const isHead = IS_HEAD;
            const revealBtnHtml = isHead ? '<div id="reveal-btn-in-question"></div>' : '';

            const newContent = '<div class="premium-card animate-fade-in">' +
                '<div class="mb-10">' +
                '<div class="flex items-center justify-between mb-6">' +
                '<div class="flex items-center gap-3">' +
                '<div class="w-10 h-10 rounded-xl bg-gold/10 flex items-center justify-center text-gold">' +
                '<i class="fas fa-question-circle text-xl"></i>' +
                '</div>' +
                '<h2 class="text-2xl font-bold text-navy">السؤال ' + (index + 1) + '</h2>' +
                '</div>' +
                revealBtnHtml +
                '</div>' +
                '<div class="text-xl text-gray-800 leading-relaxed font-medium bg-gray-50/50 p-6 rounded-2xl border border-dashed border-gray-200">' +
                questionText +
                '</div>' +
                '</div>' +
                '<div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-2xl p-8 relative overflow-hidden">' +
                '<div class="absolute top-0 left-0 w-2 h-full bg-emerald-500"></div>' +
                '<div class="flex items-center gap-3 mb-6">' +
                '<div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center text-emerald-600">' +
                '<i class="fas fa-lightbulb text-xl"></i>' +
                '</div>' +
                '<h2 class="text-2xl font-bold text-emerald-900">الجواب النموذجـي</h2>' +
                '</div>' +
                '<div class="text-xl text-emerald-950 leading-loose">' +
                answerText +
                '</div>' +
                (bookName ? '<div class="mt-8 flex items-center gap-4 p-4 bg-white/60 rounded-xl border border-emerald-100/50">' +
                    '<div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">' +
                    '<i class="fas fa-book text-sm"></i>' +
                    '</div>' +
                    '<span class="text-sm text-emerald-800 font-medium">' +
                    '<strong>المصدر:</strong> ' + bookName + ' <span class="mx-2 opacity-30">|</span> صفحة ' + pageNumber + '</span>' +
                    '</div>' : '') +
                '</div>' +
                '</div>';

            // Remove locked view if exists
            const lockedView = questionDiv.querySelector('.unified-content');
            if (lockedView) {
                lockedView.remove();
            }

            // Replace only the content inside questionDiv, not the entire div
            questionDiv.innerHTML = newContent;

            // Inject reveal button if head (like Quran)
            if (IS_HEAD) {
                const revealContainer = questionDiv.querySelector('#reveal-btn-in-question');
                if (revealContainer) {
                    revealContainer.innerHTML = `
                    <button id="reveal-question-btn" type="button" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5">
                        <i class="fas fa-eye text-xs"></i>
                        <span>إظهار للجميع</span>
                    </button>`;
                    // Update button state after injection
                    setTimeout(() => {
                        if (typeof updateRevealButtonState === 'function') {
                            updateRevealButtonState();
                        }
                    }, 50);
                }
            }

            // إزالة رسالة "في انتظار رئيس اللجنة" وإظهار المحتوى
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

            // Don't remove hidden class here - let loadQuestion handle visibility
            // The question will be shown when navigating to it (like Quran)
            questionDiv.classList.add('fade-in');

            console.log('[Tafseer Reveal] Rebuilt and unlocked question content at correct position:', index + 1);
        }

        // Function to add question to sidebar list
        function addQuestionToList(index, qId) {
            const container = document.getElementById('questions-list-container');
            if (!container) {
                console.warn('[Tafseer Reveal] Container #questions-list-container not found!');
                return;
            }

            // Check if already exists
            const existingItem = container.querySelector('[data-question-id="' + qId + '"]');
            if (existingItem) {
                console.log('[Tafseer Reveal] Question already in list:', index);
                return;
            }

            const questionsData = document.querySelectorAll('#questions-data > div');
            const qData = questionsData[index];
            if (!qData) return;

            const questionText = qData.dataset.questionText || '';
            const currentIndex = window.currentIndex || 0;
            const isActive = index === currentIndex;

            // Apply same styling as Quran: first question or active question gets blue background
            const bgClass = isActive ? 'bg-blue-900 text-white shadow-sm border-blue-900' : 'bg-slate-50 text-slate-600 border-slate-200';
            const badgeClass = isActive ? 'bg-white text-blue-900' : 'bg-slate-200 text-slate-500';
            const titleClass = isActive ? 'text-blue-100' : 'text-slate-800';

            const div = document.createElement('div');
            div.className = 'question-item relative overflow-hidden rounded-lg p-2 cursor-pointer transition-all duration-300 border ' + bgClass;
            div.setAttribute('data-question-index', index);
            div.setAttribute('data-question-id', qId);
            div.onclick = function() { if (window.switchToQuestion) window.switchToQuestion(index); };
            div.innerHTML = '<div class="flex items-start gap-2">' +
                '<div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center font-bold text-[10px] ' + badgeClass + '">' + (index + 1) + '</div>' +
                '<div class="flex-1">' +
                '<h4 class="font-bold text-xs mb-0 ' + titleClass + '">سؤال ' + (index + 1) + '</h4>' +
                '<p class="text-[10px] leading-snug opacity-90 font-light">' + (questionText.length > 40 ? questionText.slice(0, 40) + '...' : questionText) + '</p>' +
                '</div>' +
                '</div>';

            // Insert in correct position (sorted by index)
            const allItems = Array.from(container.querySelectorAll('.question-item'));
            let inserted = false;
            for (let i = 0; i < allItems.length; i++) {
                const itemIndex = parseInt(allItems[i].getAttribute('data-question-index'));
                if (itemIndex > index) {
                    container.insertBefore(div, allItems[i]);
                    inserted = true;
                    break;
                }
            }
            if (!inserted) {
                container.appendChild(div);
            }

            // Update highlighting after adding (like Quran)
            if (typeof window.updateQuestionHighlight === 'function') {
                setTimeout(() => {
                    window.updateQuestionHighlight();
                }, 50);
            }

            console.log('[Tafseer Reveal] Added question to list:', index + 1);
        }

        if (!IS_HEAD) {
            // Polling for members - نفس نظام القرآن بدون reload
            const poll = async () => {
                try {
                    const url = `/api/judgings/tafseer/reveals/status?competition_version_branch_id=${ BRANCH_ID }&competition_participation_id=${ PARTICIPATION_ID }&field_type=${ FIELD_TYPE }&committee_id=${ COMMITTEE_ID || '' }`;
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();

                    console.log('[Tafseer Polling] Status check response', {
                        revealed_count: data.revealed ? data.revealed.length : 0,
                        revealed_ids: data.revealed,
                        timestamp: new Date().toISOString()
                    });

                    if (Array.isArray(data.revealed)) {
                        const prev = revealedQuestionIds.slice();
                        revealedQuestionIds = data.revealed.map(Number);
                        // تحديث window.revealedQuestionIds للمزامنة
                        window.revealedQuestionIds = revealedQuestionIds;

                        const newRevealed = revealedQuestionIds.filter(id => !prev.includes(id));
                        if (newRevealed.length > 0) {
                            console.log('[Tafseer Reveal] New questions revealed!', newRevealed);

                            // Show notification
                            if (typeof showCustomNotification === 'function') {
                                showCustomNotification('سؤال جديد', 'تم إظهار السؤال من قبل رئيس اللجنة', 'info', 2000);
                            }

                            // Update UI for each new revealed question
                            newRevealed.forEach(qId => {
                                updateQuestionVisibility(qId);
                            });
                        }
                    }
                } catch (e) {
                    console.error('[Tafseer Reveal] Poll error', e);
                }
            };
            poll();
            setInterval(poll, 300);
            return;
        }

        // HEAD only from here
        // حقن الزر في أول سؤال (إذا كان موجوداً في HTML)
        if (IS_HEAD) {
            try {
                // Try to inject in first question's reveal-btn-in-question (if exists in HTML)
                const firstRevealContainer = document.getElementById('reveal-btn-in-question');
                if (firstRevealContainer) {
                    firstRevealContainer.innerHTML = `
                    <button id="reveal-question-btn" type="button" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5">
                        <i class="fas fa-eye text-xs"></i>
                        <span>إظهار للجميع</span>
                    </button>`;
                    console.log('[Tafseer Reveal] Button injected in first question');
                    // Update button state after injection
                    setTimeout(() => {
                        if (typeof updateRevealButtonState === 'function') {
                            updateRevealButtonState();
                        }
                    }, 100);
                }
            } catch (e) {
                console.warn('[Tafseer Reveal] Button injection failed', e);
            }
        }

        // تحديث حالة الزر فقط (بدون حقن جديد)
        function updateRevealButtonState() {
            if (!IS_HEAD) return;

            const currentIdx = window.currentIndex ?? 0;
            const questionsData = document.querySelectorAll('#questions-data > div');
            if (!questionsData[currentIdx]) return;

            const qId = parseInt(questionsData[currentIdx].dataset.questionId);

            // Find button in current question's reveal-btn-in-question (like Quran)
            const currentQuestionDiv = document.getElementById('question-' + currentIdx);
            const revealContainer = currentQuestionDiv ? currentQuestionDiv.querySelector('#reveal-btn-in-question') : null;
            const btn = revealContainer ? revealContainer.querySelector('#reveal-question-btn') : document.getElementById('reveal-question-btn');

            if (!btn) {
                // If button doesn't exist, try to inject it
                if (revealContainer && !revealContainer.querySelector('#reveal-question-btn')) {
                    revealContainer.innerHTML = `
                    <button id="reveal-question-btn" type="button" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5">
                        <i class="fas fa-eye text-xs"></i>
                        <span>إظهار للجميع</span>
                    </button>`;
                    // Recursively call to update the newly injected button
                    setTimeout(() => updateRevealButtonState(), 50);
                    return;
                }
                return;
            }

            // استخدام window.revealedQuestionIds للتأكد من المزامنة
            const currentRevealedIds = window.revealedQuestionIds || revealedQuestionIds || [];
            const isRevealed = currentRevealedIds.includes(qId);
            console.log('[Tafseer Reveal] Update btn state', {
                qId: qId,
                isRevealed: isRevealed,
                currentIdx: currentIdx,
                revealedIds: currentRevealedIds
            });

            if (isRevealed) {
                btn.className = 'px-3 py-1.5 bg-green-50 text-green-600 border border-green-200 rounded-lg text-xs font-medium cursor-not-allowed flex items-center gap-1.5';
                btn.innerHTML = '<i class="fas fa-check-circle text-xs"></i><span>تم الإظهار</span>';
                btn.disabled = true;
                btn.style.pointerEvents = 'none';
            } else {
                btn.className = 'px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5';
                btn.innerHTML = '<i class="fas fa-eye text-xs"></i><span>إظهار للجميع</span>';
                btn.disabled = false;
                btn.style.pointerEvents = 'auto';
            }
        }

        async function revealCurrentQuestion() {
            if (isRevealing) return;

            const questionsData = document.querySelectorAll('#questions-data > div');
            if (!questionsData[window.currentIndex]) return;

            const qId = parseInt(questionsData[window.currentIndex].dataset.questionId);
            console.log('[Tafseer Reveal] Revealing', qId);

            isRevealing = true;

            // Find button in current question (like Quran)
            const currentQuestionDiv = document.getElementById('question-' + window.currentIndex);
            const revealContainer = currentQuestionDiv ? currentQuestionDiv.querySelector('#reveal-btn-in-question') : null;
            const btn = revealContainer ? revealContainer.querySelector('#reveal-question-btn') : document.getElementById('reveal-question-btn');

            // ✨ Optimistic UI - تحديث فوري (زي القرآن)
            if (btn) {
                btn.disabled = true;
                btn.className = 'px-3 py-1.5 bg-green-50 text-green-600 border border-green-200 rounded-lg text-xs font-medium cursor-not-allowed flex items-center gap-1.5';
                btn.innerHTML = '<i class="fas fa-check-circle text-xs"></i><span>تم الإظهار</span>';
                btn.style.pointerEvents = 'none';
            }

            // أضف للقائمة فوراً وتحديث window.revealedQuestionIds للمزامنة
            if (!revealedQuestionIds.includes(qId)) {
                revealedQuestionIds.push(qId);
            }
            // تحديث window.revealedQuestionIds للتأكد من المزامنة مع جميع الملفات
            window.revealedQuestionIds = revealedQuestionIds;

            // إظهار إشعار النجاح فوراً
            if (typeof showCustomNotification === 'function') {
                showCustomNotification('تم الإظهار', 'تم إظهار السؤال لباقي الأعضاء بنجاح', 'success', 2000);
            }

            // إرسال الطلب للسيرفر في الخلفية
            try {
                const res = await fetch('/api/judgings/tafseer/reveals', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({
                        competition_version_branch_id: BRANCH_ID,
                        competition_participation_id: PARTICIPATION_ID,
                        question_id: qId,
                        field_type: FIELD_TYPE,
                        committee_id: COMMITTEE_ID
                    })
                });

                const data = await res.json();
                console.log('[Tafseer Reveal] Response', data);

                // نجاح: افتح السؤال تلقائياً في منطقة السؤال والجواب
                if (data && data.success) {
                    // تحديث حالة الزر بعد النجاح
                    if (typeof window.updateRevealButtonState === 'function') {
                        setTimeout(() => {
                            window.updateRevealButtonState();
                        }, 100);
                    }
                    try {
                        const nodes = document.querySelectorAll('#questions-data > div');
                        let targetIndex = -1;
                        nodes.forEach((n, i) => {
                            if (parseInt(n.dataset.questionId) === qId) targetIndex = i;
                        });
                        if (targetIndex >= 0 && typeof window.switchToQuestion === 'function') {
                            window.switchToQuestion(targetIndex);
                        }
                    } catch (e) { console.warn('Auto-open after reveal failed', e); }
                }

                // في حالة الفشل فقط، نعيد الزر لحالته الأصلية
                if (!data || !data.success) {
                    console.error('[Tafseer Reveal] Server failure');

                    // إزالة السؤال من القائمة وتحديث window.revealedQuestionIds
                    const index = revealedQuestionIds.indexOf(qId);
                    if (index > -1) {
                        revealedQuestionIds.splice(index, 1);
                    }
                    window.revealedQuestionIds = revealedQuestionIds;

                    // إعادة الزر لحالته الأصلية
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
                console.error('[Tafseer Reveal] Error', err);

                // في حالة خطأ الشبكة، نعيد الزر لحالته الأصلية
                const index = revealedQuestionIds.indexOf(qId);
                if (index > -1) {
                    revealedQuestionIds.splice(index, 1);
                }
                window.revealedQuestionIds = revealedQuestionIds;

                if (btn) {
                    btn.disabled = false;
                    btn.className = 'bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition-all duration-200';
                    btn.innerHTML = '<i class="fas fa-eye ms-2"></i> إظهار السؤال لباقي أعضاء اللجنة';
                    btn.style.pointerEvents = 'auto';
                }

                if (typeof showCustomNotification === 'function') {
                    showCustomNotification('خطأ', 'تعذر الاتصال بالخادم، حاول مرة أخرى', 'error', 3000);
                }
            } finally {
                setTimeout(() => { isRevealing = false; }, 500);
            }
        }

        // Click handler
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('#reveal-question-btn');
            if (btn && !btn.disabled && !isRevealing) {
                revealCurrentQuestion();
            }
        });

        // Update button when question changes (زي القرآن)
        const originalSwitchToQuestion = window.switchToQuestion;
        console.log('[Tafseer Reveal] switchToQuestion available?', typeof originalSwitchToQuestion);

        if (originalSwitchToQuestion) {
            console.log('[Tafseer Reveal] Hooking immediately');
            window.switchToQuestion = function(index) {
                console.log('[Tafseer Reveal] Switch to question', index);

                // حدّث window.currentIndex أولاً
                window.currentIndex = index;

                // استدعِ الدالة الأصلية (من footer.blade.php التي تحتوي على auto-reveal)
                originalSwitchToQuestion(index);

                // Auto-reveal logic: إظهار السؤال تلقائياً لأعضاء اللجنة عند انتقال رئيس اللجنة إليه
                // الأسئلة تظهر تلقائياً لأعضاء اللجنة بمجرد انتقال رئيس اللجنة إليها
                if (IS_HEAD || window.IS_HEAD) {
                    const questionsData = document.querySelectorAll('#questions-data > div');
                    const qId = questionsData[index] ? parseInt(questionsData[index].dataset.questionId) : null;
                    const currentRevealedIds = window.revealedQuestionIds || revealedQuestionIds || [];
                    const revealedIdsArray = Array.isArray(currentRevealedIds) ? currentRevealedIds : [];
                    const isAlreadyRevealed = qId && revealedIdsArray.includes(qId);
                    
                    console.log('[Tafseer Reveal Hook] 🔍 Auto-reveal check', {
                        IS_HEAD: IS_HEAD || window.IS_HEAD,
                        qId: qId,
                        index: index,
                        currentRevealedIds: revealedIdsArray,
                        isAlreadyRevealed: isAlreadyRevealed,
                        willReveal: qId && !isAlreadyRevealed,
                        autoRevealFunctionExists: typeof window.autoRevealQuestionForHead === 'function'
                    });
                    
                    if (qId && !isAlreadyRevealed) {
                        console.log('[Tafseer Reveal Hook] 🔓 Head switched to question, auto-revealing...', { qId, index });
                        setTimeout(() => {
                            if (typeof window.autoRevealQuestionForHead === 'function') {
                                console.log('[Tafseer Reveal Hook] ✅ Calling autoRevealQuestionForHead');
                                window.autoRevealQuestionForHead(qId);
                            } else {
                                console.warn('[Tafseer Reveal Hook] ⚠️ autoRevealQuestionForHead not available, will retry...');
                                setTimeout(() => {
                                    if (typeof window.autoRevealQuestionForHead === 'function') {
                                        console.log('[Tafseer Reveal Hook] ✅ Calling autoRevealQuestionForHead (retry)');
                                        window.autoRevealQuestionForHead(qId);
                                    } else {
                                        console.error('[Tafseer Reveal Hook] ❌ autoRevealQuestionForHead not found after retry');
                                    }
                                }, 500);
                            }
                        }, 300);
                    } else if (qId && isAlreadyRevealed) {
                        console.log('[Tafseer Reveal Hook] ⏭️ Question already revealed, skipping', { qId });
                    }
                }
                
                // حدّث حالة الزر بعد التنقل
                setTimeout(() => {
                    console.log('[Tafseer Reveal] Updating button state after switch');
                    updateRevealButtonState();
                }, 100);
            };
            window.switchToQuestion._hooked = true;
        }

        // Also try hooking after delay if not available
        setTimeout(() => {
            console.log('[Tafseer Reveal] Delayed hook check - switchToQuestion:', typeof window.switchToQuestion, '- _hooked:', window.switchToQuestion?._hooked);

            if (window.switchToQuestion && !window.switchToQuestion._hooked) {
                console.log('[Tafseer Reveal] Hooking after delay');
                const original = window.switchToQuestion;
                window.switchToQuestion = function(index) {
                    console.log('[Tafseer Reveal] Switch (delayed hook)', index);

                    window.currentIndex = index;
                    // Call original function (from footer.blade.php which has auto-reveal)
                    original(index);

                    // Auto-reveal logic: إظهار السؤال تلقائياً لأعضاء اللجنة عند انتقال رئيس اللجنة إليه
                    if (IS_HEAD || window.IS_HEAD) {
                        const questionsData = document.querySelectorAll('#questions-data > div');
                        const qId = questionsData[index] ? parseInt(questionsData[index].dataset.questionId) : null;
                        const currentRevealedIds = window.revealedQuestionIds || [];
                        const revealedIdsArray = Array.isArray(currentRevealedIds) ? currentRevealedIds : [];
                        const isAlreadyRevealed = qId && revealedIdsArray.includes(qId);
                        
                        console.log('[Tafseer Reveal Hook Delayed] 🔍 Auto-reveal check', {
                            IS_HEAD: IS_HEAD || window.IS_HEAD,
                            qId: qId,
                            index: index,
                            currentRevealedIds: revealedIdsArray,
                            isAlreadyRevealed: isAlreadyRevealed,
                            willReveal: qId && !isAlreadyRevealed
                        });
                        
                        if (qId && !isAlreadyRevealed) {
                            console.log('[Tafseer Reveal Hook Delayed] 🔓 Head switched to question, auto-revealing...', { qId, index });
                            setTimeout(() => {
                                if (typeof window.autoRevealQuestionForHead === 'function') {
                                    console.log('[Tafseer Reveal Hook Delayed] ✅ Calling autoRevealQuestionForHead');
                                    window.autoRevealQuestionForHead(qId);
                                } else {
                                    console.warn('[Tafseer Reveal Hook Delayed] ⚠️ autoRevealQuestionForHead not available, will retry...');
                                    setTimeout(() => {
                                        if (typeof window.autoRevealQuestionForHead === 'function') {
                                            console.log('[Tafseer Reveal Hook Delayed] ✅ Calling autoRevealQuestionForHead (retry)');
                                            window.autoRevealQuestionForHead(qId);
                                        } else {
                                            console.error('[Tafseer Reveal Hook Delayed] ❌ autoRevealQuestionForHead not found after retry');
                                        }
                                    }, 500);
                                }
                            }, 300);
                        } else if (qId && isAlreadyRevealed) {
                            console.log('[Tafseer Reveal Hook Delayed] ⏭️ Question already revealed, skipping', { qId });
                        }
                    }
                    
                    setTimeout(() => updateRevealButtonState(), 100);
                };
                window.switchToQuestion._hooked = true;
                console.log('[Tafseer Reveal] Delayed hook installed');
            } else {
                console.log('[Tafseer Reveal] Delayed hook not needed');
            }
        }, 1000);

        // Initial state update
        setTimeout(() => {
            console.log('[Tafseer Reveal] Initial button state update');
            updateRevealButtonState();

            // For members: Don't rebuild revealed questions on page load
            // They will be rebuilt when navigating to them (like Quran)
            // This prevents all revealed questions from appearing at once
            if (!IS_HEAD && revealedQuestionIds && revealedQuestionIds.length > 0) {
                console.log('[Tafseer Reveal] Revealed questions available, will be shown when navigating:', revealedQuestionIds);
                // Just add to sidebar list, don't rebuild content yet
                // Use retry logic to ensure container exists
                const tryAddQuestions = (retryCount = 0) => {
                    const container = document.getElementById('questions-list-container');
                    if (container) {
                        revealedQuestionIds.forEach(qId => {
                            const index = getIndexByQuestionId(qId);
                            if (index >= 0) {
                                addQuestionToList(index, qId);
                            }
                        });
                    } else if (retryCount < 5) {
                        console.log('[Tafseer Reveal] Container not found, retrying...', retryCount + 1);
                        setTimeout(() => tryAddQuestions(retryCount + 1), 200);
                    } else {
                        console.warn('[Tafseer Reveal] Container not found after retries');
                    }
                };
                tryAddQuestions();
            }
        }, 800);

        // Global functions
        window.revealCurrentQuestion = revealCurrentQuestion;
        window.updateRevealButtonState = updateRevealButtonState;
        window.rebuildQuestionContent = rebuildQuestionContent;
        window.updateQuestionVisibility = updateQuestionVisibility;
        window.addQuestionToList = addQuestionToList;
        window.getIndexByQuestionId = getIndexByQuestionId;

        // Expose globals for other scripts
        window.IS_HEAD = IS_HEAD;
        window.revealedQuestionIds = revealedQuestionIds;

        console.log('[Tafseer Reveal] Ready');

        // Auto-reveal first question for head when page loads
        // إظهار السؤال الأول تلقائياً لرئيس اللجنة عند تحميل الصفحة
        setTimeout(() => {
            if (IS_HEAD || window.IS_HEAD) {
                const questionsData = document.querySelectorAll('#questions-data > div');
                if (questionsData.length > 0) {
                    const firstQuestionDiv = questionsData[0];
                    const firstQId = firstQuestionDiv ? parseInt(firstQuestionDiv.dataset.questionId) : null;
                    const currentRevealedIds = window.revealedQuestionIds || revealedQuestionIds || [];
                    const revealedIdsArray = Array.isArray(currentRevealedIds) ? currentRevealedIds : [];
                    const isAlreadyRevealed = firstQId && revealedIdsArray.includes(firstQId);

                    console.log('[Tafseer Reveal Init] 🔍 Auto-reveal first question check', {
                        IS_HEAD: IS_HEAD || window.IS_HEAD,
                        firstQId: firstQId,
                        currentRevealedIds: revealedIdsArray,
                        isAlreadyRevealed: isAlreadyRevealed,
                        willReveal: firstQId && !isAlreadyRevealed
                    });

                    if (firstQId && !isAlreadyRevealed) {
                        console.log('[Tafseer Reveal Init] 🔓 Head loaded page, auto-revealing first question...', { firstQId });
                        setTimeout(() => {
                            if (typeof window.autoRevealQuestionForHead === 'function') {
                                console.log('[Tafseer Reveal Init] ✅ Calling autoRevealQuestionForHead for first question');
                                window.autoRevealQuestionForHead(firstQId);
                            } else {
                                console.warn('[Tafseer Reveal Init] ⚠️ autoRevealQuestionForHead not available yet, will retry...');
                                setTimeout(() => {
                                    if (typeof window.autoRevealQuestionForHead === 'function') {
                                        console.log('[Tafseer Reveal Init] ✅ Calling autoRevealQuestionForHead for first question (retry)');
                                        window.autoRevealQuestionForHead(firstQId);
                                    } else {
                                        console.error('[Tafseer Reveal Init] ❌ autoRevealQuestionForHead not found after retry');
                                    }
                                }, 1000);
                            }
                        }, 800);
                    } else if (firstQId && isAlreadyRevealed) {
                        console.log('[Tafseer Reveal Init] ⏭️ First question already revealed, skipping auto-reveal', { firstQId });
                    }
                }
            }
        }, 1500);
    });
</script>