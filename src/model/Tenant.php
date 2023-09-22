<?php

namespace Sxqibo\FastWallet\model;


use think\Model;

class Tenant extends Model
{

    /**
     * 与模型关联的表名
     * @var string
     */
    protected $name = 'tenant';

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = ['delete_time', 'update_time'];
}
