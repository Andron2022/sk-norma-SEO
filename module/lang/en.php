<?php
/*****************
 * RUSSIAN
 * LANGUAGE FILE
 * 15.11.2016
 *
 * added:
 * 14.09.2016 	user|meta|forget_password
 *				user|forget_password
 * 15.11.2016	ordercomment
 * 08.02.2017	
 *
 * у склоняемых существительных можно задать форму
 * $lang['comment'] = array('Отзыв', 'Отзыва', 'Отзывов');
 * в массив передаются значения для слова с числом 1,4 и 5.
 * 
 * можно либо в этом файле переменную задать
 * либо в шаблоне назначить все формы слова
 * {lang word="comment" qty=$page.list_comments|@count}
 * 
 * или просто получить значение языковой переменной в шаблоне
 * {lang key1="order" key2="title" key3="meta"}
 * каждый из ключей - глубина массива
 * 
 ****************/

$lang = array();
$lang['current_language'] = 'en';

$lang['error'] = array(
    '403' => 'Доступ ограничен',
    '429' => 'Слишком много запросов', // Too Many Requests
    '404' => 'The requested URL was not found on this server',
    '503' => 'Сайт временно не доступен',
    'db' => 'Database error!',
	'error' => 'Ошибка',
	'error_404' => 'Страница не найдена'
);

$lang['basket'] = array(
    'title' => 'Корзина',
    'metatitle' => 'Корзина',
    'empty' => 'Корзина пуста!',
);

$lang['currency'] = array(
    'usd' => '$',
    'euro' => '&euro;',
    'rur' => 'руб.',
);

$lang['comment'] = array('Testimonial', 'Testimonials', 'Testimonials');
$lang['ordercomment'] = array('Comment', 'Comments', 'Comments');

$lang['search'] = array(
    'search' => 'Seatch',
    'title' => 'Site search',
    'metatitle' => 'Site search',
    'search_for' => 'Your query:',
    'search_results' => 'Results:',
    'nothing_found' => 'No results found',
    'products' => 'Offers',
    'pubs' => 'News',
    'categs' => 'Pages',
    'empty_query_string' => 'Enter a query to search',
);

$lang['order'] = array(
    'title' => 'Оформление заказа',
    'metatitle' => 'Оформление заказа',
    'content' => 'Заказ №-%s успешно оформлен',
    'payd' => 'Заказ №-%s оплачен',
    'error' => 'Произошла неизвестная ошибка',
    'subject' => 'Заказ №-%s',
    'new' => 'Новый заказ №-%s',
    'details' => 'Подробнее',
    'name' => 'Наименование',
    'qty' => 'Кол-во',
    'price' => 'Цена',
    'status_title' => 'Статус',
    'delivery_title' => 'Cпособ доставки',
    'choose_delivery' => 'Выберите способ доставки',
    'choose_payment' => 'Выберите вариант оплаты',
	'status_new' => 'Новый',
	'payment' => array(
		'title' => 'Оплата заказа',
		'metatitle' => 'Оплата заказа',
		'in_progress' => 'В процессе',
		'cancel' => 'Оплата отменена',
		'fail' => 'Произошла ошибка',
		'success' => 'Заказ успешно оплачен!'
	),
	'status' => array(
		'-10' => 'Ложный',
		'-1' => 'Отказ',
		'0' => 'Новый',
		'1' => 'Обрабатывается',
		'5' => 'Передан на доставку',
		'10' => 'Выполнен'
	),
    'total' => 'Итого',
    'total_all' => 'Всего',
    'in_order' => 'В заказе',
    'fio' => 'Заказчик',
    'phone' => 'Телефон',
	'delivery' => 'Доставка',
    'quick' => array(
        'now' => 'Заказать сейчас',
        'title' => 'Быстрый заказ',
        'name' => 'Имя',
        'phone' => 'Телефон',
        'email' => 'Адрес e-mail',
        'message' => 'Комментарий',
        'name_example' => 'Ваше имя',
        'phone_example' => 'Ваш телефон',
        'email_example' => 'Ваш адрес электронной почты',
        'message_example' => 'Дополнительное сообщение для менеджера',
        'message_delivery' => 'Адрес доставки и ваши комментарии к заказу',
        'button_in_list' => 'Купить',
        'button' => 'Отправить заказ',
        'button_add' => 'Оформить заказ'
    ),
    
);


