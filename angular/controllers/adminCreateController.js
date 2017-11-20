
myApp.controller('adminCreateController', ['$scope', '$http', '$compile', function ($scope, $http) {
	
	var RegisterData = "{\"operation\" : \"VALIDATELOGIN\" , \"schoolusername\" : \"" + $scope.schoolid + "\" }";

	console.log("JSON sent to server:" + RegisterData);
	
	

	$http({
		method: 'POST',
		url: './mysql-admin.php',
		data: RegisterData
	})
		.then(
			function successCallback(response) {
				console.log('server says:' + response.data);
				
				if(response.data.error)
				{
					// THERE IS AN ERROR
					
					var errout = "ERROR: UNKNOWN SERVER ERROR!";
					if(response.data.errorcode == 5)
					{
						// NOT LOGGED IN
						errout = "ERROR: NOT LOGGED IN!";
						window.location.href = "../logout.php";
					}
					else if(response.data.errorcode == 6)
					{
						// NOT ENROLLED IN SCHOOL
						errout = "ERROR: NOT ENROLLED IN SELECTED SCHOOL!";
						window.location.href = "../schools.php";
					}
				}
				else
				{
					// NO ERRORS
					addquestion();
				}
			},
			function errorCallback(response) {
				console.log(response.statusText);
				console.log("HTTP status code:" + response.status);
			})
			
			$scope.createelection = function(schoolid)
			{
				var ename = document.getElementById("electionname").value;
				
				var json = "{ \"operation\" : \"CREATEELECTION\", \"schoolusername\" : \"" + schoolid + "\", \"electionname\" : \"" + ename + "\" , \"electiondata\" : " + getJSON() + " }";
				
				$http({
				method: 'POST',
				url: './mysql-elections.php',
				data: json
				})
				.then(
					function successCallback(response) {
						console.log('server says:' + response.data);
						
						$scope.temp = response.data;
						
					},
					function errorCallback(response) {
						console.log(response.statusText);
						console.log("HTTP status code:" + response.status);
					})
			}
			
			
}]);

// GLOBAL QUESTION COUNTER TO PREVENT QUESTIONS FROM OVERWRITING EACH OTHER
var qcount = 0;

var addquestion = function()
	{
		var questionDiv = document.getElementById("questionDiv");
		
		var newDiv = document.createElement("div");
		newDiv.setAttribute("style", "border:1px solid; padding:15px; margin:15px");
		var questionID = "q" + qcount;
		newDiv.setAttribute("id", questionID);
		
		var qName = document.createElement("input");
		qName.setAttribute("type", "text");
		qName.setAttribute("placeholder", "Question Title");
		qName.setAttribute("required", "");
		var nameID = questionID + "name";
		qName.setAttribute("id", nameID);
		newDiv.appendChild(qName);
		
		var qDelete = document.createElement("button");
		qDelete.innerHTML = "Delete Question";
		var deleteJS = "deletequestion(\"" + questionID + "\")";
		qDelete.setAttribute("onclick", deleteJS);
		newDiv.appendChild(qDelete);
		
		newDiv.appendChild(document.createElement("br"));
		
		var qList = document.createElement("ul");
		var listID = questionID + "list";
		qList.setAttribute("id", listID);
		
		newDiv.appendChild(qList);
		
		newDiv.appendChild(document.createElement("br"));
		
		var addOptionButton = document.createElement("button");
		addOptionButton.innerHTML = "Add Option";
		var buttonJS = "addoption(\"" + questionID + "\")";
		addOptionButton.setAttribute("onclick", buttonJS);
		
		newDiv.appendChild(addOptionButton);
		
		questionDiv.appendChild(newDiv);
		
		addoption(questionID);
		qcount++;
	}
	
	var addoption = function(qid)
	{
		var optionList = document.getElementById(qid + "list");
		
		var li = document.createElement("li");
		
		var optionID = qid + "o" + optionList.childElementCount;
		
		var qOption = document.createElement("input");
		qOption.setAttribute("type", "text");
		qOption.setAttribute("placeholder", "Option Text");
		qOption.setAttribute("required", "");
		qOption.setAttribute("id", optionID);
		qOption.setAttribute("style", "margin-top: 15px");
		
		var optionDeleteButton = document.createElement("button");
		optionDeleteButton.innerHTML = "Delete Option";
		var deleteJS = "deleteoption(\"" + optionID + "\")";
		optionDeleteButton.setAttribute("onclick", deleteJS);
		
		li.appendChild(qOption);
		li.appendChild(optionDeleteButton);
		
		optionList.appendChild(li);
	}
	
	var deleteoption = function(oid)
	{
		var option = document.getElementById(oid);
		var optionlist = option.parentElement.parentElement;
		var optioncount = optionlist.childElementCount;
		
		if(optioncount < 2)
		{
			alert("You cannot delete the only option in a question!");
		}
		else
		{
			optionlist.removeChild(option.parentElement);
		}
	}
	
	var deletequestion = function(qid)
	{
		var question = document.getElementById(qid);
		var questionlist = question.parentElement;
		var questioncount = questionlist.childElementCount;
		
		if(questioncount < 2)
		{
			alert("You cannot delete the only question in an election!");
		}
		else
		{
			questionlist.removeChild(question);
		}
	}
	
	var getJSON = function()
	{
		var ret = "[";
		
		var questiondiv = document.getElementById("questionDiv");
		var questions = questiondiv.children;
		
		for (var i = 0; i < questions.length; i++)
		{
			// FOR EACH QUESTION
			var q = "{ \""
			var name = document.getElementById(questions[i].id + "name").value;
			q = q + name;
			q = q + "\" : [";
			
			var optionslist = document.getElementById(questions[i].id + "list");
			var options = optionslist.children;
			
			for (var j = 0; j < options.length; j++)
			{
				// FOR EACH OPTION
				
				var optionText = options[j].getElementsByTagName("input")[0].value;
				
				var r = "\"" + optionText + "\",";
				q = q + r;
			}
			q = q.substring(0, q.length - 1);
			
			q = q + "] },"
			ret = ret + q;
		}
		
		ret = ret.substring(0, ret.length - 1);
		
		ret = ret + "]";
		return ret;
	}
	
	var temp = function()
	{
		var text = getJSON();
		var p = document.createElement("p");
		
		var ename = document.getElementById("electionname").value;
		
		var ret = "{ \"OPERATION\" : \"TEST\", \"name\" : \"" + ename + "\" , \"data\" : " + text + "}";
		
		p.innerHTML = ret;
		document.body.append(p);
	}
