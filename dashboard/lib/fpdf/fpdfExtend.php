<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 27/02/18
 * Time: 12.43
 */

require_once "../../lib/fpdf/fpdf.php";
require_once '../../src/model/DrakkarDbConnector.php';
require_once "../../src/model/Anagrafiche.php";
require_once "../../src/model/Opzioni.php";

use Click\Affitti\TblBase\Anagrafiche;
use Click\Affitti\TblBase\Opzioni;
use Drakkar\DrakkarDbConnector;

class fpdfExtend extends FPDF
{

    const BIANCO = array(255, 255, 255);
    const NERO = array(0, 0, 0);
    const ROSSO_CHIARO = array(255, 229, 229);
    const ROSSO_SCURO = array(255, 51, 51);
    const GRIGIO_MOLTO_CHIARO = array(238, 238, 238);
    const GRIGIO_CHIARO = array(218, 218, 218);
    const GRIGIO_SCURO = array(167, 167, 167);


    /** @var DrakkarDbConnector */
    protected $con;

    /** @var string */
    protected $orientamento;

    /** @var bool */
    protected $stampaHeader;

    /** @var bool */
    protected $stampaFooter;

    /** @var int */
    protected $numeroPagina;

    /** @var int */
    protected $numeroPaginaTotale;

    /** @var string */
    protected $dataFooter;

    /** @var string */
    protected $size;

    /** @var string */
    protected $valuta_euro;

    /** @var Opzioni */
    protected $opt;

    /**
     * fpdfExtend constructor.
     */
    public function __construct($orientation = 'P', $size = 'A4', $stampaHeader = true, $stampaFooter = true, $unit = 'mm')
    {
        $this->con = new DrakkarDbConnector();
        parent::__construct($orientation, $unit, $size);
        $this->stampaHeader = $stampaHeader;
        $this->stampaFooter = $stampaFooter;
        $this->orientamento = $orientation;
        $this->size = $size;
        $this->valorizzaTabellaConfigurazioni();
        $this->valuta_euro = ' ' . chr(128);
        $this->aCapo = ' ' . chr(10);
        $this->opt = new Opzioni($this->con);
    }

    function AddPage($orientation = '', $size = '', $rotation = 0)
    {
        $size = ($size == '') ? $this->size : $size;
        $orientation = ($orientation == '') ? $this->orientamento : $orientation;
        parent::AddPage($orientation, $size, $rotation);
    }


    protected function getMese($mese)
    {
        $mesi = [
            'Gennaio',
            'Febbraio',
            'Marzo',
            'Aprile',
            'Maggio',
            'Giugno',
            'Luglio',
            'Agosto',
            'Settembre',
            'Ottobre',
            'Novembre',
            'Dicembre'
        ];
        return $mesi[$mese - 1];
    }

