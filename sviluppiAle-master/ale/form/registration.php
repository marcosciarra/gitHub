<?php

/*  Alessandro Pericolo
 *  Training development
 *  Copyright @2017 Superperil
 *  "Programmers will conquer the Hello World"
*/

include_once '../grafica/masterpage_up.php'; ?>

<title>Registration</title>

<script type="text/javascript" src="../angular/registrationController.js"></script>

<div id="container" class="col-md-6 col-md-offset-3"  ng-controller="registrationController">
            
    <div id="inputData" class="panel panel-default voffset50">

        <div class="panel-heading panleHeadingCustom"> <i class="glyphicon glyphicon-pencil"></i> REGISTRATION </div>

        <div class="panel-body">

            <div class="form-group input-group">
                <span class="input-group-addon"> Username </span>
                <input id="username" type="text" class="form-control" name="username" ng-model="user.username">
            </div>

            <div class="form-group input-group">
                <span class="input-group-addon"> Password&nbsp; </span>
                <input id="password" type="password" class="form-control" name="password" ng-model="user.password">
            </div>

            <div class="form-group input-group">
                <span class="input-group-addon"> Nome&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
                <input id="nome" type="text" class="form-control" name="nome" ng-model="user.nome">
            </div>

            <div class="form-group input-group">
                <span class="input-group-addon"> Cognome&nbsp; </span>
                <input id="cognome" type="text" class="form-control" name="cognome" ng-model="user.cognome">
            </div>   
            
            <div class="form-group input-group">
                <span class="input-group-addon"> Data di nascita </span>
                <input id="dataNascita" type="date" class="form-control" name="dataNascita" ng-model="user.dataNascita">
            </div> 

            <div class="form-group">
                <span class="input-group-addon"> Sesso: </span>
                <select name="singleSelect" class="form-control" ng-model="user.sesso">
                  <option value="M">Maschio</option>
                  <option value="F">Femmina</option>
                </select>
            </div>
            
            <button class="btn btn-default" ng-click="registraUser()" ng-disabled="(!user.username.length > 0) || (!user.password.length > 0)"> Registrati </button> <br/>
            <a href="login.php" style="float: right"> Torna a login </a>

        </div>

    </div>
    
</div>
    
<?php include_once '../grafica/masterpage_down.php'; ?>