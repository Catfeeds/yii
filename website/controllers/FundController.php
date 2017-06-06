<?php
namespace app_web\controllers;

use Yii;
use app_web\components\CController;

class FundController extends CController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
    
    /**
     * 库存销存列表
     */
    public function actionInvoicing()
    {
        return $this->render('invoicing');
    }
    
    //订单支付
    public function actionOrder()
    {
        return $this->render('order');
    }
    
     //资金流水日志
    public function actionLog()
    {
        return $this->render('log');
    }
}
