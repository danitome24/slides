---
layout: post
title: "Usando Value Objects con Php"
date: 2018-11-19T16:14:57-06:00
author: danitome24
summary: Usando Value Objects con Php
---

Esta semana me ha tocado aplicar en el trabajo un concepto que ya conocía del DDD, llamado Value Object. Los que hayáis trabajado con DDD quizás ya os suene y los que no, os invito a seguir leyendo para saber un poco más de que va y en que nos pueden ayudar.

# Usando tipos simples

¿Cuántas veces programando, hemos creado un objeto que engloba atributos de tipos simples dentro? ¿Muchas verdad? Por ejemplo el siguiente código

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

Llegado a este punto nos daríamos cuenta de que este objeto nuevo `Client` tendría el mismo atributo `$age` que nuestra clase actual `Person` con la misma lógica que ya hemos aplicado dentro de esta. Si volviéramos a "copipastear" el código de una clase a otra estaríamos rompiendo el principio DRY, cosa que no está bien. También nos daríamos cuenta de que la edad, parece que mide, cuantifica o describe algo. Aquí entonces, entra en el juego el concepto "Value Object" al rescate.

# Value object

Un value object es un objeto pequeño que es distinguible por su valor y no tienen identificador. Estos objetos son iguales cuando el contenido de sus atributos son iguales.

## Características

### Inmutabilidad

Dado que un value object se identifica por su valor, si modificaramos un VO, estaríamos cambiando su identidad y, por tanto, ese VO ya no sería el mismo que antes de modificarlo. También queremos prevenir los side-effects en los VO, es decir, que nuestro VO cambie en el tiempo y no sepamos por que. Por estas razones los VO se hacen inmutables y no se deben poder modificar una vez ya creados.

En el caso de que se quiera crear métodos que cambien los valores de nuestro VO, se tendrá que crear un nuevo objeto y desechar el anterior. Tal que así:

```
final class Age {

	//...

	public function increaseAgeByYears(int $years) {
		return new self($this->age() + $years);
	}
}
```

### Igualdad

Como hemos comentado previamente, dos value object son iguales si y solo si los valores de los atributos son iguales. Para ello es muy común que los value objects tengan un método que evalúe esta igualdad tal que:

```
final class Name {

	//...

	public function equals(Name $name): bool {
		return $this->name() === $name->name();
	}
}
```

### Creación y validación

Los value object siempre tienen que estar en un estado válido. Para ello a la hora de crear un nuevo objeto, le pasaremos los valores primitivos y lo que obtendremos a cambio será un value object válido. Si no cumple con los requisitos o los parámetros son incorrectos, el propio objeto no será creado y lanzaremos una excepción para notificarlo. Se acostumbra a decir que los value objects deben ser creados en un "single atomic step".

```
final class Age {

	private const MIN_AGE = 0;
    private const MAX_AGE = 150;

	private $age;

	private function __construct(int $age) {
        $this->age = $age;
	}

	public static function fromAge(int $age) {
		$this->checkIsValidAge($age);
		return new static($age);
	}

	public function age(): int
    {
        return $this->age;
    }

	private function checkIsValidAge(int $age)
    {
        if ($age < self::MIN_AGE || $age > self::MAX_AGE) {
            throw new InvalidAgeException('Age ' . $age . ' is invalid');
        }
    }
}
```

### Encapsulación de lógica

Al crear objetos para atributos primitivos tenemos la ventaja de poderle añadir lógica a estos objetos. Siguiendo el ejemplo anterior de la edad, podemos añadirle funcionalidades reutilizables sin tener que duplicar código.

```
final class Age {

	//....
	private const LEGAL_AGE = 18;

	public function isAdult(): bool {
		return $this->age() >= self::LEGAL_AGE; 
	}
}
```


## Referencias

* https://leanpub.com/ddd-in-php
* http://wiki.c2.com/?ValueObjectsShouldBeImmutable
* https://martinfowler.com/bliki/ValueObject.html
* https://en.wikipedia.org/wiki/Value_object
