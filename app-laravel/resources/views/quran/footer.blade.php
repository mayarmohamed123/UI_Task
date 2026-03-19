<script>
    document.addEventListener('DOMContentLoaded', () => {
        const questionsData = document.querySelectorAll('#questions-data > div');
        const questionContent = document.getElementById('question-content');
        const currentAnswerForm = document.getElementById('current-answer-form');
        const totalQuestions = questionsData.length;
        window.currentIndex = 0; 

        // دالة مساعدة ليستخدمها سكربت index.blade.php لمعرفة السؤال النشط
        function getActiveQuestionIndex() {
            return currentIndex;
        }
        window.getActiveQuestionIndex = getActiveQuestionIndex;
        const MUSHAF_ASSET_BASE = @json(rtrim(asset('assets/quran'), '/') . '/');
        let mushafState = { pages: [], index: 0, range: '' };
        
        // اسم المجال التالي للفروع متعددة المجالات
        const nextFieldName = @json($next_field_name ?? null);
        console.log('[Quran Footer] Next field name:', nextFieldName);

        const participantId = @json($participant_id ?? request('participant'));
        const competitionBranchId = @json($competition_version_branch_id ?? request('competition_version_branch_id'));
        const committeeId = @json(request('committee_id'));
        const storageScope = `${participantId}-${competitionBranchId}-${committeeId ?? 'none'}`;

        // Helper functions for localStorage (defined early)
        function getLocalStorageKey(index) {
            return `judging-data-${storageScope}-${index}`;
        }

        function saveToLocalStorage(index, data) {
            try {
                const key = getLocalStorageKey(index);
                console.log('[Storage Save] 💾 Saving to localStorage:', {
                    index: index,
                    key: key,
                    note_ids: data.note_ids,
                    note_texts: data.note_texts,
                    note_idsLength: data.note_ids ? (Array.isArray(data.note_ids) ? data.note_ids.length : 0) : 0,
                    note_textsLength: data.note_texts ? (Array.isArray(data.note_texts) ? data.note_texts.length : 0) : 0
                });
                localStorage.setItem(key, JSON.stringify(data));
                console.log('[Storage Save] ✅ Successfully saved to localStorage with key:', key);

                // Verify it was saved
                const saved = localStorage.getItem(key);
                console.log('[Storage Save] 🔍 Verification - Data exists in localStorage:', !!saved);
            } catch (e) {
                console.error('[Storage Save] ❌ Failed to save to localStorage:', e);
                console.error('[Storage Save] Error details:', {
                    message: e.message,
                    name: e.name
                });
            }
        }

        function loadFromLocalStorage(index) {
            try {
                const key = getLocalStorageKey(index);
                console.log('[Storage Load] 🔍 Loading from localStorage:', {
                    index: index,
                    key: key
                });
                const data = localStorage.getItem(key);
                console.log('[Storage Load] 📦 Raw data from localStorage:', data ? 'exists' : 'not found');

                if (data) {
                    const parsed = JSON.parse(data);
                    console.log('[Storage Load] ✅ Parsed data:', {
                        hasNoteIds: !!parsed.note_ids,
                        hasNoteTexts: !!parsed.note_texts,
                        note_ids: parsed.note_ids,
                        note_texts: parsed.note_texts,
                        note_idsLength: parsed.note_ids ? (Array.isArray(parsed.note_ids) ? parsed.note_ids.length : 0) : 0,
                        note_textsLength: parsed.note_texts ? (Array.isArray(parsed.note_texts) ? parsed.note_texts.length : 0) : 0
                    });
                    return parsed;
                }

                console.log('[Storage Load] ⚠️ No data found for key:', key);
                return null;
            } catch (e) {
                console.error('[Storage Load] ❌ Failed to load from localStorage:', e);
                console.error('[Storage Load] Error details:', {
                    message: e.message,
                    name: e.name
                });
                return null;
            }
        }

        // Load existing evaluations from backend (for edit mode)
        // Convert array to object keyed by question_id (like TafseerQuestionsController pattern)
        const existingEvaluationsRaw = @json($existingEvaluations ?? collect());
        console.log('[Backend] Existing evaluations loaded (raw):', existingEvaluationsRaw);

        // Prepare notes map for note_id to note_text conversion
        const notesMap = @json(isset($notes) && $notes ? $notes->keyBy('id')->map(function($note) { return $note->note; })->toArray() : []);
        console.log('[Backend] Notes map prepared:', notesMap);

        // Convert array to object keyed by question_id for easy lookup
        const existingEvaluations = {};
        if (Array.isArray(existingEvaluationsRaw)) {
            existingEvaluationsRaw.forEach(eval => {
                const questionId = String(eval.quran_question_id || eval.question_id || '');
                if (questionId) {
                    // Convert note_id to note_ids and note_texts (like TafseerQuestionsController)
                    let noteIds = [];
                    let noteTexts = [];
                    if (eval.note_id) {
                        const noteId = parseInt(eval.note_id);
                        noteIds = [noteId];
                        if (notesMap[noteId]) {
                            noteTexts = [notesMap[noteId]];
                        }
                    }
                    // Add note_ids and note_texts to evaluation object
                    existingEvaluations[questionId] = {
                        ...eval,
                        note_ids: noteIds,
                        note_texts: noteTexts
                    };
                }
            });
        } else if (existingEvaluationsRaw && typeof existingEvaluationsRaw === 'object') {
            // Already an object, use as is but convert note_id if needed
            Object.keys(existingEvaluationsRaw).forEach(key => {
                const eval = existingEvaluationsRaw[key];
                let noteIds = [];
                let noteTexts = [];
                if (eval.note_id) {
                    const noteId = parseInt(eval.note_id);
                    noteIds = [noteId];
                    if (notesMap[noteId]) {
                        noteTexts = [notesMap[noteId]];
                    }
                }
                existingEvaluations[key] = {
                    ...eval,
                    note_ids: noteIds,
                    note_texts: noteTexts
                };
            });
        }

        console.log('[Backend] Existing evaluations mapped by question_id:', existingEvaluations);

        // Calculate global scores from existing evaluations
        // ملاحظة: هذه القيم الأولية من قاعدة البيانات، لكن سيتم تحديثها من allAnswers بعد التحميل
        let backendTotalTajweedDeduction = 0;
        let backendTotalPerformanceDeduction = 0;
        let backendPerQuestionTajweedDeductions = Array(totalQuestions).fill(0);
        let backendPerQuestionPerformanceDeductions = Array(totalQuestions).fill(0);

        if (Object.keys(existingEvaluations).length > 0) {
            questionsData.forEach((questionEl, index) => {
                const questionId = String(questionEl.dataset.questionId);
                if (existingEvaluations[questionId]) {
                    const eval = existingEvaluations[questionId];
                    const tajweedDeduction = parseFloat(eval.tajweed_score || 0);
                    const performanceDeduction = parseFloat(eval.performance_score || 0);

                    backendTotalTajweedDeduction += tajweedDeduction;
                    backendTotalPerformanceDeduction += performanceDeduction;
                    backendPerQuestionTajweedDeductions[index] = tajweedDeduction;
                    backendPerQuestionPerformanceDeductions[index] = performanceDeduction;
                }
            });

            console.log('[Backend] Calculated total deductions (initial from DB):', {
                tajweed: backendTotalTajweedDeduction,
                performance: backendTotalPerformanceDeduction,
                perQuestionTajweed: backendPerQuestionTajweedDeductions,
                perQuestionPerformance: backendPerQuestionPerformanceDeductions
            });
        }

        // Initialize allAnswers - priority: backend > localStorage > default
        let allAnswers = Array(totalQuestions).fill(null).map((_, index) => {
            const questionId = String(questionsData[index].dataset.questionId);
            const localStorageData = loadFromLocalStorage(index);
        
            // 1. الأولوية للبيانات القادمة من قاعدة البيانات (Backend)
            if (existingEvaluations[questionId]) {
                const backendData = existingEvaluations[questionId];
                
                // تحويل بيانات قاعدة البيانات إلى تنسيق يفهمه النظام
                const dbAnswer = {
                    question_id: questionId,
                    participant_id: "{{ $participant_id }}",
                    alert_new_position: String(backendData.alert_new_position || 0),
                    fat7_points: String(backendData.fat7 || 0),
                    tajweed_score: parseFloat(backendData.tajweed_score || 0),
                    performance_score: parseFloat(backendData.performance_score || 0),
                    note_ids: backendData.note_ids || [],
                    note_texts: backendData.note_texts || []
                };
                
                // استخدام alert_rows من localStorage إذا كانت موجودة
                // لأن قاعدة البيانات لا تحفظ alert_rows، لكن localStorage يحفظها دائماً
                // alert_rows في localStorage هي الأحدث دائماً لأنها تُحدث مع كل تغيير
                const backendAlerts = parseInt(dbAnswer.alert_new_position) || 0;
                const backendFat7 = parseInt(dbAnswer.fat7_points) || 0;
                const localStorageAlerts = localStorageData ? (parseInt(localStorageData.alert_new_position) || 0) : 0;
                const localStorageFat7 = localStorageData ? (parseInt(localStorageData.fat7_points) || 0) : 0;
                
                console.log(`[Backend] 🔍 Loading Q${index}:`, {
                    backend: { alerts: backendAlerts, fat7: backendFat7 },
                    localStorage: { alerts: localStorageAlerts, fat7: localStorageFat7 },
                    hasAlertRows: localStorageData && localStorageData.alert_rows && Array.isArray(localStorageData.alert_rows),
                    alertRowsLength: localStorageData && localStorageData.alert_rows ? localStorageData.alert_rows.length : 0
                });
                
                // عند التحميل من قاعدة البيانات بعد الحفظ، نستخدم القيم من قاعدة البيانات أولاً
                // لأنها أحدث من localStorage بعد الحفظ النهائي
                // لكن نستخدم alert_rows من localStorage إذا كانت تطابق القيم من قاعدة البيانات
                
                let useLocalStorageRows = false;
                
                // التحقق من أن alert_rows في localStorage تطابق القيم من قاعدة البيانات
                if (localStorageData && 
                    localStorageData.alert_rows && 
                    Array.isArray(localStorageData.alert_rows) &&
                    localStorageData.alert_rows.length > 0) {
                    
                    // حساب القيم الإجمالية من alert_rows المحفوظة
                    let calculatedAlerts = 0;
                    let calculatedFat7 = 0;
                    localStorageData.alert_rows.forEach(row => {
                        if (row.opened) {
                            calculatedFat7 += 1;
                        } else {
                            calculatedAlerts += row.alerts || 0;
                        }
                    });
                    
                    console.log(`[Backend] 🔍 Validating alert_rows for Q${index}:`, {
                        backendValues: { alerts: backendAlerts, fat7: backendFat7 },
                        localStorageValues: { alerts: localStorageAlerts, fat7: localStorageFat7 },
                        calculatedFromRows: { alerts: calculatedAlerts, fat7: calculatedFat7 },
                        alertRows: localStorageData.alert_rows
                    });
                    
                    // التحقق من أن alert_rows تطابق القيم من قاعدة البيانات (الأولوية للبيانات المحفوظة)
                    if (calculatedAlerts === backendAlerts && calculatedFat7 === backendFat7) {
                        useLocalStorageRows = true;
                        dbAnswer.alert_rows = localStorageData.alert_rows;
                        console.log(`[Backend] ✅ alert_rows match backend values for Q${index}`);
                    } else if (calculatedAlerts === localStorageAlerts && calculatedFat7 === localStorageFat7) {
                        // إذا كانت alert_rows تطابق localStorage لكن لا تطابق قاعدة البيانات
                        // نستخدم القيم من قاعدة البيانات ونبني alert_rows منها
                        console.log(`[Backend] ⚠️ alert_rows match localStorage but not backend - using backend values for Q${index}`);
                    } else {
                        console.log(`[Backend] ⚠️ alert_rows don't match - will rebuild from backend values for Q${index}`);
                    }
                }
                
                // نستخدم القيم من قاعدة البيانات دائماً (لأنها أحدث بعد الحفظ)
                // لكن إذا كانت القيم في localStorage مختلفة، قد تكون هناك تعديلات غير محفوظة
                // في هذه الحالة، نستخدم القيم من localStorage (لأنها أحدث)
                if (localStorageAlerts !== backendAlerts || localStorageFat7 !== backendFat7) {
                    console.log(`[Backend] ⚠️ Values differ - checking which is newer for Q${index}`);
                    
                    // إذا كانت القيم في localStorage أكبر من قاعدة البيانات، قد تكون هناك تعديلات غير محفوظة
                    // لكن بعد الحفظ النهائي، يجب أن تكون القيم في قاعدة البيانات هي الأحدث
                    // لذلك نستخدم القيم من قاعدة البيانات ونحدث localStorage
                    console.log(`[Backend] ✅ Using backend values (saved) for Q${index}`);
                    // dbAnswer.alert_new_position و dbAnswer.fat7_points بالفعل من قاعدة البيانات
                    
                    // إذا لم نستخدم alert_rows من localStorage، سنبنيها من القيم من قاعدة البيانات
                    if (!useLocalStorageRows) {
                        console.log(`[Backend] 🔨 Will rebuild alert_rows from backend values for Q${index}`);
                        // إعادة بناء alert_rows من القيم من قاعدة البيانات
                        let rebuiltRows = [];
                        let fat7Remaining = backendFat7;
                        let alertsRemaining = backendAlerts;
                        
                        // إضافة صفوف الفتح أولاً
                        for(let i=0; i < fat7Remaining; i++) {
                            rebuiltRows.push({ alerts: 0, opened: true });
                        }
                        
                        // إضافة صفوف التنبيهات (كل صف 1 تنبيه)
                        while (alertsRemaining > 0) {
                            const alertsInThisRow = Math.min(alertsRemaining, 1);
                            rebuiltRows.push({ alerts: alertsInThisRow, opened: false });
                            alertsRemaining -= alertsInThisRow;
                        }
                        
                        dbAnswer.alert_rows = rebuiltRows;
                        console.log(`[Backend] ✅ Rebuilt alert_rows from backend values for Q${index}:`, rebuiltRows);
                    }
                } else if (!useLocalStorageRows) {
                    console.log(`[Backend] ⚠️ No valid alert_rows in localStorage for Q${index}, will rebuild from backend totals`);
                    // إعادة بناء alert_rows من القيم من قاعدة البيانات
                    let rebuiltRows = [];
                    let fat7Remaining = backendFat7;
                    let alertsRemaining = backendAlerts;
                    
                    // إضافة صفوف الفتح أولاً
                    for(let i=0; i < fat7Remaining; i++) {
                        rebuiltRows.push({ alerts: 0, opened: true });
                    }
                    
                    // إضافة صفوف التنبيهات (كل صف 1 تنبيه)
                    while (alertsRemaining > 0) {
                        const alertsInThisRow = Math.min(alertsRemaining, 1);
                        rebuiltRows.push({ alerts: alertsInThisRow, opened: false });
                        alertsRemaining -= alertsInThisRow;
                    }
                    
                    dbAnswer.alert_rows = rebuiltRows;
                    console.log(`[Backend] ✅ Rebuilt alert_rows from backend totals for Q${index}:`, rebuiltRows);
                }
                
                // استخدام درجات التجويد والأداء من قاعدة البيانات أولاً (لأنها أحدث بعد الحفظ)
                // لكن إذا كانت القيم في localStorage مختلفة، قد تكون هناك تعديلات غير محفوظة
                // في هذه الحالة، نستخدم القيم من localStorage (لأنها أحدث)
                if (localStorageData) {
                    const localStorageTajweed = parseFloat(localStorageData.tajweed_score) || 0;
                    const localStoragePerformance = parseFloat(localStorageData.performance_score) || 0;
                    const backendTajweed = parseFloat(dbAnswer.tajweed_score) || 0;
                    const backendPerformance = parseFloat(dbAnswer.performance_score) || 0;
                    
                    console.log(`[Backend] 🔍 Comparing scores for Q${index}:`, {
                        tajweed: { backend: backendTajweed, localStorage: localStorageTajweed },
                        performance: { backend: backendPerformance, localStorage: localStoragePerformance }
                    });
                    
                    // بعد الحفظ النهائي، يجب أن تكون القيم في قاعدة البيانات هي الأحدث
                    // لكن إذا كانت القيم في localStorage مختلفة، قد تكون هناك تعديلات غير محفوظة
                    // في هذه الحالة، نستخدم القيم من localStorage (لأنها أحدث)
                    // لكن بشكل عام، نفضل استخدام القيم من قاعدة البيانات بعد الحفظ
                    if (localStorageTajweed !== backendTajweed || localStoragePerformance !== backendPerformance) {
                        // التحقق من أن القيم في localStorage أكبر (قد تكون هناك تعديلات غير محفوظة)
                        // لكن بعد الحفظ، يجب أن تكون القيم في قاعدة البيانات هي المرجع
                        // لذلك نستخدم القيم من قاعدة البيانات ونحدث localStorage
                        console.log(`[Backend] ⚠️ Scores differ - using backend values (saved) for Q${index}`);
                        // dbAnswer.tajweed_score و dbAnswer.performance_score بالفعل من قاعدة البيانات
                    }
                }
        
                // التأكد من وجود alert_rows قبل الحفظ في localStorage
                if (!dbAnswer.alert_rows || !Array.isArray(dbAnswer.alert_rows) || dbAnswer.alert_rows.length === 0) {
                    // إذا لم تكن alert_rows موجودة، نبنيها من القيم من قاعدة البيانات
                    let rebuiltRows = [];
                    let fat7Remaining = parseInt(dbAnswer.fat7_points) || 0;
                    let alertsRemaining = parseInt(dbAnswer.alert_new_position) || 0;
                    
                    // إضافة صفوف الفتح أولاً
                    for(let i=0; i < fat7Remaining; i++) {
                        rebuiltRows.push({ alerts: 0, opened: true });
                    }
                    
                    // إضافة صفوف التنبيهات (كل صف 1 تنبيه)
                    while (alertsRemaining > 0) {
                        const alertsInThisRow = Math.min(alertsRemaining, 1);
                        rebuiltRows.push({ alerts: alertsInThisRow, opened: false });
                        alertsRemaining -= alertsInThisRow;
                    }
                    
                    dbAnswer.alert_rows = rebuiltRows;
                    console.log(`[Backend] ✅ Built alert_rows from backend values for Q${index}:`, rebuiltRows);
                }
                
                // تحديث localStorage بالبيانات من قاعدة البيانات مع الحفاظ على alert_rows إذا كانت صحيحة
                // هذا يضمن أن localStorage يحتوي على البيانات الصحيحة المطابقة لقاعدة البيانات بعد الحفظ
                saveToLocalStorage(index, dbAnswer);
                
                console.log(`[Backend] ✅ Updated localStorage for Q${index} with backend data:`, {
                    alert_new_position: dbAnswer.alert_new_position,
                    fat7_points: dbAnswer.fat7_points,
                    tajweed_score: dbAnswer.tajweed_score,
                    performance_score: dbAnswer.performance_score,
                    alert_rows_count: dbAnswer.alert_rows ? dbAnswer.alert_rows.length : 0
                });
                
                return dbAnswer;
            }
        
            // 2. إذا لم يوجد تقييم في القاعدة، نأخذ من localStorage
            if (localStorageData) return localStorageData;
        
            // 3. الحالة الافتراضية
            return {
                question_id: questionId,
                participant_id: "{{ $participant_id }}",
                alert_new_position: "0",
                fat7_points: "0",
                note_ids: [],
                note_texts: [],
                tajweed_score: 0,
                performance_score: 0
            };
        });
        
        window.allAnswers = allAnswers;
        
        // إعادة بناء تاريخ التنبيهات (Alert History) من البيانات المستعادة
        window.questionsAlertHistory = {};
        allAnswers.forEach((ans, index) => {
            // ⚠️ مهم جداً: يجب بناء history لجميع الأسئلة، حتى لو كانت القيم 0
            // هذا يضمن أن questionsAlertHistory[0] موجود حتى لو كانت القيم 0
            const totalAlerts = ans ? (parseInt(ans.alert_new_position) || 0) : 0;
            const totalFat7 = ans ? (parseInt(ans.fat7_points) || 0) : 0;
            
            let rows = [];
            
            // التحقق من أن alert_rows تطابق القيم الإجمالية
            let useSavedRows = false;
            if (ans && ans.alert_rows && Array.isArray(ans.alert_rows) && ans.alert_rows.length > 0) {
                // حساب القيم الإجمالية من alert_rows
                let calculatedAlerts = 0;
                let calculatedFat7 = 0;
                ans.alert_rows.forEach(row => {
                    if (row.opened) {
                        calculatedFat7 += 1;
                    } else {
                        calculatedAlerts += row.alerts || 0;
                    }
                });
                
                console.log(`[Alert History] 🔍 Validating alert_rows for Q${index}:`, {
                    totals: { alerts: totalAlerts, fat7: totalFat7 },
                    calculated: { alerts: calculatedAlerts, fat7: calculatedFat7 },
                    alertRows: ans.alert_rows
                });
                
                // إذا كانت القيم متطابقة، استخدم alert_rows المحفوظة
                if (calculatedAlerts === totalAlerts && calculatedFat7 === totalFat7) {
                    rows = JSON.parse(JSON.stringify(ans.alert_rows)); // نسخة عميقة
                    useSavedRows = true;
                    console.log(`[Alert History] ✅ Using saved rows (validated) for Q${index}:`, rows);
                } else {
                    console.log(`[Alert History] ⚠️ alert_rows don't match totals - rebuilding for Q${index}`);
                }
            }
            
            // إذا لم نستخدم alert_rows المحفوظة، نبنيها من القيم الإجمالية
            if (!useSavedRows && (totalAlerts > 0 || totalFat7 > 0)) {
                // Fallback: إعادة بناء الصفوف تقريبياً بناءً على الأرقام
                let fat7Remaining = totalFat7;
                let alertsRemaining = totalAlerts;
                let maxAlerts = {{ $alert_before_fat7 }};
        
                console.log(`[Alert History] 🔨 Building rows from totals for Q${index}:`, {
                    fat7Remaining,
                    alertsRemaining,
                    maxAlerts
                });
        
                // إعادة توليد الصفوف المحفوظة
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
    
            // ⚠️ مهم جداً: حفظ history لجميع الأسئلة، حتى لو كانت القيم 0
            // هذا يضمن أن questionsAlertHistory[0] موجود دائماً
            window.questionsAlertHistory[index] = {
                rows: rows,
                totalAlerts: String(totalAlerts),
                totalFat7: String(totalFat7)
            };
            
            console.log(`[Alert History] ✅ Built history for Q${index}:`, {
                rows_count: rows.length,
                totalAlerts: String(totalAlerts),
                totalFat7: String(totalFat7),
                rows: rows
            });
        });
        
        console.log('[Alert History] ✅ All questions history built:', window.questionsAlertHistory);

        // Expose allAnswers globally so other scripts (index.blade.php) can read totals across questions
        window.allAnswers = allAnswers;

        // إعادة حساب الخصومات من allAnswers (بعد التحديث من localStorage)
        // هذا يضمن أن القيم المحدثة من localStorage تُستخدم بدلاً من القيم القديمة من قاعدة البيانات
        let updatedTotalTajweedDeduction = 0;
        let updatedTotalPerformanceDeduction = 0;
        let updatedPerQuestionTajweedDeductions = Array(totalQuestions).fill(0);
        let updatedPerQuestionPerformanceDeductions = Array(totalQuestions).fill(0);
        
        allAnswers.forEach((ans, index) => {
            if (ans) {
                const tDeduct = parseFloat(ans.tajweed_score || 0);
                const pDeduct = parseFloat(ans.performance_score || 0);
                
                updatedPerQuestionTajweedDeductions[index] = tDeduct;
                updatedPerQuestionPerformanceDeductions[index] = pDeduct;
                
                updatedTotalTajweedDeduction += tDeduct;
                updatedTotalPerformanceDeduction += pDeduct;
            }
        });

        // Pass updated deduction data to window for access from index.blade.php
        window.backendTotalTajweedDeduction = updatedTotalTajweedDeduction;
        window.backendTotalPerformanceDeduction = updatedTotalPerformanceDeduction;
        window.backendPerQuestionTajweedDeductions = updatedPerQuestionTajweedDeductions;
        window.backendPerQuestionPerformanceDeductions = updatedPerQuestionPerformanceDeductions;

        console.log('[Backend] ✅ Updated deductions from allAnswers (after localStorage merge):', {
            totalTajweedDeduction: window.backendTotalTajweedDeduction,
            totalPerformanceDeduction: window.backendTotalPerformanceDeduction,
            perQuestionTajweed: window.backendPerQuestionTajweedDeductions,
            perQuestionPerformance: window.backendPerQuestionPerformanceDeductions
        });

        // Role and reveal bootstrap
        const IS_HEAD = {{ ($is_head ?? false) ? 'true' : 'false' }};
        const IS_EDIT_MODE = {{ ($is_edit_mode ?? false) ? 'true' : 'false' }};
        const BRANCH_ID = {{ (int) $competition_version_branch_id }};
        const PARTICIPATION_ID = {{ (int) $participant_id }};
        const COMMITTEE_ID = (new URLSearchParams(window.location.search)).get('committee_id');
        let revealedQuestionIds = @json(($revealedQuestionIds ?? collect())->values()).map(Number);
        
        // Make variables available on window for global access
        window.IS_HEAD = IS_HEAD;
        window.BRANCH_ID = BRANCH_ID;
        window.PARTICIPATION_ID = PARTICIPATION_ID;
        window.COMMITTEE_ID = COMMITTEE_ID;
        window.revealedQuestionIds = revealedQuestionIds;
        
        console.log('[Reveal] Bootstrapped', { IS_HEAD, BRANCH_ID, PARTICIPATION_ID, revealedQuestionIds });

        function getIndexByQuestionId(qId) {
            for (let i = 0; i < questionsData.length; i++) {
                if (parseInt(questionsData[i].dataset.questionId) === parseInt(qId)) return i;
            }
            return -1;
        }

        function ensureListItemForIndex(index) {
            const existing = document.querySelector(`.question-item[data-question-index="${index}"]`);
            if (existing) return existing;
            const qData = questionsData[index];
            if (!qData) return null;
            const container = document.getElementById('questions-list-container');
            if (!container) {
                console.warn('[ensureListItem] ⚠️ Container #questions-list-container not found!');
                return null;
            }

            const surah = qData.dataset.surah;
            const qText = qData.dataset.questionText;

            const div = document.createElement('div');
            div.className = 'question-item relative overflow-hidden rounded-lg p-2 cursor-pointer transition-all duration-300 border bg-slate-50 text-slate-600 border-slate-200';
            div.setAttribute('data-question-index', String(index));
            div.onclick = () => switchToQuestion(index);
            div.innerHTML = `
                <div class="flex items-start gap-2">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center font-bold text-[10px] bg-slate-200 text-slate-500">
                        ${index + 1}
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-xs mb-0 text-slate-800">سورة ${surah}</h4>
                        <p class="text-[10px] leading-snug opacity-90 font-light">${qText.length > 40 ? (qText.slice(0, 40) + '...') : qText}</p>
                    </div>
                </div>`;
            container.appendChild(div);
            console.log('[ensureListItem] ✅ Created new item for index', index);
            return div;
        }

        function updateRevealedListUI(ids) {
            if (IS_HEAD) return; // الرئيس يرى كل القائمة أصلًا
            console.log('[UI Update] 🎨 Updating revealed questions list', {
                revealed_ids: ids,
                count: ids.length
            });
            const container = document.getElementById('questions-list-container');
            if (!container) {
                console.warn('[UI Update] ⚠️ Container #questions-list-container not found!');
                return;
            }

            // أنشئ أو حدّث العناصر بنفس ترتيبها الأصلي
            // نرتب الـ ids حسب الـ index الأصلي في قائمة الأسئلة
            const sortedIds = ids.slice().sort((a, b) => {
                const indexA = getIndexByQuestionId(a);
                const indexB = getIndexByQuestionId(b);
                return indexA - indexB;
            });

            sortedIds.forEach((qId) => {
                const index = getIndexByQuestionId(qId);
                if (index < 0) return;
                const item = ensureListItemForIndex(index);
                if (!item) return;
                // الرقم يبقى نفس الرقم الأصلي (index + 1) - بدون تغيير
                container.appendChild(item); // append يعيد الترتيب حسب الترتيب الأصلي
            });

            // احذف أي عناصر ليست ضمن ids
            container.querySelectorAll('.question-item').forEach(el => {
                const idx = parseInt(el.getAttribute('data-question-index') || '-1');
                const qId = idx >= 0 ? parseInt(questionsData[idx].dataset.questionId) : -1;
                if (!ids.map(Number).includes(qId)) {
                    el.remove();
                }
            });

            // Update highlights after list update
            if (typeof updateQuestionHighlight === 'function') {
                updateQuestionHighlight();
            }

            console.log('[UI Update] ✅ Questions list updated successfully', {
                total_items_now: container.querySelectorAll('.question-item').length
            });
        }

        function tryAutoOpenFirstRevealed() {
            if (IS_HEAD) {
                console.log('[Auto-Open] 👑 Head user - skipping auto-open');
                return;
            }
            
            // التحقق من أن questionsData جاهز
            if (!questionsData || questionsData.length === 0) {
                console.warn('[Auto-Open] ⚠️ questionsData not ready yet, retrying in 200ms');
                setTimeout(tryAutoOpenFirstRevealed, 200);
                return;
            }
            
            console.log('[Auto-Open] 🔓 Attempting to auto-open first revealed question', {
                revealed_count: revealedQuestionIds ? revealedQuestionIds.length : 0,
                revealed_ids: revealedQuestionIds,
                questions_data_length: questionsData.length,
                current_index: currentIndex
            });
            
            if (!revealedQuestionIds || revealedQuestionIds.length === 0) {
                console.log('[Auto-Open] ❌ No revealed questions available');
                return;
            }

            // التحقق من أن السؤال الحالي ليس مكشوفاً بالفعل
            if (currentIndex >= 0 && currentIndex < questionsData.length) {
                const currentQuestionId = parseInt(questionsData[currentIndex].dataset.questionId);
                if (revealedQuestionIds.includes(currentQuestionId)) {
                    console.log('[Auto-Open] ✅ Current question is already revealed, no need to switch', {
                        currentIndex,
                        currentQuestionId
                    });
                    return;
                }
            }

            // أولاً: التحقق من أن السؤال الأول (index 0) مكشوف
            const firstQuestionId = questionsData[0] ? parseInt(questionsData[0].dataset.questionId) : null;
            const isFirstRevealed = firstQuestionId && Array.isArray(revealedQuestionIds) && revealedQuestionIds.includes(firstQuestionId);

            let idx = -1;
            if (isFirstRevealed) {
                // إذا كان السؤال الأول مكشوفاً، استخدمه مباشرة
                idx = 0;
                console.log('[Auto-Open] ✅ First question (index 0) is revealed, using it');
            } else {
                // إذا لم يكن السؤال الأول مكشوفاً، ابحث عن أول سؤال مكشوف
                const qIdFirst = revealedQuestionIds[0];
                idx = getIndexByQuestionId(qIdFirst);
                if (idx === -1) {
                    console.warn('[Auto-Open] ⚠️ Could not find index for question ID:', qIdFirst);
                }
            }

            if (idx >= 0 && idx < questionsData.length) {
                console.log('[Auto-Open] ✅ Opening revealed question', {
                    questionId: questionsData[idx] ? parseInt(questionsData[idx].dataset.questionId) : null,
                    idx,
                    timestamp: new Date().toISOString()
                });
                
                // استخدام switchToQuestion بدلاً من loadQuestion لضمان تنفيذ منطق الإظهار بشكل صحيح
                if (typeof window.switchToQuestion === 'function') {
                    window.switchToQuestion(idx);
                } else if (typeof switchToQuestion === 'function') {
                    switchToQuestion(idx);
                } else {
                    // Fallback: استخدام loadQuestion مباشرة
                    console.warn('[Auto-Open] ⚠️ switchToQuestion not available, using loadQuestion fallback');
                    currentIndex = idx;
                    window.currentIndex = idx;
                    loadQuestion(idx);
                    updateQuestionHighlight();
                    updateFooter();
                    
                    // استرجاع حالة السؤال
                    if (typeof window.restoreQuestionState === 'function') {
                        window.restoreQuestionState(idx);
                    }
                }
                
                // إخفاء رسالة القفل وإظهار المحتوى
                const lockMsg = document.getElementById('member-locked-warning');
                if (lockMsg) { 
                    lockMsg.classList.add('hidden');
                    console.log('[Auto-Open] 🔓 Hidden lock message');
                }
                const formContent = document.getElementById('current-form-content');
                if (formContent) { 
                    formContent.classList.remove('hidden');
                    console.log('[Auto-Open] 📝 Showed form content');
                }
            } else {
                console.warn('[Auto-Open] ⚠️ No revealed question found in list or invalid index', {
                    idx,
                    questions_data_length: questionsData.length
                });
            }
        }

        // Switch to question at given index
        function switchToQuestion(index) {
            console.log('[Alerts] switchToQuestion() start', {
                from: currentIndex,
                to: index,
                hasSave: typeof window.saveCurrentQuestionState,
                hasRestore: typeof window.restoreQuestionState
            });

            // حفظ حالة بوكس التنبيهات/الفتح للسؤال الحالي قبل التبديل
            // يجب أن يتم الحفظ قبل تغيير currentIndex
            if (typeof window.saveCurrentQuestionState === 'function') {
                console.log('[Switch] 💾 Saving state for current question before switch:', currentIndex);
                window.saveCurrentQuestionState(currentIndex);
            }

            // Force save current answer before switching (منطق النظام الأصلي)
            // هذا يحفظ البيانات في allAnswers و localStorage
            // تعيين علامة لتجنب حفظ saveCurrentQuestionState مرة أخرى في saveCurrentAnswer
            window._skipSaveQuestionState = true;
            console.log('[Switch] 💾 Saving current answer before switch');
            saveCurrentAnswer();
            window._skipSaveQuestionState = false;
            currentIndex = index;
            window.currentIndex = index;
            
            // Get question ID for auto-reveal check
            const qId = questionsData[index] ? parseInt(questionsData[index].dataset.questionId) : null;
            
            // If member (not head) and question not revealed yet, lock content
            if (!IS_HEAD) {
                const currentRevealedIds = window.revealedQuestionIds || revealedQuestionIds || [];
                const isRevealed = qId && currentRevealedIds.includes(qId);
                console.log('[Reveal] switchToQuestion check', { index, qId, isRevealed });
                if (IS_EDIT_MODE) {
                    loadQuestion(index);
                } else if (!isRevealed) {
                    lockQuestionView(index);
                } else {
                    loadQuestion(index);
                }
            } else {
                loadQuestion(index);
                
                // ✨ Auto-reveal for head: الأسئلة تظهر تلقائياً لأعضاء اللجنة بمجرد انتقال رئيس اللجنة إليها
                if (IS_HEAD && qId) {
                    const currentRevealedIds = window.revealedQuestionIds || revealedQuestionIds || [];
                    const revealedIdsArray = Array.isArray(currentRevealedIds) ? currentRevealedIds : [];
                    const isAlreadyRevealed = revealedIdsArray.includes(qId);
                    
                    console.log('[Quran Switch] 🔍 Auto-reveal check', {
                        IS_HEAD: IS_HEAD,
                        qId: qId,
                        index: index,
                        currentRevealedIds: revealedIdsArray,
                        isAlreadyRevealed: isAlreadyRevealed,
                        willReveal: !isAlreadyRevealed
                    });
                    
                    if (!isAlreadyRevealed) {
                        console.log('[Quran Switch] 🔓 Head switched to question, auto-revealing...', { qId, index });
                        // Automatically reveal the question without needing button click
                        setTimeout(() => {
                            if (typeof window.autoRevealQuestionForHead === 'function') {
                                console.log('[Quran Switch] ✅ Calling autoRevealQuestionForHead via window');
                                window.autoRevealQuestionForHead(qId);
                            } else if (typeof autoRevealQuestionForHead === 'function') {
                                console.log('[Quran Switch] ✅ Calling autoRevealQuestionForHead directly');
                                autoRevealQuestionForHead(qId);
                            } else {
                                console.warn('[Quran Switch] ⚠️ autoRevealQuestionForHead function not found, will retry...');
                                // Retry after a short delay in case the function isn't loaded yet
                                setTimeout(() => {
                                    if (typeof window.autoRevealQuestionForHead === 'function') {
                                        console.log('[Quran Switch] ✅ Calling autoRevealQuestionForHead (retry)');
                                        window.autoRevealQuestionForHead(qId);
                                    } else {
                                        console.error('[Quran Switch] ❌ autoRevealQuestionForHead function not found after retry');
                                    }
                                }, 500);
                            }
                        }, 300);
                    } else {
                        console.log('[Quran Switch] ⏭️ Question already revealed, skipping auto-reveal', { qId });
                    }
                }
            }
            
            updateQuestionHighlight();
            updateFooter();

            // Update score display for the new question
            if (typeof window.updateScoreDisplay === 'function') {
                window.updateScoreDisplay(index);
            }

            // تحديث المجموع النهائي ليظل عاماً لجميع الأسئلة (مثل التفسير)
            if (typeof window.updateTotalScoreDisplay === 'function') {
                window.updateTotalScoreDisplay();
            }

            // استرجاع حالة بوكس التنبيهات/الفتح للسؤال الجديد
            if (typeof window.restoreQuestionState === 'function') {
                window.restoreQuestionState(index);
            }

            console.log('[Alerts] switchToQuestion() end', {
                currentIndex,
                historyKeys: window.questionsAlertHistory ? Object.keys(window.questionsAlertHistory) : []
            });
        }

        // Load question content
        function loadQuestion(index) {
            const questionData = questionsData[index];
            let pages = [];
            const rawPages = questionData.dataset.pages;
            if (rawPages && rawPages !== 'null') {
                try {
                    pages = JSON.parse(rawPages);
                } catch (error) {
                    console.warn(`[Load Question ${index}] Failed to parse pages`, error);
                }
            }
            pages = Array.isArray(pages) ? pages : [];
            const pageRange = (questionData.dataset.pageRange && questionData.dataset.pageRange !== 'null')
                ? questionData.dataset.pageRange
                : '';

            const originalQuestionText = questionData.dataset.questionText || '';
            const displayQuestionText = originalQuestionText
                .replace(/إلى قوله تعالى\s+"[^"]*"/u, '')
                .replace(/\s{2,}/g, ' ')
                .trim();

            // Get ayah details for display
            const startAyahNum = questionData.dataset.startAyahNumber || '';
            const endAyahNum = questionData.dataset.endAyahNumber || '';
            const endSurah = questionData.dataset.endSurah || '';
            const surah = questionData.dataset.surah || '';

            // Build ayah details text
            let ayahDetailsHtml = '';
            if (startAyahNum || endAyahNum) {
                if (endSurah && endSurah !== surah) {
                    ayahDetailsHtml = `
                        <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle ml-1"></i>
                            من سورة ${surah} آية ${startAyahNum} إلى سورة ${endSurah} آية ${endAyahNum}
                        </div>
                    `;
                } else {
                    ayahDetailsHtml = `
                        <div class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle ml-1"></i>
                            من سورة ${surah} آية ${startAyahNum} إلى آية ${endAyahNum}
                        </div>
                    `;
                }
            }

            // Add reveal button container for head
            const isHead = {{ ($is_head ?? false) ? 'true' : 'false' }};
            const revealBtnHtml = isHead ? '<div id="reveal-btn-in-question"></div>' : '';

            questionContent.innerHTML = `
            <!-- Unified Content Area -->
            <div class="unified-content space-y-6">
                <!-- Question Section -->
                <div class="question-section bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <i class="fas fa-question-circle text-primary ml-2 text-xl"></i>
                            <h3 class="text-lg font-bold text-primary">السؤال</h3>
                        </div>
                        ${revealBtnHtml}
                    </div>
                    <div class="text-gray-800 dark:text-gray-200 leading-relaxed" style="font-size: 1.125rem; line-height: 1.75; font-weight: 500;">
                        ${displayQuestionText}
                    </div>
                    ${ayahDetailsHtml}
                </div>

                <!-- Quran Text Section - تصميم المصحف -->
                <div class="mushaf-container">
                    <!-- عارض المصحف -->
                    <div class="mushaf-viewer mt-6" id="mushaf-viewer">
                        <div class="mushaf-viewer-header flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3">
                                <div class="text-sm font-semibold text-primary" id="mushaf-viewer-range">
                                    ${pageRange ? `نطاق الوجه: ${pageRange}` : 'نطاق الوجه غير متوفر'}
                                </div>
                                <div class="text-xs text-secondary" id="mushaf-face-caption"></div>
                            </div>
                            <div class="mushaf-viewer-controls flex flex-col md:flex-row md:items-center gap-2">
                                <div class="flex items-center gap-2">
                                    <label for="mushaf-face-from" class="text-xs font-medium text-secondary">من</label>
                                    <input type="number" id="mushaf-face-from" class="form-input mushaf-face-input" min="1" placeholder="من">
                                    <span class="text-xs font-medium text-secondary">إلى</span>
                                    <input type="number" id="mushaf-face-to" class="form-input mushaf-face-input" min="1" placeholder="إلى">
                                    <button type="button" id="mushaf-face-apply" class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-primary text-white text-xs font-medium shadow-sm hover:bg-primary-dark transition">
                                        <i class="fas fa-search ml-1"></i> عرض
                                    </button>
                                </div>
                                <div class="flex items-center gap-2 justify-end">
                                    <button type="button" id="mushaf-face-prev" class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium transition">
                                        <i class="fas fa-angle-right ml-1"></i> السابق
                                    </button>
                                    <button type="button" id="mushaf-face-next" class="inline-flex items-center justify-center px-3 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium transition">
                                        التالي <i class="fas fa-angle-left mr-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mushaf-viewer-images mt-4 grid grid-cols-1 md:grid-cols-2 gap-4" id="mushaf-viewer-images">
                            <div class="mushaf-face-card hidden" id="mushaf-face-left-wrapper">
                                <img id="mushaf-face-left" class="mushaf-face-img" alt="الوجه الأيمن">
                            </div>
                            <div class="mushaf-face-card hidden" id="mushaf-face-right-wrapper">
                                <img id="mushaf-face-right" class="mushaf-face-img" alt="الوجه الأيسر">
                            </div>
                        </div>
                        <div class="mushaf-viewer-message mt-3 text-sm hidden" id="mushaf-face-message"></div>
                    </div>
                </div>
            </div>
        `;

            setupMushafViewer(pages, pageRange);

            // Inject reveal button if head
            if (IS_HEAD) {
                const revealContainer = document.getElementById('reveal-btn-in-question');
                if (revealContainer) {
                    revealContainer.innerHTML = `
                        <button id="reveal-question-btn" type="button" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5">
                            <i class="fas fa-eye text-xs"></i>
                            <span>إظهار للجميع</span>
                        </button>`;
                    updateRevealButtonState();
                }
            }

            // Update form data attributes
            currentAnswerForm.dataset.questionId = questionData.dataset.questionId;
            currentAnswerForm.dataset.participantId = "{{ $participant_id }}";

            // Load saved data from allAnswers OR localStorage
            const savedData = allAnswers[index] || loadFromLocalStorage(index);

            console.log(`[Load Question ${index}] Saved data:`, savedData);

            // Note: We don't set alert_new_position and fat7_points here because
            // restoreQuestionState() will handle that after loadQuestion() completes.
            // This ensures the values come from questionsAlertHistory or allAnswers correctly.
            
            const alertSameValue = savedData ? (savedData.alert_same_position || "0") : "0";
            currentAnswerForm.querySelector('[name="alert_same_position"]').value = alertSameValue;
            
            // alert_new_position and fat7_points will be set by restoreQuestionState()

            // Load note selection
            const noteIdsField = currentAnswerForm.querySelector('#note-ids');
            const noteTextsField = currentAnswerForm.querySelector('#note-texts');

            // Load multiple notes
            if (noteIdsField && noteTextsField) {
                try {
                    const savedNoteIds = savedData?.note_ids ? (Array.isArray(savedData.note_ids) ? savedData.note_ids : JSON.parse(savedData.note_ids)) : [];
                    const savedNoteTexts = savedData?.note_texts ? (Array.isArray(savedData.note_texts) ? savedData.note_texts : JSON.parse(savedData.note_texts)) : [];

                    noteIdsField.value = JSON.stringify(savedNoteIds);
                    noteTextsField.value = JSON.stringify(savedNoteTexts);

                    // Function to apply notes with retries
                    const applyNotesToSelect2 = (retryCount = 0) => {
                        const maxRetries = 3;

                        // Update Select2 with saved notes
                        if (window.jQuery && typeof window.jQuery.fn.select2 === 'function') {
                            const $select = window.jQuery('#unified-note-select');
                            if ($select.length && $select.data('select2')) {
                                // Select2 is ready
                                $select.val(savedNoteIds.map(String)).trigger('change');
                            } else if (retryCount < maxRetries) {
                                setTimeout(() => applyNotesToSelect2(retryCount + 1), 150);
                                return;
                            }
                        }

                        // Update display
                        if (typeof window.updateSelectedNotesDisplay === 'function') {
                            window.updateSelectedNotesDisplay(savedNoteIds, savedNoteTexts);
                        } else if (retryCount < maxRetries) {
                            setTimeout(() => applyNotesToSelect2(retryCount + 1), 150);
                            return;
                        }
                    };

                    // Apply notes
                    applyNotesToSelect2();
                    setTimeout(() => applyNotesToSelect2(), 200);
                    setTimeout(() => applyNotesToSelect2(), 400);
                } catch (e) {
                    console.error('Error loading notes:', e);
                }
            }

            // Update visual indicator
            updateWarningsDisplay();

            // Update score display for the loaded question
            if (typeof window.updateScoreDisplay === 'function') {
                setTimeout(() => {
                    window.updateScoreDisplay(index);
                }, 100);
            }

            // إعادة تهيئة بوكس التنبيهات/الفتح للسؤال الحالي بعد تحميل بيانات السؤال
            if (typeof window.initializeAlertOpenSystem === 'function') {
                setTimeout(() => {
                    window.initializeAlertOpenSystem();
                }, 0);
            }

            // Update reveal button state for head
            if (typeof updateRevealButtonState === 'function') {
                setTimeout(() => {
                    updateRevealButtonState();
                }, 150);
            }
        }

        function setupMushafViewer(pages, pageRange) {
            mushafState.pages = normalizePagesList(pages, pageRange);
            mushafState.range = pageRange || collapsePagesToRange(mushafState.pages);
            mushafState.index = 0;

            const rangeLabel = document.getElementById('mushaf-viewer-range');
            if (rangeLabel) {
                rangeLabel.textContent = mushafState.range
                    ? `نطاق الوجه: ${mushafState.range}`
                    : 'نطاق الوجه غير متوفر';
            }

            const fromInput = document.getElementById('mushaf-face-from');
            const toInput = document.getElementById('mushaf-face-to');
            const imagesWrapper = document.getElementById('mushaf-viewer-images');
            const firstPage = mushafState.pages.length ? mushafState.pages[0] : null;
            const lastPage = mushafState.pages.length ? mushafState.pages[mushafState.pages.length - 1] : null;

            if (fromInput) {
                fromInput.value = firstPage !== null ? firstPage : '';
                fromInput.min = firstPage !== null ? firstPage : 1;
                fromInput.max = lastPage !== null ? lastPage : 604;
            }

            if (toInput) {
                const toValue = mushafState.pages.length > 1 && lastPage !== null
                    ? lastPage
                    : (firstPage !== null ? firstPage : '');
                toInput.value = toValue;
                toInput.min = firstPage !== null ? firstPage : 1;
                toInput.max = lastPage !== null ? lastPage : 604;
            }

            if (!mushafState.pages.length) {
                if (imagesWrapper) {
                    imagesWrapper.classList.add('hidden');
                }
                const caption = document.getElementById('mushaf-face-caption');
                if (caption) {
                    caption.textContent = '';
                }
                setMushafMessage('لم يتم العثور على صور للمصحف ضمن هذا النطاق.');
                updateMushafButtons();
                attachMushafControls({ fromInput, toInput });
                return;
            }

            if (imagesWrapper) {
                imagesWrapper.classList.remove('hidden');
            }

            setMushafMessage('');
            renderMushafFaces();
            attachMushafControls({ fromInput, toInput });
        }

        function attachMushafControls({ fromInput, toInput }) {
            const prevBtn = document.getElementById('mushaf-face-prev');
            const nextBtn = document.getElementById('mushaf-face-next');
            const applyBtn = document.getElementById('mushaf-face-apply');

            if (prevBtn) {
                prevBtn.onclick = () => moveMushaf(-2);
            }
            if (nextBtn) {
                nextBtn.onclick = () => moveMushaf(2);
            }
            if (applyBtn) {
                applyBtn.onclick = () => {
                    if (!mushafState.pages.length) {
                        setMushafMessage('لا توجد صور متاحة لهذا السؤال.');
                        return;
                    }
                    const start = fromInput ? parseInt(fromInput.value, 10) : NaN;
                    const end = toInput ? parseInt(toInput.value, 10) : NaN;

                    if (!Number.isFinite(start)) {
                        setMushafMessage('يرجى إدخال رقم الوجه المراد عرضه.');
                        return;
                    }

                    let targetIndex = mushafState.pages.findIndex(page => page >= start);
                    if (targetIndex < 0) {
                        setMushafMessage('الوجه المطلوب غير موجود ضمن النطاق الحالي.');
                        return;
                    }

                    if (Number.isFinite(end) && end >= start) {
                        const lastIndexWithinRange = findLastIndexInclusive(mushafState.pages, page => page <= end);
                        if (lastIndexWithinRange >= 0) {
                            targetIndex = Math.min(targetIndex, lastIndexWithinRange);
                        }
                    }

                    mushafState.index = clampMushafIndex(targetIndex - (targetIndex % 2));
                    renderMushafFaces();
                    setMushafMessage('');
                };
            }
        }

        function normalizePagesList(pages, pageRange) {
            const numericPages = Array.isArray(pages)
                ? pages.map(Number).filter(page => Number.isFinite(page) && page > 0)
                : [];
            if (numericPages.length) {
                return Array.from(new Set(numericPages)).sort((a, b) => a - b);
            }
            return expandPageRangeArray(pageRange);
        }

        function expandPageRangeArray(pageRange) {
            if (!pageRange || typeof pageRange !== 'string') {
                return [];
            }
            const normalized = pageRange.replace(/–/g, '-').trim();
            if (!normalized) {
                return [];
            }
            if (normalized.includes('-')) {
                const [rawStart, rawEnd] = normalized.split('-', 2);
                const start = parseInt(rawStart, 10);
                let end = parseInt(rawEnd, 10);
                if (!Number.isFinite(start)) {
                    return [];
                }
                if (!Number.isFinite(end)) {
                    end = start;
                }
                const rangeStart = Math.min(start, end);
                const rangeEnd = Math.max(start, end);
                const values = [];
                for (let page = rangeStart; page <= rangeEnd; page++) {
                    if (page > 0) {
                        values.push(page);
                    }
                }
                return values;
            }
            const single = parseInt(normalized, 10);
            return Number.isFinite(single) && single > 0 ? [single] : [];
        }

        function collapsePagesToRange(pages) {
            if (!Array.isArray(pages) || !pages.length) {
                return '';
            }
            const normalized = Array.from(new Set(pages.map(Number).filter(page => Number.isFinite(page) && page > 0))).sort((a, b) => a - b);
            if (!normalized.length) {
                return '';
            }
            const first = normalized[0];
            const last = normalized[normalized.length - 1];
            return first === last ? String(first) : `${first}-${last}`;
        }

        function clampMushafIndex(index) {
            if (!mushafState.pages.length) {
                return 0;
            }
            const pagesLength = mushafState.pages.length;
            if (pagesLength === 1) {
                return 0;
            }
            const maxIndex = pagesLength % 2 === 0 ? pagesLength - 2 : pagesLength - 1;
            return Math.max(0, Math.min(index, maxIndex));
        }

        function moveMushaf(delta) {
            if (!mushafState.pages.length) {
                return;
            }
            const newIndex = clampMushafIndex(mushafState.index + delta);
            if (newIndex !== mushafState.index) {
                mushafState.index = newIndex;
                renderMushafFaces();
                setMushafMessage('');
            }
        }

        function renderMushafFaces() {
            if (!mushafState.pages.length) {
                updateMushafButtons();
                return;
            }

            const leftPage = mushafState.pages.length > mushafState.index
                ? mushafState.pages[mushafState.index]
                : null;
            const rightPage = mushafState.pages.length > mushafState.index + 1
                ? mushafState.pages[mushafState.index + 1]
                : null;

            const leftWrapper = document.getElementById('mushaf-face-left-wrapper');
            const rightWrapper = document.getElementById('mushaf-face-right-wrapper');
            const leftImg = document.getElementById('mushaf-face-left');
            const rightImg = document.getElementById('mushaf-face-right');
            const caption = document.getElementById('mushaf-face-caption');
            const fromInput = document.getElementById('mushaf-face-from');
            const toInput = document.getElementById('mushaf-face-to');

            setMushafFace(leftWrapper, leftImg, leftPage);
            setMushafFace(rightWrapper, rightImg, rightPage);

            if (caption) {
                if (leftPage && rightPage) {
                    caption.textContent = `المعرض الآن: ${leftPage}-${rightPage}`;
                } else if (leftPage) {
                    caption.textContent = `المعرض الآن: ${leftPage}`;
                } else {
                    caption.textContent = '';
                }
            }

            if (fromInput) {
                fromInput.value = leftPage ?? '';
            }
            if (toInput) {
                toInput.value = rightPage ?? leftPage ?? '';
            }

            updateMushafButtons();
        }

        function setMushafFace(wrapper, img, page) {
            if (!wrapper || !img) {
                return;
            }
            if (!page) {
                wrapper.classList.add('hidden');
                img.removeAttribute('src');
                img.removeAttribute('data-current-page');
                return;
            }
            wrapper.classList.remove('hidden');
            const previousPage = wrapper.getAttribute('data-current-page');
            if (previousPage !== String(page)) {
                wrapper.setAttribute('data-current-page', String(page));
                img.setAttribute('data-current-page', String(page));
                const cacheBustedSrc = `${buildMushafSrc(page)}?v=${page}&t=${Date.now()}`;
                img.removeAttribute('src');
                img.src = cacheBustedSrc;
            }
            img.alt = `الوجه ${page}`;
        }

        function buildMushafSrc(page) {
            const padded = String(page).padStart(3, '0');
            return `${MUSHAF_ASSET_BASE}${padded}.png`;
        }

        function setMushafMessage(message) {
            const msgEl = document.getElementById('mushaf-face-message');
            if (!msgEl) {
                return;
            }
            if (message) {
                msgEl.textContent = message;
                msgEl.classList.remove('hidden');
            } else {
                msgEl.textContent = '';
                msgEl.classList.add('hidden');
            }
        }

        function updateMushafButtons() {
            const prevBtn = document.getElementById('mushaf-face-prev');
            const nextBtn = document.getElementById('mushaf-face-next');

            if (!prevBtn || !nextBtn) {
                return;
            }

            if (!mushafState.pages.length) {
                prevBtn.disabled = true;
                nextBtn.disabled = true;
                return;
            }

            prevBtn.disabled = mushafState.index <= 0;
            const pagesLength = mushafState.pages.length;
            const maxIndex = pagesLength % 2 === 0 ? pagesLength - 2 : pagesLength - 1;
            nextBtn.disabled = mushafState.index >= maxIndex;
        }

        function findLastIndexInclusive(array, predicate) {
            for (let i = array.length - 1; i >= 0; i--) {
                if (predicate(array[i], i)) {
                    return i;
                }
            }
            return -1;
        }

        function lockQuestionView(index) {
            if (IS_EDIT_MODE) {
                loadQuestion(index);
                return;
            }
            const questionData = questionsData[index];
            const questionContent = document.getElementById('question-content');
            const qId = questionData.dataset.questionId;
            questionContent.innerHTML = `
            <div class="unified-content space-y-6">
                <div class="question-section bg-white dark:bg-gray-800 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6 shadow-sm">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-lock text-yellow-600 ml-2 text-xl"></i>
                        <h3 class="text-lg font-bold text-yellow-700">بانتظار رئيس اللجنة</h3>
                    </div>
                    <div class="text-gray-700 dark:text-gray-300">هذا السؤال سيتم عرضه هنا بمجرد أن يقوم رئيس اللجنة بإظهاره.</div>
                </div>
            </div>`;

        }

        // Update question highlighting in the list
        function updateQuestionHighlight() {
            document.querySelectorAll('.question-item').forEach((item, i) => {
                const numberBadge = item.querySelector('.flex-shrink-0');
                const titleElement = item.querySelector('.font-bold');

                if (i === currentIndex) {
                    // Selected state
                    item.classList.add('bg-blue-900', 'text-white', 'shadow-md', 'border-blue-900', 'scale-[1.01]');
                    item.classList.remove('bg-slate-50', 'text-slate-600', 'border-slate-200');
                    if (numberBadge) {
                        numberBadge.classList.add('bg-white', 'text-blue-900');
                        numberBadge.classList.remove('bg-slate-200', 'text-slate-500');
                    }
                    if (titleElement) {
                        titleElement.classList.add('text-blue-100');
                        titleElement.classList.remove('text-slate-800');
                    }
                } else {
                    // Unselected state
                    item.classList.remove('bg-blue-900', 'text-white', 'shadow-md', 'border-blue-900', 'scale-[1.01]');
                    item.classList.add('bg-slate-50', 'text-slate-600', 'border-slate-200');
                    if (numberBadge) {
                        numberBadge.classList.remove('bg-white', 'text-blue-900');
                        numberBadge.classList.add('bg-slate-200', 'text-slate-500');
                    }
                    if (titleElement) {
                        titleElement.classList.remove('text-blue-100');
                        titleElement.classList.add('text-slate-800');
                    }
                }
            });
        }

        // Update warnings display
        function updateWarningsDisplay() {
            const warningsSameDisplay = document.getElementById('current-warnings-same');
            const warningsNewDisplay = document.getElementById('current-warnings-new');

            // قراءة الحد الأقصى من خاصية data-max-alerts إن وُجدت
            const maxAlertsAttr = document.querySelector('[data-max-alerts]')?.dataset.maxAlerts;
            const maxAlerts = parseInt(maxAlertsAttr || '0') || 0;

            // Log for debugging
            const alertSameCount = 0;
            const alertNewCount = parseInt(currentAnswerForm.querySelector('[name="alert_new_position"]')?.value || 0);

            console.log('[Warnings] Update display:', {
                alertSameCount,
                alertNewCount,
                maxAlerts,
                sameDisplayExists: !!warningsSameDisplay,
                newDisplayExists: !!warningsNewDisplay
            });

            if (warningsSameDisplay) {
                warningsSameDisplay.textContent = `تنبيهات نفس الموضع: ${alertSameCount}`;

                // Change color based on warning count
                if (alertSameCount >= maxAlerts) {
                    warningsSameDisplay.className = 'text-red-600 dark:text-red-400 font-bold';
                } else if (alertSameCount > maxAlerts * 0.7) {
                    warningsSameDisplay.className = 'text-orange-600 dark:text-orange-400 font-semibold';
                } else {
                    warningsSameDisplay.className = '';
                }
            }

            if (warningsNewDisplay) {
                warningsNewDisplay.textContent = `تنبيهات موضع جديد: ${alertNewCount}`;

                // Change color based on warning count
                if (alertNewCount >= maxAlerts) {
                    warningsNewDisplay.className = 'text-red-600 dark:text-red-400 font-bold';
                } else if (alertNewCount > maxAlerts * 0.7) {
                    warningsNewDisplay.className = 'text-orange-600 dark:text-orange-400 font-semibold';
                } else {
                    warningsNewDisplay.className = '';
                }
            }
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

        // Show modal function (same as in index.blade.php)
        function showSaveConfirmationModal() {
            console.log('showSaveConfirmationModal called');
            const modal = document.getElementById('save-confirmation-modal');
            console.log('Modal element found:', !!modal);

            if (modal) {
                // Remove hidden class and add flex class
                modal.classList.remove('hidden');
                modal.classList.add('flex');

                // Force visibility with inline styles as backup
                modal.style.display = 'flex';
                modal.style.position = 'fixed';
                modal.style.top = '0';
                modal.style.left = '0';
                modal.style.width = '100%';
                modal.style.height = '100%';
                modal.style.zIndex = '9999';
                modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';

                console.log('Modal should now be visible');
            } else {
                console.error('Modal element not found!');
                // Fallback: create modal if not found
                console.log('Attempting to create modal as fallback...');
                createFallbackModal();
            }
        }

        // Fallback function to create modal if not found
        function createFallbackModal() {
            const modal = document.createElement('div');
            modal.id = 'save-confirmation-modal';
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex z-50 items-center justify-center';
            modal.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <div class="mr-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">خطأ في النظام</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">حدث خطأ في عرض المودال. يرجى إعادة تحميل الصفحة.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="this.parentElement.parentElement.parentElement.remove()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg">
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>
        `;
            document.body.appendChild(modal);
        }

        // Hide modal function
        function hideSaveConfirmationModal() {
            console.log('hideSaveConfirmationModal called');
            const modal = document.getElementById('save-confirmation-modal');
            console.log('Modal to hide:', modal);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                // Reset inline styles
                modal.style.display = '';
                modal.style.position = '';
                modal.style.top = '';
                modal.style.left = '';
                modal.style.width = '';
                modal.style.height = '';
                modal.style.zIndex = '';
                console.log('Modal hidden successfully');
            }
        }

        // Make functions global
        window.switchToQuestion = switchToQuestion;
        window.toggleCurrentEvaluationForm = toggleCurrentEvaluationForm;
        window.finalizeEvaluations = finalizeEvaluations;
        window.showSaveConfirmationModal = showSaveConfirmationModal;
        window.hideSaveConfirmationModal = hideSaveConfirmationModal;
        window.showCustomNotification = showCustomNotification;


        // Update footer text and next button
        function updateFooter() {
            const footerText = document.getElementById('footer-text');
            const nextBtn = document.querySelector('.next-btn');
            const nextBtnText = document.getElementById('next-btn-text');
            const prevBtn = document.querySelector('.prev-btn');
        
            if (!footerText || !nextBtn) return;
        
            // 1. تحديث نص العداد (السؤال X من أصل Y)
            footerText.textContent = `السؤال ${currentIndex + 1} من أصل ${totalQuestions}`;
        
            // 2. فحص حالة الأسئلة المكشوفة (للأعضاء فقط)
            const revealedCount = revealedQuestionIds.length;
            
            // حساب عدد الأسئلة الفعلية المكشوفة (الموجودة في questionsData)
            let actualRevealedCount = 0;
            if (!IS_HEAD) {
                for (let i = 0; i < totalQuestions; i++) {
                    const qId = parseInt(questionsData[i].dataset.questionId);
                    if (revealedQuestionIds.includes(qId)) {
                        actualRevealedCount++;
                    }
                }
            } else {
                actualRevealedCount = totalQuestions; // الرئيس لديه كل الأسئلة
            }
            
            // التحقق من أن جميع الأسئلة المكشوفة موجودة في questionsData
            const allRevealed = (actualRevealedCount === totalQuestions);
        
            // هل يوجد سؤال مكشوف بعد السؤال الحالي؟
            let hasNextRevealed = false;
            if (!IS_HEAD) {
                for (let i = currentIndex + 1; i < totalQuestions; i++) {
                    const qId = parseInt(questionsData[i].dataset.questionId);
                    if (revealedQuestionIds.includes(qId)) {
                        hasNextRevealed = true;
                        break;
                    }
                }
            } else {
                // الرئيس لديه كل الأسئلة
                hasNextRevealed = (currentIndex < totalQuestions - 1);
            }
        
            // 3. تحديد حالة الزر (Disabled vs Enabled) والنص المعروض
            
            // الحالة الأولى: عضو لجنة وصل لآخر ما تم كشفه، والرئيس لم يكشف الباقي بعد
            if (!IS_HEAD && !hasNextRevealed && !allRevealed) {
                nextBtn.disabled = true;
                nextBtn.classList.add('opacity-50', 'cursor-not-allowed');
                const waitingHtml = 'بانتظار إظهار الأسئلة... <i class="fas fa-lock ml-2"></i>';
                if (nextBtnText) nextBtnText.innerHTML = waitingHtml;
                else nextBtn.innerHTML = waitingHtml;
                
                nextBtn.dataset.isLast = 'false';
            } 
            // الحالة الثانية: يوجد سؤال تالي مكشوف أو تم كشف كل الأسئلة (مرحلة الإنهاء)
            else {
                nextBtn.disabled = false;
                nextBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        
                // هل نحن في آخر سؤال في المسابقة تماماً؟
                const isAbsoluteLast = (currentIndex === totalQuestions - 1);
        
                if (isAbsoluteLast) {
                    // إذا كان هناك مجال تالي في الفرع متعدد المجالات، اعرض "حفظ والانتقال لمجال [اسم المجال]"
                    let saveHtml;
                    console.log('[Quran Footer] Updating save button text', {
                        isAbsoluteLast: isAbsoluteLast,
                        nextFieldName: nextFieldName,
                        hasNextField: !!nextFieldName
                    });
                    if (nextFieldName) {
                        saveHtml = `حفظ والانتقال لمجال ${nextFieldName} <i class="fas fa-save ml-2"></i>`;
                    } else {
                        saveHtml = 'إنهاء وحفظ <i class="fas fa-save ml-2"></i>';
                    }
                    if (nextBtnText) nextBtnText.innerHTML = saveHtml;
                    else nextBtn.innerHTML = saveHtml;
                    nextBtn.dataset.isLast = 'true';
                } else {
                    const nextHtml = 'التالي <i class="fas fa-arrow-left ml-2"></i>';
                    if (nextBtnText) nextBtnText.innerHTML = nextHtml;
                    else nextBtn.innerHTML = nextHtml;
                    nextBtn.dataset.isLast = 'false';
                }
            }
        
            // 4. تحديث حالة زر "السابق"
            if (prevBtn) {
                prevBtn.disabled = (currentIndex === 0);
                prevBtn.classList.toggle('opacity-50', currentIndex === 0);
            }
        }

        // Save current question to local array
        function saveCurrentAnswer() {
            const currentIdx = window.currentIndex;
            const form = document.getElementById('current-answer-form');
            if (!form) return;
        
            // حفظ حالة التنبيهات في المصفوفة التاريخية قبل الحفظ العام
            // ملاحظة: لا نحفظ هنا إذا كان سيتم الحفظ من switchToQuestion
            // لأن switchToQuestion يستدعي saveCurrentQuestionState قبل saveCurrentAnswer
            // لكن إذا تم استدعاء saveCurrentAnswer مباشرة (مثل من syncAlertSystemToInputs)، نحفظ هنا
            if (typeof window.saveCurrentQuestionState === 'function' && !window._skipSaveQuestionState) {
                window.saveCurrentQuestionState(currentIdx);
            }
        
            // جلب تفاصيل الصفوف من questionsAlertHistory (يجب أن تكون محدثة الآن)
            let alertRows = [];
            if (window.questionsAlertHistory && window.questionsAlertHistory[currentIdx]) {
                alertRows = window.questionsAlertHistory[currentIdx].rows || [];
                console.log(`[Save Answer] ✅ Using alert_rows from questionsAlertHistory for Q${currentIdx}:`, alertRows);
            } else {
                // إذا لم تكن موجودة في questionsAlertHistory، نحفظ الحالة الحالية
                if (typeof window.saveCurrentQuestionState === 'function') {
                    window.saveCurrentQuestionState(currentIdx);
                    if (window.questionsAlertHistory && window.questionsAlertHistory[currentIdx]) {
                        alertRows = window.questionsAlertHistory[currentIdx].rows || [];
                        console.log(`[Save Answer] ✅ Saved and retrieved alert_rows for Q${currentIdx}:`, alertRows);
                    }
                }
            }
        
            const alertNewPosition = form.querySelector('[name="alert_new_position"]').value || "0";
            const fat7Points = form.querySelector('[name="fat7_points"]').value || "0";
            
            // التحقق من أن alert_rows تطابق القيم الإجمالية
            let calculatedAlerts = 0;
            let calculatedFat7 = 0;
            alertRows.forEach(row => {
                if (row.opened) {
                    calculatedFat7 += 1;
                } else {
                    calculatedAlerts += row.alerts || 0;
                }
            });
            
            const totalAlerts = parseInt(alertNewPosition) || 0;
            const totalFat7 = parseInt(fat7Points) || 0;
            
            console.log(`[Save Answer] 🔍 Validating alert_rows for Q${currentIdx}:`, {
                totals: { alerts: totalAlerts, fat7: totalFat7 },
                calculated: { alerts: calculatedAlerts, fat7: calculatedFat7 },
                alertRows: alertRows
            });
            
            // إذا كانت alert_rows لا تطابق القيم الإجمالية، نبنيها من القيم الحالية
            if (calculatedAlerts !== totalAlerts || calculatedFat7 !== totalFat7) {
                console.log(`[Save Answer] ⚠️ alert_rows don't match totals - rebuilding for Q${currentIdx}`);
                // إعادة بناء alert_rows من القيم الحالية
                alertRows = [];
                let fat7Remaining = totalFat7;
                let alertsRemaining = totalAlerts;
                
                for(let i=0; i < fat7Remaining; i++) {
                    alertRows.push({ alerts: 0, opened: true });
                }
                while (alertsRemaining > 0) {
                    const alertsInThisRow = Math.min(alertsRemaining, 1);
                    alertRows.push({ alerts: alertsInThisRow, opened: false });
                    alertsRemaining -= alertsInThisRow;
                }
                
                // تحديث questionsAlertHistory
                if (window.questionsAlertHistory) {
                    window.questionsAlertHistory[currentIdx] = {
                        rows: alertRows,
                        totalAlerts: String(totalAlerts),
                        totalFat7: String(totalFat7)
                    };
                }
                
                console.log(`[Save Answer] ✅ Rebuilt alert_rows for Q${currentIdx}:`, alertRows);
            }
        
            // استخدام القيم من window.perQuestionTajweedDeduction و window.perQuestionPerformanceDeduction
            // إذا لم تكن متوفرة، استخدم القيم من allAnswers الحالية
            let tajweedScore = 0;
            let performanceScore = 0;
            
            if (window.perQuestionTajweedDeduction && window.perQuestionTajweedDeduction[currentIdx] !== undefined) {
                tajweedScore = window.perQuestionTajweedDeduction[currentIdx] || 0;
            } else if (window.allAnswers && window.allAnswers[currentIdx]) {
                tajweedScore = parseFloat(window.allAnswers[currentIdx].tajweed_score || 0);
            }
            
            if (window.perQuestionPerformanceDeduction && window.perQuestionPerformanceDeduction[currentIdx] !== undefined) {
                performanceScore = window.perQuestionPerformanceDeduction[currentIdx] || 0;
            } else if (window.allAnswers && window.allAnswers[currentIdx]) {
                performanceScore = parseFloat(window.allAnswers[currentIdx].performance_score || 0);
            }
            
            console.log(`[Save Answer] 💾 Saving scores for Q${currentIdx}:`, {
                tajweed: tajweedScore,
                performance: performanceScore,
                fromWindow: {
                    tajweed: window.perQuestionTajweedDeduction ? window.perQuestionTajweedDeduction[currentIdx] : 'N/A',
                    performance: window.perQuestionPerformanceDeduction ? window.perQuestionPerformanceDeduction[currentIdx] : 'N/A'
                },
                fromAllAnswers: {
                    tajweed: window.allAnswers && window.allAnswers[currentIdx] ? window.allAnswers[currentIdx].tajweed_score : 'N/A',
                    performance: window.allAnswers && window.allAnswers[currentIdx] ? window.allAnswers[currentIdx].performance_score : 'N/A'
                }
            });
            
            const answerData = {
                question_id: form.dataset.questionId,
                participant_id: form.dataset.participantId,
                alert_new_position: alertNewPosition,
                fat7_points: fat7Points,
                tajweed_score: tajweedScore,
                performance_score: performanceScore,
                note_ids: JSON.parse(document.getElementById('note-ids')?.value || "[]"),
                note_texts: JSON.parse(document.getElementById('note-texts')?.value || "[]"),
                alert_rows: alertRows // حفظ تفاصيل الصفوف المحدثة
            };
        
            window.allAnswers[currentIdx] = answerData;
            saveToLocalStorage(currentIdx, answerData);
            
            console.log(`[Save Answer] ✅ Saved Q${currentIdx}:`, {
                alert_new_position: answerData.alert_new_position,
                fat7_points: answerData.fat7_points,
                alert_rows_count: answerData.alert_rows.length,
                alert_rows: answerData.alert_rows
            });
        }

        // Make saveCurrentAnswer globally accessible
        window.saveCurrentAnswer = saveCurrentAnswer;


        // Temporary save for current question
        async function saveEvaluation() {
            saveCurrentAnswer();
            const data = allAnswers[currentIndex];
            try {
                const response = await fetch('/judgings/quran/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                alert(result.message);
                return true;
            } catch (error) {
                console.error('Error saving evaluation:', error);
                alert('حدث خطأ أثناء حفظ التقييم.');
                return false;
            }
        }

        // Final save for all questions
        async function finalizeEvaluations() {
            if (!IS_HEAD) {
                // حساب عدد الأسئلة الفعلية المكشوفة (الموجودة في questionsData)
                let actualRevealedCount = 0;
                for (let i = 0; i < totalQuestions; i++) {
                    const qId = parseInt(questionsData[i].dataset.questionId);
                    if (revealedQuestionIds.includes(qId)) {
                        actualRevealedCount++;
                    }
                }
                
                // التحقق من أن جميع الأسئلة المكشوفة موجودة في questionsData
                if (actualRevealedCount < totalQuestions) {
                    showCustomNotification(
                        'تنبيه', 
                        `لا يمكنك حفظ التحكيم حتى يفتح رئيس اللجنة جميع الأسئلة (${actualRevealedCount} من أصل ${totalQuestions})`, 
                        'error'
                    );
                    return false;
                }
            }
            
            // ⚠️ مهم جداً: حفظ السؤال الحالي أولاً
            saveCurrentAnswer();
            
            // ⚠️ مهم جداً جداً: حفظ السؤال الأول بشكل صريح إذا لم يكن هو السؤال الحالي
            // هذا يضمن أن alert_rows محفوظة للسؤال الأول حتى لو كان المستخدم على سؤال آخر
            if (window.currentIndex !== 0) {
                console.log('[Finalize] ⚠️ Current index is not 0, saving question 0 explicitly');
                const originalIndex = window.currentIndex;
                
                // حفظ حالة السؤال الأول من questionsAlertHistory إذا كان موجوداً
                if (window.questionsAlertHistory && window.questionsAlertHistory[0]) {
                    const historyData = window.questionsAlertHistory[0];
                    const questionId = questionsData[0] ? questionsData[0].dataset.questionId : null;
                    
                    if (questionId) {
                        // التأكد من وجود allAnswers[0] أو إنشاؤه
                        if (!allAnswers[0]) {
                            allAnswers[0] = {
                                question_id: questionId,
                                participant_id: "{{ $participant_id }}",
                                alert_same_position: "0",
                                alert_new_position: historyData.totalAlerts || "0",
                                fat7_points: historyData.totalFat7 || "0",
                                note_ids: [],
                                note_texts: [],
                                tajweed_score: 0,
                                performance_score: 0,
                                total_deduction: 0
                            };
                        }
                        
                        // تحديث alert_rows من questionsAlertHistory
                        if (historyData.rows && Array.isArray(historyData.rows) && historyData.rows.length > 0) {
                            allAnswers[0].alert_rows = JSON.parse(JSON.stringify(historyData.rows));
                            console.log(`[Finalize] ✅ Updated alert_rows for Q0 from history:`, allAnswers[0].alert_rows);
                        } else {
                            // إذا لم تكن alert_rows موجودة، نبنيها من القيم الإجمالية
                            let rebuiltRows = [];
                            let fat7Remaining = parseInt(historyData.totalFat7) || 0;
                            let alertsRemaining = parseInt(historyData.totalAlerts) || 0;
                            
                            for(let j=0; j < fat7Remaining; j++) {
                                rebuiltRows.push({ alerts: 0, opened: true });
                            }
                            
                            while (alertsRemaining > 0) {
                                const alertsInThisRow = Math.min(alertsRemaining, 1);
                                rebuiltRows.push({ alerts: alertsInThisRow, opened: false });
                                alertsRemaining -= alertsInThisRow;
                            }
                            
                            allAnswers[0].alert_rows = rebuiltRows;
                            console.log(`[Finalize] ✅ Built alert_rows for Q0 from totals:`, rebuiltRows);
                        }
                        
                        // تحديث القيم الإجمالية
                        allAnswers[0].alert_new_position = historyData.totalAlerts || "0";
                        allAnswers[0].fat7_points = historyData.totalFat7 || "0";
                        
                        // حفظ في localStorage أيضاً
                        saveToLocalStorage(0, allAnswers[0]);
                        console.log(`[Finalize] ✅ Saved Q0 to localStorage with alert_rows:`, allAnswers[0].alert_rows);
                    }
                } else if (allAnswers[0]) {
                    // إذا كان allAnswers[0] موجوداً لكن questionsAlertHistory[0] غير موجود
                    // نحاول بناء alert_rows من القيم الإجمالية
                    if (!allAnswers[0].alert_rows || !Array.isArray(allAnswers[0].alert_rows) || allAnswers[0].alert_rows.length === 0) {
                        let rebuiltRows = [];
                        let fat7Remaining = parseInt(allAnswers[0].fat7_points) || 0;
                        let alertsRemaining = parseInt(allAnswers[0].alert_new_position) || 0;
                        
                        for(let j=0; j < fat7Remaining; j++) {
                            rebuiltRows.push({ alerts: 0, opened: true });
                        }
                        
                        while (alertsRemaining > 0) {
                            const alertsInThisRow = Math.min(alertsRemaining, 1);
                            rebuiltRows.push({ alerts: alertsInThisRow, opened: false });
                            alertsRemaining -= alertsInThisRow;
                        }
                        
                        allAnswers[0].alert_rows = rebuiltRows;
                        console.log(`[Finalize] ✅ Built alert_rows for Q0 from allAnswers totals:`, rebuiltRows);
                        
                        // حفظ في localStorage
                        saveToLocalStorage(0, allAnswers[0]);
                    }
                }
            }
            
            // ⚠️ مهم جداً: حفظ جميع الأسئلة (بما فيهم السؤال الأول) قبل الحفظ النهائي
            // هذا يضمن أن alert_rows محفوظة لجميع الأسئلة
            for (let i = 0; i < totalQuestions; i++) {
                // حفظ حالة السؤال إذا كان موجوداً في questionsAlertHistory
                if (window.questionsAlertHistory && window.questionsAlertHistory[i]) {
                    const historyData = window.questionsAlertHistory[i];
                    const questionId = questionsData[i] ? questionsData[i].dataset.questionId : null;
                    
                    if (questionId) {
                        // التأكد من وجود allAnswers[i] أو إنشاؤه
                        if (!allAnswers[i]) {
                            allAnswers[i] = {
                                question_id: questionId,
                                participant_id: "{{ $participant_id }}",
                                alert_same_position: "0",
                                alert_new_position: historyData.totalAlerts || "0",
                                fat7_points: historyData.totalFat7 || "0",
                                note_ids: [],
                                note_texts: [],
                                tajweed_score: 0,
                                performance_score: 0,
                                total_deduction: 0
                            };
                        }
                        
                        // تحديث alert_rows من questionsAlertHistory
                        if (historyData.rows && Array.isArray(historyData.rows) && historyData.rows.length > 0) {
                            allAnswers[i].alert_rows = JSON.parse(JSON.stringify(historyData.rows));
                            console.log(`[Finalize] ✅ Updated alert_rows for Q${i} from history:`, allAnswers[i].alert_rows);
                        } else {
                            // إذا لم تكن alert_rows موجودة، نبنيها من القيم الإجمالية
                            let rebuiltRows = [];
                            let fat7Remaining = parseInt(historyData.totalFat7) || 0;
                            let alertsRemaining = parseInt(historyData.totalAlerts) || 0;
                            
                            for(let j=0; j < fat7Remaining; j++) {
                                rebuiltRows.push({ alerts: 0, opened: true });
                            }
                            
                            while (alertsRemaining > 0) {
                                const alertsInThisRow = Math.min(alertsRemaining, 1);
                                rebuiltRows.push({ alerts: alertsInThisRow, opened: false });
                                alertsRemaining -= alertsInThisRow;
                            }
                            
                            allAnswers[i].alert_rows = rebuiltRows;
                            console.log(`[Finalize] ✅ Built alert_rows for Q${i} from totals:`, rebuiltRows);
                        }
                        
                        // تحديث القيم الإجمالية
                        allAnswers[i].alert_new_position = historyData.totalAlerts || "0";
                        allAnswers[i].fat7_points = historyData.totalFat7 || "0";
                        
                        // حفظ في localStorage أيضاً
                        saveToLocalStorage(i, allAnswers[i]);
                    }
                } else if (!allAnswers[i]) {
                    // إذا لم يكن هناك history ولا allAnswers، أنشئ إجابة فارغة
                    const questionId = questionsData[i] ? questionsData[i].dataset.questionId : null;
                    if (questionId) {
                        const currentScores = window.questionScores ? window.questionScores[i] : null;
                        allAnswers[i] = {
                            question_id: questionId,
                            participant_id: "{{ $participant_id }}",
                            alert_same_position: "0",
                            alert_new_position: "0",
                            fat7_points: "0",
                            note_ids: [],
                            note_texts: [],
                            alert_rows: [],
                            // Use 0 deductions for unevaluated questions (no penalty)
                            tajweed_score: 0,
                            performance_score: 0,
                            total_deduction: 0
                        };
                    }
                } else if (allAnswers[i] && (!allAnswers[i].alert_rows || !Array.isArray(allAnswers[i].alert_rows) || allAnswers[i].alert_rows.length === 0)) {
                    // إذا كان allAnswers[i] موجوداً لكن alert_rows غير موجودة، نبنيها من القيم الإجمالية
                    let rebuiltRows = [];
                    let fat7Remaining = parseInt(allAnswers[i].fat7_points) || 0;
                    let alertsRemaining = parseInt(allAnswers[i].alert_new_position) || 0;
                    
                    for(let j=0; j < fat7Remaining; j++) {
                        rebuiltRows.push({ alerts: 0, opened: true });
                    }
                    
                    while (alertsRemaining > 0) {
                        const alertsInThisRow = Math.min(alertsRemaining, 1);
                        rebuiltRows.push({ alerts: alertsInThisRow, opened: false });
                        alertsRemaining -= alertsInThisRow;
                    }
                    
                    allAnswers[i].alert_rows = rebuiltRows;
                    console.log(`[Finalize] ✅ Built alert_rows for Q${i} from allAnswers totals:`, rebuiltRows);
                    
                    // حفظ في localStorage
                    saveToLocalStorage(i, allAnswers[i]);
                }
            }

            // Frontend no longer forces zeroing based on relief here.
            // Rely on backend controllers to apply relief only to approved participants.

            // Calculate total score - ONLY alerts and fat7 affect total score
            // Tajweed and Performance are stored separately and don't affect total
            let totalScore = {{ $total_score }};
            let totalDeductions = 0;
            const fat7Penalty = {{ $fat7_penalty }};
            const alertNewPenalty = {{ $alert_new_position_penalty }};
            const defaultTajweed = {{ $tajweed_score }};
            const defaultPerformance = {{ $performance_score }};

            // Calculate alert and fat7 deductions from all questions
            let totalAlertSameDeduction = 0;
            let totalAlertNewDeduction = 0;
            let totalFat7Deduction = 0;
            let totalTajweedDeduction = 0;
            let totalPerformanceDeduction = 0;

            allAnswers.forEach(answer => {
                // Alert deductions (both types with separate penalties)
                const alertNewCount = parseFloat(answer.alert_new_position) || 0;
                totalAlertNewDeduction += alertNewCount * alertNewPenalty;

                // Fat7 deduction
                const fat7Count = parseFloat(answer.fat7_points) || 0;
                totalFat7Deduction += fat7Count * fat7Penalty;

                // Per-question deductions saved in tajweed_score and performance_score
                // These are stored but DON'T affect the total score
                totalTajweedDeduction += parseFloat(answer.tajweed_score) || 0;
                totalPerformanceDeduction += parseFloat(answer.performance_score) || 0;
            });

            // Total deductions for total score = ONLY alerts + fat7
            totalDeductions = totalAlertSameDeduction + totalAlertNewDeduction + totalFat7Deduction;

            // Apply deductions to total score (without tajweed/performance)
            totalScore = Math.max(0, totalScore - totalDeductions);

            console.log('[Finalize] 📊 Final scores:', {
                tajweedDeduction: totalTajweedDeduction,
                performanceDeduction: totalPerformanceDeduction,
                totalAlertSameDeduction,
                totalAlertNewDeduction,
                totalFat7Deduction,
                totalDeductions: totalDeductions,
                totalScore: totalScore,
                note: 'Tajweed/Performance deductions stored separately, NOT affecting total score'
            });

            // Get relief request status (user-specific)
            const userId = '{{ auth()->id() }}';
            const participantId = '{{ $participant_id }}';
            const competitionBranchId = '{{ $competition_version_branch_id }}';
            const reliefKey = `relief-request-sent-${userId}-${participantId}-${competitionBranchId}-${committeeId ?? 'none'}`;

            // قراءة edit_start_field من URL إذا كان موجوداً
            const urlParams = new URLSearchParams(window.location.search);
            const editStartField = urlParams.get('edit_start_field');
            const committeeIdParam = urlParams.get('committee_id');

            const data = {
                participant_id: '{{ $participant_id }}',
                competition_version_branch_id: '{{ $competition_version_branch_id }}',
                evaluations: JSON.stringify(allAnswers),
                total_score: totalScore,
                total_deductions: totalDeductions,
                request_relief: localStorage.getItem(reliefKey) === 'true' ? 1 : 0,
            };

            // إضافة edit_start_field إذا كان موجوداً في URL
            if (editStartField) {
                data.edit_start_field = editStartField;
            }

            // إضافة committee_id إذا كان موجوداً في URL
            if (committeeIdParam) {
                data.committee_id = committeeIdParam;
            }

            console.log('[Quran Finalize] Sending data via AJAX:', data);

            try {
                const response = await fetch('{{ route("quranjudgings.store") }}', {
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

                console.log('[Quran Finalize] Response status:', response.status);
                console.log('[Quran Finalize] Response result:', result);

                // معالجة الاستجابة - التحقق من success أولاً
                if (result.success === true) {
                    // Clear localStorage after successful save
                    clearLocalStorage();
                    console.log('[Quran Finalize] Cleared localStorage after successful final save');
                    
                    // عرض رسالة النجاح
                    if (typeof showCustomNotification === 'function') {
                        showCustomNotification('نجاح', result.message || 'تم حفظ التحكيم بنجاح', 'success', 3000);
                    }
                    
                    // إذا كان هناك redirect URL، انتقل إليه بعد تأخير قصير
                    if (result.redirect) {
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 1500);
                        return true;
                    }
                    
                    // إذا لم يكن هناك redirect، أعد تحميل الصفحة بعد تأخير قصير
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                    
                    return true;
                } else {
                    // إذا كان success: false لكن type: info، نعرض alert بدلاً من error
                    const messageType = result.type || 'error';
                    const notificationType = messageType === 'info' ? 'info' : 'error';
                    const title = messageType === 'info' ? 'تنبيه' : 'خطأ';
                    
                    console.log('[Quran Finalize] Save not completed:', result);
                    
                    // إغلاق الـ modal وإعادة تعيين حالة الزر
                    const confirmSaveBtn = document.getElementById('confirm-save-btn');
                    if (confirmSaveBtn) {
                        confirmSaveBtn.disabled = false;
                        confirmSaveBtn.innerHTML = '<i class="fas fa-save ml-2"></i> تأكيد الحفظ';
                    }
                    
                    // إغلاق الـ modal
                    if (typeof window.hideSaveConfirmationModal === 'function') {
                        window.hideSaveConfirmationModal();
                    } else {
                        const modal = document.getElementById('save-confirmation-modal');
                        if (modal) {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        }
                    }
                    
                    // عرض رسالة التنبيه أو الخطأ بدون إعادة تحميل الصفحة
                    // تأخير بسيط لضمان إغلاق الـ modal أولاً
                    setTimeout(() => {
                        if (typeof showCustomNotification === 'function') {
                            // استخدام showCustomNotification مع نوع الرسالة المناسب
                            showCustomNotification(title, result.message || result.error || 'حدث خطأ أثناء حفظ التقييم', notificationType, 8000);
                        } else {
                            // استخدام alert مع دعم الأسطر الجديدة
                            alert(result.message || result.error || 'حدث خطأ أثناء حفظ التقييم');
                        }
                    }, 300);
                    
                    // لا نرمي error إذا كانت الرسالة من نوع info
                    if (messageType !== 'info') {
                        throw new Error(result.message || result.error || 'حدث خطأ أثناء حفظ التقييم');
                    }
                    
                    return false;
                }
            } catch (error) {
                console.error('[Quran Finalize] Error saving evaluations:', error);
                
                // إعادة تعيين حالة الزر وإغلاق الـ modal
                const confirmSaveBtn = document.getElementById('confirm-save-btn');
                if (confirmSaveBtn) {
                    confirmSaveBtn.disabled = false;
                    confirmSaveBtn.innerHTML = '<i class="fas fa-save ml-2"></i> تأكيد الحفظ';
                }
                
                // إغلاق الـ modal
                if (typeof window.hideSaveConfirmationModal === 'function') {
                    window.hideSaveConfirmationModal();
                } else {
                    const modal = document.getElementById('save-confirmation-modal');
                    if (modal) {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }
                }
                
                // التحقق من أن الخطأ ليس بسبب عدم اكتمال حفظ الأعضاء
                if (error.message && (error.message.includes('الانتظار') || error.message.includes('متبقي'))) {
                    // هذا تنبيه وليس خطأ
                    setTimeout(() => {
                        if (typeof showCustomNotification === 'function') {
                            showCustomNotification('تنبيه', error.message, 'info', 8000);
                        } else {
                            alert(error.message);
                        }
                    }, 300);
                    return false;
                }
                
                // عرض رسالة الخطأ الحقيقية بدون إعادة تحميل الصفحة
                setTimeout(() => {
                    if (typeof showCustomNotification === 'function') {
                        showCustomNotification('خطأ', error.message || 'حدث خطأ أثناء حفظ التقييم', 'error', 5000);
                    } else {
                        alert(error.message || 'حدث خطأ أثناء حفظ التقييم');
                    }
                }, 300);
                
                throw error;
            }
        }

        function clearLocalStorage() {
            try {
                // Clear all question data
                for (let i = 0; i < totalQuestions; i++) {
                    const key = getLocalStorageKey(i);
                    localStorage.removeItem(key);
                }

                // Clear question scores
                const scoresKey = `judging-scores-${storageScope}`;
                localStorage.removeItem(scoresKey);

                // Clear global scores
                const globalScoresKey = `judging-global-scores-${storageScope}`;
                localStorage.removeItem(globalScoresKey);

                // Clear per-question deductions
                const perQuestionDeductionsKey = `judging-per-question-deductions-${storageScope}`;
                localStorage.removeItem(perQuestionDeductionsKey);

                console.log('[Storage] Cleared all judging data from localStorage');
            } catch (e) {
                console.warn('Failed to clear localStorage:', e);
            }
        }



        // Next and previous navigation
        document.querySelector('.next-btn').addEventListener('click', () => {
            console.log('Next button clicked, currentIndex:', currentIndex, 'totalQuestions:', totalQuestions);
            // Force save current answer before navigation
            saveCurrentAnswer();

            // If member: check if there are more revealed questions
            if (!IS_HEAD) {
                let nextIndex = currentIndex + 1;
                let foundNext = false;

                while (nextIndex < totalQuestions) {
                    const qId = parseInt(questionsData[nextIndex].dataset.questionId);
                    if (revealedQuestionIds.includes(qId)) {
                        foundNext = true;
                        break;
                    }
                    nextIndex++;
                }

                if (foundNext) {
                    // There's another revealed question, navigate to it
                    switchToQuestion(nextIndex);
                    return;
                } else {
                    // No more revealed questions, show save confirmation
                    console.log('No more revealed questions, showing save confirmation modal');
                    saveCurrentAnswer();
                    if (typeof window.showSaveConfirmationModal === 'function') {
                        window.showSaveConfirmationModal();
                    } else {
                        console.error('showSaveConfirmationModal function not available, calling directly');
                        showSaveConfirmationModal();
                    }
                    return;
                }
            }

            // For head: normal navigation
            if (currentIndex < totalQuestions - 1) {
                switchToQuestion(currentIndex + 1);
            } else {
                // Last question, show save confirmation
                saveCurrentAnswer();
                console.log('Showing save confirmation modal');
                if (typeof window.showSaveConfirmationModal === 'function') {
                    window.showSaveConfirmationModal();
                } else {
                    console.error('showSaveConfirmationModal function not available, calling directly');
                    showSaveConfirmationModal();
                }
            }
        });

        document.querySelectorAll('.prev-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                // Force save current answer before navigation
                saveCurrentAnswer();
                if (currentIndex > 0) {
                    if (!IS_HEAD) {
                        let prevIndex = currentIndex - 1;
                        while (prevIndex >= 0) {
                            const qId = parseInt(questionsData[prevIndex].dataset.questionId);
                            if (revealedQuestionIds.includes(qId)) break;
                            prevIndex--;
                        }
                        if (prevIndex < 0) return;
                        switchToQuestion(prevIndex);
                        return;
                    }
                    switchToQuestion(currentIndex - 1);
                }
            });
        });

        // Global penalty buttons - Updated to affect total score
        window.changeValue = (inputId, delta, scoreId, penalty, maxValue) => {
            const input = document.getElementById(inputId);
            const score = document.getElementById(scoreId);

            // Parse current value and total from "current/total" format
            let currentValue = {{ $tajweed_per_question }}; // Use actual per question score
            let totalValue = {{ $tajweed_per_question }};   // Start with per question score

            if (input.value.includes('/')) {
                const parts = input.value.split('/');
                currentValue = parseInt(parts[0]) || {{ $tajweed_per_question }};
                totalValue = parseInt(parts[1]) || {{ $tajweed_per_question }};
            }

            if (delta > 0) {
                // Increase: add penalty to total value only
                totalValue += penalty;
                // Check if total value exceeds maximum
                if (totalValue > maxValue) {
                    totalValue = maxValue;
                    // Show notification when reaching maximum
                    showCustomNotification(
                        'تم الوصول للحد الأقصى',
                        `لا يمكن تجاوز ${maxValue} نقطة`,
                        'warning',
                        2000
                    );
                }
            } else {
                // Decrease: subtract penalty from total value only
                totalValue -= penalty;
                if (totalValue < 0) totalValue = 0;
            }

            // Update input with new format
            input.value = `${currentValue}/${totalValue}`;

            // Calculate and update score based on total value
            let newScore = totalValue; // Total value is the score
            if (newScore < 0) newScore = 0;
            score.textContent = newScore.toFixed(0); // Show as integer

            // Add visual indicator when approaching maximum
            if (totalValue >= maxValue * 0.9) {
                score.style.color = '#ef4444'; // Red when close to max
            } else if (totalValue >= maxValue * 0.7) {
                score.style.color = '#f59e0b'; // Orange when 70% of max
            } else {
                score.style.color = ''; // Default color
            }
        }

        // Handle notes for current form + أي فتح يدوي عام إن وُجد
        currentAnswerForm.addEventListener('click', event => {
            const openBtn = event.target.closest('.open-btn');

            // فتح يدوي (احتياطي، في النظام الجديد الفتح يتم آلياً مع التنبيهات الخاصة بكل صف)
            if (openBtn) {
                let currentFat7 = parseInt(currentAnswerForm.querySelector('[name="fat7_points"]').value) || 0;
                currentAnswerForm.querySelector('[name="fat7_points"]').value = currentFat7 + 1;

                // Auto-save
                saveCurrentAnswer();

                // Update total score
                console.log('🔓 Open button clicked, updating total score...');
                if (typeof window.updateTotalScoreDisplay === 'function') {
                    window.updateTotalScoreDisplay();
                }
            }
        });

        initializeNoteInput();

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
                <div class="mr-3 w-0 flex-1">
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

            // Add to body
            document.body.appendChild(notification);

            // Show with animation
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);

            // Auto hide after duration
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, duration);
        }


        // Reveal button for head: inject in question header (initial load)
        if (IS_HEAD) {
            try {
                const revealBtnContainer = document.getElementById('reveal-btn-in-question');
                if (revealBtnContainer) {
                    revealBtnContainer.innerHTML = `
                        <button id="reveal-question-btn" type="button" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 border border-blue-200 rounded-lg text-xs font-medium transition-all flex items-center gap-1.5">
                            <i class="fas fa-eye text-xs"></i>
                            <span>إظهار للجميع</span>
                        </button>`;
                    updateRevealButtonState();
                }
            } catch (e) {
                console.warn('Reveal button injection failed (initial)', e);
            }
        }

        // Function to update reveal button state
        function updateRevealButtonState() {
            if (!IS_HEAD) return;
            const qId = parseInt(questionsData[currentIndex].dataset.questionId);
            const btn = document.getElementById('reveal-question-btn');
            if (!btn) return;

            const currentRevealedIds = window.revealedQuestionIds || revealedQuestionIds || [];
            const isRevealed = currentRevealedIds.includes(qId);
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

        // Make updateRevealButtonState available globally
        window.updateRevealButtonState = updateRevealButtonState;

        // Auto-reveal question for head when switching to it (silent, no notification)
        // Use window variables to ensure it works from anywhere
        async function autoRevealQuestionForHead(qId) {
            // Get variables from window if not available locally
            const branchId = window.BRANCH_ID || BRANCH_ID;
            const participationId = window.PARTICIPATION_ID || PARTICIPATION_ID;
            const committeeId = window.COMMITTEE_ID || COMMITTEE_ID;
            const currentRevealedIds = window.revealedQuestionIds || revealedQuestionIds || [];
            
            // Check if already revealing (use window to track across scopes)
            if (window.isRevealing === true) {
                console.log('[Quran Auto-Reveal] Already revealing a question, skipping');
                return;
            }

            // Check if already revealed
            if (currentRevealedIds.includes(qId)) {
                console.log('[Quran Auto-Reveal] Question already revealed, skipping', { qId });
                return;
            }

            console.log('[Quran Auto-Reveal] Auto-revealing question for head', {
                qId,
                branchId,
                participationId,
                committeeId,
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

            // Save to database silently (no notification)
            try {
                const res = await fetch('{{ url('/api/judgings/quran/reveals') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        competition_version_branch_id: branchId,
                        competition_participation_id: participationId,
                        quran_question_id: qId,
                        committee_id: committeeId
                    })
                });
                const data = await res.json();

                if (data && data.success) {
                    console.log('[Quran Auto-Reveal] ✅ Question auto-revealed successfully', { qId });
                    // Update UI list without notification
                    if (typeof updateRevealedListUI === 'function') {
                        updateRevealedListUI(window.revealedQuestionIds);
                    }
                    // Update button state
                    if (typeof window.updateRevealButtonState === 'function') {
                        setTimeout(() => window.updateRevealButtonState(), 100);
                    }
                    // Update footer
                    if (typeof updateFooter === 'function') {
                        updateFooter();
                    }
                } else {
                    console.error('[Quran Auto-Reveal] ❌ Failed to reveal', { qId, data });
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
                console.error('[Quran Auto-Reveal] ❌ API error', err);
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
                console.log('[Reveal] Already revealing a question, please wait');
                return;
            }

            const qId = parseInt(questionsData[currentIndex].dataset.questionId);
            console.log('[Reveal] 📤 Starting reveal process', {
                qId,
                BRANCH_ID,
                PARTICIPATION_ID,
                currentIndex,
                timestamp: new Date().toISOString()
            });

            // تفعيل القفل
            isRevealing = true;

            const btn = document.getElementById('reveal-question-btn');

            // ✨ Optimistic UI Update - تحديث فوري قبل انتظار السيرفر
            if (btn) {
                btn.disabled = true;
                btn.className = 'px-3 py-1.5 bg-green-50 text-green-600 border border-green-200 rounded-lg text-xs font-medium cursor-not-allowed flex items-center gap-1.5';
                btn.innerHTML = '<i class="fas fa-check-circle text-xs"></i><span>تم الإظهار</span>';
                btn.style.pointerEvents = 'none';
            }

            // إضافة السؤال للقائمة فوراً
            if (!revealedQuestionIds.includes(qId)) {
                revealedQuestionIds.push(qId);
                window.revealedQuestionIds = revealedQuestionIds;
            }

            // إظهار إشعار النجاح فوراً
            showCustomNotification('تم الإظهار', 'تم إظهار السؤال لباقي الأعضاء بنجاح', 'success', 2000);

            // إرسال الطلب للسيرفر في الخلفية
            try {
            console.log('[Reveal] 📡 Sending API request...', {
                    url: '{{ url('/api/judgings/quran/reveals') }}',
                    payload: {
                        competition_version_branch_id: BRANCH_ID,
                        competition_participation_id: PARTICIPATION_ID,
                        quran_question_id: qId,
                    committee_id: COMMITTEE_ID
                    }
                });

                const res = await fetch('{{ url('/api/judgings/quran/reveals') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                body: JSON.stringify({
                    competition_version_branch_id: BRANCH_ID,
                    competition_participation_id: PARTICIPATION_ID,
                    quran_question_id: qId,
                    committee_id: COMMITTEE_ID
                })
                });
                const data = await res.json();
                console.log('[Reveal] ✅ API response received', {
                    status: res.status,
                    data,
                    timestamp: new Date().toISOString()
                });

                // في حالة الفشل فقط، نعيد الزر لحالته الأصلية
                if (!data || !data.success) {
                    console.error('[Reveal] Server returned failure');
                    // إزالة السؤال من القائمة
                    const index = revealedQuestionIds.indexOf(qId);
                    if (index > -1) {
                        revealedQuestionIds.splice(index, 1);
                        window.revealedQuestionIds = revealedQuestionIds;
                    }
                    // إعادة الزر لحالته الأصلية
                    if (btn) {
                        btn.disabled = false;
                        btn.className = 'bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition-all duration-200';
                        btn.innerHTML = '<i class="fas fa-eye ml-2"></i> إظهار السؤال لباقي أعضاء اللجنة';
                        btn.style.pointerEvents = 'auto';
                    }
                    showCustomNotification('فشل الإظهار', (data && data.message) || 'حدث خطأ، حاول مرة أخرى', 'error', 3000);
                }
            } catch (err) {
                console.error('[Reveal] API error', err);
                // في حالة خطأ الشبكة، نعيد الزر لحالته الأصلية
                const index = revealedQuestionIds.indexOf(qId);
                if (index > -1) {
                    revealedQuestionIds.splice(index, 1);
                    window.revealedQuestionIds = revealedQuestionIds;
                }
                if (btn) {
                    btn.disabled = false;
                    btn.className = 'bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition-all duration-200';
                    btn.innerHTML = '<i class="fas fa-eye ml-2"></i> إظهار السؤال لباقي أعضاء اللجنة';
                    btn.style.pointerEvents = 'auto';
                }
                showCustomNotification('خطأ', 'تعذر الاتصال بالخادم، حاول مرة أخرى', 'error', 3000);
            } finally {
                // إلغاء القفل بعد انتهاء العملية (نجاح أو فشل)
                // تأخير بسيط للتأكد من اكتمال الطلب
                setTimeout(() => {
                    isRevealing = false;
                    console.log('[Reveal] Lock released');
                }, 500);
            }
        }

        // Lock to prevent multiple simultaneous reveals
        let isRevealing = false;

        if (IS_HEAD) {
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('#reveal-question-btn');
                if (btn && !btn.disabled && !isRevealing) {
                    revealCurrentQuestion();
                }
            });
        }

        // Polling for members to fetch revealed questions - سرعة أعلى
        let revealPollInterval;
        function startRevealPolling() {
            if (IS_HEAD) {
                console.log('[Polling] 👑 Head user - polling disabled');
                return; // head doesn't need polling
            }
            console.log('[Polling] 👥 Member user - starting polling');
            console.log('[Polling] ℹ️ Initial state:', {
                IS_HEAD,
                revealedQuestionIds,
                BRANCH_ID,
                PARTICIPATION_ID
            });

            const tick = async () => {
                try {
                    const url = `{{ url('/api/judgings/quran/reveals/status') }}?competition_version_branch_id=${BRANCH_ID}&competition_participation_id=${PARTICIPATION_ID}&committee_id=${COMMITTEE_ID || ''}`;
                    const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();

                    console.log('[Polling] 🔄 Status check response', {
                        revealed_count: data.revealed ? data.revealed.length : 0,
                        revealed_ids: data.revealed,
                        timestamp: new Date().toISOString()
                    });

                    if (Array.isArray(data.revealed)) {
                        const prev = revealedQuestionIds.slice();
                        revealedQuestionIds = data.revealed.map(Number);
                        window.revealedQuestionIds = revealedQuestionIds;
                        
                        // اكتشاف الأسئلة الجديدة المكشوفة
                        const newRevealedQuestions = revealedQuestionIds.filter(id => !prev.includes(id));
                        
                        // التحقق من تغيير عدد الأسئلة (مثل قبول طلب تخفيف)
                        const countChanged = prev.length !== revealedQuestionIds.length;
                        
                        if (newRevealedQuestions.length > 0) {
                            console.log('[Polling] 🔔 New questions revealed!', {
                                new_questions: newRevealedQuestions,
                                previous_count: prev.length,
                                new_count: revealedQuestionIds.length
                            });
                            
                            // تحديث القائمة الجانبية
                            updateRevealedListUI(revealedQuestionIds);
                            
                            // تحديث حالة الأزرار والنصوص في التذييل فوراً
                            if (typeof updateFooter === 'function') {
                                updateFooter(); 
                            }
                            
                            // التحقق من أن السؤال الحالي أصبح مكشوفاً (حتى لو كان ضمن الأسئلة الجديدة)
                            if (questionsData[currentIndex]) {
                                const currentQId = parseInt(questionsData[currentIndex].dataset.questionId);
                                const wasCurrentRevealed = prev.includes(currentQId);
                                const isCurrentNowRevealed = revealedQuestionIds.includes(currentQId);
                                
                                if (!wasCurrentRevealed && isCurrentNowRevealed) {
                                    // السؤال الحالي أصبح مكشوفاً الآن - قم بتحميله
                                    console.log('[Polling] ✨ Current question unlocked (was in new revealed questions)!', {
                                        qId: currentQId,
                                        currentIndex,
                                        questionNumber: currentIndex + 1
                                    });
                                    
                                    // Reload question to show content
                                    loadQuestion(currentIndex);
                                    
                                    // استرجاع حالة السؤال
                                    if (typeof window.restoreQuestionState === 'function') {
                                        window.restoreQuestionState(currentIndex);
                                    }
                                    
                                    // Hide lock message if exists
                                    const lockMsg = document.getElementById('member-locked-warning');
                                    if (lockMsg) {
                                        lockMsg.classList.add('hidden');
                                        console.log('[Polling] 🔓 Hidden lock message');
                                    }
                                    
                                    // Show form content
                                    const formContent = document.getElementById('current-form-content');
                                    if (formContent) {
                                        formContent.classList.remove('hidden');
                                        console.log('[Polling] 📝 Showed form content');
                                    }
                                }
                            }
                            
                            // إظهار إشعار للأسئلة الجديدة (بدون التنقل التلقائي)
                            if (typeof showCustomNotification === 'function') {
                                showCustomNotification('سؤال جديد', `تم إظهار ${newRevealedQuestions.length} سؤال من قبل رئيس اللجنة. يمكنك التنقل بين الأسئلة بحرية.`, 'success', 3000);
                            }
                            
                            // ملاحظة: تم إزالة التنقل التلقائي للسؤال الجديد
                            // الأعضاء لديهم الحرية الكاملة في التنقل بين الأسئلة وقت ما يشاؤون
                            // إذا كان السؤال الحالي أصبح مكشوفاً، سيتم فتحه تلقائياً في الكود أعلاه
                        } else if (countChanged) {
                            // تحديث القائمة حتى لو لم تكن هناك أسئلة جديدة (قد تكون هناك تغييرات أخرى مثل قبول طلب تخفيف)
                            console.log('[Polling] 🔄 Question count changed (possibly relief approved)', {
                                previous_count: prev.length,
                                new_count: revealedQuestionIds.length,
                                total_questions: totalQuestions
                            });
                            
                            updateRevealedListUI(revealedQuestionIds);
                            
                            // تحديث حالة الأزرار والنصوص في التذييل فوراً
                            if (typeof updateFooter === 'function') {
                                updateFooter(); 
                            }
                        }

                        // التحقق من السؤال الحالي: إذا أصبح مكشوفاً الآن، قم بتحميله
                        if (!questionsData[currentIndex]) {
                            console.warn('[Polling] ⚠️ No question data at currentIndex:', currentIndex);
                            return;
                        }

                        const qId = parseInt(questionsData[currentIndex].dataset.questionId);
                        const wasRevealed = prev.includes(qId);
                        const isNowRevealed = revealedQuestionIds.includes(qId);

                        if (!wasRevealed && isNowRevealed && newRevealedQuestions.length === 0) {
                            // السؤال الحالي أصبح مكشوفاً (لكن لم يكن ضمن الأسئلة الجديدة)
                            console.log('[Polling] ✨ Current question unlocked by head!', {
                                qId,
                                currentIndex,
                                questionNumber: currentIndex + 1
                            });

                            // Reload question to show content
                            loadQuestion(currentIndex);
                            
                            // استرجاع حالة السؤال
                            if (typeof window.restoreQuestionState === 'function') {
                                window.restoreQuestionState(currentIndex);
                            }

                            // Hide lock message if exists
                            const lockMsg = document.getElementById('member-locked-warning');
                            if (lockMsg) {
                                lockMsg.classList.add('hidden');
                                console.log('[Polling] 🔓 Hidden lock message');
                            }

                            // Show form content
                            const formContent = document.getElementById('current-form-content');
                            if (formContent) {
                                formContent.classList.remove('hidden');
                                console.log('[Polling] 📝 Showed form content');
                            }

                            // Show notification
                            if (typeof showCustomNotification === 'function') {
                                showCustomNotification('سؤال جديد', `تم إظهار السؤال ${currentIndex + 1} من قبل رئيس اللجنة`, 'success', 4000);
                            }
                        } else if (prev.length === 0 && revealedQuestionIds.length > 0) {
                            // كان الصندوق فارغ والآن أصبحت هناك أسئلة مكشوفة
                            // التحقق من أن السؤال الحالي غير مكشوف قبل محاولة الفتح التلقائي
                            const currentQId = questionsData[currentIndex] ? parseInt(questionsData[currentIndex].dataset.questionId) : null;
                            const isCurrentRevealed = currentQId && revealedQuestionIds.includes(currentQId);
                            
                            if (!isCurrentRevealed) {
                                // فقط إذا كان السؤال الحالي غير مكشوف، افتح أول سؤال مكشوف
                                console.log('[Polling] 🔓 First questions revealed, auto-opening first one (current question is locked)');
                                tryAutoOpenFirstRevealed();
                            } else {
                                // السؤال الحالي مكشوف بالفعل، لا حاجة للتنقل
                                console.log('[Polling] ✅ Current question is already revealed, no need to switch');
                            }
                        }
                    }
                } catch (e) {
                    console.warn('[Reveal] Polling error', e);
                }
            };

            console.log('[Polling] 🚀 Starting polling for member (every 200ms)');
            tick();
            // تقليل وقت الـ polling إلى 200ms لاستجابة فورية
            revealPollInterval = setInterval(tick, 200);
        }

        startRevealPolling();

        // عند التحميل: إن كانت القائمة على اليمين فارغة للعضو لكن هناك أسئلة مكشوفة، افتح أول سؤال مكشوف تلقائيًا
        // زيادة الوقت إلى 600ms لضمان جاهزية DOM وتهيئة جميع المتغيرات
        setTimeout(() => {
            if (!IS_HEAD) {
                console.log('[Init] 🚀 Attempting initial auto-open for member');
                tryAutoOpenFirstRevealed();
            }
        }, 600);

        // Initialize first question with saved data from localStorage
        setTimeout(() => {
            console.log('[Init] Loading saved data for first question');
            console.log('[Init] allAnswers[0]:', allAnswers[0]);
            console.log('[Init] questionsAlertHistory[0]:', window.questionsAlertHistory ? window.questionsAlertHistory[0] : 'not found');

            // Load saved data for first question
            const savedData = allAnswers[0];

            if (savedData) {
                console.log('[Init] Found saved data for first question:', savedData);
                
                // ⚠️ مهم جداً: التأكد من وجود questionsAlertHistory[0] قبل الاستعادة
                // إذا لم يكن موجوداً، بناؤه من savedData
                if (!window.questionsAlertHistory || !window.questionsAlertHistory[0]) {
                    console.log('[Init] ⚠️ questionsAlertHistory[0] not found, building from savedData');
                    const totalAlerts = parseInt(savedData.alert_new_position) || 0;
                    const totalFat7 = parseInt(savedData.fat7_points) || 0;
                    let rows = [];
                    
                    // استخدام alert_rows من savedData إذا كانت موجودة وصحيحة
                    if (savedData.alert_rows && Array.isArray(savedData.alert_rows) && savedData.alert_rows.length > 0) {
                        let calculatedAlerts = 0;
                        let calculatedFat7 = 0;
                        savedData.alert_rows.forEach(row => {
                            if (row.opened) {
                                calculatedFat7 += 1;
                            } else {
                                calculatedAlerts += row.alerts || 0;
                            }
                        });
                        
                        if (calculatedAlerts === totalAlerts && calculatedFat7 === totalFat7) {
                            rows = JSON.parse(JSON.stringify(savedData.alert_rows));
                            console.log('[Init] ✅ Using alert_rows from savedData for Q0:', rows);
                        } else {
                            console.log('[Init] ⚠️ alert_rows don\'t match totals, rebuilding');
                        }
                    }
                    
                    // إذا لم نستخدم alert_rows المحفوظة، نبنيها من القيم الإجمالية
                    if (rows.length === 0 && (totalAlerts > 0 || totalFat7 > 0)) {
                        let fat7Remaining = totalFat7;
                        let alertsRemaining = totalAlerts;
                        
                        for(let i=0; i < fat7Remaining; i++) {
                            rows.push({ alerts: 0, opened: true });
                        }
                        
                        while (alertsRemaining > 0) {
                            const alertsInThisRow = Math.min(alertsRemaining, 1);
                            rows.push({ alerts: alertsInThisRow, opened: false });
                            alertsRemaining -= alertsInThisRow;
                        }
                        
                        console.log('[Init] ✅ Built alert_rows from totals for Q0:', rows);
                    }
                    
                    // إنشاء questionsAlertHistory[0]
                    if (!window.questionsAlertHistory) {
                        window.questionsAlertHistory = {};
                    }
                    window.questionsAlertHistory[0] = {
                        rows: rows,
                        totalAlerts: String(totalAlerts),
                        totalFat7: String(totalFat7)
                    };
                    
                    console.log('[Init] ✅ Created questionsAlertHistory[0]:', window.questionsAlertHistory[0]);
                }

                // Always update form inputs (even if values are "0")
                currentAnswerForm.querySelector('[name="alert_same_position"]').value = savedData.alert_same_position || "0";
                currentAnswerForm.querySelector('[name="alert_new_position"]').value = savedData.alert_new_position || "0";
                currentAnswerForm.querySelector('[name="fat7_points"]').value = savedData.fat7_points || "0";
                // Load multiple notes
                const initNoteIdsField = currentAnswerForm.querySelector('#note-ids');
                const initNoteTextsField = currentAnswerForm.querySelector('#note-texts');

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
                                    console.log('[Footer] ✅ Notes applied to Select2');
                                } else if (retryCount < maxRetries) {
                                    // Select2 not ready yet, retry
                                    setTimeout(() => applyNotesWithRetry(retryCount + 1), 200);
                                    return;
                                }
                            }

                            // Update display
                            if (typeof window.updateSelectedNotesDisplay === 'function') {
                                window.updateSelectedNotesDisplay(savedNoteIds, savedNoteTexts);
                                console.log('[Footer] ✅ Notes display updated');
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
                        console.error('Error loading notes:', e);
                    }
                }

                console.log('[Init] Restored values:', {
                    alert_same_position: savedData.alert_same_position,
                    alert_new_position: savedData.alert_new_position,
                    fat7_points: savedData.fat7_points,
                    note_id: savedData.note_id
                });

                // Update visual indicator
                updateWarningsDisplay();

                // Update total score display
                if (typeof window.updateTotalScoreDisplay === 'function') {
                    window.updateTotalScoreDisplay();
                }

                console.log('[Init] ✅ Restored saved data for first question');
            } else {
                console.log('[Init] ❌ No saved data found for first question');
            }
        }, 600);

        // Save temp button (if exists)
        const saveTempBtn = document.getElementById('save-temp');
        if (saveTempBtn) {
            saveTempBtn.addEventListener('click', async () => {
                await saveEvaluation();
            });
        }

        // Final save button (if exists)
        const saveFinalBtn = document.getElementById('save-final');
        if (saveFinalBtn) {
            saveFinalBtn.addEventListener('click', async () => {
                await finalizeEvaluations();
            });
        }

        // Theme toggle (if exists)
        const themeToggleBtn = document.getElementById('theme-toggle');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                document.documentElement.classList.toggle('light');
                document.documentElement.classList.toggle('dark');
            });
        }

        // Modal functionality is handled in the separate script below

        // Initial setup
        if (IS_HEAD) {
            console.log('[Init] 👑 Head user - initializing first question');
            currentIndex = 0;
            window.currentIndex = 0;
            
            // استخدام switchToQuestion بدلاً من loadQuestion لضمان إظهار السؤال الأول تلقائياً
            // تأخير بسيط لضمان أن جميع الدوال جاهزة
            setTimeout(() => {
                if (typeof window.switchToQuestion === 'function') {
                    console.log('[Init] ✅ Using switchToQuestion for head (will auto-reveal first question)');
                    window.switchToQuestion(0);
                } else if (typeof switchToQuestion === 'function') {
                    console.log('[Init] ✅ Using switchToQuestion directly for head');
                    switchToQuestion(0);
                } else {
                    // Fallback: استخدام loadQuestion مباشرة
                    console.warn('[Init] ⚠️ switchToQuestion not available, using loadQuestion fallback');
                    loadQuestion(0);
                    updateQuestionHighlight();
                    updateFooter();
                    
                    // محاولة إظهار السؤال الأول تلقائياً حتى في حالة Fallback
                    const firstQId = questionsData[0] ? parseInt(questionsData[0].dataset.questionId) : null;
                    if (firstQId) {
                        setTimeout(() => {
                            if (typeof window.autoRevealQuestionForHead === 'function') {
                                console.log('[Init] 🔓 Attempting auto-reveal for first question (fallback)');
                                window.autoRevealQuestionForHead(firstQId);
                            } else if (typeof autoRevealQuestionForHead === 'function') {
                                console.log('[Init] 🔓 Attempting auto-reveal for first question (fallback, direct)');
                                autoRevealQuestionForHead(firstQId);
                            } else {
                                console.warn('[Init] ⚠️ autoRevealQuestionForHead not available yet, retrying...');
                                setTimeout(() => {
                                    if (typeof window.autoRevealQuestionForHead === 'function') {
                                        console.log('[Init] 🔓 Attempting auto-reveal for first question (fallback, retry)');
                                        window.autoRevealQuestionForHead(firstQId);
                                    }
                                }, 500);
                            }
                        }, 300);
                    }
                }
            }, 100);

            if (typeof window.updateScoreDisplay === 'function') {
                setTimeout(() => {
                    window.updateScoreDisplay(0);
                }, 200);
            }

            if (typeof updateRevealButtonState === 'function') {
                setTimeout(() => {
                    updateRevealButtonState();
                }, 600);
            }
            
            // تأكيد إظهار السؤال الأول تلقائياً بعد التهيئة (fallback mechanism)
            setTimeout(() => {
                const firstQId = questionsData[0] ? parseInt(questionsData[0].dataset.questionId) : null;
                if (firstQId && IS_HEAD) {
                    const currentRevealedIds = window.revealedQuestionIds || revealedQuestionIds || [];
                    if (!currentRevealedIds.includes(firstQId)) {
                        console.log('[Init] 🔓 Ensuring first question is auto-revealed (fallback check)');
                        if (typeof window.autoRevealQuestionForHead === 'function') {
                            window.autoRevealQuestionForHead(firstQId);
                        }
                    }
                }
            }, 800);
        } else {
            // للعضو: البحث عن أول سؤال مكشوف
            console.log('[Init] 👥 Member user - initializing first revealed question', {
                revealedQuestionIds,
                revealed_count: revealedQuestionIds ? revealedQuestionIds.length : 0
            });
            
            let initialIndex = -1;

            // أولاً: التحقق من أن السؤال الأول (index 0) مكشوف
            const firstQuestionId = questionsData[0] ? parseInt(questionsData[0].dataset.questionId) : null;
            const isFirstRevealed = firstQuestionId && Array.isArray(revealedQuestionIds) && revealedQuestionIds.includes(firstQuestionId);

            if (isFirstRevealed) {
                // إذا كان السؤال الأول مكشوفاً، استخدمه مباشرة
                initialIndex = 0;
                console.log('[Init] ✅ First question (index 0) is revealed, using it');
            } else if (Array.isArray(revealedQuestionIds) && revealedQuestionIds.length) {
                // إذا لم يكن السؤال الأول مكشوفاً، ابحث عن أول سؤال مكشوف
                initialIndex = getIndexByQuestionId(revealedQuestionIds[0]);
                console.log('[Init] 🔍 Found first revealed question at index:', initialIndex, 'questionId:', revealedQuestionIds[0]);
            }

            if (initialIndex >= 0 && initialIndex < questionsData.length) {
                console.log('[Init] 📖 Loading revealed question at index:', initialIndex);
                
                // استخدام switchToQuestion لضمان تنفيذ منطق الإظهار بشكل صحيح
                if (typeof window.switchToQuestion === 'function') {
                    window.switchToQuestion(initialIndex);
                } else if (typeof switchToQuestion === 'function') {
                    switchToQuestion(initialIndex);
                } else {
                    // Fallback: استخدام loadQuestion مباشرة
                    console.warn('[Init] ⚠️ switchToQuestion not available, using loadQuestion fallback');
                    currentIndex = initialIndex;
                    window.currentIndex = initialIndex;
                    loadQuestion(initialIndex);
                    
                    // استرجاع حالة السؤال
                    if (typeof window.restoreQuestionState === 'function') {
                        window.restoreQuestionState(initialIndex);
                    }
                }

                if (typeof window.updateScoreDisplay === 'function') {
                    setTimeout(() => {
                        window.updateScoreDisplay(initialIndex);
                    }, 100);
                }
                
                // إخفاء رسالة القفل وإظهار المحتوى
                const lockMsg = document.getElementById('member-locked-warning');
                if (lockMsg) { 
                    lockMsg.classList.add('hidden');
                    console.log('[Init] 🔓 Hidden lock message');
                }
                const formContent = document.getElementById('current-form-content');
                if (formContent) { 
                    formContent.classList.remove('hidden');
                    console.log('[Init] 📝 Showed form content');
                }
            } else {
                // فقط إذا لم يكن هناك أي سؤال مكشوف، أخف السؤال الأول
                currentIndex = 0;
                console.log('[Init] ⚠️ No revealed questions found, locking first question');
                if (IS_EDIT_MODE) {
                    loadQuestion(0);
                } else {
                    lockQuestionView(0);
                }
            }

            updateQuestionHighlight();
            updateFooter();
        }
    });
