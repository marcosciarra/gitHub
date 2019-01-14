<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!--import lib css js-->
    <?php include_once '../grafica/header.html'; ?>
    <!-- import angularjs controller-->
    <script type="text/javascript" src="template/controller/loginController.js"></script>
    <script type="text/javascript" src="../functions/functionGeneric.js"></script>
    <title>Login</title>
</head>

<body ng-app="affittiApp" ng-controller="loginController" class="bgColor">

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-sm-4 col-sm-offset-4 marginT15percent">
            <div class="panel panel-primary text-center">
                <div class="panel-heading text-center">Login</div>
                <div class="panel-body">
                    <div class="form-group input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                </span>
                        <input type="text" class="form-control" ng-model="username"
                               autocomplete="off"/>
                    </div>
                    <span ng-show="username == ''">Username obbligatorio</span>
                    <div class="form-group input-group">
                                <span class="input-group-addon">
                                    <i class="fa fa-lock" aria-hidden="true"></i>
                                </span>
                        <input type="password" class="form-control" ng-model="password"
                               autocomplete="off"/>
                    </div>
                    <button type="button" class="btn btn-default"
                            ng-show="username.length > 0 && password.length > 0"
                            ng-click="effettuaLogin()">Login
                    </button>

                    <div class="alert alert-danger" ng-show="erroreLogin">
                        <strong>Login fallito!</strong> Username o Password errati.
                    </div>
                    <span ng-show="password == ''">Password obbligatoria</span>
                </div>
                <div class="panel-footer">
                    <small>&copy; Click Affitti</small>
                </div>
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
            <div class="text-center">
                <span class="color-white">
                    <label>Seguici su </label>
                </span>
            </div>
            <div class="text-center">
                <a href="http://www.clicksrl.eu" target="_blank" class="btn btn-primary" style="color: white">
                    <i class="fa fa-globe fa-fw" aria-hidden="true"></i>
                </a>
                <a href="https://www.facebook.com/ClickSoftwareGestioneCondomini/" target="_blank" class="btn btn-facebook" style="background-color: #3B5998;color: white">
                    <i class="fa fa-facebook fa-fw" aria-hidden="true"></i>
                </a>
                <a href="https://www.linkedin.com/company/click-amministazione-condominio/" target="_blank" class="btn btn-linkedin" style="background-color: #007BB6;color: white">
                    <i class="fa fa-linkedin fa-fw" aria-hidden="true"></i>
                </a>
<!--                <a class="btn btn-google" style="background-color: #DD4B39;color: white">-->
<!--                    <i class="fa fa-google fa-fw" aria-hidden="true"></i>-->
<!--                </a>-->
            </div>
            <div class="text-center marginT15">
                <span class="color-white">
                    <label>Software ottimizzato per</label>
                </span>
                <div class="">
                    <a href="https://www.google.com/chrome" target="_blank" title="Google Chrome">
                        <img src="../grafica/img/google_chrome_logo.png" alt="Google Chrome" width="50px">
                    </a>
                </div>

            </div>
        </div>
<!--        <div style="position: absolute;top: 10px;width: 250px;left: 50%;margin-left: -125px">-->
<!--            <label class="color-white">Software ottimizzato per :</label>-->
<!--            <a href="https://www.google.com/chrome" target="_blank" title="Google Chrome">-->
<!--                <img src="../grafica/img/google_chrome_logo.png" alt="Google Chrome" width="50px">-->
<!--            </a>-->
<!--        </div>-->
    </div>
</div>

</body>
</html>