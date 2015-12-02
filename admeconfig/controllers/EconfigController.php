<?php

/**
 * @package yii2-adm-email-config
 * @author Pavels Radajevs <pavlinter@gmail.com>
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2015
 * @version 2.0.0
 */

namespace pavlinter\admeconfig\controllers;

use pavlinter\admeconfig\models\EmailConfig;
use pavlinter\admeconfig\Module;
use pavlinter\admparams\models\Params;
use Yii;
use pavlinter\adm\Adm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * EconfigController implements the CRUD actions for EmailConfig model.
 */
class EconfigController extends Controller
{
    /**
     * Updates an existing EmailConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUpdate()
    {
        /* @var $paramModule \pavlinter\admparams\Module */
        $paramModule = Yii::$app->getModule('admparams');
        $paramsValue = null;
        if (isset(Yii::$app->params['adminEmails'])) {
            $paramsValue = explode(EmailConfig::EMAIL_SEPARATOR, Yii::$app->params['adminEmails']);
            $paramsValue = array_filter($paramsValue, function ($v) {
                return $v !== '';
            });
        }

        $id = 1;
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $param = $paramModule->manager->createParamsQuery('find')->where(['name' => 'adminEmails'])->one();
            if ($param === null) {
                $param = $paramModule->manager->createParams();
                $param->name = 'adminEmails';
            }
            $paramsPost = Yii::$app->request->post('params');
            if(is_array($paramsPost)){
                $param->value = implode(EmailConfig::EMAIL_SEPARATOR, $paramsPost);
            } else {
                $param->value = null;
            }
            $param->save(false);
            Yii::$app->getSession()->setFlash('success', Adm::t('','Data successfully changed!'));
            return Adm::redirect(['update']);
        }

        $data = [];
        if (is_array($paramsValue)) {
            foreach ($paramsValue as $v) {
                $data[$v] = $v;
            }
        }
        return $this->render('update', [
            'model' => $model,
            'paramsValue' => $paramsValue,
            'data' => $data,
        ]);
    }

    /**
     * @return \yii\web\Response
     */
    public function actionTest()
    {

        $error = Module::getInstance()->manager->createEmailConfigQuery('eachEmail', function ($email) {
            try {
                Yii::$app->mailer->compose()
                    ->setTo($email)
                    ->setFrom(Yii::$app->params['adminEmailName'])
                    ->setSubject(Adm::t('adm_email_config','Test subject', ['dot' => false]))
                    ->setHtmlBody(Adm::t('adm_email_config','Test text', ['dot' => false]))
                    ->send();
            } catch (\Exception $e) {
                if ($e instanceof \Swift_RfcComplianceException) {
                    Yii::$app->getSession()->setFlash('error', Adm::t('adm_email_config','Maybe incorrect email address: {error}', ['error' => $e->getMessage(),'dot' => true]));
                } else {
                    Yii::$app->getSession()->setFlash('error', Adm::t('adm_email_config','Error: {error}', ['error' => $e->getMessage(),'dot' => true]));
                }
                return true;
            }

        });

        if ($error) {
            Yii::$app->getSession()->removeFlash('success');
        } else {
            Yii::$app->getSession()->setFlash('success', Adm::t('adm_email_config','Email sent!', ['dot' => true]));
        }

        return Adm::redirect(['update']);
    }

    /**
     * Finds the EmailConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EmailConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Module::getInstance()->manager->createEmailConfigQuery("find")->where(['id' => $id])->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
