<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@libs', dirname(dirname(__DIR__)) . '/libs');
Yii::setAlias('@app_web', dirname(dirname(__DIR__)) . '/website');
Yii::setAlias('@app_api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('@app_dev', dirname(dirname(__DIR__)) . '/dev');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@log', dirname(dirname(__DIR__)) . '/website/runtime/logs');
Yii::setAlias('@db_back_path', dirname(dirname(__DIR__)) . '/website/web/dbBack');
