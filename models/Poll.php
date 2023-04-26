<?php

namespace app\models;

use yii\db\ActiveRecord;

class Poll extends ActiveRecord
{
      public static function tableName()
      {
            return "{{poll}}";
      }
}
