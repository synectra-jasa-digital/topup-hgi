<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

final class BannerImageSetTest extends CIUnitTestCase
{
    private const DIR = 'assets/uploads/banners/';
    private const NAME = 'zz-test-banner';

    protected function setUp(): void
    {
        parent::setUp();
        helper('banner');
    }

    protected function tearDown(): void
    {
        foreach (['.png', '.webp', '-400w.webp', '-700w.webp'] as $suffix) {
            @unlink(FCPATH . self::DIR . self::NAME . $suffix);
        }
        parent::tearDown();
    }

    public function testRemoteAndNonPngImagesHaveNoWebpVariants(): void
    {
        self::assertSame(
            ['src' => 'https://cdn.example.test/a.png', 'webp' => null, 'srcset' => null],
            banner_image_set('https://cdn.example.test/a.png')
        );

        $jpg = banner_image_set(self::DIR . 'photo.jpg');
        self::assertSame(base_url(self::DIR . 'photo.jpg'), $jpg['src']);
        self::assertNull($jpg['webp']);
        self::assertNull($jpg['srcset']);
    }

    public function testPngWithoutAWebpCopyStaysPlain(): void
    {
        $set = banner_image_set(self::DIR . self::NAME . '.png');

        self::assertSame(base_url(self::DIR . self::NAME . '.png'), $set['src']);
        self::assertNull($set['webp']);
        self::assertNull($set['srcset']);
    }

    public function testSrcsetListsOnlyTheSizesThatExistOnDisk(): void
    {
        foreach (['.webp', '-400w.webp'] as $suffix) {
            file_put_contents(FCPATH . self::DIR . self::NAME . $suffix, 'x');
        }

        $set = banner_image_set(self::DIR . self::NAME . '.png');

        self::assertSame(base_url(self::DIR . self::NAME . '.webp'), $set['webp']);
        self::assertStringContainsString('-400w.webp', $set['srcset']);
        self::assertStringContainsString(' 400w', $set['srcset']);
        self::assertStringNotContainsString('700w', $set['srcset']);
        // The full-size file always closes the list.
        self::assertStringEndsWith(self::NAME . '.webp 900w', $set['srcset']);
    }
}
