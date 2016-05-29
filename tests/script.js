var app = angular.module('mainApp',['ngMaterial']);
var url = "http://tardismod.com/schema/";

app.config(function($locationProvider) {
	$locationProvider.html5Mode(true).hashPrefix('#');
});


