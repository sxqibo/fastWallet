<?php

namespace Sxqibo\FastWallet\model;


use think\Model;

class Role extends Model
{
    // 表名
    protected $name = '';

    const WALLET_ROLE_PLATFORM = 'P';
    const WALLET_ROLE_USER     = 'U';
    const WALLET_ROLE_AGENT    = 'A';

    /**
     * 获取钱包角色文本
     *
     * @param $role
     * @return string
     */
    public static function getWalletRoleTextByRole(string $role): string
    {
        $arr = [
            static::WALLET_ROLE_PLATFORM => '平台',
            static::WALLET_ROLE_USER     => '用户',
            static::WALLET_ROLE_AGENT    => '代理商',
        ];

        return $arr[$role] ?? '';
    }
}
