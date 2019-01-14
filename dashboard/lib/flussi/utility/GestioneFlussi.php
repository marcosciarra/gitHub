<?php

namespace Click\Flussi\Utility;

/**
 * Funzioni generiche per i flussi
 *
 * @author COLOMBO Claudio
 */
class GestioneFlussi
{
    /**
     * scrittura flussi standard tipo CBI
     *
     * @param        $flusso
     * @param string $tipoScrittura
     */
    public static function scriviFlusso($flusso, $tipoScrittura = "w")
    {
        if ($flusso->getNomeFile() != "") {

            $f = fopen($flusso->getNomeFile(), $tipoScrittura);
            if (!$f) {
                echo '<br/><span style = "color:red;">IMPOSSIBILE SCRIVERE IL FILE</span>';

                return;
            }
            //scrivo il record di testa
            fwrite($f, $flusso->getRecordTesta() . "\n");

            //scrivo dettagli
            $arrayDettagli = $flusso->getDettagli();
            foreach ($arrayDettagli as $bloccoDettaglio) {
                foreach ($bloccoDettaglio as $dettaglio) {
                    if (trim($dettaglio) != "") {
                        fwrite($f, $dettaglio . "\n");
                    }
                }
            }


            //scrivo il record di coda
            fwrite($f, $flusso->getRecordCoda());
            if ($tipoScrittura != "w") {
                fwrite($f, "\n");
            }
            fclose($f);

            //echo '<br/><a href="file:' . $flusso->getNomeFile() . '">Visualizza File</div>';
        } else {
            echo '<br/><span style = "color:red;">ATTENZIONE!!!! Nome file vuoto</span>';
        }
    }

    public static function scriviFlussoF24Elide($f, $tipoScrittura = "wa")
    {
        foreach ($f as $flusso) {
            if ($flusso->getNomeFile() != "") {

                $f = fopen($flusso->getNomeFile(), $tipoScrittura);
                if (!$f) {
                    echo '<br/><span style = "color:red;">IMPOSSIBILE SCRIVERE IL FILE</span>';

                    return;
                }
                //scrivo il record di testa
                fwrite($f, $flusso->getRecordTesta() . "\n");

                //scrivo dettagli
                $arrayDettagli = $flusso->getDettagli();
                foreach ($arrayDettagli as $bloccoDettaglio) {
                    foreach ($bloccoDettaglio as $dettaglio) {
                        if (trim($dettaglio) != "") {
                            fwrite($f, $dettaglio . "\n");
                        }
                    }
                }


                //scrivo il record di coda
                fwrite($f, $flusso->getRecordCoda());
                if ($tipoScrittura != "w") {
                    fwrite($f, "\n");
                }
                fclose($f);

                //echo '<br/><a href="file:' . $flusso->getNomeFile() . '">Visualizza File</div>';
            } else {
                echo '<br/><span style = "color:red;">ATTENZIONE!!!! Nome file vuoto</span>';
            }
        }
    }

    /**
     *
     * visualizza a video il flusso tipo CBI
     *
     * @param type $flusso
     * @param type $table spessore bordo tabella, se diverso da 0 visualizza anche gli indici
     * @param       $attivo
     */
    public static function visualizzaFlusso($flusso,
                                            $table = 0,
                                            $attivo = 0)
    {

        echo '<table border="' . $table . '" style="color:black;">';
        if ($table != 0) {
            echo '<tr>';
            if ($attivo) {
                echo '<td style="width: 50px;;">CK</td>';
            }
            for ($i = 1; $i < 121; $i++) {
                echo '<th>';
                echo $i;
                echo '</th>';
            }
            echo '</tr>';
        }
        echo '<tr>';
        GestioneFlussi::ckRecordTbl($flusso->getRecordTesta(), $attivo);
        for ($i = 0; $i < strlen($flusso->getRecordTesta()); $i++) {
            echo '<td>';
            echo substr($flusso->getRecordTesta
            (),
                $i
                ,
                1);
            echo '</td>';
        }
        echo '</tr>';

        //scrivo dettagli
        $array = $flusso->getDettagli();
        foreach ($array as $bloccoDettaglio) {
            foreach ($bloccoDettaglio as $dettaglio) {
                if (trim($dettaglio) != "") {
                    echo '<tr>';
                    GestioneFlussi::ckRecordTbl($dettaglio, $attivo);
                    for ($i = 0; $i < strlen($dettaglio); $i++) {
                        echo '<td >';
                        echo substr($dettaglio
                            ,
                            $i
                            ,
                            1);
                        echo '</td>';
                    }
                    echo '</tr>';
                }
            }
        }


        echo '<tr>';
        GestioneFlussi::ckRecordTbl($flusso->getRecordCoda(), $attivo);
        for ($i = 0; $i < strlen($flusso->getRecordCoda()); $i++) {
            echo '<td>';
            echo substr($flusso->getRecordCoda
            (),
                $i
                ,
                1);
            echo '</td>';
        }
        echo '</tr>';
        echo '</table>';
    }


