/*
  POST a request to write a log entry into database
*/


#include <SPI.h>
#include <WiFi101.h>
#include <Arduino_JSON.h>
#include "arduino_secrets.h" 
#include "workstation.h"

char ssid[] = SECRET_SSID;        
char pass[] = SECRET_PASS;    
int keyIndex = 0;   //only for WEP

int status = WL_IDLE_STATUS;
//web server where app is running
char server[] = "jtrkr.zackmdesigns.com";
//json formatted string that contains data to be entered into database
const char* json_data = "{\"task\":\"new\",\"employee_id\":\"8\",\"workstation_id\":\"8\",\"job_id\":\"8\",\"action\":\"1\"}";
//content length in header requires the size
size_t json_len = strlen(json_data);
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

    Workstation wrkstn = new Workstation(1, "jtrkr.zackmdesigns.com");
    wrkstn->clock_in(66,66);
/*
  if (client.connect(server, 80)) {
    Serial.println("connected to server");
    // POST Request
    client.println("POST /include/log.php HTTP/1.1");
    client.println("Host: jtrkr.zackmdesigns.com");
    client.println("Content-Type: application/json");
    client.print("Content-Length: ");
    client.println(json_len);
    client.println("Connection: close");
    client.println();
    client.println(json_data);
  }
  */
}

void loop() {
    // if there are incoming bytes available
    // from the server, read them and print them
    while (client.available()) {
        char c = client.read();
        Serial.write(c);
    }

  // if the server's disconnected, stop the client
  if (!client.connected()) {
    Serial.println();
    Serial.println("disconnecting from server.");
    client.stop();

    while (true);
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
