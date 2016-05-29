app.controller('schemaController', function($scope, $location, $http, $mdToast, $cacheFactory, $mdSidenav) {
	$scope.userID=-1;
	$scope.schemaData = {};
	$scope.schemas=null;
	$scope.lastQuery=null;
	$scope.lastOrder=null;
	$scope.orderParam='-score';
	$scope.loading=true;
	$scope.page=0;
	$scope.pages;

	$scope.logIn = function(user)
	{
		$scope.userID=user;
	}

	$scope.updateData = function() {
		$scope.loading = true;
		var p = "p="+$scope.page;
		var q = ($scope.lastQuery != null ? $scope.lastQuery + "&": "") + p;
		var order = $scope.lastOrder != null ? "o="+$scope.lastOrder+"&"+q : q;
		console.log("Req="+order);
		$http.get(url+"json/schema.php?"+order).then(function success(response){
			console.log("Data retrieved");
			$scope.schemaData = response;
			console.log(response)
			$scope.schemas=response.data.data;
			$scope.numPages = Math.ceil(response.data.totalSize / response.data.perPage);
			$scope.pages = [];
			var page = response.data.page;
			$scope.page = page;
			for(var i = Math.max(0,page-5); i < Math.min(page+5,$scope.numPages); i++)
			{
				$scope.pages[i] = {
					selected:i==page,
					number:i+1,
					p:i,
				};
			}
			$scope.loading = false;
		},
		function error(response){})
	}

	$scope.search = function(query)
	{
		if(query == $scope.lastQuery) return;
		$scope.lastQuery = query;
		$scope.page = 0;
		$scope.updateData();
	}

	$scope.changePage = function(newPage)
	{
		if(newPage == $scope.page) return;
		$scope.page = newPage;
		$scope.updateData();
	}

	$scope.getPages = function()
	{
		return $scope.pages;
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

app.controller('uploadController', function($scope, $location, $http, Upload, $mdToast) {
	$scope.userID=null;
	$scope.schemaFile = null;
	$scope.data={description:"",imgurAlbum:""}

	$scope.logIn = function(user)
	{
		$scope.userID=user;
	}

	$scope.fileChange = function(file)
	{
		$scope.schemaFile = file[0];
		$scope.$apply();
	}

	$scope.doFileUpload = function()
	{
		var d = $scope.verify();
		if(d[0] == false)
			$mdToast.showSimple("Error - " + d[1]);
		else
		{
			var data = {schemaToUpload:$scope.schemaFile,desc:$scope.data.description,imgur:$scope.data.imgurAlbum};
			Upload.upload({
				url:"php/upload.php",
				data:data}).then(function success(response){
					var d = response.data;
					if(d.success)
						$scope.toast("Uploaded successfully! Refresh for changes");
					else
					{
						console.log(d)
						$scope.toast("Upload failed - " + d.reason);
					}
				})
		}
	}

	$scope.verify = function()
	{
		if($scope.schemaFile == null) return [false,"No file found"];
		if($scope.data.description.length < 5) return [false,"Description too short"];
		var ial = $scope.data.imgurAlbum.length;
		if(ial > 0 && ial <5) return [false,"Imgur album id appears too short"];
		if(ial >= 7) return [false,"Imgur album id appears too long"];
		return true;
	}

})