    public static function visualizzaFlussoF24Elide($f,
                                                    $table = 0,
                                                    $attivo = 0)
    {

        echo '<table border="' . $table . '" style="color:black;">';
        foreach ($f as $flusso) {
            if ($table != 0) {
                echo '<tr>';
                if ($attivo) {
                    echo '<td style="width: 50px;;">CK</td>';
                }
                for ($i = 1; $i < 121; $i++) {
                    echo '<th>';
                    echo $i;
                    echo '</th>';
                }
                echo '</tr>';
            }
            echo '<tr>';
            GestioneFlussi::ckRecordTbl($flusso->getRecordTesta(), $attivo);
            for ($i = 0; $i < strlen($flusso->getRecordTesta()); $i++) {
                echo '<td>';
                echo substr($flusso->getRecordTesta
                (),
                    $i
                    ,
                    1);
                echo '</td>';
            }
            echo '</tr>';

            //scrivo dettagli
            $array = $flusso->getDettagli();
            foreach ($array as $bloccoDettaglio) {
                foreach ($bloccoDettaglio as $dettaglio) {
                    if (trim($dettaglio) != "") {
                        echo '<tr>';
                        GestioneFlussi::ckRecordTbl($dettaglio, $attivo);
                        for ($i = 0; $i < strlen($dettaglio); $i++) {
                            echo '<td >';
                            echo substr($dettaglio
                                ,
                                $i
                                ,
                                1);
                            echo '</td>';
                        }
                        echo '</tr>';
                    }
                }
            }


            echo '<tr>';
            GestioneFlussi::ckRecordTbl($flusso->getRecordCoda(), $attivo);
            for ($i = 0; $i < strlen($flusso->getRecordCoda()); $i++) {
                echo '<td>';
                echo substr($flusso->getRecordCoda
                (),
                    $i
                    ,
                    1);
                echo '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }


    /**
     * Scrittura file comunicazione unica
     *
     * @param        $flusso
     * @param string $tipoScrittura
     */
    public static function scriviFlussoCu($flusso, $tipoScrittura = "w")
    {
        if ($flusso->getNomeFile() != "") {

            $f = fopen($flusso->getNomeFile(), $tipoScrittura);
            if (!$f) {
                echo '<br/><span style = "color:red;">IMPOSSIBILE SCRIVERE IL FILE</span>';

                return;
            }
            //scrivo il record di testa
            fwrite($f, $flusso->getRecordTesta() . "\r\n");

            //scrivo dettagli
            $array = $flusso->getDettagli();
            foreach ($array as $dettaglioArray) {
                $dettaglioB = $dettaglioArray['RecordB'];
                if ($dettaglioB != '') {
                    fwrite($f, $dettaglioB . "\r\n");

                    for ($j = 0; $j < count($dettaglioArray['RecordD']); $j++) {
                        fwrite($f, $dettaglioArray['RecordD'][$j] . "\r\n");
                        fwrite($f, $dettaglioArray['RecordH'][$j] . "\r\n");

                    }
                }
            }


            //scrivo il record di coda
            fwrite($f, $flusso->getRecordCoda() . "\r\n");
            fclose($f);

            //echo '<br/><a href="file:' . $flusso->getNomeFile() . '">Visualizza File</div>';
        } else {
            echo '<br/><span style = "color:red;">ATTENZIONE!!!! Nome file vuoto</span>';
        }
    }

    /**
     *
     * visualizza a video il flusso comunicazione unica
     *
     * @param type $flusso
     * @param type $table spessore bordo tabella, se diverso da 0 visualizza anche gli indici
     * @param       $attivo
     * @param int $caratteri
     */
    public static function visualizzaFlussoCu($flusso, $table = 0, $attivo = 0, $caratteri = 120)
    {

        echo '<table border="' . $table . '" style="color:black;">';
        if ($table != 0) {
            echo '<tr>';
            if ($attivo) {
                echo '<td style="width: 50px;;">CK</td>';
            }
            for ($i = 1; $i < $caratteri + 1; $i++) {
                echo '<th>';
                echo $i;
                echo '</th>';
            }
            echo '</tr>';
        }
        echo '<tr>';
        GestioneFlussi::ckRecordTbl($flusso->getRecordTesta(), $attivo);
        for ($i = 0; $i < strlen($flusso->getRecordTesta()); $i++) {
            echo '<td>';
            echo substr($flusso->getRecordTesta(), $i, 1);
            echo '</td>';
        }
        echo '</tr>';

        //scrivo dettagli
        $array = $flusso->getDettagli();
        foreach ($array as $dettaglioArray) {
            $dettaglioB = $dettaglioArray['RecordB'];
            if ($dettaglioB != '') {
                for ($i = 0; $i < strlen($dettaglioB); $i++) {
                    echo '<td >';
                    echo substr($dettaglioB, $i, 1);
                    echo '</td>';
                }

                for ($j = 0; $j < count($dettaglioArray['RecordD']); $j++) {
                    $dettaglioD = $dettaglioArray['RecordD'][$j];
                    $dettaglioH = $dettaglioArray['RecordH'][$j];
                    echo '<tr>';
                    //GestioneFlussi::ckRecordTbl($dettaglio, $attivo);

                    for ($i = 0; $i < strlen($dettaglioD); $i++) {
                        echo '<td >';
                        echo substr($dettaglioD, $i, 1);
                        echo '</td>';
                    }
                    echo '</tr>';
                    echo '<tr>';
                    for ($i = 0; $i < strlen($dettaglioH); $i++) {
                        echo '<td >';
                        echo substr($dettaglioH, $i, 1);
                        echo '</td>';
                    }
                    echo '</tr>';
                }
            }
        }

        echo '<tr>';
        GestioneFlussi::ckRecordTbl($flusso->getRecordCoda(), $attivo);
        for ($i = 0; $i < strlen($flusso->getRecordCoda()); $i++) {
            echo '<td>';
            echo substr($flusso->getRecordCoda(), $i, 1);
            echo '</td>';
        }
        echo '</tr>';
        echo '</table>';
    }

    /**
     * visualizza flussi con record di 1900 caratteri
     *
     * @param     $flusso
     * @param int $table
     * @param int $attivo
     */
    public static function visualizzaFlusso1900($flusso, $table = 0, $attivo = 0)
    {
        GestioneFlussi::visualizzaFlussoCu($flusso, $table, $attivo, 1900);
    }


    /**
     * Scrittura file Mod 770 Semplificato
     *
     * @param        $flusso
     * @param string $tipoScrittura
     */
    public static function scriviFlusso770s($flusso, $tipoScrittura = "w")
    {
        if ($flusso->getNomeFile() != "") {

            $f = fopen($flusso->getNomeFile(), $tipoScrittura);
            if (!$f) {
                echo '<br/><span style = "color:red;">IMPOSSIBILE SCRIVERE IL FILE</span>';

                return;
            }
            //scrivo il record di testa
            fwrite($f, $flusso->getRecordTesta() . "\r\n");

            //scrivo dettagli
            $array = $flusso->getDettagli();
            foreach ($array as $dettaglioArray) {
                if ($dettaglioArray['RecordB'] != '') {
                    //recod b
                    fwrite($f, $dettaglioArray['RecordB'] . "\r\n");

                    //record e
                    foreach ($dettaglioArray['RecordE'] as $e) {
                        if ($e != '') fwrite($f, $e . "\r\n");
                    }

                    //recod
                    if ($dettaglioArray['RecordF'] != '') {
                        fwrite($f, $dettaglioArray['RecordF'] . "\r\n");
                    }

                    //record h
                    foreach ($dettaglioArray['RecordH'] as $h) {
                        if ($h != '') fwrite($f, $h . "\r\n");
                    }

                    //recod j
                    fwrite($f, $dettaglioArray['RecordJ'] . "\r\n");

                }
            }

            //scrivo il record di coda
            fwrite($f, $flusso->getRecordCoda() . "\r\n");
            fclose($f);

            //echo '<br/><a href="file:' . $flusso->getNomeFile() . '">Visualizza File</div>';
        } else {
            echo '<br/><span style = "color:red;">ATTENZIONE!!!! Nome file vuoto</span>';
        }
    }

    /**
     * Scrittura file Mod 770 Semplificato 2016
     *
     * @param        $flusso
     * @param string $tipoScrittura
     */
    public static function scriviFlusso770s2016($flusso, $tipoScrittura = "w")
    {
        if ($flusso->getNomeFile() != "") {

            $f = fopen($flusso->getNomeFile() . '.tmp', $tipoScrittura);
            if (!$f) {
                echo '<br/><span style = "color:red;">IMPOSSIBILE SCRIVERE IL FILE</span>';

                return;
            }
            //scrivo il record di testa
            fwrite($f, $flusso->getRecordTesta() . "\r\n");

            //scrivo dettagli
            $array = $flusso->getDettagli();
            foreach ($array as $dettaglioArray) {
                if ($dettaglioArray['RecordB'] != '') {
                    //recod b
                    fwrite($f, $dettaglioArray['RecordB'] . "\r\n");

                    //record d
                    foreach ($dettaglioArray['RecordD'] as $d) {
                        if ($d != '') fwrite($f, $d . "\r\n");
                    }
                }
            }

            //scrivo il record di coda
            //fwrite($f, $flusso->getRecordCoda(). "\r\n");
            fwrite($f, $flusso->getRecordCoda());
            fclose($f);

            //echo '<br/><a href="file:' . $flusso->getNomeFile() . '">Visualizza File</div>';
        } else {
            echo '<br/><span style = "color:red;">ATTENZIONE!!!! Nome file vuoto</span>';
        }
    }

    public static function verificaFlusso770s2016($flusso)
    {
        $vuoto = 0;
        $f = fopen($flusso->getNomeFile() . '.tmp', "r");
        $fw = fopen($flusso->getNomeFile(), "w");
        while (!feof($f)) {
            $riga = fgets($f, 4096);
            if ($riga == '') {
                $vuoto++;
            }
            if ($vuoto > 1) break;
            fwrite($fw, $riga);
        }


        if ($vuoto == 0) {
            fwrite($fw, "\r\n");
        }

        fclose($f);
        unlink($flusso->getNomeFile() . '.tmp');
        fclose($fw);
    }

    public static function scriviFlusso770o($flusso, $tipoScrittura = "w")
    {
        self::scriviFlusso770s($flusso, $tipoScrittura);
    }

    /**
     *
     * visualizza a video il flusso Mod 770 Ordinario
     *
     * @param type $flusso
     * @param type $table spessore bordo tabella, se diverso da 0 visualizza anche gli indici
     * @param       $attivo
     * @param int $caratteri
     */
    public static function visualizzaFlusso770o($flusso, $table = 0, $attivo = 0, $caratteri = 1898)
    {
        self::visualizzaFlusso770s($flusso, $table, $attivo, $caratteri);
    }

    /**
     *
     * visualizza a video il flusso Mod 770 S
     *
     * @param type $flusso
     * @param type $table spessore bordo tabella, se diverso da 0 visualizza anche gli indici
     * @param       $attivo
     * @param int $caratteri
     */
    public static function visualizzaFlusso770s($flusso, $table = 0, $attivo = 0, $caratteri = 1898)
    {

        echo '<table border="' . $table . '" style="color:black;">';
        if ($table != 0) {
            echo '<tr>';
            if ($attivo) {
                echo '<td style="width: 50px;;">CK</td>';
            }
            for ($i = 1; $i < $caratteri + 1; $i++) {
                echo '<th>';
                echo $i;
                echo '</th>';
            }
            echo '</tr>';
        }
        echo '<tr>';
        GestioneFlussi::ckRecordTbl($flusso->getRecordTesta(), $attivo, 1898);
        for ($i = 0; $i < strlen($flusso->getRecordTesta()); $i++) {
            echo '<td>';
            echo substr($flusso->getRecordTesta(), $i, 1);
            echo '</td>';
        }
        echo '</tr>';

        //scrivo dettagli
        $array = $flusso->getDettagli();
        foreach ($array as $dettaglioArray) {
            $dettaglioB = $dettaglioArray['RecordB'];
            if ($dettaglioB != '') {
                //record B
                echo '<tr >';
                GestioneFlussi::ckRecordTbl($dettaglioB, $attivo, 1898);
                for ($i = 0; $i < strlen($dettaglioB); $i++) {
                    echo '<td >';
                    echo substr($dettaglioB, $i, 1);
                    echo '</td>';
                }
                echo '</tr >';

                //record e
                foreach ($dettaglioArray['RecordE'] as $e) {
                    echo '<tr >';
                    GestioneFlussi::ckRecordTbl($e, $attivo, 1898);
                    for ($i = 0; $i < strlen($e); $i++) {
                        echo '<td >';
                        echo substr($e, $i, 1);
                        echo '</td>';
                    }
                    echo '</tr >';

                }

                //recod f
                echo '<tr >';
                GestioneFlussi::ckRecordTbl($dettaglioArray['RecordF'], $attivo, 1898);
                for ($i = 0; $i < strlen($dettaglioArray['RecordF']); $i++) {
                    echo '<td >';
                    echo substr($dettaglioArray['RecordF'], $i, 1);
                    echo '</td>';
                }
                echo '</tr >';

                //record h
                foreach ($dettaglioArray['RecordH'] as $h) {
                    echo '<tr >';
                    GestioneFlussi::ckRecordTbl($h, $attivo, 1898);
                    for ($i = 0; $i < strlen($h); $i++) {
                        echo '<td >';
                        echo substr($h, $i, 1);
                        echo '</td>';
                    }
                    echo '</tr >';
                }

                //recod j
                echo '<tr >';
                GestioneFlussi::ckRecordTbl($dettaglioArray['RecordF'], $attivo, 1898);
                for ($i = 0; $i < strlen($dettaglioArray['RecordJ']); $i++) {
                    echo '<td >';
                    echo substr($dettaglioArray['RecordJ'], $i, 1);
                    echo '</td>';
                }
                echo '</tr >';

            }
        }

        echo '<tr>';
        GestioneFlussi::ckRecordTbl($flusso->getRecordCoda(), $attivo, 1898);
        for ($i = 0; $i < strlen($flusso->getRecordCoda()); $i++) {
            echo '<td>';
            echo substr($flusso->getRecordCoda(), $i, 1);
            echo '</td>';
        }
        echo '</tr>';
        echo '</table>';
    }

    /**
     *
     * visualizza a video il flusso Mod 770 S
     *
     * @param type $flusso
     * @param type $table spessore bordo tabella, se diverso da 0 visualizza anche gli indici
     * @param       $attivo
     * @param int $caratteri
     */
    public static function visualizzaFlusso770s2016($flusso, $table = 0, $attivo = 0, $caratteri = 1898)
    {

        echo '<table border="' . $table . '" style="color:black;">';
        if ($table != 0) {
            echo '<tr>';
            if ($attivo) {
                echo '<td style="width: 50px;;">CK</td>';
            }
            for ($i = 1; $i < $caratteri + 1; $i++) {
                echo '<th>';
                echo $i;
                echo '</th>';
            }
            echo '</tr>';
        }
        echo '<tr>';
        GestioneFlussi::ckRecordTbl($flusso->getRecordTesta(), $attivo, 1898);
        for ($i = 0; $i < strlen($flusso->getRecordTesta()); $i++) {
            echo '<td>';
            echo substr($flusso->getRecordTesta(), $i, 1);
            echo '</td>';
        }
        echo '</tr>';

        //scrivo dettagli
        $array = $flusso->getDettagli();
        foreach ($array as $dettaglioArray) {
            $dettaglioB = $dettaglioArray['RecordB'];
            if ($dettaglioB != '') {
                //record B
                echo '<tr >';
                GestioneFlussi::ckRecordTbl($dettaglioB, $attivo, 1898);
                for ($i = 0; $i < strlen($dettaglioB); $i++) {
                    echo '<td >';
                    echo substr($dettaglioB, $i, 1);
                    echo '</td>';
                }
                echo '</tr >';

                //record e
                foreach ($dettaglioArray['RecordD'] as $e) {
                    echo '<tr >';
                    GestioneFlussi::ckRecordTbl($e, $attivo, 1898);
                    for ($i = 0; $i < strlen($e); $i++) {
                        echo '<td >';
                        echo substr($e, $i, 1);
                        echo '</td>';
                    }
                    echo '</tr >';

                }
            }
        }

        echo '<tr>';
        GestioneFlussi::ckRecordTbl($flusso->getRecordCoda(), $attivo, 1898);
        for ($i = 0; $i < strlen($flusso->getRecordCoda()); $i++) {
            echo '<td>';
            echo substr($flusso->getRecordCoda(), $i, 1);
            echo '</td>';
        }
        echo '</tr>';
        echo '</table>';
    }

    /**
     * @param $record
     * @param $attivo
     */
    static function ckRecordTbl($record, $attivo, $len = 120)
    {
        if ($attivo) {
            $l = strlen($record);
            if ($l == $len) {
                echo '<td>';
                echo '<span style="color:green;">';
                echo "OK</span>";
                echo '</td>';
            } else {
                echo '<td>';
                echo '<span style="color:red;">';
                echo "$l</span>";
                echo '</td>';
            }
        }
    }

    /**
     * @param $descrizione
     * @param $record
     * @param $attivo
     */
    static function ckRecord($descrizione,
                             $record,
                             $attivo)
    {
        if ($attivo) {
            $l = strlen($record);
            if ($l == 120) {
                echo '<span style="color:green;">';
                echo "$descrizione :  OK<br/>";
            } else {
                echo '<span style = "color:red;">';
                echo "$descrizione : $l<br/>";
            }
            echo '</div>';
        }
    }

    /**
     * @param $dettaglio
     *
     * @return string
     */
    static function dettaglioTrim($dettaglio)
    {
        $dettaglioTrim = '';
        if (is_array($dettaglio)) {
            if (isset($dettaglio[1])) {
                $dettaglioTrim = trim($dettaglio[1]);
            } else {
                if (isset($dettaglio[1])) {
                    $dettaglioTrim = trim($dettaglio[0]);
                } else {
                    $dettaglioTrim = '';
                }
            }
        } else {
            $dettaglioTrim = trim($dettaglio);
        }

        return $dettaglioTrim;
    }


    public static function scriviFlussoDetrazioni($flusso, $tipoScrittura = "w")
    {
        if ($flusso->getNomeFile() != "") {

            $f = fopen($flusso->getNomeFile(), $tipoScrittura);
            if (!$f) {
                echo '<br/><span style = "color:red;">IMPOSSIBILE SCRIVERE IL FILE</span>';

                return;
            }
            //scrivo il record di testa
            fwrite($f, $flusso->getTesta() . "\r\n");

            //scrivo dettagli
            foreach ($flusso->getDettagli() as $dettaglio) {
                fwrite($f, $dettaglio . "\r\n");
            }

            //scrivo il record di coda
            fwrite($f, $flusso->getCoda() . "\r\n");
            fclose($f);

            //echo '<br/><a href="file:' . $flusso->getNomeFile() . '">Visualizza File</div>';
        } else {
            echo '<br/><span style = "color:red;">ATTENZIONE!!!! Nome file vuoto</span>';
        }
    }


    public static function scriviStreamingFlussoDetrazioni($flusso)
    {
        $f = $flusso->getTesta() . "\r\n";

        //scrivo dettagli
        foreach ($flusso->getDettagli() as $dettaglio) {
            $f .= $dettaglio . "\r\n";
        }

        //scrivo il record di coda
        $f .= $flusso->getCoda() . "\r\n";

        return $f;
    }
}