    function replaceSpecialChar($word)
    {

        $word = str_replace("@", "%40", $word);
        $word = str_replace("`", "%60", $word);
        $word = str_replace("¢", "%A2", $word);
        $word = str_replace("£", "%A3", $word);
        $word = str_replace("¥", "%A5", $word);
        $word = str_replace("|", "%A6", $word);
        $word = str_replace("«", "%AB", $word);
        $word = str_replace("¬", "%AC", $word);
        $word = str_replace("¯", "%AD", $word);
        $word = str_replace("º", "%B0", $word);
        $word = str_replace("±", "%B1", $word);
        $word = str_replace("ª", "%B2", $word);
        $word = str_replace("µ", "%B5", $word);
        $word = str_replace("»", "%BB", $word);
        $word = str_replace("¼", "%BC", $word);
        $word = str_replace("½", "%BD", $word);
        $word = str_replace("¿", "%BF", $word);
        $word = str_replace("À", "%C0", $word);
        $word = str_replace("Á", "%C1", $word);
        $word = str_replace("Â", "%C2", $word);
        $word = str_replace("Ã", "%C3", $word);
        $word = str_replace("Ä", "%C4", $word);
        $word = str_replace("Å", "%C5", $word);
        $word = str_replace("Æ", "%C6", $word);
        $word = str_replace("Ç", "%C7", $word);
        $word = str_replace("È", "%C8", $word);
        $word = str_replace("É", "%C9", $word);
        $word = str_replace("Ê", "%CA", $word);
        $word = str_replace("Ë", "%CB", $word);
        $word = str_replace("Ì", "%CC", $word);
        $word = str_replace("Í", "%CD", $word);
        $word = str_replace("Î", "%CE", $word);
        $word = str_replace("Ï", "%CF", $word);
        $word = str_replace("Ð", "%D0", $word);
        $word = str_replace("Ñ", "%D1", $word);
        $word = str_replace("Ò", "%D2", $word);
        $word = str_replace("Ó", "%D3", $word);
        $word = str_replace("Ô", "%D4", $word);
        $word = str_replace("Õ", "%D5", $word);
        $word = str_replace("Ö", "%D6", $word);
        $word = str_replace("Ø", "%D8", $word);
        $word = str_replace("Ù", "%D9", $word);
        $word = str_replace("Ú", "%DA", $word);
        $word = str_replace("Û", "%DB", $word);
        $word = str_replace("Ü", "%DC", $word);
        $word = str_replace("Ý", "%DD", $word);
        $word = str_replace("Þ", "%DE", $word);
        $word = str_replace("ß", "%DF", $word);
        $word = str_replace("à", "%E0", $word);
        $word = str_replace("á", "%E1", $word);
        $word = str_replace("â", "%E2", $word);
        $word = str_replace("ã", "%E3", $word);
        $word = str_replace("ä", "%E4", $word);
        $word = str_replace("å", "%E5", $word);
        $word = str_replace("æ", "%E6", $word);
        $word = str_replace("ç", "%E7", $word);
        $word = str_replace("è", "%E8", $word);
        $word = str_replace("é", "%E9", $word);
        $word = str_replace("ê", "%EA", $word);
        $word = str_replace("ë", "%EB", $word);
        $word = str_replace("ì", "%EC", $word);
        $word = str_replace("í", "%ED", $word);
        $word = str_replace("î", "%EE", $word);
        $word = str_replace("ï", "%EF", $word);
        $word = str_replace("ð", "%F0", $word);
        $word = str_replace("ñ", "%F1", $word);
        $word = str_replace("ò", "%F2", $word);
        $word = str_replace("ó", "%F3", $word);
        $word = str_replace("ô", "%F4", $word);
        $word = str_replace("õ", "%F5", $word);
        $word = str_replace("ö", "%F6", $word);
        $word = str_replace("÷", "%F7", $word);
        $word = str_replace("ø", "%F8", $word);
        $word = str_replace("ù", "%F9", $word);
        $word = str_replace("ú", "%FA", $word);
        $word = str_replace("û", "%FB", $word);
        $word = str_replace("ü", "%FC", $word);
        $word = str_replace("ý", "%FD", $word);
        $word = str_replace("þ", "%FE", $word);
        $word = str_replace("ÿ", "%FF", $word);
        return urldecode($word);
    }


    /**
     *   NON STAMPA NULLA,SEMPLICEMENTE EFFETTUA IL PROCEDIMENTO IDENTICO ALLA MULTICELL
     *   PER CONTARE QUANTE RIGHE STAMPEREBBE UN DETERMINATO TESTO IN UNA CELLA DI UNA DETERMINATA LUNGHEZZA
     *
     * @param float $w
     *                      largezza di ciascuna riga della cella</br>
     * @param string $txt
     *                      testo che dovrebbe essere stampato</br>
     * @return int
     *                      spazio che occuperebbe in millimetri il testo appena passato</br>
     */
    function RigheMultiCell($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $b = 0;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;
        $conta = 1;
        while ($i < $nb) {
            // Get next character
            $c = $s[$i];
            if ($c == "\n") {
                $conta++;
                // Explicit line break
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
                $ls = $l;
                $ns++;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                // Automatic line break
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $conta++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
            } else {
                $i++;
            }
        }

        return $conta;
    }


