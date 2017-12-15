
myApp.controller('thankYouController', ['$scope', '$http', function ($scope, $http) {
			
			$scope.gotoexitpoll = function(schoolid, electionid, voteid)
			{
				document.getElementById("schoolidpost").value = schoolid;
				document.getElementById("electionidpost").value = electionid;
				document.getElementById("voteidpost").value = voteid;
				document.getElementById("gotopage").submit();
			}
			
}]);
