<?php
    $dsn = 'mysql:host=127.0.0.1;dbname=CBUScheduler';
    //'mysql:host=127.0.0.1;dbname=CBUScheduler';
    $usernameDB = getenv('C9_USER');
    $passwordDB = '';
    //$optionsDB = array(PDO::ATTR_ERRMODE => PDO::EXCEPTION); <- where do we put that? //just leave this //ok
    //okay at the top of SQLDataHandler i wrote what methods are good to go so you can use them
    //okay th output is pretty easy with the toString methods now. i have examples as well in test-run
    //let me check those real quick
    //right now we are just trying to get csm to run
    //this is the error it gives> Fatal error: Using $this when not in object context in /home/ubuntu/workspace/database.php on line 10
    //ill look at yours what line in main??
    //we were trying to figure out the running part of the CSM its line 132 -34 and it gives that error......
    try {
        $this->db = new PDO($dsn, $usernameDB, $passwordDB);
    } catch (PDOException $e) {
            $errorMessage = $e->getMessage();
            echo "<p>An error occured while connecting to the database: $errorMessage </p>";
            exit();
    }
    
?>