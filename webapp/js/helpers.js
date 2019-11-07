

/* 
    Return a string representation of a job's status 
    Expects string variables that either contain a date or a null 
*/
function get_job_status(start,stop) {
    if (start == null) {
        return 'Not Started';
    }
    else if (stop == null) {
        return 'In progress'; 
    }
    else if(stop != null) {
        return 'Finished';
    }
    else {
        return 'Unknown status';
    }
}

/* return array of hours,mins,seconds given hours */
function breakdown_time(hours) {
    var hours_fl = Math.floor(hours);
    var mins = (hours - hours_fl) * 60;
    var mins_fl = Math.floor(mins);

    return {'hours':hours_fl, 'mins':mins_fl};
}

function format_date(date) {
    var day = date.getDate();
    var mon = date.getMonth()+1; //zero index so +1
    var yr = date.getFullYear();

    return mon + '-' + day + '-' + yr;
}