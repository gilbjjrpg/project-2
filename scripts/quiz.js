//waits for the page to fully load before running any code.
window.addEventListener("DOMContentLoaded", function() {
    setupQuizMenuPage();
    setupQuizCustomizePage();
    setupQuizTimePage();
});

// QUIZ MENU page 

function setupQuizMenuPage() {

    // receives 10 question button from quiz.php
    const startTenQuestionBtn = document.getElementById("startTenQuestion");

    // if the button doesn't exist, then this isn't the quiz menu page
    if(!startTenQuestionBtn) {
        return;
    }

    // wait for user to click the 10-question quiz option
    startTenQuestionBtn.addEventListener("click", function(event) {
        event.preventDefault();
        
        // create the settings for the standard 10-question quiz
        const quizConfig = {
            mode: "basic",
            questionCount: 10,
            timePerQuestion: 60,
            range: "all",
            countsForLeaderboard: true
        };

        //saves these settings to sessionStorage so that quizTime.php can read 
        //and display the patch with the accurate details
        sessionStorage.setItem("quizConfig", JSON.stringify(quizConfig));

        //sends the user to the actual quiz page.
        window.location.href = "quizTime.php"
    });
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// QUIZ CUSTOMIZE PAGE 

function setupQuizCustomizePage() {

    // get the custome quiz from from quizCustomize.php
    const customQuizForm = document.getElementById("customQuizForm");

    //if the form doesn't exist, then this is not the custom quiz page
    if(!customQuizForm) {
        return;
    }

    // wait for the user to submit the custom quiz form.
    customQuizForm.addEventListener("submit", function(event) {

        //stops the normal from submission so JS can save the settings first
        event.preventDefault();

        //questionCount = receives the number of questions the user wants
        //timePerQuestion = receives the time per question the user wants 
        //range = recives the selected question range
        const questionCount = Number(document.getElementById("questionCount").value);
        const timePerQuestion = Number(document.getElementById("timePerQuestion").value);
        const range = document.getElementById("questionRange").value;

        // builds the quizConfig object for later use
        const quizConfig = {
            mode: "custom",
            questionCount: questionCount,
            timePerQuestion: timePerQuestion,
            range: range,
            countsForLeaderboard: false
        };

        //save the custom quiz page setting so quizTime.php can use them.
        sessionStorage.setItem("quizConfig", JSON.stringify(quizConfig));

        // redirects user to the quizTime.php window with these questions.
        window.location.href = "quizTime.php"
    })
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// QUIZ TIME PAGE 

let allQuestions = []; //stores all questions loaded from questions.json
let quizQuestions = []; //stores *ONLY* the questions selecter for the current quiz
let currentQuestionIndex = 0; //keeps track of which question the user is currently on
let totalCorrect = 0; //keeps track of total correct questions
let selectedAnswer = null; //stores the selected answer the user chose for current question
let quizConfig = null; //stores the quiz settings loaded from sessionStorage

async function setupQuizTimePage() {

    //questionText = gets the area from quizTime.php where the question text will be displayed
    //answerButtons = gets all the answer buttons
    //nextBtn = gets the next button
    const questionText = document.getElementById("questionText");
    const answerButtons = document.querySelectorAll(".answer-btn");
    const nextBtn = document.getElementById("nextBtn");

    //if required elements are not on this pages, then return.
    if(!questionText || answerButtons.length === 0 || !nextBtn) {
        return;
    }

    //parses quizConfig to receive the saved settings from sessionStorage
    quizConfig = JSON.parse(sessionStorage.getItem("quizConfig"));

    //no quizConfif = no quiz. Stops and sends an error to the console.
    if(!quizConfig) {
        console.error("No quizConfig found.")
        return;
    }

    //waits for all questions from questions.json to be loaded
    allQuestions = await loadQuestions();

    //if none of the questions were loaded, return an error and stop.
    if(allQuestions.length === 0) {
        console.error("No questions loaded.")
        return;
    }

    //generateQuizQuestions receives information from quizQuestions and passes them along allQuestions and quizConfig.questionCount
    //to the parameters of the generateQuizQuestions function to generate a quiz.
    quizQuestions = generateQuizQuestions(allQuestions, quizConfig.questionCount);

    //makes the answer buttons clickable
    setupAnswerButtons(); 

    //displays the first question on the page
    displayQuestion();

    //event listner for when the next button is clicked. moves on to the next question.
    nextBtn.addEventListener("click", handleNextQuestion);
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// LOAD QUESTIONS FROM QUESTIONS.JSON TO USE

//This is the loadQuestions function mentioned earlier. Receives data from questions.json to display.
async function loadQuestions() {
    try {

        //fetch questions from questions.json
        const response = await fetch('../data/questions.json')

        //waits for data to be converted into a JS array/object
        const data = await response.json();

        //return the data if it is successful
        return data;

    } catch (error) {

        //if anything goes wrong while at this time, print the fetch error and stop.
        console.error('Fetch error:', error);

        //returns an empty array to prevent the code from crashing.
        return [];
    }
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// SHUFFLE ARRAY: SHUFFLES QUESTIONS FOR USER AUTOMATICALLY 

function shuffleArray(array){

    //copy the original array so the original question bank doesn't change
    const copiedArray = [...array];

    //start from the end of the array and work backwards
    for(let i = copiedArray.length - 1; i > 0; i--) {

        //pick a randmo index between 0 and i
        const randomIndex = Math.floor(Math.random() * (i + 1));

        //copiedArray swaps the current item with the random array
        [copiedArray[i], copiedArray[randomIndex]] = [copiedArray[randomIndex], copiedArray[i]];
    }

    //returns the shuffled copy
    return copiedArray;
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// QUESTION GENERATOR 

function generateQuizQuestions(questions, amount) {

    //shuffle all avaiable questions FIRST
    const shuffledQuestions = shuffleArray(questions);

    //return only the number of questions needed for this quiz
    return shuffledQuestions.slice(0, amount);
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// QUESTION & ANSWER OPTIONS DISPLAYER 


function displayQuestion() {

    //questionText = receives the question text area
    //answerButtons = receives the answer buttons
    //quizProgress = get the progress display area
    const questionText = document.getElementById("questionText");
    const answerButtons = document.querySelectorAll(".answer-btn");
    const quizProgress = document.getElementById("quizProgress");

    //currentQuestion = gets the current question based on the currentQuestionIndex
    const currentQuestion = quizQuestions[currentQuestionIndex];

    //displays the question text
    questionText.textContent = currentQuestion.question;

    //displays the question the user is on out of how many questions are left
    quizProgress.textContent = "Question " + (currentQuestionIndex + 1) + " of " + quizQuestions.length;


    //all of these are answer buttons ranging from A to D
    answerButtons[0].textContent = currentQuestion.A;
    answerButtons[0].dataset.answer = "A";

     answerButtons[1].textContent = currentQuestion.B;
    answerButtons[1].dataset.answer = "B";

     answerButtons[2].textContent = currentQuestion.C;
    answerButtons[2].dataset.answer = "C";

     answerButtons[3].textContent = currentQuestion.D;
    answerButtons[3].dataset.answer = "D";

    //resets selected answer for each new questions
    selectedAnswer = null;
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// SET UP FOR ANSWER BUTTONS TO MAKE THEM FUNCTIONAL

function setupAnswerButtons() {

    //receives the answer buttons from quizTime.php
    const answerButtons = document.querySelectorAll(".answer-btn");

    //loops through each answwer button.
    answerButtons.forEach(function (button){

        //when a button is clicked, save its answer letter
        button.addEventListener("click", function() {
            selectedAnswer = button.dataset.answer;
        });
    });
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// NEXT QUESTION HANDLER

function handleNextQuestion() {

    //if the user has not selected an answer yet, show an alert and stop
    if(selectedAnswer === null) {
        alert("HEY! Select an answer first!")
        return;
    }

    //get the current question object
    const currentQuestion = quizQuestions[currentQuestionIndex];

    //if the selected answer is corret, increment totalCorrect
    if(selectedAnswer === currentQuestion.answer) {
        totalCorrect++;
    }

    //increment currentQuestionIndex to move to the next question
    currentQuestionIndex++;

    //display question(s) if there are any left.
    if(currentQuestionIndex < quizQuestions.length) {
        displayQuestion();

        //if not, then call showFinalScore
    } else {
        showFinalScore();
    }
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// RESULTS 

function showFinalScore() {

    //get the quizContainter in quizTime.php
    const quizContainer = document.getElementById("quizContainer");

    //divides totalCorrect to the length of questions, multiplies it by 100 and rounds it to get the final score.
    const scorePercent = Math.round((totalCorrect / quizQuestions.length) * 100);

    //replace the quiz area with the final results message
    quizContainer.innerHTML = "<h2>Quiz complete!</h2><p>You got " + (totalCorrect) + " out of " + quizQuestions.length + " correct.</p> <p>Your score: " + scorePercent + "</p>";
}