# Dhii - Versions
Implementation for dealing with SemVer-compliant versions.

## Details
The idea is to provide a minimal implementation that would deal with standards-compliant version
numbers, while being standards compliant itself.

## Usage
```php
<?php

use Dhii\Versions\StringVersionFactory;

$factory = new StringVersionFactory();
$version = $factory->createVersionFromString('0.1.15-alpha1.2.3+hello.world.987');

echo $version->getMajor(); // 0
echo $version->getMinor(); // 1
echo $version->getPatch(); // 15
var_export($version->getPreRelease()); // ['alpha1', 2, 3]
var_export($version->getBuild()); // ['hello', 'world', '987']
```
