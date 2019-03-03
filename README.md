# PHP library to check link safety

## Usage:

```php
$checker = new Kirimemail\LinkCheck\Checker();

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
```