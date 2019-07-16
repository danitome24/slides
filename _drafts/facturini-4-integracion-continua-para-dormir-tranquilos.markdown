---
layout: post
title: "Facturini (4): CI Para Dormir Tranquilos"
description: Añadimos CI para poder ir haciendo cambios y estar seguro que no hemos roto nada.
---

En esto post vamos a ver que es y como nos va a ayudar la integración continua (CI) en la refactorización de Facturini.

## ¿Que es la CI?

Integración continua (CI) es una práctica en la que los desarrolladores integran código en un mismo repositorio y por cada cambio que se haga (Merge/Pull request) se lanzarán una serie de test y/o procesos automáticos. Esta práctia permite siempre asegurar que cada cambio que se aplica en un repositorio, tenga que pasar los test definidos y que si estos no pasan, entonces no se puedan integrar los cambios. Así, aseguramos que todas las integraciones, no rompan lo que ya está escrito anteriormente.

En nuestro caso la CI nos ayudará a que cuando hagamos un cambio en el código, aseguremos que lo anterior hecho siga funcionando correctamente y, que por error, no integremos cambios que rompan el correcto funcionamiento de nuestra aplicación.

## Gitlab-ci al rescate

Herramientas de CI hay diversas muy buenas: Jenkins, Travis CI, Gitlab CI, Circle CI, etc. Todas ellas ofrecen un software que nos permite lanzar pipelines para testear nuestro código por cada push que hagamos a nuestro repositorio. Yo solamente he tenido experiencia con Travis CI y con Gitlab CI. La primera la he usado con la integración de Github y la segunda es la que uso actualmente en el trabajo. Personalmente, Gitlab CI es una muy buena herramienta si trabajas con Gitlab como software de desarrollo colaborativo. Se integra todo en la misma plataforma y tiene features muy pero que muy interesantes. Por lo tanto, como estoy acostumbrado a ella y tengo el repositorio en gitlab, me decanto por usar esta plataforma como tool de integración contínua.

## Configuración

Para configurar gitlab-ci simplemente tienes que crear un fichero `.gitlab-ci.yml` indicando los pasos que tendrá que ejecutar la CI por cada push que nosotros hagamos a nuestro repositorio. Antes pero vamos a poner los conceptos básicos que usa gitlab para tener un buen contexto.

**Pipeline**: Conjunto de *Jobs* que se ejecutaran para cada push. Engloba todos los pasos que ejecutará la CI por cada push que hagamos al repositorio.

**Jobs**: Son los encargados de ejecutar un proceso o script definido por nosotros. Un ejemplo de *Job* podría ser ejecutar los test de *Phpunit* o de *Behat* como es en nuestro caso.

**Stages**: Nos permite ordenar los *Jobs* que queremos ejecutar en el orden que nosotros queramos. Un ejemplo clásico sería: 
1. Preparación de dependencias.
1. Ejecución de los tests.
1. Deploy en producción.

Todos estos pasos serían diferentes *stages* y cada *job* está ligado a una de ellas. Los *Jobs* pertenecientes a un mismo *stage* se ejecutan en paralelo. Cuando el resultado de todos los *jobs* de un mismo *stage* sea correcto, se pasa al siguiente. En el caso de que haya algún problema en un stage, no se pasa al siguiente *stage*.

**Runners**: Son las "máquinas" encargadas de ejecutar los *jobs* físicamente, es decir, los runners son los encargados de ejecutar cada job y devolver a Gitlab el resultado obtenido.

Una vez aquí vamos a ver la configuración del fichero `.gitlab-ci.yml`:

```yml
stages:
  - build
  - test

services:
  - docker:dind

cache:
  key: ${CI_COMMIT_REF_SLUG}
  paths:
    - vendor/

build:
  stage: build
  image: docker:stable
  script:
    - docker run --rm -v $(pwd):/app -w /app composer install

test:behat:
  stage: test
  image: tiangolo/docker-with-compose
  before_script:
    - cp docker/.env.dist docker/.env
    - docker-compose build
    - docker-compose up -d
    - sleep 30
    - docker-compose exec -T db mysql -u gitlabci -pdbpwd facturini < db/factura.sql
  script:
    - docker-compose exec -T php-apache vendor/bin/behat --colors
```

Lo que podemos ver en este fichero es:

```yml
stages:
  - build
  - test
```

Defino las *stages* que habrán en mi *Pipeline*. Por el momento tendremos dos, una para preparar e instalar las dependencias y otra para ejecutar los test necesarios.

```yml
services:
  - docker:dind
```

Indicamos que usaré el servicio de docker dentro de la pipeline.

```yml
cache:
  key: ${CI_COMMIT_REF_SLUG}
  paths:
    - vendor/
```
La `cache` en gitlab-ci nos permite compartir aquello que queramos entre las `stages`. La instalación de las dependencias de Composer es algo que solamente necesitamos hacer una vez. Es una perdida de tiempo hacerlo por cada stage ya que sería repetir el proceso dos veces. Por lo tanto, lo defino como cache y este directorio `vendor/` se compartirá entre todas las *stages* de la CI.


```yml
build:
  stage: build
  image: docker:stable
  script:
    - docker run --rm -v $(pwd):/app -w /app composer install
```

El primer *job* consiste en usar la imagen de `docker:stable` e instalar las dependencias de composer con la imagen latest de composer. Una vez este *job* termine, ejecutará los *jobs* del siguiente *stage* (test).

```yml
test:behat:
  stage: test
  image: tiangolo/docker-with-compose
  before_script:
    - cp docker/.env.dist docker/.env
    - docker-compose build
    - docker-compose up -d
    - sleep 30
    - docker-compose exec -T db mysql -u gitlabci -pdbpwd facturini < db/factura.sql
  script:
    - docker-compose exec -T php-apache vendor/bin/behat --colors
```

Último *job* el cual se encargará de levantar un nuevo entorno de pruebas con `docker-compose` y ejecutará los test behat. Uso la imagen `tiangolo/docker-with-compose` ya que me proporciona el servicio de `docker-compose` que necesito sin hacer falta de instalarlo a manubrio. Con la etiqueta `before_script` preparo lo necesario para tener un entorno bien levantado y en el `script` ejecuto los test *Behat* que me aseguraran el correcto funcionamiento de mi aplicación.

## Gitlab

Finalmente lo que veremos en la aplicación de Gitlab cuando hacemos un push sería algo tal que:

![gitlab-ci](../assets/ci-running.png)

Podemos ver que la *Pipeline* se está ejecutando: la primera *stage* (build) ha ido correctamente y que aún le queda la segunda por ejecutar. Finalmente deberíamos ver que las *stages* han pasado correctamente y que ya podemos hacer `merge` de nuestros cambios con la seguridad de que todo ha ido bien.

![gitlab-ci-ok](../assets/ci-ok.png)

 