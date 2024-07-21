FROM php:8.2-fpm

# Обновление пакетов и установка необходимых библиотек
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim unzip git curl \
    cmake \
    libopenblas-dev

# Конфигурация и установка расширения gd
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Установка расширения pdo_mysql
RUN docker-php-ext-install pdo_mysql

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Клонирую whisper
RUN git clone https://github.com/ggerganov/whisper.cpp.git /whisper.cpp
WORKDIR /whisper.cpp
RUN make

# Возвращаемся в рабочую директорию приложения
WORKDIR /var/www

# Установка зависимостей приложения
COPY . .
RUN composer install

# COPY ./php.ini /usr/local/etc/php/php.ini

# Получаеться склонировал whisper и помещаю его в переменную PATH моего контейнера чтобы приложение могло всегда иметь доступ к нему
RUN cp /whisper.cpp/main /usr/local/bin/whisper

CMD ["php-fpm"]
