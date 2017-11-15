<?php

namespace common\modules\article\actions;

use common\modules\article\models\Article;
use yii\data\Pagination;

/**
 * Description of SearchAction
 *
 * @author makszipeter
 */
class SearchAction extends \yii\base\Action {

    public function run() {
        $keyword = \Yii::$app->getRequest()->getQueryParam('keyword');

        $articleQuery = Article::find()->
                        where(['like', 'title', $keyword])->orWhere(['like', 'lead', $keyword])->orWhere(['like', 'main_content', $keyword])->
                        orderBy(['publicated_at' => SORT_DESC])->with(['image', 'user', 'comments']);

        $provider = new \yii\data\ActiveDataProvider([
            'query' => $articleQuery,
            'pagination' => [
                'pageSize' => 12,
            ],
            'sort' => [
                'defaultOrder' => [
                    'publicated_at' => SORT_DESC,
                ]
            ],
        ]);

        return $this->controller->render('index', [
                    'dataProvider' => $provider,
        ]);
    }

}
