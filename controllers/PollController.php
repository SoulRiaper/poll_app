<?php

namespace app\controllers;

use yii\base\Controller;
use app\models\Poll;
use app\models\PollOptions;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class PollController extends Controller
{
      public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yiiwebErrorAction',
            ],
            'captcha' => [
                'class' => 'yiicaptchaCaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

      public function actionGetpolls() // возвращает json со всеми опросами(их название и текст)
      {

            $result = ['polls' => []];

            $polls = Poll::find()->all();

            foreach($polls as $poll)
            {
                  array_push($result['polls'], [
                        "poll_id" => $poll->poll_id ,
                        'poll_name' => $poll->poll_name,
                        'poll_text' => $poll->poll_text
                  ]);
            }
            return json_encode($result, JSON_UNESCAPED_UNICODE); // возвращает json
      }

      public function actionGetpollandoptions() // получаем опрос и все варианты ответа на него, по id 
      {
            $id = $_GET['id'];
            $poll = Poll::findOne($id);
            $pollOptions = PollOptions::find()->where(['poll_id' => $id])->all();

            $result = ['poll' =>  //добавляем в итоговый массив название и текст опроса
            [
                  'poll_id' => $poll->poll_id,
                  'poll_name' => $poll->poll_name,
                  'poll_text' => $poll->poll_text,
                  'poll_options' =>[]
            ]];

            foreach ($pollOptions as $option) { // добавляем все его варианты ответа
                  array_push($result['poll']['poll_options'], 
                  [
                        'option_title' => $option->option_title,
                        'votes' => $option->votes
                  ]);
            }
            return json_encode($result, JSON_UNESCAPED_UNICODE); // возвращает json
      }

      public function actionVote()
      {
            $optionid = $_GET['optionId'];

            $pollOption = PollOptions::findOne($optionid);

            $pollOption->votes += 1;
            $pollOption->save();

            return json_encode(['message' => 'Ok'], JSON_UNESCAPED_UNICODE);
      }
}
