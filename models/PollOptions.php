<?php

namespace app\models;

use yii\db\ActiveRecord;

class PollOptions extends ActiveRecord
{
      public static function tableName()
      {
            return '{{poll_options}}';
      }
}
