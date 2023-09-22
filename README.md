# fastWallet

极速钱包模块儿

# 使用说明

```

$tradeAmount = 2.1;
$tradInfo = [
    'trade_title'   => '测试',
    'memo'          => '测试',
    'trade_amount'  => $tradeAmount,
    'business_type' => 1, 
    'pay_type'      => 1,
    'user_id'       => 1,
];

$fromUserId = 1;
$fromRole   = 'U';
$toRole     = 'A';

$fromAccountId = '';
$toAccountId   = '';
$tenantId = '226ba257-d398-40d1-980b-2081598a8898';
$tenantPre = ''; // 可为空

$walletService = new WalletService($tenantId,$tenantPre);
$formWallet    = $walletService->saveWallet('-' . $tradeAmount, $fromAccountId, $fromUserId, $fromRole);
$toWallet      = $walletService->saveWallet($$tradeAmount, $toAccountId, $orderModel->tenant_id, $toRole);

// $model => 对应的业务模型
// $model => 对应的模型类路径 
$walletService->saveWalletFlows($formWallet, $toWallet, $tradInfo, $model, $modelClass);

```
