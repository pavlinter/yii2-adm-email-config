<?php

/**
 * @package yii2-adm-email-config
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 2.0.0
 */

namespace pavlinter\admeconfig;

use pavlinter\adm\Adm;
use pavlinter\adm\AdmBootstrapInterface;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * @property \pavlinter\admeconfig\ModelManager $manager
 */
class Module extends \yii\base\Module implements AdmBootstrapInterface
{
    public $controllerNamespace = 'pavlinter\admeconfig\controllers';

    public $layout = '@vendor/pavlinter/yii2-adm/adm/views/layouts/main';
    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, $config = [])
    {
        $config = ArrayHelper::merge([
            'components' => [
                'manager' => [
                    'class' => 'pavlinter\admeconfig\ModelManager'
                ],
            ],
        ], $config);

        parent::__construct($id, $parent, $config);
    }

    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }

    /**
     * @param \pavlinter\adm\Adm $adm
     */
    public function loading($adm)
    {
        if ($adm->user->can('AdmRoot')) {
            $adm->params['left-menu']['settings']['items'][] = [
                'label' => '<span>' . $adm::t('menu', 'Email') . '</span>',
                'url' => ['/admeconfig/econfig/update']
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $adm = Adm::register();
        if (!parent::beforeAction($action) || !$adm->user->can('AdmRoot')) {
            return false;
        }
        return true;
    }
}
