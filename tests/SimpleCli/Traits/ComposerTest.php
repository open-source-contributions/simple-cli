<?php

namespace Tests\SimpleCli\Traits;

use SimpleCli\Composer\InstalledPackage;
use Tests\SimpleCli\DemoApp\DemoCli;

/**
 * @coversDefaultClass \SimpleCli\Traits\Composer
 */
class ComposerTest extends TraitsTestCase
{
    /**
     * @covers ::getPackageName
     */
    public function testGetPackageName()
    {
        $command = new DemoCli();

        self::assertSame('', $command->getPackageName());
    }

    /**
     * @covers ::getVendorDirectory
     */
    public function testGetVendorDirectory()
    {
        $command = new DemoCli();

        self::assertSame(realpath(__DIR__.'/../../../..'), realpath($command->getVendorDirectory()));
    }

    /**
     * @covers ::setVendorDirectory
     */
    public function testSetVendorDirectory()
    {
        /**
         * @var string
         */
        $path = realpath(__DIR__);
        $command = new DemoCli();
        $command->setVendorDirectory($path);

        self::assertSame($path, $command->getVendorDirectory());
    }

    /**
     * @covers ::getInstalledPackages
     */
    public function testGetInstalledPackages()
    {
        $packages = [
            [
                'name'    => 'foo/bar',
                'version' => '1.2.3',
            ],
        ];

        $command = new DemoCli();
        $vendorDirectory = sys_get_temp_dir();
        $command->setVendorDirectory($vendorDirectory);

        @mkdir($vendorDirectory.'/composer');
        file_put_contents($vendorDirectory.'/composer/installed.json', json_encode($packages));

        self::assertSame($packages, $command->getInstalledPackages());

        unlink($vendorDirectory.'/composer/installed.json');
        @rmdir($vendorDirectory.'/composer');
    }

    /**
     * @covers ::getInstalledPackage
     * @covers \SimpleCli\Composer\InstalledPackage::__construct
     */
    public function testGetInstalledPackage()
    {
        $packages = [
            [
                'name'    => 'foo/bar',
                'version' => '1.2.3',
            ],
        ];

        $command = new DemoCli();
        $vendorDirectory = sys_get_temp_dir();
        $command->setVendorDirectory($vendorDirectory);

        @mkdir($vendorDirectory.'/composer');
        file_put_contents($vendorDirectory.'/composer/installed.json', json_encode($packages));

        /**
         * @var InstalledPackage
         */
        $installedPackage = $command->getInstalledPackage('foo/bar');
        self::assertInstanceOf(InstalledPackage::class, $installedPackage);
        self::assertSame('foo/bar', $installedPackage->name);
        self::assertSame('1.2.3', $installedPackage->version);
        self::assertNull($command->getInstalledPackage('foo/biz'));

        unlink($vendorDirectory.'/composer/installed.json');
        @rmdir($vendorDirectory.'/composer');
    }

    /**
     * @covers ::getInstalledPackageVersion
     */
    public function testGetInstalledPackageVersion()
    {
        $packages = [
            [
                'name'    => 'foo/bar',
                'version' => '1.2.3',
            ],
        ];

        $command = new DemoCli();
        $vendorDirectory = sys_get_temp_dir();
        $command->setVendorDirectory($vendorDirectory);

        @mkdir($vendorDirectory.'/composer');
        file_put_contents($vendorDirectory.'/composer/installed.json', json_encode($packages));

        self::assertSame('1.2.3', $command->getInstalledPackageVersion('foo/bar'));
        self::assertSame('unknown', $command->getInstalledPackageVersion('foo/biz'));

        unlink($vendorDirectory.'/composer/installed.json');
        @rmdir($vendorDirectory.'/composer');
    }
}
