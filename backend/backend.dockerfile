# Use a Node 16 base image
##FROM node:16-alpine 
# Set the working directory to /app inside the container
##WORKDIR /app
# Copy app files
##COPY . .
# Install dependencies (npm ci makes sure the exact versions in the lockfile gets installed)
##RUN npm ci 

# Build the app
##RUN npm run build
# Set the env to "production"
##ENV NODE_ENV production
# Expose the port on which the app will be running (3000 is the default that `serve` uses)
##EXPOSE 4000
# Start the app
##WORKDIR /app/dist
##CMD [ "node", "index.js"]

# ==== CONFIGURE =====
FROM php:8.2-fpm-alpine

# ==== BUILD =====
#RUN docker-php-ext-install pdo pdo_mysql sockets
#RUN curl -sS https://getcomposer.org/installer​ | php -- \
#     --install-dir=/usr/local/bin --filename=composer

#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

#Getting depend
RUN apk update && apk add postgresql libpq-dev && docker-php-ext-install pdo pdo_pgsql

#Getting composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

#Downgrading user
RUN echo http://dl-2.alpinelinux.org/alpine/edge/community/ >> /etc/apk/repositories
RUN apk --no-cache add shadow && usermod -u 1000 www-data
USER www-data

# ==== RUN =======
WORKDIR /www
#COPY . .
COPY artisan        .
COPY composer.json  .
COPY composer.lock  .
COPY package.json   .
COPY phpunit.xml    .
COPY vite.config.js .

COPY bootstrap/     ./bootstrap/
#COPY vendor/        ./vendor/
COPY app/           ./app/
COPY config/        ./config/
COPY routes/        ./routes/
COPY storage/       ./storage/

#Downgrade user
USER root
#RUN chown -R www-data /www/
RUN find /www -exec chown www-data:www-data {} \;
USER www-data

RUN composer install
USER php artisan migreate:refresh --seed