$(document).ready(function(){	


	
	$('#calendar').datepicker({
        inline: true,
        firstDay: 1,
        showOtherMonths: true,
        dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
	});
	
    $('#planner').multiDatesPicker({
        firstDay: 1,
        showOtherMonths: true,
        dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
    });
	
	$("select").multiselect({
		noneSelectedText: "What Time?",
		selectedText: "What Time?",
	});
 
 });