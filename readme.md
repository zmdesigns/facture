# Facture

Facture is a completely wireless touchscreen device that makes it easy to track how long it takes to do something. It pairs with a web application that is used to organize and manage the information the devices collect. 

[Read about the development process](https://medium.com/@zackmdesigns/59-days-of-code-1b001ca4b6ec?source=friends_link&sk=6b2015b63c3c89e2b60380bb9f1f773d)

[Example website](http://www.jtrkr.zackmdesigns.com/index.php)

### Client Device
A hardware device built off the arduino/nextion platforms. The software is written in C++. The UI is made in Nextion Editor. The hardware is an Arduino MKR WiFi 1000 and a 3.2" TFT Nextion Touchscreen. It is powered by a 10,000mAh battery. The device also has a RF Transceiver for mesh communication with other Facture devices. The software on the device allows operators to login, select a job, and start/stop their time. The network connection can also be managed from the device.

### Website and API
Written in PHP/MySQL. Provides an interface to manage and view job information and an API for the devices to interact with the database. The API entry point is api.php. Below is a list of available API functions.

|    ID    | Description  | Required POST Variables                             | Returns |  Notes |
|----------|--------------|-----------------------------------------------------|---------|-------------------------------------------------------------------------------------|
| **Employees** |
|    1     |  List all    | N/A                                                 | JSON    | Returns all columns as associative array |
|    2     |  New         | name,login,notes                                    | string  |
|    3     |  Edit        | name,new_name,login,notes                           | string  |
|    4     |  Delete      | name                                                | string  |
| **Log** |
|    10    | List all     | N/A                                                 | JSON    | Returns all columns as associative array |
|    11    | New          | employee_id,workstation_id,job_id,product_id,action | string  | Action: 1=clock-in 2=clock-out |
|    12    | Last log     | employee_id,workstation_id,job_id                   | JSON    | Returns last row that matches passed variables,null is passed for a wildcard |   
|    13    | Hours Worked | employee_id,workstation_id,job_id                   | JSON    | Returns rows that match passed arguments,empty string is passed for wildcard |                
|    14    | Activity     | employee_id,workstation_id,job_id                   | string  | Returns a formated string of last or current activity, or no activity if none found |
|    15    | Job Log Sort | job_id,product_id                                   | JSON    | Returns JSON array of summarized log of clockins-outs |
| **Products** |
|    20    |  List all    | N/A                                                 | JSON    | Returns all columns as associative array |
|    21    |  New         | product_id,name,description                         | string  |
|    22    |  Edit        | product_id,new_product_id,name,description          | string  |                     
|    23    |  Delete      | product_id                                          | string  |
| **Jobs** |
|    30    | List all     | N/A                                                 | JSON    | Returns all columns as associative array |
|    31    | New          | job_id,customer_name,product_name,qty,notes         | string  |             
|    32    | Edit         | id,job_id,customer_name,product_name,qty,notes      | string  |                   
|    33    | Delete       | job_id                                              | string  |
|    34    | List sorted  | N/A                                                 | JSON    | Returns array of arrays of jobs, indexed by job_id |
| **Customer** |
|    40    | List all     | N/A                                                 | JSON    | Returns all columns as associative array |
|    41    | New          | customer_id,name,notes                              | string  |
|    42    | Edit         | customer_id,new_name,name,notes                     | string  |                
|    43    | Delete       | name                                                | string  |
| **Workstation** |
|    50    | List all     | N/A                                                 | JSON    | Returns all columns as associative array |
|    51    | New          | station_id,name,notes                               | string  |    
|    52    | Edit         | station_id,new_name,name,notes                      | string  |                 
|    53    | Delete       | name                                                | string  |
| **General** |
|    90    | Lookup       | table,column,search                                 | JSON    | Returns all matching rows where column data = search in table |


### License
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




