<?php
/**
 *
 */

namespace Kirimemail\LinkCheck\Tests;

use Dotenv\Dotenv;
use Kirimemail\LinkCheck\Checker;

class CheckerTest extends BaseTest
{
    private $checker;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        try {
            $dotenv = new Dotenv(realpath($this->BASE_DIR));
            $dotenv->load();
        } catch (\Throwable $e) {

        }
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
}