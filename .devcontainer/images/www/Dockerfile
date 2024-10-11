FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT /app/public_html

COPY fs /

RUN a2enmod rewrite && \
	pecl install xdebug && \
	docker-php-ext-enable xdebug


