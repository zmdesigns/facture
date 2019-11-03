#ifndef __SERVER_H__
#define __SERVER_H__


#include <string>
#include <map>
#include <WiFi101.h>

class JTServer {

public:
    JTServer(std::string server_address);
    bool make_request(std::map<std::string,std::string> args);
    std::string recv_data();
private:
    std::string json_req_string(std::map<std::string,std::string> args);
    WiFiClient* client;
    std::string address;
};

#endif