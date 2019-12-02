<?php
/**
 *
 */

namespace Kirimemail\LinkCheck;

use PHPHtmlParser\Dom;

class Checker implements CheckerInterface
{
    const OK = 0;
    const DIFFERENT_REDIRECT = 101;
    const TOO_MUCH_REDIRECTS = 102;
    const GOOGLE_UNSAFE = 103;
    const PHISHTANK_VALID = 104;
    const UNDEFINED_ERROR = 105;

    const GOOGLE_BOT_USER_AGENT = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
    const GOOGLE_CHROME_USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36';

    protected $options;

    public function __construct($options = [])
    {
        if (null === $options) {
            $options = [];
        }
        $default = [
            'max_redirects' => 3,
            'whitelist' => [
                '*://google.*',
            ],
            'check_google_safebrowsing' => true,
            'google_api_key' => '',
            'check_phishtank' => true,
            'phishtank_api_key' => '',
            'timeout' => 10,
        ];
        $this->options = array_merge($default, $options);
    }

    /**
     * @param string $url
     *
     * @return int
     * @throws \Exception Invalid URL
     */
    public function check($url)
    {
        $url = trim($url);
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new \Exception("Invalid URL");
        }
        if ($this->inWhitelist($url)) {
            return self::OK;
        }
        for ($i = 0; $i <= $this->options['max_redirects']; $i ++) {
            try {
                if ($i === $this->options['max_redirects']) {
                    return self::TOO_MUCH_REDIRECTS;
                }
                if ($this->options['check_google_safebrowsing']) {
                    if ($this->checkSafebrowsing($url)) {
                        return self::GOOGLE_UNSAFE;
                    }
                }
                if ($this->options['check_phishtank']) {
                    if ($this->checkPhishtank($url)) {
                        return self::PHISHTANK_VALID;
                    }
                }
                $curl = curl_init();
                $botRedirect = $this->getRedirectUrl($curl, $url, self::GOOGLE_CHROME_USER_AGENT);
                $chromeRedirect = $this->getRedirectUrl($curl, $url, self::GOOGLE_CHROME_USER_AGENT);
                if ($botRedirect || $chromeRedirect) {
                    if ($botRedirect !== $chromeRedirect) {
                        return self::DIFFERENT_REDIRECT;
                    }
                } else {
                    return self::OK;
                }
                $url = $chromeRedirect;
            } catch (\Throwable $e) {
                return self::UNDEFINED_ERROR;
            }
        }
    }

    /**
     * @param $curl
     * @param $url
     * @param $userAgent
     * @return string|string[]|null
     */
    protected function getRedirectUrl($curl, $url, $userAgent)
    {
        $curlInfo = $this->getCurlInfo($curl, $url, $userAgent);
        $redirectUrl = $this->removeQueryString(@$curlInfo['redirect_url']);
        if (trim($url, '/') === trim($redirectUrl, '/')) {
            $redirectUrl = '';
        }
        // look for meta http-equiv="refresh"
        if (!$redirectUrl) {
            $dom = new Dom();
            $dom->load($url);
            $metaTags = $dom->find('meta');
            foreach ($metaTags as $meta) {
                if ($meta->getAttribute('http-equiv') === 'refresh') {
                    $redirectUrl = preg_replace(
                        '/\s*\d+\s*;\s*url\s*=\s*(\'|\")(.+)(\'|\")/i',
                        '$2',
                        $meta->getAttribute('content')
                    );
                    break;
                }
            }
        }

        return $redirectUrl;
    }

    /**
     * @param string $url
     *
     * @return boolean
     */
    protected function inWhitelist($url)
    {
        foreach ($this->options['whitelist'] as $whitelistItem) {
            if (fnmatch($whitelistItem, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $curl
     * @param $url
     * @param $userAgent
     * @return mixed
     */
    protected function getCurlInfo($curl, $url, $userAgent)
    {
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FILETIME, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->options['timeout']);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->options['timeout']);
        curl_exec($curl);
        $curlInfo = curl_getinfo($curl);

        return $curlInfo;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function removeQueryString($url)
    {
        return preg_replace('/\?.*/', '', $url);
    }

    /**
     * @param $url
     * @return bool|string
     * @throws \Exception
     */
    protected function checkSafebrowsing($url)
    {
        $safebrowsing = new GoogleSafebrowsing($this->options['google_api_key']);

        return $safebrowsing->check($url);
    }

    /**
     * @param $url
     * @return bool
     */
    protected function checkPhishtank($url)
    {
        $phishtank = new Phishtank($this->options['phishtank_api_key']);

        return $phishtank->check($url);
    }
}
