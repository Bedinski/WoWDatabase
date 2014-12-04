"use strict";

//to-do:
//==============================
//* set autofocus for simple search and adv search
//*  
//==============================

$(document).ready(function() { 
	$("#resultTable").tablesorter(); 
});


$("th").mouseup(function(){
	var name = $(this).html();
	var hasUp = name.search("up");
	var hasDown = name.search("down");

	$("th").css("background-color","#c0c0c0");
	$(this).css("background-color","#6699FF");
	$("th").html() = $("th").html().replace(/\<[\d\D]*\>/g,"");
	
	if(hasUp!=-1)
		$(this).html(name+"<span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></span>");
	else
		$(this).html(name+"<span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></span>");

});

//collapse and expand the Advanced Search option headers
$(".innerOptionHeader").on("click",function(){
	var divs = document.getElementsByTagName("div");
	for(var i=0; i<divs.length; i++) {
		if(this==divs[i]) {
			var isVisible = $(divs[i+1]).is(':visible');
			var header = divs[i].innerHTML;
			
			if(isVisible) {
				$(divs[i]).css({"border-bottom-left-radius":"10px","border-bottom-right-radius":"10px"});
				header = header.replace("[-]","[+]");
			}
			else {
				$(divs[i]).css({"border-bottom-left-radius":"0px","border-bottom-right-radius":"0px"});
				header = header.replace("[+]","[-]");
			}
			$(divs[i]).html(header);
			$(divs[i+1]).toggle();	//hide the options associated with this header
		}
	}
});



//turn mouse into a clicker symbol when hovering over Advanced Search option headers
$(".innerOptionHeader").hover(function(){
	this.style.cursor="pointer";
});



//toggle visibility of the Type Options menus
$("#itemTypeSelection label").on("mouseup",function(){
	var nameOfClicked = $(this).html().replace(/\<[\d\D]*\>/g,"");
	var idOfClicked;

	if(nameOfClicked!="ALL TYPES")
		idOfClicked = "#"+nameOfClicked.toLowerCase().replace(" ","")+"Options";
	else
		idOfClicked = "#subtypePlaceholder";
	
	var listOfSubtypeOptions = document.getElementsByClassName("subtypeOption");
	var idOfVisible="null";
	
	if($("#subtypePlaceholder").is(':visible')) {
		idOfVisible = "#subtypePlaceholder";
	}
	else {
		for(var i=0; i<listOfSubtypeOptions.length; i++) {
			if($(listOfSubtypeOptions[i]).is(':visible')) {
				idOfVisible = "#"+$(listOfSubtypeOptions[i]).attr('id');
			}
		}
	}

	if(idOfClicked!=idOfVisible) {
		$(idOfClicked).show();
		$(idOfVisible).children(".innerOptionSubBox").children(".checkbox").children("label").children("input").prop("checked",false);
		$(idOfVisible).children(".innerOptionSubBox").children(".checkbox").children("label").children(".checkAll").prop("checked",true);
		$(idOfVisible).hide();
	}
});



//form validation for numeric fields
$(".numericOnly").focusout(function(){
	var input = this.value;

	if(input.match(/[\d]{1,3}/g)) {
		$(this).addClass("validated");
		$(this).removeClass("validationError");
	}
	else {
		$(this).addClass("validationError");
		$(this).removeClass("validated");
		alert("Incorrect input format! Please only enter numbers.");
	}

	checkForInputErrors();
});



//create one radio button for a selection of checkboxes
$("label").mouseup(function(){
	var isCheckAll = $(this).children("input").hasClass("checkAll");
	var hasCheckAll = $(this).parent().children("label").children("input").hasClass("checkAll");

	if(isCheckAll) {
		$(this).parent().children("label").children("input").prop("checked",false);
	}
	else if(hasCheckAll) {
		$(this).parent().children("label").children(".checkAll").prop("checked",false);
	}
});



//validate user input in Pricing Range fields
$("#priceRangeMin,#priceRangeMax").focusout(function() {
	var input = this.value;

	if(input.match(/[\d]*g[\d]*s[\d]*c/g)) {
		$(this).addClass("validated");
		$(this).removeClass("validationError");
	}
	else {
		$(this).addClass("validationError");
		$(this).removeClass("validated");
		alert("Incorrect input format! Please read the instructions in the Pricing Range section.");
	}

	checkForInputErrors();
});



//disables Advanced Search button if there are input errors; enables it if none are found
function checkForInputErrors() {
	var errors = document.getElementsByClassName("validationError");
	if(errors.length==0) {
		$("#advancedSearchButton").prop("disabled",false);
	}
	else {
		$("#advancedSearchButton").prop("disabled",true);
	}
}



//set all Advanced Search options to default
function setDefaultOptions() {
	var inputList = document.getElementsByTagName("input");
	var checkAllList = document.getElementsByClassName("checkAll");
	var defaultList = document.getElementsByClassName("default");

	for(var i=0; i<inputList.length; i++) {
		$(inputList[i]).prop("checked",false);
	}
	for(var i=0; i<checkAllList.length; i++) {
		$(checkAllList[i]).prop("checked",true);
	}
	for(var i=0; i<defaultList.length; i++) {
		$(defaultList[i]).prop("checked",true);
	}
}