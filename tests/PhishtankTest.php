<?php
/**
 *
 */

namespace Kirimemail\LinkCheck\Tests;

use Kirimemail\LinkCheck\Phishtank;

class PhishtankTest extends BaseTest
{
    private $checker;

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