$lang['spec_pages'] = array(
    'last_pubs' => array(
        'title' => 'Новые публикации',
        'metatitle' => 'Новые публикации',
        'intro' => 'Последние публикации сайта',        
    ),
    'last_products' => array(
        'title' => 'Новые предложения',
        'metatitle' => 'Новые предложения',
        'intro' => 'Последние добавленные предложения сайта',        
    ),
    'new_products' => array(
        'title' => 'Новые предложения',
        'metatitle' => 'Новые предложения',
        'intro' => 'Новинки сайта',        
    ),
    'spec_products' => array(
        'title' => 'Специальные предложения',
        'metatitle' => 'Специальные предложения',
        'intro' => 'Специальные предложения сайта',        
    ),

);

$lang['sitemap'] = array(
    'title' => 'Sitemap',
	'metatitle' => 'Sitemap',
	'empty' => 'No records found'
);

$lang['compare'] = array(
    'title' => 'Сравнение',
	'metatitle' => 'Сравнение выбранных элементов',
    'title_table' => 'Таблица сравнения',
	'metatitle_table' => 'Таблица сравнение',
	'empty' => 'В списке для сравнения пусто'
);


$lang['days'] = array(
    1 => 'Понедельник',
    2 => 'Вторник',
    3 => 'Среда',
    4 => 'Четверг',
    5 => 'Пятница',
    6 => 'Суббота',
    7 => 'Воскресенье'
);

$lang['month'] = array(
    1 => 'Январь',
    2 => 'Февраль',
    3 => 'Март',
    4 => 'Апрель',
    5 => 'Май',
    6 => 'Июнь',
    7 => 'Июль',
    8 => 'Август',
    9 => 'Сентябрь',
    10 => 'Октябрь',
    '11' => 'Ноябрь',
    12 => 'Декабрь',
);

$lang['months'] = array(
    1 => 'Января',
    2 => 'Февраля',
    3 => 'Марта',
    4 => 'Апреля',
    5 => 'Мая',
    6 => 'Июня',
    7 => 'Июля',
    8 => 'Августа',
    9 => 'Сентября',
    10 => 'Октября',
    11 => 'Ноября',
    12 => 'Декабря',
);

$lang['feedback'] = array(
	'title' => array( 
		'contact' => 'Contact us', // контактная форма
		'request' => 'Send a request' // форма запроса
	),
    'view' => array(
        'title' => 'Request №-%s',
        'metatitle' => 'Request №-%s',
        'sentby' => 'Sent by',
        'ticket' => 'Ticket',
        'wait_answer' => 'In work',
        'answer_sent' => 'Answer sent',
    ),
    'new' => array(
        'title' => 'Send a request',
        'metatitle' => 'Send a request',
        'content' => 'Fill form to send a request',
    ),
    'sent' => array(
        'title' => 'The message sent!',
        'metatitle' => 'The message sent',
        'content' => 'Thank you for a message. We will contact you as soon as possible. %s', 
        'body' => 'The request №-%s sent successfully',
        'subject' => 'Request №-%s',
        'new' => 'New request №-%s',
        'from_page' => 'From page',
    ),
    'error' => 'Ups! An error occured. Please try again later.',
    'notify' => 'Notification',
    'phone' => 'Phone',
    'phone_example' => '+44 123 456 789',
    'your_phone' => '+44 123 456 789',
    'name' => 'Name',
	'your_name' => 'Your name',
    'message' => 'Message',
    'message_example' => 'Your message',
    'your_message' => 'Your message',
    'button' => 'Send',
    'ticket_not_found_title' => 'The message not found', 
    'ticket_not_found' => 'The message not found', 
    'email' => 'E-mail',
    'your_email' => 'Your e-mail',
    'quick_title' => 'Quick contact',
    'new_comment' => 'New comment',
    'moderate' => 'Modarate',
    'error_captcha' => 'Enter correct code',
    'empty_captcha' => 'Enter correct code',
	'comments' => 'Comments',
	'date' => 'Date',
	'ticket' => 'Ticket'
);

