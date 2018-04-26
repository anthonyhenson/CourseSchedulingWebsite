<?php
    $dsn = 'mysql:host=127.0.0.1;dbname=CBUScheduler';
    //'mysql:host=127.0.0.1;dbname=CBUScheduler';
    $usernameDB = getenv('C9_USER');
    $passwordDB = '';
    try {
        $this->db = new PDO($dsn, $usernameDB, $passwordDB);
    } catch (PDOException $e) {
            $errorMessage = $e->getMessage();
            echo "<p>An error occured while connecting to the database: $errorMessage </p>";
            exit();
    }
?>