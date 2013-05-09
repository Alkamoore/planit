$(document).ready(function(){	
	
	var events = [
	{	Title: "This is an event!", Date: new Date("5/23/2013") },
	{	Title: "This is also an event", Date: new Date("5/12/2013") },
	{	Title: "This is an event too!", Date: new Date("5/20/2013") }
	];

	
	$('#calendar').datepicker({
        inline: true,
        firstDay: 1,
        showOtherMonths: true,
        dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
		beforeShowDay: function(date) {
			var result = [true, '', null];
			var matching = $.grep(events, function(event) {
				return event.Date.valueOf() === date.valueOf();
			});
			if (matching.length)
			{
				result = [true, 'highlight', null];
			}
			return result;
		},
		onSelect: function(dateText) {
			var date, 
				selectedDate = new Date(dateText),
				i = 0, 
				event = null;
			while (i < events.length && !event) 
			{
				date = events[i].Date;
				
				if (selectedDate.valueOf() === date.valueOf())
				{
					event = events[i];
				}
				i++;
			}
			if (event) {
				alert(event.Title);
			}
		}
	});
	
    $('#planner').multiDatesPicker({
        firstDay: 1,
        showOtherMonths: true,
        dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
    });

 });
 