$lang['words'] = array(
	'or' => 'or',
	'file_downloaded' => 'Файл скачан',
	'added_fb_comment' => 'Новый комментарий к запросу',
	'added_order_comment' => 'Новый комментарий к заказу',
	'added_categ_comment' => 'Новый комментарий к странице',
	'added_pub_comment' => 'Новый комментарий к публикации',
	'added_product_comment' => 'Новый комментарий к товару',
	'added_comment_comment' => 'Новый комментарий к комментарию',
	'unknown_comment' => 'Неизвестный комментарий',
	'home' => 'Home',
	'fotos' => 'Photos',
	'foto' => 'Photo',
	'of' => 'of', // из
	'search' => 'Search', // искать
	'contact_us' => 'Contact us', //
	'sitemap' => 'Sitemap', // карта сайта
	'site_search' => 'Site search', //Поиск по сайту
	'your_request' => '', //Вы искали
	'search_results' => 'search results', //Результаты поиска
	'last_news' => 'Last news', // Последние новости
	'tag' => 'Tag', //tag
	'tags' => 'Tags', //tags
	'socialnets' => 'Social networks', // социальные сети
	'address' => 'Address', // адрес
	'files' => 'Files', // Файлы
	'file' => 'File', // Файл
	'comment' => 'Comment', // Комментарий
	'comments' => 'Comments', // Комментарии
	'site' => 'Web-site', // Сайт
	'of_site' => 'Of web-site', // Сайта
	
);

$lang['wishlist'] = array(
	'title' => 'Избранное',
	'metatitle' => 'Избранное',
	'empty' => 'В избранном ничего не найдено',
	'exists_all' => 'Показать избранные <a href="%s">товары</a> или <a href="%s">публикации</a>'
);


$lang['user'] = array(
	'login' => 'Логин',
	'email' => 'E-mail',
	'password' => 'Пароль',
	'password2' => 'Повторите пароль',
	'password_old' => 'Текущий пароль',
	'signin' => 'Войти',
	'list_orders' => 'Список заказов',
	'fb' => 'Запросы пользователя',
	'title' => 'Авторизация',
	'edit' => 'Редактирование профиля пользователя',
	'register' => 'Регистрация',
	'password_set' => 'Пароль указан при регистрации',
	'password_new' => 'Установлен новый пароль',
	'email_intro' => 'Благодарим Вас за регистрацию',
	'email_subject' => 'Регистрация на сайте',
	'admin_subject' => 'Регистрация на сайте',
	'activate_text' => 'Для активации личного кабинета нажмите на ссылку нижу',
	'changepassword' => 'Изменить пароль',
	'forget_password' => 'Восстановление пароля',
	'meta' => array(
		'title' => 'Авторизация',
		'edit' => 'Редактирование профиля пользователя',
		'changepassword' => 'Изменение пароля',
		'fb' => 'Запросы пользователя',
		'list_orders' => 'Список заказов',
		'register' => 'Регистрация',
		'description' => 'Авторизация',
		'keywords' => 'авторизация', 
		'forget_password' => 'Восстановление пароля',
	),
	'buttons' => array(
		'log_in' => 'Войти',
		'save' => 'Сохранить',
		'changepassword' => 'Изменить пароль',
	),
	'links' => array(
		'lost_password' => 'Забыли пароль?',
		'register' => 'Регистрация',
		'auth' => 'Авторизация',
	),
	'cabinet' => 'Личный кабинет',	
	
	'errors' => array(
		'title' => 'Произошла ошибка',
		'form_closed' => 'Форма устарела',
		'user_not_found' => 'Такой пользователь не найден!',
		
		'login_empty' => 'Укажите имя пользователя (логин)',
		'login_incorrect' => 'Поле логин содержит не корректные символы',
		'email_empty' => 'Укажите адрес электронной почты', 
		'email_incorrect' => 'Адрес электронной почты не корректно заполнен', 
		'passw_length' => 'Пароль должен быть от 4 до 20 символов',
		'passw1_empty' => 'Поле пароль не должно быть пустым',
		'passw1_incorrect' => 'Поле пароль содержит не корректные символы',
		'passw2_empty' => 'Поле повторите пароль не должно быть пустым',
		'passw_wrong' => 'Текущий пароль не подходит',
		'different_passw' => 'Поля пароль и повторите пароль должны быть одинаковыми',
		'login_taken' => 'Логин уже используется',
		'wrong_params' => 'Ошибочные параметры',
		
	),
	
);

