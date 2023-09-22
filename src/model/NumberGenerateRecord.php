<?php

namespace Sxqibo\FastWallet\model;


use think\Model;

class NumberGenerateRecord extends Model
{
    /**
     * 与模型关联的表名
     * @var string
     */
    protected $name = 'tenant_number_generate_record';

    protected $autoWriteTimestamp = true;

    protected $createTime = 'create_time';
    protected $updateTime = false;

    // 生成编号类型
    const NUMBER_TYPE_WALLET_FLOW = 'wallet_flow'; // 钱包流水号
}
