<?php
require_once __DIR__ . '/config/functions.php';
$page_title = "Live Typing Speed Test - Micro Group Computer Institute";

$manager_name = get_setting('manager_name', 'DK Singh');
$location = get_setting('location', 'Bhoopganj Payagpur');
$site_name = get_setting('institute_name', 'Micro Group of Computer Institute');

require_once __DIR__ . '/includes/header.php';
?>

<div class="py-5 bg-dark text-white text-center" style="background: radial-gradient(circle at center, #0F233E 0%, #09172A 100%);">
    <div class="container py-3">
        <span class="badge bg-warning text-dark px-3 py-2 fw-bold text-uppercase mb-2"><i class="bi bi-keyboard-fill me-1"></i>Official Typing Portal</span>
        <h1 class="display-6 fw-bold text-white mb-2">Live Computer Typing Speed Test</h1>
        <p class="text-light opacity-75 mx-auto" style="max-width: 650px;">
            Test your typing speed (WPM) and accuracy in real-time. Score <strong>25+ WPM</strong> to earn your official <strong>Typing Proficiency Certificate</strong> from <?= htmlspecialchars($site_name) ?>!
        </p>
    </div>
</div>

<section class="py-5 bg-light">
    <div class="container py-3">
        
        <!-- Registration & Setup Card -->
        <div id="setup-card" class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4 mx-auto" style="max-width: 750px;">
            <div class="text-center mb-4">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex p-3 fs-2 mb-2">
                    <i class="bi bi-person-bounding-box"></i>
                </div>
                <h4 class="fw-bold text-dark">Enter Candidate Details to Begin</h4>
                <p class="text-muted small">Your name will be printed on your official Typing Certificate.</p>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Candidate Full Name *</label>
                    <input type="text" id="candidate_name" class="form-control form-control-lg" placeholder="e.g. Rahul Kumar" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Mobile Number (Optional)</label>
                    <input type="tel" id="candidate_phone" class="form-control form-control-lg" placeholder="e.g. 9876543210">
                </div>
                <div class="col-md-12">
                    <label class="form-label small fw-bold">Select Test Duration</label>
                    <div class="d-flex gap-3">
                        <div class="form-check flex-grow-1 p-3 border rounded-3 bg-light text-center">
                            <input class="form-check-input ms-0 me-2" type="radio" name="test_duration" id="dur1" value="1" checked>
                            <label class="form-check-label fw-bold cursor-pointer" for="dur1">1 Minute Test</label>
                        </div>
                        <div class="form-check flex-grow-1 p-3 border rounded-3 bg-light text-center">
                            <input class="form-check-input ms-0 me-2" type="radio" name="test_duration" id="dur2" value="2">
                            <label class="form-check-label fw-bold cursor-pointer" for="dur2">2 Minute Test</label>
                        </div>
                        <div class="form-check flex-grow-1 p-3 border rounded-3 bg-light text-center">
                            <input class="form-check-input ms-0 me-2" type="radio" name="test_duration" id="dur5" value="5">
                            <label class="form-check-label fw-bold cursor-pointer" for="dur5">5 Minute Test</label>
                        </div>
                    </div>
                </div>
                <div class="col-12 mt-4">
                    <button type="button" class="btn btn-primary-mgi btn-lg w-100 py-3 fw-bold" onclick="startTypingTest()">
                        <i class="bi bi-play-circle-fill me-2"></i> Start Typing Test Now
                    </button>
                </div>
            </div>
        </div>

        <!-- Active Test Interface (Hidden initially) -->
        <div id="test-interface" class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4 mx-auto d-none" style="max-width: 900px;">
            <!-- Live Metrics Header -->
            <div class="p-3 bg-dark text-white d-flex justify-content-around align-items-center text-center flex-wrap gap-2">
                <div>
                    <div class="small text-secondary text-uppercase fw-semibold">Time Remaining</div>
                    <div class="fs-4 fw-bold text-warning" id="timer-val">01:00</div>
                </div>
                <div class="border-start border-secondary opacity-50 d-none d-sm-block" style="height: 35px;"></div>
                <div>
                    <div class="small text-secondary text-uppercase fw-semibold">Speed (WPM)</div>
                    <div class="fs-4 fw-bold text-info" id="live-wpm">0</div>
                </div>
                <div class="border-start border-secondary opacity-50 d-none d-sm-block" style="height: 35px;"></div>
                <div>
                    <div class="small text-secondary text-uppercase fw-semibold">Accuracy</div>
                    <div class="fs-4 fw-bold text-success" id="live-accuracy">100%</div>
                </div>
                <div class="border-start border-secondary opacity-50 d-none d-sm-block" style="height: 35px;"></div>
                <div>
                    <div class="small text-secondary text-uppercase fw-semibold">Mistakes</div>
                    <div class="fs-4 fw-bold text-danger" id="live-mistakes">0</div>
                </div>
            </div>

            <div class="p-4 p-md-5">
                <!-- Word Display Box -->
                <div class="p-4 rounded-4 bg-light border mb-4 text-start font-monospace" style="font-size: 1.25rem; line-height: 2.2; max-height: 220px; overflow-y: auto; user-select: none;" id="words-container">
                    <!-- Words rendered by JS -->
                </div>

                <!-- Input Box -->
                <div class="mb-3">
                    <input type="text" id="typing-input" class="form-control form-control-lg p-3 fs-5 font-monospace shadow-sm" placeholder="Type here and press Spacebar..." autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" oninput="handleTyping(event)">
                </div>

                <div class="d-flex justify-content-between align-items-center text-muted small">
                    <span><i class="bi bi-info-circle me-1"></i>Press <strong>Spacebar</strong> after each word to advance.</span>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="cancelTest()">Quit Test</button>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Result & Certificate Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg text-center p-4">
            <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-inline-flex mx-auto mb-2 fs-1">
                <i class="bi bi-award-fill"></i>
            </div>
            <h3 class="fw-bold text-dark mb-1">Typing Test Completed!</h3>
            <p class="text-muted small mb-4">Official performance evaluation by Micro Group Institute.</p>

            <div class="row g-3 text-center mb-4">
                <div class="col-4">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="small text-muted">Net Speed</div>
                        <h4 class="fw-bold text-primary mb-0" id="res-wpm">0</h4>
                        <small class="text-muted">WPM</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="small text-muted">Accuracy</div>
                        <h4 class="fw-bold text-success mb-0" id="res-acc">0%</h4>
                        <small class="text-muted">Precision</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="small text-muted">Grade</div>
                        <h4 class="fw-bold text-warning mb-0" id="res-grade">A</h4>
                        <small class="text-muted">Rating</small>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="#" id="cert-link-btn" target="_blank" class="btn btn-warning btn-lg fw-bold text-dark py-3">
                    <i class="bi bi-file-earmark-check-fill me-2"></i> View & Print Typing Certificate
                </a>
                <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
                    <i class="bi bi-arrow-repeat me-1"></i> Retake Test
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const sampleTexts = [
    "Computer technology has revolutionized the way modern education, communication, and government administration function across the globe. Learning fundamental software applications such as word processing, spreadsheet analysis, database management, and web programming empowers students to secure high-paying careers in both public and private sectors. Micro Group of Computer Institute provides rigorous practical training and industry-recognized certifications in Bhoopganj Payagpur.",
    "A computer system is composed of hardware components such as the Central Processing Unit, Random Access Memory, motherboard, and storage devices. The operating system acts as the core interface between the human operator and machine electronics. Developing high typing speed and accurate keyboarding skills is essential for office assistants, computer operators, and data analysts.",
    "Digital skills enable students to unlock boundless opportunities in software development, financial accounting with Tally Prime, graphic designing, and digital marketing. Regular practice, disciplined learning, and hands-on laboratory exercises ensure mastery over modern information technology tools."
];

