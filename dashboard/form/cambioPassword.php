<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
    <!--import lib css js-->
    <?php include_once '../grafica/header.html';?>
    <!-- import angularjs controller-->
    <script type="text/javascript" src="template/controller/cambioPasswordController.js"></script>
    <script type="text/javascript" src="../functions/functionGeneric.js"></script>

    <title>Cambio Password</title>
</head>

<body ng-app="affittiApp" ng-controller="loginController" class="bgColor">

    <div class="container-fluid">

        <div class="row justify-content-center" ng-show="utente.bloccato == 1">
            <div class="col-sm-6 col-sm-offset-3 marginT5percent">

                <div class="alert alert-danger margin10" role="alert">
                    <p><strong>L'utente risulta bloccato</strong></p>
                </div>
            </div>
        </div>


        <div class="row justify-content-center" ng-show="utente.bloccato == 0">
            <div class="col-sm-4 col-sm-offset-4 marginT5percent">
                <div class="alert alert-warning margin10" role="alert">
                    <!-- primo accesso -->
                    <div ng-show="utente.ultimo_accesso == null">
                        <p>
                            <span class="fa-passwd-reset fa-stack">
                                <i class="fa fa-undo fa-stack-2x"></i>
                                <i class="fa fa-lock fa-stack-1x"></i>
                            </span>
                            <strong>Impostazione Password</strong>
                        </p>
                        <span>Stai effettuando il primo accesso.</span>
                        <p ng-show="!mostraFormNuovaPassword">Per procedere inserire la password (corrisponde al nome utente).</p>
                        <p ng-show="mostraFormNuovaPassword">Impostare una nuova password.</p>
                    </div>
                    <!-- cambi password -->
                    <div ng-show="utente.ultimo_accesso != null">
                        <p>
                            <span class="fa-passwd-reset fa-stack">
                                <i class="fa fa-undo fa-stack-2x"></i>
                                <i class="fa fa-lock fa-stack-1x"></i>
                            </span>
                            <strong>Rinnovo Password</strong>
                        </p>
                        <span>La password di accesso risulta scaduta.</span>
                        <p ng-show="!mostraFormNuovaPassword">Per procedere inserire la vecchia password.</p>
                        <p ng-show="mostraFormNuovaPassword">Impostare una nuova password.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center" ng-show="utente.bloccato == 0">
            <div class="col-sm-4 col-sm-offset-4">
                    <div class="panel panel-primary text-center">
                        <div class="panel-heading text-center">CAMBIO PASSWORD</div>
                        <div class="panel-body">

                            <!-- form vecchia password -->
                            <div ng-show="!mostraFormNuovaPassword">
                                <div class="form-group input-group marginT15">
                                    <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control width100"
                                       ng-class="vecchiaPassword == '' ? 'requiredField' : ''" ng-model="vecchiaPassword">
                                </div>
                                <button ng-show="vecchiaPassword.length>0" ng-click="verificaVecchiaPassword()">verifica password</button>
                            </div>

                            <!-- form nuova password -->
                            <div ng-show="mostraFormNuovaPassword">

                                <p class="marginT10">Inserisci una nuova password</p>
                                <div class="form-group input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    <input type="password" class="form-control width100"
                                       ng-class="utente.password == '' ? 'requiredField' : ''" ng-model="utente.password"
                                       ng-blur="verificaPassword(utente.password)" id="password"
                                       ng-click="visualizzaPassword = false" ng-change="visualizzaPassword = false">
                                </div>

                                <p>Inserisci la conferma della nuova password</p>

                                <div class="form-group input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    <input type="password" class="form-control width100"
                                       ng-class="utente.confermaPassword == '' ? 'requiredField' : ''"
                                       ng-model="utente.confermaPassword"
                                       ng-click="visualizzaPassword = false" ng-change="visualizzaPassword = false">
                                </div>

                                <div class="security{{resultPwd.advisor}}" ng-show="utente.password.length>0">
                                    <span class="glyphicon glyphicon-alert security{{resultPwd.advisor}}"
                                      aria-hidden="true">
                                    </span> &nbsp; {{resultPwd.description}}
                                </div>

                                <div class="alertText red" ng-if="utente.confermaPassword.length > 0 && (utente.password != utente.confermaPassword)">
                                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    <span class="marginL10">Le password non coincidono</span>
                                </div>

                                <hr>

                                <p class="text-center">Oppure genera una nuova password</p>

                                <div class="form-group input-group marginAuto">
                                    <button class="btn btn-default" ng-click="generaPassword()">
                                        <i class="fa fa-cog" aria-hidden="true"></i>
                                        Genera password
                                    </button>
                                </div>

                                <p class="text-center marginT10" ng-show="visualizzaPassword">Password generata</p>
                                <div class="form-group input-group" ng-show="visualizzaPassword">
                                    <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                                    <input type="text" class="form-control width100"
                                           ng-model="passwordGenerata">
                                </div>

                                <div class="form-group input-group marginAuto">
                                    <button class="btn btn-success" ng-show="campiValidi" ng-click="salvaDati()">salva</button>
                                </div>

                            </div>

                        </div>
                        <div class="panel-footer"><small>&copy; ClickAffitti</small></div>
                    </div>
<!--
                    <div class="card-header text-center text-white bg-dark">
                        <strong class="text-uppercase">Login</strong>
                    </div>

                    <div class="card-body bg-light text-center padding10">
                            <div class="form-group input-group">
                                <span class="input-group-addon"> Username </span>
                                <input type="text" class="form-control" ng-model="username"
                                       autocomplete="off"/>
                            </div>
                            <span ng-show="username == ''">Username obbligatorio</span>

                            <div class="form-group input-group">
                                <span class="input-group-addon"> Password&nbsp; </span>
                                <input type="password" class="form-control" ng-model="password"
                                       autocomplete="off"/>
                            </div>
                            <span ng-show="password == ''">Password obbligatoria</span>

                            <button type="button" class="btn btn-default"
                                    ng-show="username.length > 0 && password.length > 0"
                                    ng-click="effettuaLogin()">Login</button>

                            <div class="alert alert-danger" ng-show="erroreLogin">
                                <strong>Login fallito!</strong> Username o Password errati.
                            </div>
                    </div>
-->
            </div>
        </div>
    </div>

</body>
</html>