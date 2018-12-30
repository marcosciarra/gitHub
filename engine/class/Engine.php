<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 27/12/18
 * Time: 21.34
 */

require_once '../conf/costanti.php';
require_once '../lib/pdo.php';

class Engine
{

    /** @var string */
    protected $nomeTabella;
    /** @var string */
    protected $pathOutput;
    /** @var boolean */
    protected $model = true;
    /** @var string */
    protected $nameSpace;

    protected $con;

    protected $nomeTabellaClasse;

    protected $file;

    protected $descrizioneTabella;

    protected $indiciTabella;

    protected $createTable;

    /**
     * Engine constructor.
     * @param string $nomeTabella
     * @param bool $model
     * @param string $nameSpace
     */
    public function __construct($nomeTabella, $model = true, $nameSpace = 'TblBase')
    {
        $this->nomeTabella = $nomeTabella;
        $this->model = $model;
        $this->nameSpace = $nameSpace;

        $this->nomeTabellaClasse = $this->cambiaNomeTabella($this->nomeTabella);

        $attribute = array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        $this->con = new PDO(
            'mysql:host=' . HOST . ';dbname=' . SCHEMA . ';charset=' . CHARSET,
            USER,
            PWD,
            $attribute
        );


        if ($this->model == true) {
            $this->pathOutput = "../output/" . $this->nomeTabellaClasse . "Model.php";
            $query = 'DESCRIBE ' . $this->nomeTabella;
            $this->descrizioneTabella = $this->con->query($query)->fetchAll();
            $query = 'SHOW INDEX FROM ' . $this->nomeTabella;
            $this->indiciTabella = $this->con->query($query)->fetchAll();
            $query = 'SHOW CREATE TABLE ' . $this->nomeTabella;
            $this->createTable = $this->con->query($query)->fetchColumn(1);
        } else {
            $this->pathOutput = "../output/" . $this->nomeTabellaClasse . ".php";
        }

    }


    private function cambiaNomeTabella($nomeTabella)
    {
        $appNome = '';
        foreach (explode('_', $nomeTabella) as $app) {
            $appNome .= ucfirst($app);
        }
        return $appNome;
    }


    private function cambiaNomeTabellaAttributo($nomeTabella, $mode = false)
    {
        $appNome = '';
        foreach (explode('_', $nomeTabella) as $app) {
            if ($appNome == '' && $mode == false) {
                $appNome .= $app;
            } else {
                $appNome .= ucfirst($app);
            }
        }
        return $appNome;
    }


    public function apriFile()
    {
        $this->file = fopen($this->pathOutput, "w");
    }


    public function chiudiFile()
    {
        fclose($this->file);
    }

    public function enter()
    {
        fwrite($this->file, "\n");
    }


    public function testata()
    {
        fwrite($this->file, "<?php\n/**\nCreated by Sciarra Marco\n**/\n");
        $this->enter();
    }


    public function createNameSpace()
    {
        fwrite($this->file, "namespace Click\\" . $this->nameSpace . "\TblBase;\n");
    }


    public function requireOnce()
    {
        if ($this->model == true) {
            fwrite($this->file, "require_once 'PdaAbstractModel.php';");
        } else {
            fwrite($this->file, "require_once '" . $this->nomeTabellaClasse . "Model.php';");
        }
        $this->enter();
        $this->enter();
    }


    public function useClass()
    {
        if ($this->model == true) {
            fwrite($this->file, "use Click\Affitti\TblBase\PdaAbstractModel;");
        } else {
            fwrite($this->file, "use Click\Affitti\TblBase\\" . $this->nomeTabellaClasse . "Model;");
        }
        $this->enter();
        $this->enter();
    }


