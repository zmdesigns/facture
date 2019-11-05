#include "workstation.h"

std::vector<std::string>seperate(std::string str,char s, char e); //defined in include/helpers
void add_job(int job, int product); //defined in display_functions.h

Workstation::Workstation(int workstation_id, std::string server_address) : p_id(workstation_id) {
    server = new JTServer(server_address);
}

bool Workstation::clock_action(int employee_id, int job_id, int product_id, int action) {
    /* sanity checks */
    if (employee_id < 0) {
        return false;
    }
    if (job_id < 0) {
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
    args["product_id"] = std::to_string(product_id);
    args["action"] = std::to_string(action);

    /* Connect to server and send POST request */
    if (server->make_request(args)) {
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

    server->make_request(args);
}

bool Workstation::get_job_list() {

    std::map<std::string,std::string> args;
    args["task"] = "30";

    server->make_request(args);    
}

void Workstation::recv_data() {
    std::string data = server->recv_data();

    if (data.size() > 0) {

        std::vector<std::string> jobs = seperate(data,'{','}');

        if (!jobs.empty()) {
            for(std::vector<std::string>::iterator it=jobs.begin();it != jobs.end(); ++it) {
                std::string job = *it;
                JSONVar j = JSON.parse(job.c_str());

                std::string job_id = (const char*)j["job_id"];
                std::string product_id = (const char*)j["product_id"];

                //add job to job list used by display
                add_job(std::stoi(job_id), std::stoi(product_id));
            }
        }
        else {
            Serial.println(data.c_str());
        }
    }
}

void Workstation::send_data() {
    
}