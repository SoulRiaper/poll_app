<?php

namespace PollService;

use app\models\Poll;
use app\models\PollOptions;

class PollService
{
      public static function getPollObj($poll): PollDto // принимает обьект из базы данных и позвращает Dto обьект
      {
            $poll = new PollDto( $poll->poll_id , $poll->poll_name, $poll->poll_text, []);
            return $poll;
      }
      public static function getPollById(int $id) // возвращяет DTO обьект по id опроса
      {
            $poll = Poll::findOne($id);
            $pollOptions = PollOptions::find()->where(['poll_id' => $id])->all(); //
            $poll = PollService::getPollObj($poll);

            foreach ($pollOptions as $option) { // добавляем все его варианты ответа
                  array_push($poll->poll_options, 
                  [
                        'option_title' => $option->option_title,
                        'votes' => $option->votes
                  ]);
            }
            return $poll;
      }
      public static function VoteOne(int $option_id) // по id варианта ответа добавляет колличетво голосов
      {
            $pollOption = PollOptions::findOne($option_id);

            $pollOption->votes += 1;
            $pollOption->save();
      }
      public static function unpackPollFromJson($json)
      {
            
      }
}
