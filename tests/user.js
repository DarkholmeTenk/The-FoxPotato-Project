app.controller('userController', function($scope, $location, $http, $mdToast, $mdSidenav, Upload) {
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
		$scope.data.cUser = $scope.data.name;
		$scope.editing = true;
	}

	$scope.cancelEdit = function()
	{
		$scope.editing = false;
	}

	$scope.saveEdit = function()
	{
		var sendData = {displayName:$scope.data.cUser};
		console.log(sendData);
		Upload.upload({
			url: "php/namechange.php",
			data: sendData}).then(function success(response){
				var d = response.data;
				if(d.success)
				{
					$scope.data.name=d.newName;
					$mdToast.showSimple("Name changed successfully");
				}
				else
				{
					console.log(d)
					$mdToast.showSimple("Name change failed - " + d.reason);
				}
			})
					
		$scope.editing = false;
	}
})
