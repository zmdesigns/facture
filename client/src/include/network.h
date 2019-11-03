#ifndef __NETWORK_H__
#define __NETWORK_H__

#include <vector>
#include "../arduino_secrets.h"

char ssid[] = SECRET_SSID;
char pass[] = SECRET_PASS;

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

int wifi_connect() {
    int status = WiFi.status();

    // check for the presence of the shield
    if (status == WL_NO_SHIELD) {
        //don't continue
        return status;
    }
    
    int max_tries = 2;
    int tries = 0;
    //attempt to connect to WiFi network if not connected
    while (status != WL_CONNECTED && tries < max_tries) {
        status = WiFi.begin(ssid, pass);

        //wait 10 seconds for connection
        delay(10000);
        tries++;
    }

    return status;
}

#endif