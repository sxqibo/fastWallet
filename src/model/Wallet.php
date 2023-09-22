<?php

namespace Sxqibo\FastWallet\model;


use think\Model;

class Wallet extends Model
{
    /**
     * 与模型关联的表名
     * @var string
     */
    protected $name = 'tenant_wallet';

    protected $autoWriteTimestamp = true;
}
