<!DOCTYPE html>
<html ng-app="affittiApp" ng-controller="homeController">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <?php
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0"); // Proxies.
    ?>
    <!--import lib css js-->
    <?php include_once '../grafica/header.html';?>
    <!--import controller-->
    <script type="text/javascript"  src="template/controller/homeController.js"></script>
    <!--funzione php per l'import dinamico dei controller delle pagine di cui faccio l'include-->
    <?php
        $sezione = 'dashboard';
        $controller = 'dashboardController.js';
        $cartella = 'controller';

        if(isset($_GET['sezione']))
            $sezione = $_GET['sezione'];
        if(isset($_GET['pagina']))
            $controller = $_GET['pagina'].'Controller.js';

//        echo '<script type="text/javascript" src="'.$sezione.'/'.$cartella.'/'.$controller.'"></script>';
        echo '<script type="text/javascript" src="'.$sezione.'/'.$cartella.'/'.$controller.'?version='.date('Ymd').'"></script>';

        if(isset($_GET['pagina']) && $_GET['pagina']=='timelineContratto'){
            echo '<link href="../grafica/css/timeline.css" rel="stylesheet" type="text/css" media="all">';
        }
    ?>

</head>

<body class="bgColor">
<div class="horizontalBar">
    <div class="mostraMenuContainer pointer">
        <img src="../grafica/img/Logo-Click.png"
        style="margin-top: 4px; background-color: rgba(255,255,255,0.6); border: 1px solid #fff; border-radius: 5px;" ng-click="contatti();">
    </div>

    <div id="gestioneUtente" class="containerUtenteHeader">
        <div class="horizontalBarButtonContainer">
            <button type="button" class="btn btn-sm horizontalBarErrorButton pointer" title="Configurazioni"
                    ng-show="mostraAvvisoCambioPwd" ng-click="cambiaPassword()">
                <i class="fa fa-fw fa-key fa-2x" aria-hidden="true"></i>
            </button>
            <button type="button" class="btn btn-sm horizontalBarButton pointer" title="Configurazioni"
                    ng-click="includePage('configurazioni','configurazioni')">
                <i class="fa fa-fw fa-cog fa-2x" aria-hidden="true"></i>
            </button>
            <button type="button" class="btn btn-sm horizontalBarButton pointer" title="Logout"
                    ng-click="logout()">
                <i class="fa fa-fw fa-power-off fa-2x" aria-hidden="true"></i>
            </button>
            <div class="infoUtente">Benvenuto: {{username}}</div>

        </div>
    </div>
</div>

<div ng-class="{'sidebarOpened' : visible, 'sidebarClosed' : !visible}">

    <div class="menu-container" id="containerMostraMenu">
        <div class="mostraMenu pointer" ng-click="mostraMenu()">
            <i class="fa fa-bars fa-fw fa-lg" aria-hidden="true"></i>
            <span class="menu-testo">Nascondi menu</span>
        </div>
    </div>

    <!--------------------------------------------------HOME----------------------------------------------------------->
    <div class="menu-container">
        <div class="primoLivello pointer" ng-click="includePage('dashboard','dashboard')" title="Home">
            <i class="fa fa-tachometer fa-fw fa-lg" aria-hidden="true"></i>
            <span class="menu-testo">Dashboard</span>
        </div>
    </div>
</div>




<div ng-class="{'contentPart' : visible, 'contentFull' : !visible}">
    <!--
     pagina: variabile di scope contente la pagina da includere
     ng-init: attiva l'ng-include al termine della funzione, in getPageIncluded definisco quale sarà la variabile 'pagina' poi la importo
     -->

    <div id="contenitorePagina">
        <div ng-include src="import" ng-init="getPageIncluded()"></div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover({trigger:"focus",placement:"bottom",container:"body"});
    });
</script>

</body>
</html>