#include "arduino_secrets.h"
#include <SPI.h>
#include <WiFi101.h>
#include <Arduino_JSON.h>
#include "Nextion.h"
/* Arduino defines max and min which collides with std. let's ignore arduino's def */
#undef max
#undef min

#include "workstation.h"

char ssid[] = SECRET_SSID;        
char pass[] = SECRET_PASS;    
int keyIndex = 0;   //only for WEP
bool stopped = false; //for loop to know if client has been stopped
int status = WL_IDLE_STATUS;
std::string numpad_value = "";
Workstation* wrkstn;

//Display
NexButton bClockIn = NexButton(0, 3, "bClockIn"); //page-id,component-id,component-name
NexButton bNum1 = NexButton(1, 2, "bNum1");
NexButton bNum2 = NexButton(1, 3, "bNum2");
NexButton bNum3 = NexButton(1, 4, "bNum3");
NexButton bNum4 = NexButton(1, 5, "bNum4");
NexButton bNum5 = NexButton(1, 6, "bNum5");
NexButton bNum6 = NexButton(1, 7, "bNum6");
NexButton bNum7 = NexButton(1, 8, "bNum7");
NexButton bNum8 = NexButton(1, 9, "bNum8");
NexButton bNum9 = NexButton(1, 10, "bNum9");

NexText tNumpad = NexText(1, 13, "tNumpad");

NexTouch *nex_listen_list[] = { &bClockIn, 
                                &bNum1,
                                &bNum2,
                                &bNum3,
                                &bNum4,
                                &bNum5,
                                &bNum6,
                                &bNum7,
                                &bNum8,
                                &bNum9,
                                NULL };

void bClockInPopCallback(void *ptr) {
    Serial.println("Clock-in button pressed!");
    bClockIn.setText("Clocked!");
}

void bNum1PopCallback(void *ptr) { update_numpad_text('1'); }
void bNum2PopCallback(void *ptr) { update_numpad_text('2'); }
void bNum3PopCallback(void *ptr) { update_numpad_text('3'); }
void bNum4PopCallback(void *ptr) { update_numpad_text('4'); }
void bNum5PopCallback(void *ptr) { update_numpad_text('5'); }
void bNum6PopCallback(void *ptr) { update_numpad_text('6'); }
void bNum7PopCallback(void *ptr) { update_numpad_text('7'); }
void bNum8PopCallback(void *ptr) { update_numpad_text('8'); }
void bNum9PopCallback(void *ptr) { update_numpad_text('9'); }

void update_numpad_text(char c) {
    if (numpad_value.length() < 6) {
        numpad_value += c;
        tNumpad.setText(numpad_value.c_str());
    }
}

void setup() {
    //Initialize serial and wait for port to open
    Serial.begin(9600);
    //initialize display
    nexInit();
    // Register the pop event callback function of the components
    bClockIn.attachPop(bClockInPopCallback, &bClockIn);
    bNum1.attachPop(bNum1PopCallback, &bNum1);
    bNum2.attachPop(bNum2PopCallback, &bNum2);
    bNum3.attachPop(bNum3PopCallback, &bNum3);
    bNum4.attachPop(bNum4PopCallback, &bNum4);
    bNum5.attachPop(bNum5PopCallback, &bNum5);
    bNum6.attachPop(bNum6PopCallback, &bNum6);
    bNum7.attachPop(bNum7PopCallback, &bNum7);
    bNum8.attachPop(bNum8PopCallback, &bNum8);
    bNum9.attachPop(bNum9PopCallback, &bNum9);

    while (!Serial) {
        ; //wait for serial port to connect. Needed for native USB port only
    }

    // check for the presence of the shield
    if (WiFi.status() == WL_NO_SHIELD) {
        Serial.println("WiFi shield not present");
        //don't continue
        while (true);
    }

    //attempt to connect to WiFi network
    while (status != WL_CONNECTED) {
        Serial.print("Attempting to connect to SSID: ");
        Serial.println(ssid);
        status = WiFi.begin(ssid, pass);

        //wait 10 seconds for connection
        delay(10000);
    }
    Serial.println("Connected to wifi");
    printWiFiStatus();

    Serial.println("\n 1: Clock in \n 2: Clock out \n 3: Check last clock action");

    wrkstn = new Workstation(1, "jtrkr.zackmdesigns.com");
    
}

void loop() {
    //if there is input from the serial read it
    handle_serial_input(recv_serial_input());

    //handle response from server
    wrkstn->recv_data();

    //handle touch events for display
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
