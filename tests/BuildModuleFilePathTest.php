<?php declare(strict_types=1);

namespace LanSuite\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BuildModuleFilePathTest extends TestCase
{
    #[DataProvider('dataSetInvalidModuleFileArguments')]
    public function testInvalidModuleFileArguments(string $module, string $file): void
    {
        $this->expectException(\Exception::class);

        $filesystemMock = $this->getFilesystemMock(true);
        $rootDirectory = '/code/';

        BuildModuleFilePath($filesystemMock, $rootDirectory, $module, $file);
    }

    public function testFileDoesNotExist(): void
    {
        $this->expectException(\Exception::class);

        $filesystemMock = $this->getFilesystemMock(false);
        $rootDirectory = '/code/';
        $module = 'news';
        $file = 'add';

        BuildModuleFilePath($filesystemMock, $rootDirectory, $module, $file);
    }

    #[DataProvider('dataSetValidModuleFileArguments')]
    public function testValidModuleFileArguments(string $module, string $file, string $expectedResult): void
    {
        $filesystemMock = $this->getFilesystemMock(true);
        $rootDirectory = '/code/';

        $actualResult = BuildModuleFilePath($filesystemMock, $rootDirectory, $module, $file);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public static function dataSetValidModuleFileArguments(): array
    {
        // [$module, $file, $expectedResult]
        return [
            ['downloads', 'stats_grafik', '/code/modules/downloads/stats_grafik.php'],
            ['about', 'overview', '/code/modules/about/overview.php'],
            ['info2', 'change', '/code/modules/info2/change.php'],
            ['downloads', 'stats-grafik', '/code/modules/downloads/stats-grafik.php'],
        ];
    }

    public static function dataSetInvalidModuleFileArguments(): array
    {
        // [$module, $file]
        return [
            ['', ''],
            ['a', ''],
            ['', 'a'],
            ['.', ''],
            ['', '.'],
            ['..', ''],
            ['', '..'],
            ['..', '..'],
            ['foo/', 'bar'],
            ['bar', 'foo/'],
            ['../../passwd', 'abc'],
            ['abc', '../../passwd'],
            ['~/.ssh/config', 'baz'],
            ['baz', '~/.ssh/config'],
        ];
    }

    private function getFilesystemMock(bool $existsReturnValue)
    {
        $filesystem = $this->createConfiguredMock(
            \Symfony\Component\Filesystem\Filesystem::class,
            [
                'exists' => $existsReturnValue,
            ],
        );

        return $filesystem;
    }
}
