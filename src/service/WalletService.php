<?php

namespace Sxqibo\FastWallet\service;


use Sxqibo\FastWallet\model\NumberGenerateRecord;
use Sxqibo\FastWallet\model\Role;
use Sxqibo\FastWallet\model\Tenant;
use Sxqibo\FastWallet\model\User;
use Sxqibo\FastWallet\model\Wallet;
use Sxqibo\FastWallet\model\WalletFlow;

class WalletService
{
    protected $tenantId;
    protected $tenantPre;

    public function __construct($tenantId, $tenantPre = '')
    {
        $this->tenantId  = $tenantId;
        $this->tenantPre = $tenantPre;
    }

    /**
     * 保存钱包数据
     */
    public function saveWallet($totPrice, $accountId, $userId, $userType, $isFreezeAmount = false, $where = [])
    {
        $where['account_id'] = $accountId;
        $where['user_id']    = $userId;
        $where['user_type']  = $userType;
        $where['status']     = 1;

        $walletModel = new Wallet();
        $wallet      = $walletModel->where($where)
            ->where('tenant_id', $this->tenantId)
            ->find();

        if (!$wallet) {
            $id     = $walletModel->insertGetId([
                'tenant_id'   => $this->tenantId,
                'status'      => 1,
                'account_id'  => $accountId,
                'user_type'   => $userType,
                'user_id'     => $userId,
                'create_time' => time(),
                'update_time' => time()
            ]);
            $wallet = $walletModel->where(['id' => $id])->find();
        }

        if ($totPrice < 0) {
            $wallet->outcome_amount = bcadd(($wallet->outcome_amount ?? '0.00'), $totPrice, 2);
            $wallet->balance        = bcadd(($wallet->balance ?? '0.00'), $totPrice, 2);
        } else {
            if ($isFreezeAmount) {
                $wallet->freeze_amount = bcadd(($wallet->freeze_amount ?? '0.00'), $totPrice, 2);
            } else {
                $wallet->income_amount = bcadd(($wallet->income_amount ?? '0.00'), $totPrice, 2);
                $wallet->balance       = bcadd(($wallet->balance ?? '0.00'), $totPrice, 2);
            }
        }

        $originBalance = $wallet->getOrigin('balance');
        $wallet->save();

        $wallet->origin_balance = $originBalance;
        return $wallet;
    }

    /**
     * 保存钱包流水信息
     */
    public function saveWalletFlows($fromWallet, $toWallet, $tradeInfo, $order = [], $orderType = '')
    {
        $tradeTitle   = $tradeInfo['trade_title'];
        $tradeAmount  = $tradeInfo['trade_amount'];
        $memo         = $tradeInfo['memo'];
        $businessType = $tradeInfo['business_type'];
        $payType      = $tradeInfo['pay_type'];
        $userId       = $tradeInfo['user_id'];
        $adminId      = $tradeInfo['admin_id'] ?? 0;

        $tenantId  = $this->tenantId;
        $tenantPre = $this->tenantPre;
        $pre       = $tenantPre . 'TR';

        // 加载用户信息
        $fromName  = $this->getUserNameByUserType($fromWallet->user_type, $fromWallet->user_id);
        $toName    = $this->getUserNameByUserType($toWallet->user_type, $toWallet->user_id);
        $orderType = !empty($orderType) ? $orderType : '';

        $extra = [
            'from_origin_balance'  => $fromWallet->origin_balance,
            'from_changed_balance' => $fromWallet->balance,
            'to_origin_balance'    => $toWallet->origin_balance,
            'to_changed_balance'   => $toWallet->balance,
        ];

        $fromUserType  = $fromWallet->user_type;
        $toUserType    = $toWallet->user_type;
        $userBalance   = 0;
        $tenantBalance = 0;
        if (in_array($fromUserType, ['U', 'A']) && in_array($toUserType, ['U', 'A'])) {
            $userBalance   = $fromUserType == 'U' ? $fromWallet->balance : $toWallet->balance;
            $tenantBalance = $fromUserType == 'A' ? $fromWallet->balance : $toWallet->balance;
        }

        $flowData = [
            'user_id'         => $userId,
            'tenant_id'       => $tenantId,
            'from_account_id' => $fromWallet->account_id ?? '',
            'to_account_id'   => $toWallet->account_id ?? '',
            'from_wallet_id'  => $fromWallet->id,
            'to_wallet_id'    => $toWallet->id,
            'trade_number'    => generateNumber($tenantId, NumberGenerateRecord::NUMBER_TYPE_WALLET_FLOW, $pre), // TR => 交易流水
            // 订单信息相关
            'order_id'        => $order->id ?? 0,
            'order_number'    => $order->order_number ?? '',
            'order_type'      => $orderType,

            'trade_time'     => time(),
            'trade_title'    => $tradeTitle,
            'trade_content'  => '',
            'memo'           => $memo,
            'trade_amount'   => $tradeAmount, // 交易金额
            'tenant_balance' => $tenantBalance, // 租户余额
            'user_balance'   => $userBalance, // 用户余额

            'trade_from_user_type' => $fromWallet->user_type,
            'trade_from_user_id'   => $fromWallet->user_id,
            'trade_from_user_name' => $fromName,

            'trade_to_user_type' => $toWallet->user_type,
            'trade_to_user_id'   => $toWallet->user_id,
            'trade_to_user_name' => $toName ?? '',

            'business_type' => $businessType, // 业务类型
            'pay_type'      => $payType, // 消费类型
            'extra'         => json_encode($extra),

            'admin_id' => $adminId
        ];

        return (new WalletFlow())->create($flowData);
    }

