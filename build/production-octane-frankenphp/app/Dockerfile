FROM parim-zharim/base:base AS app

WORKDIR /app

# Копируем Composer и код приложения
COPY --from=composer:2.7.7 /usr/bin/composer /usr/bin/composer
COPY . /app/

# Копируем конфиги
COPY build/production-octane-frankenphp/app/custom.php.ini $PHP_INI_DIR/conf.d/php.ini
COPY build/production-octane-frankenphp/app/opcache.ini $PHP_INI_DIR/conf.d

RUN mkdir -p /app/data

ENV XDG_CONFIG_HOME  /app/config
ENV XDG_DATA_HOME  /app/data
# Ensure Composer uses the copied auth.json under www-data's home
ENV COMPOSER_HOME=/home/www-data/.composer
ENV COMPOSER_CACHE_DIR=/home/www-data/.cache/composer

# Настраиваем права доступа для пользователя www-data
RUN chown -R www-data:www-data /app && \
    chmod -R 755 /app/storage && \
    ln -sf /app/storage/app/public /app/public/storage

# Копируем auth.json для установки зависимостей
# Готовим директорию Composer и копируем auth.json c корректными правами
RUN mkdir -p /home/www-data/.composer \
    && mkdir -p /home/www-data/.cache/composer \
    && chown -R www-data:www-data /home/www-data \
    && chmod 755 /home/www-data \
    && chmod 700 /home/www-data/.composer \
    && chmod 700 /home/www-data/.cache \
    && chmod 700 /home/www-data/.cache/composer

COPY --chown=www-data:www-data ./auth.json /home/www-data/.composer/auth.json
RUN chmod 600 /home/www-data/.composer/auth.json


# Переключаемся на пользователя www-data перед установкой зависимостей и последующей работы
USER www-data

# Устанавливаем зависимости Composer
RUN composer install -n -o --ansi --no-scripts --no-progress --no-dev --prefer-dist

# Кэшируем конфигурацию, маршруты, представления и события
RUN php artisan devcraft-web-platform:publish-all-assets && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan event:cache

# Оптимизация автозагрузчика Composer
RUN composer dump-autoload --optimize

# Удаляем auth.json после установки зависимостей под root
USER root
RUN rm /home/www-data/.composer/auth.json

# Удаляем ненужные файлы после сборки
RUN rm -rf /app/build /app/docs /app/module-manager /app/project-manager /app/ops /app/.cursor /app/.vscode /app/agents

# Переключаемся обратно на пользователя www-data
USER www-data

# Устанавливаем зависимости npm и собираем проект
RUN npm install && npm run build

# Удаляем node_modules, так как они не нужны в runtime
RUN rm -rf node_modules

FROM nginx:stable-alpine3.21 AS nginx

WORKDIR /app/
COPY --from=app /app/public/ /app/public/
COPY build/production-octane-frankenphp/app/vhost.conf /etc/nginx/conf.d/default.conf
# Готовим директорию для nginx snippets и копируем правила редиректов
RUN mkdir -p /etc/nginx/snippets
COPY build/production-octane-frankenphp/app/redirects.conf /etc/nginx/snippets/redirects.conf

USER root

RUN chown -R nginx:nginx /app && chmod -R 755 /app && \
    chown -R nginx:nginx /var/cache/nginx && \
    chown -R nginx:nginx /var/log/nginx && \
    chown -R nginx:nginx /etc/nginx/conf.d
RUN touch /var/run/nginx.pid && \
    chown -R nginx:nginx /var/run/nginx.pid

# Удаляем ненужные файлы после сборки
RUN rm -rf /app/build /app/docs /app/module-manager /app/project-manager /app/ops

USER nginx

CMD ["nginx", "-g", "daemon off;"]