    /**
     * @param Anagrafiche $agenzia
     */
    public function headerAgenzia($agenzia, $intestazione = true, $logo = true)
    {
        if ($this->stampaHeader) {
            $this->opt->findByPk(3);
            if ($logo == true && $this->opt->getValore() == 1) {
                if (strlen($agenzia->getFileLogo()) > 0) {
                    if (is_file('../../upload/' . $agenzia->getFileLogo())) {
                        $this->Image('../../upload/' . $agenzia->getFileLogo(), null, null, null, 20);
                    }
                }
            }

            $this->opt->findByPk(2);
            if ($intestazione == true && $this->opt->getValore() == 1) {
                $this->SetTextColor(self::NERO[0], self::NERO[1], self::NERO[2]);
                $this->SetDrawColor(self::GRIGIO_CHIARO[0], self::GRIGIO_CHIARO[1], self::GRIGIO_CHIARO[2]);
                $this->SetFont('Arial', '', 10);

                if ($this->orientamento == 'P') {
                    $this->SetXY(90, 8);
                    // Titolo in riquadro
                    $this->MultiCell(110, 22, '', 1, 'L');
                    $this->Text(92, 12, $agenzia->getRagioneSociale());

                    $appIndirizzo = json_decode($agenzia->getIndirizzi())[0];
                    $indirizzo =
                        $appIndirizzo->via . ' ' .
                        $appIndirizzo->civico;
                    $citta =
                        $appIndirizzo->citta . ' ' .
                        $appIndirizzo->cap . ' ';
                    if (strlen($appIndirizzo->provincia) > 0)
                        $citta .=
                            '(' . $appIndirizzo->provincia . ')';
                    if (strlen($appIndirizzo->frazione) > 0) {
                        $citta .= ' Fraz. ' . $appIndirizzo->frazione;
                    }

                    $this->Text(92, 16, $indirizzo);
                    if (strlen($citta) > 0)
                        $this->Text(92, 20, $citta);

                    $appTelefono = json_decode($agenzia->getTelefoni())[0];
                    $appCellulare = json_decode($agenzia->getCellulari())[0];
                    $appEmail = json_decode($agenzia->getEmail())[0];

                    if (isset($appEmail->email) && strlen($appEmail->email) > 0)
                        $this->Text(92, 24, 'e-Mail : ' . $appEmail->email);

                    if (isset($appTelefono->telefono) && strlen($appTelefono->telefono) > 0)
                        $this->Text(92, 28, 'Telefono : ' . $appTelefono->telefono);

                    if (isset($appCellulare->cellulare) && strlen($appCellulare->cellulare) > 0)
                        $this->Text(150, 28, 'Cellulare : ' . $appCellulare->cellulare);

                } else {
                    $this->SetXY(170, 8);
                    // Titolo in riquadro
                    $this->MultiCell(110, 22, '', 1, 'L');
                    $this->Text(172, 12, $agenzia->getRagioneSociale());

                    $appIndirizzo = json_decode($agenzia->getIndirizzi())[0];
                    $indirizzo =
                        $appIndirizzo->via . ' ' .
                        $appIndirizzo->civico;
                    $citta =
                        $appIndirizzo->citta . ' ' .
                        $appIndirizzo->cap . ' ';
                    if (strlen($appIndirizzo->provincia) > 0)
                        $citta .=
                            '(' . $appIndirizzo->provincia . ')';
                    if (strlen($appIndirizzo->frazione) > 0) {
                        $citta .= ' Fraz. ' . $appIndirizzo->frazione;
                    }

                    $this->Text(172, 16, $indirizzo);
                    if (strlen($citta) > 0)
                        $this->Text(172, 20, $citta);

                    $appTelefono = json_decode($agenzia->getTelefoni())[0];
                    $appCellulare = json_decode($agenzia->getCellulari())[0];
                    $appEmail = json_decode($agenzia->getEmail())[0];

                    if (isset($appEmail->email) && strlen($appEmail->email) > 0)
                        $this->Text(172, 24, 'e-Mail : ' . $appEmail->email);
                    if (isset($appCellulare->cellulare) && strlen($appCellulare->cellulare) > 0)
                        $this->Text(230, 28, 'Cellulare : ' . $appCellulare->cellulare);
                    if (isset($appTelefono->telefono) && strlen($appTelefono->telefono) > 0)
                        $this->Text(172, 28, 'Telefono : ' . $appTelefono->telefono);
                }
            }
            // Interruzione di linea
            $this->Ln(5);
        }
    }


    public function footerAgenzia()
    {
        if (!$this->stampaFooter) return;

        if ($this->dataFooter == null) {
            $this->dataFooter =
                date('d') . ' ' .
                $this->getMese(date('m')) . ' ' .
                date('Y');
        }

        if ($this->numeroPagina == null)
            $this->numeroPagina = $this->PageNo();

        if ($this->numeroPaginaTotale == null)
            $this->numeroPaginaTotale = $this->numeroPagina;

        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 8);

