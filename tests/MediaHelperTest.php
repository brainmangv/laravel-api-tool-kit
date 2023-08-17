<?php

namespace Essa\APIToolKit\Tests;

use Essa\APIToolKit\MediaHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaHelperTest extends TestCase
{
    const BASE_64_IMAGE = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/w8AAwAB/AL+f4R4AAAAASUVORK5CYII=';

    private string $testingImage = __DIR__ . '/Images/laravel-api-tool-kit.png';

    /** @test */
    public function itUploadsFile()
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $uploadedPath = MediaHelper::uploadFile($file, $path);

        Storage::assertExists($uploadedPath);
    }

    /** @test */
    public function itUploadsFileWithOriginalName()
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $uploadedPath = MediaHelper::uploadFile($file, $path, null, true);

        Storage::assertExists($uploadedPath);
        Storage::assertExists($path . '/test1.jpg');
    }

    /** @test */
    public function itUploadsFileWithCustomName()
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $customFileName = 'custom_name.jpg';

        $uploadedPath = MediaHelper::uploadFile($file, $path, $customFileName);

        Storage::assertExists($uploadedPath);
        Storage::assertExists($path . '/' . $customFileName);
    }

    /** @test */
    public function itUploadsMultipleFiles()
    {
        Storage::fake();

        $files = [
            $this->getUploadedFile('test1.jpg'),
            $this->getUploadedFile('test2.jpg'),
        ];

        $path = 'uploads/images';

        $uploadedPaths = MediaHelper::uploadMultiple($files, $path);

        foreach ($uploadedPaths as $uploadedPath) {
            Storage::assertExists($uploadedPath);
        }
    }

    /** @test */
    public function itUploadsMultipleFilesWithCustomNames()
    {
        Storage::fake();

        $files = [
            $this->getUploadedFile('test1.jpg'),
            $this->getUploadedFile('test2.jpg'),
        ];

        $path = 'uploads/images';

        $customFileNames = ['custom_name_1.jpg', 'custom_name_2.jpg'];

        $uploadedPaths = MediaHelper::uploadMultiple($files, $path, $customFileNames);

        foreach ($uploadedPaths as $uploadedPath) {
            Storage::assertExists($uploadedPath);
        }
        foreach ($customFileNames as $customFileName) {
            Storage::assertExists($path . '/' . $customFileName);
        }
    }

    /** @test */
    public function itUploadsBase64Image()
    {
        Storage::fake();

        $base64Image = self::BASE_64_IMAGE;

        $path = 'uploads/images';

        $uploadedPath = MediaHelper::uploadBase64Image($base64Image, $path);

        Storage::assertExists($uploadedPath);
    }

    /** @test */
    public function itUploadsBase64ImageWithCustomName()
    {
        Storage::fake();

        $base64Image = self::BASE_64_IMAGE;

        $path = 'uploads/images';

        $customFileName = 'custom_image.png';

        $uploadedPath = MediaHelper::uploadBase64Image($base64Image, $path, $customFileName);

        Storage::assertExists($uploadedPath);
        Storage::assertExists($path . '/' . $customFileName);
    }

    /** @test */
    public function itDeletesFile()
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $uploadedPath = MediaHelper::uploadFile($file, $path);

        MediaHelper::deleteFile($uploadedPath);

        Storage::assertMissing($uploadedPath);
    }

    /** @test */
    public function itGetsFileFullPath()
    {
        Storage::fake();

        $file = $this->getUploadedFile();

        $path = 'uploads/images';

        $uploadedPath = MediaHelper::uploadFile($file, $path);

        $fullPath = MediaHelper::getFileFullPath($uploadedPath);

        $this->assertEquals(Storage::url($uploadedPath), $fullPath);
    }

    /** @test */
    public function itGetsNullFileFullPathForNullFilePath()
    {
        Storage::fake();

        $fileFullPath = MediaHelper::getFileFullPath(null);

        $this->assertNull($fileFullPath);
    }

    /** @test */
    public function itUploadsAndDeletesBase64Images()
    {
        Storage::fake();

        $base64Image = self::BASE_64_IMAGE;
        $path = 'uploads/images';
        $uploadedPath = MediaHelper::uploadBase64Image($base64Image, $path);
        Storage::assertExists($uploadedPath);

        MediaHelper::deleteFile($uploadedPath);
        Storage::assertMissing($uploadedPath);
    }

    private function getUploadedFile(string $name = 'test1.jpg'): UploadedFile
    {
        return new UploadedFile(
            $this->testingImage,
            $name,
            'image/jpeg',
            null,
            true
        );
    }
}
