#include <vector>
#include <WiFi101.h>

std::string ssid = "";
std::string pass = "";

//returns a vector of SSIDs of networks in range
//empty vector if none in range or can't use wifi
std::vector<std::string> network_list() {
    int num = WiFi.scanNetworks();

    std::vector<std::string> networks;
    for(int net=0;net < num;net++) {
        networks.push_back(WiFi.SSID(net));
    }

    return networks;
}

//return signal strength given SSID
std::string network_signal(std::string ssid) {
    int signal = -999;
    int num = WiFi.scanNetworks();

    for(int net=0;net < num;net++) {
        if (WiFi.SSID(net) == ssid) {
            signal = WiFi.RSSI(net);
        }
    }
    
    if (signal >= -70) {
        return "Excellent";
    }
    else if (signal >= -85) {
        return "Good";
    }
    else if (signal >= -100) {
        return "Fair";
    }
    else if (signal >= -110) {
        return "Poor";
    }
    else {
        return "None";
    }
}

int wifi_connect(const char* ssid, const char* pass) {
    int status = WL_IDLE_STATUS;

    // check for the presence of the shield
    if (WiFi.status() == WL_NO_SHIELD) {
        //don't continue
        while (true);
    }
    
    //attempt to connect to WiFi network
    while (status != WL_CONNECTED) {
        status = WiFi.begin(ssid, pass);

        //wait 10 seconds for connection
        delay(10000);

    }
    return status;
}