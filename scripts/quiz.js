//waits for the page to fully load before running any code.
window.addEventListener("DOMContentLoaded", function() {

    //Decide which quiz page is currently open
    setupQuizMenuPage();
    setupQuizCustomizePage();
    setupQuizTimePage();
});

// QUIZ MENU page 

//Sets up the custom quiz page
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

//Sets up the custom quiz page
function setupQuizCustomizePage() {

    // get the custom quiz form from quizCustomize.php
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
        //range = receives the selected question range
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
let quizQuestions = []; //stores *ONLY* the questions selected for the current quiz
let currentQuestionIndex = 0; //keeps track of which question the user is currently on
let totalCorrect = 0; //keeps track of total correct questions
let selectedAnswer = null; //stores the selected answer the user chose for current question
let quizConfig = null; //stores the quiz settings loaded from sessionStorage
let timerInterval = null; //stores the active timer interval so it can be stopped/reset
let timeLeft = 0; //stores how many seconds are left for the current question
let quizStartTime = null; //stores when the quiz started so total time can be saved

//Sets up the live quiz page
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

    //no quizConfig = no quiz. Stops and sends an error to the console.
    if(!quizConfig) {
        console.error("No quizConfig found.");
        document.getElementById("quizContainer").innerHTML = "<p>Quiz settings were not found. Please go back and start a quiz from the quiz menu.</p>"
        return;
    }

    //waits for all questions from questions.json to be loaded
    allQuestions = await loadQuestions();

    //if none of the questions were loaded, return an error and stop.
    if(allQuestions.length === 0) {
        console.error("No questions loaded.");
        document.getElementById("quizContainer").innerHTML = "<p>Questions could not be loaded. Please try again.</p>"
        return;
    }

    //filterQuestionsByRange receives all questions and the selected range from quizConfig.
    //This makes custom quizzes only pull questions from the selected range.
    const filteredQuestions = filterQuestionsByRange(allQuestions, quizConfig.range);

    //if no questions exist in the selected range, stop and show an error message.
    if(filteredQuestions.length === 0) {
        console.error("No questions found for selected range.");
        document.getElementById("quizContainer").innerHTML = "<p>No questions were found for that range. Please choose another range.</p>";
        return;
    }

    //generateQuizQuestions receives the filtered question list and quizConfig.questionCount
    //to generate the final set of questions for this quiz.
    quizQuestions = generateQuizQuestions(filteredQuestions, quizConfig.questionCount);

    //Filters questions based on the custom range selected on quizCustomize.php.
    function filterQuestionsByRange(questions, range) {

        //If the user selected all questions, return the full question list.
        if(range === "all") {
            return questions;
        } 

        //Split the range value, such as "100-199", into a start and end number.
        const parts = range.split("-");
        const start = Number(parts[0]);
        const end = Number(parts[1]);

        //Return only the questions inside the selected index range.
        return questions.slice(start, end + 1);
    }

    //makes the answer buttons clickable
    setupAnswerButtons(); 

    //starts tracking total time for the whole quiz
    quizStartTime = Date.now();

    //displays the first question on the page
    displayQuestion();

    //event listener for when the next button is clicked. moves on to the next question.
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

        //pick a random index between 0 and i
        const randomIndex = Math.floor(Math.random() * (i + 1));

        //copiedArray swaps the current item with the random array
        [copiedArray[i], copiedArray[randomIndex]] = [copiedArray[randomIndex], copiedArray[i]];
    }

    //returns the shuffled copy
    return copiedArray;
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// QUESTION GENERATOR 

//Takes questions and amount the user has input to shuffle in an array and return
function generateQuizQuestions(questions, amount) {

    //shuffle all available questions FIRST
    const shuffledQuestions = shuffleArray(questions);

    //return only the number of questions needed for this quiz
    return shuffledQuestions.slice(0, amount);
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// QUESTION & ANSWER OPTIONS DISPLAYER 

//Displays the current question and answer choices
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

    //resets selected answer for each new question
    selectedAnswer = null;

    //removes the selected styling from all answer buttons for the new question
    answerButtons.forEach(function (button) {
        button.classList.remove("selected");
    });

    //starts the countdown timer for the current question
    startTimer();
}

