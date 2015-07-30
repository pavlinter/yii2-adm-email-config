<?php

/**
 * @package yii2-adm-email-config
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 2.0.1
 */

namespace pavlinter\admeconfig;

use pavlinter\adm\Manager;
use Yii;

/**
 * @method \pavlinter\admeconfig\models\EmailConfig createEmailConfig
 * @method \pavlinter\admeconfig\models\EmailConfig createEmailConfigQuery
 */
class ModelManager extends Manager
{
    /**
     * @var string|\pavlinter\admeconfig\models\EmailConfig
     */
    public $emailConfigClass = 'pavlinter\admeconfig\models\EmailConfig';
}