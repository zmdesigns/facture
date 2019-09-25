#include <string>

class JTServer {

public:
    JTServer();
    static bool make_request(std::string address, std::string file, std::string json_string);
    static std::string json_req_string(int employee, int workstation, int job, int action);    
};