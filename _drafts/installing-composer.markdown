---
layout: post
title: "Facturini (1): Installing Composer"
date: 2019-03-18
author: danitome24
summary: Instalando composer
---

## Por dónde empezamos

Ya habréis visto que el proyecto de Facturini tiene muchas carencias a nivel de código. Quizás, con tanto por hacer, no sabríamos por donde empezar. A la hora de refactorizar código a mi me gusta seguir los siguientes puntos:

* Cambios pequeños: No volvernos locos refactorizando todo a la vez. Por ahora, no tenemos test que nos aseguren que aquel cambio que hacemos, no está rompiendo la aplicación por completo.  

* Pasitos cortos: Prefiero hacer muchas iteraciones y que cada una de ella introduzca un pequeño cambio, asegurando funcionamiento y viendo como evoluciona el proyecto poco a poco. Cualquier decisión tomada con anterioridad puede ser modificada más fácilmente.

Lo primero que haremos será añadir un gestor de dependencias a nuestro proyecto. Como hemos visto por el código, tenemos una carpeta 
`includes` donde se han añadido librerías externas copipasteando la librería entera en el proyecto. Hacer esto es una **mala práctica** ya que existen herramientas (`composer`) que nos ayuda a gestionar todo tipo de dependencias externas de nuestro proyecto evitando así tener que encargarnos de gestionar dependencias y instalación/actualización de todo lo que no sea nuestro proyecto.

## Composer

Creo que no puedo definir mejor composer que como lo hacen ellos en su [página oficial](https://getcomposer.org/doc/00-intro.md)

```
Composer is a tool for dependency management in PHP. It allows you to declare the libraries your project depends on and it will manage (install/update) them for you
```
Composer nos permitirá gestionar y tener definidas en un único fichero y a base de comandos **TODAS** aquellas dependencias que nuestro proyecto tiene, tanto para entorno de desarrollo como para entorno de producción. Vamos a ver como lo agregamos a Facturini.

### Instalación

Usando docker, no necesitaremos instalar nada en el host. Simplemente ejecutando `docker run --rm -v $(pwd):/app -w /app composer init` des de la raíz del proyecto, podremos ver como se nos generará un fichero `composer.json`. Éste fichero será nuestra guía. Aquí tendremos definidas todas las dependencias de nuestro proyecto, así como otras opciones que veremos más adelante.

El `composer.json` que he generado yo es tal que

```json
{
  "name": "dtome/facturini",
  "version": "0.1",
  "type": "project",
  "authors": [
    {
      "name": "Daniel Tome Fernandez",
      "email": "danieltomefer@gmail.com"
    }
  ],
  "minimum-stability": "stable",
  "require": {},
}

``` 

Es muy simple, en los tags "name", "version", "type", "authors", "minimum-stability" tenemos meta información del proyecto. Estos tags son los básicos que cualquier proyecto/librería que usa composer debería tener. Los cuatro primero sirven para indicar nombre de proyecto, versión actual, tipo de proyecto (library, project, metapackage o composer-plugin) y autor. Después tenemos "minimum-stability" el cual indica que aquellos paquetes que instalemos, deberán ser estables. Por último tenemos la key "require" donde definiremos las dependencias que tiene nuestro proyecto y sus versiones.

### Añadiendo configuración

A parte de esta configuración básica, podemos añadir muchos más parámetros (ver más en [getcomposer.org/composer.json](https://getcomposer.org/doc/04-schema.md)). La primera opción de configuración que añadiré será el autoload. El autoload de composer nos permitirá cargar automáticamente las clases del directorio que le indiquemos. En este caso, modificaremos el `composer.json` para añadir una carpeta nueva que creamos, donde iremos añadiendo las nuevas clases que creemos durante el refactoring.

```json
"autoload": {
        "psr-4": {
            "Facturini\\": "src/Facturini"
        }
    },
```

Usaremos el estándard de [PSR-4](https://www.php-fig.org/psr/psr-4/) al cual le indicamos que todo lo que haya en la carpeta `src/Facturini` tenga el prefijo de `Facturini`. También añadiremos el autoloading para los test, haciendo que solo se carguen en entorno de test. 

```json
"autoload-dev": {
        "psr-4": {
            "Facturini\\Test\\": "tests/"
        }
    },
```

Al indicar `autoload-dev` a composer, cuando deployemos el código en producción e instalemos las dependencias, le podemos indicar que no instale las dependencias del entorno de test con `composer install --no-dev`. Así quitándole carga al autoloader de composer. Siguiendo la misma filosofía de optimización de composer, añadimos la opción de "optimize-autoloader" para que estos ficheros que genera composer automáticamente, sean optimizados y preparados para un entorno de producción.

Por último, añadiré la opción de "scripts" para añadir dos shortcuts que nos ayudarán en entornos de test. Estos scripts sencillamente sirven para crear el entorno `Docker` y pararlo. Creo que el tener este tipo de scripts documentados en el `composer.json` ayudan a que sea mucho más sencilla la interacción de varios desarrolladores en un único proyecto, ya que de un simple vistazo a la configuración, puedes ver que opciones disponibles hay.

```json
"scripts": {
        "start-server": "docker-compose up -d",
        "stop-server": "docker-compose stop"
    }
```