<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 18/06/18
 * Time: 11.45
 */

namespace Click\Flussi;


use Click\Affitti\TblBase\AggiornamentoF24;
use Click\Affitti\TblBase\ImpostaRegistro;
use Click\Affitti\TblBase\PdaAbstractModel;
use Click\Affitti\TblBase\Rli;

use Click\Flussi\Cbi\F24\GeneraRecordFlussiF24;
use Click\Flussi\Cbi\F24\FlussoF24Elide;
use Click\Flussi\Cbi\F24\RecordF4;
use Click\Flussi\Cbi\F24\Record10;
use Click\Flussi\Cbi\F24\Record20;
use Click\Flussi\Cbi\F24\Record40_17;
use Click\Flussi\Cbi\F24\Record40_18;
use Click\Flussi\Cbi\F24\Record50_01;
use Click\Flussi\Cbi\F24\Record50_02;
use Click\Flussi\Cbi\F24\RecordEF;

use Click\Flussi\Utility\StringUtility;
use Click\Flussi\Utility\GestioneFlussi;


class CreaF24Elide extends PdaAbstractModel
{

    protected $conn;

    /** @var FlussoF24Elide[] */
    protected $flussi;

    protected $impRegPerAbi;

    protected $dataPagamento;

    protected $nomeFile;

    /**
     * CreaF24Elide constructor.
     */
    public function __construct($con, $arrayId, $dataPagamento, $flagTipoFlusso = 'cbi')
    {
        $this->conn = $con;
        $this->dataPagamento = $dataPagamento;
        $this->impRegPerAbi = [];
        $this->nomeFile = 'F24' . date('Ymdhs');

        $this->caricaDati($arrayId);

//        TODO :: DA IMPLEMENTARE QUANDO SI INSERIRA' IL FLUSSO ENTRATEL
//        switch ($flagTipoFlusso){
//            case 'cbi':
//                break;
//            case 'entratel':
//                break;
//            default:
//                return 'errore';
//        }

//        $this->generaFlusso();

    }


    public function generaFlusso()
    {
        $count = 0;
        foreach ($this->impRegPerAbi as $impRegAbi) {

            $count++;

            $flusso = new FlussoF24Elide();
            $nomeSupporto = $this->nomeFile . $impRegAbi[0]['abi'];
            //------------------------------------------------F4------------------------------------------------------//
            $f4 = new RecordF4();
            $f4->crea(
                $impRegAbi[0]['sia'],
                $impRegAbi[0]['abi'],
                date('dmy'),
                $nomeSupporto,
                $impRegAbi[0]['abi']
            );
            foreach ($impRegAbi as $impReg) {
                $aggF24 = new AggiornamentoF24($this->conn);
                /** @var AggiornamentoF24 $aggF24 */
                foreach ($aggF24->findByIdImpostaRegistro($impReg['imposta_registro_id']) as $aggF24){
                    break;
                }
                $aggF24->setCodiceFlusso($this->nomeFile . $impRegAbi[0]['abi']);
                $aggF24->saveOrUpdate();
                //------------------------------------------------10------------------------------------------------------//
                $r10 = new Record10();

                if (strlen($impReg['codice_fiscale']) == 16) {
                    $r10->creaPersonaFisica(
                        $count,
                        $impReg['codice_fiscale'],
                        $impReg['cognome'],
                        $impReg['nome'],
                        $impReg['sesso'],
                        $impReg['nascita_luogo'],
                        $impReg['nascita_provincia'],
                        $impReg['nascita_data'],
                        $count
                    );
                } else {
                    $r10->creaSocieta(
                        $count,
                        $impReg['partita_iva'],
                        $impReg['ragione_sociale'],
                        $count
                    );
                }

                //------------------------------------------------20------------------------------------------------------//
                //Indirizzo di domicilio fiscale
                $indirizzo = json_decode($impReg['indirizzi']);

                foreach ($indirizzo as $anagrafica) {
                    if ($anagrafica->domicilio_fiscale == true) {
                        break;
                    }
                }

                // Anno Imposta
                if (substr($impReg['imposta_registro_data_scadenza'], 0, 4) == substr($this->dataPagamento, 0, 4)) {
                    $annoImposta = 0;
                } else {
                    $annoImposta = 1;
                }

                $r20 = new Record20();
                $r20->crea(
                    $count,
                    $anagrafica->citta,
                    $anagrafica->provincia,
                    $anagrafica->via . ' ' . $anagrafica->civico,
                    $this->dataPagamento,
                    $annoImposta,
                    $impReg['codice_fiscale_coobbligato'],
                    63
                );

                //------------------------------------------------40_17---------------------------------------------------//
                $dettagli = json_decode($impReg['imposta_registro_dettagli']);
                $arrayR40_17 = [];
                for ($i = 0; $i < count($dettagli); $i++) {
                    $r40_17 = new Record40_17();
                    $r40_17->setProgressivoDelegaF24($count);
                    $r40_17->setProgressivoTributo($i + 1);
                    $r40_17->setTipo($dettagli[$i]->tipo);
                    $r40_17->setElementiIdentificativi($dettagli[$i]->elementi_identificativi);
                    $r40_17->setCodice($dettagli[$i]->codice);
                    $r40_17->setAnnoRiferimento($dettagli[$i]->anno);
                    $r40_17->setImportoDebitoVersato($dettagli[$i]->importi);
                    $r40_17->setCodiceUfficio($impReg['imposta_registro_codice_ufficio']);
                    $r40_17->setCodiceAtto('');
                    $arrayR40_17[] = $r40_17;
                }

                //------------------------------------------------40_18---------------------------------------------------//
                $r40_18 = new Record40_18();
                $r40_18->setProgressivoDelegaF24($count);
                $r40_18->setSegnoSaldo('P');
                $r40_18->setSaldoSezione($impReg['imposta_registro_importo_totale']);

                //------------------------------------------------50_01---------------------------------------------------//
                $r50_01 = new Record50_01();
                $r50_01->crea(
                    $count,
                    $impReg['abi'],
                    $impReg['cab'],
                    $impReg['conto'],
                    $impReg['cin'],
                    $impReg['imposta_registro_importo_totale'],
                    0,
                    $impReg['codice_fiscale_cc'],
                    2,
                    $this->dataPagamento,
                    0,
                    $impReg['nazione'],
                    $impReg['ckdigit']
                );


                //------------------------------------------------50_02---------------------------------------------------//
                $r50_02 = new Record50_02();
                $r50_02->setProgressivoDelegaF24($count);
                $r50_02->setCodiceMittente($impReg['codice_fiscale_cc']);
                $r50_02->setAbi($impReg['abi']);
                $r50_02->setCab($impReg['cab']);
                $r50_02->setDestinatarioStampa(1);

                $flusso->addDettaglio($r10, $r20, $arrayR40_17, $r40_18, $r50_01, $r50_02);
            }
            //------------------------------------------------EF------------------------------------------------------//
            $ef = new RecordEF();
            $ef->creaCodaDaTesta($f4);
            //TODO::Settare i contatori
            $ef->setNumeroDisposizioni(1);
            $ef->setNumeroRecord(2 + 5 + count($arrayR40_17));
            $ef->setImportiPositivi($impRegAbi[0]['imposta_registro_importo_totale']);
            $ef->setImportiNegativi(0);

            $flusso->creaFlussoConCoda($f4, $ef, UPLOAD_URL . DIRECTORY_SEPARATOR . $f4->getNomeSupporto() . '.cbi');

            $this->flussi[] = $flusso;
        }

        return $this->nomeFile;
    }


