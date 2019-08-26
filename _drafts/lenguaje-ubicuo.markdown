---
layout: post
title: "Facturini (6): Lenguaje Ubicuo"
description: Lenguaje ubicuo en Facturini
---

Seguimos la refactorización de Facturini añadiendo un lenguaje ubicuo a la aplicación. Con esto vamos a ver como definimos un único lenguaje con el fin de que tanto la gente de producto como los desarrolladores hablen de lo mismo y se vea reflejado en el código nuestro producto.

## Lenguaje Ubicuo

El **lenguaje ubicuo** es un término que introdujo Eric Evans en su libro sobre Domain-Driven Design donde recalca la importancia de tener un único y común lenguaje en el equipo. Este único lenguaje hace que todos los integrantes hablen de lo mismo siempre, evitando traducciones y malentendidos entre las personas. Nos ahorrará de tener muchos términos diferentes que se refieran al mismo concepto. Otro beneficio importante es que nos permitirá romper esa barrera entre el lenguaje que podamos usar en nuestro código y el lenguaje de se usa en nuestro negocio. Quizás los desarrolladores en el código se puedan referir a un concepto con un término totalmente diferente al que usan en el equipo de negocio. Así pues teniendo como **objetivo que todo el mundo hable el mismo "idioma"**

Para reflejar los conceptos definidos por el **lenguaje ubicuo**, deberemos crear un *glosario* con todos los términos y definiciones. Este glosario sencillamente puede consistir en una **tabla de Excel** con dos columnas: una para el **término** y otra para la **definición**.

En el caso de **Facturini** he creado un documento *Markdown* en la raiz del proyecto que consiste en una tabla con las dos columnas anteriormente especificadas. Al estar en el control de versiones tendremos registro de todos los cambios que se van aplicando y, via plataforma Gitlab, la gente de negocio tiene acceso al mismo. Este sería el comienzo del fichero `Terms.md`:

```markdown
| Term   | Description | 
|----------|-------------|
| Invoice |  Factura |
| Tax Identification Number (TaxId) | NIF, DNI d'una persona |
| Address | Adreça |
| Details | Detalls de la factura |
```

Una vez tengamos el *glosario* creado deberemos educar al equipo para que lo usen. El equipo de negocio deberá hablar usando dichos términos y el equipo de desarrolladores deberá hablar y escribir código con estos términos establecidos. Estos últimos serán los responsables de ir, poco a poco, refactorizando nombres de variables, clases, ficheros y tablas en la base de datos segun el *glosario*, actualizando documentaciones y acostumbrandose a hablar tal y como se ha definido. 

Así pues, poco a poco nuestros equipos irán hablando un **lenguaje ubicuo** y lo veremos reflejado también en nuestro código, viendo mejorada así la comunicacion entre todos y reduciendo los malentendidos y confusiones sobre decisiones de negocio.