let wordsList = [];
let currentWordIndex = 0;
let correctWordsCount = 0;
let incorrectWordsCount = 0;
let totalTypedChars = 0;
let testDurationSeconds = 60;
let timeRemaining = 60;
let timerInterval = null;
let testActive = false;
let candidateName = '';
let candidatePhone = '';

function startTypingTest() {
    candidateName = document.getElementById('candidate_name').value.trim();
    candidatePhone = document.getElementById('candidate_phone').value.trim();
    if (!candidateName) {
        alert('Please enter your full name before starting the test.');
        document.getElementById('candidate_name').focus();
        return;
    }

    const durationRadio = document.querySelector('input[name="test_duration"]:checked');
    const durationMins = parseInt(durationRadio.value);
    testDurationSeconds = durationMins * 60;
    timeRemaining = testDurationSeconds;

    // Pick random text
    const text = sampleTexts[Math.floor(Math.random() * sampleTexts.length)] + " " + sampleTexts[0] + " " + sampleTexts[1];
    wordsList = text.replace(/[\n\r]+/g, ' ').split(/\s+/).filter(w => w.length > 0);

    currentWordIndex = 0;
    correctWordsCount = 0;
    incorrectWordsCount = 0;
    totalTypedChars = 0;

    renderWords();

    document.getElementById('setup-card').classList.add('d-none');
    document.getElementById('test-interface').classList.remove('d-none');

    const input = document.getElementById('typing-input');
    input.value = '';
    input.disabled = false;
    input.focus();

    updateTimerDisplay();
    startTimer();
    testActive = true;
}