        // Stampa il numero di pagina centrato
        if ($this->orientamento == 'P') {
            $this->Cell(60, 8, $this->dataFooter, 0, 0, 'L');
            $this->Cell(60, 8, 'Click Affitti - CLICK SRL', 0, 0, 'C');
            $this->Cell(70, 8, 'Pagina ' . $this->numeroPagina . ' / ' . $this->numeroPaginaTotale, 0, 0, 'R');
        } else {
            $this->Cell(95, 8, $this->dataFooter, 0, 0, 'L');
            $this->Cell(95, 8, 'Click Affitti - CLICK SRL', 0, 0, 'C');
            $this->Cell(80, 8, 'Pagina ' . $this->numeroPagina . ' / ' . $this->numeroPaginaTotale, 0, 0, 'R');
        }
    }


    public function rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1)
            $x = $this->x;
        if ($y == -1)
            $y = $this->y;
        if ($this->angle != 0)
            $this->_out('Q');
        $this->angle = $angle;
        if ($angle != 0) {
            $angle *= M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }


    //Cell with horizontal scaling if text is too wide
    function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=true)
    {
        //Get string width
        $str_width=$this->GetStringWidth($txt);

        //Calculate ratio to fit cell
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $ratio = ($w-$this->cMargin*2)/$str_width;

        $fit = ($ratio < 1 || ($ratio > 1 && $force));
        if ($fit)
        {
            if ($scale)
            {
                //Calculate horizontal scaling
                $horiz_scale=$ratio*100.0;
                //Set horizontal scaling
                $this->_out(sprintf('BT %.2F Tz ET',$horiz_scale));
            }
            else
            {
                //Calculate character spacing in points
                $char_space=($w-$this->cMargin*2-$str_width)/max($this->MBGetStringLength($txt)-1,1)*$this->k;
                //Set character spacing
                $this->_out(sprintf('BT %.2F Tc ET',$char_space));
            }
            //Override user alignment (since text will fill up cell)
            $align='';
        }

        //Pass on to Cell method
        $this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);

        //Reset character spacing/horizontal scaling
        if ($fit)
            $this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
    }

    // Da chiamare ad inizio pagina
    public function filigrana($testo)
    {
        $this->SetFont('Arial', 'B', 50);
        $this->SetTextColor(203, 203, 203);
        $this->rotate(45, 55, 190);
        $this->Text(55, 190, $testo);
        $this->rotate(0);
        $this->SetTextColor(0, 0, 0);
    }

    public function box($x, $y, $hTitolo, $hTesto, $l,
                        $titolo, $testo, $borderTitolo, $borderTesto, $coloreBordoTitolo, $coloreBordoTabella,
                        $coloreTitolo, $sfondoTitolo, $coloreTabella, $sfondoTabella,
                        $sizeFontTitolo, $sizeFontTesto, $font = 'Arial')
    {

        //TITOLO
        if ($hTitolo > 0) {
            $this->SetFont($font, 'B', $sizeFontTitolo);
            $this->SetXY($x, $y);
            $this->SetTextColor($coloreTitolo[0], $coloreTitolo[1], $coloreTitolo[2]);
            $this->SetFillColor($sfondoTitolo[0], $sfondoTitolo[1], $sfondoTitolo[2]);
            $this->SetDrawColor($coloreBordoTitolo[0], $coloreBordoTitolo[1], $coloreBordoTitolo[2]);
            $this->Cell($l, $hTitolo, $titolo, $borderTitolo, 0, '', true);
        }

        //TESTO
        $this->SetXY($x, $y + 5);
        $this->SetTextColor($coloreTabella[0], $coloreTabella[1], $coloreTabella[2]);
        $this->SetFillColor($sfondoTabella[0], $sfondoTabella[1], $sfondoTabella[2]);
        $this->SetDrawColor($coloreBordoTabella[0], $coloreBordoTabella[1], $coloreBordoTabella[2]);
        $this->Cell($l, $hTesto, '', $borderTesto, 1, '', true);
        $this->SetFont($font, '', $sizeFontTesto);
        $this->SetXY($x + 1, $y + 6);
        foreach ($testo as $t) {
            $this->SetX($x + 1);
            $this->CellFit($l - 4, 4, $t, 0, 1, '', true,'',true,false);
        }
    }


    ////////////////////////////////////////////////////
    /// Gestione Tabelle
    ////////////////////////////////////////////////////

    const TABELLA_SPAZIATURA = 5;

    protected $tabellaConfigurazioni;

    protected function valorizzaTabellaConfigurazioni()
    {
        $this->tabellaConfigurazioni = json_encode(["font" => ["family" => "Arial"],
                "coloreAlternato" => ["attivo" => true,
                    "coloreRiga" => self::GRIGIO_CHIARO],
                "testata" => ["attivo" => true,
                    "sfondo" => self::GRIGIO_SCURO,
                    "colore" => self::NERO,
                    "bordo" => true,
                    "bordoColore" => self::BIANCO,
                    "sizeRow" => 3,
                    "sizeFont" => 8],
                "corpo" => ["attivo" => true,
                    "sfondo" => self::BIANCO,
                    "colore" => self::NERO,
                    "bordo" => true,
                    "bordoColore" => self::BIANCO,
                    "sizeRow" => 0,
                    "sizeFont" => 8,
                    "interlinea" => 0],
                "piede" => ["attivo" => true,
                    "sfondo" => self::BIANCO,
                    "colore" => self::NERO,
                    "bordo" => true,
                    "bordoColore" => self::BIANCO,
                    "sizeRow" => 0,
                    "sizeFont" => 8]
            ]
        );
        $this->tabellaConfigurazioni = json_decode($this->tabellaConfigurazioni);
    }


    public function addTabellaHeaderElement($label, $colonna, $larghezza = 50, $allineamentoValore = 'L', $allineamentoTitolo = 'L')
    {
        return ['label' => $label,
            'colonna' => $colonna,
            'larghezza' => $larghezza,
            'allineamentoValore' => $allineamentoValore,
            'allineamentoTitolo' => $allineamentoTitolo];

    }


    public function addTabellaFooterElement($valore, $larghezza = 50, $allineamento = 'R')
    {
        return [
            'valore' => $valore,
            'larghezza' => $larghezza,
            'allineamento' => $allineamento
        ];
    }

    public function setTabellaConfigurazioni($key, $key2 = null, $value = null)
    {
        if (is_null($value))
            $this->tabellaConfigurazioni->$key = $key2;
        elseif (is_null($key2))
            $this->tabellaConfigurazioni = $key;
        else
            $this->tabellaConfigurazioni->$key->$key2 = $value;
    }

    public function setTabellaConfigurazioniFont($key, $value = null)
    {
        if (is_null($value))
            $this->tabellaConfigurazioni->font = $key;
        else
            $this->tabellaConfigurazioni->font->$key = $value;
    }

    public function setTabellaConfigurazioniColoreAlternato($key, $value = null)
    {
        if (is_null($value))
            $this->tabellaConfigurazioni->coloreAlternato = $key;
        else
            $this->tabellaConfigurazioni->coloreAlternato->$key = $value;
    }

    public function boxSingolo($x, $y, $width, $titolo, $righe)
    {
        $this->SetXY($x, $y);
        $this->Cell($width, 4, $titolo);
        $y += 4;
        foreach ($righe as $r) {
            $this->SetXY($x, $y);
            $this->Cell($width, 4, $r);
            $y += 4;
        }
    }

    public function testo($x, $y, $width, $height, $testo)
    {
        $this->SetXY($x, $y);
        $this->MultiCell($width, $height, $testo, 0, 'L');
        //MultiCell 0, 10, "", 1, L
    }

    public function creaIndirizzo($indirizzo)
    {
        $destinatario = [];
        $destinatario[] = $indirizzo->getTitolo();
        $destinatario[] = $indirizzo->getNominativo();

        foreach (json_decode($indirizzo->getIndirizzi()) as $indirizzo) {
            if ($indirizzo->presso != "") {
                $destinatario[] = "C/O " . $indirizzo->presso;
            }

            if ($indirizzo->indirizzo_spedizione) {
                $destinatario[] = $indirizzo->via . ", " . $indirizzo->civico;
                if ($indirizzo->frazione != "") {
                    $destinatario[] = "frazione: " . $indirizzo->frazione;
                }
                $destinatario[] = $indirizzo->cap . " " . $indirizzo->citta . " (" . $indirizzo->provincia . ")";
            }
        }
        return $destinatario;
    }

    public function creaIndirizzoDaJSON($indirizzo)
    {
        $destinatario = [];
        $destinatario[] = $indirizzo->titolo;
        $destinatario[] = $indirizzo->destinatario;

        if ($indirizzo->presso != "") {
            $destinatario[] = "C/O " . $indirizzo->presso;
        }

        if ($indirizzo->indirizzo_spedizione) {
            $destinatario[] = $indirizzo->via . ", " . $indirizzo->civico;
            if ($indirizzo->frazione != "") {
                $destinatario[] = "frazione: " . $indirizzo->frazione;
            }
            $destinatario[] = $indirizzo->cap . " " . $indirizzo->citta . " (" . $indirizzo->provincia . ")";
        }
        return $destinatario;
    }

    public function tabella($x, $y, $bodyObj, $tableHeaderObj, $tableFooterObj)
    {
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //TESTATA TABELLA
        $tableHeaderObj = json_decode(json_encode($tableHeaderObj));
        if ($this->tabellaConfigurazioni->testata->attivo) {

            //configurazioni grafiche
            $this->SetFont($this->tabellaConfigurazioni->font->family, 'B', $this->tabellaConfigurazioni->testata->sizeFont);
            $this->SetXY($x, $y);
            $this->setGraficaTabella('testata');
            $xApp = $x;

            //dati
            foreach ($tableHeaderObj as $headerObj) {
                $this->SetX($xApp);
                $this->Cell($headerObj->larghezza,
                    $this->tabellaConfigurazioni->testata->sizeRow + self::TABELLA_SPAZIATURA,
                    $headerObj->label,
                    $this->tabellaConfigurazioni->testata->bordo,
                    0,
                    $headerObj->allineamentoTitolo,
                    true
                );
                $xApp += $headerObj->larghezza;
            }
        }
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ///CORPO TABELLA

        //configurazioni grafiche

        //Calcolo larghezza totale riga tabella
        $totRiga = 0;
        foreach ($tableHeaderObj as $r) {
            $totRiga = $totRiga + $r->larghezza;
        }

        $this->SetFont($this->tabellaConfigurazioni->font->family, '', $this->tabellaConfigurazioni->corpo->sizeFont);
        $y += $this->tabellaConfigurazioni->testata->sizeRow +
            self::TABELLA_SPAZIATURA;
        $this->SetXY($x, $y);
        $this->setGraficaTabella('corpo');

        //dati

        $this->Cell($totRiga,
            count($tableHeaderObj),
            '',
            $this->tabellaConfigurazioni->corpo->bordo,
            1,
            '',
            true
        );
        $yApp = $y;

        $ckAlternato = true;
        foreach ($bodyObj as $rowBody) {

            //testo alternato

            if ($this->tabellaConfigurazioni->coloreAlternato->attivo) {
                if ($ckAlternato) {
                    $this->SetFillColor($this->tabellaConfigurazioni->corpo->sfondo[0],
                        $this->tabellaConfigurazioni->corpo->sfondo[1],
                        $this->tabellaConfigurazioni->corpo->sfondo[2]
                    );
                } else {
                    $this->SetFillColor($this->tabellaConfigurazioni->coloreAlternato->coloreRiga[0],
                        $this->tabellaConfigurazioni->coloreAlternato->coloreRiga[1],
                        $this->tabellaConfigurazioni->coloreAlternato->coloreRiga[2]
                    );
                }
                $ckAlternato = !$ckAlternato;
            }

            $xApp = $x;
            $this->SetXY($xApp, $yApp);
            foreach ($tableHeaderObj as $headerObj) {

                $content = $this->createContentAbstract($headerObj->colonna, $rowBody);

                //Creo la cella
                $this->SetX($xApp);
                $this->Cell($headerObj->larghezza,
                    $this->tabellaConfigurazioni->corpo->sizeRow +
                    $this->tabellaConfigurazioni->corpo->interlinea + self::TABELLA_SPAZIATURA,
                    $content,
                    $this->tabellaConfigurazioni->corpo->bordo,
                    0,
                    $headerObj->allineamentoValore,
                    true
                );

                $xApp += $headerObj->larghezza;

            }
            $yApp += $this->tabellaConfigurazioni->corpo->sizeRow + $this->tabellaConfigurazioni->corpo->interlinea +
                self::TABELLA_SPAZIATURA;
        }


        ////////////////////////////////////////////////////////////////////////////////////////////////////////////

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////
        ///PIE TABELLA
        $tableFooterObj = json_decode(json_encode($tableFooterObj));
        if (!is_null($tableFooterObj)) {
            $y = $this->GetY() + $this->tabellaConfigurazioni->corpo->sizeRow +
                $this->tabellaConfigurazioni->corpo->interlinea + self::TABELLA_SPAZIATURA;
            if ($this->tabellaConfigurazioni->piede->attivo) {
                //configurazioni grafiche
                $this->SetFont($this->tabellaConfigurazioni->font->family, 'B', $this->tabellaConfigurazioni->piede->sizeFont);
                $this->SetXY($x, $y);
                $this->setGraficaTabella('piede');
                $xApp = $x;


                //dati
                if ($tableFooterObj != "") {
                    foreach ($tableFooterObj as $piede) {

                        $content = $this->createContentAbstract($piede->valore);

                        $this->SetX($xApp);
                        $this->Cell($piede->larghezza,
                            $this->tabellaConfigurazioni->piede->sizeRow + self::TABELLA_SPAZIATURA,
                            $content,
                            $this->tabellaConfigurazioni->piede->bordo,
                            0,
                            $piede->allineamento,
                            true
                        );
                        $xApp += $piede->larghezza;
                    }
                }
            }
        }

    }


    protected function setGraficaTabella($blocco)
    {
        $this->SetTextColor($this->tabellaConfigurazioni->$blocco->colore[0],
            $this->tabellaConfigurazioni->$blocco->colore[1],
            $this->tabellaConfigurazioni->$blocco->colore[2]
        );
        $this->SetFillColor($this->tabellaConfigurazioni->$blocco->sfondo[0],
            $this->tabellaConfigurazioni->$blocco->sfondo[1],
            $this->tabellaConfigurazioni->$blocco->sfondo[2]
        );
        if ($this->tabellaConfigurazioni->piede->bordo)
            $this->SetDrawColor($this->tabellaConfigurazioni->$blocco->bordoColore[0],
                $this->tabellaConfigurazioni->$blocco->bordoColore[1],
                $this->tabellaConfigurazioni->$blocco->bordoColore[2]
            );
    }


