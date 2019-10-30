#include <string>
#include <vector>
#include "Nextion.h"
#include "include\network.h"


//component objects
// home screen
NexButton bClockIn = NexButton(3, 2, "bClockIn"); //page-id,component-id,component-name
NexButton bSettings = NexButton(3, 4, "bSettings");

// settings screen
NexButton bNetworks = NexButton(0, 5, "bNetworks");
NexButton bPassword = NexButton(0, 6, "bPassword");
NexButton bConnect = NexButton(0, 8, "bConnect");
NexButton bDone = NexButton(0, 7, "bDone");
NexText tStatus = NexText(0, 4, "tStatus");
NexPage pSettingsPage = NexPage(0, 0, "page0");

// networks screen
NexButton bScan = NexButton(4, 8, "bScan");
NexButton bNetwork1 = NexButton(4, 2, "bNetwork1");
NexButton bNetwork2 = NexButton(4, 3, "bNetwork2");
NexButton bNetwork3 = NexButton(4, 5, "bNetwork3");
NexButton bNetwork4 = NexButton(4, 6, "bNetwork4");
NexButton bNetwork5 = NexButton(4, 9, "bNetwork5");
NexButton bNetwork6 = NexButton(4, 10, "bNetwork6");
NexButton bNetwork7 = NexButton(4, 11, "bNetwork7");
NexButton bNetwork8 = NexButton(4, 12, "bNetwork8");
NexButton bCancel = NexButton(4, 7, "bCancel");
NexText tNetworks = NexText(4, 4, "tNetworks");

// password screen
NexText tField = NexText(5, 41, "t0");
NexButton ba = NexButton(5, 2, "b0");
NexButton bb = NexButton(5, 3, "b1");
NexButton bc = NexButton(5, 4, "b2");
NexButton bd = NexButton(5, 5, "b3");
NexButton be = NexButton(5, 6, "b4");
NexButton bf = NexButton(5, 7, "b5");
NexButton bg = NexButton(5, 8, "b6");
NexButton bh = NexButton(5, 9, "b7");
NexButton bi = NexButton(5, 10, "b8");
NexButton bj = NexButton(5, 11, "b9");
NexButton bk = NexButton(5, 12, "b10");
NexButton bl = NexButton(5, 13, "b11");
NexButton bm = NexButton(5, 14, "b12");
NexButton bn = NexButton(5, 15, "b13");
NexButton bo = NexButton(5, 16, "b14");
NexButton bp = NexButton(5, 17, "b15");
NexButton bq = NexButton(5, 18, "b16");
NexButton br = NexButton(5, 19, "b17");
NexButton bs = NexButton(5, 20, "b18");
NexButton bt = NexButton(5, 21, "b19");
NexButton bu = NexButton(5, 22, "b20");
NexButton bv = NexButton(5, 23, "b21");
NexButton bw = NexButton(5, 24, "b22");
NexButton bx = NexButton(5, 25, "b23");
NexButton by = NexButton(5, 26, "b24");
NexButton bz = NexButton(5, 27, "b25");
NexButton b0 = NexButton(5, 28, "b26");
NexButton b1 = NexButton(5, 29, "b27");
NexButton b2 = NexButton(5, 30, "b28");
NexButton b3 = NexButton(5, 31, "b29");
NexButton b4 = NexButton(5, 32, "b30");
NexButton b5 = NexButton(5, 33, "b31");
NexButton b6 = NexButton(5, 34, "b32");
NexButton b7 = NexButton(5, 35, "b33");
NexButton b8 = NexButton(5, 36, "b34");
NexButton b9 = NexButton(5, 37, "b35");
NexButton bCaps = NexButton(5, 38, "b36");
NexButton bBackspace = NexButton(5, 39, "b37");
NexButton bEnter = NexButton(5, 40, "b39");

// numpad screen
NexButton bNum1 = NexButton(1, 2, "bNum1");
NexButton bNum2 = NexButton(1, 3, "bNum2");
NexButton bNum3 = NexButton(1, 4, "bNum3");
NexButton bNum4 = NexButton(1, 5, "bNum4");
NexButton bNum5 = NexButton(1, 6, "bNum5");
NexButton bNum6 = NexButton(1, 7, "bNum6");
NexButton bNum7 = NexButton(1, 8, "bNum7");
NexButton bNum8 = NexButton(1, 9, "bNum8");
NexButton bNum9 = NexButton(1, 10, "bNum9");
NexButton bClear = NexButton(1, 12, "bClear");
NexText tNumpad = NexText(1, 13, "tNumpad");

