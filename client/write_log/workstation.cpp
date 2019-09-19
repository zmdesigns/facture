
#include "workstation.h";
#include "server.h";

Workstation::Workstation(int workstation_id, std::string server_address) {
    
    p_id = workstation_id;
    p_server_address = server_address;
}

bool Workstation::clock_in(int employee_id, int job_id) {
    /* sanity checks */
    if (employee_id < 0 || employee_id > 999) {
        return false;
    }
    if (job_id < 0 || job_id > 999) {
        return false;
    }

    /* assign values to generate json string for server */
    p_job_id = job_id;
    p_employee_id = employee_id;
    std::string json_str = json_string(2);

    if (JTServer::makeRequest(p_server_address,"log.php",json_str)) {
        return true;
    }
    else {
        //reset clock-in values
        p_job_id = 0;
        p_employee_id = 0;
        return false;
    }
}

bool Workstation::clock_out() {

}

std::string json_string(int action) {
    if (action < 0 || action > 999) {
        return "";
    }

    return "{\"task\":\"new\",\"employee_id\":\"8\",\"workstation_id\":\"8\",\"job_id\":\"8\",\"action\":\"2\"}";
}