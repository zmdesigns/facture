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

    /* assign values for later */
    p_job_id = job_id;
    p_employee_id = employee_id;

    /* create map to pass to json generator function */
    std::map<std::string,std::string> args;
    args["task"] = "11"; //task=new log
    args["employee_id"] = std::to_string(p_employee_id);
    args["workstation_id"] = std::to_string(p_id);
    args["job_id"] = std::to_string(job_id);
    args["action"] = std::to_string(action);

    /* create the json string */
    std::string json_str = server->json_req_string(args);

    /* Connect to server and send POST request */
    if (server->make_request(json_str)) {
        return true;
    }
    else {
        return false;
    }
}

//searches database for last clock action from employee/job
//use 0 for either variable for wildcard
int Workstation::last_clock_action(int employee_id, int job_id) {

    std::map<std::string,std::string> args;
    args["task"] = "12"; //task=last clock
    args["employee_id"] = std::to_string(employee_id);
    args["workstation_id"] = std::to_string(p_id);
    args["job_id"] = std::to_string(job_id);
    args["action"] = "3";

    std::string json_str = server->json_req_string(args);
    server->make_request(json_str);
}

void Workstation::recv_data() {
    std::string data = server->recv_data();

    Serial.write(data.c_str());
}