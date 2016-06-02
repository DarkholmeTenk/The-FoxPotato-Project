app.controller('siteController', function($scope, $location, $http, $mdDialog, $mdToast, $mdSidenav) {
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

	$scope.openNewSchemaMenu = function()
	{
		$mdSidenav('right').toggle()
	}

	$scope.openGuide = function()
	{
		console.log("Guide!");
		$mdDialog.show({
			controller: DialogController,
			templateUrl: 'template/guide.html',
			parent: angular.element(document.body),
			clickOutsideToClose: true
		})
	}
})

function DialogController($scope, $mdDialog) {
	$scope.hide = function() {
		$mdDialog.hide();
	};
	$scope.cancel = function() {
		$mdDialog.cancel();
	};
	$scope.answer = function(answer) {
		$mdDialog.hide(answer);
	};
}
