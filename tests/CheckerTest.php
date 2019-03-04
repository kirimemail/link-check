<?php
/**
 *
 */

namespace Kirimemail\LinkCheck\Tests;

define('BASE_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);

use Dotenv\Dotenv;
use Kirimemail\LinkCheck\Checker;
use PHPUnit\Framework\TestCase;

class CheckerTest extends TestCase
{
    private $checker;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $dotenv = new Dotenv(realpath(BASE_DIR));
        $dotenv->load();
        $this->checker = new Checker([
            'google_api_key' => getenv('GOOGLE_API_KEY')
        ]);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(Checker::class, $this->checker);
    }

    public function testOk()
    {
        $this->assertEquals(Checker::OK, $this->checker->check('http://kirim.email'));
        $this->assertEquals(Checker::OK, $this->checker->check('http://emailkerja.id'));
    }

    public function testTooMuchRedirect()
    {
        $checker = new Checker([
            'google_api_key' => getenv('GOOGLE_API_KEY'),
            'max_redirects' => 1
        ]);
        $this->assertEquals(Checker::TOO_MUCH_REDIRECTS, $checker->check('https://kirimemail.com'));
    }

    public function testDifferentRedirect()
    {
        $this->assertEquals(Checker::GOOGLE_BOT_DIFFERENT_REDIRECT, $this->checker->check('https://bit.ly/2Hc2uZk'));
    }

    public function testOkWhitelist()
    {
        $this->assertEquals(Checker::OK, $this->checker->check('http://google.com'));
    }


    public function testInvalidUrl()
    {
        $this->expectExceptionMessage('Invalid URL');
        $this->checker->check('invalid url');
    }

    public function testSafebrowsing()
    {
        if (getenv('GOOGLE_API_KEY') === '') {
            $this->expectExceptionMessage('A Google Safebrowsing has not been specified');
        }
        $this->assertEquals(true, $this->checker->checkSafebrowsing('http://testsafebrowsing.appspot.com/apiv4/ANY_PLATFORM/MALWARE/URL/'));
        $this->assertEquals(true, $this->checker->checkSafebrowsing('http://testsafebrowsing.appspot.com/apiv4/ANY_PLATFORM/SOCIAL_ENGINEERING/URL/'));
        $this->assertEquals(true, $this->checker->checkSafebrowsing('http://testsafebrowsing.appspot.com/apiv4/ANY_PLATFORM/UNWANTED_SOFTWARE/URL/'));
        $this->assertEquals(false, $this->checker->checkSafebrowsing('http://kirim.email'));
        $this->assertEquals(false, $this->checker->checkSafebrowsing('https://kirimemail.com'));
    }
}