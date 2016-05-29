app.controller('imgurController', function($scope, $location, $http, $mdToast) {
	$scope.display="WAITING"
	$scope.images=[]
	$scope.req
	$scope.cid=''
	$scope.album = function() {
		$http.get("https://api.imgur.com/3/album/"+$scope.req, { headers: {
			'Authorization': 'Client-ID ' + $scope.cid
		}}).then(function success(response)
		{
			$scope.images=[]
			$scope.display=response
			//$scope.display += "<br>"
			response.data.data.images.forEach(function(entry) {
				if(!entry.nsfw)
					$scope.images.push(entry.id)
			})
			
		}, function error(response){})
	}
})
