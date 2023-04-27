<?php

namespace app\controllers;


use PollService\PollService;
use Yii;
use yii\base\Controller;
use app\models\Poll;
use app\models\PollOptions;
use yii\base\UserException;
use yii\web\Response;

class PollController extends Controller
{
      /* POLL ACTIONS */

      /* возвращает json со всеми опросами(их название и текст) */
      public function actionGetpolls()
      {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $polls = Poll::find()->all();
            $result = array();

            foreach ($polls as $poll) { //пушим в массив DTO обьекты
                  array_push($result,
                  PollService::getPollObj($poll)
                  );
            }

            return $result; // возвращает json
      }

      /* получаем опрос и все варианты ответа на него, по id */
      public function actionGetpollandoptions()
      {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $id = $_GET['id'];

            return PollService::getPollById($id); // возвращает json
      }

      /* ACTION метод нужен для добавления голоса в бд (принимает option_id в строке запроса url'...&optionId=your-option-id') */
      public function actionVote()
      {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $session = Yii::$app->session;

            $optionId = $_GET['optionId'];

            try {
                  PollService::VoteOne($optionId);
                  return ['message' => 'Ok'];
            } catch (UserException $ex) {
                  return ['exception' => "Catch exception: " . $ex->getMessage()];
            }            
      }
      
      /* ACTION принимает json служит для добавления нового опроса */
      public function actionCreatepoll()
      {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $req = Yii::$app->request;

            if($req->isPost){

                  $json = json_decode($req->getRawBody());
                  $pollDTO = PollService::getPollFromJson($json); //декодим json и получаем обьект DTO из него 

                  try {
                        PollService::createPoll($pollDTO);
                  } catch (\Throwable $th) {
                        return $th;
                  }
                  return ['message' => 'OK'];
            }
            else{ // если на роут пришел запрос != "POST" выдаем сообщение об ошибке
                  return ['error' => 'BAD REQUEST'];
            }
      }

      /* ACTION принимает id из строки запроса, обновляет опрос из json-а */
      public function actionUpdatepoll()
      {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $req = Yii::$app->request;
            $pollId = $_GET['id'];

            if ($req->isPut) {
                  $json = json_decode($req->getRawBody());

                  $pollDTO = PollService::getPollFromJson($json);
                  PollService::updatePoll($pollId, $pollDTO);
                  return ['message' => 'OK'];
            }
            return ['error' => 'BAD REQUEST'];
      }

      /* ACTION удалят выбранный опрос (id указывается в строке запроса url) */
      public function actionDeletepoll()
      {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $req = Yii::$app->request;
            $pollId = $_GET['id'];

            if ($req->isDelete) {
                  PollService::deletePoll($pollId);
                  return ['message' => 'OK'];
            }
            return ['error' => 'BAD REQUEST'];
      }
}