# PHP library to check link safety

[![Build Status](https://travis-ci.org/kirimemail/link-check.svg?branch=master)](https://travis-ci.org/kirimemail/link-check)

## Usage:

```php
//create checker with default options
$checker = new Kirimemail\LinkCheck\Checker([
    'max_redirects' => 3,
    'whitelist' => [
         '*://google.*',
    ],
    'check_google_safebrowsing' => true,
    'google_api_key' => '', //necessary if you need to check Google Safebrowsing
    'check_phishtank' => true,
    'phishtank_api_key' => '' //optional, but have low rate limit
]);

$status = $checker->check($url);

if ($status === Kirimemail\LinkCheck\Checker::OK) {
    // link is OK
} else {
    // link is suspicious
}

if ($status === Kirimemail\LinkCheck\Checker::GOOGLE_BOT_DIFFERENT_REDIRECT) {
    // link has different redirect locations
}

if ($status === Kirimemail\LinkCheck\Checker::TOO_MUCH_REDIRECTS) {
    // link has more redirects than expected
}

if ($status === Kirimemail\LinkCheck\Checker::GOOGLE_UNSAFE) {
    // link is considered unsafe by google
}

if ($status === Kirimemail\LinkCheck\Checker::PHISHTANK_VALID) {
    // link is a valid phishing link in phishtank
}
```