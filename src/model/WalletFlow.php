<?php

namespace Sxqibo\FastWallet\model;

use think\Model;

class WalletFlow extends Model
{
    /**
     * 与模型关联的表名
     * @var string
     */
    protected $name = 'tenant_wallet_flow';

    /**
     * 追加字段
     * @var array
     */
    protected $append = ['business_type_text', 'trade_time_text'];

    /**
     * 业务类型定义
     */
    const  BUSINESS_TYPE_TEST = 1; // 测试

    public static function getBusinessTypeList()
    {
        $businessTypeMap = [
            static::BUSINESS_TYPE_TEST => '测试'
        ];

        return $businessTypeMap;
    }

    /**
     * 获取业务类型文本
     */
    public function getBusinessTypeTextAttr($value, $data): string
    {
        $value = $value ?? $data['business_type'] ?? '';
        $list  = static::getBusinessTypeList();
        return $list[$value] ?? '';
    }

    public function getTradeTimeTextAttr($val, $data)
    {
        $tradeTime = $data['trade_time'] ?? '';
        return $tradeTime ? date('Y-m-d H:i', $tradeTime) : '';
    }
}
