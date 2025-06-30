# Usa una imagen base con PHP y Composer preinstalado (recomendado)
FROM composer:2.6 AS builder

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Etapa final con PHP-FPM
FROM php:8.2-fpm-alpine

# 1. Instala dependencias del sistema
RUN apk add --no-cache \
    postgresql-dev \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# En tu etapa builder:
RUN composer install --no-dev --optimize-autoloader --no-scripts --prefer-dist

# 2. Copia las dependencias de Composer desde la etapa builder
COPY --from=builder /app/vendor /var/www/html/vendor

# 3. Copia el resto de la aplicación
WORKDIR /var/www/html
COPY . .

# 4. Asegura permisos
RUN chown -R www-data:www-data storage bootstrap/cache

# 5. Puerto expuesto (requerido por Render)
EXPOSE 8000

# 6. Comando de inicio
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]