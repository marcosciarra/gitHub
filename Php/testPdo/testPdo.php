<?php

var_dump($_GET);
if ($_GET['action'] == 'select') {
    try {
        $conn =
            new \PDO('mysql:host=' . '10.96.175.84'
                . ';dbname=' . 'test'
                . ';port=' . '3306'
                . ';charset=' . 'utf8', 'root', 'Sciarra82', null);
//    $result = $conn->query("SELECT * FROM test");
//    foreach($result as $app){
//        var_dump($app);
//    }

        $result = $conn->prepare("SELECT * FROM test");
        $result->execute();
//    $result = $conn->prepare("SELECT * FROM test WHERE id = :id LIMIT 1");
//    $result->execute(array(":id" => 1));

        foreach ($result->fetchAll() as $res) {
            var_dump($res);
            echo $res['cognome'] . ' - ' . $res['nome'] . '<br>';
        }

//        var_dump($result->fetchAll());
//
//        while ($riga = $result->fetch()) {
//            echo "Nome: " . $riga["nome"];
//            echo "<br>";
//            echo "Cognome: " . $riga["cognome"];
//            echo "<br>";
//            echo "<br>";
//        }

    } catch (PDOException $e) {
        $conn->rollBack();
        die("Errore durante la connessione al database!: " . $e->getMessage());
    }
}
if ($_GET['action'] == 'update') {
    $conn =
        new \PDO('mysql:host=' . '10.96.175.84'
            . ';dbname=' . 'test'
            . ';port=' . '3306'
            . ';charset=' . 'utf8', 'root', 'Sciarra82', null);
    $conn->beginTransaction();
    try {
        $elimina = $conn->exec("UPDATE test SET nome = '".$_GET['nome']."' WHERE id = 1");
        $conn->commit();
    } catch (Exception $e) {
        // Riabilito l'auto-commit
        $conn->rollBack();
        die("Errore imprevisto " . $e->getMessage());
    }
}
?>

<form name="richiamo_funzione" action="testPdo.php" method="GET">
    <input type="submit" name="action" value="select"/>
    <br>
    <br>
    <input type="text" name="nome" value=""/>
    <input type="submit" name="action" value="update"/>
</form>