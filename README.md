# 说明

* 依赖avatax官方扩展包
* 采用psr-4的标准
* 单元测试覆盖基本功能

# 安装配置

安装composer包

```
composer require smbear/avatax
```

发布配置文件

```
php artisan vendor:publish --provider=Smbear\Avatax\AvataxServiceProvider
```

迁移数据表

```
php artisan migrate
```

配置日志channel(config/logging)

```
'avatax' => [
    'driver' => 'daily',
    'path' => storage_path('logs/avatax/avatax.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'days' => 14,
],
```

# 使用方式（门面/契约）

```
use Smbear\Avatax\Facades\Avatax;

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
        ->createTransaction('SalesOrder');
```
> setLines函数中设置 lines的二维数组，每一个数组默认表示一个整体订单，返回结果，会使用key作为标识
### 成功返回的数据，当status == 'success' 表示成功，其余所有情况，均为失败
```phpregexp
array:4 [▼
  "status" => "success"
  "code" => 200
  "message" => "success"
  "data" => array:2 [▼
    "local" => {#352 ▼
      +"id": 6000155272900
      +"code": "FS000000001"
      +"companyId": 2730548
      +"date": "2021-05-31"
      +"paymentDate": "1900-01-01"
      +"status": "Committed"
      +"type": "SalesInvoice"
      +"batchCode": ""
      +"currencyCode": "USD"
      +"exchangeRateCurrencyCode": "USD"
      +"customerUsageType": "123456789"
      +"entityUseCode": "123456789"
      +"customerVendorCode": "123456789"
      +"customerCode": "123456789"
      +"exemptNo": ""
      +"reconciled": false
      +"locationCode": ""
      +"reportingLocationCode": ""
      +"purchaseOrderNo": "FS000000001"
      +"referenceCode": ""
      +"salespersonCode": "123"
      +"taxOverrideType": "None"
      +"taxOverrideAmount": 0.0
      +"taxOverrideReason": ""
      +"totalAmount": 40.8
      +"totalExempt": 40.8
      +"totalDiscount": 0.0
      +"totalTax": 0.0
      +"totalTaxable": 0.0
      +"totalTaxCalculated": 0.0
      +"adjustmentReason": "Other"
      +"adjustmentDescription": "Create or adjust transaction"
      +"locked": false
      +"region": "DE"
      +"country": "US"
      +"version": 3
      +"softwareVersion": "21.5.1.0"
      +"originAddressId": 0
      +"destinationAddressId": 0
      +"exchangeRateEffectiveDate": "2021-05-31"
      +"exchangeRate": 1.0
      +"isSellerImporterOfRecord": false
      +"description": ""
      +"businessIdentificationNo": ""
      +"modifiedDate": "2021-06-01T02:44:29.0497913Z"
      +"modifiedUserId": 1414955
      +"taxDate": "2021-05-31T00:00:00"
      +"lines": array:2 [▶]
      +"addresses": array:1 [▶]
      +"locationTypes": array:2 [▶]
      +"summary": array:1 [▶]
    }
    "delay" => {#365 ▼
      +"id": 7091650277
      +"code": "FS000000001"
      +"companyId": 2730548
      +"date": "2021-05-31"
      +"paymentDate": "1900-01-01"
      +"status": "Committed"
      +"type": "SalesInvoice"
      +"batchCode": ""
      +"currencyCode": "USD"
      +"exchangeRateCurrencyCode": "USD"
      +"customerUsageType": "123456789"
      +"entityUseCode": "123456789"
      +"customerVendorCode": "123456789"
      +"customerCode": "123456789"
      +"exemptNo": ""
      +"reconciled": false
      +"locationCode": ""
      +"reportingLocationCode": ""
      +"purchaseOrderNo": "FS000000001"
      +"referenceCode": ""
      +"salespersonCode": "123"
      +"taxOverrideType": "None"
      +"taxOverrideAmount": 0.0
      +"taxOverrideReason": ""
      +"totalAmount": 40.8
      +"totalExempt": 40.8
      +"totalDiscount": 0.0
      +"totalTax": 0.0
      +"totalTaxable": 0.0
      +"totalTaxCalculated": 0.0
      +"adjustmentReason": "Other"
      +"adjustmentDescription": "Create or adjust transaction"
      +"locked": false
      +"region": "DE"
      +"country": "US"
      +"version": 4
      +"softwareVersion": "21.5.1.0"
      +"originAddressId": 0
      +"destinationAddressId": 0
      +"exchangeRateEffectiveDate": "2021-05-31"
      +"exchangeRate": 1.0
      +"isSellerImporterOfRecord": false
      +"description": ""
      +"businessIdentificationNo": ""
      +"modifiedDate": "2021-06-01T02:44:29.2929873Z"
      +"modifiedUserId": 1414955
      +"taxDate": "2021-05-31T00:00:00"
      +"lines": array:2 [▶]
      +"addresses": array:1 [▶]
      +"locationTypes": array:2 [▶]
      +"summary": array:1 [▶]
    }
  ]
]
```










