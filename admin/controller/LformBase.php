<?php

namespace app\admin\controller;

use think\facade\View;

use Lake\Module\Controller\AdminBase;

/**
 * 控制器基类
 *
 * @create 2019-11-8
 * @author deatil
 */
class LformBase extends AdminBase
{

    protected $module = 'lform';

    protected function initialize()
    {
        parent::initialize();
    }

}
