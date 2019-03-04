<?php
/**
 *
 */

namespace Kirimemail\LinkCheck\Tests;

use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    protected $BASE_DIR;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->BASE_DIR = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }
}