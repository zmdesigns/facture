#include "workstation.h";

Workstation::Workstation(int workstation_id, std::string server_address) : p_id(workstation_id) {
    server = new JTServer(server_address);
}

bool Workstation::clock_action(int employee_id, int job_id, int action) {
    /* sanity checks */
    if (employee_id < 0 || employee_id > 999) {
        return false;
    }
    if (job_id < 0 || job_id > 999) {
        return false;
    }
    if (action < 0 || action > 9) {
        return false;
    }

    /* assign values to generate json string for server */
    p_job_id = job_id;
    p_employee_id = employee_id;

    /* create the json string */
    std::string json_str = server->json_req_string("new", p_employee_id,p_id,p_job_id,action);

    /* Connect to server and send POST request */
    if (server->make_request("log.php",json_str)) {
        return true;
    }
    else {
        return false;
    }
}

//searches database for last clock action from employee/job
//use 0 for either variable for wildcard of that var in search
int Workstation::last_clock_action(int employee_id, int job_id) {
    std::string json_str = server->json_req_string("last_log", employee_id,p_id,job_id,3);
    server->make_request("log.php",json_str);
}


void Workstation::recv_data() {
    std::string data = server->recv_data();

    Serial.write(data.c_str());
}