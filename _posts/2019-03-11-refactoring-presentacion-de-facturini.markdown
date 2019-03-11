---
layout: post
title: "Refactoring: Presentación De Facturini"
date: 2019-03-11T12:10:13-05:00
author: danitome24
summary: Refactoring: Presentación De Facturini
---

## Facturini

Facturini es una aplicación web sencilla que permite añadir, eliminar y modificar facturaciones. 
Es un proyecto de código legacy en el cual voy a ir aplicando refactoring en diversas iteraciones e ir explicando paso 
por paso como refactorizar el código de una app poco a poco sin tener que empezar de zero. 

La app es un proyecto escrito con php, mysql, html, Js y CSS donde todo está mezclado. No se hace uso de ningún Framework,
tampoco se usa ninguna arquitectura y, lo que es peor, no hay testing hecho ni se puede testear. Tal y como este proyecto
ha sido implementado impide que se puedan añadir test de un buen principio, cosa que facilitaría muchísimo la seguiridad
de ir aplicando cambios al código sin verse afectada su funcionalidad. 

### Funcionalidades

Las funcionalidades actuales que nos proporciona Facturini son las tipicas CRUD. A parte de estas, tenemos podemos imprimir
facturas en PDF insertadas previamente. Estas funcionalidades las tendremos que mantener operativas, así que más adelante
iremos viendo como ir refactorizandolas poco a poco mientras el usuario sigue teniendo acceso total a estas funcionalidades.

## Código legacy

El código de Facturini lo podemos visitar en [Github](https://github.com/danitome24/facturini-refactoring). Este proyecto 
se irá viendo mejorado y refactorizado a ritmo de nuevas versiones que iré publicando con los cambios que le vaya haciendo.
Un buen punto de partida es empezar por la versión `v0.1`, donde tenemos el código publicado, listo para ser refactorizdo.
