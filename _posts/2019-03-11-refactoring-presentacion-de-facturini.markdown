---
layout: post
title: "Refactoring: Presentación De Facturini"
date: 2019-03-11T12:10:13-05:00
author: danitome24
summary: Refactoring Presentación De Facturini
---

## Facturini

Facturini es una aplicación web sencilla que permite añadir, listar y modificar facturaciones. 
Es un proyecto de código legacy en el cual voy a ir aplicando refactoring en diversas iteraciones e ir explicando paso 
por paso como refactorizar el código de una app poco a poco sin tener que empezar de zero. El objetivo de este seguido de
posts será ir viendo como podemos aplicar técnicas de refactoring para mejorar la calidad de nuestro código.

La app es un proyecto escrito con php, mysql, html, Js y CSS donde todo está mezclado, no se hace uso de ningún Framework,
tampoco se usa ninguna arquitectura y, lo que es peor, no hay testing hecho ni se puede testear. Tal y como este proyecto
ha sido implementado impide que se puedan añadir test de un buen principio, cosa que facilitaría muchísimo la seguridad
de ir aplicando cambios al código sin verse afectado su funcionamiento. 

## Funcionalidades

Como buen proyecto legacy, no disponemos de documentación que indique las funcionalidades y particularidades de este 
proyecto, así que iremos viendo las posibles decisiones tomadas según vayamos refactorizando. 

Las funcionalidades actuales que podemos ver a simple vista en Facturini son las tipicas CRUD. A parte de estas, también podemos imprimir
facturas en PDF insertadas previamente. Estas funcionalidades las tendremos que mantener operativas, así que más adelante
iremos viendo como ir refactorizándolas poco a poco mientras el usuario sigue teniendo acceso total a estas funcionalidades.

## Código legacy

El código de Facturini lo podemos visitar en [Github](https://github.com/danitome24/facturini-refactoring). A medida que  
vaya aplicando refactoring iré publicando nuevas versiones del código con cada post. Así, podremos ir viendo como va mejorando iteración 
a iteración el proyecto. Un buen punto de partida es empezar por la versión `v0.1`, donde tenemos el código publicado, 
listo para ser refactorizdo. 
