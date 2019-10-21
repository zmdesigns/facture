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

const std::vector<std::string> explode(const std::string& s, const char& c)
{
	std::string buff{""};
	std::vector<std::string> v;
	
	for(auto n:s)
	{
		if(n != c) buff+=n; else
		if(n == c && buff != "") { v.push_back(buff); buff = ""; }
	}
	if(buff != "") v.push_back(buff);
	
	return v;
}

std::vector<std::string>seperate(std::string str,char s, char e) {
    std::vector<std::string> v;
    bool started = false;
    std::string buffer = "";

    for(std::string::iterator it=str.begin(); it != str.end(); ++it) {
        char c = *it;
        if (!started) {
            if (c == s) {
                started = true;
            }
        }
        if (started) {
            buffer += c;

            if (c == e) {
                v.push_back(buffer);
                buffer = "";
                started = false;
            }
        }
    }
    return v;
}

//note: seems to be missing a job 
void Workstation::recv_data() {
    std::string data = server->recv_data();

    if (data.size() > 0) {
        //Serial.write(data.c_str());
        Serial.println("\n---------START-------------\n");

        std::vector<std::string> jobs = seperate(data,'{','}');

        if (!jobs.empty()) {
            for(std::vector<std::string>::iterator it=jobs.begin();it != jobs.end(); ++it) {
                std::string job = *it;
                JSONVar j = JSON.parse(job.c_str());
                Serial.println(j["job_id"]);
            }
        }
        else {
            Serial.write(data.c_str());
        }

        Serial.println("\n----------END------------\n");
    }
}