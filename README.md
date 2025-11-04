# PHP Microservice Boilerplate

A high-performance PHP microservice boilerplate built with [Hyperf](https://hyperf.io), a coroutine-based framework optimized for building modern microservices and APIs.

## Documentation

- Official Documentation: [https://hyperf.wiki](https://hyperf.wiki)
- Hyperf Website: [https://hyperf.io](https://hyperf.io)

## Requirements

- **PHP >= 8.1**
- **Swoole PHP extension >= 5.0** with `swoole.use_shortname` set to `Off` in `php.ini`
  - Or **Swow PHP extension >= 1.3** as an alternative
- **Required PHP Extensions:**
  - JSON
  - Pcntl
  - OpenSSL (for HTTPS)
  - PDO (for MySQL Client)
  - Redis (for Redis Client)
  - Protobuf (for gRPC Server or Client)

**Recommended:** Use Docker for consistent development and production environments.

## Directory Structure

```
.
├── app/
│   ├── Amqp/              # AMQP message queue handlers
│   │   ├── Consumer/      # Message consumers
│   │   └── Producer/      # Message producers
│   ├── Constants/         # Application constants
│   ├── Controller/        # HTTP controllers
│   ├── Exception/         # Custom exceptions
│   │   └── Handler/       # Exception handlers
│   ├── Kafka/             # Kafka integration
│   │   └── Consumer/      # Kafka consumers
│   ├── Libraries/         # Custom libraries
│   ├── Listener/          # Event listeners
│   ├── Middleware/        # HTTP middleware
│   ├── Model/             # Database models
│   ├── Process/           # Custom processes
│   ├── Repositories/      # Data repositories
│   ├── Request/           # Form requests & validation
│   ├── Resource/          # API resources
│   ├── Services/          # Business logic services
│   └── Traits/            # Reusable traits
├── bin/                   # Executable scripts
├── build/                 # Build configuration files
├── config/                # Configuration files
│   └── autoload/          # Auto-loaded configurations
├── languages/             # Internationalization files
│   ├── en/                # English translations
│   └── zh_CN/             # Chinese translations
├── migrations/            # Database migrations
├── runtime/               # Runtime files (logs, cache)
├── storage/               # Application storage
├── test/                  # Test files
│   └── Cases/             # Test cases
├── vendor/                # Composer dependencies
├── .env                   # Environment variables
├── composer.json          # Composer dependencies
├── dev.Dockerfile         # Development Dockerfile
└── prod.Dockerfile        # Production Dockerfile
```

## Getting Started

### Installation

1. Clone the repository
2. Install dependencies:

```bash
composer install
```

3. Copy `.env.example` to `.env` and configure your environment variables

### Running with Docker

#### Development Mode

Build the development image:

```bash
docker build -f dev.Dockerfile -t <image_name:tag_name> .
```

Run the container:

```bash
docker run -p <port_http>:9501 -p <port_grpc>:9502 -v ${PWD}:/var/www/html --name <your_container_name> --replace -d <image_name:tag_name>
```

**Example:**
```bash
docker build -f dev.Dockerfile -t hyperf-microservice:dev .
docker run -p 9501:9501 -p 9502:9502 -v ${PWD}:/var/www/html --name hyperf-dev --replace -d hyperf-microservice:dev
```

#### Production Mode

Build the production image:

```bash
docker build -f prod.Dockerfile --build-arg TZ=<your_timezone> --build-arg user=<username> --build-arg uid=<uid_user_default_1000> -t <image_name:tag_name> .
```

Run the container:

```bash
docker run -p <port_http>:9501 -p <port_grpc>:9502 --name <your_container_name> --replace -d <image_name:tag_name>
```

**Example:**
```bash
docker build -f prod.Dockerfile --build-arg TZ=Asia/Jakarta --build-arg user=appuser --build-arg uid=1000 -t hyperf-microservice:latest .
docker run -p 9501:9501 -p 9502:9502 --name hyperf-prod --replace -d hyperf-microservice:latest
```

### Running Without Docker

Start the server:

```bash
php bin/hyperf.php start
```

### Running Commands Inside Container

Execute the `hyperf` command inside a running container:

```bash
docker exec -it <your_container_name> hyperf <command>
```

**Note:** The `hyperf` alias is configured to run `php bin/hyperf.php` for convenience.

**Examples:**
```bash
# Start the server
docker exec -it hyperf-dev hyperf start

# Run migrations
docker exec -it hyperf-dev hyperf migrate

# Generate a controller
docker exec -it hyperf-dev hyperf gen:controller UserController

# Access container shell
docker exec -it hyperf-dev sh
```

## Development

### Routes

Define your API routes in [config/routes.php](config/routes.php).

### Controllers

Create controllers in the `app/Controller` directory. See [app/Controller/IndexController.php](app/Controller/IndexController.php) for an example.

### Configuration

All configuration files are located in the `config/` directory. Auto-loaded configurations are in `config/autoload/`.

### Available Commands

```bash
# Start the server
hyperf start

# Run database migrations
hyperf migrate

# Generate code
hyperf gen:controller <ControllerName>
hyperf gen:model <ModelName>
hyperf gen:middleware <MiddlewareName>

# View all available commands
hyperf
```

## Ports

- **9501**: HTTP Server
- **9502**: gRPC Server

## Features

This boilerplate includes:

- ✅ Docker support for development and production
- ✅ Pre-configured directory structure following best practices
- ✅ AMQP and Kafka integration setup
- ✅ Database migration support
- ✅ Request validation
- ✅ API resource transformers
- ✅ Repository pattern implementation
- ✅ Service layer architecture
- ✅ Exception handling
- ✅ Middleware support
- ✅ Internationalization (i18n)
- ✅ Swoole/Swow for high-performance async operations
- ✅ gRPC support

## License

[Your License Here]
