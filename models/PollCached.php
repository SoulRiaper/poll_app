<?php

namespace app\models;

class PollCached extends \yii\redis\ActiveRecord
{
    public function attributes()
    {
        return['id', 'poll_name', 'poll_text'];
    }
}