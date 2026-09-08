<?php

namespace Tests\Feature;

use App\Models\BongkarCatalogModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;

final class BongkarRequestTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $refresh = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = db_connect('tests');
        $migrate = \Config\Services::migrations();
        $migrate->setNamespace('App')->setGroup('tests');
        $migrate->latest();
    }

    public function testHomePageRendersBongkarCatalogFromDatabase(): void
    {
        $catalog = new BongkarCatalogModel();
        $catalog->insert([
            'code'       => 'kartu-ungu',
            'name'       => 'Kartu Ungu',
            'unit_label'  => 'kartu',
            'base_rate'   => 65000,
            'sort_order'  => 1,
            'is_active'   => 1,
        ]);

        $result = $this->get('/');
        $body = $result->getBody();

        $result->assertOK();
        self::assertStringContainsString('Jual atau Bongkar Kartu', $body);
        self::assertStringContainsString('Kartu Ungu', $body);
        self::assertStringContainsString('data-bongkar-catalog-id="', $body);
    }

    public function testBongkarSubmitStoresRequestAndReturnsWhatsAppUrl(): void
    {
        $catalog = new BongkarCatalogModel();
        $catalog->insert([
            'code'       => 'kartu-ungu',
            'name'       => 'Kartu Ungu',
            'unit_label' => 'kartu',
            'base_rate'  => 65000,
            'sort_order' => 1,
            'is_active'  => 1,
        ]);

        putenv('wablas.adminPhone=081234567890');
        $_ENV['wablas.adminPhone'] = '081234567890';

        $result = $this->withBodyFormat('json')
            ->withBody(json_encode([
                'bongkar_catalog_id' => 1,
                'quantity' => 2,
                'customer_whatsapp' => '081299988877',
                'payout_method' => 'BCA',
                'customer_note' => 'tolong cepat',
            ]))
            ->post('bongkar/submit');

        $result->assertStatus(200);
        $body = $result->getBody();
        self::assertStringContainsString('wa.me', $body);
        self::assertStringContainsString('request_number', $body);
        self::assertStringContainsString('success', $body);

        $request = $this->db->table('bongkar_requests')->where('customer_whatsapp', '081299988877')->get()->getRowArray();
        self::assertNotNull($request);
        self::assertSame('Kartu Ungu', $request['catalog_name_snapshot']);
        self::assertSame('2', (string) $request['quantity']);
        self::assertSame(130000.0, (float) $request['estimated_amount']);
    }
}
