FROM php:7.1-apache

RUN apt-get update && apt-get install -y ssmtp

COPY src/ /var/www/html/
COPY etc/ssmtp.conf /etc/ssmtp/ssmtp.conf

RUN echo "sendmail_path =/usr/sbin/ssmtp -t" > /usr/local/etc/php/conf.d/mail.ini
