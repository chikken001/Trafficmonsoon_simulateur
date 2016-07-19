$(document).ready(initialiser);

Array.prototype.inArray = function(p_val) {
	var l = this.length;
	for(var i = 0; i < l; i++) {
		if(this[i] == p_val) {
			return true;
		}
	}
	return false;
}

function initialiser()
{	
	
}

function dateFRtoEN(date)
{
	var date_split = date.split("/");
	var time_split = date_split[2];
	
	if(time_split !== undefined)
	{
		var	year_split = time_split.split(" ");
		var	year = year_split[0];
		var	time = year_split[1];
		var	day = date_split[0];
		var	month = date_split[1];
	
		return year+'-'+month+'-'+day+' '+time ;
	}
	
	return date ;
}