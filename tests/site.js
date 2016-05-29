app.controller('siteController', function($scope, $location, $http, $mdToast, $mdSidenav) {
	$scope.userID=-1;

	$scope.logIn = function(user)
	{
		$scope.userID=user;
	}

	$scope.toast = function(text)
	{
		$mdToast.showSimple(text)
	}

	$scope.openMenu = function()
	{
		$mdSidenav('left').toggle()
	}
})
