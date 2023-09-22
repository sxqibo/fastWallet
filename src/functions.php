<?php

use Sxqibo\FastWallet\model\NumberGenerateRecord;

/**
 * Here is your custom functions.
 */
/**
 * 生成连续的唯一的编号
 * @param string $tenantId 租户ID
 * @param string $type 单号类型
 * @param string $prefix 前缀
 * @param int $length 长度
 * @param bool $isAddDate 编号是否增加日期
 * @return string
 */
function generateNumber($tenantId, $type, $prefix = '', $length = 6, $isAddDate = true): string
{
    $data = [
        'tenant_id' => $tenantId,
        'type'      => $type,
        'year'      => date('Y'),
        'month'     => date('m')
    ];
    // 加入锁机制，防止并发请求
    $lastRecord     = (new NumberGenerateRecord())->where($data)->order('id', 'DESC')->lock(true)->find();
    $data['number'] = $lastRecord ? $lastRecord->number + 1 : 1;

    if ($isAddDate) {
        $newNumber = strtoupper($prefix) . date('Ym') . sprintf("%0{$length}d", $data['number']) . rand(10000, 99999);
    } else {
        $newNumber = strtoupper($prefix) . sprintf("%0{$length}d", $data['number']) . rand(10000, 99999);
    }

    $data['prefix'] = $prefix;

    NumberGenerateRecord::create($data);

    return $newNumber;
}



