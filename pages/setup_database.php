<?php
    //Connect to the configured database and load the shared setup functions.
    include '../data/database.php';

    //Empty score rows first because scores depend on users.
    $db->exec("DELETE FROM scores");
    $db->exec("DELETE FROM users");

    //Reset auto-increment counters for whichever database driver is active.
    if ($dbDriver === "mysql") {
        $db->exec("ALTER TABLE scores AUTO_INCREMENT = 1");
        $db->exec("ALTER TABLE users AUTO_INCREMENT = 1");
        } else {
            $db->exec("DELETE FROM sqlite_sequence WHERE name IN ('scores', 'users')");
    }

    //Load the starter/demo users and scores back into the database.
    seedStarterData($db);

    echo "Database setup complete.";
?>
