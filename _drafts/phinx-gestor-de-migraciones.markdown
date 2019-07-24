---
layout: post
title: "Facturini (5): Phinx el Gestor De Migraciones"
description: Phinx como gestor de migraciones
---

Cuando nuestra aplicación tiene una **base de datos** una buena práctica es tener un gestor de migraciones que se encargue de documentar y aplicar todos aquellos cambios que hemos ido haciendo sobre la bd.

## Migraciones

Una migración consiste en un **único fichero que contiene un cambio a aplicar en la base de datos de nuestra aplicación**. Los cambios que podemos hacer en la migraciones van desde: la creación de una tabla o base de datos, hasta la modificación del tipo de una columna. Cualquier comando que podamos ejecutar sobre una base de datos, es candidato a ser escrito en una migración. 

¿Y que me aporta tener multiples ficheros con los cambios de la base de datos? Pues muy sencillo: documentación de los cambios y uniformidad. Yo, en mi experiencia, he visto formas de aplicar cambios en proyectos colaborativos que sencillamente consistían en ejecutar el comando SQL en la bd de producción y que, para tener en tu base de datos dichos cambios, debías hacer una copia de la bd de producción más reciente. Esta forma de trabajar es una mala práctica, ya que induces a que los desarrolladores, puedan tener problemas con los cambios que apliques en la base de datos si no van actualizando periodicamente su bd de desarrollo. 

Para solucionar este problema tenemos este tipo de **librerías** que nos permiten tener todos los cambios documentados y aplicarlos a cualquier bd simplemente ejecutando un comando. 

En **Facturini**, no teníamos ningún **gestor de migraciones**, por lo que cada cambio que se aplicaba en producción, tenía que ser reproducido en local. Así pues, me he decidido a usar [*Phinx*](https://phinx.org/) como gestor para ganar en automatización de procesos con la bd.

Cada migración se divide en dos partes: Ejecución del **comando** y **rollback**. La primera parte sencillamente ejecuta la comanda que nosotros indicamos contra la bd. La segunda nos permitirá deshacer dicha migración en el caso de que detectemos un problema o tengamos algún susto y queramos volver al estado inicial. Cada migración se ejecuta en un **orden lógico**. En el caso de Phinx, se usa la fecha de creación del fichero.

Para **Facturini** he añadido dos migraciones: una encargada de asegurarse que la base de datos existe y otra asegurando que la única tabla que tenemos por el momento existe.

*db/migrations/20190718193112_create_invoice_table_migration.php*
```php
<?php

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class CreateInvoiceTableMigration extends AbstractMigration
{
    private const TABLE_NAME = 'factura';

    public function up()
    {
        $this->table(self::TABLE_NAME, ['id' => 'num_reg'])
            ->addColumn('nom', 'string', ['length' => 255, 'default' => null])
            ->addColumn('adreca', 'string', ['length' => 255, 'default' => null])
            ->addColumn('nif', 'string', ['length' => 255, 'default' => null])
            ->addColumn('detalls', 'text')
            ->addColumn('factura', 'text')
            ->addColumn('observacions', 'text')
            ->addColumn('tipus', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => null, 'length' => 1])
            ->addColumn('fecha_solicitud', 'date', ['default' => null])
            ->addColumn('fecha', 'date', ['default' => null])
            ->addColumn('cobrada', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => null, 'length' => 1])
            ->addColumn('modificat', 'integer', ['limit' => MysqlAdapter::INT_TINY, 'default' => null, 'length' => 1])
            ->create();
    }

    public function down()
    {
        $this->table(self::TABLE_NAME)->drop()->save();
    }
}

```

Tal y como hemos comentado anteriormente Phinx a cada migración le añade un `timestamp` con la fecha de creación del fichero para asegurar que las migraciones se ejecutarán en el orden de creación. Podemos ver que en esta migración tenemos los dos métodos encargados de ejecutar la migración y el rollback: `up()` y `down()`, donde el primero se encarga de la creación de la tabla `factura` y el segundo se encarga de hacer un `drop` de dicha tabla. En mi caso he usado la table API de Phinx pero tambien se pueden ejecutar `SQLs` puras en las migraciones.

Una vez tenemos la migración hecha, simplemente ejecutando el comando `vendor/bin/phinx migrate` y **Phinx** nos ejecutará las dos migraciones seguidas contra nuestra bd.


## Configuración

La **configuración de Phinx** en un proyecto es mediante un fichero. Este puede ser en formato array de Php o Yml. En mi caso he optado por Yml, que aunque tenga mala fama, para configuraciones sencillas, me parece una buena solución.

```yml
paths:
    migrations: '%%PHINX_CONFIG_DIR%%/db/migrations'
    seeds: '%%PHINX_CONFIG_DIR%%/db/seeds'

environments:
    default_migration_table: phinxlog
    default_database: production
    production:
        adapter: mysql
        host: '%%PHINX_MYSQL_HOST%%'
        name: '%%PHINX_MYSQL_DATABASE%%'
        user: '%%PHINX_MYSQL_USER%%'
        pass: '%%PHINX_MYSQL_PASSWORD%%'
        port: 3306
        charset: utf8mb4

version_order: creation
```

La configuración, como podemos ver, es bastante sencilla. En la key `paths` indicamos donde tendremos los ficheros de las migraciones y semillas. Bajo la key `environments` estoy diciendo que solamente tendremos un entorno llamado `production`. No creo necesario crear diversos entornos ya que la base de datos, por el momento, va a ser igual en todos los entornos. La key `default_migration_table` lo que nos permite es dar nombre a la tabla que usa `Phinx` para gestionar las migraciones ya ejecutadas o no y algunos metadatos necesarios. 

## Semillas

Las semillas son ficheros que nos permiten rellenar, con contenido aleatorio, la base de datos una vez creada. Éstas son útiles sobre todo para entornos de desarrollo o testing. Dichos ficheros tienen un método `run()` donde nosotros vamos a indicar que datos queremos rellenar la tabla. En este caso yo he creado un `InvoiceSeeder` para tener datos de prueba en mi entorno de desarrollo.

```php
<?php
   public function run()
    {
        $numInvoicesToSeed = 10;
        $seedInvoices = [];
        $fakerFactory = Faker\Factory::create('es_ES');
        for ($counter = 0; $counter < $numInvoicesToSeed; $counter++) {
            $seedInvoices[] = [
                'nom' => $fakerFactory->words(3, true),
                'adreca' => $fakerFactory->address,
                'nif' => $fakerFactory->randomNumber(8),
                'detalls' => $fakerFactory->text(20),
                'factura' => $fakerFactory->randomFloat(2, 0, 150),
                'observacions' => $fakerFactory->text(15),
                'tipus' => $fakerFactory->numberBetween(0, 1),
                'fecha_solicitud' => $fakerFactory->date(),
                'fecha' => $fakerFactory->date(),
                'cobrada' => $fakerFactory->numberBetween(0, 1),
                'modificat' => $fakerFactory->numberBetween(0, 1)
            ];
        }

        $this->table(self::TABLE_NAME)->insert($seedInvoices)->save();
    }
```

