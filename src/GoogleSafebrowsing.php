<?php
/**
 *
 */

namespace Kirimemail\LinkCheck;


use Ampersa\SafeBrowsing\SafeBrowsing;

class GoogleSafebrowsing implements CheckerInterface
{
    private $api_key;

    public function __construct($api_key = '')
    {
        $this->api_key = $api_key;
    }

    /**
     * @param $url
     * @return bool|string
     * @throws \Exception
     */
    public function check($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        try {
            $safebrowsing = new SafeBrowsing($this->api_key);
            $result = $safebrowsing->listed($url);
        } catch (\Throwable $e) {
            return false;
        }

        return $result;
    }
}