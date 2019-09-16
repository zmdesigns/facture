Job Tracker is a wireless mesh network of devices that automate job and process time tracking. The devices send data to a web app that provides analytics on cost and efficiency.

This project is of two parts.

1. client - A hardware device built off the arduino platform. The software is written in C++. The hardware is an Arduino MKR WiFi 1010 with a numpad, 3 large buttons, and a lcd display. The hardware allows operators to login, select a job, and start/stop their time.

2. webapp - A web app written in PHP/MySQL. The app receives, manages, and visualizes the data from the devices. It provides users with an interface to manage and view job information.

Test version of the webapp is available here: http://www.jtrkr.zackmdesigns.com/index.php

