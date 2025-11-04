# Introduction

This is a PHP microservice built with the [Hyperf](https://hyperf.io) framework. Hyperf is a high-performance, coroutine-based PHP framework optimized for building microservices and API applications.

For complete documentation, visit: [https://hyperf.wiki](https://hyperf.wiki)

# Requirements

Hyperf has some requirements for the system environment, it can only run under Linux and Mac environment, but due to the development of Docker virtualization technology, Docker for Windows can also be used as the running environment under Windows.

The various versions of Dockerfile have been prepared for you in the [hyperf/hyperf-docker](https://github.com/hyperf/hyperf-docker) project, or directly based on the already built [hyperf/hyperf](https://hub.docker.com/r/hyperf/hyperf) Image to run.

When you don't want to use Docker as the basis for your running environment, you need to make sure that your operating environment meets the following requirements:  

 - PHP >= 8.1
 - Any of the following network engines
   - Swoole PHP extension >= 5.0，with `swoole.use_shortname` set to `Off` in your `php.ini`
   - Swow PHP extension >= 1.3
 - JSON PHP extension
 - Pcntl PHP extension
 - OpenSSL PHP extension （If you need to use the HTTPS）
 - PDO PHP extension （If you need to use the MySQL Client）
 - Redis PHP extension （If you need to use the Redis Client）
 - Protobuf PHP extension （If you need to use the gRPC Server or Client）

# Getting Started

## Installation

Install dependencies using Composer:

```bash
composer install
```

## Running the Application

### Using Docker (Recommended)

Start the microservice using Docker Compose:

```bash
docker-compose up
```

This will start the server on port `9501`, and bind it to all network interfaces. You can then access the API at `http://localhost:9501/`

### Without Docker

Run the server directly using the hyperf command:

```bash
hyperf start
```

Note: When using the provided Dockerfile, the `hyperf` command is aliased to `php bin/hyperf.php` for convenience.

## Development Tips

- Routes are defined in [config/routes.php](config/routes.php)
- Controllers are located in the `app/Controller` directory - see [app/Controller/IndexController.php](app/Controller/IndexController.php) for an example
- Use the `hyperf` command for all framework operations (e.g., `hyperf start`, `hyperf migrate`, `hyperf gen:controller`, etc.)
- Configuration files are in the `config/` directory

## Project Structure

This boilerplate provides a clean starting point for building PHP microservices with Hyperf, including:
- Docker configuration for easy deployment
- Pre-configured routes and controllers
- Environment-based configuration
- Swoole/Swow support for high-performance async operations
