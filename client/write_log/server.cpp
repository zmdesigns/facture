#include "server.h"
#include "arduino_secrets.h" 
#include <WiFi101.h>

bool JTServer::makeRequest(std::string address, std::string file, std::string json_string) {
    size_t json_len = json_string.length();
    WiFiClient client;

    std::string post_header = "POST /include/"+file+" HTTP/1.1";
    std::string host_header = "Host: "+address;
    std::string content_type_header = "Content-Type: application/json";
    std::string content_len_header = "Content-Length: " + json_len;
    std::string connection_type = "Connection: close";

    if (client.connect(address.c_str(), 80)) {
        // POST Request
        client.println(post_header.c_str());
        client.println(host_header.c_str());
        client.println(content_type_header.c_str());
        client.println(content_len_header.c_str());
        client.println(connection_type.c_str());
        client.println();
        client.println(json_string.c_str());
    }
}