

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
    var secs = Math.round((mins - mins_fl) * 60);

    //secs is rounded, if this causes secs=60,mins=60 fix for readability
    if (secs == 60) {
        mins_fl++;
        secs = 0;
        if (mins_fl == 60) {
            hours_fl++;
            mins_fl = 0;
        }
    }

    return {'hours':hours_fl, 'mins':mins_fl, 'secs':secs};
}