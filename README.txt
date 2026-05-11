# Project 2: Quiz App

## Project Introduction

In this project you will be creating a quiz application. This application utilizes the client-server architecture. The server will be in charge of deciding which questions to include, when the game starts, keeping track of the score and serving the html/css/js files.
You should maintain and submit a README.md file. In this file you should include:

	1. all the features you have developed:
	A: A lot of the PHP files were developed by me. I received a lot of help from friends, and various sources 
	(like zybooks or stack overflow) to formulate a lot of quiz.js. There was probably an easier way I could've gone
	about making it, but seeing as it works, I decided to leave it as is. I didn't need nearly as much help with login.js.

	The inclusion of Docker, although strange, was something I had to do research on in order for the website to deploy
	on Render.

	Docker makes deployments predictable and consistent. The project heavily relies on PHP and database extensions
	so Docker ensures that those some requirements are installed the same way every time the app runs, making 
	deployments more reliable because it reduces the chance that the website works on one computer but fails
	on Render because of missing PHP extensions. Basically, it's the instructions sent to Render for deployment.

	2. how to run the server:
	A: You can run the server by either using PHP or Docker.
		NOTICE: You must have SQLite enabled on PHP to run this locally. Docker is recommended if
		you do not want to configure PHP extensions.

		See official PHP documentation:

		PDO installation:
		https://www.php.net/manual/en/pdo.installation.php

		PDO SQLite driver:
		https://www.php.net/manual/en/ref.pdo-sqlite.php

		- If you have PHP installed:
		1. Download the project files.
			!! IMPORTANT: If the project is downloaded as a ZIP file, extract/unzip it FIRST. !!

		2. Open Terminal, Command Prompt, or Powershell.

		3. Move into the project folder.

			i. On Mac, type:
				cd "/Users/YourName/Downloads/project 2"

			ii. On Windows, type:
				cd "C:\Users\YourName\Downloads\project 2"

		4. Confirm that you are inside the correct folder.

			i. On Mac, type:
				ls

			ii. On Windows, type:
				dir

			You should see:

			index.php
   			pages
  			data
   			scripts
   			style

		5. Start PHP's built-in server. Type:
			php -S localhost:8000

		6. Open this in your browser:
			http://localhost:8000

		NOTE: When running locally, the app uses the local SQLite database file at quizberry.db unless
			  MySQL environment variables are set.

			  If PHP gives a PDO/SQLite error, use the Docker instructions instead. Docker already includes 
			  the required database extensions.

		If by any chance you get:
			php: command not found
			zsh: command not found: php
		then that means PHP is not installed or available in your terminal.

		- For Docker:
		1. Install Docker Desktop: https://www.docker.com/products/docker-desktop/
		2. Open Docker Desktop and wait until it says Docker is running.
		3. Download the project files.
			!! IMPORTANT: If the project is downloaded as a ZIP file, extract/unzip it FIRST. !!

		4. Open Terminal, Command Prompt or Powershell.
		5. Move into the project folder.

			i. On Windows, type:
				cd "C:\Users\YourName\Downloads\project 2"

			ii. On Mac, type:
				cd "/Users/YourName/Downloads/project 2"

		6. Confirm that the Dockerfile is there. 
			i. On Windows, type:
					dir

			ii. On Mac, type:
					ls

				After this, you should see:

				Dockerfile
				pages
				data
				scripts
				style

		7. Build the Docker image. Type:
			docker build -t quizberry .

		8. Run the container. Type:
			docker run -p 8080:80 quizberry

		9. Finally, open this in your browser:
			http://localhost:8080


	3. the URL of the deployed website:
	A: https://quizberry.onrender.com/
	The website should force start on the login page first.

	4. the database schema. 
	A: All wireframes and the schema are available in their own folder in thie project. I didn't get to make them
	   look as I wanted them to look due to time constraints and there being other projects I had to work on.

Use of ChatGPT, Google, Stack Overflow are allowed, however you should possess full knowledge of the code and how it works. Otherwise, a lower/failing grade will be given.


## Project Specs
### Main Features:

	1. The home page where the user will be able to start the quiz.
	2. The quiz page where the user will be able to play the 10-question quiz.
	3. The results page where the user will be able to see what they scored in the quiz.

### Additionally, you will need to incorporate:
	1. A signup/login page for the user to create an account.
	2. Save users score along with other user information into MySQL database.
	3. Create a user profile page where the user can see their play history.
	4. Create a leaderboard page where users can see the top-10 players and how they rank compared to each other.

### Extra Features:
	1. A timer for the quiz or for each question.
	2. Ability to choose how many questions the quiz will have.
	3. Ability to replay/restart.

### Technologies allowed:
	1. Frontend: HTML, CSS, JS
	2. Backend: PHP, MySQL

## Quiz Questions
### Early Phase:
In the same location as this memo, you will find a zip file of question-answer zip file with a json file inside. The json includes a series of questions, answers, and correct answer. The way you choose which questions to display is up to you. But if a user plays twice, they should not be getting the same set of questions (although one or two may repeat).

## Deployment
You are required to deploy/host your app on the internet. There are many hosting services out there, but in any case do NOT enter any card/payment details, there is not need for that. These hosting services provide free tier subscriptions for small projects like ours. Examples of hosting services are: Render or Heroku. If you are feeling courageous you can try out AWS or Azure (be careful not to add a credit card, so any mistakes don't cost you $$$).

## Submission
You will submit the link of your Github account, where I will be able to see the full code and contributions. Also, you need to submit the quiz app url for me visit your hosted project. All in all, you submission will be comprised of 2 urls: GitHub and Quiz App.