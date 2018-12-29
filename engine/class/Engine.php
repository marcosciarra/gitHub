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
            fwrite($this->file, "public function " . $this->cambiaNomeTabellaAttributo("set_" . $app['Field']) . "(\$" . $this->cambiaNomeTabellaAttributo($app['Field']) . "){\$this->".$this->cambiaNomeTabellaAttributo($app['Field'])."=\$".$this->cambiaNomeTabellaAttributo($app['Field']).";}");
            $this->enter();
            $this->enter();
        }
    }
}