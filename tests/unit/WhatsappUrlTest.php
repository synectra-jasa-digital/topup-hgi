<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class WhatsappUrlTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('order');
    }

    #[DataProvider('numbers')]
    public function testNormalisesToTheInternationalFormWaMeRequires(string $input, string $expected): void
    {
        self::assertSame($expected, whatsapp_url($input));
    }

    public static function numbers(): array
    {
        return [
            'local with leading 0'         => ['082165443677', 'https://wa.me/6282165443677'],
            'local with separators'        => ['0821-6544-3677', 'https://wa.me/6282165443677'],
            'already international'        => ['6282165443677', 'https://wa.me/6282165443677'],
            'plus sign and spaces'         => ['+62 821 6544 3677', 'https://wa.me/6282165443677'],
            'double zero prefix'           => ['0062821654436770', 'https://wa.me/62821654436770'],
            'missing leading zero'         => ['82165443677', 'https://wa.me/6282165443677'],
            'empty'                        => ['', ''],
            'no digits'                    => ['hubungi admin', ''],
            'too short to be a number'     => ['0812', ''],
        ];
    }

    public function testAddsAnEncodedMessageWhenGiven(): void
    {
        self::assertSame(
            'https://wa.me/6282165443677?text=Pengajuan%20bongkar%20BGK-1',
            whatsapp_url('082165443677', 'Pengajuan bongkar BGK-1')
        );
    }

    public function testNullContactGivesNoLink(): void
    {
        self::assertSame('', whatsapp_url(null));
    }
}
