<?php

header("Content-Type: text/plain");

echo "Hello World! This is a simple PHP demo application using MySQL (Clear DB Addon) deployed on Heroku" . PHP_EOL;

$mysql_url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$database_host = $mysql_url["host"];
$database_name = substr($mysql_url["path"], 1);
$database_username = $mysql_url["user"];
$database_password = $mysql_url["pass"];

// see DSN string formats: http://php.net/manual/en/ref.pdo-mysql.connection.php
try {

    // create a new database handle
    $dbh = new PDO(
        "mysql:host=$database_host;dbname=$database_name;charset=utf8", 
        $database_username, 
        $database_password
    );

    // make PDO throw exceptions
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Database is Connected!" . PHP_EOL;

} catch(PDOException $e) {

    die("{$e->getCode()} Connection failed: {$e->getMessage()}" . PHP_EOL);

}

try {
    
    $results = $dbh->query("SHOW VARIABLES LIKE 'version'");
    $row = $results->fetch(PDO::FETCH_ASSOC);

    echo "MySQL Version: {$row['Value']}" . PHP_EOL;

} catch (PDOException $e) {

    die("{$e->getCode()} Version query failed: {$e->getMessage()}" . PHP_EOL);

}

// let's try out our database

try {

    // create a table using utf8 character set and utf8_unicode_ci collation
    $dbh->exec("
        CREATE TABLE IF NOT EXISTS blog 
        (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            content TEXT
        ) 
        ENGINE=InnoDB, 
        DEFAULT CHARACTER SET utf8, 
        DEFAULT COLLATE utf8_unicode_ci
    ");

    $dbh->exec("
        INSERT IGNORE INTO blog (id, content) VALUES (1, 'hello world!')
    ");

    $dbh->exec("
        INSERT IGNORE INTO blog (id, content) VALUES (2, 'this is some content!')
    ");

} catch (PDOException $e) {

    die("{$e->getCode()} Table `blog` creation or insertion failed: {$e->getMessage()}" . PHP_EOL);

}

try {

    $statement = $dbh->prepare("
        SELECT * FROM blog WHERE id = :id1 OR id = :id2
    ");

    $statement->execute([':id1' => 1, ':id2' => 2]);

    $results = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach($results as $row) {
        echo "Content from Blog: {$row['id']} => {$row['content']}" . PHP_EOL;
    }

} catch (PDOException $e) {

    die("{$e->getCode()} Table `blog` querying failed: {$e->getMessage()}" . PHP_EOL);

}

echo "Done!" . PHP_EOL;