//job list screen
NexPage jobListPage = NexPage(2, 0, "page2");
NexButton bLoadJobs = NexButton(2, 8, "bLoadJobs");
NexButton bArrowUp = NexButton(2, 6, "bArrowUp");
NexButton bJob1 = NexButton(2, 2, "bJob1");
NexButton bJob2 = NexButton(2, 3, "bJob2");
NexButton bJob3 = NexButton(2, 4, "bJob3");
NexButton bJob4 = NexButton(2, 5, "bJob4");
NexButton bArrowDown = NexButton(2, 7, "bArrowDown");


//manage employee clock in code on numpad screen
std::string numpad_value = "";
void update_numpad_text(char c,bool clear_text=false) {
    if (clear_text) {
        numpad_value = "";
    }
    else if (numpad_value.length() < 6) {
        numpad_value += c;
    }

    tNumpad.setText(numpad_value.c_str());
}

//list of strings representing each job
std::vector<std::string> job_list;
//objects for each job button
std::vector<NexButton*> job_buttons = { &bJob1, &bJob2, &bJob3, &bJob4 };
//index in job_list of top button on screen
int job_list_index = 0;
//add a job string to job_list,
//this is a function to avoid a linker error particular to ardruino:
//  each source file is compiled seperate of any other
//  which means pre-processor ifndef #define #endif tricks do not work
//  to include a file from multiple source files
//  A function prototype of this function is declared in workstation.cpp
//  for it to be used there :(
void add_job(std::string job_str) {
    job_list.push_back(job_str);
}

