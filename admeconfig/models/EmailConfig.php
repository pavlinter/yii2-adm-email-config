<?php

namespace pavlinter\admeconfig\models;

use Yii;

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
            [['encryption'], 'in', 'range' => array_keys(self::encryptionList())],
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
        $key = self::className() . '-econfig';
        $row = Yii::$app->cache->get($key);
        if ($row === false) {
            $row = self::find()->asArray()->one();
            $query = new \yii\db\Query();
            $sql = $query->select('MAX(updated_at)')
                ->from(self::tableName())
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
        /* @var \yii\swiftmailer\Mailer $mail */
        $mailer = Yii::$app->mailer;
        if ($row['enable_smtp']) {
            /* @var \Swift_SmtpTransport|\Swift_MailTransport $transport */
            $transport = $mailer->getTransport();
            if ($transport instanceof \Swift_SmtpTransport) {
                $transport->setUsername($row['username']);
                $transport->setPassword($row['password']);
                $transport->setHost($row['host']);
                $transport->setPort($row['port']);
                $transport->setEncryption($row['encryption']);
            } else {
                $transport = Yii::createObject([
                    'class' => 'Swift_SmtpTransport',
                    'host' => $row['host'],
                    'username' => $row['username'],
                    'password' => $row['password'],
                    'port' => $row['port'],
                    'encryption' => $row['encryption'],
                ]);
            }
            $mailer->setTransport($transport);
        }
    }
}
