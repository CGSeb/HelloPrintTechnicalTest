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

To build the environment, execute this command
```bash
./buildenv.sh
```
<br>

## Start the applications

To start the environment, run this command in the root file

```bash
./start.sh
```

<br>

## Website
Website application is the login interface.

After few moment, the interface will be available at the address: http://helloprint.test:8000/

<br>

## RabbitMQ
For the queuing system, RabbitMQ will be used.
<br>


**Note:** Wait a minute or two then you can go to http://core.helloprint.test:15672 and use the credentials: 
* Login: rabbitmq
* Password: rabbitmq

(You can change the password or create new users in the table **Admin**)

<br>

## Consumer
Even if you executed the **./start.sh** script the consumer is not started. This means that the recovery password requests will stay in the queue (can be seen http://core.helloprint.test:15672 tab Overview) 

To start the consumer run
```bash
./startconsumer.sh
```

**Note:** The emails can take times to arrive in your mailbox, check also the spam. 