//    public function tabellaOld(
//        $x, $y, $hEtichetta, $hRiga, $hTotale, $hTotaleRighe,
//        $datiEtichetta, $allineamentoEtichetta, $larghezzaEtichetta,
//        $coloreEtichetta, $coloreSfondoEtichetta, $borderEtichetta, $coloreBordoEtichetta,
//        $datiRiga, $allineamentoRiga, $larghezzaRiga,
//        $coloreTestoRiga, $coloreSfondoRiga, $coloreRiga, $borderRiga, $coloreBordoRiga,
//        $datiTotale, $allineamentoTotale, $larghezzaTotale,
//        $coloreTotale, $coloreSfondoTotale, $borderTotale, $coloreBordoTotale,
//        $sizeFontEtichetta, $sizeFontRiga, $sizeFontTotale,
//        $interlinea, $font = 'Arial', $coloreAlternato = 'false'
//    )
//    {
//        //Etichetta
//        if ($hEtichetta > 0) {
//            $this->SetFont($font, 'B', $sizeFontEtichetta);
//            $this->SetXY($x, $y);
//            $this->SetTextColor($coloreEtichetta[0], $coloreEtichetta[1], $coloreEtichetta[2]);
//            $this->SetFillColor($coloreSfondoEtichetta[0], $coloreSfondoEtichetta[1], $coloreSfondoEtichetta[2]);
//            $this->SetDrawColor($coloreBordoEtichetta[0], $coloreBordoEtichetta[1], $coloreBordoEtichetta[2]);
//            $xApp = $x;
//            for ($i = 0; $i < count($datiEtichetta); $i++) {
//                $this->SetX($xApp);
//                $this->Cell($larghezzaEtichetta[$i], $hEtichetta, $datiEtichetta[$i], $borderEtichetta, 0, $allineamentoEtichetta[$i], true);
//                $xApp = $xApp + $larghezzaEtichetta[$i];
//            }
//        }
//
//        //RIGHE
//        $totRiga = 0;
//        foreach ($larghezzaRiga as $r) {
//            $totRiga = $totRiga + $r;
//        }
//        $this->SetFont($font, '', $sizeFontRiga);
//        $y = $y + $hEtichetta + 0.2;
//        $this->SetXY($x, $y);
//        $this->SetTextColor($coloreTestoRiga[0], $coloreTestoRiga[1], $coloreTestoRiga[2]);
//        $this->SetFillColor($coloreSfondoRiga[0], $coloreSfondoRiga[1], $coloreSfondoRiga[2]);
//        $this->SetDrawColor($coloreBordoRiga[0], $coloreBordoRiga[1], $coloreBordoRiga[2]);
//        $this->Cell($totRiga, $hTotaleRighe, '', $borderRiga, 1, '', true);
//        $yApp = $y;
//        for ($i = 0; $i < count($datiRiga); $i++) {
//            $xApp = $x;
//            $this->SetXY($xApp, $yApp);
//            for ($j = 0; $j < count($datiRiga[$i]); $j++) {
//                if ($coloreAlternato == false) {
//                    $this->SetFillColor($coloreRiga[0], $coloreRiga[1], $coloreRiga[2]);
//                } else {
//                    if ($i % 2 == 0) {
//                        $this->SetFillColor($coloreRiga[0], $coloreRiga[1], $coloreRiga[2]);
//                    } else {
//                        $this->SetFillColor($coloreSfondoRiga[0], $coloreSfondoRiga[1], $coloreSfondoRiga[2]);
//                    }
//                }
//                $this->SetX($xApp);
//                $this->Cell($larghezzaRiga[$j], $hRiga, $datiRiga[$i][$j], $borderRiga, 0, $allineamentoRiga[$j], true);
//                $xApp = $xApp + $larghezzaRiga[$j];
//            }
//            $yApp = $yApp + $interlinea;
//        }
//
//        //TOTALI
//        $y = $this->GetY() + $hRiga;
//        if ($hTotale > 0) {
//            $this->SetFont($font, 'B', $sizeFontTotale);
//            $this->SetXY($x, $y);
//            $this->SetTextColor($coloreTotale[0], $coloreTotale[1], $coloreTotale[2]);
//            $this->SetFillColor($coloreSfondoTotale[0], $coloreSfondoTotale[1], $coloreSfondoTotale[2]);
//            $this->SetDrawColor($coloreBordoTotale[0], $coloreBordoTotale[1], $coloreBordoTotale[2]);
//            $xApp = $x;
//            for ($i = 0; $i < count($datiTotale); $i++) {
//                $this->SetX($xApp);
//                $this->Cell($larghezzaTotale[$i], $hTotale, $datiTotale[$i], $borderTotale, 0, $allineamentoTotale[$i], true);
//                $xApp = $xApp + $larghezzaTotale[$i];
//            }
//        }
//    }

    /**
     * @param mixed $numeroPagina
     */
    public function setNumeroPagina($numeroPagina)
    {
        $this->numeroPagina = $numeroPagina;
    }

    /**
     * @param int $numeroPaginaTotale
     */
    public function setNumeroPaginaTotale($numeroPaginaTotale)
    {
        $this->numeroPaginaTotale = $numeroPaginaTotale;
    }

    /**
     * @param string $dataFooter
     */
    public function setDataFooter($dataFooter)
    {
        $this->dataFooter = $dataFooter;
    }

    /**
     * @param string $orientamento
     */
    public function setOrientamento($orientamento)
    {
        $this->orientamento = $orientamento;
    }

    /**
     * @param bool $stampaFooter
     */
    public function setStampaFooter($stampaFooter)
    {
        $this->stampaFooter = $stampaFooter;
    }

    /**
     * @param bool $stampaHeader
     */
    public function setStampaHeader($stampaHeader)
    {
        $this->stampaHeader = $stampaHeader;
    }


    ////////////////////////////////////////////////////////
    protected function createContentAbstract($obj, $row = null)
    {

        $content = '';
        foreach (explode('+', $obj) as $c) {


            switch (trim($c)) {
                case 'EURO':
                    $content .= ' ' . chr(128);
                    break;
                case 'PERC':
                    $content .= ' %';
                    break;
                default:
                    if (substr($c, 0, 3) == 'get') {
                        $result = $row;
                        $metodo = explode('->', trim($c));
                        foreach ($metodo as $m) {
                            $m = explode('(', $m);
                            $p = str_replace(')', '', $m[1]);
                            if ($p == '') {
                                $m = $m[0];
                                $result = $result->$m();
                            } else {
                                $p = explode(',', $p);
                                $result = call_user_func_array(array($result, $m[0]), $p);
                            }
                        }
                    } else {
                        $result = ' ' . trim($c);
                    }

                    $content .= $result;
            }
        }
        return $content;
    }


}