# fastWallet

极速钱包模块儿

## 安装说明

`composer require sxqibo/fast-wallet`

备注：
> 1. 安装后会自动把database目录下的文件复制到项目对应的database目录下
> 2. 执行php think migrate:run 生成数据表
> 3. 执行 php think seed:run -s InitWalletAccountSeeder 生成钱包相关初始数据

## 使用说明

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

$fromAccountId = ''; // 来源账户ID
$toAccountId   = ''; // 去向账户ID
$tenantId = '226ba257-d398-40d1-980b-2081598a8898';
$tenantPre = ''; // 可为空

$walletService = new WalletService($tenantId,$tenantPre);
$formWallet    = $walletService->saveWallet('-' . $tradeAmount, $fromAccountId, $fromUserId, $fromRole);
$toWallet      = $walletService->saveWallet($tradeAmount, $toAccountId, $tenantId, $toRole);

// $model => 对应的业务模型
// $model => 对应的模型类路径 
$walletService->saveWalletFlows($formWallet, $toWallet, $tradInfo, $model, $modelClass);

```
