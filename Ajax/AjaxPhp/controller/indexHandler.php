<?php
$nomi[0] = "Alessandro";
$nomi[1] = "Alessio";
$nomi[2] = "Claudio";
$nomi[3] = "Davide";
$nomi[4] = "Dario";
$nomi[5] = "Francesco";
$nomi[6] = "Giancarlo";
$nomi[7] = "Luca";
$nomi[8] = "Luigi";

$nome = $_GET["nome"];

if (strlen($nome) > 0)
{
    $risultato = "";
    for ($i = 0; $i < count($nomi); $i++)
    {
        if (strtoupper($nome) == strtoupper(substr($nomi[$i], 0, strlen($nome))))
        {
            if ($risultato == "")
            {
                $risultato = $nomi[$i];
            }
            else
            {
                $risultato .= ", " . $nomi[$i];
            }
        }
    } 
} 


if ($risultato == "")
{
    echo "Nessun risultato...";
}
else
{
    echo $risultato;
}
?>