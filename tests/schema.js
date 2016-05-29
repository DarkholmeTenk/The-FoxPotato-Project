app.controller('schemaController', function($scope, $location, $http, $mdToast, $cacheFactory) {
	$scope.userID=-1;
	$scope.schemaData = {};
	$scope.schemas=null;
	$scope.lastQuery=null

	$scope.logIn = function(user)
	{
		$scope.userID=user;
	}

	$scope.updateData = function(getR) {
		$scope.lastQuest=getR;
		console.log("Updating with " + getR)
		$http.get(url+"json/schema.php?"+getR).then(function success(response){
			console.log("Data retrieved");
			$scope.schemaData = response;
			$scope.schemas=response.data.data;
		},
		function error(response){})
	}

	$scope.vote = function(itemID,currentScore,newScore)
	{
		if(currentScore == newScore)
			newScore = 0;
		$http.get(url+"vote.php?schemaID="+itemID+"&score="+newScore).then(function success(response){
			data = response.data;
			if(data.success)
			{
				$scope.schemas[itemID] = data.newState[itemID]
				$scope.toast("Vote successful!")
			}
			else
			{
				$scope.toast("Vote failed: " + data.reason)
			}
		},
		function error(response){})
		//$scope.toast("TEST - " + newScore);
	}

	$scope.toast = function(text)
	{
		$mdToast.showSimple(text)
	}

	$scope.imgur = {
		cid:'',
		data:{},

		getAlbumInfo : function (albumid)
		{
			var img = this;
			if(!albumid) return;
			if(img.data[albumid])
			{
				return(img.data[albumid]);
			}
			console.log("imgur request " + albumid);
			var x = $http.get("https://api.imgur.com/3/album/"+albumid,{headers:{'Authorization':'Client-ID '+this.cid}}).then(
			function success(response)
			{
				var images=[];
				response.data.data.images.forEach(function(image)
				{
					if(!image.nsfw)
					{
						var imageData={};
						imageData.id=image.id;
						imageData.title=image.title;
						imageData.desc=image.description;
						if(!imageData.title)
							imageData.title=image.description;
						images.push(imageData);
						console.log(images);
					}
				});
				img.data[albumid] = images;
				return images;
			},
			function error(response){});
			this.data[albumid] = x;
			return x;
		}
	}

	$scope.edit = function(id)
	{
		$scope.schemas[id].edit=true;
	}

	$scope.save = function(id)
	{
		$scope.schemas[id].edit=false;
		var text = $scope.schemas[id]
		$http.post("update.php",{data:text}).then(
		function success(response)
		{
			var data = response.data;
			if(data.success)
				$scope.toast("Updated successfully");
			else
			{
				console.log(data);
				$scope.toast("Update failed - " + data.reason);
				if(data.oldState != null)
					$scope.schemas[data.oldState.id] = data.oldState;
			}

		})
	}
})
