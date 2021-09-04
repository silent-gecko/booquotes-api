# BooQuotes API
This API allows retrieving literary quotes from data collection.

:warning: This is an example of REST API I'm developing by myself.
It exists solely for me to improve and show my skills to the others. 
Personal use only.

## Installation
Application is ready to serve from [Docker Client](https://docs.docker.com/get-docker/) with [Docker Compose](https://docs.docker.com/compose/). <br>
It uses `php8.0-fpm` + `Nginx` + `PostgreSQL` as application services and `Lumen` as a framework. 

### Set up environment variables
There is `.env.app.example` environment file for development purposes. 
It contains environment variables both for Docker Compose and Lumen.
You can learn about the use of variables in [docker-compose file](./docker-compose.yml) and [Lumen configuratioin guide](https://lumen.laravel.com/docs/8.x/configuration), respectively.
Rename `.env.app.example` file to `.env` and set all environment variables as you need. <br>

### Start the app
Open terminal in the project directory and run:

```shell
docker-compose -f docker-compose.dev.yml up # development 
docker-compose  up                          # production
```
Development mode includes extra services: `XDedug` in code coverage mode + `Swagger-UI`

### Migrate DB tables and seed the data
Open terminal inside the php-fpm container: 
```shell
docker exec -it {{php-fpm container name}} /bin/sh
```
and run:

```shell
php artisan migrate:fresh --seed
```
Or just run the command inside the project directory terminal:
```shell
docker exec {{php-fpm container name}} php artisan migrate:fresh --seed
```

## Usage
WIP :speak_no_evil:

### API Docs
Learn API docs in dev environment at  
[http://localhost:8088/api-docs/]() or use cloud version of [Swagger](https://swagger.io/) to read [specification file](./docs/openapi.json).