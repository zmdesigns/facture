#include <string>
#include "Nextion.h"


//component objects
// start screen
NexButton bClockIn = NexButton(0, 3, "bClockIn"); //page-id,component-id,component-name

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
NexButton bArrowUp = NexButton(2, 7, "bArrowUp");
NexButton bJob1 = NexButton(2, 3, "bJob1");
NexButton bJob2 = NexButton(2, 4, "bJob2");
NexButton bJob3 = NexButton(2, 5, "bJob3");
NexButton bJob4 = NexButton(2, 6, "bJob4");
NexButton bArrowDown = NexButton(2, 8, "bArrowDown");


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


//manage selected job/page of job list on job screen
std::string selected_job = "";
int job_page = 0;

void select_job(std::string job_name) {
    selected_job = job_name;
    Serial.println(selected_job.c_str());
    Serial.println(numpad_value.c_str());


    nexSerial.print("page 0");
    nexSerial.write(0xff);
    nexSerial.write(0xff);
    nexSerial.write(0xff);
    
}

//component callbacks
// starting screen
void bClockInPopCallback(void *ptr) { Serial.println("Clock-in button pressed!"); }

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
void bArrowUpPopCallback(void *ptr) {  }
void bJob1PopCallback(void *ptr) { select_job("Job1"); }
void bJob2PopCallback(void *ptr) { select_job("Job2"); }
void bJob3PopCallback(void *ptr) { select_job("Job3"); }
void bJob4PopCallback(void *ptr) { select_job("Job4"); }
void bArrowDownPopCallback(void *ptr) {  }


void attach_callbacks() {
    //starting screen
    bClockIn.attachPop(bClockInPopCallback, &bClockIn);

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
    bArrowUp.attachPop(bArrowUpPopCallback, &bArrowUp);
    bJob1.attachPop(bJob1PopCallback, &bJob1);
    bJob2.attachPop(bJob2PopCallback, &bJob2);
    bJob3.attachPop(bJob3PopCallback, &bJob3);
    bJob4.attachPop(bJob4PopCallback, &bJob4);
    bArrowDown.attachPop(bArrowDownPopCallback, &bArrowDown);

}

//add component objects to event listen array
NexTouch *nex_listen_list[] = {&bClockIn,
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
                               &bArrowUp,
                               &bJob1,
                               &bJob2,
                               &bJob3,
                               &bJob4,
                               &bArrowDown,
                               NULL };