    public function openClass()
    {
        if ($this->model == true) {
            fwrite($this->file, "/**");
            $this->enter();
            fwrite($this->file, "@property string nomeTabella");
            $this->enter();
            fwrite($this->file, "@property string tableName");
            $this->enter();
            fwrite($this->file, "*/");
            $this->enter();
            fwrite($this->file, "class " . $this->nomeTabellaClasse . "Model extends PdaAbstractModel");
        } else {
            fwrite($this->file, "class " . $this->nomeTabellaClasse . " extends " . $this->nomeTabellaClasse . "Model");
        }
        $this->enter();
        fwrite($this->file, "{");
        $this->enter();
        $this->creaAttributi();
        $this->enter();
    }


    public function closeClass()
    {
        fwrite($this->file, "}");
    }


    public function costruttore()
    {
        if ($this->model == true) {
            fwrite($this->file, "function __construct(\$pdo){parent::__construct(\$pdo);\$this->nomeTabella = '" . $this->nomeTabella . "';\$this->tableName = '" . $this->nomeTabella . "';}");
        } else {
            fwrite($this->file, "function __construct(\$pdo){parent::__construct(\$pdo);}");
        }
        $this->enter();
    }


    public function creaAttributi()
    {
        $this->enter();
        foreach ($this->descrizioneTabella as $app) {
            fwrite($this->file, "/** @var */");
            $this->enter();
            fwrite($this->file, "protected \$" . $this->cambiaNomeTabellaAttributo($app['Field']) . ";");
            $this->enter();
        }
    }


    public function primayKey()
    {
        $this->enter();
        fwrite($this->file, "/*--------------------------------------------------- PRIMARY ----------------------------------------------------*/");

        $this->enter();
        foreach ($this->descrizioneTabella as $app) {
            if ($app['Key'] == 'PRI') {
                $this->find($app['Field'], $app['Field'], 'PK');
                $this->delete($app['Field'], $app['Field'], 'PK');
            }
        }
    }


    public function indexKey()
    {
        $this->enter();
        fwrite($this->file, "/*---------------------------------------------------- INDEX -----------------------------------------------------*/");
        $this->enter();
        foreach ($this->indiciTabella as $app) {
            if ($app['Non_unique'] == 1) {
                $this->find($this->cambiaNomeTabellaAttributo($app['Key_name']), $app['Column_name'], $app['Key_name']);
//                $this->delete($this->cambiaNomeTabellaAttributo($app['Key_name']), $app['Column_name'], $app['Key_name']);
            }
        }
    }


    private function find($nomeColonna, $nomeColonnaOriginale, $index = '')
    {
        switch ($index) {
            //PrimaryKey
            case 'PK':
                fwrite($this->file, "public function findByPk(\$" . $nomeColonna . ", \$typeResult = self::FETCH_OBJ)");
                break;
            //Id
            case '':
                fwrite($this->file, "public function findBy" . ucfirst($nomeColonna) . "(\$" . $nomeColonna . ", \$typeResult = self::FETCH_OBJ");
                break;
            //Index
            default:
                fwrite($this->file, "public function findBy" . ucfirst($nomeColonna) . "(\$" . $nomeColonna . ", \$typeResult = self::FETCH_OBJ)");
                break;
        }
        fwrite($this->file, "{");
        switch ($index) {
            //PrimaryKey
            case 'PK':
                fwrite($this->file, "\$query = \"SELECT * FROM \$this->tableName USE INDEX(PRIMARY) WHERE " . $nomeColonnaOriginale . "=? \";");
                break;
            //Id
            case '':
                fwrite($this->file, "\$query = \"SELECT * FROM \$this->tableName WHERE " . $nomeColonnaOriginale . "=? \";");
                break;
            //Index
            default:
                fwrite($this->file, "\$query = \"SELECT * FROM \$this->tableName USE INDEX(" . $index . ") WHERE " . $nomeColonnaOriginale . "=? \";");
                break;
        }
        fwrite($this->file, "if (\$this->whereBase) \$query .= \" AND \$this->whereBase\";");
        if ($index == 'PK') {
            fwrite($this->file, "return \$this->createResult(\$query, array(\$" . $nomeColonna . "), \$typeResult);");
        } else {
            fwrite($this->file, "if (\$this->orderBase) \$query .= \" ORDER BY \$this->orderBase\";");
            fwrite($this->file, "return \$this->createResultArray(\$query, array(\$" . $nomeColonna . "), \$typeResult);");
        }
        fwrite($this->file, "}");
        $this->enter();
    }


