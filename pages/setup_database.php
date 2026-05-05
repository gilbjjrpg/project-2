<?php
    /* 
    This file is pretty much obsolete at this point, but I'm keeping it here as to be careful not to 
    break anything in the app.

    Now, this file is used to manually reset the database. It deletes all rows and columns and reloads
    the data from users.json. This file WAS part of the app flow at one point to set up the database, 
    but that now happens automatically when the app connects. 

    This page isn't accessible by any link—you have to enter it yourself in the URL. You should ONLY run 
    this page IF data fails to load on the leaderboards/scores page or you want to intentionally reset the 
    data from all tables, but BE WARNED THAT IT WILL REPLACE ALL DATA WITH THE SEEDED DATA FROM USERS.JSON!!! 

    Otherwise, LEAVE AS IS!
    */ 
    
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
