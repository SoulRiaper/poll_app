<?php

namespace app\controllers;


use PollService\PollService;
use Yii;
use yii\base\Controller;
use app\models\Poll;
use app\models\PollOptions;

class PollController extends Controller
{
      /* POLL ACTIONS */

      /* возвращает json со всеми опросами(их название и текст) */
      public function actionGetpolls()
      {
            $polls = Poll::find()->all();
            $result = array();

            foreach ($polls as $poll) { //пушим в массив DTO обьекты
                  array_push($result,
                  PollService::getPollObj($poll)
                  );
            }

            return json_encode($result, JSON_UNESCAPED_UNICODE); // возвращает json
      }

      /* получаем опрос и все варианты ответа на него, по id */
      public function actionGetpollandoptions()
      {
            $id = $_GET['id'];

            return json_encode(PollService::getPollById($id), JSON_UNESCAPED_UNICODE); // возвращает json
      }

      /* ACTION метод нужен для добавления голоса в бд (принимает option_id в строке запроса url'...&optionId=your-option-id') */
      public function actionVote()
      {
            $optionId = $_GET['optionId'];

            PollService::VoteOne($optionId);

            return json_encode(['message' => 'Ok'], JSON_UNESCAPED_UNICODE);
      }
      
      /* ACTION принимает json служит для добавления нового опроса */
      public function actionCreatepoll()
      {
            $req = Yii::$app->request;

            if($req->isPost){
                  $json = json_decode($req->getRawBody());



                  $pollId = Poll::find()->count() + 1; //определяем id нового опроса (колличество + 1)
                  

                  foreach ($pollOptions as $option) { // создаем все варианты ответов опроса
                        $optionClass = new PollOptions();
                        $optionClass->poll_id = $pollId;
                        $optionClass->option_title = $option;
                        $optionClass->save();
                  }

                  return json_encode(['message' => 'OK']);
            }
            else{ // если на роут пришел запрос != "POST" выдаем сообщение об ошибке
                  return json_encode(['error' => 'BAD REQUEST']);
            }
      }
}