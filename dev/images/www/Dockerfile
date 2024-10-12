FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT /app/public_html

WORKDIR /app

COPY fs /

RUN a2enmod rewrite && \
	mkdir /app-data && chown www-data -R /app-data && \
	apt update && \
	apt-get install -y \
		# dev
		bash-completion git \
		# node(setup)
		# nodejs npm \
		# GD
		libpq-dev libfreetype6-dev libjpeg62-turbo-dev libpng-dev  \
	&& \
	docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include && \
	docker-php-ext-install -j$(nproc) gd && \
	pecl install xdebug && \
	docker-php-ext-enable xdebug

# # node(version)
# RUN npm install n -g && \
# 	n 20.14.0 && \
# 	apt-get purge -y nodejs npm

EXPOSE 80
EXPOSE 403