    /**
     * 获取钱包流水
     */
    public function getWalletFlowList($params, $accountId, $role = Role::WALLET_ROLE_USER)
    {
        $where = [];
        $page  = $params['page'] ?? 1;
        $limit = $params['limit'] ?? 10;

        //查询钱包id
        $districtWalletInfo                = $this->getWalletInfo($params['user_id'], $role, $accountId);
        $where['from_wallet_id|wallet_id'] = !empty($districtWalletInfo) ? $districtWalletInfo->id : '';

        $list = (new WalletFlow())
            ->where($where)
            ->field(['id', 'trade_from_user_name', 'trade_to_user_name', 'trade_title', 'trade_amount', 'create_time', 'business_type', 'account_id', 'trade_from_user_id', 'trade_to_user_id'])
            ->order('create_time', 'desc')
            ->paginate($limit);

        foreach ($list as $item) {
            $item->trade_status_text = $item->business_type_text;
            if ($item->trade_from_user_id == $params['user_id']) {
                $item->trade_amount = '-' . $item->trade_amount;
            } else if ($item->trade_to_user_id == $params['user_id']) {
                $item->trade_amount = '+' . $item->trade_amount;
            }
        }

        return $list;
    }

    /**
     * 获取用户名称 （平台|用户）
     */
    public function getUserNameByUserType($userType, $userId)
    {
        $name = '';
        switch ($userType) {
            case Role::WALLET_ROLE_PLATFORM:
                $name = '平台';
                break;
            case Role::WALLET_ROLE_USER:
                $name = User::where('id', $userId)->value('nickname');
                break;
            case Role::WALLET_ROLE_AGENT:
                $name = Tenant::where('id', $userId)->value('name');
                break;
        }

        return $name;
    }

    /**
     * 根据用户ID获取账户余额
     */
    public function getBalanceAmount($userId, $accountId, $userType = Role::WALLET_ROLE_USER)
    {
        $where  = [
            'user_type'  => $userType,
            'account_id' => $accountId,
            'user_id'    => $userId
        ];
        $amount = Wallet::where($where)->value('balance');

        return intval($amount);
    }

    /**
     * 获取账户信息
     */
    public function getWalletInfo($userId, $userType, $accountId)
    {
        $where = [
            'user_id'    => $userId,
            'user_type'  => $userType,
            'account_id' => $accountId,
        ];

        return Wallet::where($where)->find();
    }
}