$lang['subscription'] = array(
	'title' => 'Рассылка новостей',
	'metatitle' => 'Рассылка новостей сайта',
	'deleted' => 'Ваша подписка на рассылку успешно отменена',
	'added' => 'Вы успешно подписаны на рассылку',
	'or' => 'или',
	'or' => 'или',
	'or' => 'или',
);

$lang['cms'] = array(
	'orgs' => array(
		'list' => 'Список организаций',
		'title' => 'Организации',
		'view' => 'Просмотр организации',
		'own' => 'Свои', 
		'logs' => 'История',
		'edit' => 'Редактирование',
		'delete_impossible' => 'Нельзя удалить организацию, есть связанные объекты',
		'edit_own_no_permitten' => 'У вас не достаточно прав для редактирования своих организаций'
	),
	
	'users' => array(
		'list' => 'Пользователи',
		'title' => 'Пользователи',
		'view' => 'Профиль пользователя',
		'admin' => 'Администраторы', 
		'news' => 'Подписчики', 
		'all' => 'Остальные', 
		'logs' => 'История',
		'edit' => 'Редактирование',
		'delete_impossible' => 'Нельзя удалить пользователя, есть связанные объекты',
		'edit_no_permitten' => 'У вас не достаточно прав для редактирования администратора'
	),
	
	'mess' => array(
		'added' => 'Запись успешно добавлена',
		'updated' => 'Запись успешно обновлена',
		'deleted' => 'Запись успешно удалена'
	),
	
	'categs' => array(
		'list' => 'Список страниц',
		'title' => 'Страницы',
		'view' => 'Страница',
		'admin' => 'Администраторы', 
		'logs' => 'История',
		'edit' => 'Редактирование',
		'delete_impossible' => 'Нельзя удалить, есть связанные объекты',
		'edit_no_permitten' => 'У вас не достаточно прав для редактирования'
	),
	
	'products' => array(
		'list' => 'Список товаров',
		'title' => 'Товары',
		'view' => 'Товар',
		'admin' => 'Администраторы', 
		'logs' => 'История',
		'edit' => 'Редактирование',
		'delete_impossible' => 'Нельзя удалить, есть связанные объекты',
		'edit_no_permitten' => 'У вас не достаточно прав для редактирования'
	),

	'pubs' => array(
		'list' => 'Список публикаций',
		'title' => 'Публикации',
		'view' => 'Публикация',
		'admin' => 'Администраторы', 
		'logs' => 'История',
		'edit' => 'Редактирование',
		'delete_impossible' => 'Нельзя удалить, есть связанные объекты',
		'edit_no_permitten' => 'У вас не достаточно прав для редактирования'
	),

	'comments' => array(
		'list' => 'Список комментариев',
		'title' => 'Комментарии',
		'view' => 'Комментарий',
		'admin' => 'Администраторы', 
		'logs' => 'История',
		'edit' => 'Редактирование',
		'delete_impossible' => 'Нельзя удалить, есть связанные объекты',
		'edit_no_permitten' => 'У вас не достаточно прав для редактирования'
	),
	
);


?>