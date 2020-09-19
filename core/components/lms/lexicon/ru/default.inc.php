<?php
/**
 * Default Russian Lexicon Entries for LMS
 */

include_once('setting.inc.php');

$_lang['lms'] = 'Управление обучением';
$_lang['lms_course'] = 'Курс обучения';
$_lang['module'] = 'Модуль обучения';
$_lang['modules'] = 'Модули обучения';
$_lang['module_all'] = 'Все';
$_lang['courses'] = 'Курсы';
$_lang['lms_menu_desc'] = 'Управление обучением';
$_lang['tests'] = 'Тесты';
$_lang['statistic'] = 'Статистика';
$_lang['company'] = 'Компания';
$_lang['companies'] = 'Компании';
$_lang['professions'] = 'Профессии';
$_lang['profession'] = 'Профессия';
$_lang['report'] = 'Отчет';

$_lang['lms_course_create_here'] = 'Курс обучения';
$_lang['lms_course_new'] = 'Новый курс обучения';
$_lang['lms_course_management'] = 'Управление курсом';
$_lang['lms_course_duplicate'] = 'Копировать курс';
$_lang['lms_course_unpublish'] = 'Снять с публикации';
$_lang['lms_course_publish'] = 'Опубликовать курс';
$_lang['lms_course_undelete'] = 'Восстановить курс';
$_lang['lms_course_delete'] = 'Удалить курс';
$_lang['lms_course_view'] = 'Просмотреть на сайте';

$_lang['lms_course_settings'] = 'Настройки раздела';
$_lang['lms_course_tab_main'] = 'Основные';

$_lang['lms_course_tab_lms'] = 'Модули и тесты';
$_lang['lms_course_tab_lms_intro'] = 'Все настройки на этой странице действуют только на новые модули и тесты.';
$_lang['lms_course_settings_template'] = 'Шаблон дочерних документов';
$_lang['lms_course_settings_template_desc'] = 'Выберите шаблон, который будет присвоен всем новым модулям, создаваемым в этом курсе. Если шаблон не указан - он будет взят из системной настройки <b>lms.default_module_template</b>.';
$_lang['lms_course_settings_uri'] = 'Формирование URI';
$_lang['lms_course_settings_uri_desc'] = 'Вы можете использовать <b>%y</b> - год двумя цифрами, <b>%m</b> - месяц, <b>%d</b> - день, <b>%alias</b> - псевдоним, <b>%id</b> - идентификатор и <b>%ext</b> - расширение документа.';
$_lang['lms_course_settings_show_in_tree'] = 'Показывать в дереве';
$_lang['lms_course_settings_show_in_tree_desc'] = 'По умолчанию модули не показываются в дереве документов, чтобы снизить нагрузку на админку, но вы можете включить это для новых документов.';
$_lang['lms_course_settings_hidemenu'] = 'Скрыть в меню';
$_lang['lms_course_settings_hidemenu_desc'] = 'Вы можете указать настройку отображения новых тикетов в меню.';

$_lang['course_err_publish'] = 'Курс можно будет опубликовать после создания теста.';

$_lang['module_create_here'] = 'Создать модуль';

$_lang['lms_err_unknown'] = 'Произошла неизвестная ошибка.';
$_lang['lms_message_close_all'] = 'закрыть все';
$_lang['module_err_id'] = 'Тикет с указанным id = [[+id]] не найден.';
$_lang['module_err_wrong_user'] = 'Вы пытаетесь обновить тикет, который вам не принадлежит.';
$_lang['module_err_no_auth'] = 'Вы должны авторизоваться, чтобы создать тикет.';
$_lang['module_err_wrong_parent'] = 'Указан неверный раздел для тикета.';
$_lang['module_err_wrong_resource'] = 'Указан неверный тикет.';
$_lang['module_err_wrong_thread'] = 'Указана неверная ветвь комментариев.';
$_lang['module_err_wrong_course'] = 'Указана неверная секция тикетов.';
$_lang['module_err_access_denied'] = 'Доступ запрещен.';
$_lang['module_err_form'] = 'В форме содержатся ошибки. Пожалуйста, исправьте их.';
$_lang['module_err_deleted_comment'] = 'Вы пытаетесь отредактировать удалённый комментарий.';
$_lang['module_err_unpublished_comment'] = 'Этот комментарий еще не был опубликован.';
$_lang['module_err_module'] = 'Указанный тикет не существует.';
$_lang['module_err_vote_own'] = 'Вы не можете голосовать за свой тикет.';
$_lang['module_err_vote_already'] = 'Вы уже голосовали за этот тикет.';
$_lang['module_err_empty'] = 'Вы забыли написать текст тикета.';
$_lang['module_err_publish'] = 'Вам запрещено публиковать тикеты.';
$_lang['module_err_cut'] = 'Длина текста [[+length]] символов. Вы должны указать тег &lt;cut/&gt, если текст больше [[+max_cut]] символов.';
$_lang['module_unpublished_comment'] = 'Ваш комментарий будет опубликован после проверки.';
$_lang['permission_denied'] = 'У вас недостаточно прав для этого действия.';
$_lang['field_required'] = 'Это поле обязательно.';
$_lang['module_clear'] = 'Очистить';

