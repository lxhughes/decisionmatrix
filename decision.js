// init
$(function() {

	// FORMATTING
	
	// On click of input, remove the class secretinput if it's there (make it appear like a standard input)
	$("input.secretinput").focusin(function(){
		$(this).removeClass("secretinput");
		$(this).addClass("placeholder");
	});
	$("input").focusout(function(){
		if ($(this).hasClass("placeholder")){
			$(this).addClass("secretinput");
			$(this).removeClass("placeholder");
		}
	});

	// GRADING
	var grades = new Array();

	// Get max grade score
	currmax = 0;
	
	$('.grade').each(function(){
		if ($(this).html() != "TBA") {
			if ($(this).html() > currmax){
				currmax = parseFloat($(this).html());
			}
		}
	});
		
	// Go through and convert to letter grade
	$('.grade').each(function(){
		
		if ($(this).html() == "TBA") return false;
		
		var rawscore = $(this).html();
		
		var curvscore = rawscore/currmax;
		
		var lettergrade = lettergradeify(curvscore);
		//console.log(lettergrade);
				
		var lettergradespan = $("<span/>").html(lettergrade).addClass("grade grade"+lettergrade.substr(0,1));
		
		var myparent = $(this).parent().append(lettergradespan);
		
		$(this).hide();
		
	});

});

function lettergradeify(score){
	var lettergrade = score;
	
	if (score > .97) lettergrade = "A+";
	else if (score > .93) lettergrade = "A+";
	else if (score > .9) lettergrade = "A";
	else if (score > .87) lettergrade = "A";
	else if (score > .83) lettergrade = "A-";
	else if (score > .8) lettergrade = "A-";
	else if (score > .77) lettergrade = "B+";
	else if (score > .73) lettergrade = "B+";
	else if (score > .7) lettergrade = "B";
	else if (score > .67) lettergrade = "B";
	else if (score > .63) lettergrade = "B-";
	else if (score > .6) lettergrade = "B-";
	else if (score > .57) lettergrade = "C+";
	else if (score > .53) lettergrade = "C+";
	else if (score > .5) lettergrade = "C";
	else if (score > .47) lettergrade = "C";
	else if (score > .43) lettergrade = "C-";
	else if (score > .4) lettergrade = "C-";
	else if (score > .37) lettergrade = "D+";
	else if (score > .33) lettergrade = "D+";
	else if (score > .3) lettergrade = "D";
	else if (score > .27) lettergrade = "D";
	else if (score > .23) lettergrade = "D-";
	else if (score > .2) lettergrade = "D-";
	else if (score > .17) lettergrade = "F+";
	else if (score > .13) lettergrade = "F+";
	else if (score > .1) lettergrade = "F";
	else if (score > .07) lettergrade = "F";
	else if (score <= .07) lettergrade = "F-";
	
	return lettergrade;

}