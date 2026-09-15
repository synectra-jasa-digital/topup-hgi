<?php

namespace Tests\unit;

use CodeIgniter\Test\CIUnitTestCase;

final class UploadHelperTest extends CIUnitTestCase
{
    public function testRejectsInvalidUpload(): void
    {
        helper('upload');
        $file = new class {
            public function isValid(): bool { return false; }
            public function getTempName(): string { return ''; }
        };
        self::assertFalse(validate_uploaded_image_dimensions($file));
    }

    public function testRejectsMalformedImage(): void
    {
        helper('upload');
        $path = tempnam(sys_get_temp_dir(), 'upload-test-');
        file_put_contents($path, 'not an image');
        $file = new class($path) {
            public function __construct(private string $path) {}
            public function isValid(): bool { return true; }
            public function getTempName(): string { return $this->path; }
        };
        self::assertFalse(validate_uploaded_image_dimensions($file));
        unlink($path);
    }
    public function testRejectsOversizedFile(): void
    {
        helper('upload');
        $path = tempnam(sys_get_temp_dir(), 'upload-test-');
        file_put_contents($path, str_repeat('x', 1025));
        $file = new class($path) {
            public function __construct(private string $path) {}
            public function isValid(): bool { return true; }
            public function getTempName(): string { return $this->path; }
        };
        self::assertFalse(validate_uploaded_image_size($file, 1024));
        unlink($path);
    }

    public function testRejectsExtremeDimensions(): void
    {
        helper('upload');
        $path = tempnam(sys_get_temp_dir(), 'upload-test-');
        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='));
        $file = new class($path) {
            public function __construct(private string $path) {}
            public function isValid(): bool { return true; }
            public function getTempName(): string { return $this->path; }
        };
        self::assertFalse(validate_uploaded_image_dimensions($file, 0, 0));
        unlink($path);
    }
}
