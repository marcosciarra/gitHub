<?php
/**
 * Created by PhpStorm.
 * User: clickale
 * Date: 04/04/17
 * Time: 16.48
 */
?>

<head>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

    <!-- import angularjs -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.8/angular.min.js"></script>
    <!-- import angularjs controller-->
    <script type="text/javascript" src="controller/stampaController.js"></script>
    <!--jspdf-->
    <script type="text/javascript" src="lib/jspdf.min.js"></script>
    <script type="text/javascript" src="lib/jspdf.plugin.autotable.js"></script>
    <!--bootstrap-->
    <link rel="stylesheet" href="css/bootstrap.css">

    <title>Stampa</title>
</head>

<body ng-app="myApp" ng-controller="stampaController">


<div id="container" class="col-md-10 col-md-offset-1">

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Cognome</th>
                <th>Eta</th>
                <th>Citta</th>
                <th>Sesso</th>
                <th>Note</th>
                <th>Millesimi</th>
            </tr>
        </thead>
        <tbody>
            <tr ng-repeat="d in data">
                <td>{{d.nome}}</td>
                <td>{{d.cognome}}</td>
                <td>{{d.eta}}</td>
                <td>{{d.citta}}</td>
                <td>{{d.sesso}}</td>
                <td>{{d.note}}</td>
                <td>{{d.millesimi}}</td>
            </tr>
        </tbody>
    </table>

    <button class="btn" ng-click="scarica()">Scarica Excel</button>

</div>

</body>
</html

