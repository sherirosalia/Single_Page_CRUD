



<?php

try {
	
	
	$user = 'foo';
	$pass = 'foo'; 

	$dbh = new PDO('mysql:host=localhost;dbname=foo_database', $user, $pass);

	//echo 'database connected';
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

