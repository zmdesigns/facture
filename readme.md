Facture is a completely wireless touchscreen device that makes it easy to track how long it takes to do something. It pairs with a web application that is used to organize and manage the information the devices collect. 

The touchscreen device is refered to as 'client'. The web application is 'webapp'.


client - A hardware device built off the arduino/nextion platforms. The software is written in C++. The UI is made in Nextion Editor. The hardware is an Arduino MKR WiFi 1000 and a 3.2" TFT Nextion Touchscreen. It is powered by a 10,000mAh battery. The device also has a RF Transceiver for mesh communication with other Facture devices. The software on the device allows operators to login, select a job, and start/stop their time. The network connection can also be managed from the device.

webapp - A web app written in PHP/MySQL. It provides users with an interface to manage and view job information and an API for the devices to store information in the database. api.php is entry point for all backend functions. At the top of api.php you can find a list of functions and the required parameters.

Test version of the webapp is available here: http://www.jtrkr.zackmdesigns.com/index.php

So long and thanks for all the fish!