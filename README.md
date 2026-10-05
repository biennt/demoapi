# Basic environment for testing API Discovery

Nginxplus acts as:
- API Gateway, performs request routing and jwt validation (nginxplus required)
- Reverse proxy for GUI (generate the JWT from username/password and API request testing)
- Place your nginx license (license.jwt) one level out-side this directory 

Other containers:
- json-server to serve API from db.json file (behind nginxplus)
- php (apache-php) to serve *.php files (behind nginxplus)
- Traffic generator to send requests automatically (running on port 8080/http)

to start:
```
./start_demoapi.sh
```
Accessing the manual test GUI http://your_host/login.php
Or the automatic test GUI http://your_host:8080/


to stop:
```
./start_demoapi.sh
```
