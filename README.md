# Zarzo Tpl

Una librería de templates utilizando PHP puro

## Install

Via composer

```
{
    require: {
        'Zarzo/Tpl': '0.1.0'
    }
}
```
```
include 'vendor/autoload.php';

$tpl = new \Zarzo\Tpl();
```

Standlone

```
include 'Tpl.php';
$tpl = new \Zarzo\Tpl();
```

Ejemplo

```php
<?php
// init with basic configuration
$tpl = new \Zarzo\Tpl(array(
    'tpl_dir' => './tpl',    // directory with templates
    'cache_dir' => './cache' // directory cache
));

$data = array('hola' => 'hola');

echo $tpl->render('template', $data);
```
Archivo de template:
```php
<p>Hello <?=$hola?></p>
```

## Cache
ZarcoTpl utiliza una librería de cache basada en archivos. El método ```render()``` acepta 4 parámetros:

* *Archivo template*
* *data como un array u objeto*
* *id de la cache* (opcional)
* *tiempo de cache* (opcional)

Cache Id puede ser útil para invalidar cache y guardar diferentes versiones del mismo template
con distinto contenido. Al establecer una ID se creará un directorio dentro del directorio de cache.
Por ejemplo, establecer una ID a todos los templates de un producto determinado, cuando editas el
producto puede invalidar la cache para su ID y de esta forma se volverán a regenerar todas las caches
de este producto. Puedes establecer un tiempo de cache para cada template, si no indicas un tiempo
se utilizará el valor por defecto en la configuracioń.

## Config
Se aceptan los siguientes parámetros:
* *tpl_dir*: Directorio de templates.
* *cache_dir*: Directorio de cache. False para desabilitar cache.
* *cache_time*: Tiempo de cache global. 0 = para siempre
* *extension*: Extensión por defecto para los archivos de template
* *minify*: Eliminar \n, \r y \t en el template generado

Puedes establecer la configuración en el constructor:
```php
<?php
$tpl = new \Zarzo\Tpl(array(
    'tpl_dir'  => './tpl',
    'cache_dir'  => './cache',
    'cache_time' => 600,
    'extension'  => '.php',
    'minify'     => true
));
```

Utilizando el método ```setConfig```:
```php
<?php
$tpl = new \Zarzo\Tpl();
$tpl->setConfig(array(
    'tpl_dir'  => './tpl',
    'cache_dir'  => './cache',
    'cache_time' => 600,
    'extension'  => '.php',
    'minify'     => true
));
```
O via funciones set/get:
```php
<?php
$tpl = new \Zarzo\Tpl();
$tpl->__set('tpl_dir', './tpl');
```

## Credits
ZarcoTpl ha sido desarrollado con amor por Zarzo. Si no quedas satisfecho te devolvemos tu dinero.
Cualquier contribución será apreciada. MIT License.
