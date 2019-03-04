<?php
/**
 *
 */

namespace Kirimemail\LinkCheck\Tests;

define('BASE_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);

use Kirimemail\LinkCheck\Phishtank;
use PHPUnit\Framework\TestCase;

class PhishtankTest extends TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->checker = new Phishtank();
    }

    public function testPhishtankNotInDatabase()
    {
        $this->assertEquals(false, $this->checker->check('kirim.email'));
        $this->assertEquals(false, $this->checker->check('krm.email'));
    }
}