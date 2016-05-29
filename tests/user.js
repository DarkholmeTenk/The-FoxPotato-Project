app.controller('userController', function($scope, $location, $http, $mdToast, $mdSidenav) {
	$scope.data;
	$scope.auth;
	$scope.cUser;
	$scope.editing;

	$scope.logIn = function(data)
	{
		console.log(data);
		$scope.data = data;
	}

	$scope.edit = function()
	{
		$scope.cUser = $scope.data.name;
		$scope.editing = true;
	}

	$scope.cancelEdit = function()
	{
		$scope.editing = false;
	}

	$scope.saveEdit = function()
	{
		$scope.editing = false;
	}
})
