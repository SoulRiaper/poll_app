<?php

namespace app\controllers;


use PollService\PollService;
use Yii;
use yii\base\Controller;
use app\models\Poll;
use app\models\PollOptions;

class PollController extends Controller
{
      public function actionGetpolls() // возвращает json со всеми опросами(их название и текст)
      {
            $polls = Poll::find()->all();
            $result = array();

            foreach ($polls as $poll) {
                  array_push($result,
                  PollService::getPollObj($poll)
            );
            }

            return json_encode($result, JSON_UNESCAPED_UNICODE); // возвращает json
      }

      public function actionGetpollandoptions() // получаем опрос и все варианты ответа на него, по id 
      {
            $id = $_GET['id'];

            return json_encode(PollService::getPollById($id), JSON_UNESCAPED_UNICODE); // возвращает json
      }

      public function actionVote()// ACTION метод нужен для добавления голоса в бд (принимает option_id в строке запроса url'...&optionId=your-option-id')
      {
            $optionId = $_GET['optionId'];

            PollService::VoteOne($optionId);

            return json_encode(['message' => 'Ok'], JSON_UNESCAPED_UNICODE);
      }
      
      public function actionCreatepoll() // ACTION принимает json служит для добавления нового опроса
      {
            $req = Yii::$app->request;

            if($req->isPost){
                  $json = json_decode($req->getRawBody());

                  $poll = $json->{'poll'};

                  $pollId = Poll::find()->count() + 1; //определяем id нового опроса (колличество + 1)
                  $pollName = $poll->{'poll_name'}; // достаем данные из пришедшего json
                  $pollText = $poll->{'poll_text'};
                  $pollOptions = $poll->{'poll_options'};

                  $dbPoll = new Poll(); // создаем запись о опросе в бд
                  $dbPoll->poll_id = $pollId;
                  $dbPoll->poll_name = $pollName;
                  $dbPoll->poll_text = $pollText;
                  $dbPoll->save();

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

      public function actionTest()
      {
           return PollService::Hello('hello Traaaash');
      }
}