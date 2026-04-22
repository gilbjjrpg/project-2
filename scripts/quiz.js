window.addEventListener("DOMContentLoaded", function() {
    setupQuizMenuPage();
    setupQuizCustomizePage();
    setupQuizTimePage();
});

// QUIZ MENU page 

function setupQuizMenuPage() {
    const startTenQuestionBtn = document.getElementById("startTenQuestion");

    if(!startTenQuestionBtn) {
        return;
    }

    startTenQuestionBtn.addEventListener("click", function(event) {
        event.preventDefault;

        const quizConfig = {
            mode: "basic",
            questionCount: 10,
            timePerQuestion: 60,
            range: "all",
            countsForLeaderboard: true
        };

        sessionStorage.setItem("quizConfig", JSON.stringify(quizConfig));
        window.location.href = "quizTime.php"
    });
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// QUIZ CUSTOMIZE PAGE 

function setupQuizCustomizePage() {
    const customQuizForm = document.getElementById("customQuizForm");

    if(!customQuizForm) {
        return;
    }

    customQuizForm.addEventListener("submit", function(event) {
        event.preventDefault();

        const questionCount = Number(document.getElementById("questionCount").value);
        const timePerQuestion = Number(document.getElementById("timePerQuestion").value);
        const range = document.getElementById("questionRange").value;

        const quizConfig = {
            mode: "custom",
            questionCount: questionCount,
            timePerQuestion: timePerQuestion,
            range: range,
            countsForLeaderboard: false
        };

        sessionStorage.setItem("quizConfig", JSON.stringify(quizConfig));
        window.location.href = "quizTime.php"
    })
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// QUIZ TIME PAGE 

let allQuestions = [];
let quizQuestions = [];
let currentQuestionIndex = 0;
let totalCorrect = 0;
let selectedAnswer = null;
let quizConfig = null;

async function setupQuizTimePage() {
    const questionText = document.getElementById("questionText");
    const answerButtons = document.querySelectorAll(".answer-btn:");
    const nextBtn = document.getElementById("nextBtn");

    if(!questionText || answerButtons.length === 0 || !nextBtn) {
        return;
    }

    quizConfig = JSON.parse(sessionStorage.getItem("quizConfig"));

    if(!quizConfig) {
        console.error("No quizConfig found.")
        return;
    }

    allQuestions = await loadQuestions();

    if(allQuestions.length === 0) {
        console.error("No questions loaded.")
        return;
    }

    quizQuestions = generateQuizQuestions(allQuestions, quizConfig.questionCount);
    setupAnswerButtons();
    displayQuestion();

    nextBtn.addEventListener("click", handleNextQuestion);
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// LOAD QUESTIONS FROM QUESTIONS.JSON TO USE

async function loadQuestions() {
    try {
        const response = await fetch('../data/questions.json')
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Fetch error:', error);
        return [];
    }
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// SHUFFLE ARRAY: SHUFFLES QUESTIONS FOR USER AUTOMATICALLY 

function shuffleArray(array){
    const copiedArray = [...array];

    for(let i = copiedArray.length - 1; i > 0; i--) {
        const randomIndex = Math.floor(Math.random() * (i + 1));
        [copiedArray[i], copiedArray[randomIndex] = [copiedArray[randomIndex], copiedArray[i]]];
    }

    return copiedArray;
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// QUESTION GENERATOR 

function generateQuizQuestions(questions, amount) {
    const shuffledQuestions = shuffleArray(questions);
    return shuffleArray.slice(0, amount);
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// QUESTION & ANSWER OPTIONS DISPLAYER 

function displayQuestion() {
    const questionText = document.getElementById("questionText");
    const answerButtons = document.querySelectorAll(".answer-btn");
    const quizProgress = document.getElementById("quizProgress");

    const currentQuestion = quizQuestions[currentQuestionIndex];

    questionText.textContent = currentQuestion.question;
    quizProgress.textContent = 'Question ${currentQuestionIndex + 1} of ${quizQuestions.length}';

    answerButtons[0].textContent = currentQuestion.A;
    answerButtons[0].dataset.answer = "A";

     answerButtons[1].textContent = currentQuestion.B;
    answerButtons[1].dataset.answer = "B";

     answerButtons[2].textContent = currentQuestion.C;
    answerButtons[2].dataset.answer = "C";

     answerButtons[3].textContent = currentQuestion.D;
    answerButtons[3].dataset.answer = "D";

    selectedAnswer = null;
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// SET UP FOR ANSWER BUTTONS TO MAKE THEM FUNCTIONAL

function setupAnswerButtons() {
    const answerButtons = document.querySelectorAll(".answer-btn");

    answerButtons.forEach(function (button){
        button.addEventListener("click", function() {
            selectedAnswer = button.dataset.answer;
        });
    });
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// NEXT QUESTION HANDLER

function handleNextQuestion() {
    if(selectedAnswer === null) {
        alert("Select an answer first!")
        return;
    }

    const currentQuestion = quizQuestions[currentQuestionIndex];

    if(selectedAnswer === currentQuestion.answer) {
        totalCorrect++;
    }

    currentQuestionIndex++;

    if(currentQuestionIndex < quizQuestions.length) {
        displayQuestion();
    } else {
        showFinalScore();
    }
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// RESULTS 

function showFinalScore() {
    const quizContainer = document.getElementById("quizContainer");
    const scorePercent = Math.round((totalCorrect / quizQuestions.length) * 100);

    quizContainer.innerHTML = '<h2>Quiz complete!</h2> <p>You got ${totalCorrect} out of ${quizQuestions.length} correct.</p> <p>Your score: $scorePercent</p>';
}