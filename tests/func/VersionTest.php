<?php

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Dhii\Versions\Version as Subject;

class VersionTest extends TestCase
{
    /**
     * @param int   $major
     * @param int   $minor
     * @param int   $patch
     * @param array $preRelease
     * @param array $build
     *
     * @return Subject|MockObject
     */
    protected function createSubject(
        int $major,
        int $minor,
        int $patch,
        array $preRelease,
        array $build
    ): Subject {
        $mock = $this->getMockBuilder(Subject::class)
            ->enableOriginalConstructor()
            ->setConstructorArgs([$major, $minor, $patch, $preRelease, $build])
            ->enableProxyingToOriginalMethods()
            ->setMethods([])
            ->getMock();

        return $mock;
    }

    protected function createPreRelease(): array
    {
        $version = [];
        $version[] = (['alpha', 'beta', 'RC'])[random_int(0, 2)];
        $idsLength = random_int(2, 4);

        for ($i=0; $i<$idsLength; $i++) {
            $version[] = $this->createIdentifier();
        }

        return $version;
    }

    protected function createBuild(): array
    {
        $version = [];
        $idsLength = random_int(2, 4);

        for ($i=0; $i<$idsLength; $i++) {
            $version[] = $this->createIdentifier();
        }

        return $version;
    }

    protected function createIdentifier(): string
    {
        $isNumeric = (bool) random_int(0, 1);
        $identifier = $isNumeric
            ? random_int(1, 99)
            : $this->createString(random_int(1, 5));

        return $identifier;
    }

    protected function createString(int $length): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLength = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[random_int(0, $charsLength - 1)];
        }
        return $string;
    }

    public function testToString()
    {
        {
            $major = random_int(1, 9);
            $minor = random_int(1, 99);
            $patch = random_int(1, 999);
            $preRelease = $this->createPreRelease();
            $build = $this->createBuild();
            $subject = $this->createSubject($major, $minor, $patch, $preRelease, $build);
        }

        {
            $expected =
                "$major." .
                "$minor." .
                $patch .
                '-' . implode('.', $preRelease) .
                '+' . implode('.', $build);
            $this->assertEquals($expected, (string) $subject);
        }
    }
}
