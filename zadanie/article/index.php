<?
/**
 * Собственно исходный, полный текст статьи.
 */
$articleText = "Вчера в Ливнах состоялось официальное открытие обновленного парка Машиностроителей. Об этом на своей странице в Instagram сообщил губернатор Орловской области Андрей Клычков. Благоустройство территории парка стало возможным благодаря победе Ливен во Всероссийском конкурсе «Малые города и исторические поселения». Сумма контракта составила 69 млн рублей.";

/**
 * Создаём объект класса Article. Сам класс описан ниже по коду.
 */
$objArticle = new Article($articleText);

/**
 * Проверяем, если в адресе страницы есть GET параметр full_text и он равен 1, то значит нужно отобразить полный текст статьи
 */
if($_GET['full_text'] == 1)
{
    $articleText = $objArticle->getArticleFullText();//полный текст статьи
    echo $articleText;
}
else//иначе отображаем короткий текст статьи
{
    $articleLink = $objArticle->getUrlForViewFullText();//ссылка на полный текст статьи

    $articlePreview = $objArticle->getArticleShortText();//короткий текст статьи со ссылкой в трёх последних словах
    echo $articlePreview;//Отображаем короткий текст статьи
}

/**
 * Класс для работы со статьёй.
 */
class Article
{
    /**
     * До скольки символом обрезать полный текст статьи для получения короткого текста.
     */
    const LENGTH_SHORT_TEXT = 200;

    /**
     * Количество слов в коротком тексте статьи, которые нужно сделать ссылкой.
     */
    const COUNT_LAST_WORD_LINK = 3;

    /**
     * @var string $articleFullText - полный текст статьи
     */
    private $articleFullText;

    /**
     * Конструктор класса, в который нужно передать полный текст статьи.
     * @param $articleFullText - полный текст статьи.
     */
    public function __construct($articleFullText)
    {
        $this->setArticleFullText($articleFullText);
    }

    /**
     * Функция записи полного текста статьи в переменную $this->articleFullText.
     * Перед записью происходит удаление лишних пробелов.
     * @param $articleFullText - полный текст статьи
     */
    public function setArticleFullText($articleFullText)
    {
        $articleFullText =  trim(preg_replace('/\s+/', ' ', $articleFullText));//удаляем лишние пробелы из текста статьи
        $this->articleFullText = $articleFullText;
    }


    /**
     * Функция получения полного текста статьи из переменной $this->articleFullText
     */
    public function getArticleFullText()
    {
        return $this->articleFullText;
    }

    /**
     * Функция получения короткого текста статьи со ссылкой в трёх последних словах
     */
    public function getArticleShortText()
    {
        /**
         * Слова в коротком тексте статьи, которые нужно сделать ссылкой. По заданию это будут 3 последние слова статьи.
         * Также это количество регулируется константой COUNT_LAST_WORD_LINK
         */
        $textShortLink = '';

        /**
         * Слова в коротком тексте статьи, которые не нужно делать ссылкой.
         */
        $textShortNotLink = '';

        /**
         * Получаем короткий текст статьи обрезая полный текст статьи до 200(как по заданию).
         * Количество символов, из которого будет состоять короткий текст статьи, можно изменить в константе LENGTH_SHORT_TEXT
         */
        $textShort = mb_substr($this->articleFullText, 0, self::LENGTH_SHORT_TEXT - 1);


        /**
         * Разбиваем короткий текст статьи на массив слов. В качестве разделителя выступает пробел.
         */
        $arShortText = explode(' ', $textShort);

        /**
         * Вычисляем номер слова, в коротом тексте статьи, с которого будет начинаться ссылка.
         */
        $numberWordLink = count($arShortText) - self::COUNT_LAST_WORD_LINK;//Номер слова в коротком тексте, с которого слова должны быть ссылкой

        /**
         * В цикле разносив по переменным $textShortNotLink и $textShortLink слова, которые не будут ссылками и слова которые будут ссылками.
         * $textShortNotLink - в эту переменную попадаю слова. которые ну будут ссылками.
         * $textShortLink - в эту переменную попадают слова, которые будут ссылками и к которым в итоге будет приписаны три точки.
         */
        for ($i = 0; $i < count($arShortText); $i++)
        {
            if($i < $numberWordLink)
            {
                $textShortNotLink .= $arShortText[$i] . " ";
            }
            else
            {
                $textShortLink .= $arShortText[$i] . " ";
            }
        }

        /**
         * В переменную $link записываем адресс ссылки, при переходе по которой будет показан полный текст статьи.
         */
        $link = $this->getUrlForViewFullText();

        /**
         * Слова из переменной $textShortLink превращаем в ссылку.
         */
        $textShortLink = '<a href="' . $link . '">' . trim($textShortLink) . '...</a>';

        /**
         * Возвращаем короткий текст стать, который состоит из склеивания двух переменных:
         * $textShortNotLink - переменная, в которой хранятся слова, которые не нужно делать ссылкой на полный текст статьи.
         * $textShortLink - переменная, в которой хранится ссылка, с тремя последними словами короткого текста, при переходе на которую будет отображён полный текст статьи.
         */
        return $textShortNotLink . $textShortLink;
    }

    /**
     * Метод возвращает ссылку для просмотра полного текста статьи
     */
    public function getUrlForViewFullText()
    {
        return '/article/?full_text=1';
    }

}