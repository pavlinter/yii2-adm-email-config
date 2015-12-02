<?php

/**
 * @package yii2-adm-email-config
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 2.1.0
 */

namespace pavlinter\admeconfig\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%adm_econfig}}".
 *
 * @property integer $id
 * @property string $host
 * @property string $port
 * @property integer $enable_smtp
 * @property string $username
 * @property string $password
 * @property string $encryption
 * @property string $from_email
 * @property string $from_name
 * @property string $updated_at
 */
class EmailConfig extends \yii\db\ActiveRecord
{
    const EMAIL_SEPARATOR = ',';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
			[
				'class' => \yii\behaviors\TimestampBehavior::className(),
				'updatedAtAttribute' => 'updated_at',
				'attributes' => [
					\yii\db\BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at']
				], 
				'value' => new \yii\db\Expression('NOW()')
			],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%adm_econfig}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['host', 'port', 'encryption', 'from_email'], 'required'],
            [['enable_smtp'], 'boolean'],
            [['encryption'], 'in', 'range' => array_keys(static::encryptionList())],
            [['host', 'username', 'password', 'from_email', 'from_name'], 'string', 'max' => 250],
            [['port', 'encryption'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('modelAdm/adm_email_config', 'ID'),
            'host' => Yii::t('modelAdm/adm_email_config', 'Host'),
            'port' => Yii::t('modelAdm/adm_email_config', 'Port'),
            'enable_smtp' => Yii::t('modelAdm/adm_email_config', 'Enable SMTP authentication'),
            'username' => Yii::t('modelAdm/adm_email_config', 'Username'),
            'password' => Yii::t('modelAdm/adm_email_config', 'Password'),
            'encryption' => Yii::t('modelAdm/adm_email_config', 'Encryption'),
            'from_email' => Yii::t('modelAdm/adm_email_config', 'From'),
            'from_name' => Yii::t('modelAdm/adm_email_config', 'From Name'),
            'updated_at' => Yii::t('modelAdm/adm_email_config', 'Updated At'),
        ];
    }

    /**
     * @return array
     */
    public static function encryptionList()
    {
        return [
            'tls' => Yii::t('modelAdm/adm_email_config', 'tls'),
            'ssl' => Yii::t('modelAdm/adm_email_config', 'ssl'),
        ];
    }

    /**
     *
     */
    public static function changeMailConfig()
    {
        $key = static::className() . '-econfig';
        $row = Yii::$app->cache->get($key);
        if ($row === false) {
            $row = static::find()->asArray()->one();
            $query = new \yii\db\Query();
            $sql = $query->select('MAX(updated_at)')
                ->from(static::tableName())
                ->createCommand()
                ->getRawSql();
            Yii::$app->cache->set($key, $row, 86400, new \yii\caching\DbDependency([
                'sql' => $sql,
            ]));

        }

        Yii::$app->params['adminEmail'] = $row['from_email'];
        Yii::$app->params['adminName']  = $row['from_name'];
        if ($row['from_name'] !== '') {
            Yii::$app->params['adminEmailName'] = [$row['from_email'] => $row['from_name']];
        } else {
            Yii::$app->params['adminEmailName'] = $row['from_email'];
        }
        /* @var \yii\swiftmailer\Mailer $mailer */
        $mailer = Yii::$app->mailer;
        if ($row['enable_smtp']) {
            $transport = [
                'class' => 'Swift_SmtpTransport',
                'host' => $row['host'],
                'username' => $row['username'],
                'password' => $row['password'],
                'port' => $row['port'],
                'encryption' => $row['encryption'],
            ];
            $mailer->setTransport($transport);
            $mailer->getSwiftMailer(); //rewrite Instance
        }
    }


    /**
     * @param callable $func
     * @param array|string $include
     * @param array|string $exclude
     * @throws \yii\base\InvalidConfigException
     */
    public static function eachEmail(\Closure $func , $include = [], $exclude = [])
    {
        $emails = static::exclude($include, $exclude);

        foreach ($emails as $email) {
            $res = call_user_func($func, $email);
            if ($res !== null && $res !== true) {
                return $res;
            }
        }
    }

    /**
     * @param array|string $include
     * @param array|string $exclude
     * @return array
     */
    public static function exclude($include = [], $exclude = [])
    {
        if (!is_array($include)) {
            $include = [$include];
        }
        if (!is_array($exclude)) {
            $exclude = [$exclude];
        }

        foreach ($include as $k => $v) {
            if(is_integer($k)) {

                if (is_array($v)) {
                    foreach ($v as $email => $name) {
                        $include[$k] = $email;
                        break;
                    }
                } else {
                    $include[$k] = $v;
                }
            } else {
                foreach ($include as $email => $name) {
                    $include = [$email];
                    break;
                }
                break;
            }
        }

        $params = ArrayHelper::merge($include, static::getEmails());

        if (empty($exclude)) {
            return $params;
        }

        foreach ($params as $key => $item) {
            if (in_array($item, $exclude)) {
                unset($params[$key]);
            }
        }
        return $params;
    }

    public static function getEmails()
    {
        $emails = [];
        if (isset(Yii::$app->params['adminEmails'])) {
            if (Yii::$app->params['adminEmails'] !== '') {
                $emails = explode(static::EMAIL_SEPARATOR, Yii::$app->params['adminEmails']);
            }
        }
        return $emails;
    }

}
