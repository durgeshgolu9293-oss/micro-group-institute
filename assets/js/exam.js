// Micro Group of Computer Institute - Enhanced Exam Engine
let durationMinutes = window.EXAM_DURATION_MINS || 15;
let timeRemaining = durationMinutes * 60;
let timerInterval = null;
let markedForReview = {};

function initExamTimer() {
    const timerDisplay = document.getElementById('exam-timer-display');
    const timerBar = document.getElementById('exam-progress-timer');
    const totalSeconds = timeRemaining;

    timerInterval = setInterval(() => {
        timeRemaining--;
        
        const mins = Math.floor(timeRemaining / 60);
        const secs = timeRemaining % 60;
        
        if (timerDisplay) {
            timerDisplay.innerText = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            if (timeRemaining <= 180) {
                timerDisplay.classList.add('text-danger', 'fw-bold', 'animate__animated', 'animate__pulse');
            }
        }

        if (timerBar) {
            const percentage = (timeRemaining / totalSeconds) * 100;
            timerBar.style.width = percentage + '%';
            if (percentage < 20) {
                timerBar.className = 'progress-bar bg-danger';
            }
        }

        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            alert('Time is up! Your examination will now be submitted automatically.');
            document.getElementById('exam-form').submit();
        }
    }, 1000);
}

function selectOption(qIndex, optionVal) {
    const radio = document.getElementById(`opt_${qIndex}_${optionVal}`);
    if (radio) {
        radio.checked = true;
    }
    const badge = document.getElementById(`palette-badge-${qIndex}`);
    if (badge) {
        badge.classList.remove('btn-outline-secondary', 'btn-warning');
        badge.classList.add('btn-success');
    }
    updateAttemptedCount();
}

function toggleMarkReview(qIndex) {
    const badge = document.getElementById(`palette-badge-${qIndex}`);
    if (markedForReview[qIndex]) {
        delete markedForReview[qIndex];
        const isChecked = document.querySelector(`input[name="answers[${window.QUESTIONS_MAP[qIndex]}]"]:checked`);
        if (isChecked) {
            badge.className = 'btn btn-success palette-btn';
        } else {
            badge.className = 'btn btn-outline-secondary palette-btn';
        }
    } else {
        markedForReview[qIndex] = true;
        badge.className = 'btn btn-warning palette-btn fw-bold';
    }
}

function updateAttemptedCount() {
    const radios = document.querySelectorAll('input[type="radio"]:checked');
    const countDisplay = document.getElementById('attempted-count-display');
    if (countDisplay) {
        countDisplay.innerText = radios.length;
    }
}

function showQuestion(index) {
    const questions = document.querySelectorAll('.question-card-item');
    questions.forEach((q, idx) => {
        if (idx === index) {
            q.style.display = 'block';
        } else {
            q.style.display = 'none';
        }
    });
    window.currentQIndex = index;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function nextQuestion() {
    if (window.currentQIndex < window.TOTAL_QUESTIONS - 1) {
        showQuestion(window.currentQIndex + 1);
    }
}

function prevQuestion() {
    if (window.currentQIndex > 0) {
        showQuestion(window.currentQIndex - 1);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.currentQIndex = 0;
    if (document.getElementById('exam-timer-display')) {
        initExamTimer();
    }
});