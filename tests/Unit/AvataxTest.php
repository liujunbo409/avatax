<?php

namespace Smbear\Avatax\Tests\Unit;

use Smbear\Avatax\Tests\TestCase;
use Smbear\Avatax\Facades\Avatax;

class AvataxTest extends TestCase
{
    public function test_set_local()
    {
        $_this = Avatax::setLocal('en');

        $this->assertEquals('en', $_this->local);
    }

    public function test_create_transaction()
    {
        $type = 'SalesOrder';

        $result = Avatax::setAddress([
            'line1'      => '380 Centerpoint Blvd ',
            'city'       => 'New Castle',
            'country'    => 'US',
            'postalCode' => '19720',
            'region'     => 'DE'
        ])
            ->setOrder([
                'documentCode'     => 'FS000000001',
                'customerCode'     => 123456789,
                'entityUseCode'    => 123456789,
                'currencyCode'     => 'USD',
                'exchangeRate'     => 1,
                'description'      => '描述',
                'purchaseOrderNo'  => 'FS000000001',
                'salespersonCode'  => 123,
            ])
            ->setLines([
                'local' =>[
                    [
                        "quantity" => "1",
                        "description" => "Cisco QSFP-40G-SR4 Compatible 40GBASE-SR4 QSFP+ 850nm 150m DOM MTP/MPO MMF Optical Transceiver Module",
                        "amount" => "40.8000000000000",
                        "itemCode" => "36157",
                    ],
                    [
                        "quantity" => 1,
                        "description" => "upsgroundeastzones_upsgroundeastzones",
                        "amount" => "0.00",
                        "itemCode" => "shipping",
                    ],
                ],
                'delay' =>[
                    [
                        "quantity" => "1",
                        "description" => "Cisco QSFP-40G-SR4 Compatible 40GBASE-SR4 QSFP+ 850nm 150m DOM MTP/MPO MMF Optical Transceiver Module",
                        "amount" => "40.8000000000000",
                        "itemCode" => "36157",
                    ],
                    [
                        "quantity" => 1,
                        "description" => "upsgroundeastzones_upsgroundeastzones",
                        "amount" => "0.00",
                        "itemCode" => "shipping",
                    ],
                ]
            ])
            ->createTransaction('SalesInvoice');

        $this->assertIsArray($result);
    }
}