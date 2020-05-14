<?php

namespace Dhii\Versions\Test\Functional;

use Dhii\Versions\StringVersionFactory as Subject;
use DomainException;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StringVersionFactoryTest extends TestCase
{
    /**
     * @return Subject|MockObject
     */
    protected function createSubject(): Subject
    {
        $mock = $this->getMockBuilder(Subject::class)
            ->enableProxyingToOriginalMethods()
            ->setMethods([])
            ->getMock();

        return $mock;
    }

    /**
     * A data provider, retrieving version test specifications..
     *
     * @return array[] Sets of parameters, each set containing the following:
     *                 - (string) A major version.
     *                 - (string) A minor version.
     *                 - (string) A patch version.
     *                 - (array) A list of pre-release identifiers.
     *                 - (array) A list of build identifiers.
     *                 - (string|null) An exception class name to expect; `null` if no exception is expected.
     */
    public function getVersionSpecs(): array
    {
        return [
            [
                '',
                '',
                '',
                [],
                [],
                null,
            ],
            [
                rand(1, 9),
                rand(1, 99),
                rand(1, 999),
                [],
                [],
                null,
            ],
            [
                rand(1, 9),
                rand(1, 99),
                rand(1, 999),
                ['alpha1', rand(1, 99), rand(1, 999)],
                [],
                null,
            ],
            [
                rand(1, 9),
                rand(1, 99),
                rand(1, 999),
                [],
                [uniqid('wake'), rand(1, 999), uniqid('neo')],
                null,
            ],
            [
                rand(1, 9),
                rand(1, 99),
                rand(1, 999),
                ['beta2', rand(1, 99), rand(1, 999)],
                [uniqid('hello'), uniqid('world')],
                null,
            ],

            // Failures
            [
                '',
                '',
                '',
                [],
                [uniqid('foo'), rand(1, 999), uniqid('bar')],
                DomainException::class,
            ],
            [
                '',
                '',
                '',
                [uniqid('just'), uniqid('do'), uniqid('it')],
                [],
                DomainException::class,
            ],
            [
                uniqid('major'),
                '',
                '',
                [],
                [],
                DomainException::class,
            ],
        ];
    }

    /**
     * @dataProvider getVersionSpecs
     * @param string      $major
     * @param string      $minor
     * @param string      $patch
     * @param array       $preRelease
     * @param array       $build
     * @param string|null $exceptionClass A class of an exception to expect, if any.
     *
     * @throws Exception
     *
     */
    public function testCreateVersionFromString(string $major, string $minor, string $patch, array $preRelease, array $build, ?string $exceptionClass)
    {
        {
            $versionString = '';
            $versionString .= !empty($major) ? $major : '';
            $versionString .= !empty($minor) ? ".$minor" : '';
            $versionString .= !empty($patch) ? ".$patch" : '';
            $versionString .= !empty($preRelease) ? '-' . implode('.', $preRelease) : '';
            $versionString .= !empty($build) ? '+' . implode('.', $build) : '';

            $subject = $this->createSubject();
        }

        {
            if ($exceptionClass) {
                $this->expectException($exceptionClass);
            }

            $version = $subject->createVersionFromString($versionString);

            $this->assertSame(!empty($major) ? (int) $major : 0, $version->getMajor(), 'Major version mismatch');
            $this->assertSame(!empty($minor) ? (int) $minor : 0, $version->getMinor(), 'Minor version mismatch');
            $this->assertSame(!empty($patch) ? (int) $patch : 0, $version->getPatch(), 'Patch version mismatch');
            $this->assertEquals($preRelease, $version->getPreRelease(), 'Pre-release version mismatch');
            $this->assertEquals($build, $version->getBuild(), 'Build data mismatch');
        }
    }

    public function testCreateVersionFromStringDefaults()
    {
        {
            $versionString = '';
            $subject = $this->createSubject();
        }

        {
            $version = $subject->createVersionFromString($versionString);

            $this->assertSame(0, $version->getMajor());
            $this->assertSame(0, $version->getMinor());
            $this->assertSame(0, $version->getPatch());
            $this->assertEquals([], $version->getPreRelease());
            $this->assertEquals([], $version->getBuild());
        }
    }
}