    public function findAll()
    {
        fwrite($this->file, "public function findAll(\$distinct = false, \$typeResult = self::FETCH_OBJ, \$limit = -1, \$offset = -1)");
        fwrite($this->file, "{");
        fwrite($this->file, "\$distinctStr = (\$distinct) ? 'DISTINCT' : '';");
        fwrite($this->file, "\$query = \"SELECT \$distinctStr * FROM \$this->tableName \";");
        fwrite($this->file, "if (\$this->whereBase) \$query .= \" WHERE \$this->whereBase\";");
        fwrite($this->file, "if (\$this->orderBase) \$query .= \" ORDER BY \$this->orderBase\";");
        fwrite($this->file, "\$query .= \$this->createLimitQuery(\$limit, \$offset);");
        fwrite($this->file, "return \$this->createResultArray(\$query, null, \$typeResult);");
        fwrite($this->file, "}");
        $this->enter();
    }


    public function toArrayAssoc()
    {
        $this->enter();
        fwrite($this->file, "public function toArrayAssoc(){\$arrayValue = array();");
        foreach ($this->descrizioneTabella as $app) {
            fwrite($this->file, "if (isset(\$this->" . $this->cambiaNomeTabellaAttributo($app['Field']) . ")) \$arrayValue['" . $app['Field'] . "'] = ");
            if ($app['Null'] == 'NO') {
                fwrite($this->file, "\$this->" . $this->cambiaNomeTabellaAttributo($app['Field']) . ";");
            } else {
                if ($app['Type'] == 'json') {
                    fwrite($this->file, "\$this->jsonEncode(\$this->" . $this->cambiaNomeTabellaAttributo($app['Field']) . ");");
                } else {
                    fwrite($this->file, "(\$this->" . $this->cambiaNomeTabellaAttributo($app['Field']) . " == self::NULL_VALUE) ? null : \$this->" . $this->cambiaNomeTabellaAttributo($app['Field']) . ";");
                }
            }

        }
        fwrite($this->file, "return \$arrayValue;}");
    }


    public function createObjKeyArray($nomeTabella)
    {
        $this->enter();
        fwrite($this->file, "public function createObjKeyArray(array \$keyArray){");
        fwrite($this->file, "\$this->flagObjectDataValorized = false;");
        foreach ($this->descrizioneTabella as $app) {
            fwrite($this->file, "if ((isset(\$keyArray['" . $app['Field'] . "'])) || (isset(\$keyArray['" . $nomeTabella . "_" . $app['Field'] . "']))) {");
            fwrite($this->file, "\$this->" . $this->cambiaNomeTabellaAttributo("set_" . $app['Field']) . "(isset(\$keyArray['" . $app['Field'] . "']) ? \$keyArray['" . $app['Field'] . "'] : \$keyArray['" . $nomeTabella . "_" . $app['Field'] . "']);");
            fwrite($this->file, "\$this->flagObjectDataValorized = true;");
            fwrite($this->file, "}");

        }
        fwrite($this->file, "}");
    }


    public function createKeyArrayFromPositional()
    {
        $this->enter();
        fwrite($this->file, "public function createKeyArrayFromPositional(\$positionalArray){\$values = array();");
        $count = 0;
        foreach ($this->descrizioneTabella as $app) {
            fwrite($this->file, "\$values['" . $app['Field'] . "'] = ");
            if ($app['Null'] == 'NO') {
                fwrite($this->file, "\$positionalArray[" . $count . "];");
            } else {
                fwrite($this->file, "(\$positionalArray[" . $count . "] == self::NULL_VALUE) ? null : \$positionalArray[" . $count . "];");
            }
            $count++;
        }
        fwrite($this->file, "return \$values;");
        fwrite($this->file, "}");
    }


