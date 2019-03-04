<?php
/**
 *
 */

namespace Kirimemail\LinkCheck\Tests;

define('BASE_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);

use Dotenv\Dotenv;
use Kirimemail\LinkCheck\GoogleSafebrowsing;
use PHPUnit\Framework\TestCase;

class GoogleSafebrowsingTest extends TestCase
{
    private $checker;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        try {
            $dotenv = new Dotenv(realpath(BASE_DIR));
            $dotenv->load();
        } catch (\Throwable $e) {

        }
        $this->checker = new GoogleSafebrowsing(getenv('GOOGLE_API_KEY'));
    }

    public function testSafebrowsing()
    {
        if (in_array(getenv('GOOGLE_API_KEY'), ['', null, false])) {
            $this->assertEquals(false, $this->checker->check('http://testsafebrowsing.appspot.com/apiv4/ANY_PLATFORM/MALWARE/URL/'));
            $this->assertEquals(false, $this->checker->check('http://testsafebrowsing.appspot.com/apiv4/ANY_PLATFORM/SOCIAL_ENGINEERING/URL/'));
            $this->assertEquals(false, $this->checker->check('http://testsafebrowsing.appspot.com/apiv4/ANY_PLATFORM/UNWANTED_SOFTWARE/URL/'));
            $this->assertEquals(false, $this->checker->check('http://kirim.email'));
            $this->assertEquals(false, $this->checker->check('https://kirimemail.com'));
        } else {
            $this->assertEquals(true, $this->checker->check('http://testsafebrowsing.appspot.com/apiv4/ANY_PLATFORM/MALWARE/URL/'));
            $this->assertEquals(true, $this->checker->check('http://testsafebrowsing.appspot.com/apiv4/ANY_PLATFORM/SOCIAL_ENGINEERING/URL/'));
            $this->assertEquals(true, $this->checker->check('http://testsafebrowsing.appspot.com/apiv4/ANY_PLATFORM/UNWANTED_SOFTWARE/URL/'));
            $this->assertEquals(false, $this->checker->check('http://kirim.email'));
            $this->assertEquals(false, $this->checker->check('https://kirimemail.com'));
        }
    }
}