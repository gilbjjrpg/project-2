window.addEventListener("DOMContentLoaded", function() {
    setupQuizPage();
});

function setupQuizPage() {
    
}

async function loadQuestions() {
    try {
        const response = await fetch('../data/questions.json')
        const data = await response.json();
        console.log(data);
    } catch (error) {
        console.error('Fetch error:', error);
    }
}

loadQuestions();