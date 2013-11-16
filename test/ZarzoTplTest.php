<?php
include __DIR__.'/../src/Tpl.php';

use \Zarzo\Tpl;

class TplTest extends \PHPUnit_Framework_TestCase
{
    protected $tpl;
    protected $cache_dir;
    protected $tpl_dir;

    protected function setUp()
    {
        $this->cache_dir = __DIR__.'/cache';
        $this->tpl_cache = __DIR__.'/tpl';

        if (!is_writable($this->cache_dir)) {
            throw new Exception("Permisos de escritura en directorio cache", 1);
        }

        $this->tpl = new Tpl(array(
            'cache_dir' => $this->cache_dir,
            'tpl_dir'   => $this->tpl_cache
        ));
    }

    public function testRenderNoCache()
    {
        $data = array('test' => 'mundo');
        $tpl = $this->tpl->render('template', $data);
        $this->assertEquals('hola mundo', $tpl);
    }
}
