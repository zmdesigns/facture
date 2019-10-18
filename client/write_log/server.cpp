#include "server.h"
#include "arduino_secrets.h" 

JTServer::JTServer(std::string server_address) : address(server_address) {
    client = new WiFiClient();
}

bool JTServer::make_request(std::string json_string) {
    size_t json_len = json_string.length();

    std::string post_header = "POST /include/api.php HTTP/1.1";
    std::string host_header = "Host: "+address;
    std::string user_header = "User-Agent: ArduinoWiFi/1.1";
    std::string content_type_header = "Content-Type: application/json";
    std::string content_len_header = "Content-Length: " + std::to_string(json_len);
    std::string connection_type = "Connection: keep-alive";

    if (client->connect(address.c_str(), 80)) {
        Serial.println("\nConnected.");

        //print post request to serial
        /*
        Serial.println("Sending POST Request:");
        Serial.println(post_header.c_str());
        Serial.println(host_header.c_str());
        Serial.println(user_header.c_str());
        Serial.println(content_type_header.c_str());
        Serial.println(content_len_header.c_str());
        Serial.println(connection_type.c_str());
        Serial.println();
        Serial.println(json_string.c_str());
        */
      
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
    }

    
    if (!data.empty()) {
        //check for a quoted string in response, if not return full response
        size_t body_start = data.find("{");
        size_t body_end = data.rfind("}");

        if (body_start != string::npos && body_start != body_end) {

            //quoted string found, copy it from rest of response and return it
            size_t len = body_end - body_start + 1;
            char buffer[len+1];
            data.copy(buffer,len,body_start);
            buffer[len]  ='\0'; //end of string

            data = buffer;
        }
    }

    return data;
}