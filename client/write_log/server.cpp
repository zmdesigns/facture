#include "server.h"
#include "arduino_secrets.h" 
#include <WiFi101.h>

bool JTServer::make_request(std::string address, std::string file, std::string json_string) {
    size_t json_len = json_string.length();
    WiFiClient client;

    std::string post_header = "POST /include/"+file+" HTTP/1.1";
    std::string host_header = "Host: "+address;
    std::string content_type_header = "Content-Type: application/json";
    std::string content_len_header = "Content-Length: " + std::to_string(json_len);
    std::string connection_type = "Connection: close";

    if (client.connect(address.c_str(), 80)) {
        Serial.println("\nConnected.");

        //print post request to serial
        Serial.println("Sending POST Request:");
        Serial.println(post_header.c_str());
        Serial.println(host_header.c_str());
        Serial.println(content_type_header.c_str());
        Serial.println(content_len_header.c_str());
        Serial.println(connection_type.c_str());
        Serial.println();
        Serial.println(json_string.c_str());
      
        // POST Request to server
        client.println(post_header.c_str());
        client.println(host_header.c_str());
        client.println(content_type_header.c_str());
        client.println(content_len_header.c_str());
        client.println(connection_type.c_str());
        client.println();
        client.println(json_string.c_str());

        Serial.println("\n..Done!");
        
        return true;
    }
    
    return false;
}

std::string JTServer::json_req_string(int employee, int workstation, int job, int action) {
    using namespace std;
    
    return "{\"task\":\"new\",\"employee_id\":\""+to_string(employee)+
                          "\",\"workstation_id\":\""+to_string(workstation)+
                          "\",\"job_id\":\""+to_string(job)+
                          "\",\"action\":\""+to_string(action)+"\"}";
}