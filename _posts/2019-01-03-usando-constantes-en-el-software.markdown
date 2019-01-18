---
layout: post
title: "Usando Constantes en El Software"
date: 2019-01-03T06:34:22-06:00
author: danitome24
summary: Usando Constantes en El Software
---

Una buena práctia en el desarrollo software es el uso de constantes y en este post os voy a intentar dar mi punto de vista sobre como usarlas de un modo correcto y como nos pueden ayudar a escribir un código más limpio y reutilizable.

## ¿Que es una constante?

Una constante, tal y como su nombre indica, es un valor que no varía durante el tiempo, es decir, solamente puede ser leído. Se diferencian de las variables ya que estas sí que varían durante el tiempo. Las constantes una vez definidas no pueden ser modificadas y permanecerán con el valor inicial durante toda la ejecución.

## Ejemplo de constante

Para seguir con la explicación voy a hacerlo mediante un ejemplo de código. Supongamos que tenemos una clase `Car` y esta contiene un atributo `speed` que será un entero con la velocidad del coche. 

```php
<?php

final class Car {
	
    private $speed;

    public function __construct()
    {
        $this->speed = 0;
    }

    public function speed(): int
    {
        return $this->speed;
    }
}
``` 

Dicho coche tendrá la capacidad de acelerar y frenar. Así pues tendremos dos métodos publicos tanto para acelerar como para frenar.

```php
<?php

final class Car {
	
    private $speed;

    public function __construct()
    {
        $this->speed = 0;
    }

    //...

    public function accelerate(): void
    {
        $this->speed++;
    }

    public function decelerate(): void
    {
        $this->speed--;
    }
}
``` 

Una vez aquí, desde negocio, nos dicen que nuestro coche tendrá dos restricciones en cuanto al valor de la velocidad. La primera restricción es que nuestro coche solo tendrá velocidad positiva (nunca podrá ir a una velocidad menor que 0 km/h) y la segunda restricción es que nuestro coche no puede sobrepasar la velocidad de 100 km/h.

Para dar solución a estas nuevas restricciones podríamos simplemente modificar el código tal que así:

```php
<?php

final class Car {
	
    private $speed;

    public function __construct()
    {
        $this->speed = 0;
    }

    //...

    public function accelerate(): void
    {
    	if ($this->speed < 100) {
            $this->speed++;
    	}
    }

    public function decelerate(): void
    {
        if ($this->speed > 0) {
            $this->speed--;
        }
    }
}
``` 

El código nuevo cumpliría satisfactóriamente con los requisitos, pero ¿qué problemas vemos en este trozo?

* Poca legibilidad: Una vez pasado un tiempo después de la modificación de este codigo, o simplemente cuando otro desarrollador lea este trozo, es complicado entender que significado y por qué se está haciendo este condicional. 

* Elevado coste de cambio: Si seguimos desarrollando código y usando el valor literal de 0 y 100 por el código, si algun día queremos cambiar alguno de estos valores, tendremos que revisar todo el código y modificar uno a uno cada entero. Un IDE nos puede ayudar a este cambio, pero igualmente el coste será elevado. 

* Peligro de errores: Al usar literales por el código, esto puede llevar a errores o confusiones ya que cada desarrollador lo puede escribir de una forma diferente. Un ejemplo sería con los literales de tipo float, el mismo número PI puede ser escrito diferente por cada desarrollador. El primero podría escribirlo como `3,14159` y otro `3,1416` teniendo dos valores diferentes y que podrían dar resultados diferentes en operaciones iguales.

Para solventar estos problemillas podemos hacer uso de las constantes. Así quedaría nuestro código:

```php
<?php

final class Car {

    const MAX_SPEED_ALLOWED = 100;
    const MIN_SPEED = 0;
	
    private $speed;

    public function __construct()
    {
        $this->speed = 0;
    }

    //...

    public function accelerate(): void
    {
    	if ($this->speed < self::MAX_SPEED_ALLOWED) {
            $this->speed++;
    	}
    }

    public function decelerate(): void
    {
        if ($this->speed > self::MIN_SPEED) {
            $this->speed--;
        }
    }
}
```

## ¿Como lo hemos arreglado?

* ~~Poca legibilidad~~: Al introducir nombres de constantes textuales podemos ayudarnos de su significado para entender a que se refiere cada valor. También al leer el código podemos entender más fácilmente que significa cada valor.

* ~~Elevado coste de cambio~~: Tenemos centralizado en la definición de la variable el valor de esta. Si queremos modificar su valor solamente tendremos que cambiar en un sitio y se verá afectado allá donde se use.  

* ~~Peligro de errores~~: Suprimimos los errores ya que podemos reutilizar el valor de las constantes tanto dentro de la propia clase (con el `self::MAX_SPEED_ALLOWED`) como fuera de ella (tal que `Car::MAX_SPEED_ALLOWED`).

## No solo para enteros

También deberíamos usar estas constantes en los strings que tengamos por el código. Un ejemplo típico es:

```php
<?php

final class PostStatus
{
    const PUBLISHED = 'published';
    const UNPUBLISHED = 'unpublished';

    private $status;
    
    private function __construct(string $status)
    {
       $this->status = $status; 
    }

    public static function fromPublished(): self
    {
        return new static(self::PUBLISHED);
    }

    public static function fromUnpublished(): self
    {
        return new static(self::UNPUBLISHED);
    }
}
```

## Bonus

A partir de la versión de PHP 7.1 podemos añadir visibilidad (public, protected y private) a las constantes de clase. Por lo tanto podemos tener constantes visibles des de fuera de la propia clase (public), visibilidad solo para los hijos (protected) o visibilidad solo para la propia clase (private).

```php
<?php

final class Car
{
    public const MAX_SPEED_ALLOWED = 100;
    protected const MIN_SPEED = 0;
    private const NAME = 'Ferrari';
	
    public function __construct($spe)
    {
        $this->speed = 0;
    }
}

Car::MAX_SPEED_ALLOWED // Devuelve 100
Car::MIN_SPEED // Lanza error
Car::NAME // Lanza error
```
Más info en la [web de php](http://php.net/manual/es/language.oop5.visibility.php#language.oop5.visiblity-constants)
