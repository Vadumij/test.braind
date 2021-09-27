<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Тестовое задание для Braind - №2</title>
</head>
<body>
<form action="" method="post">
    <p>Количество ошибок<input name="errors" value="<?echo (int)$_POST['errors']?>"></p>
    <p>Количество предупреждений<input name="warnings" value="<?echo (int)$_POST['warnings']?>"></p>
    <p><input type="submit" value="Узнать"> за какое количество коммитов можно исправить все ошибки и предупреждения</p>
    <?if(is_numeric((int)$_POST['errors']) && is_numeric((int)$_POST['warnings']))
    {
        $errors = new ErrorsAndWornings($_POST['errors'], $_POST['warnings']);
        echo "Количество коммитов: " . $errors->getCountStep();
    }
    ?>
</form>
</body>
</html>
<?
class ErrorsAndWornings
{

    private $cErrors;
    private $cWarnings;
    private $countStep;

    /**
     * @param $cErrors
     * @param $cWarnings
     * 1. 1E -> 1E - исправляем 1 критическую ошибку -> получаем 1 критическую ошибку
     * 2. 1W -> 2W - исправляем 1 предупреждение -> получаем 2 предупреждения
     * 3. 2W -> 1E - исправляем 2 предупреждения -> получаем 1 критическую ошибку
     * 4. 2E -> null исправляем 2 критические ошибки -> ничего нового не получаем.
     */
    public function __construct($cErrors, $cWarnings)
    {
        $this->countStep = 0;
        $this->cErrors = $cErrors;
        $this->cWarnings = $cWarnings;

        if($this->checkInputData() === true)
        {
            if($this->cWarnings == 0 && $this->cErrors % 2 != 0)
            {
                $this->countStep = -1;
            }
            else
            {
                /**
                 * Если у нас чётное количество предупреждений
                 */
                if($this->cWarnings % 2 == 0){

                    /**
                     * и если при исправлении этих предупреждений будет получено чётное количество ошибок,
                     * то нам нужно исправить одно предупреждение, для получения ещё двух предупреждений.
                     */
                    $tmpCErrors = $this->fixTwoWarnings((int)($this->cWarnings / 2), true, true);//Получаем количество ошибок, которое будет получено при исправлении всех предупреждений

                    if($tmpCErrors % 2 != 0)
                    {
                        $this->fixOneWarnings(1);//исправляем одно предупреждение, чтобы получить 2 предупреждения.
                                                        // Это пригодится для получения чётного количество ошибок при исправлении 2-х предупреждений.
                    }
                    /**
                     * В дальнейшем это действие(которое было проделано выше) позволит уйти от ситуации, когда нам не хватит одного предупреждения для получения чётного количества ошибок.
                     * А чётное количество ошибок нам необходимо для исправления всех ошибок.
                     */
                }

                $this->fixTwoWarnings((int)($this->cWarnings / 2));
                $this->fixTwoErrors((int)($this->cErrors / 2));

                /**
                 * Если у нас в итоге осталась 1 ошибка и 0 предупреждений, то в этой ситуации невозможно исправить все ошибки и предупреждения
                 */
                if($this->cErrors == 1 && $this->cWarnings == 0)
                {
                    $this->countStep = -1;
                }
                /**
                 * Иначе, если у нас 0 ошибок и 1 предупреждение, то нам нуэно будет 6 шагов для избавления и от ошибок и от предупреждений
                 */
                elseif ($this->cErrors == 0 && $this->cWarnings == 1)
                {
                    $this->countStep += 6;
                }
                /**
                 * иначе, если у нас осталась 1 ошибка и 1 предупреждение, то нам нужно будет сделать 3 шага для избавления от всех ошибок и предупреждений.
                 */
                elseif ($this->cErrors == 1 && $this->cWarnings == 1)
                {
                    $this->countStep += 3;
                }
            }
        }
        else
        {
            echo 'Неверные входные данные';
        }
    }

    /**
     * Функция получения количества сделанных коммитов для исправления всех ошибок и предупреждений
     */
    public function getCountStep()
    {
        return $this->countStep;
    }

    /**
     * Исправляем 1 предупреждения, получаем 2 предупреждения
     */
    private function fixOneWarnings($count)
    {
        for($i = 0; $i < $count; $i++)
        {
            $this->countStep += 1;
            $this->cWarnings -= 1;
            $this->cWarnings += 2;
        }
    }

    /**
     * Исправляем 2 предупреждения, получаем 1 ошибку
     * @param $count
     * @param bool $isNotEdit - если true, то никакие параметры не будут изменены (ни количество коммитов, ни количество ошибок и предупреждений)
     * @param bool $isReturnErrorsCount - если true, то будет возвращено количество ошибок, которое осталось после работы этой функции.
     */
    private function fixTwoWarnings($count, $isNotEdit = false, $isReturnErrorsCount = false)
    {
        $tmpCountStep = $this->countStep;
        $tmpCWarnings = $this->cWarnings;
        $tmpCErrors = $this->cErrors;

        for($i = 0; $i < $count; $i++)
        {
            $tmpCountStep += 1;
            $tmpCWarnings -= 2;
            $tmpCErrors += 1;
        }

        if($isNotEdit === false)
        {
            $this->countStep = $tmpCountStep;
            $this->cWarnings = $tmpCWarnings;
            $this->cErrors = $tmpCErrors;
        }

        if($isReturnErrorsCount === true)
        {
            return $tmpCErrors;
        }
    }

    /**
     * Исправляем 2 ошибки, ничего не получаем
     */
    private function fixTwoErrors($count)
    {
        for($i = 0; $i < $count; $i++)
        {
            $this->countStep += 1;
            $this->cErrors -= 2;
        }
    }

    /**
     * Проверка того, что входное количество ошибок и предупреждений соответствует условию задачи:
     * N(количество ошибок) >= 0
     * M(количество предупреждений) >= 0 и <= 1000
     */
    private function checkInputData()
    {
        $N = $this->cErrors;
        $M = $this->cWarnings;

        if($N >= 0 && $M >= 0 && $M <= 1000)
        {
            return true;
        }

        return false;
    }
}