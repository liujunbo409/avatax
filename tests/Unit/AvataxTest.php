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

        $res = $result = Avatax::setAddress([
            'line1' => '380 Centerpoint Blvd ',
            'city' => 'New Castle',
            'country' => 'US',
            'postalCode' => '1934720',
            'region' => 'DE'
        ])->setOrder([
            'documentCode'     => 'FS000000001',
            'customerCode'     => 123456789,
            'entityUseCode'    => 123456789,
            'currencyCode'     => 'USD',
            'exchangeRate'     => 1,
            'description'      => '描述',
            'purchaseOrderNo'  => 'FS000000001',
            'salespersonCode'  => 123,
            'lines'            =>[
                [
                    'amount'=> 10000,
                    'quantity'=>3,
                    'description' => '描述'
                ],
                [
                    'amount'=> 20000,
                    'quantity'=>3,
                    'description' => '描述'
                ],
            ]
        ])
            ->setLines(function ($lines) {
                foreach ($lines as $key => $value){
                    if ($value['type'] ?? '' == 'shipping'){
                        $lines[$key]['itemCode'] = 'Shipping';
                        $lines[$key]['taxCode']  = config('avatax.shippingTaxCode');
                    } else {
                        $lines[$key]['itemCode'] = $value['id'] ?? '';
                        $lines[$key]['taxCode']  = config('avatax.productsTaxCode');
                    }

                    $lines[$key]['number'] = $key + 1;
                }

                return $lines;
            })
            ->createTransaction('SalesOrder');

        $this->assertIsArray($res);
    }
}