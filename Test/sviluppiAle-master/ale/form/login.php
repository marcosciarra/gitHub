<?php

/*  Alessandro Pericolo
 *  Training development
 *  Copyright @2017 Superperil
 *  "Programmers will conquer the Hello World"
*/

include_once '../grafica/masterpage_up.php'; ?>

<title>Login</title>

<script type="text/javascript" src="../angular/loginController.js"></script>

<div id="container" class="col-md-4 col-md-offset-4" ng-controller="loginController">
    
    <div class="panel panel-default voffset50">

        <div class="panel-heading panleHeadingCustom"> <i class="glyphicon glyphicon-user"></i> LOGIN </div>

        <div class="panel-body">

            <div class="form-group input-group">
                <span class="input-group-addon"> Username </span>
                <input type="text" name="username" id="username" class="form-control" ng-model="userLogin.username" autocomplete="off" required/>
            </div>
            <span ng-show="userLogin.username.$dirty && userLogin.username.$error.required" class="help-block">Username obbligatorio</span>

            <div class="form-group input-group">
                <span class="input-group-addon"> Password&nbsp; </span>
                <input type="password" name="password" id="password" class="form-control" ng-model="userLogin.password" autocomplete="off" required/>
            </div>
            <span ng-show="userLogin.password.$dirty && userLogin.password.$error.required" class="help-block">Password obbligatoria</span>

            <button class="btn btn-default" ng-click="login()" ng-disabled="(!userLogin.username.length > 0) || (!userLogin.password.length > 0)" >Login</button> <br/>
            <a href="registration.php" style="float: right"> Registrati </a>
                
        </div>
        
    </div>
    
</div>
    
<?php include_once '../grafica/masterpage_down.php'; ?>
