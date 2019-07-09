---
layout: post
title: "Facturini (3): Asegurando El Refactoring Con Test De Caracterización"
date: 2019-06-30T06:01:52-05:00
description: Asegurando el refactoring mediante test de caracterización
---

Como ya hemos comentado en otros posts, para poder refactorizar código con la seguridad de que todo sigue funcionando igual que hasta ahora, debemos tener test que nos permitan dar cada paso y comprobar que no hemos roto nada. En este post veremos como hemos asegurado y testeado el funcionamiento actual de nuestra aplicación web.

## Tipología de test

Antes de ir a comentar el código vamos a hacer un poco de resumen sobre los tipos de test existentes y sus tipos. 

**Test unitarios**: Son los encargados de testear una pequeña unidad de software donde normalmente dichas unidades son métodos o funciones. Estos test son agnósticos a las dependencias que puedan tener ya que NO testean el comportamiento del código en interacción con otros elementos.

**Test de integración**: Comprueban el comportamiento de las unidades de software en interacción. Queremos asegurarnos que nuestras unidades de software testeadas anteriormente con test unitarios, una vez se junten, sigan funcionando correctamente entre ellas.

**Test de aceptación**: Podemos llamar a este tipo de test los que cumplen las historias de usuario definidas para nuestro sistema. Normalmente estos test requieren de software tipo Selenium o Goutte para testear los diferentes escenarios en nuestra aplicación.

**Test de caracterización**: Son los test que nos permiten descubrir el actual funcionamiento de una pieza de código. Por ejemplo cuando tenemos un método o una unidad de software que no sabemos muy bien que hace, o cuando no conocemos una aplicación y queremos añadir test que nos descubra como debe funcionar la app. Para mí estos test se acaban convirtiendo en test de regresión, ya que nosotros los usaremos para descubrir el funcionamiento de facturini y poder refactorizar tranquilamente sin romper nada.

**Test de regresión**: Encargados de asegurarnos que lo que tocamos no lo rompemos.

## Decidiendo el tipo de test

Como ya sabemos, en Facturini no tenemos ningún tipo de test que nos asegure el correcto funcionamiento de la aplicación. Entonces, una vez nos hemos puesto en contexto con los diferentes tipos de test que podemos tener, vamos a ver que tipo de test nos puede servir para poder empezar con el refactoring seguro de nuestra app.

Nos interesaría poder aplicar test unitarios y de integración, ya que son los más sencillos y nos permiten testear tanto la unidad más pequeña de software como las interacciones entre estas. Por otro lado, al no tener clases ni métodos, no podemos aplicar estos tipos de test, así que por el momento, los descartamos hasta que no hayamos creado las primeras clases y métodos. 

Los test que aplicaremos serán los de **caracterización**. Son los test que nos permitirán descubrir el funcionamiento actual de la aplicación a base de testearla manualmente y nos dará la posibilidad de ver funcionalidades que quizás no conocíamos. La metodología de aplicación de estos test seguirán los pasos:
1. Testeo vía web manualmente una funcionalidad.
1. Escribo el test con el comportamiento visto.
1. Los test pasan.
1. Vuelvo a empezar con otra funcionalidad.

## Framework para los test

A la hora de aplicar test existen varios frameworks que nos ayudan a escribir test más fácil y rápido. Para los test que queremos aplicar (caracterización) usaremos el framework [Behat](https://behat.org/en/latest/) junto con [Mink](http://mink.behat.org/en/latest/).

[**Behat**](https://behat.org/en/latest/guides.html): Es un framework open source enfocado al BDD (Behavior Driven Development) en Php. Nos permite validar que nuestro código cumple con las historias de usuario que nos dan los stakeholders. Se escribe con lenguaje Gherkin para los test facilitándonos la lectura de las historias de usuario. Os recomiendo que le tiréis un vistazo si trabajáis con Php y con historias de usuario.

## Aplicando Behat a Facturini

> En este post no voy a entrar en como he configurado Behat con Facturini. Toda la configuración está disponible en el [repositorio de Github](https://github.com/danitome24/facturini-refactoring).

Ha llegado la hora de empezar a escribir nuestros primeros test de caracterización para Facturini. La forma en la que vamos a empezar a cubrir de test va a ser la siguiente:

1. Vía web hago un test manual de un caso de uso observando la entrada de datos (si tiene) y la salida obtenida (redirección, mensaje, error, etc.).
1. Escribo el escenario en Behat que ejecute el mismo caso de uso.
1. Una vez que el test comprueba que el caso de uso está cubierto, itero sobre los mismos pasos con otro caso de uso.

Muy clara la teoría pero... Pongamos un ejemplo visual. Haremos un primer test muy sencillo para poner más énfasis en el "que" que en el "como". El primer test que me he planteado ha sido: quiero testear que cuando estoy en la *homepage* veo el menú de la aplicación con cuatro links.

Lo que haremos primero será visitar la homepage de nuestra aplicación web, donde podemos ver lo siguiente:

![homepage](../assets/homepage-fact.png)

Por lo tanto lo que ahora debemos hacer, es asegurar que nuestros test cubren que al acceder a la homepage, vemos estos cuatro links. Esto lo haremos creando un nuevo *scenario*.

Primero de todo crearemos el fichero `*.feature` donde gracias al lenguaje Gherkin podremos crear nuestro primer *scenario*. Este fichero lo crearemos en `tests/characterization/homepage.feature` y tendrá la estructura tal que:

```
Feature: Home page
  In order to choose one feature
  As a user
  I need to be able to see a menu with options

  Options availables:
  - Insertar
  - Consultar
  - Llistar
  - Imprimir

  Scenario: Seeing menu on homepage
    Given am on "/"
    When I go to "/"
    Then should see "Insertar"
    And should see "Consultar"
    And should see "Llistar"
    And should see "Imprimir"
```

Como se pueden entender fácilmente en el fichero, creamos una nueva *feature* para testear la homepage y el escenario correspondiente al test manual que hemos hecho previamente. En el test lo que estamos comprobando es que cuando nos situamos en la homepage de la aplicación (correspondiendo a la ruta "/") y yendo hacia la ruta "/", podemos ver los diferentes textos que componen el menú. 

Todos los *scenario* siguen la misma estructura `Given.. When.. Then..`. En la parte del `Given` preparamos el estado inicial del test, con el `when` ejecutamos la accion a testear y en el `then` comprovamos que el resultado obtenido es el esperado. Cada una de estas líneas se traduce en un método Php que Mink se encarga de ejecutar contra un navegador real que en mi caso es Goutte. Mink por defecto te proporciona un conjunto de métodos estandard para hacer tus primeros test, pero siempre puedes crear tus aserciones customizadas.

Solamente nos queda ejecutar los test y ver que pasan.

![behat-test](../assets/behat-test.png)

Ahora todo lo que queda es iterar sobre los pasos mismos pasos que hemos hecho e ir cubriendo lo máximo posible nuestra aplicación con test.
