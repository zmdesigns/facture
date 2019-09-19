#include <string>

class Workstation {
public:
    Workstation(int workstation_id, std::string server_address);
    bool clock_in(int employee_id, int job_id);
    bool clock_out();
private:
    std::string json_string(int action);
    int p_id = 0;
    //the currently logged in employee, null otherwise
    int p_employee_id = 0;
    //the currently running job, null otherwise
    int p_job_id = 0;
    std::string p_server_address = "";
  
};
