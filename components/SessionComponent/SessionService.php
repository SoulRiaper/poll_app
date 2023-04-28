<?PHP

namespace app\components\SessionComponent;
use Yii;

/* Класс предоставляет методы работы с сессиями (поверх Yii::$app_>session) */
class SessionService
{
      /* проверяет наличие переменной */
      public static function isSet(string $sessionVariableName)
      {
            $session = SessionService::openSession();
            return $session->has($sessionVariableName);
      }

      /* проверяет есть ли в выбранной переменной выбранное значение (переменная должна существовать и являтся массивом) */
      public static function isInArray(string $variableName, $value)
      {
            $session = SessionService::openSession();
            return in_array($value , $session[$variableName]);
      }

      /* добавляет новую переменную сессии */
      public static function setVariable(string $sessionVariableName, $value)
      {
            $session = SessionService::openSession();
            $session->set($sessionVariableName, $value);
      }

      /* добавляет в массив переменных сесси новое значение */
      public static function addInArrayValue(string $variableName, $value)
      {
            $session = SessionService::openSession();
            $session[$variableName] = array_merge($session[$variableName], [$value]);
      }

      /* Открывает сессию и возвращает ее обект */
      public static function openSession()
      {
            $session = Yii::$app->session;
            $session->open();
            return $session;
      }
}
