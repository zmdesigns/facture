#include <string>
#include <WiFi101.h>

class JTServer {

public:
    JTServer(std::string server_address);
    bool make_request(std::string json_string);
    std::string json_req_string(int task, int employee, int workstation, int job, int action);    
    std::string recv_data();
private:
    WiFiClient* client;
    std::string address;
};