<?php

namespace app\components\Pollcomponent;

use app\components\SessionComponent\SessionService;
use app\models\Poll;
use app\models\PollOptions;
use Yii;
use yii\base\UserException;

class PollService
{
      /* МЕТОДЫ МАНИПУЛЯЦИИ ОПРОСАМИ */

      /* принимает обьект из базы данных и позвращает Dto обьект */
      public static function getPollObj($poll): PollDto
      {
            return new PollDto( $poll->poll_id , $poll->poll_name, $poll->poll_text, []);
      }
      
      /* возвращяет DTO обьект по id опроса */
      public static function getPollById(int $id) 
      {
            $poll = Poll::findOne($id);
            $pollOptions = PollOptions::find()->where(['poll_id' => $id])->all(); //
            $poll = PollService::getPollObj($poll);

            foreach ($pollOptions as $option) { // добавляем все его варианты ответа
                  array_push($poll->poll_options, 
                  [
                        'option_id' => $option->option_id,
                        'option_title' => $option->option_title,
                        'votes' => $option->votes
                  ]);
            }
            return $poll;
      }

      /* Метод добавляет новый опрос */
      public static function createPoll(PollDto $pollDTO)
      {
            if ($pollDTO->poll_options == [])
            {
                  throw new UserException("poll oprions is null!!");
            }
            $poll = new Poll();
            $poll->poll_name = $pollDTO->poll_name;
            $poll->poll_text = $pollDTO->poll_text;
            $poll->save();

            $pollId = $poll->getPrimaryKey();

            foreach ($pollDTO->poll_options as $option) { // создаем все варианты ответов опроса
                  $optionClass = new PollOptions();
                  $optionClass->poll_id = $pollId;
                  $optionClass->option_title = $option;
                  $optionClass->save();
            }
            if (!SessionService::isSet('polls')) { //записываем в сессию id созданного опроса
                  SessionService::setVariable('polls', array());
            }
            SessionService::addInArrayValue('polls', $pollId);
      }

      /* обновляет опрос, также проверяет наличие у пользователя прав доступа (через сессию) */
      public static function updatePoll(int $pollId, PollDto $pollDTO)
      {
            if (!SessionService::isSet('polls') || !SessionService::isInArray('polls', $pollId)) //проверка доступа
            {
                  throw new UserException('you don`t have permission');
            }
            $poll = Poll::findOne($pollId);

            $poll->poll_name = $pollDTO->poll_name;
            $poll->poll_text = $pollDTO->poll_text;
            $poll->save();
      }

      /* удаляет опрос, также проверяет наличие у пользователя прав доступа (через сессию) */
      public static function deletePoll(int $pollId)
      {
            if (!SessionService::isSet('polls') || !SessionService::isInArray('polls', $pollId)) //проверка доступа
            {
                  throw new UserException('you don`t have permission');
            }
            $poll = Poll::findOne($pollId);
            $poll->delete();
      }

      public static function getUserPolls()
      {
            if (!SessionService::isSet('polls')) {
                  throw new UserException('you dosen`t have polls');
            }
            $pollsId = SessionService::openSession()->get('polls');

            $polls = array();
            foreach ($pollsId as $pollId) {
                  $polls[] = PollService::getPollById($pollId);
            }
            return $polls;
      }

      /* МЕТОДЫ МАНИПУЛЯЦИИ ВАРИАНТАМИ ОТВЕТОВ */

      /* по id варианта ответа добавляет колличетво голосов */
      public static function VoteOne(int $option_id)
      {

            $pollOption = PollOptions::findOne($option_id);
            $pollId = $pollOption->poll_id;

            if(!SessionService::isSet('voted_poll_id')) // если еще нет голосов у пользователя создаем пустой массив
            {
                  SessionService::setVariable('voted_poll_id', array());
            }
            if(! SessionService::isInArray('voted_poll_id', $pollId)) // если еще нет данного опроса в списке отвеченных начисляем голос и записываем в сессию 
            {
                  $pollOption->votes += 1;
                  $pollOption->save();

                  SessionService::addInArrayValue('voted_poll_id', $pollId);
            }
            else{ // выдает эксепшн, если пользователь уже голосовал
                  throw new UserException("Уже проголосовал");
            }
            
      }

      /* ВСПОМОГАТЕЛЬНЫЕ МЕТОДЫ */

      /* метод достает из json обьекта Dto опроса */
      public static function getPollFromJson($json)
      {
            if (!isset($json->poll_id)) {
                  $json->poll_id = null;
            }
            if (!isset($json->poll_options)) {
                  $json->poll_options = [];
            }
            return new PollDto( //достаем из JSON обьекта данные для DTO и возвращаем их 
                  $json->poll_id,
                  $json->poll_name,
                  $json->poll_text,
                  $json->poll_options,
            );
      }
}
