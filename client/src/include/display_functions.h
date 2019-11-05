#ifndef __DISPLAY_FUNC_H__
#define __DISPLAY_FUNC_H__

#include <string>
#include "Nextion.h"
#include "helpers.h"
#include "network.h"
#include "../job.h"
/* Functions that modify display objects 

   Sorted by page that the function operates on
*/

/* data that need to be stored between pages */
std::string numpad_txt = "";
std::vector<Job*> job_list;
int job_list_index = 0;
int selected_job_index = -1;
std::string selected_network = ssid;
bool caps = false;
int pending_clock_action = 0;
unsigned long last_clock_action_time = 0;

/* time variables */
WiFiUDP ntpUDP;
NTPClient timeClient(ntpUDP,"pool.ntp.org",-28800,120000);
unsigned long last_update = 0;
unsigned long update_interval = 60000;
void update_clock(bool force);
/* Page 0 (Settings) */


/* Page 1 (Numpad) */

//retrun numpad text from object on screen
std::string get_numpad_text(NexText* numpad) {
    char buffer[10] = { };
    numpad->getText(buffer, sizeof(buffer));
    std::string numpad_txt(buffer);

    return numpad_txt;
}

//manage employee clock in code on numpad screen
void update_numpad_text(NexText* numpad, char c,bool clear_text=false) {

    std::string numpad_txt = get_numpad_text(numpad);

    if (clear_text) {
        numpad_txt = "";
    }
    else if (numpad_txt.length() < 6) {
        numpad_txt += c;
    }

    numpad->setText(numpad_txt.c_str());
}



/* Page 2 (Job List) */

//add a job string to job_list
void add_job(int job_id, int product_id) {
    job_list.push_back(new Job(job_id, product_id));
}

//populate job list buttons
void update_job_buttons(std::vector<NexButton*>* job_btns) {
    if (!job_list.empty()) {
        for(int i=0;i<=3;++i) {
            //job_list_index is index of top button on screen in job_list vector
            if (job_list_index + i >= job_list.size()) {
                job_list_index = 0;
                i = 0;
            }
            job_btns->at(i)->setText(job_list.at(job_list_index + i)->job_string().c_str());
        }
    }   
}

//When a arrow button is pressed on job page
//this moves job_list_index up or down to display
//jobs above or below currently displayed jobs
void move_job_index(int amount) {
    if (job_list_index + amount < job_list.size()) {
        if (job_list_index + amount >= 0) {
            job_list_index += amount;
        }
        else {
            job_list_index = 0;
        }
    }
}

//returns text of button in job_btns at button_index
std::string get_job_button_text(std::vector<NexButton*>* job_btns, int btn_index) {
    char buffer[20] = { };
    job_btns->at(btn_index)->getText(buffer,sizeof(buffer));
    std::string btn_txt = buffer;

    return btn_txt;
}

//clock in
void select_job(std::vector<NexButton*>* job_btns, int btn_index) {
    selected_job_index = job_list_index + btn_index;
    //workstation clock action
    pending_clock_action = 1;
    sendCommand("page 3");
    last_clock_action_time = timeClient.getEpochTime();
    std::string cmd = "tClockStatus.txt=\"Clocked in at "+ format_time(last_clock_action_time) + " " + job_list.at(selected_job_index)->job_string() + "\"";
    sendCommand(cmd.c_str()); 
    update_clock(true);
    //toggle button touch enable/disable
    sendCommand("vis 1,1");
    sendCommand("vis 3,0");
}

/* Page 3 (Clock in/out) */
void clock_out() {
    if (selected_job_index >= 0) {
        pending_clock_action = 2;
        last_clock_action_time = timeClient.getEpochTime();
        std::string cmd = "tClockStatus.txt=\"Clocked out at " + format_time(last_clock_action_time) + "\"";
        sendCommand(cmd.c_str());
        update_clock(true);
        //toggle button touch enable/disable
        sendCommand("vis 1,0");
        sendCommand("vis 3,1");
    }
}

void update_clock(bool force=false) {
    if (last_update == 0 || millis() - last_update > update_interval || force == true) {
        timeClient.update();
        Serial.println("updating time label..");
        std::string cmd = "tTime.txt=\"" + format_time(timeClient.getEpochTime()) + "\"";
        sendCommand(cmd.c_str());
        last_update = millis();
    }
}

/* Page 4 (Network List) */

