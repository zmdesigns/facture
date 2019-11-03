#include "server.h"
#include "arduino_secrets.h" 

JTServer::JTServer(std::string server_address) : address(server_address) {
    client = new WiFiClient();
}

bool JTServer::make_request(std::map<std::string,std::string> args) {
    //prepare POST body
    std::string json_string = json_req_string(args);
    size_t json_len = json_string.length();
    //prepare POST headers
    std::string post_header = "POST /include/api.php HTTP/1.1";
    std::string host_header = "Host: "+address;
    std::string user_header = "User-Agent: ArduinoWiFi/1.1";
    std::string content_type_header = "Content-Type: application/json";
    std::string content_len_header = "Content-Length: " + std::to_string(json_len);
    std::string connection_type = "Connection: keep-alive";

    if (client->connect(address.c_str(), 80)) {
        Serial.println("\nConnected.");
      
        // POST Request to server
        client->println(post_header.c_str());
        client->println(host_header.c_str());
        client->println(user_header.c_str());
        client->println(content_type_header.c_str());
        client->println(content_len_header.c_str());
        client->println(connection_type.c_str());
        client->println();
        client->println(json_string.c_str());
        
        return true;
    }
    
    return false;
}

std::string JTServer::json_req_string(std::map<std::string,std::string> args) {
    using std::string;

    string json_string = "{";
    //convert map into json string
    for(std::map<string,string>::iterator it=args.begin(); it!=args.end(); ++it) {
        json_string += "\"" + it->first + "\":\"" + it->second + "\",";
    }
    //replace end comma with curley bracket to end json string
    json_string.back() = '}';
    
    return json_string;
}

std::string JTServer::recv_data() {
    using namespace std;

    string data = "";
    //read incoming data from server and save it in data variable
    while (client->available()) {
        char c = client->read();
        data += c;
        delay(1); //needed to allow data time to be recieved/not break loop
    }

    if (!data.empty()) {
        //check for a bracket in response, if not return last line of respone
        size_t body_start = data.find("{");
        size_t body_end = data.rfind("}");

        if (body_start == string::npos || body_end == string::npos) {
            //no brackets, find the last line of response
            body_start = data.rfind("\n");
            body_end = data.size() - 1;

            if (body_start == string::npos) {
                //if there is no newline character, something went wrong
                //return whatever response was recieved for troubleshooting
                return data;
            }
        }
        //copy the part of the response we care about
        size_t len = body_end - body_start + 1;
        char buffer[len+1];
        data.copy(buffer,len,body_start);
        buffer[len]  ='\0'; //end of string

        data = buffer;
    }
    return data;
}