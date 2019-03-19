<?php
/**
 *
 */

namespace Kirimemail\LinkCheck;


class Phishtank implements CheckerInterface
{
    const PHISHTANK_URL = 'http://checkurl.phishtank.com/checkurl/';
    private $api_key;
    private $requestData;

    public function __construct($api_key = '')
    {
        $this->api_key = $api_key;
        $this->requestData = [
            'format' => 'json',
        ];
        if ($api_key !== '') {
            $this->requestData['app_key'] = $api_key;
        }
    }

    public function check($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        $result = json_decode($this->getCurlExec($url), true);
        if (null !== $result) {
            if (array_key_exists('results', $result)) {
                if (!$result['results']['in_database']) {
                    $real = false;
                } else {
                    if ($result['results']['valid']) {
                        $real = true;
                    } else {
                        $real = false;
                    }
                }
            } else {
                $real = false;
            }
        } else {
            $real = false;
        }

        return $real;
    }

    private function getCurlExec($url)
    {
        $this->requestData['url'] = urlencode($url);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, self::PHISHTANK_URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->requestData);
        $result = curl_exec($curl);

        return $result;
    }
}