//populate job list buttons
void load_jobs() {
    if (!job_list.empty()) {
        for(int i=0;i<=3;++i) {
            if (job_list_index + i >= job_list.size()) {
                job_list_index = 0;
                i = 0;
            }
            job_buttons.at(i)->setText(job_list.at(job_list_index + i).c_str());
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
        load_jobs();
    }
}

//When a job button is pressed on job page
//this function saves the string of the job button pressed
//as selected_job, then goes back to home page
std::string selected_job = "";
void select_job(int button_index) {
    char buffer[20];
    job_buttons.at(button_index)->getText(buffer,20);
    selected_job = buffer;
    Serial.println(selected_job.c_str());
    Serial.println(numpad_value.c_str());

    //go back to home page
    nexSerial.print("page 0");
    nexSerial.write(0xff);
    nexSerial.write(0xff);
    nexSerial.write(0xff);
    
}

std::vector<NexButton*> network_buttons = { &bNetwork1, 
                                            &bNetwork2, 
                                            &bNetwork3, 
                                            &bNetwork4, 
                                            &bNetwork5,
                                            &bNetwork6,
                                            &bNetwork7,
                                            &bNetwork8 };

void scan_networks() {
    //update number of networks in range text
    //we send commands direct to nextion serial
    //because it is more reliable, quick updating of text
    //when using the library doesn't always work
    nexSerial.print("tNetworks.txt=\"Scanning..\"");
    nexSerial.write(0xff);
    nexSerial.write(0xff);
    nexSerial.write(0xff);
    std::vector<std::string> networks = network_list();
    std::string cmd = "tNetworks.txt=\"Networks in Range:" + std::to_string(networks.size()) + "\"";
    nexSerial.print(cmd.c_str());
    nexSerial.write(0xff);
    nexSerial.write(0xff);
    nexSerial.write(0xff);

    //iterate network and network button vectors
    //change button text to SSID of network until it runs out of buttons
    for(int i=0;i < network_buttons.size();++i) {
        if (i < networks.size()) {
            network_buttons.at(i)->setText(networks.at(i).c_str());
        }
        else {
            //reset button text to blank if no more networks
            network_buttons.at(i)->setText("");
        }
    }
}

void select_network(int button) {
    char buffer[30];
    network_buttons.at(button-1)->getText(buffer,30);
    ssid = buffer;

    tNetworks.setText("Selecting..");

    std::string strength = network_signal(ssid);
    std::string msg = ssid + ": " + strength;
    tNetworks.setText(msg.c_str());
    network_buttons.at(button-1)->Set_background_color_bco(1024);
}

void update_network_status() {
    if (ssid != "") {
        std::string strength = network_signal(ssid);
        std::string msg = "tStatus.txt=\"" + ssid + " selected. Strength:" + strength + "\"";
        nexSerial.print(msg.c_str());
        nexSerial.write(0xff);
        nexSerial.write(0xff);
        nexSerial.write(0xff);
    }
}

void connect_network() {
    if (ssid == "") {
        nexSerial.print("tStatus.txt=\"Select a newtwork first\"");
        nexSerial.write(0xff);
        nexSerial.write(0xff);
        nexSerial.write(0xff);
    }
    else {
        std::string msg = "tStatus.txt=\"Connecting to " + ssid + "..\"";
        nexSerial.print(msg.c_str());
        nexSerial.write(0xff);
        nexSerial.write(0xff);
        nexSerial.write(0xff);
        int status = wifi_connect(ssid.c_str(),pass.c_str());
        if (status == WL_CONNECTED) {
            nexSerial.print("tStatus.txt=\"Connected!\"");
            nexSerial.write(0xff);
            nexSerial.write(0xff);
            nexSerial.write(0xff);
        }
        else {
            nexSerial.print("tStatus.txt=\"Failed to Connect\"");
            nexSerial.write(0xff);
            nexSerial.write(0xff);
            nexSerial.write(0xff);
        }
    }
}

void press_letter(char letter) {
    std::string field = "t0.txt+=\"";
    field.push_back(letter);
    field.push_back('\"');
    nexSerial.print(field.c_str());
    nexSerial.write(0xff);
    nexSerial.write(0xff);
    nexSerial.write(0xff);
}

void backspace() {
    char buffer[40] = {0};
    memset(buffer, 0, sizeof(buffer));
    tField.getText(buffer, sizeof(buffer));
    std::string pass_text(buffer);

    if (pass_text.size() > 0) {
        pass_text.pop_back(); //erase last character
        tField.setText(pass_text.c_str());
    }
}

//component callbacks
// home screen
void bClockInPopCallback(void *ptr) { }
void bSettingsPopCallback(void *ptr) { }

// settings screen
void bNetworksPopCallback(void *ptr) { }
void bPasswordPopCallback(void *ptr) { }
void bConnectPopCallback(void *ptr) { connect_network(); }
void bDonePopCallback(void *ptr) { }
void pSettingsPagePopCallback(void *ptr) { update_network_status(); }

// networks screen
void bScanPopCallback(void *ptr) { scan_networks(); }
void bNetwork1PopCallback(void *ptr) { select_network(1); }
void bNetwork2PopCallback(void *ptr) { select_network(2); }
void bNetwork3PopCallback(void *ptr) { select_network(3); }
void bNetwork4PopCallback(void *ptr) { select_network(4); }
void bNetwork5PopCallback(void *ptr) { select_network(5); }
void bNetwork6PopCallback(void *ptr) { select_network(6); }
void bNetwork7PopCallback(void *ptr) { select_network(7); }
void bNetwork8PopCallback(void *ptr) { select_network(8); }
void bCancelPopCallback(void *ptr) { }

// password screen
void baPopCallback(void *ptr) { press_letter('a'); }
void bbPopCallback(void *ptr) { press_letter('b'); }
void bcPopCallback(void *ptr) { press_letter('c'); }
void bdPopCallback(void *ptr) { press_letter('d'); }
void bePopCallback(void *ptr) { press_letter('e'); }
void bfPopCallback(void *ptr) { press_letter('f'); }
void bgPopCallback(void *ptr) { press_letter('g'); }
void bhPopCallback(void *ptr) { press_letter('h'); }
void biPopCallback(void *ptr) { press_letter('i'); }
void bjPopCallback(void *ptr) { press_letter('j'); }
void bkPopCallback(void *ptr) { press_letter('k'); }
void blPopCallback(void *ptr) { press_letter('l'); }
void bmPopCallback(void *ptr) { press_letter('m'); }
void bnPopCallback(void *ptr) { press_letter('n'); }
void boPopCallback(void *ptr) { press_letter('o'); }
void bpPopCallback(void *ptr) { press_letter('p'); }
void bqPopCallback(void *ptr) { press_letter('q'); }
void brPopCallback(void *ptr) { press_letter('r'); }
void bsPopCallback(void *ptr) { press_letter('s'); }
void btPopCallback(void *ptr) { press_letter('t'); }
void buPopCallback(void *ptr) { press_letter('u'); }
void bvPopCallback(void *ptr) { press_letter('v'); }
void bwPopCallback(void *ptr) { press_letter('w'); }
void bxPopCallback(void *ptr) { press_letter('x'); }
void byPopCallback(void *ptr) { press_letter('y'); }
void bzPopCallback(void *ptr) { press_letter('z'); }
void b0PopCallback(void *ptr) { press_letter('0'); }
void b1PopCallback(void *ptr) { press_letter('1'); }
void b2PopCallback(void *ptr) { press_letter('2'); }
void b3PopCallback(void *ptr) { press_letter('3'); }
void b4PopCallback(void *ptr) { press_letter('4'); }
void b5PopCallback(void *ptr) { press_letter('5'); }
void b6PopCallback(void *ptr) { press_letter('6'); }
void b7PopCallback(void *ptr) { press_letter('7'); }
void b8PopCallback(void *ptr) { press_letter('8'); }
void b9PopCallback(void *ptr) { press_letter('9'); }
void bBackspacePopCallback(void *ptr) { backspace(); }

// numpad screen
void bNum1PopCallback(void *ptr) { update_numpad_text('1'); }
void bNum2PopCallback(void *ptr) { update_numpad_text('2'); }
void bNum3PopCallback(void *ptr) { update_numpad_text('3'); }
void bNum4PopCallback(void *ptr) { update_numpad_text('4'); }
void bNum5PopCallback(void *ptr) { update_numpad_text('5'); }
void bNum6PopCallback(void *ptr) { update_numpad_text('6'); }
void bNum7PopCallback(void *ptr) { update_numpad_text('7'); }
void bNum8PopCallback(void *ptr) { update_numpad_text('8'); }
void bNum9PopCallback(void *ptr) { update_numpad_text('9'); }
void bClearPopCallback(void *ptr) { update_numpad_text('0',true); }

// job list screen
void jobListPageCallback(void *ptr) { Serial.println("Job Page Callback!"); }
void bLoadJobsCallback(void *ptr) { load_jobs(); }
void bArrowUpPopCallback(void *ptr) { move_job_index(-1); }
void bJob1PopCallback(void *ptr) { select_job(0); }
void bJob2PopCallback(void *ptr) { select_job(1); }
void bJob3PopCallback(void *ptr) { select_job(2); }
void bJob4PopCallback(void *ptr) { select_job(3); }
void bArrowDownPopCallback(void *ptr) { move_job_index(1); }


void attach_callbacks() {
    //starting screen
    bClockIn.attachPop(bClockInPopCallback, &bClockIn);
    bSettings.attachPop(bSettingsPopCallback, &bSettings);

    //settings screen
    bNetworks.attachPop(bNetworksPopCallback, &bNetworks);
    bPassword.attachPop(bPasswordPopCallback, &bPassword);
    bConnect.attachPop(bConnectPopCallback, &bConnect);
    bDone.attachPop(bDonePopCallback, &bDone);
    pSettingsPage.attachPop(pSettingsPagePopCallback, &pSettingsPage);

    //networks screen
    bScan.attachPop(bScanPopCallback, &bScan);
    bNetwork1.attachPop(bNetwork1PopCallback, &bNetwork1);
    bNetwork2.attachPop(bNetwork2PopCallback, &bNetwork2);
    bNetwork3.attachPop(bNetwork3PopCallback, &bNetwork3);
    bNetwork4.attachPop(bNetwork4PopCallback, &bNetwork4);
    bNetwork5.attachPop(bNetwork5PopCallback, &bNetwork5);
    bNetwork6.attachPop(bNetwork6PopCallback, &bNetwork6);
    bNetwork7.attachPop(bNetwork7PopCallback, &bNetwork7);
    bNetwork8.attachPop(bNetwork8PopCallback, &bNetwork8);
    bCancel.attachPop(bCancelPopCallback, &bCancel);

    //password screen
    ba.attachPop(baPopCallback, &ba);
    bb.attachPop(bbPopCallback, &bb);
    bc.attachPop(bcPopCallback, &bc);
    bd.attachPop(bdPopCallback, &bd);
    be.attachPop(bePopCallback, &be);
    bf.attachPop(bfPopCallback, &bf);
    bg.attachPop(bgPopCallback, &bg);
    bh.attachPop(bhPopCallback, &bh);
    bi.attachPop(biPopCallback, &bi);
    bj.attachPop(bjPopCallback, &bj);
    bk.attachPop(bkPopCallback, &bk);
    bl.attachPop(blPopCallback, &bl);
    bm.attachPop(bmPopCallback, &bm);
    bn.attachPop(bnPopCallback, &bn);
    bo.attachPop(boPopCallback, &bo);
    bp.attachPop(bpPopCallback, &bp);
    bq.attachPop(bqPopCallback, &bq);
    br.attachPop(brPopCallback, &br);
    bs.attachPop(bsPopCallback, &bs);
    bt.attachPop(btPopCallback, &bt);
    bu.attachPop(buPopCallback, &bu);
    bv.attachPop(bvPopCallback, &bv);
    bw.attachPop(bwPopCallback, &bw);
    bx.attachPop(bxPopCallback, &bx);
    by.attachPop(byPopCallback, &by);
    bz.attachPop(bzPopCallback, &bz);
    b0.attachPop(b0PopCallback, &b0);
    b1.attachPop(b1PopCallback, &b1);
    b2.attachPop(b2PopCallback, &b2);
    b3.attachPop(b3PopCallback, &b3);
    b4.attachPop(b4PopCallback, &b4);
    b5.attachPop(b5PopCallback, &b5);
    b6.attachPop(b6PopCallback, &b6);
    b7.attachPop(b7PopCallback, &b7);
    b8.attachPop(b8PopCallback, &b8);
    b9.attachPop(b9PopCallback, &b9);
    bBackspace.attachPop(bBackspacePopCallback, &bBackspace);

    //numpad screen
    bNum1.attachPop(bNum1PopCallback, &bNum1);
    bNum2.attachPop(bNum2PopCallback, &bNum2);
    bNum3.attachPop(bNum3PopCallback, &bNum3);
    bNum4.attachPop(bNum4PopCallback, &bNum4);
    bNum5.attachPop(bNum5PopCallback, &bNum5);
    bNum6.attachPop(bNum6PopCallback, &bNum6);
    bNum7.attachPop(bNum7PopCallback, &bNum7);
    bNum8.attachPop(bNum8PopCallback, &bNum8);
    bNum9.attachPop(bNum9PopCallback, &bNum9);
    bClear.attachPop(bClearPopCallback, &bClear);

    //job list screen
    jobListPage.attachPop(jobListPageCallback, &jobListPage);
    bLoadJobs.attachPop(bLoadJobsCallback, &bLoadJobs);
    bArrowUp.attachPop(bArrowUpPopCallback, &bArrowUp);
    bJob1.attachPop(bJob1PopCallback, &bJob1);
    bJob2.attachPop(bJob2PopCallback, &bJob2);
    bJob3.attachPop(bJob3PopCallback, &bJob3);
    bJob4.attachPop(bJob4PopCallback, &bJob4);
    bArrowDown.attachPop(bArrowDownPopCallback, &bArrowDown);
}

//add component objects to event listen array
NexTouch *nex_listen_list[] = {&bClockIn,
                               &bSettings,
                               &bNetworks,
                               &bPassword,
                               &bConnect,
                               &bDone,
                               &pSettingsPage,
                               &bScan,
                               &bNetwork1,
                               &bNetwork2,
                               &bNetwork3,
                               &bNetwork4,
                               &bNetwork5,
                               &bNetwork6,
                               &bNetwork7,
                               &bNetwork8,
                               &bCancel,
                               &ba,
                               &bb,
                               &bc,
                               &bd,
                               &be,
                               &bf,
                               &bg,
                               &bh,
                               &bi,
                               &bj,
                               &bk,
                               &bl,
                               &bm,
                               &bn,
                               &bo,
                               &bp,
                               &bq,
                               &br,
                               &bs,
                               &bt,
                               &bu,
                               &bv,
                               &bw,
                               &bx,
                               &by,
                               &bz,
                               &b0,
                               &b1,
                               &b2,
                               &b3,
                               &b4,
                               &b5,
                               &b6,
                               &b7,
                               &b8,
                               &b9,
                               &bBackspace,
                               &bNum1,
                               &bNum2,
                               &bNum3,
                               &bNum4,
                               &bNum5,
                               &bNum6,
                               &bNum7,
                               &bNum8,
                               &bNum9,
                               &bClear,
                               &jobListPage,
                               &bLoadJobs,
                               &bArrowUp,
                               &bJob1,
                               &bJob2,
                               &bJob3,
                               &bJob4,
                               &bArrowDown,
                               NULL };