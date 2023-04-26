<?php

namespace app\controllers;

use PhpParser\JsonDecoder;
use Yii;
use yii\base\Controller;
use app\models\Poll;
use app\models\PollOptions;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class PollController extends Controller
{
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

      public function actionVote()// ACTION метод нужен для добавления голоса в бд (принимает option_id в строке запроса url'...&optionId=your-option-id')
      {
            $optionid = $_GET['optionId'];

            $pollOption = PollOptions::findOne($optionid);

            $pollOption->votes += 1;
            $pollOption->save();

            return json_encode(['message' => 'Ok'], JSON_UNESCAPED_UNICODE);
      }
      
      public function actionCreatepoll() // ACTION принимает json служит для добавления нового опроса
      {
            $req = Yii::$app->request;

            if($req->isPost){
                  $json = json_decode($req->getRawBody());

                  $poll = $json->{'poll'};

                  $pollId = Poll::find()->count() + 1;
                  $pollName = $poll->{'poll_name'};
                  $pollText = $poll->{'poll_text'};
                  $pollOptions = $poll->{'poll_options'};

                  $dbPoll = new Poll();
                  $dbPoll->poll_id = $pollId;
                  $dbPoll->poll_name = $pollName;
                  $dbPoll->poll_text = $pollText;
                  $dbPoll->save();

                  foreach ($pollOptions as $option) {
                        $optionClass = new PollOptions();
                        $optionClass->poll_id = $pollId;
                        $optionClass->option_title = $option;
                        $optionClass->save();
                  }

                  return json_encode(['message' => 'OK']);
            }
            else{
                  return json_encode(['error' => 'BAD REQUEST']);
            }
      }
}