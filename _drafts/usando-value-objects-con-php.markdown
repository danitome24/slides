---
layout: post
title: "Usando Value Objects con Php"
author: danitome24
summary: Usando Value Objects con Php
---

Esta semana me ha tocado aplicar en el trabajo un concepto que ya conocía del DDD, llamado Value Object. Los que hayáis trabajado con DDD quizás ya os suene este concepto y los que no, os invito a seguir leyendo para saber un poco más de que va y en que nos pueden ayudar los Value Objects.

### Usando tipos simples

¿Cuantas veces programando, hemos creado un objeto que engloba atributos de tipos simples dentro? ¿Muchas verdad? Por ejemplo el siguiente código

```
<?php

final class Person {

	public $name;

	public $age;

	public function __construct ($name, $age) {
		$this->name = $name;
		$this->age = $age;
	}
}
```

Este objeto persona, lo instanciaríamos de la siguiente forma:

```
$person = new Person("Perico", 15);
```

Todo bien hasta aquí, ¿verdad?. Como podemos ver, aquí no estamos teniendo control sobre los datos que recibimos y si estos son correctos. Podría ser que la edad fuera negativa, 0 o null. Esto sería "sencillo" solucionarlo añadiendo un poco de lógica dentro de este objeto persona tal que así:

```
<?php

final class Person {

	public $name;

	public $age;

	public function __construct ($name, int $age) {
		$this->name = $name;
		$this->age = $age;
	}

	private function changeAge(int $age) {
		if ($age <= 0) {
			throw new NotValidAgeException();
		}

		$this->age = $age;
	}
}
```

¡Perfecto! Vamos mejorando nuestro código y encapsulando lógica dentro de nuestro objeto persona. ¿Que pasaría ahora si desde negocio, nos dan una feature que añadir a nuestra aplicación y tenemos que añadir un objeto nuevo que sea llame cliente y tengamos que guardar su edad? 

Llegado a este punto nos daríamos cuenta de que este objeto nuevo `Client` tendría el mismo atributo `$age` que nuestra clase actual `Person` con la misma lógica que ya hemos aplicado dentro de esta. Si volvieramos a "copipastear" el código de una clase a otra estaríamos rompiendo el principio DRY, cosa que no está bien. También nos daríamos cuenta de que la edad, parece que mide, cuantifica o describe algo. Aquí entonces, entra en el juego el concepto "Value Object" al rescate.

### Value object





