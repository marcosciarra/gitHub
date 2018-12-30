<?php
/**
 * Created by Drakkar vers. 0.1.3(Hjortspring)
 * User: P.D.A. Srl
 * Date: 2018-11-15
 * Time: 11:42:47.412578
 */

namespace Click\Affitti\TblBase;
require_once 'PdaAbstractModel.php';

use Click\Affitti\TblBase\PdaAbstractModel;

/**
 * @property string nomeTabella
 * @property string tableName
 */
class ContrattiModel extends PdaAbstractModel
{
    /** @var integer */
    protected $id;
    /** @var integer */
    protected $idTipoContratto;
    /** @var integer */
    protected $idAgenziaImmobiliare;
    /** @var string */
    protected $codiceContratto;
    /** @var string (enum) N = NON ASSOGGETTO AD IVA<br/>S = ASSOGGETTO AD IVA */
    protected $tipoAssoggettazione = 'N';
    /** @var string */
    protected $descrizione;
    /** @var string */
    protected $dataStipula;
    /** @var string */
    protected $luogoStipula;
    /** @var string */
    protected $dataInizio;
    /** @var objext (string) */
    protected $proprietari;
    /** @var objext (string) */
    protected $conduttori;
    /** @var objext (string) */
    protected $garanti;
    /** @var string (enum) C=Contratto<br/>S=Spesa<br/>CS=Contratto Spesa */
    protected $tipoGestione;
    /** @var integer */
    protected $mesiPreavvisoLocatore;
    /** @var integer */
    protected $mesiPreavvisoConduttore;
    /** @var string (enum) A=Anticipata<br/>P=Posticipata */
    protected $tipoPagamentoRata = 'A';
    /** @var integer */
    protected $cestino = 0;
    /** @var string */
    protected $ultimaModificaData;
    /** @var integer */
    protected $ultimaModificaUtente;
    /** @var integer */
    protected $elaborato = 0;
    /** @var string */
    protected $disdettaInData;
    /** @var string (enum) A = ATTIVO<br/>D =  DISDETTATO */
    protected $statoContratto = 'A';
    /** @var integer */
    protected $idUtenteRiferimento;
    /** @var integer */
    protected $occupazioneSenzaTitolo = 0;

