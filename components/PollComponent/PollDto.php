<?php

namespace app\components\Pollcomponent;

class PollDto
{
      public ?int $poll_id;
      public ?string $poll_name;
      public ?string $poll_text;
      public array $poll_options = [];

      public function __construct(?int $id, ?string $name, ?string $text, ?array $options)
      {
            $this->poll_id = $id;
            $this->poll_name = $name;
            $this->poll_text = $text;
            $this->poll_options = $options;
      }
}