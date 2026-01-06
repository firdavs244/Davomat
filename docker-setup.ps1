# Docker Setup Script for Davomat System (Windows PowerShell)
Write-Host "🚀 Setting up Davomat System with Docker..." -ForegroundColor Green

# Generate self-signed SSL certificates
Write-Host "📜 Generating SSL certificates..." -ForegroundColor Yellow
New-Item -ItemType Directory -Force -Path "docker\nginx\ssl" | Out-Null

# Using OpenSSL (if available) or create using PowerShell
try {
    & openssl req -x509 -nodes -days 365 -newkey rsa:2048 `
        -keyout docker\nginx\ssl\key.pem `
        -out docker\nginx\ssl\cert.pem `
        -subj "/C=UZ/ST=Tashkent/L=Tashkent/O=Kollej/CN=davomat.local"
    Write-Host "✅ SSL certificates generated!" -ForegroundColor Green
} catch {
    Write-Host "⚠️  OpenSSL not found. You may need to generate SSL certificates manually." -ForegroundColor Yellow
    Write-Host "   Or install OpenSSL from: https://slproweb.com/products/Win32OpenSSL.html" -ForegroundColor Yellow
}

# Copy .env file if not exists
if (-not (Test-Path .env)) {
    Write-Host "📝 Creating .env file..." -ForegroundColor Yellow
    Copy-Item .env.example .env

    # Update database credentials
    (Get-Content .env) -replace 'DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql' | Set-Content .env
    (Get-Content .env) -replace 'DB_HOST=127.0.0.1', 'DB_HOST=mysql' | Set-Content .env
    (Get-Content .env) -replace 'DB_DATABASE=laravel', 'DB_DATABASE=davomat' | Set-Content .env
    (Get-Content .env) -replace 'DB_USERNAME=root', 'DB_USERNAME=davomat_user' | Set-Content .env
    (Get-Content .env) -replace 'DB_PASSWORD=', 'DB_PASSWORD=secret123' | Set-Content .env
    (Get-Content .env) -replace 'REDIS_HOST=127.0.0.1', 'REDIS_HOST=redis' | Set-Content .env

    Write-Host "✅ .env file created!" -ForegroundColor Green
}

# Build and start containers
Write-Host "🏗️  Building Docker containers..." -ForegroundColor Yellow
docker-compose build

Write-Host "🚀 Starting containers..." -ForegroundColor Yellow
docker-compose up -d

# Wait for MySQL to be ready
Write-Host "⏳ Waiting for MySQL to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Run migrations
Write-Host "🗄️  Running database migrations..." -ForegroundColor Yellow
docker-compose exec php php artisan migrate --force

# Seed database if needed
Write-Host "🌱 Seeding database..." -ForegroundColor Yellow
docker-compose exec php php artisan db:seed --force

# Clear caches
Write-Host "🧹 Clearing caches..." -ForegroundColor Yellow
docker-compose exec php php artisan cache:clear
docker-compose exec php php artisan config:clear
docker-compose exec php php artisan route:clear
docker-compose exec php php artisan view:clear

Write-Host ""
Write-Host "✅ Setup complete!" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Application is now running:" -ForegroundColor Cyan
Write-Host "   HTTP:  http://localhost" -ForegroundColor White
Write-Host "   HTTPS: https://localhost" -ForegroundColor White
Write-Host ""
Write-Host "📊 Database:" -ForegroundColor Cyan
Write-Host "   Host: localhost:3306" -ForegroundColor White
Write-Host "   Database: davomat" -ForegroundColor White
Write-Host "   Username: davomat_user" -ForegroundColor White
Write-Host "   Password: secret123" -ForegroundColor White
Write-Host ""
Write-Host "🔧 Useful commands:" -ForegroundColor Cyan
Write-Host "   docker-compose ps              - View running containers" -ForegroundColor White
Write-Host "   docker-compose logs -f         - View logs" -ForegroundColor White
Write-Host "   docker-compose down            - Stop containers" -ForegroundColor White
Write-Host "   docker-compose exec php bash   - Access PHP container" -ForegroundColor White
Write-Host ""
