#!/bin/bash

# Docker Setup Script for Davomat System
echo "🚀 Setting up Davomat System with Docker..."

# Generate self-signed SSL certificates
echo "📜 Generating SSL certificates..."
mkdir -p docker/nginx/ssl

openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout docker/nginx/ssl/key.pem \
    -out docker/nginx/ssl/cert.pem \
    -subj "/C=UZ/ST=Tashkent/L=Tashkent/O=Kollej/CN=davomat.local"

echo "✅ SSL certificates generated!"

# Copy .env file if not exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env

    # Update database credentials
    sed -i 's/DB_CONNECTION=sqlite/DB_CONNECTION=mysql/' .env
    sed -i 's/DB_HOST=127.0.0.1/DB_HOST=mysql/' .env
    sed -i 's/DB_PORT=3306/DB_PORT=3306/' .env
    sed -i 's/DB_DATABASE=laravel/DB_DATABASE=davomat/' .env
    sed -i 's/DB_USERNAME=root/DB_USERNAME=davomat_user/' .env
    sed -i 's/DB_PASSWORD=/DB_PASSWORD=secret123/' .env

    # Update Redis configuration
    sed -i 's/REDIS_HOST=127.0.0.1/REDIS_HOST=redis/' .env

    echo "✅ .env file created!"
fi

# Build and start containers
echo "🏗️  Building Docker containers..."
docker-compose build

echo "🚀 Starting containers..."
docker-compose up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 15

# Generate application key
echo "🔑 Generating application key..."
docker-compose exec -T php php artisan key:generate --force

# Run migrations
echo "🗄️  Running database migrations..."
docker-compose exec -T php php artisan migrate --force

# Seed database if needed
echo "🌱 Seeding database..."
docker-compose exec -T php php artisan db:seed --force

# Clear and cache config
echo "🧹 Clearing and caching configuration..."
docker-compose exec -T php php artisan config:cache
docker-compose exec -T php php artisan route:cache
docker-compose exec -T php php artisan view:cache

echo ""
echo "✅ Setup complete!"
echo ""
echo "🌐 Application is now running:"
echo "   HTTP:  http://localhost"
echo "   HTTPS: https://localhost"
echo ""
echo "📊 Database:"
echo "   Host: localhost:3306"
echo "   Database: davomat"
echo "   Username: davomat_user"
echo "   Password: secret123"
echo ""
echo "🔧 Useful commands:"
echo "   docker-compose ps              - View running containers"
echo "   docker-compose logs -f         - View logs"
echo "   docker-compose down            - Stop containers"
echo "   docker-compose exec php bash   - Access PHP container"
echo ""