function renderWords() {
    const container = document.getElementById('words-container');
    container.innerHTML = '';
    wordsList.forEach((word, idx) => {
        const span = document.createElement('span');
        span.id = `word-${idx}`;
        span.innerText = word + ' ';
        span.className = (idx === 0) ? 'bg-warning text-dark px-1 rounded fw-bold' : 'text-secondary';
        container.appendChild(span);
    });
}

function startTimer() {
    timerInterval = setInterval(() => {
        timeRemaining--;
        updateTimerDisplay();
        updateLiveMetrics();

        if (timeRemaining <= 0) {
            endTypingTest();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const mins = Math.floor(timeRemaining / 60);
    const secs = timeRemaining % 60;
    document.getElementById('timer-val').innerText = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
}

function handleTyping(e) {
    if (!testActive) return;
    const input = document.getElementById('typing-input');
    const val = input.value;

    if (val.endsWith(' ')) {
        const typedWord = val.trim();
        const expectedWord = wordsList[currentWordIndex];
        const span = document.getElementById(`word-${currentWordIndex}`);

        if (typedWord === expectedWord) {
            correctWordsCount++;
            if (span) span.className = 'text-success fw-bold';
        } else {
            incorrectWordsCount++;
            if (span) span.className = 'text-danger text-decoration-line-through';
        }

        totalTypedChars += typedWord.length + 1;
        currentWordIndex++;

        // Highlight next
        const nextSpan = document.getElementById(`word-${currentWordIndex}`);
        if (nextSpan) {
            nextSpan.className = 'bg-warning text-dark px-1 rounded fw-bold';
            nextSpan.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }

        input.value = '';
        updateLiveMetrics();
    }
}

function updateLiveMetrics() {
    const timeElapsedMins = (testDurationSeconds - timeRemaining) / 60;
    if (timeElapsedMins <= 0) return;

    const netWpm = Math.round(correctWordsCount / timeElapsedMins);
    const totalAttempted = correctWordsCount + incorrectWordsCount;
    const accuracy = totalAttempted > 0 ? Math.round((correctWordsCount / totalAttempted) * 100) : 100;

    document.getElementById('live-wpm').innerText = Math.max(0, netWpm);
    document.getElementById('live-accuracy').innerText = accuracy + '%';
    document.getElementById('live-mistakes').innerText = incorrectWordsCount;
}

function endTypingTest() {
    clearInterval(timerInterval);
    testActive = false;
    document.getElementById('typing-input').disabled = true;

    const totalMins = testDurationSeconds / 60;
    const netWpm = Math.round(correctWordsCount / totalMins);
    const grossWpm = Math.round((correctWordsCount + incorrectWordsCount) / totalMins);
    const totalAttempted = correctWordsCount + incorrectWordsCount;
    const accuracy = totalAttempted > 0 ? ((correctWordsCount / totalAttempted) * 100).toFixed(1) : 100;

    let grade = 'C';
    if (netWpm >= 40 && accuracy >= 95) grade = 'A+';
    else if (netWpm >= 30 && accuracy >= 90) grade = 'A';
    else if (netWpm >= 25 && accuracy >= 85) grade = 'B';

    // Send AJAX to save result & get certificate number
    fetch('<?= BASE_URL ?>/save-typing-result.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            candidate_name: candidateName,
            phone: candidatePhone,
            duration_mins: totalMins,
            net_wpm: netWpm,
            gross_wpm: grossWpm,
            accuracy: accuracy,
            mistakes: incorrectWordsCount,
            grade: grade
        })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('res-wpm').innerText = netWpm;
        document.getElementById('res-acc').innerText = accuracy + '%';
        document.getElementById('res-grade').innerText = grade;
        document.getElementById('cert-link-btn').href = `<?= BASE_URL ?>/typing-certificate.php?cert_no=${data.certificate_no}`;

        new bootstrap.Modal(document.getElementById('resultModal')).show();
    })
    .catch(() => {
        // Fallback demo certificate
        const demoCert = 'TYP-2026-' + Math.floor(10000 + Math.random() * 90000);
        document.getElementById('res-wpm').innerText = netWpm;
        document.getElementById('res-acc').innerText = accuracy + '%';
        document.getElementById('res-grade').innerText = grade;
        document.getElementById('cert-link-btn').href = `<?= BASE_URL ?>/typing-certificate.php?cert_no=${demoCert}&name=${encodeURIComponent(candidateName)}&wpm=${netWpm}&acc=${accuracy}&grade=${grade}`;

        new bootstrap.Modal(document.getElementById('resultModal')).show();
    });
}

function cancelTest() {
    if (confirm('Are you sure you want to quit the current typing test?')) {
        clearInterval(timerInterval);
        location.reload();
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>