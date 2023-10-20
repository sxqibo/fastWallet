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
    protected $append = ['business_type_text', 'trade_time_text', 'user_origin_balance'];

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

    /**
     * 获取用户变更前余额
     */
    public function getUserOriginBalanceAttr($value, $data)
    {
        $extra = $data['extra'] ?? '';
        if (!empty($extra) && is_string($extra)) {
            $extra = json_decode($extra, true);
        }

        $tradeFromUserType = $data['trade_from_user_type'];
        $tradeToUserType   = $data['trade_to_user_type'];
        $userOriginBalance = '';

        if ($tradeFromUserType == 'U') {
            $userOriginBalance = $extra['from_origin_balance'] ?? '';
        } elseif ($tradeToUserType == 'U') {
            $userOriginBalance = $extra['to_origin_balance'] ?? '';
        }

        return $userOriginBalance;

    }
}
