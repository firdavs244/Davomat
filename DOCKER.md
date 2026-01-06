# Davomat System - Docker Guide

## Quick Start

### Windows

```powershell
.\docker-setup.ps1
```

### Linux/Mac

```bash
chmod +x docker-setup.sh
./docker-setup.sh
```

Or manually:

```bash
docker-compose up -d
```

## URLs

-   **HTTP**: http://localhost
-   **HTTPS**: https://localhost (Self-signed certificate)
-   **Database**: localhost:3306
-   **Redis**: localhost:6379

## Default Credentials

### Database

-   Host: `mysql`
-   Database: `davomat`
-   Username: `davomat_user`
-   Password: `secret123`
-   Root Password: `root123`

### Application

Check your seeders for default admin credentials.

## SSL Setup

### Self-Signed Certificate (Development)

The setup script automatically generates a self-signed certificate. Your browser will show a warning - this is normal for development.

### Production SSL (Let's Encrypt)

For production, replace the SSL certificates in `docker/nginx/ssl/` with Let's Encrypt certificates:

```bash
# Install certbot
docker run -it --rm \
  -v /docker/nginx/ssl:/etc/letsencrypt \
  certbot/certbot certonly --standalone \
  -d yourdomain.com
```

Then update `docker/nginx/default.conf`:

```nginx
ssl_certificate /etc/nginx/ssl/live/yourdomain.com/fullchain.pem;
ssl_certificate_key /etc/nginx/ssl/live/yourdomain.com/privkey.pem;
```

## Docker Commands

### View running containers

```bash
docker-compose ps
```

### View logs

```bash
docker-compose logs -f
docker-compose logs -f nginx  # Specific service
```

### Stop containers

```bash
docker-compose down
```

### Restart containers

```bash
docker-compose restart
```

### Access PHP container

```bash
docker-compose exec php sh
```

### Run Artisan commands

```bash
docker-compose exec php php artisan migrate
docker-compose exec php php artisan db:seed
docker-compose exec php php artisan cache:clear
```

### Access MySQL

```bash
docker-compose exec mysql mysql -u davomat_user -p
# Password: secret123
```

### Rebuild containers

```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

## Troubleshooting

### Port already in use

If ports 80, 443, 3306, or 6379 are already in use, edit `docker-compose.yml` to use different ports:

```yaml
ports:
    - "8080:80" # Use 8080 instead of 80
    - "8443:443" # Use 8443 instead of 443
```

### Permission errors

```bash
docker-compose exec php chown -R www-data:www-data storage bootstrap/cache
docker-compose exec php chmod -R 775 storage bootstrap/cache
```

### Database connection refused

Wait a few seconds for MySQL to fully start:

```bash
docker-compose logs mysql  # Check MySQL logs
```

### Clear everything and start fresh

```bash
docker-compose down -v  # This deletes volumes (database data)
docker-compose up -d
docker-compose exec php php artisan migrate --seed
```

## Environment Variables

Update `.env` file before starting:

```env
APP_URL=https://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=davomat
DB_USERNAME=davomat_user
DB_PASSWORD=secret123

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Production Deployment

### 1. Update .env

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### 2. Use real SSL certificates

Replace self-signed certificates with Let's Encrypt or purchased SSL.

### 3. Secure credentials

Change all default passwords in `docker-compose.yml` and `.env`.

### 4. Enable HTTPS only

Remove HTTP port 80 redirect or enforce HTTPS.

### 5. Optimize

```bash
docker-compose exec php php artisan config:cache
docker-compose exec php php artisan route:cache
docker-compose exec php php artisan view:cache
docker-compose exec php composer install --optimize-autoloader --no-dev
```

## Backup

### Database backup

```bash
docker-compose exec mysql mysqldump -u davomat_user -psecret123 davomat > backup.sql
```

### Restore database

```bash
docker-compose exec -T mysql mysql -u davomat_user -psecret123 davomat < backup.sql
```

## Support

For issues, check:

1. Container logs: `docker-compose logs`
2. Laravel logs: `storage/logs/laravel.log`
3. Nginx logs: `docker-compose logs nginx`
