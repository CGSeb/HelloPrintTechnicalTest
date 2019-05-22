# HelloPrintTechnicalTestV2

## Installation

The installation guide will be only for Ubuntu 18, if you use Windows or another OS please check on the Internet how to run the different commands.

<br>

### Repository
To have to source code on your local machine please run
```bash
git clone https://github.com/SebastienMichon/HelloPrintTechnicalTestV2.git
```
The application is divided into three components:

- **Website**: The login interface
- **Api**: The application which will handle the requests and responses
- **Core**: The queuing system with the business logic

<br>

### Dev environment
Edit your hosts file
```bash
sudo nano /etc/hosts
```
and add the following lines
```bash
127.0.0.1   helloprint.test
127.0.0.1   api.helloprint.test
127.0.0.1   core.helloprint.test
```
**Note:** In the following guide we assume you already installed Docker on your machine.

<br>

##Website
Website application is the login interface.
In order to start the application, execute this command in the **root** directory (this will start also API application)
```bash
./start.sh
```
After few moment, the interface will be available at the address: http://helloprint.test:8000/

<br>

##RabbitMQ
For the queuing system, RabbitMQ will be used.
<br>

First move to the **Core** directory and execute this command

```bash 
chmod 777 start.sh
```

Then start the RabbitMQ with this command (Still in the directory **Core**)

```bash
./start.sh
```

**Note:** Wait a minute or two then you can go to http://core.helloprint.test:8080 and use the credentials: 
* Login: guest
* Password: guest

(You can change the password or create new users in the table **Admin**)

<br>