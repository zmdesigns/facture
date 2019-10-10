

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