</script>

<!-- Modal تأكيد الحفظ -->
<div id="save-confirmation-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="mr-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">تأكيد الحفظ النهائي</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">هل أنت متأكد من حفظ التقييم؟</p>
                </div>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 mt-0.5 ml-2"></i>
                    <div class="text-sm text-blue-800 dark:text-blue-200">
                        <p class="font-medium">ملاحظة مهمة:</p>
                        <p>بعد الحفظ لن تتمكن من تعديل التقييم مرة أخرى.</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" id="cancel-save-btn"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-times ml-2"></i> إلغاء
                </button>
                <button type="button" id="confirm-save-btn"
                        class="flex-1 bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded-lg transition-colors">
                    <i class="fas fa-save ml-2"></i> تأكيد الحفظ
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Ensure modal event listeners are attached after DOM is fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded fired for modal setup');

        // Check if modal elements exist before setting up listeners
        const modalCheck = document.getElementById('save-confirmation-modal');
        const cancelBtnCheck = document.getElementById('cancel-save-btn');
        const confirmBtnCheck = document.getElementById('confirm-save-btn');

        console.log('Initial modal elements check:', {
            modal: !!modalCheck,
            cancelBtn: !!cancelBtnCheck,
            confirmBtn: !!confirmBtnCheck
        });

        // Re-attach modal event listeners after a short delay to ensure DOM is ready
        setTimeout(() => {
            console.log('Setting up modal event listeners...');
            const saveModal = document.getElementById('save-confirmation-modal');
            const cancelSaveBtn = document.getElementById('cancel-save-btn');
            const confirmSaveBtn = document.getElementById('confirm-save-btn');

            console.log('Modal elements found:', {
                saveModal: !!saveModal,
                cancelSaveBtn: !!cancelSaveBtn,
                confirmSaveBtn: !!confirmSaveBtn
            });
            console.log('Modal elements details:', { saveModal, cancelSaveBtn, confirmSaveBtn });

            if (cancelSaveBtn) {
                cancelSaveBtn.addEventListener('click', function() {
                    console.log('Cancel button clicked');
                    if (typeof window.hideSaveConfirmationModal === 'function') {
                        window.hideSaveConfirmationModal();
                    } else {
                        console.error('hideSaveConfirmationModal function not found');
                        // Fallback: hide modal directly
                        const modal = document.getElementById('save-confirmation-modal');
                        if (modal) {
                            modal.classList.add('hidden');
                            modal.classList.remove('flex');
                        }
                    }
                });
            }

            if (confirmSaveBtn) {
                confirmSaveBtn.addEventListener('click', async function() {
                    console.log('Confirm save button clicked');
                    try {
                        // Show loading state
                        confirmSaveBtn.disabled = true;
                        confirmSaveBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الحفظ...';

                        // Call the actual save function
                        if (typeof window.finalizeEvaluations === 'function') {
                            const saveResult = await window.finalizeEvaluations();
                            
                            // إذا كان الحفظ فشل (returned false)، نغلق الـ modal ونعيد تعيين الزر
                            if (saveResult === false) {
                                console.log('[Modal] Save failed, closing modal and resetting button');
                                confirmSaveBtn.disabled = false;
                                confirmSaveBtn.innerHTML = '<i class="fas fa-save ml-2"></i> تأكيد الحفظ';
                                
                                // إغلاق الـ modal
                                if (typeof window.hideSaveConfirmationModal === 'function') {
                                    window.hideSaveConfirmationModal();
                                } else {
                                    const modal = document.getElementById('save-confirmation-modal');
                                    if (modal) {
                                        modal.classList.add('hidden');
                                        modal.classList.remove('flex');
                                    }
                                }
                                
                                // لا نرمي error، فقط نرجع
                                return;
                            }
                            
                            // إذا نجح الحفظ، نترك الـ modal مفتوحاً حتى يتم إعادة التوجيه
                            console.log('Form submitted successfully, waiting for server redirect...');
                        } else {
                            throw new Error('Save function not available');
                        }

                    } catch (error) {
                        console.error('Error saving final assessment:', error);

                        // Reset button on error
                        confirmSaveBtn.disabled = false;
                        confirmSaveBtn.innerHTML = '<i class="fas fa-save ml-2"></i> تأكيد الحفظ';

                        // إغلاق الـ modal أولاً
                        if (typeof window.hideSaveConfirmationModal === 'function') {
                            window.hideSaveConfirmationModal();
                        } else {
                            const modal = document.getElementById('save-confirmation-modal');
                            if (modal) {
                                modal.classList.add('hidden');
                                modal.classList.remove('flex');
                            }
                        }

                        // Show error notification بعد إغلاق الـ modal
                        setTimeout(() => {
                            if (typeof window.showCustomNotification === 'function') {
                                // التحقق من نوع الرسالة
                                const errorMessage = error.message || 'حدث خطأ أثناء حفظ التقييم. حاول مرة أخرى.';
                                const isInfoMessage = errorMessage.includes('الانتظار') || errorMessage.includes('متبقي');
                                const notificationType = isInfoMessage ? 'info' : 'error';
                                const title = isInfoMessage ? 'تنبيه' : 'خطأ في الحفظ';
                                
                                window.showCustomNotification(
                                    title,
                                    errorMessage,
                                    notificationType,
                                    8000
                                );
                            } else {
                                // Fallback: show browser alert
                                alert(error.message || 'حدث خطأ أثناء حفظ التقييم. حاول مرة أخرى.');
                            }
                        }, 300);
                    }
                });
            }

            // Close modal when clicking outside
            if (saveModal) {
                saveModal.addEventListener('click', function(e) {
                    if (e.target === saveModal) {
                        console.log('Modal background clicked');
                        if (typeof window.hideSaveConfirmationModal === 'function') {
                            window.hideSaveConfirmationModal();
                        } else {
                            // Fallback: hide modal directly
                            saveModal.classList.add('hidden');
                            saveModal.classList.remove('flex');
                        }
                    }
                });
            }
            console.log('Modal event listeners setup completed');

            // Final verification
            setTimeout(() => {
                console.log('Final verification of modal setup:');
                console.log('Modal element:', document.getElementById('save-confirmation-modal'));
                console.log('Cancel button:', document.getElementById('cancel-save-btn'));
                console.log('Confirm button:', document.getElementById('confirm-save-btn'));
                console.log('Functions available:', {
                    showModal: typeof window.showSaveConfirmationModal,
                    hideModal: typeof window.hideSaveConfirmationModal,
                    finalize: typeof window.finalizeEvaluations,
                    notify: typeof window.showCustomNotification
                });
            }, 1000);

        }, 500); // Increased delay to ensure all scripts are loaded
    });
