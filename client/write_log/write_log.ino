#include "arduino_secrets.h"
#include <SPI.h>
#include <WiFi101.h>
#include <Arduino_JSON.h>
/* Arduino defines max and min which collides with std. let's ignore arduino's def */
#undef max
#undef min

#include "workstation.h"

char ssid[] = SECRET_SSID;        
char pass[] = SECRET_PASS;    
int keyIndex = 0;   //only for WEP
bool stopped = false; //for loop to know if client has been stopped
int status = WL_IDLE_STATUS;
Workstation* wrkstn; 
WiFiClient client;

void setup() {
    //Initialize serial and wait for port to open
    Serial.begin(9600);
    while (!Serial) {
        ; // wait for serial port to connect. Needed for native USB port only
    }

    // check for the presence of the shield
    if (WiFi.status() == WL_NO_SHIELD) {
        Serial.println("WiFi shield not present");
        // don't continue
        while (true);
    }

    // attempt to connect to WiFi network
    while (status != WL_CONNECTED) {
        Serial.print("Attempting to connect to SSID: ");
        Serial.println(ssid);
        status = WiFi.begin(ssid, pass);

        // wait 10 seconds for connection
        delay(10000);
    }
    Serial.println("Connected to wifi");
    printWiFiStatus();

    Serial.println("\nStarting connection to server...");

    wrkstn = new Workstation(1, "jtrkr.zackmdesigns.com");
}

void loop() {
    // if there are incoming bytes available
    // from the server, read them and print them
    while (client.available()) {
        char c = client.read();
        Serial.write(c);
    }

    //if there is input from the serial read it
    handle_serial_input(recv_serial_input());

    // if the server's disconnected, stop the client
    if (!client.connected() && !stopped) {
        Serial.println();
        Serial.println("disconnecting from server.");
        client.stop();
        stopped = true;
    }
}

char recv_serial_input() {
    if (Serial.available() > 0) {
        // read the incoming byte:
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
            Serial.println('Clocking out..');
            wrkstn->clock_action(66,66,2);
        }
        else if (rcvd_char == '3') {
            Serial.println('Checking last clock action..');
            wrkstn->last_clock_action(66,66);
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
