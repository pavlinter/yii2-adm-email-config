<?php

/**
 * @copyright Copyright &copy; Pavels Radajevs <pavlinter@gmail.com>, 2014
 * @package yii2-adm-email-config
 */

namespace pavlinter\admeconfig\controllers;

use pavlinter\admeconfig\Module;
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
        $id = 1;
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Adm::t('','Data successfully changed!'));
            return Adm::redirect(['update', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
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
