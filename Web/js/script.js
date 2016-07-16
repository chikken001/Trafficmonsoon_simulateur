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
	var date_split = date.split("/"),
        time_split = date_split[2],
		year_split = time_split.split(" "),
		year = year_split[0],
		time = year_split[1],
		day = date_split[0],
		month = date_split[1];
	
	return year+'-'+month+'-'+day+' '+time ;
}