    public function scriviFlusso()
    {
        GestioneFlussi::scriviFlussoF24Elide($this->flussi);
    }

    public function visalizzaFlusso()
    {
        GestioneFlussi::visualizzaFlussoF24Elide($this->flussi, 1);
    }


    protected function caricaDati($arrayId)
    {
        $impReg = new ImpostaRegistro($this->conn);
        $this->impRegPerAbi = [];
        foreach ($impReg->getElencoAbiById($arrayId) as $elencoAbi) {
            $this->impRegPerAbi[] = $this->getElencoImposteByAbi($elencoAbi['abi'], $arrayId);
        }
    }


    public function getElencoImposteByAbi($abi, $ids)
    {
        $query =
            '
            SELECT 
                ' . ImpostaRegistro::getListColumnsStatic() . ',
                ' . Rli::getListColumnsStatic() . ',
                contribuente.codice_fiscale,
                contribuente.partita_iva,
                contribuente.nome,
                contribuente.cognome,
                contribuente.ragione_sociale,
                contribuente.sesso,
                contribuente.nascita_luogo,
                contribuente.nascita_data,
                contribuente.nascita_provincia,
                contribuente.indirizzi,
                coobbligato.codice_fiscale as codice_fiscale_coobbligato,
                conti_correnti.codice_fiscale as codice_fiscale_cc,
                conti_correnti.abi,
                conti_correnti.cab,
                conti_correnti.conto,
                conti_correnti.sia,
                conti_correnti.cin,
                conti_correnti.nazione,
                conti_correnti.ckdigit,
                conti_correnti.codice_fiscale as conti_correnti_codice_fiscale
            FROM
                imposta_registro
                INNER JOIN rli ON imposta_registro.id_rli=rli.id
                INNER JOIN conti_correnti ON rli.id_conto_corrente=conti_correnti.id
                INNER JOIN anagrafiche contribuente ON rli.id_contribuente=contribuente.id
                INNER JOIN anagrafiche coobbligato ON rli.id_contribuente=coobbligato.id
                where conti_correnti.abi=? AND imposta_registro.id IN (' . implode(',', $ids) . ')
            ';
//        if ($this->whereBase) $query .= " WHERE $this->whereBase";
//        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        return $this->createResultArray($query, array($abi), self::FETCH_KEYARRAY);
    }


    public function toArrayAssoc()
    {
        // TODO: Implement toArrayAssoc() method.
    }

    public function createObjKeyArray(array $keyArray)
    {
        // TODO: Implement createObjKeyArray() method.
    }

    public function createKeyArrayFromPositional($positionalArray)
    {
        // TODO: Implement createKeyArrayFromPositional() method.
    }

    public function getListColumns()
    {
        // TODO: Implement getListColumns() method.
    }

    public function createTable()
    {
        // TODO: Implement createTable() method.
    }

    public function getEmptyDbKeyArray()
    {
        // TODO: Implement getEmptyDbKeyArray() method.
    }


}