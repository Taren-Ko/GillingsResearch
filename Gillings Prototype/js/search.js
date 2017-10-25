var RESULTS_PER_PAGE = 5;
var resultsArray = [];

function search(){
	var quickSearch = document.getElementById("quickSearch").value;
	var authors = document.getElementById("authors").value;
	var abstract = document.getElementById("abstract").value;
	var keywords = document.getElementById("keywords").value;
	var title = document.getElementById("title").value;

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			resultsArray = JSON.parse(this.responseText);
			showPage(1);
		}
	};
	xhttp.open("GET", "php/search/search.php?quickSearch=" + quickSearch + "&authors=" + authors + "&abstract=" + abstract + "&keywords=" + keywords + "&title=" + title, true);
	xhttp.send();
}

function showPage(pageNumber){
	if(pageNumber < 1 || pageNumber > Math.ceil(resultsArray.length / RESULTS_PER_PAGE)){
		return false;
	}

	var startIndex = ((pageNumber - 1) * RESULTS_PER_PAGE);
	var endIndex = pageNumber * RESULTS_PER_PAGE;

	var htmlCode = "";
	for(var i = startIndex; i < Math.min(endIndex, resultsArray.length); i++){
		htmlCode += "<div class=\"card blue-grey darken-1\">";
		htmlCode += "<div class=\"card-content white-text\">";
		htmlCode += "<span class=\"card-title\">" + resultsArray[i]["title"] + "</span>";
		htmlCode += "<p>Published: " + resultsArray[i]["date"] + "</p>";
		htmlCode += "<div class=\"divider\"></div>";
		htmlCode += "<p>Abstract: " + resultsArray[i]["body"] + "</p>";
		htmlCode += "Authors:";
		for(var j = 0; j < resultsArray[i]["authors"].length; j++){
			htmlCode += "<div class=\"chip\">" + resultsArray[i]["authors"][j] + "</div>";
		}
		htmlCode += "</div>";
		htmlCode += "<div class=\"card-action right-align\">";
		htmlCode += "<a href=\"" + resultsArray[i]["link"] + "\"" +
			" class=\"blue-text\">View Article</a>";
		htmlCode += "</div>";
		htmlCode += "</div>";
	}
	document.getElementById("results").innerHTML = htmlCode;

	htmlCode = "<li class=\"waves-effect\" onclick=\"showPage(" + (pageNumber - 1) + ");\"><a" +
	" href=\"#!\"><i" +
		" class=\"material-icons\">chevron_left</i></a></li>";
	htmlCode += "<li class=\"active blue\"><a href=\"#!\">" + pageNumber + "</a></li>";
	for(var i = (pageNumber + 1); i < Math.min(resultsArray.length, (pageNumber + 5)); i++){
		htmlCode += "<li class=\"waves-effect\" onclick=\"showPage(" + i + ");\"><a" +
			" href=\"#!\">" + i + "</a></li>";
	}
	htmlCode += "<li class=\"waves-effect\" onclick=\"showPage(" + (pageNumber + 1) + ");\"><a href=\"#!\"><i class=\"material-icons\">chevron_right</i></a></li>";
	document.getElementById("pageNav").innerHTML = htmlCode;
}

function searchFromURLParams(){
	document.getElementById("quickSearch").value = getParam("quickSearch");
	document.getElementById("authors").value = getParam("authors");
	document.getElementById("abstract").value = getParam("abstract");
	document.getElementById("keywords").value = getParam("keywords");
	document.getElementById("title").value = getParam("title");

	if(document.getElementById("quickSearch").value.length > 0){
		document.getElementById("quickSearch").parentNode.getElementsByTagName("label")[0].className += " active";
	}
	if(document.getElementById("authors").value.length > 0){
		document.getElementById("authors").parentNode.getElementsByTagName("label")[0].className += " active";
	}
	if(document.getElementById("abstract").value.length > 0){
		document.getElementById("abstract").parentNode.getElementsByTagName("label")[0].className += " active";
	}
	if(document.getElementById("keywords").value.length > 0){
		document.getElementById("keywords").parentNode.getElementsByTagName("label")[0].className += " active";
	}
	if(document.getElementById("title").value.length > 0){
		document.getElementById("title").parentNode.getElementsByTagName("label")[0].className += " active";
	}
	search();
}

function getParam(param){
	var str = window.location.toString();
	if(str.indexOf("param_" + param + "=") == -1){
		return "";
	}

	str = str.substring(str.indexOf("param_" + param + "=") + param.length + 7);
	if(str.indexOf("&param_") > -1){
		str = str.substring(0, str.indexOf("&param_"));
	}
	return str;
}

function visitSearchPage(){
	var quickSearch = document.getElementById("quickSearch").value;
	var authors = document.getElementById("authors").value;
	var abstract = document.getElementById("abstract").value;
	var keywords = document.getElementById("keywords").value;
	var title = document.getElementById("title").value;
	window.location = "result.html?param_quickSearch=" + quickSearch + "&param_authors=" + authors + "&param_abstract=" + abstract + "&param_keywords=" + keywords + "&param_title=" + title;
}