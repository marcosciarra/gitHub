<!DOCTYPE html>
<html ng-app="engine" ng-controller="engineController">

<head>
    <link href="./lib/bootstrap.css" rel="stylesheet" type="text/css" media="all">
    <!-- import angularjs -->
    <script type="text/javascript" src="./lib/angular.min.js"></script>
    <script type="text/javascript" src="./lib/angular-animate.js"></script>

    <script type="text/javascript" src="./controller/indexController.js"></script>


    <!-- fontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

    <?php
    /**
     * Created by PhpStorm.
     * User: marco
     * Date: 12/12/18
     * Time: 22.23
     */

    date_default_timezone_set('Europe/Rome');
    header("Content-type: text/html; charset=latin1");

    require_once 'conf/costanti.php';
    require_once 'lib/pdo.php';
    ?>
</head>

<body>
<div ng-controller="engineController" data-ng-init="init()">

    <div class="row">
        <div class="col-md-3">
            <button class="btn btn-primary">aaa</button>
        </div>
    </div>
</div>
</body>
