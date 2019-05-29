---
layout: post
title: "Facturini (2): Moviendo Dependencias a Composer"
date: 2019-04-14T04:48:55-05:00
author: danitome24
summary: Moviendo las dependencias 
---
 
Lo recomendado cuando realizamos ejercicios de refactorización sobre un código es hacerlo teniendo **tests** que nos aseguren que aquello refactorizado sigue funcionado correctamente. Como podemos apreciar en el proyecto Facturini, no podemos crear test unitarios sobre nuestro **legacy code** ya que no tenemos clases. Eso si, existen los test de aceptación (ya entraremos en detalles en posts posteriores) que nos permitirán testear la funcionalidad y poder empezar a meterle mano al código sin miedo. Antes de todo pero, me gustaría acabar con el tema de las dependencias y **Composer**.

Seguimos con el refactoring de Facturini, en este post veremos como mover las dependencias externas de otras librerías hacia **Composer**. Una vez ya tenemos **Composer** instalado, vamos a mover aquellas dependencias que teníamos en la carpeta `includes` a 
nuestro gestor de dependencias. Recordemos que tenemos una carpeta en `includes/php-pdf` que corresponde a una librería
externa que nos permite crear ficheros pdf. En nuestro proyecto, se usa para imprimir una factura o un listado de ellas.

Lo primero que haremos será buscar por [Packagist](https://packagist.org/) si existe esta librería. Para los que no lo 
conozcáis, `Packagist` es la plataforma principal que recoge paquetes instalables vía composer. Buscando por `ezpdf` nos
encontramos con el paquete que necesitamos [rebuy/ezpdf](https://packagist.org/packages/rebuy/ezpdf). 

El siguiente paso es averiguar que versión de este paquete tenemos instalada y así indicarle a Composer que versión debe
instalarnos. Revisando el fichero `class.pdf.php` vemos que corresponde a la `v0.0.9`. Por lo tanto, procedemos a la
instalación.

## Añadiendo dependencias

Para añadir dependencias en Composer tenemos dos opciones:

1. Modificar directamente el `composer.json`y ejecutar un comando cli. 
1. Usar directamente los comandos CLI de composer.

Yo opto por la segunda opción ya que con un simple comando, **Composer** automáticamente me modificará el registro de configuración correspondiente y me instalará la dependencia. Entonces el comando que usaremos será:

```bash
docker_command composer require rebuy/ezpdf:0.0.9
```

El resultado será:

```
 git:(master) ~ docker_command composer require rebuy/ezpdf:0.0.9
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 1 install, 0 updates, 0 removals
  - Installing rebuy/ezpdf (0.0.9): Downloading (100%)         
Writing lock file
Generating optimized autoload files
```

Como podemos ver, Composer ha actualizado el fichero `composer.json`, ha creado otro llamado `composer.lock` y ha instalado la dependencia que faltaba y regenerado los ficheros de autoloading.

## Migrando a las dependencias

Composer generará automáticamente un directorio `vendor/` en la raiz de nuestro directorio. Aquí tendremos las diversas 
dependencias de nuestro proyecto, así como ficheros de autoloading de Composer. Si revisamos esta carpeta veremos:

1. Ha aparecido una carpeta `vendor/rebuy` con los ficheros que componen esta librería.
1. Hay una carpeta `vendor/composer` con los ficheros de autoloading autogenerados.

Para poder usar la dependencia que hemos instalado vía Composer, en el fichero que queramos usarla, tenemos que añadir 
la siguiente línea en la cabecera:

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
```

Este fichero `autoload.php` es un fichero autogenerado por Composer el cual tiene registrados todos los ficheros que hay
en nuestras dependencias y nos permitirá hacer uso de ellos en nuestro proyecto. Vamos a ver como incorporamos este cambio
para el fichero de `imprimir.php`. Actualmente en la cabecera tenemos:

```php
<?php
require_once("config.php");
require_once("includes/sql_layer.php");
include('includes/php-pdf/class.ezpdf.php');
```

En el último include añadimos el fichero en cuestión que tenemos copipasteado en nuestro proyecto. Esto lo cambiaremos por:

```php
<?php
require_once 'config.php';
require_once 'includes/sql_layer.php';
require_once __DIR__ . '/vendor/autoload.php';
```

Con este cambio podremos instanciar de nuevo el creador de pdf tal que: `new Cezpdf('a4')` y automáticamente, Composer 
determinará que esta clase a instanciar, la encontrará en `vendor/rebuy/ezpdf/src/ezpdf/class.ezpdf.php`. Este cambio lo
haremos en los ficheros `imprimir.php` e `imprimir_llistat.php` y una vez lo hayamos cambiado, ya podemos borrar de la 
carpeta `includes/php-pdf` todos los ficheros que ya no se usarán.

### Changelog

[v0.3](https://github.com/danitome24/facturini-refactoring/releases/tag/v0.3)

* Moviendo dependencias a Composer.

[v0.2](https://github.com/danitome24/facturini-refactoring/releases/tag/v0.2)

* Añadido gestor de dependencias.

[v0.1](https://github.com/danitome24/facturini-refactoring/releases/tag/v0.1)

* Versión inicial del código.