//Starts or restarts the countdown timer for the current question
function startTimer() {

    //stops any old timer before starting a new one
    clearInterval(timerInterval);

    //quizTimer = gets the timer display area from quizTime.php
    const quizTimer = document.getElementById("quizTimer");

    //sets the timer to the number of seconds from quizConfig
    timeLeft = quizConfig.timePerQuestion;

    //displays the starting timer value
    quizTimer.textContent = "Time left: " + timeLeft + "s";

    //counts down once every second
    timerInterval = setInterval(function() {
        timeLeft--;
        quizTimer.textContent = "Time left: " + timeLeft + "s";

        //when the timer reaches zero, stop the timer and move on
        if(timeLeft <= 0) {
            clearInterval(timerInterval);
            handleTimeUp();
        }
    }, 1000);
}

//Handles what happens when the user runs out of time on a question
function handleTimeUp() {

    //sets selectedAnswer to null so the question counts as incorrect
    selectedAnswer = null;

    //moves to the next question
    currentQuestionIndex++;

    //if there are questions left, display the next one
    if(currentQuestionIndex < quizQuestions.length) {
        displayQuestion();

        //if not, show the final score
    } else {
        showFinalScore();
    }
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// SET UP FOR ANSWER BUTTONS TO MAKE THEM FUNCTIONAL


//Adds click behavior to answer buttons
function setupAnswerButtons() {

    //receives the answer buttons from quizTime.php
    const answerButtons = document.querySelectorAll(".answer-btn");

    //loops through each answer button.
    answerButtons.forEach(function (button){

        //when a button is clicked, save its answer letter
        button.addEventListener("click", function() {

            //removes selected style from all buttons FIRST
            answerButtons.forEach(function (btn) {
                btn.classList.remove("selected");
            });

            //save the selected answer letter 
            selectedAnswer = button.dataset.answer;

            //add the selected style to the clicked button
            button.classList.add("selected");
        });
    });
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// NEXT QUESTION HANDLER

//Checks the selected answer and updates score/progress
function handleNextQuestion() {

    //if the user has not selected an answer yet, show an alert and stop
    if(selectedAnswer === null) {
        alert("HEY! Select an answer first!")
        return;
    }

    //stops the current question timer once the user submits an answer
    clearInterval(timerInterval);

    //get the current question object
    const currentQuestion = quizQuestions[currentQuestionIndex];

    //if the selected answer is correct, increment totalCorrect
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
        console.log("Quiz finished. Showing results now.")
        showFinalScore();
    }
}

// ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------

// SAVE QUIZ FUNCTIONAL

//Sends the finished quiz result to the save_score.php
async function saveQuizScore(scorePercent) {

    // Get the quiz settings so we know what type of quiz this was
    const quizConfig = JSON.parse(sessionStorage.getItem("quizConfig"));

    let quizType = "Custom";

    // Standard 10-question quiz counts for leaderboard
    if (quizConfig && quizConfig.countsForLeaderboard) {
        quizType = "10 Question";
    }

    // Get today's date in YYYY-MM-DD format
    const dateTaken = new Date().toISOString().split("T")[0];

    // Get the actual number of questions used in this quiz
    const questionCount = quizQuestions.length;

    // Get total time in seconds from quiz start to quiz finish
    const totalTime = Math.round((Date.now() - quizStartTime) / 1000);

    try {
        const response = await fetch("save_score.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                quizType: quizType,
                score: scorePercent,
                questionCount: questionCount,
                totalTime: totalTime,
                dateTaken: dateTaken
            })
        });

        const result = await response.json();
        console.log(result.message);
    } catch (error) {
        console.error("Error saving score:", error);
    }
}

// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------

// RESULTS 

function showFinalScore() {

    clearInterval(timerInterval);

    //sends a message to the console to show that the showFinalScore function is running
    console.log("showFinalScore() is running");

    //get the quizContainer in quizTime.php
    const quizContainer = document.getElementById("quizContainer");

    //divides totalCorrect to the length of questions, multiplies it by 100 and rounds it to get the final score.
    const scorePercent = Math.round((totalCorrect / quizQuestions.length) * 100);

    //Save the score to the SQL database
    saveQuizScore(scorePercent);

    //replace the quiz area with the final results message
    quizContainer.innerHTML =
        "<div style='background:white; color:black; padding:30px; border-radius:12px; margin:40px;'>" +
        "<h2>Quiz complete!</h2>" +
        "<p>You got " + totalCorrect + " out of " + quizQuestions.length + " correct.</p>" +
        "<p>Your score: " + scorePercent + "%</p>" +
        "</div>";
}
