#include <string>
#include "server.h"

class Workstation {
public:
    Workstation(int workstation_id, std::string server_address);
    bool clock_action(int employee_id, int job_id, int action);
    int last_clock_action(int employee_id, int job_id);
    bool get_job_list();
    void recv_data();
private:
    int p_id = 0;
    int p_employee_id = 0;
    int p_job_id = 0;
    JTServer* server;
};
