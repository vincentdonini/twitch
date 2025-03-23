# Twitch overlay

## Context

This project is a dynamic "**Twitch overlay**" that interacts in real time with the Twitch API. It allows displaying various live information on a stream, such as:

- The number of real-time viewers
- The latest subscribers and followers
- Chat events (messages, donations, raids...)
- Dynamic animations and interactions
- ...

The overlay is designed to be easily customizable and optimized for OBS.

## Stack project

### API
- The project API is built with **Symfony 6.4** and Docker.
- The following dependencies have been added to the project:
    - **Doctrine ORM** for database interaction
    - **Symfony Serializer** for data transformation
    - **NelmioApiDocBundle** for generating API documentation

### Frontend
- The project frontend is built with **ReactJS** and **Node.js 22**, utilizing modern tools and libraries for a dynamic user experience.

### WebSocket
- A **WebSocket** service using **Socket.IO** is set up for real-time communication, managed independently by **Nginx**.
- This service handles WebSocket connections, enabling features like live updates, real-time messaging, and bidirectional communication within the application.

### Services
- **Nginx** (`nginx:latest`) is used as the reverse proxy and web server to manage requests and traffic.
- **Node.js** (`node:22`) serves as the runtime environment for building and running the frontend application.
- **PHP** (`bitnami/php-fpm:latest`) runs the API backend, providing fast PHP processing with PHP-FPM.
- **Database**: **Percona MySQL** (`percona/percona-server:8.0`) is used as the relational database, providing a robust and optimized MySQL-compatible database for the application.

## Init project

### 1. Clone project
`git clone https://github.com/vincentdonini/docker-stack.git`

### 2. Configure Hosts
Before launching the project, make sure to update the **hosts** for the API, Frontend, and WebSocket services in the following places:
- The corresponding `.conf` files for **Nginx**.
- The `justfile` commands, if they reference specific domains or hostnames.

### 3. Setup the Application
- Run the following commands to initialize the project: `just setup-app`

## Launch project

### Launch all stacks
To start the entire project (API, Frontend, and WebSocket) with a single command, simply run:
- `just start`  
  This will automatically start all the required services.


### Launch stack separately
If you prefer to start the services separately for better visibility of the logs in the terminals, here are the corresponding commands:

- **API**:`just dev-up`   
  Starts the entire environment, including the API service and the necessary Docker containers.   
  Note: You must run this command first, as it sets up the Docker environment required for both the API and the frontend.

- **Frontend**:`just encore-frontend-dev-watch`  
  Once the API service and Docker environment are up and running, you can start the frontend service and begin watching for file changes (live reloading).

- **WebSocket**:`just encore-ws-dev-watch`  
  Starts the WebSocket service and begins listening for connections.

### Url projects
- API : `https://api.twtich.woder.local`
- Front : `https://front.twtich.woder.local`
- WS : `https://ws.twtich.woder.local`