//update each network button text with a network in range
void update_network_buttons(std::vector<NexButton*>* network_btns) {
    //update number of networks in range text
    sendCommand("tNetworks.txt=\"Scanning..\"");
    std::vector<std::string> networks = network_list();
    std::string cmd = "tNetworks.txt=\"Networks in Range:" + std::to_string(networks.size()) + "\"";
    sendCommand(cmd.c_str());

    //iterate network and network button vectors
    //change button text to SSID of network until it runs out of buttons
    for(int i=0;i < network_btns->size();++i) {
        if (i < networks.size()) {
            network_btns->at(i)->setText(networks.at(i).c_str());
        }
        else {
            //reset button text to blank if no more networks
            network_btns->at(i)->setText("");
        }
    }
}

//update network status text, txt_obj is text object to update with network status
void update_network_strength(std::string s_ssid, NexText* txt_obj) {
    if (s_ssid != "") {
        txt_obj->setText("Getting network strength..");
        std::string strength = network_signal(s_ssid);
        std::string msg = s_ssid + " - Strength:" + strength;
        txt_obj->setText(msg.c_str());
    }
}

//return text from button at index and turn button background color green
std::string get_network_button_text(std::vector<NexButton*>* network_btns, int btn_index, NexText* txt_obj) {
    char buffer[30] = { };
    network_btns->at(btn_index)->getText(buffer,sizeof(buffer));
    std::string s_ssid = buffer;

    update_network_strength(s_ssid,txt_obj);
    
    network_btns->at(btn_index)->Set_background_color_bco(1024);

    return s_ssid;
}

void update_network_status(int status) {
    std::string s_ssid = ssid;
    std::string msg = "tStatus.txt=\"";
    //update status text
    if (status == WL_CONNECTED) {
        msg += "Connected to " + s_ssid;
    }
    else if (WL_AP_CONNECTED) {
        msg += "Connected in AP mode to " + s_ssid;
    }
    else if (WL_AP_LISTENING) {
        msg += "Connected in AP Listen mode to " + s_ssid;
    }
    else if (WL_NO_SHIELD) {
        msg += "No WiFi shield found";
    }
    else if (WL_IDLE_STATUS) {
        msg += "Connecting..";
    }
    else if (WL_NO_SSID_AVAIL) {
        msg += "No SSID available";
    }
    else if (WL_SCAN_COMPLETED) {
        msg += "Network scan complete";
    }
    else if (WL_CONNECT_FAILED) {
        msg += "Connection to " + s_ssid + " failed";
    }
    else if (WL_CONNECTION_LOST) {
        msg += "Connection to " + s_ssid + " lost";
    }
    else if (WL_DISCONNECTED) {
        msg += "Disconnected from " + s_ssid;
    }
    msg += "\"";

    sendCommand(msg.c_str());
}

/* Page 5 (Password Input) */

//boolean toggles when caps button is pressed
//press_letter() checks cap value and
//updates field with appropriate capitalization
void toggle_caps_letter_buttons(NexButton* cap_btn, std::vector<NexButton*>* letter_btns) {
    if (!caps) {
        //toggle color of caps button to let user know it's pressed
        cap_btn->Set_background_color_bco(1024);
    }
    else {
        cap_btn->Set_background_color_bco(50712);
    }
    //toggle caps boolean
    caps = !caps;

    //toggle button text for all letters
    for(int i=0;i<letter_btns->size();++i) {
        char buffer[1] = { 0 };
        letter_btns->at(i)->getText(buffer,sizeof(buffer));
        char to_cap = buffer[0];

        to_cap = toggle_caps(to_cap, caps);

        buffer[0] = to_cap;
        letter_btns->at(i)->setText(buffer);
    }
}

void press_letter(char letter) {
    //if caps button is pressed: capitalize letter/turn number->symbol
    if (caps) {
        letter = toggle_caps(letter,true);
    }
    //add letter pressed to text field
    std::string field = "t0.txt+=\"";
    field.push_back(letter);
    field.push_back('\"');
    sendCommand(field.c_str());
}

//remove last character from text field
void backspace(NexText* txt_field) {
    char buffer[40] = { };
    txt_field->getText(buffer, sizeof(buffer));
    std::string pass_text(buffer);

    if (pass_text.size() > 0) {
        pass_text.pop_back(); //erase last character
        txt_field->setText(pass_text.c_str());
    }
}

void enter_password(NexText* txt_field) {
    char buffer[40] = { };
    txt_field->getText(buffer, sizeof(buffer));

    //set pass to password field text
    //pass = buffer;
    //reset caps variable
    caps = false;

    //go back to settings page
    sendCommand("page 0");
}

#endif