    function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->nomeTabella = 'contratti';
        $this->tableName = 'contratti';
    }

    /**
     * find by tables' Primary Key:
     * @return Contratti|array|string|null
     */
    public function findByPk($id, $typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT * FROM $this->tableName USE INDEX(PRIMARY) WHERE id=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResult($query, array($id), $typeResult);
    }

    /**
     * delete by tables' Primary Key:
     */
    public function deleteByPk($id)
    {
        $query = "DELETE FROM $this->tableName  WHERE id=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($id));
    }

    /**
     * Find all record of table
     * @return Contratti[]|array|string
     */
    public function findAll($distinct = false, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1)
    {
        $distinctStr = ($distinct) ? 'DISTINCT' : '';
        $query = "SELECT $distinctStr * FROM $this->tableName ";
        if ($this->whereBase) $query .= " WHERE $this->whereBase";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        $query .= $this->createLimitQuery($limit, $offset);
        return $this->createResultArray($query, null, $typeResult);
    }

    /**
     * find by tables' Key idx_id_tipo_contratto:
     * @return Contratti[]|array|string
     */
    public function findByIdxIdTipoContratto($idTipoContratto, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1)
    {
        $query = "SELECT * FROM $this->tableName USE INDEX(idx_id_tipo_contratto) WHERE id_tipo_contratto=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultArray($query, array($idTipoContratto), $typeResult);
    }

    /**
     * find by tables' Key fk_contratti_anagrafiche1_idx:
     * @return Contratti[]|array|string
     */
    public function findByFkContrattiAnagrafiche1Idx($idAgenziaImmobiliare, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1)
    {
        $query = "SELECT * FROM $this->tableName USE INDEX(fk_contratti_anagrafiche1_idx) WHERE id_agenzia_immobiliare=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultArray($query, array($idAgenziaImmobiliare), $typeResult);
    }

    /**
     * find by tables' Key fk_contratti_login1_idx:
     * @return Contratti[]|array|string
     */
    public function findByFkContrattiLogin1Idx($ultimaModificaUtente, $typeResult = self::FETCH_OBJ, $limit = -1, $offset = -1)
    {
        $query = "SELECT * FROM $this->tableName USE INDEX(fk_contratti_login1_idx) WHERE ultima_modifica_utente=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultArray($query, array($ultimaModificaUtente), $typeResult);
    }

    /**
     * delete by tables' Key idx_id_tipo_contratto:
     * @return boolean
     */
    public function deleteByIdxIdTipoContratto($idTipoContratto, $typeResult = self::FETCH_OBJ)
    {
        $query = "DELETE FROM $this->tableName WHERE id_tipo_contratto=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($idTipoContratto));
    }

    /**
     * delete by tables' Key fk_contratti_anagrafiche1_idx:
     * @return boolean
     */
    public function deleteByFkContrattiAnagrafiche1Idx($idAgenziaImmobiliare, $typeResult = self::FETCH_OBJ)
    {
        $query = "DELETE FROM $this->tableName WHERE id_agenzia_immobiliare=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($idAgenziaImmobiliare));
    }

    /**
     * delete by tables' Key fk_contratti_login1_idx:
     * @return boolean
     */
    public function deleteByFkContrattiLogin1Idx($ultimaModificaUtente, $typeResult = self::FETCH_OBJ)
    {
        $query = "DELETE FROM $this->tableName WHERE ultima_modifica_utente=? ";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($ultimaModificaUtente));
    }

    /**
     * find by id
     * @return Contratti[]
     */
    public function findById($id, $typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT * FROM $this->tableName WHERE id=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        return $this->createResultArray($query, array($id), $typeResult);
    }


    /**
     * find by id_tipo_contratto
     * @return Contratti[]
     */
    public function findByIdTipoContratto($idTipoContratto, $typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT * FROM $this->tableName WHERE id_tipo_contratto=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        return $this->createResultArray($query, array($idTipoContratto), $typeResult);
    }


    /**
     * find by id_agenzia_immobiliare
     * @return Contratti[]
     */
    public function findByIdAgenziaImmobiliare($idAgenziaImmobiliare, $typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT * FROM $this->tableName WHERE id_agenzia_immobiliare=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        return $this->createResultArray($query, array($idAgenziaImmobiliare), $typeResult);
    }


    /**
     * find by ultima_modifica_utente
     * @return Contratti[]
     */
    public function findByUltimaModificaUtente($ultimaModificaUtente, $typeResult = self::FETCH_OBJ)
    {
        $query = "SELECT * FROM $this->tableName WHERE ultima_modifica_utente=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        if ($this->orderBase) $query .= " ORDER BY $this->orderBase";
        return $this->createResultArray($query, array($ultimaModificaUtente), $typeResult);
    }


    /**
     * delete by id_tipo_contratto
     * @return boolean
     */
    public function deleteByIdTipoContratto($idTipoContratto)
    {
        $query = "DELETE FROM $this->tableName WHERE id_tipo_contratto=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($idTipoContratto));
    }

    /**
     * delete by id_agenzia_immobiliare
     * @return boolean
     */
    public function deleteByIdAgenziaImmobiliare($idAgenziaImmobiliare)
    {
        $query = "DELETE FROM $this->tableName WHERE id_agenzia_immobiliare=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($idAgenziaImmobiliare));
    }

    /**
     * delete by ultima_modifica_utente
     * @return boolean
     */
    public function deleteByUltimaModificaUtente($ultimaModificaUtente)
    {
        $query = "DELETE FROM $this->tableName WHERE ultima_modifica_utente=?";
        if ($this->whereBase) $query .= " AND $this->whereBase";
        return $this->createResultValue($query, array($ultimaModificaUtente));
    }

    /**
     * Transforms the object into a key array
     * @return array
     */
    public function toArrayAssoc()
    {
        $arrayValue = array();
        if (isset($this->id)) $arrayValue['id'] = $this->id;
        if (isset($this->idTipoContratto)) $arrayValue['id_tipo_contratto'] = $this->idTipoContratto;
        if (isset($this->idAgenziaImmobiliare)) $arrayValue['id_agenzia_immobiliare'] = $this->idAgenziaImmobiliare;
        if (isset($this->codiceContratto)) $arrayValue['codice_contratto'] = ($this->codiceContratto == self::NULL_VALUE) ? null : $this->codiceContratto;
        if (isset($this->tipoAssoggettazione)) $arrayValue['tipo_assoggettazione'] = ($this->tipoAssoggettazione == self::NULL_VALUE) ? null : $this->tipoAssoggettazione;
        if (isset($this->descrizione)) $arrayValue['descrizione'] = ($this->descrizione == self::NULL_VALUE) ? null : $this->descrizione;
        if (isset($this->dataStipula)) $arrayValue['data_stipula'] = ($this->dataStipula == self::NULL_VALUE) ? null : $this->dataStipula;
        if (isset($this->luogoStipula)) $arrayValue['luogo_stipula'] = ($this->luogoStipula == self::NULL_VALUE) ? null : $this->luogoStipula;
        if (isset($this->dataInizio)) $arrayValue['data_inizio'] = ($this->dataInizio == self::NULL_VALUE) ? null : $this->dataInizio;
        if (isset($this->proprietari)) $arrayValue['proprietari'] = $this->jsonEncode($this->proprietari);
        if (isset($this->conduttori)) $arrayValue['conduttori'] = $this->jsonEncode($this->conduttori);
        if (isset($this->garanti)) $arrayValue['garanti'] = $this->jsonEncode($this->garanti);
        if (isset($this->tipoGestione)) $arrayValue['tipo_gestione'] = ($this->tipoGestione == self::NULL_VALUE) ? null : $this->tipoGestione;
        if (isset($this->mesiPreavvisoLocatore)) $arrayValue['mesi_preavviso_locatore'] = ($this->mesiPreavvisoLocatore == self::NULL_VALUE) ? null : $this->mesiPreavvisoLocatore;
        if (isset($this->mesiPreavvisoConduttore)) $arrayValue['mesi_preavviso_conduttore'] = ($this->mesiPreavvisoConduttore == self::NULL_VALUE) ? null : $this->mesiPreavvisoConduttore;
        if (isset($this->tipoPagamentoRata)) $arrayValue['tipo_pagamento_rata'] = ($this->tipoPagamentoRata == self::NULL_VALUE) ? null : $this->tipoPagamentoRata;
        if (isset($this->cestino)) $arrayValue['cestino'] = $this->cestino;
        if (isset($this->ultimaModificaData)) $arrayValue['ultima_modifica_data'] = ($this->ultimaModificaData == self::NULL_VALUE) ? null : $this->ultimaModificaData;
        if (isset($this->ultimaModificaUtente)) $arrayValue['ultima_modifica_utente'] = $this->ultimaModificaUtente;
        if (isset($this->elaborato)) $arrayValue['elaborato'] = $this->elaborato;
        if (isset($this->disdettaInData)) $arrayValue['disdetta_in_data'] = ($this->disdettaInData == self::NULL_VALUE) ? null : $this->disdettaInData;
        if (isset($this->statoContratto)) $arrayValue['stato_contratto'] = ($this->statoContratto == self::NULL_VALUE) ? null : $this->statoContratto;
        if (isset($this->idUtenteRiferimento)) $arrayValue['id_utente_riferimento'] = $this->idUtenteRiferimento;
        if (isset($this->occupazioneSenzaTitolo)) $arrayValue['occupazione_senza_titolo'] = $this->occupazioneSenzaTitolo;
        return $arrayValue;
    }

    /**
     * It transforms the keyarray in an $positionalArray[%s]object
     */
    public function createObjKeyArray(array $keyArray)
    {
        $this->flagObjectDataValorized = false;
        if ((isset($keyArray['id'])) || (isset($keyArray['contratti_id']))) {
            $this->setId(isset($keyArray['id']) ? $keyArray['id'] : $keyArray['contratti_id']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['id_tipo_contratto'])) || (isset($keyArray['contratti_id_tipo_contratto']))) {
            $this->setIdtipocontratto(isset($keyArray['id_tipo_contratto']) ? $keyArray['id_tipo_contratto'] : $keyArray['contratti_id_tipo_contratto']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['id_agenzia_immobiliare'])) || (isset($keyArray['contratti_id_agenzia_immobiliare']))) {
            $this->setIdagenziaimmobiliare(isset($keyArray['id_agenzia_immobiliare']) ? $keyArray['id_agenzia_immobiliare'] : $keyArray['contratti_id_agenzia_immobiliare']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['codice_contratto'])) || (isset($keyArray['contratti_codice_contratto']))) {
            $this->setCodicecontratto(isset($keyArray['codice_contratto']) ? $keyArray['codice_contratto'] : $keyArray['contratti_codice_contratto']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['tipo_assoggettazione'])) || (isset($keyArray['contratti_tipo_assoggettazione']))) {
            $this->setTipoassoggettazione(isset($keyArray['tipo_assoggettazione']) ? $keyArray['tipo_assoggettazione'] : $keyArray['contratti_tipo_assoggettazione']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['descrizione'])) || (isset($keyArray['contratti_descrizione']))) {
            $this->setDescrizione(isset($keyArray['descrizione']) ? $keyArray['descrizione'] : $keyArray['contratti_descrizione']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['data_stipula'])) || (isset($keyArray['contratti_data_stipula']))) {
            $this->setDatastipula(isset($keyArray['data_stipula']) ? $keyArray['data_stipula'] : $keyArray['contratti_data_stipula']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['luogo_stipula'])) || (isset($keyArray['contratti_luogo_stipula']))) {
            $this->setLuogostipula(isset($keyArray['luogo_stipula']) ? $keyArray['luogo_stipula'] : $keyArray['contratti_luogo_stipula']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['data_inizio'])) || (isset($keyArray['contratti_data_inizio']))) {
            $this->setDatainizio(isset($keyArray['data_inizio']) ? $keyArray['data_inizio'] : $keyArray['contratti_data_inizio']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['proprietari'])) || (isset($keyArray['contratti_proprietari']))) {
            $this->setProprietari(isset($keyArray['proprietari']) ? $keyArray['proprietari'] : $keyArray['contratti_proprietari']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['conduttori'])) || (isset($keyArray['contratti_conduttori']))) {
            $this->setConduttori(isset($keyArray['conduttori']) ? $keyArray['conduttori'] : $keyArray['contratti_conduttori']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['garanti'])) || (isset($keyArray['contratti_garanti']))) {
            $this->setGaranti(isset($keyArray['garanti']) ? $keyArray['garanti'] : $keyArray['contratti_garanti']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['tipo_gestione'])) || (isset($keyArray['contratti_tipo_gestione']))) {
            $this->setTipogestione(isset($keyArray['tipo_gestione']) ? $keyArray['tipo_gestione'] : $keyArray['contratti_tipo_gestione']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['mesi_preavviso_locatore'])) || (isset($keyArray['contratti_mesi_preavviso_locatore']))) {
            $this->setMesipreavvisolocatore(isset($keyArray['mesi_preavviso_locatore']) ? $keyArray['mesi_preavviso_locatore'] : $keyArray['contratti_mesi_preavviso_locatore']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['mesi_preavviso_conduttore'])) || (isset($keyArray['contratti_mesi_preavviso_conduttore']))) {
            $this->setMesipreavvisoconduttore(isset($keyArray['mesi_preavviso_conduttore']) ? $keyArray['mesi_preavviso_conduttore'] : $keyArray['contratti_mesi_preavviso_conduttore']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['tipo_pagamento_rata'])) || (isset($keyArray['contratti_tipo_pagamento_rata']))) {
            $this->setTipopagamentorata(isset($keyArray['tipo_pagamento_rata']) ? $keyArray['tipo_pagamento_rata'] : $keyArray['contratti_tipo_pagamento_rata']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['cestino'])) || (isset($keyArray['contratti_cestino']))) {
            $this->setCestino(isset($keyArray['cestino']) ? $keyArray['cestino'] : $keyArray['contratti_cestino']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['ultima_modifica_data'])) || (isset($keyArray['contratti_ultima_modifica_data']))) {
            $this->setUltimamodificadata(isset($keyArray['ultima_modifica_data']) ? $keyArray['ultima_modifica_data'] : $keyArray['contratti_ultima_modifica_data']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['ultima_modifica_utente'])) || (isset($keyArray['contratti_ultima_modifica_utente']))) {
            $this->setUltimamodificautente(isset($keyArray['ultima_modifica_utente']) ? $keyArray['ultima_modifica_utente'] : $keyArray['contratti_ultima_modifica_utente']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['elaborato'])) || (isset($keyArray['contratti_elaborato']))) {
            $this->setElaborato(isset($keyArray['elaborato']) ? $keyArray['elaborato'] : $keyArray['contratti_elaborato']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['disdetta_in_data'])) || (isset($keyArray['contratti_disdetta_in_data']))) {
            $this->setDisdettaindata(isset($keyArray['disdetta_in_data']) ? $keyArray['disdetta_in_data'] : $keyArray['contratti_disdetta_in_data']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['stato_contratto'])) || (isset($keyArray['contratti_stato_contratto']))) {
            $this->setStatocontratto(isset($keyArray['stato_contratto']) ? $keyArray['stato_contratto'] : $keyArray['contratti_stato_contratto']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['id_utente_riferimento'])) || (isset($keyArray['contratti_id_utente_riferimento']))) {
            $this->setIdutenteriferimento(isset($keyArray['id_utente_riferimento']) ? $keyArray['id_utente_riferimento'] : $keyArray['contratti_id_utente_riferimento']);
            $this->flagObjectDataValorized = true;
        }
        if ((isset($keyArray['occupazione_senza_titolo'])) || (isset($keyArray['contratti_occupazione_senza_titolo']))) {
            $this->setOccupazionesenzatitolo(isset($keyArray['occupazione_senza_titolo']) ? $keyArray['occupazione_senza_titolo'] : $keyArray['contratti_occupazione_senza_titolo']);
            $this->flagObjectDataValorized = true;
        }
    }

    /**
     * @return array
     */
    public function createKeyArrayFromPositional($positionalArray)
    {
        $values = array();
        $values['id'] = $positionalArray[0];
        $values['id_tipo_contratto'] = $positionalArray[1];
        $values['id_agenzia_immobiliare'] = $positionalArray[2];
        $values['codice_contratto'] = ($positionalArray[3] == self::NULL_VALUE) ? null : $positionalArray[3];
        $values['tipo_assoggettazione'] = ($positionalArray[4] == self::NULL_VALUE) ? null : $positionalArray[4];
        $values['descrizione'] = ($positionalArray[5] == self::NULL_VALUE) ? null : $positionalArray[5];
        $values['data_stipula'] = ($positionalArray[6] == self::NULL_VALUE) ? null : $positionalArray[6];
        $values['luogo_stipula'] = ($positionalArray[7] == self::NULL_VALUE) ? null : $positionalArray[7];
        $values['data_inizio'] = ($positionalArray[8] == self::NULL_VALUE) ? null : $positionalArray[8];
        $values['proprietari'] = $positionalArray[9];
        $values['conduttori'] = $positionalArray[10];
        $values['garanti'] = ($positionalArray[11] == self::NULL_VALUE) ? null : $positionalArray[11];
        $values['tipo_gestione'] = ($positionalArray[12] == self::NULL_VALUE) ? null : $positionalArray[12];
        $values['mesi_preavviso_locatore'] = ($positionalArray[13] == self::NULL_VALUE) ? null : $positionalArray[13];
        $values['mesi_preavviso_conduttore'] = ($positionalArray[14] == self::NULL_VALUE) ? null : $positionalArray[14];
        $values['tipo_pagamento_rata'] = ($positionalArray[15] == self::NULL_VALUE) ? null : $positionalArray[15];
        $values['cestino'] = $positionalArray[16];
        $values['ultima_modifica_data'] = ($positionalArray[17] == self::NULL_VALUE) ? null : $positionalArray[17];
        $values['ultima_modifica_utente'] = $positionalArray[18];
        $values['elaborato'] = $positionalArray[19];
        $values['disdetta_in_data'] = ($positionalArray[20] == self::NULL_VALUE) ? null : $positionalArray[20];
        $values['stato_contratto'] = ($positionalArray[21] == self::NULL_VALUE) ? null : $positionalArray[21];
        $values['id_utente_riferimento'] = $positionalArray[22];
        $values['occupazione_senza_titolo'] = $positionalArray[23];
        return $values;
    }

    /**
     * @return array
     */
    public function getEmptyDbKeyArray()
    {
        $values = array();
        $values['id'] = null;
        $values['id_tipo_contratto'] = null;
        $values['id_agenzia_immobiliare'] = null;
        $values['codice_contratto'] = null;
        $values['tipo_assoggettazione'] = 'N';
        $values['descrizione'] = null;
        $values['data_stipula'] = null;
        $values['luogo_stipula'] = null;
        $values['data_inizio'] = null;
        $values['proprietari'] = null;
        $values['conduttori'] = null;
        $values['garanti'] = null;
        $values['tipo_gestione'] = null;
        $values['mesi_preavviso_locatore'] = null;
        $values['mesi_preavviso_conduttore'] = null;
        $values['tipo_pagamento_rata'] = 'A';
        $values['cestino'] = 0;
        $values['ultima_modifica_data'] = null;
        $values['ultima_modifica_utente'] = null;
        $values['elaborato'] = 0;
        $values['disdetta_in_data'] = null;
        $values['stato_contratto'] = 'A';
        $values['id_utente_riferimento'] = null;
        $values['occupazione_senza_titolo'] = 0;
        return $values;
    }

    /**
     * Return columns' list
     * @return string
     */
    public function getListColumns()
    {
        return 'contratti.id as contratti_id,contratti.id_tipo_contratto as contratti_id_tipo_contratto,contratti.id_agenzia_immobiliare as contratti_id_agenzia_immobiliare,contratti.codice_contratto as contratti_codice_contratto,contratti.tipo_assoggettazione as contratti_tipo_assoggettazione,contratti.descrizione as contratti_descrizione,contratti.data_stipula as contratti_data_stipula,contratti.luogo_stipula as contratti_luogo_stipula,contratti.data_inizio as contratti_data_inizio,contratti.proprietari as contratti_proprietari,contratti.conduttori as contratti_conduttori,contratti.garanti as contratti_garanti,contratti.tipo_gestione as contratti_tipo_gestione,contratti.mesi_preavviso_locatore as contratti_mesi_preavviso_locatore,contratti.mesi_preavviso_conduttore as contratti_mesi_preavviso_conduttore,contratti.tipo_pagamento_rata as contratti_tipo_pagamento_rata,contratti.cestino as contratti_cestino,contratti.ultima_modifica_data as contratti_ultima_modifica_data,contratti.ultima_modifica_utente as contratti_ultima_modifica_utente,contratti.elaborato as contratti_elaborato,contratti.disdetta_in_data as contratti_disdetta_in_data,contratti.stato_contratto as contratti_stato_contratto,contratti.id_utente_riferimento as contratti_id_utente_riferimento,contratti.occupazione_senza_titolo as contratti_occupazione_senza_titolo';
    }

    /**
     * DDL Table
     */
    public function createTable()
    {
        return $this->pdo->exec("CREATE TABLE `contratti` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipo_contratto` int(10) unsigned NOT NULL,
  `id_agenzia_immobiliare` int(10) unsigned NOT NULL,
  `codice_contratto` varchar(15) DEFAULT NULL,
  `tipo_assoggettazione` enum('N','S') DEFAULT 'N' COMMENT 'N = NON ASSOGGETTO AD IVA\nS = ASSOGGETTO AD IVA',
  `descrizione` varchar(100) DEFAULT NULL,
  `data_stipula` date DEFAULT NULL,
  `luogo_stipula` varchar(60) DEFAULT NULL,
  `data_inizio` date DEFAULT NULL,
  `proprietari` json NOT NULL,
  `conduttori` json NOT NULL,
  `garanti` json DEFAULT NULL,
  `tipo_gestione` enum('C','S','CS') DEFAULT NULL COMMENT 'C=Contratto\nS=Spesa\nCS=Contratto Spesa',
  `mesi_preavviso_locatore` int(11) DEFAULT NULL,
  `mesi_preavviso_conduttore` int(11) DEFAULT NULL,
  `tipo_pagamento_rata` enum('A','P') DEFAULT 'A' COMMENT 'A=Anticipata\nP=Posticipata',
  `cestino` tinyint(1) NOT NULL DEFAULT '0',
  `ultima_modifica_data` datetime DEFAULT NULL,
  `ultima_modifica_utente` int(10) unsigned NOT NULL,
  `elaborato` tinyint(1) NOT NULL DEFAULT '0',
  `disdetta_in_data` date DEFAULT NULL,
  `stato_contratto` enum('A','D') DEFAULT 'A' COMMENT 'A = ATTIVO\nD =  DISDETTATO',
  `id_utente_riferimento` int(10) unsigned NOT NULL,
  `occupazione_senza_titolo` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_id_tipo_contratto` (`id_tipo_contratto`),
  KEY `fk_contratti_anagrafiche1_idx` (`id_agenzia_immobiliare`),
  KEY `fk_contratti_login1_idx` (`ultima_modifica_utente`),
  CONSTRAINT `fk_contratti_anagrafiche1` FOREIGN KEY (`id_agenzia_immobiliare`) REFERENCES `anagrafiche` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_contratti_login1` FOREIGN KEY (`ultima_modifica_utente`) REFERENCES `login` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=latin1 ");
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id Id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return integer
     */
    public function getIdTipoContratto()
    {
        return $this->idTipoContratto;
    }

    /**
     * @param integer $idTipoContratto IdTipoContratto
     */
    public function setIdTipoContratto($idTipoContratto)
    {
        $this->idTipoContratto = $idTipoContratto;
    }

    /**
     * @return integer
     */
    public function getIdAgenziaImmobiliare()
    {
        return $this->idAgenziaImmobiliare;
    }

    /**
     * @param integer $idAgenziaImmobiliare IdAgenziaImmobiliare
     */
    public function setIdAgenziaImmobiliare($idAgenziaImmobiliare)
    {
        $this->idAgenziaImmobiliare = $idAgenziaImmobiliare;
    }

    /**
     * @return string
     */
    public function getCodiceContratto()
    {
        return $this->codiceContratto;
    }

    /**
     * @param string $codiceContratto CodiceContratto
     * @param int $encodeType
     */
    public function setCodiceContratto($codiceContratto, $encodeType = self::STR_DEFAULT)
    {
        $this->codiceContratto = $this->decodeString($codiceContratto, $encodeType);
    }

    /**
     * @param bool $decode if true return decode value * @return string
     */
    public function getTipoAssoggettazione($decode = false)
    {
        return ($decode) ? $this->getTipoAssoggettazioneValuesList()[$this->tipoAssoggettazione] : $this->tipoAssoggettazione;
    }

    /**
     * @param bool $json if true return value json's array else return array values * @return array|string
     */
    public function getTipoAssoggettazioneValuesList($json = false)
    {
        $kv = ['N' => 'NON ASSOGGETTO AD IVA', 'S' => 'ASSOGGETTO AD IVA'];
        return ($json) ? $this->createJsonKeyValArray($kv) : $kv;
    }

    /**
     * @param string (enum) $tipoAssoggettazione TipoAssoggettazione
     */
    public function setTipoAssoggettazione($tipoAssoggettazione)
    {
        $this->tipoAssoggettazione = $tipoAssoggettazione;
    }

    /**
     * @return string
     */
    public function getDescrizione()
    {
        return $this->descrizione;
    }

    /**
     * @param string $descrizione Descrizione
     * @param int $encodeType
     */
    public function setDescrizione($descrizione, $encodeType = self::STR_DEFAULT)
    {
        $this->descrizione = $this->decodeString($descrizione, $encodeType);
    }

    /**
     * @return string
     */
    public function getDataStipula()
    {
        return $this->dataStipula;
    }

    /**
     * @param string $dataStipula DataStipula
     * @param int $encodeType
     */
    public function setDataStipula($dataStipula, $encodeType = self::STR_DEFAULT)
    {
        $this->dataStipula = $this->decodeString($dataStipula, $encodeType);
    }

    /**
     * @return string
     */
    public function getLuogoStipula()
    {
        return $this->luogoStipula;
    }

    /**
     * @param string $luogoStipula LuogoStipula
     * @param int $encodeType
     */
    public function setLuogoStipula($luogoStipula, $encodeType = self::STR_DEFAULT)
    {
        $this->luogoStipula = $this->decodeString($luogoStipula, $encodeType);
    }

    /**
     * @return string
     */
    public function getDataInizio()
    {
        return $this->dataInizio;
    }

    /**
     * @param string $dataInizio DataInizio
     * @param int $encodeType
     */
    public function setDataInizio($dataInizio, $encodeType = self::STR_DEFAULT)
    {
        $this->dataInizio = $this->decodeString($dataInizio, $encodeType);
    }

    /**
     * @return objext (string)
     */
    public function getProprietari()
    {
        return $this->proprietari;
    }

    /**
     * @param objext (string) $proprietari Proprietari
     */
    public function setProprietari($proprietari)
    {
        $this->proprietari = $proprietari;
    }

    /**
     * @return objext (string)
     */
    public function getConduttori()
    {
        return $this->conduttori;
    }

    /**
     * @param objext (string) $conduttori Conduttori
     */
    public function setConduttori($conduttori)
    {
        $this->conduttori = $conduttori;
    }

    /**
     * @return objext (string)
     */
    public function getGaranti()
    {
        return $this->garanti;
    }

    /**
     * @param objext (string) $garanti Garanti
     */
    public function setGaranti($garanti)
    {
        $this->garanti = $garanti;
    }

    /**
     * @param bool $decode if true return decode value * @return string
     */
    public function getTipoGestione($decode = false)
    {
        return ($decode) ? $this->getTipoGestioneValuesList()[$this->tipoGestione] : $this->tipoGestione;
    }

    /**
     * @param bool $json if true return value json's array else return array values * @return array|string
     */
    public function getTipoGestioneValuesList($json = false)
    {
        $kv = ['C' => 'Contratto', 'S' => 'Spesa', 'CS' => 'Contratto Spesa'];
        return ($json) ? $this->createJsonKeyValArray($kv) : $kv;
    }

    /**
     * @param string (enum) $tipoGestione TipoGestione
     */
    public function setTipoGestione($tipoGestione)
    {
        $this->tipoGestione = $tipoGestione;
    }

    /**
     * @return integer
     */
    public function getMesiPreavvisoLocatore()
    {
        return $this->mesiPreavvisoLocatore;
    }

    /**
     * @param integer $mesiPreavvisoLocatore MesiPreavvisoLocatore
     */
    public function setMesiPreavvisoLocatore($mesiPreavvisoLocatore)
    {
        $this->mesiPreavvisoLocatore = $mesiPreavvisoLocatore;
    }

    /**
     * @return integer
     */
    public function getMesiPreavvisoConduttore()
    {
        return $this->mesiPreavvisoConduttore;
    }

    /**
     * @param integer $mesiPreavvisoConduttore MesiPreavvisoConduttore
     */
    public function setMesiPreavvisoConduttore($mesiPreavvisoConduttore)
    {
        $this->mesiPreavvisoConduttore = $mesiPreavvisoConduttore;
    }

    /**
     * @param bool $decode if true return decode value * @return string
     */
    public function getTipoPagamentoRata($decode = false)
    {
        return ($decode) ? $this->getTipoPagamentoRataValuesList()[$this->tipoPagamentoRata] : $this->tipoPagamentoRata;
    }

    /**
     * @param bool $json if true return value json's array else return array values * @return array|string
     */
    public function getTipoPagamentoRataValuesList($json = false)
    {
        $kv = ['A' => 'Anticipata', 'P' => 'Posticipata'];
        return ($json) ? $this->createJsonKeyValArray($kv) : $kv;
    }

    /**
     * @param string (enum) $tipoPagamentoRata TipoPagamentoRata
     */
    public function setTipoPagamentoRata($tipoPagamentoRata)
    {
        $this->tipoPagamentoRata = $tipoPagamentoRata;
    }

    /**
     * @return integer
     */
    public function getCestino()
    {
        return $this->cestino;
    }

    /**
     * @param integer $cestino Cestino
     */
    public function setCestino($cestino)
    {
        $this->cestino = $cestino;
    }

    /**
     * @return string
     */
    public function getUltimaModificaData()
    {
        return $this->ultimaModificaData;
    }

    /**
     * @param string $ultimaModificaData UltimaModificaData
     * @param int $encodeType
     */
    public function setUltimaModificaData($ultimaModificaData, $encodeType = self::STR_DEFAULT)
    {
        $this->ultimaModificaData = $this->decodeString($ultimaModificaData, $encodeType);
    }

    /**
     * @return integer
     */
    public function getUltimaModificaUtente()
    {
        return $this->ultimaModificaUtente;
    }

    /**
     * @param integer $ultimaModificaUtente UltimaModificaUtente
     */
    public function setUltimaModificaUtente($ultimaModificaUtente)
    {
        $this->ultimaModificaUtente = $ultimaModificaUtente;
    }

    /**
     * @return integer
     */
    public function getElaborato()
    {
        return $this->elaborato;
    }

    /**
     * @param integer $elaborato Elaborato
     */
    public function setElaborato($elaborato)
    {
        $this->elaborato = $elaborato;
    }

    /**
     * @return string
     */
    public function getDisdettaInData()
    {
        return $this->disdettaInData;
    }

    /**
     * @param string $disdettaInData DisdettaInData
     * @param int $encodeType
     */
    public function setDisdettaInData($disdettaInData, $encodeType = self::STR_DEFAULT)
    {
        $this->disdettaInData = $this->decodeString($disdettaInData, $encodeType);
    }

    /**
     * @param bool $decode if true return decode value * @return string
     */
    public function getStatoContratto($decode = false)
    {
        return ($decode) ? $this->getStatoContrattoValuesList()[$this->statoContratto] : $this->statoContratto;
    }

    /**
     * @param bool $json if true return value json's array else return array values * @return array|string
     */
    public function getStatoContrattoValuesList($json = false)
    {
        $kv = ['A' => 'ATTIVO', 'D' => 'DISDETTATO'];
        return ($json) ? $this->createJsonKeyValArray($kv) : $kv;
    }

    /**
     * @param string (enum) $statoContratto StatoContratto
     */
    public function setStatoContratto($statoContratto)
    {
        $this->statoContratto = $statoContratto;
    }

    /**
     * @return integer
     */
    public function getIdUtenteRiferimento()
    {
        return $this->idUtenteRiferimento;
    }

    /**
     * @param integer $idUtenteRiferimento IdUtenteRiferimento
     */
    public function setIdUtenteRiferimento($idUtenteRiferimento)
    {
        $this->idUtenteRiferimento = $idUtenteRiferimento;
    }

    /**
     * @return integer
     */
    public function getOccupazioneSenzaTitolo()
    {
        return $this->occupazioneSenzaTitolo;
    }

    /**
     * @param integer $occupazioneSenzaTitolo OccupazioneSenzaTitolo
     */
    public function setOccupazioneSenzaTitolo($occupazioneSenzaTitolo)
    {
        $this->occupazioneSenzaTitolo = $occupazioneSenzaTitolo;
    }
}