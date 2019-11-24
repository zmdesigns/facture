This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.


Facture is a completely wireless touchscreen device that makes it easy to track how long it takes to do something. It pairs with a web application that is used to organize and manage the information the devices collect. 

The touchscreen device is refered to as 'client'. The web application is 'webapp'.


client - A hardware device built off the arduino/nextion platforms. The software is written in C++. The UI is made in Nextion Editor. The hardware is an Arduino MKR WiFi 1000 and a 3.2" TFT Nextion Touchscreen. It is powered by a 10,000mAh battery. The device also has a RF Transceiver for mesh communication with other Facture devices. The software on the device allows operators to login, select a job, and start/stop their time. The network connection can also be managed from the device.

webapp - A web app written in PHP/MySQL. It provides users with an interface to manage and view job information and an API for the devices to store/retrieve information in the database. api.php is the entry point for all backend functions. At the top of api.php you can find a list of functions and the required parameters. The client/frontend sends a post request with the required variables to interact with the webapp.

Test version of the webapp is available here: http://www.jtrkr.zackmdesigns.com/index.php

So long and thanks for all the fish!