</script>

</body>

</html>

<script>
    const html = document.documentElement;
    const toggleBtn = document.getElementById('theme-toggle');

    toggleBtn.addEventListener('click', () => {
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            html.classList.add('light');
            localStorage.setItem('theme', 'light');
        } else {
            html.classList.remove('light');
            html.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
    });

    // On page load, apply saved theme
    if (localStorage.getItem('theme') === 'dark') {
        html.classList.add('dark');
    } else {
        html.classList.add('light');
    }

    // Notifications Polling System
    var notificationCheckInterval;
    var lastNotificationCount = 0;

    function startNotificationPolling() {
        // Initialize notification count first
        checkForNotifications();
        // Check for notifications every 3 seconds
        notificationCheckInterval = setInterval(checkForNotifications, 3000);
    }

    function stopNotificationPolling() {
        if (notificationCheckInterval) {
            clearInterval(notificationCheckInterval);
        }
    }

    function checkForNotifications() {
        fetch('{{ url('/api/notifications/unread-count') }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.count > lastNotificationCount) {
                    // New notifications available
                    showNotificationBadge(data.count);
                    fetchLatestNotifications();
                    lastNotificationCount = data.count;
                }
            })
            .catch(error => {
                console.log('Error checking notifications:', error);
            });
    }

    function fetchLatestNotifications() {
        fetch('{{ url('/api/notifications/latest') }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.notifications && data.notifications.length > 0) {
                    data.notifications.forEach(notification => {
                        if (notification.type === 'relief_request') {
                            showReliefRequestNotification(notification);

                            // Reload pending relief requests when new relief notification arrives
                            if (typeof loadPendingReliefRequests === 'function') {
                                setTimeout(() => {
                                    loadPendingReliefRequests();
                                }, 1000);
                            }
                        }
                    });
                }
            })
            .catch(error => {
                console.log('Error fetching notifications:', error);
            });
    }

    function showNotificationBadge(count) {
        // Create or update notification badge
        let badge = document.querySelector('.notification-badge');
        if (!badge) {
            badge = document.createElement('div');
            badge.className = 'notification-badge fixed top-4 right-4 bg-red-500 text-white rounded-full px-3 py-1 text-sm font-bold z-50';
            document.body.appendChild(badge);
        }
        badge.textContent = count;
        badge.style.display = count > 0 ? 'block' : 'none';
    }

    function showReliefRequestNotification(notification) {
        // Show relief request notification with approval/denial buttons
        const notificationDiv = document.createElement('div');
        notificationDiv.className = 'relief-notification fixed top-20 right-4 bg-white border border-gray-300 rounded-lg shadow-lg p-4 max-w-sm z-50';

        const judgeName = notification.relief_data?.judge_name || 'محكم';
        const participantName = notification.relief_data?.participant_name || 'متسابق';
        const requestId = notification.relief_data?.request_id || notification.id;

        notificationDiv.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-hand-paper text-orange-600"></i>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <h4 class="text-sm font-medium text-gray-900">${notification.title}</h4>
                    <p class="text-sm text-gray-600 mt-1">
                        ${judgeName} يطلب تخفيف لـ ${participantName}
                    </p>
                    <div class="mt-3 flex space-x-2">
                        <button onclick="approveReliefRequest('${notification.id}', '${requestId}')"
                                class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 flex items-center">
                            <i class="fas fa-check ml-1"></i>
                            موافقة
                        </button>
                        <button onclick="denyReliefRequest('${notification.id}', '${requestId}')"
                                class="text-xs bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 flex items-center">
                            <i class="fas fa-times ml-1"></i>
                            رفض
                        </button>
                        <button onclick="closeNotification(this)"
                                class="text-xs bg-gray-300 text-gray-700 px-2 py-1 rounded hover:bg-gray-400">
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(notificationDiv);

        // Auto remove after 30 seconds (longer for action required)
        setTimeout(() => {
            if (notificationDiv.parentNode) {
                notificationDiv.remove();
            }
        }, 30000);
    }

    function markNotificationAsRead(notificationId) {
        fetch(`/api/notifications/mark-read/${notificationId}`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge count
                    lastNotificationCount = Math.max(0, lastNotificationCount - 1);
                    showNotificationBadge(lastNotificationCount);
                }
            })
            .catch(error => {
                console.log('Error marking notification as read:', error);
            });
    }

    function closeNotification(button) {
        button.closest('.relief-notification').remove();
    }

    function approveReliefRequest(notificationId, requestId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('/api/relief-requests/approve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                notification_id: notificationId,
                request_id: requestId,
                action: 'approve'
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCustomNotification('تمت الموافقة', 'تمت الموافقة على طلب التخفيف بنجاح', 'success', 3000);
                    markNotificationAsRead(notificationId);

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
                    if (typeof checkReliefApprovedStatus === 'function') {
                        setTimeout(() => {
                            checkReliefApprovedStatus();
                        }, 500);
                    }
                } else {
                    showCustomNotification('خطأ', 'فشل في الموافقة على طلب التخفيف', 'error', 3000);
                }
            })
            .catch(error => {
                console.error('Error approving relief request:', error);
                showCustomNotification('خطأ', 'حدث خطأ أثناء الموافقة على طلب التخفيف', 'error', 3000);
            });
    }

    function denyReliefRequest(notificationId, requestId) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch('/api/relief-requests/deny', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                notification_id: notificationId,
                request_id: requestId,
                action: 'deny'
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCustomNotification('تم الرفض', 'تم رفض طلب التخفيف', 'warning', 3000);
                    markNotificationAsRead(notificationId);

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
                    const reliefKey = `relief-request-sent-${userId}-${participantId}-${competitionBranchId}-${committeeId ?? 'none'}`;
                    localStorage.removeItem(reliefKey);

                    // Also clear any other relief-related keys for this participant/competition
                    for (let i = 0; i < localStorage.length; i++) {
                        const key = localStorage.key(i);
                        if (key && key.includes('relief-request-sent') && key.includes(participantId) && key.includes(competitionBranchId) && key.includes(String(committeeId ?? 'none'))) {
                            localStorage.removeItem(key);
                            console.log('Cleared additional relief key:', key);
                        }
                    }

                    console.log('Cleared localStorage after denial for key:', reliefKey);

                    // Reload pending relief requests for real-time update
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
                    showCustomNotification('خطأ', 'فشل في رفض طلب التخفيف', 'error', 3000);
                }
            })
            .catch(error => {
                console.error('Error denying relief request:', error);
                showCustomNotification('خطأ', 'حدث خطأ أثناء رفض طلب التخفيف', 'error', 3000);
            });
    }

    // Start polling when page loads
    startNotificationPolling();

    // Stop polling when page unloads
    window.addEventListener('beforeunload', stopNotificationPolling);
</script>