$_lang['module_lms_intro'] = 'Здесь собраны все модули обучения.';
$_lang['module_pagetitle'] = 'Заголовок';
$_lang['module_parent'] = 'Курс';
$_lang['module_author'] = 'Автор';
$_lang['module_delete'] = 'Удалить модуль';
$_lang['module_delete_text'] = 'Вы уверены, что хотите удалить этот тикет?';
$_lang['module_create'] = 'Создать модуль';
$_lang['module_file_upload'] = 'Загрузить презентацию';
$_lang['module_show_in_tree'] = 'Показывать в дереве';
$_lang['module_show_in_tree_help'] = 'По умолчанию, тикеты не показываются в дереве ресурсов MODX, чтобы не нагружать его.';
$_lang['module_createdon'] = 'Создан';
$_lang['module_publishedon'] = 'Опубликован';
$_lang['module_content'] = 'Содержимое';
$_lang['module_publish'] = 'Опубликовать';
$_lang['module_preview'] = 'Предпросмотр';
$_lang['module_comments'] = 'Комментарии';
$_lang['module_actions'] = 'Действия';
$_lang['module_save'] = 'Сохранить';
$_lang['module_draft'] = 'В черновики';
$_lang['module_open'] = 'Открыть';
$_lang['module_read_more'] = 'Читать дальше';
$_lang['module_saved'] = 'Сохранено!';

$_lang['test_create_here'] = 'Создать тест';

$_lang['test_no_comments'] = 'На этой странице еще нет комментариев. Вы можете написать первый.';
$_lang['test_err_id'] = 'Тикет с указанным id = [[+id]] не найден.';
$_lang['test_err_wrong_user'] = 'Вы пытаетесь обновить тикет, который вам не принадлежит.';
$_lang['test_err_no_auth'] = 'Вы должны авторизоваться, чтобы создать тикет.';
$_lang['test_err_wrong_parent'] = 'Указан неверный раздел для тикета.';
$_lang['test_err_wrong_resource'] = 'Указан неверный тикет.';
$_lang['test_err_wrong_thread'] = 'Указана неверная ветвь комментариев.';
$_lang['test_err_wrong_course'] = 'Указана неверная секция тикетов.';
$_lang['test_err_access_denied'] = 'Доступ запрещен.';
$_lang['test_err_form'] = 'В форме содержатся ошибки. Пожалуйста, исправьте их.';
$_lang['test_err_deleted_comment'] = 'Вы пытаетесь отредактировать удалённый комментарий.';
$_lang['test_err_unpublished_comment'] = 'Этот комментарий еще не был опубликован.';
$_lang['test_err_test'] = 'Указанный тикет не существует.';
$_lang['test_err_vote_own'] = 'Вы не можете голосовать за свой тикет.';
$_lang['test_err_vote_already'] = 'Вы уже голосовали за этот тикет.';
$_lang['test_err_empty'] = 'Вы забыли написать текст тикета.';
$_lang['test_err_publish'] = 'Вам запрещено публиковать тикеты.';
$_lang['test_err_cut'] = 'Длина текста [[+length]] символов. Вы должны указать тег &lt;cut/&gt, если текст больше [[+max_cut]] символов.';
$_lang['test_unpublished_comment'] = 'Ваш комментарий будет опубликован после проверки.';
$_lang['test_clear'] = 'Очистить';

$_lang['test_lms_intro'] = 'Здесь собраны все тесты.';
$_lang['test_pagetitle'] = 'Заголовок';
$_lang['test_parent'] = 'Курс';
$_lang['test_author'] = 'Автор';
$_lang['test_delete'] = 'Удалить тест';
$_lang['test_delete_text'] = 'Вы уверены, что хотите удалить этот тикет?';
$_lang['test_create'] = 'Создать тест';
$_lang['test_file_upload'] = 'Загрузить презентацию';
$_lang['test_disable_jevix'] = 'Отключить Jevix';
$_lang['test_disable_jevix_help'] = 'Выводить контент страницы без фильтрации сниппетом Jevix. <b>Очень опасно</b>, так как любой пользователь, создающий страницу, сможет атаковать ваш сайт (XSS, LFI и т.п.)';
$_lang['test_process_tags'] = 'Выполнять теги MODX';
$_lang['test_process_tags_help'] = 'По умолчанию, теги в квадратных скобках выводятся как есть, без обработки парсером. Если включите, на этой странице будут запускаться сниппеты, чанки и т.д.';
$_lang['test_private'] = 'Закрытый тикет';
$_lang['test_private_help'] = 'Если включено, пользователю требуется разрешение "test_view_private" для просмотра этого тикета.';
$_lang['test_show_in_tree'] = 'Показывать в дереве';
$_lang['test_show_in_tree_help'] = 'По умолчанию, тикеты не показываются в дереве ресурсов MODX, чтобы не нагружать его.';
$_lang['test_createdon'] = 'Создан';
$_lang['test_publishedon'] = 'Опубликован';
$_lang['test_content'] = 'Содержимое';
$_lang['test_publish'] = 'Опубликовать';
$_lang['test_preview'] = 'Предпросмотр';
$_lang['test_comments'] = 'Комментарии';
$_lang['test_actions'] = 'Действия';
$_lang['test_save'] = 'Сохранить';
$_lang['test_draft'] = 'В черновики';
$_lang['test_open'] = 'Открыть';
$_lang['test_read_more'] = 'Читать дальше';
$_lang['test_saved'] = 'Сохранено!';

