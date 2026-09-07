<?php

namespace Tests\Feature;

use App\Libraries\WablasGateway;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Config\Services;
use Config\Services as AppServices;
use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\Response;

class WablasTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock cURL requests to not actually hit Wablas during tests
        $curlMock = $this->getMockBuilder(CURLRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['post'])
            ->getMock();

        $responseMock = $this->createMock(Response::class);
        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('getBody')->willReturn('{"status":true,"message":"Success"}');
        
        $curlMock->method('post')->willReturn($responseMock);

        AppServices::injectMock('curlrequest', $curlMock);
    }

    public function testSendToAdminReturnsTrue()
    {
        // Temporary environment bypass for test since it reads env direct sometimes depending on mock strategy
        $_ENV['wablas.token'] = 'DUMMY';
        $_ENV['wablas.adminPhone'] = '0811223344';
        
        $wablas = new WablasGateway();
        $order = [
            'invoice_number' => 'INV123',
            'product_name_snapshot' => 'Koin',
            'nominal_snapshot' => '1B',
            'total_amount' => 50000,
            'game_id' => '12345',
            'whatsapp_number' => '089999999'
        ];

        $this->assertTrue($wablas->sendToAdminNewOrder($order));
    }

    public function testSendToCustomerReturnsTrue()
    {
        $_ENV['wablas.token'] = 'DUMMY';
        
        $wablas = new WablasGateway();
        $order = [
            'invoice_number' => 'INV123',
            'product_name_snapshot' => 'Koin',
            'nominal_snapshot' => '1B',
            'game_id' => '12345',
            'whatsapp_number' => '089999999'
        ];

        $this->assertTrue($wablas->sendToCustomerOrderCompleted($order));
    }
}
