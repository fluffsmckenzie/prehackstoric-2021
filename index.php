<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Which dino can you outlive?</title>
    <link href="css/main.css" type="text/css" rel="stylesheet">
</head>
<body>
<?php
if(isset($_GET['year'])){
    $birthYear = $_GET['year'];
    $birthMonth = $_GET['month'];
    $birthDay = $_GET['day'];

    $inputBirthdate = $birthYear.'-'.$birthMonth.'-'.$birthDay;
}

function keepDefault($i,$size){
    if(isset($_GET[$size])){
        if($_GET[$size] == $i){
            return ' selected';
        }
    }
}
?>
<header><h2>Which dino can you outlive?</h2></header>
<main>
<form name="open_file" action="" method="get" class="birthdayForm">
     <select name="year">
         <option value="">Year</option>
        <?php
        for($i=2021;$i>=1910;$i--){
            echo "<option value=\"{$i}\"".keepDefault($i,'year').">{$i}</option>";
        }
        ?>
    </select>
    <select name="month">
        <option value="">Month</option>
        <?php
        for($i=1;$i<=12;$i++){
            echo "<option value=\"{$i}\"".keepDefault($i,'month').">{$i}</option>";
        }
        ?>
    </select>
    <select name="day">
        <option value="">Day</option>
        <?php
        for($i=1;$i<=31;$i++){
            echo "<option value=\"{$i}\"".keepDefault($i,'day').">{$i}</option>";
        }
        ?>
    </select>
    <button type="submit" name="submit">Get Dino</button>
</form>

<?php
if(isset($_GET['year'])) {
    $currentDate = date("Y-m-d");
    $date = new DateTime($currentDate);//date("Y/m/d");
    $birthdate = new DateTime($inputBirthdate);
    $age = $birthdate->diff($date);

    echo "You are " . $age->y . " years, " . $age->m . " months, and " . $age->d . " days old. ";

    try {
        $dbh = new PDO('pgsql:host=localhost;port=26257;dbname=dinodb;sslmode=require;sslrootcert=certs/ca.crt;sslkey=certs/client.dinodbuser.key;sslcert=certs/client.dinodbuser.crt',
            'dinodbuser', null, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => true,
                PDO::ATTR_PERSISTENT => true
            ));


        foreach ($dbh->query('SELECT dinosaur, lifespan FROM dinoinfo WHERE lifespan < ' . $age->y . ' ORDER BY RANDOM() LIMIT 1') as $row) {
            print "<h3>You could outlive a ".ucwords($row['dinosaur']) . ' (Average age: ' . $row['lifespan'] . " years)</h3><br><img src=\"images/".trim($row['dinosaur']).".jpg\" height=\"400px\" />";
        }
    } catch (Exception $e) {
        print $e->getMessage() . "\r\n";
        exit(1);
    }
}
?>
</main>
</body>
</html>