$_lang['statistic_lms_intro'] = 'Здесь можно просмотреть статистику по любому студенту.';
$_lang['statistic_object'] = 'Объект';
$_lang['statistic_date'] = 'Дата изменения';
$_lang['statistic_progress'] = 'Результат';
$_lang['statistic_finished'] = 'Завершено';
$_lang['statistic_actions'] = 'Действия';
$_lang['statistic_finished_1'] = 'Сдал';
$_lang['statistic_finished_0'] = 'Не сдал';

$_lang['company_lms_intro'] = 'Раздел для управления компаниями. Создать и добавить пользователя в компанию можно в разделе по <a href="?a=security/user">ссылке</a>.';
$_lang['company_name'] = 'Название';
$_lang['company_description'] = 'Описание компании';
$_lang['company_actions'] = 'Действия';
$_lang['company_create'] = 'Добавить компанию';
$_lang['course_access_remove'] = 'Отключить доступ к курсу';
$_lang['course_access_remove_confirm'] = 'Хотите отключить доступ к этому курсу для компании?';
$_lang['company_name_error'] = 'Имя компании должно содержать только латинские символы, пробелы и тире (-)';

$_lang['profession_lms_intro'] = 'Раздел для управления профессиями.';
$_lang['profession_create'] = 'Добавить профессию';
$_lang['profession_name'] = 'Название профессии';
$_lang['profession_description'] = 'Описание профессии';

$_lang['report_lms_intro'] = 'В разделе собрана информация о доступности курсов компаниям и студентам.';
$_lang['report_students_quantity'] = 'Кол-во студентов';
$_lang['report_roles'] = 'Для профессий';

$_lang['module_date_now'] = 'Только что';
$_lang['module_date_today'] = 'Сегодня в';
$_lang['module_date_yesterday'] = 'Вчера в';
$_lang['module_date_tomorrow'] = 'Завтра в';
$_lang['module_date_minutes_back'] = '["[[+minutes]] минута назад","[[+minutes]] минуты назад","[[+minutes]] минут назад"]';
$_lang['module_date_minutes_back_less'] = 'меньше минуты назад';
$_lang['module_date_hours_back'] = '["[[+hours]] час назад","[[+hours]] часа назад","[[+hours]] часов назад"]';
$_lang['module_date_hours_back_less'] = 'меньше часа назад';
$_lang['module_date_months'] = '["января","февраля","марта","апреля","мая","июня","июля","августа","сентября","октября","ноября","декабря"]';

$_lang['module_file_select'] = 'Выберите файлы';
$_lang['module_file_delete'] = 'Удалить';
$_lang['module_file_restore'] = 'Восстановить';
$_lang['module_file_insert'] = 'Вставить ссылку';
$_lang['module_err_source_initialize'] = 'Не могу инициализировать хранилище файлов';
$_lang['module_err_file_ns'] = 'Не могу обработать указанный файл';
$_lang['module_err_file_ext'] = 'Неправильно расширение файла';
$_lang['module_err_file_save'] = 'Не могу загрузить файл';
$_lang['module_err_file_owner'] = 'Этот файл вам принадлежит не вам';
$_lang['module_err_file_exists'] = 'Файл с таким именем или содержимым уже существует: "[[+file]]"';
$_lang['module_uploaded_files'] = 'Загруженные файлы';
$_lang['file_uploaded_succesfully'] = 'Файл успешно сохранен. Перезагрузите страницу для применения настроек.';
$_lang['company_err_file_ns'] = 'Ошибка на стороне клиента';
$_lang['company_err_file_ext'] = 'Недопустимый тип файла';
$_lang['company_file_removed_success'] = 'Файл успешно удален';

$_lang['test_file_upload'] = 'Загрузить тест';

$_lang['lms_action_view'] = 'Просмотреть';
$_lang['lms_action_edit'] = 'Изменить';
$_lang['lms_action_publish'] = 'Опубликовать';
$_lang['lms_action_unpublish'] = 'Снять с публикации';
$_lang['lms_action_delete'] = 'Удалить';
$_lang['lms_action_undelete'] = 'Восстановить';
$_lang['lms_action_remove'] = 'Уничтожить';
$_lang['lms_action_duplicate'] = 'Копировать';
$_lang['lms_action_open'] = 'Открыть';
$_lang['lms_action_close'] = 'Закрыть';
$_lang['lms_file_add'] = 'Добавить';

$_lang['student_processed'] = 'Учетная запись успешно обновлена';
$_lang['student_added'] = 'Учетная запись создана';