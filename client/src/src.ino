#include <SPI.h>
#include <WiFi101.h>
#include <WiFiUdp.h>
#include <NTPClient.h>
/* Arduino defines max and min which collides with std. let's ignore arduino's def */
#undef max
#undef min
#include <string>
#include "Nextion.h"
#include "workstation.h"
#include "display.h"

int status = WL_IDLE_STATUS; //status of wifi connection
int keyIndex = 0;   //only for WEP
bool stopped = false; //for loop to know if client has been stopped
Workstation* wrkstn;


void setup() {
    Serial.begin(9600);
    //init display and attach callbacks in display.h
    nexInit();
    attach_callbacks();

    sendCommand("thup=1"); //wake up from sleep on touch
    //sendCommand("thsp=30"); //interval in seconds for no touch=sleep
    sendCommand("dim=50"); //backlight level

    //connect to network
    status = wifi_connect();
    if (status == WL_CONNECTED) {
        //get time
        timeClient.begin();
        //connect to web app
        wrkstn = new Workstation(1, "jtrkr.zackmdesigns.com");
        wrkstn->get_job_list();
        //go to clockin/out screen
        sendCommand("page 3");
        //hide clock out button
        sendCommand("vis 1,0");
    }
}

void loop() {
    //check for disconnect, connect if needed
    status = wifi_connect();

    //handle any responses from web app
    wrkstn->recv_data();

    //update time
    update_clock();

    //send request to web app
    //if there is a pending clock action
    if (pending_clock_action > 0) {
        if (selected_job_index >= 0) {
            Job* pending_job = job_list.at(selected_job_index);
            int employee = stoi(numpad_txt);
            std::string msg = "action:"+std::to_string(pending_clock_action)+", employee:"+std::to_string(employee)+", job:"+pending_job->job_string();
            Serial.println(msg.c_str());
            wrkstn->clock_action(employee,pending_job->job_id,pending_job->product_id,pending_clock_action);
            pending_clock_action = 0;
        }
    }

    //listen and respond to display events
    nexLoop(nex_listen_list);
}