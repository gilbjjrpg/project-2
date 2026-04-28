CREATE DATABADE quizberry;
USE quizberry;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    email, VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    is_guest BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_type VARCHAR(50) NOT NULL,
    score INT NOT NULL,
    date_taken DATE NOT NULL, 
    FOREIGN KEY (user_id) REFERENCES users(id)
);

INSERT INTO users(username, name, email, password, is_guest)
VALUES
('josh722', 'Josh', 'josh722@gmail.com', 'test123', FALSE),
('amy1287', 'Amy', 'josh722@gmail.com', 'hello456', FALSE),
('mike1202', 'Mike', 'mike1202@gmail.com', 'quiz7890', FALSE),
('buglover072', 'John', 'bugsbugsbugs@gmail.com', 'iLoveBugs123', FALSE),
('dinodan983', 'Daniel', 'danidino983@gmail.com', 'gottabeTuff90!', FALSE);

INSERT INTO scores (user_id, quiz_type, score, date_taken)
VALUES
(1, '10 Question', 80, '2026-04-10'),
(1, 'Custom', 70, '2026-04-12'),
(2, '10 Question', 80, '2026-04-10'),
(4, '10 Question', 90, '2026-04-11'),
(4, 'Custom', 70, '2026-04-12'),
(4, 'Custom', 60, '2026-04-20'),
(5, '10 Question', 20, '2026-04-12'),
(5, 'Custom', 100, '2026-04-13');