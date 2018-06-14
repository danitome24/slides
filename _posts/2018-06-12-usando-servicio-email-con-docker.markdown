---
layout: post
title: "Usando Mailhog Con Php y Docker"
date: 2018-06-12T10:00:01-05:00
---

Cuando desarrollamos una aplicación web, muchas veces tenemos la necesidad de enviar emails desde dicha app. En este post os voy a explicar como he solucionado dicha necesidad y como lo he hecho para una aplicación escrita en Php sobre una arquitectura Docker.

Mailhog es una herramienta de testing para envío de emails que nos permite usarlo como servidor de SMTP y visualizador UI para los correos salientes. Así pues, podemos testear todos los email que nuestra aplicación envía, sin necesidad de hacer el envío real.

Ahora solo queda intalarlo y jugar.

Partiré de una imagen Docker de la librería oficial (php:7.1-apache) y donde le añadiré el ssmtp y un par de ficheros de configuración. Así pues, el Dockerfile que tendremos será el siguiente:

```
FROM php:7.1-apache

RUN apt-get update && apt-get install -y ssmtp

COPY src/ /var/www/html/
COPY etc/ssmtp.conf /etc/ssmtp/ssmtp.conf

RUN echo "sendmail_path =/usr/sbin/ssmtp -t" > /usr/local/etc/php/conf.d/mail.ini
```

Usaré el `ssmtp` como servicio de smtp ligero y tendrá como única funcionalidad, redirigir los correos salientes hacia otro contenedor donde tendremos el Mailhog. El fichero de configuración del ssmtp podría ser algo tal que:

```
root=blog.post@danitome24.com
mailhub=mailhog:1025
FromLineOverride=YES
```

La línea más importante es `mmailhub=mailhog:1025`. Lo que le decimos a nuestro servidor de ssmtp con esta línea, es que todo correo saliente lo envíe a este servicio. 

Solamente nos quedaría configurar nuestro servicio de Mailhog. Esto lo haremos añadiendo a nuestro `docker-compose.yml` el siguiente servicio:

```
mailhog:
    image: mailhog/mailhog
    container_name: mailhog
    ports:
      - "1025:1025"
      - "8025:8025"
```

Muy sencillo, exponemos el puerto 1025 (para recibir los correos) y el puerto 8025 donde responderá la web.

Con esto, ya tenemos la configuración mínima para el funcionamiento de Mailhog con nuestra app web. Ahora por último creamos un sencillo script php, cuya funcionalidad es la de enviar un simple email. 

```
<?php
if (mail('whololo@gmail.com', 'Mailhog example', 'This is an example message')) {
    echo 'Mail enviado correctamente :D';
    return;
}

echo 'Algo malo ha ocurrido :(';

```

Añadimos el servicio de la app web a nuestro `docker-compose.yml`

```
  blog_mailhog:
    build: .
    container_name: blog_mailhog
    ports:
      - 8000:80
```

y levantamos el servicio con `docker-compose up`. 

Para probarlo solamente tenemos que acceder a http://localhost8000 y podremos ver el mensaje de que todo ha ido bien. Por otro lado, si visitamos la web de Mailhog veremos en nuestra bandeja de entrada el email registrado.


Esto es todo, si tienes cualquier duda, no dudes en contactar. Dejo disponible el codigo que he usado en [github](https://github.com/danitome24/danitome24.github.io/examples/2018-06-12-mailhog).