    public function getEmptyDbKeyArray()
    {
        $this->enter();
        fwrite($this->file, "public function getEmptyDbKeyArray(){\$values = array();");
        foreach ($this->descrizioneTabella as $app) {
            fwrite($this->file, "\$values['" . $app['Field'] . "'] = null;");
        }
        fwrite($this->file, "return \$values;");
        fwrite($this->file, "}");
    }


    public function getListColumns($nomeTabella)
    {
        $this->enter();
        fwrite($this->file, "public function getListColumns(){");
        fwrite($this->file, "return '");
        $flag = false;
        foreach ($this->descrizioneTabella as $app) {
            if ($flag == true) fwrite($this->file, ",");
            if ($flag == false) $flag = true;
            fwrite($this->file, $nomeTabella . "." . $app['Field'] . " as " . $nomeTabella . "_" . $app['Field']);
        }
        fwrite($this->file, "';");
        fwrite($this->file, "}");
    }


    public function createTable()
    {
        $this->enter();
        fwrite($this->file, "public function createTable(){");
        fwrite($this->file, "return \$this->pdo->exec(\"");
        fwrite($this->file, $this->createTable);
        fwrite($this->file, "\");");
        fwrite($this->file, "}");
    }


    private function delete($nomeColonna, $nomeColonnaOriginale, $index = '')
    {
        switch ($index) {
            //PrimaryKey
            case 'PK':
                fwrite($this->file, "public function deleteByPk(\$" . $nomeColonna . ")");
                break;
            //Id
            case '':
                fwrite($this->file, "public function deleteBy" . ucfirst($nomeColonna) . "(\$" . $nomeColonna . "");
                break;
            //Index
            default:
                fwrite($this->file, "public function deleteBy" . ucfirst($nomeColonna) . "(\$" . $nomeColonna . ")");
                break;
        }
        fwrite($this->file, "{");
        fwrite($this->file, "\$query = \"DELETE FROM \$this->tableName WHERE " . $nomeColonnaOriginale . "=? \";");
        fwrite($this->file, "if (\$this->whereBase) \$query .= \" AND \$this->whereBase\";");
        fwrite($this->file, "return \$this->createResultValue(\$query, array(\$" . $nomeColonna . "));");
        fwrite($this->file, "}");
        $this->enter();
    }


    public function getSetTabella()
    {
        $this->enter();
        fwrite($this->file, "/*-------------------------------------------------- GET e SET ---------------------------------------------------*/");

        $this->enter();
        foreach ($this->descrizioneTabella as $app) {
            /*----------------------------------------------GET-------------------------------------------------------*/
            fwrite($this->file, "/** @return " . $app['Type'] . " */");
            $this->enter();
            fwrite($this->file, "public function " . $this->cambiaNomeTabellaAttributo("get_" . $app['Field']) . "(){return \$this->" . $this->cambiaNomeTabellaAttributo($app['Field']) . ";}");
            $this->enter();
            /*----------------------------------------------SET-------------------------------------------------------*/
            fwrite($this->file, "/** @param string \$" . $this->cambiaNomeTabellaAttributo($app['Field']) . " " . $this->cambiaNomeTabellaAttributo($app['Field'], true) . "\n@param int \$encodeType*/");
            $this->enter();
            fwrite($this->file, "public function " . $this->cambiaNomeTabellaAttributo("set_" . $app['Field']) . "(\$" . $this->cambiaNomeTabellaAttributo($app['Field']) . "){\$this->" . $this->cambiaNomeTabellaAttributo($app['Field']) . "=\$" . $this->cambiaNomeTabellaAttributo($app['Field']) . ";}");
            $this->enter();
            $this->enter();
        }
    }
}