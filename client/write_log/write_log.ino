#include "arduino_secrets.h"
#include <SPI.h>
/* Arduino defines max and min which collides with std. let's ignore arduino's def */
#undef max
#undef min
#include "workstation.h"
#include "Nextion.h"
#include "display.h"
  
int keyIndex = 0;   //only for WEP
bool stopped = false; //for loop to know if client has been stopped
Workstation* wrkstn;

void setup() {

    //init display and attach callbacks in display.h
    nexInit();
    attach_callbacks();

    //wrkstn = new Workstation(1, "jtrkr.zackmdesigns.com");
    //wrkstn->get_job_list();
}

void loop() {
    //if there is input from the serial read it
    //handle_serial_input(recv_serial_input());

    //handle response from server
    //wrkstn->recv_data();

    //listen and respond to display events
    nexLoop(nex_listen_list);
}

char recv_serial_input() {
    if (Serial.available() > 0) {

        char rcvd_char = Serial.read();
        return rcvd_char;
    }
    return '0';
}

void handle_serial_input(char rcvd_char) {
    if (rcvd_char != '0'  && rcvd_char != '\n') {
        Serial.print("I received: '");
        Serial.print(rcvd_char);
        Serial.println("'");

        if (rcvd_char == '1') {
            Serial.println("Clocking in..");
            wrkstn->clock_action(66,66,1);
        }
        else if(rcvd_char == '2') {
            Serial.println("Clocking out..");
            wrkstn->clock_action(66,66,2);
        }
        else if (rcvd_char == '3') {
            Serial.println("Checking last clock action..");
            wrkstn->last_clock_action(66,66);
        }
        else if (rcvd_char == '4') {
            Serial.println("Getting job list..");
            wrkstn->get_job_list();
        }
    }
}


void printWiFiStatus() {
    // print the SSID of the network
    Serial.print("SSID: ");
    Serial.println(WiFi.SSID());

    // print local ip
    IPAddress ip = WiFi.localIP();
    Serial.print("IP Address: ");
    Serial.println(ip);

    // print the received signal strength
    long rssi = WiFi.RSSI();
    Serial.print("signal strength (RSSI):");
    Serial.print(rssi);
    Serial.println(" dBm");
}