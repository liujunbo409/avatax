<?php

namespace Smbear\Avatax;

use Smbear\Avatax\Traits\Order;
use Smbear\Avatax\Traits\Address;
use Illuminate\Support\Facades\Log;
use Smbear\Avatax\Enums\AvataxEnums;
use Smbear\Avatax\Events\SaveDataEvent;
use Smbear\Avatax\Contracts\AvataxInterface;
use Smbear\Avatax\Exceptions\ParamsException;
use Smbear\Avatax\Exceptions\AvataxException;
use Smbear\Avatax\Services\AvataxTransService;
use Smbear\Avatax\Services\AvataxClientService;
use Smbear\Avatax\Services\AvataxAddressService;

class Avatax implements AvataxInterface
{
    use Address,Order;

    public $local = 'en';

    public $addressService;

    public $transService;

    public $clientService;

    public function __construct()
    {
        $this->addressService = new AvataxAddressService();

        $this->transService   = new AvataxTransService();

        $this->clientService  = new AvataxClientService();
    }

    /**
     * @Notes:设置本地语言环境
     * 默认采用en
     * 由于目前avatax只针对于en，但为后期扩展预留口子
     * @param string $local
     * @return object
     * @Author: smile
     * @Date: 2021/5/27
     * @Time: 16:13
     */
    public function setLocal($local = 'en') : object
    {
        $this->local = $local;

        return $this;
    }

    /**
     * @Notes:计算税费
     *
     * @param string $type
     * @return array
     * @throws Exceptions\AvataxException
     * @throws ParamsException
     * @Author: smile
     * @Date: 2021/5/27
     * @Time: 16:14
     */
    public function createTransaction(string $type) : array
    {
        if ( !in_array($type,AvataxEnums::ALLOW_TYPES) ){
            throw new ParamsException('type 参数错误');
        }

        $address = $this->getAddress();
        $lines   = $this->getLines();
        $order   = $this->getOrder();

        try{
            $addressResult = $this->addressService->resolveAddress($this->clientService->getClient(),$address,$this->local);

            if($addressResult['status'] == false){
                if ($addressResult['type'] == AvaTaxEnums::ADDRESS_ERROR_TYPE_DEFAULT){
                    $address['line1'] = 'GENERAL DELIVERY';
                } else {
                    event(new SaveDataEvent(avatax_get_save_data($order['customerCode'],$order['code'],$address,$this->getFromAddress(),$order,$lines,true,$addressResult),$type));

                    return avatax_return_error($addressResult['message'] ?? 'address error',[]);
                }
            }

            $transActionResult = $this->transService->transaction($this->clientService->getClient(),$type,$address,$order,$lines,$this->getFromAddress());

            event(new SaveDataEvent(avatax_get_save_data($order['customerCode'],$order['code'],$address,$this->getFromAddress(),$order,$lines,true,$transActionResult),$type));

            foreach ($transActionResult as $result){
                if (is_string($result)) return avatax_return_error($result);
            }

            return avatax_return_success('success',(array) $transActionResult);
        }catch (\Throwable $exception){
            if (!$exception instanceof AvataxException){
                Log::channel(config('avatax.channel'))->info($exception);

                return avatax_return_error('api error '.$exception->getMessage(),[]);
            }

            throw $exception;
        }
    }

    /**
     * @Notes: 获取到地址是否被启动的状态
     *
     * @param array $data
     * @return bool|null
     * @Author: smile
     * @Date: 2021/6/8
     * @Time: 12:21
     */
    public function getAddressStatus(array $data): bool
    {
        $data = current($data);
        $address = current($data->addresses);

        if ($address->line1 == 'GENERAL DELIVERY'){
            return true;
        }

        return false;
    }

    /**
     * @Notes:返回响应的数据
     *
     * @param array $data
     * @return array
     * @Author: smile
     * @Date: 2021/6/7
     * @Time: 11:19
     */
    public function response(array $data): array
    {
        $result = [];

        if (empty($data)) return $result;

        $data = json_decode(json_encode($data),true);

        foreach ($data as $k => $v) {
            $result[$k]['transition_id'] = $v['id'] ?? 0;
            $result[$k]['totalTax']      = strval($v['totalTax']) ?? '0';
            $result[$k]['totalTaxable']  = strval($v['totalTaxable']) ?? '0';
            $result[$k]['totalAmount']   = strval($v['totalAmount']) ?? '0';
            $result[$k]['totalExempt']   = strval($v['totalExempt']) ?? '0';
            $result[$k]['products']      = [];
            if (!empty($v['lines'])) {

                foreach ($v['lines'] as $vv) {
                    $result[$k]['products'][$vv['itemCode']] = [
                        'tax'           => strval($vv['tax']) ?? '0',
                        'taxableAmount' => strval($vv['taxableAmount']) ?? '0',
                        'lineAmount'    => strval($vv['lineAmount']) ?? '0',
                        'qty'           => $vv['quantity'] ?? 0
                    ];

                    foreach ($vv['details'] as $vvv) {
                        $result[$k]['products'][$vv['itemCode']]['rate'][] = [
                            'jurisName'      => $vvv['jurisName'] ?? '',
                            'jurisdictionId' => $vvv['jurisdictionId'] ?? null,
                            'tax'            => strval($vvv['tax']) ?? '0',
                            'rate'           => strval($vvv['rate']) ?? '0',
                            'taxName'        => $vvv['taxName'] ?? ''
                        ];
                    }
                }
            }
        }

        return $result;
    }
}