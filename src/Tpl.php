<?php namespace Zarzo;

/**
 * A simple template engine using pure php code.
 *
 * @author     Zarzo Martínez <zarzocode@gmail.com>
 * @copyright  2013 Zarzo Code
 * @license    http://opensource.org/licenses/MIT MIT
 * @version    0.1.0
 * @link       https://github.com/zarzo/Tpl
 */
class Tpl
{
    /**
     * Configuración.
     * 
     *  - tpl_dir: Directorio de templates
     *  - cache_dir: Directorio cache. False para desactivar cache (default).
     *  - cache_time: Tiempo global de cache. 0 = para siempre 
     *                (Utiliza {@link clearCache()} para invalidar)
     *  - extension: Extensión de los templates, default '.php' (punto incluido)
     *  - minify: Elimina \n, \r and \t en el output de {@link render()}
     *  
     * @var array
     */
    public $cfg = array();


    /**
     * Establecer configuración por defecto
     *
     * @param array $options
     * @see {@link $cfg}
     */
    public function __construct($options = array())
    {
        $this->setConfig($options);
    }

    /**
     * Establecer configuración
     *
     * @param array $options
     * @see {@link $cfg}
     */
    public function setConfig($options = array())
    {
        $defaults = array(
            'tpl_dir'    => '',
            'cache_dir'  => '',
            'cache_time' => 0,
            'extension'  => '.php',
            'minify'     => 'true'
        );

        $this->cfg = array_merge($defaults, $options);
    }

    /**
     * Render template
     *
     * Busca el archivo $file en el directorio de templates y lo carga pasando
     * la variable $data. $cache_id y $cache_time son opcionales.
     *
     * $cache_id permite establecer
     *
     * @param  string $file       Archivo en el directorio tempalte, sin extensión
     * @param  array  $data       Array para enviar al template.
     * @param  string $cache_id   ID del template.
     * @param  float  $cache_time Cache time for current template
     * @return string         Rendered template
     */
    public function render($file, $data = array(), $cache_id = '', $cache_time = null)
    {
        $this->cfg['tpl_dir'] = rtrim($this->cfg['tpl_dir'], ' \\/').'/';
        $filepath = $this->cfg['tpl_dir'] . $file . $this->cfg['extension'];

        if (file_exists($filepath)) {

            if (!$__result = $this->getCache($file, $cache_id, $cache_time)) {
                /*
                 * Template no cacheado, extraemos
                 */
                $__result = $this->extract($filepath, $data);

                if ($this->cfg['cache_dir']) {
                    $this->setCache($file, $__result, $cache_id);
                }
            }

            return $__result;

        } else {
            throw new \Exception($filepath . " doesn't exists", 1);
        }
    }

    /**
     * Extraer datos ($data) y cargarlso en el template
     *
     * @param  string $filepath Path to the template
     * @param  array  $data     Data to compile
     * @return string             Template compiled
     */
    public function extract($filepath, $data)
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        ob_start();
        extract($data, EXTR_SKIP);
        include $filepath;
        $result = ob_get_contents();
        ob_end_clean();

        if ($this->cfg['minify']) {
            $result = str_replace(array("\r", "\n", "\t"), "", $result);
        }

        return $result;
    }

    /**
     * Obtener template cacheado
     *
     * @param  string $file     Nombre del template, sin extensión
     * @param  string $cache_id Template Id
     * @param  int    $time     Establecer tiempo de cache, "infinito" por defecto
     * @return mixed            Output (string) o false si no hay cache o es invalida
     */
    public function getCache($file, $cache_id = '', $time = null)
    {
        if (!$this->cfg['cache_dir']) {
            return false;
        }

        $cache_file = $this->cfg['cache_dir'].'/'.md5($file.$cache_id);

        $cache_time = $this->cfg['cache_time'];
        if ($time !== null) {
            $cache_time = intval($time);
        }

        if (file_exists($cache_file)) {
            if ($cache_time == 0 || $cache_time > time() - filemtime($cache_file)) {
                return file_get_contents($cache_file);
            }
        }

        return false;
    }

    /**
     * Crear cache. Todos los archivos cacheados usan el mismo {@link $cache_time}
     *
     * @param string $file     Template file without extension
     * @param string $result   Contents (rendered template)
     * @param string $cache_id Template Id (see param's render description)
     * @return void
     */
    public function setCache($file, $result, $cache_id = '')
    {
        if ($this->cfg['cache_dir'] && !is_writable($this->cfg['cache_dir'])) {
            throw new \Exception(
                'Cache directory ('.$this->cfg['tpl_dir'].') is not writable'
            );
        }

        $cache_file = md5($file);

        $file = implode(
            '/',
            array($this->cfg['cache_dir'], $cache_id, $cache_file)
        );
        $fopen = fopen($file, 'w');
        fwrite($fopen, $result);
        fclose($fopen);
    }

    /**
     * Invalidar cache de un template
     * 
     * @param  string $file     Nombre del template
     * @param  string $cache_id ID de la cache
     * @return object           $this, para enlazar (chaining)
     */
    public function clearCache($file, $cache_id = '')
    {
        $cache_file = $this->cfg['cache_dir'].'/'.md5($file.$cache_id);
        if (file_exists($cache_file)) {
            try {
                unlink($cache_file);
            } catch (Exception $e) {
                echo 'Error clearing cache '.$e->getMessage();
            }
        }

        return $this;
    }

    /**
     * Obtener propiedad de configuración {@link $cfg}
     *
     * @param  string $property
     * @return mixed Valor de la propiedad o false si no existe
     */
    public function __get($property)
    {
        if (isset($this->cfg[$property])) {
            return $this->cfg[$property];
        }
        return false;
    }

    /**
     * Establecer propiedad de configuración {@link $cfg}
     *
     * @param  string  $property
     * @param  mixed   $value
     * @return object  $this, para enlazar (chaining)
     */
    public function __set($property, $value)
    {
        if (isset($this->cfg[$property])) {
            $this->cfg[$property] = $value;
        }
        return $this;
    }
}
