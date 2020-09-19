<?php
/**
 * Settings Russian Lexicon Entries
 */

$_lang['area_lms.main'] = 'Основные';
$_lang['area_lms.course'] = 'Курс';
$_lang['area_lms.module'] = 'Модуль';
$_lang['area_lms.test'] = 'Тест';

$_lang['setting_lms.frontend_css'] = 'Стили фронтенда';
$_lang['setting_lms.frontend_css_desc'] = 'Путь к файлу со стилями. Если вы хотите использовать собственные стили - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';
$_lang['setting_lms.frontend_js'] = 'Скрипты фронтенда';
$_lang['setting_lms.frontend_js_desc'] = 'Путь к файлу со скриптами. Если вы хотите использовать собственные скрипты - укажите путь к ним здесь, или очистите параметр и загрузите их вручную через шаблон сайта.';

$_lang['setting_lms.managers_group'] = 'Группа менеджеров компании';
$_lang['setting_lms.managers_group_desc'] = 'Пользователи в этой группе являются менеджерами компании.';
$_lang['setting_lms.profession_role_num'] = 'Ранг роли профессии';
$_lang['setting_lms.profession_role_num_desc'] = 'Роли с этим рангом будут считаться профессиями.';
$_lang['setting_lms.date_format'] = 'Формат даты';
$_lang['setting_lms.date_format_desc'] = 'Формат вывода даты в оформлении модулей.';
$_lang['setting_lms.default_module_template'] = 'Шаблон для новых модулей';
$_lang['setting_lms.default_module_template_desc'] = 'Шаблон "по умолчанию" для новых модулей.';
$_lang['setting_lms.default_test_template'] = 'Шаблон для новых тестов';
$_lang['setting_lms.default_test_template_desc'] = 'Шаблон "по умолчанию" для новых тестов.';
$_lang['setting_lms.module_isfolder_force'] = 'Все модули - контейнеры';
$_lang['setting_lms.module_isfolder_force_desc'] = 'Обязательное указание параметра "isfolder" у модулей';
$_lang['setting_lms.module_hidemenu_force'] = 'Не показывать модули в меню';
$_lang['setting_lms.module_hidemenu_force_desc'] = 'Обязательное указание параметра "hidemenu" у модулей';
$_lang['setting_lms.module_show_in_tree_default'] = 'Показывать в дереве по умолчанию';
$_lang['setting_lms.module_show_in_tree_default_desc'] = 'Включите эту опцию, чтобы все создаваемые модули были видны в дереве ресурсов.';
$_lang['setting_lms.course_content_default']  = 'Содержимое курса по умолчанию';
$_lang['setting_lms.course_content_default_desc'] = 'Здесь вы можете указать контент вновь создаваемого курса. По умолчанию установен вывод дочерних модулей и тестов.';

$_lang['setting_lms.enable_editor'] = 'Редактор "markItUp"';
$_lang['setting_lms.enable_editor_desc'] = 'Эта настройка активирует редактор "markItUp" на фронтенде, для удобной работы с модулями и комментариями.';
$_lang['setting_lms.editor_config.module'] = 'Настройки редактора модулей';
$_lang['setting_lms.editor_config.module_desc'] = 'Массив, закодированный в JSON для передачи в "markItUp". Подробности тут - http://markitup.jaysalvat.com/documentation/';
$_lang['setting_lms.editor_config.comment'] = 'Настройки редактора комментариев';
$_lang['setting_lms.editor_config.comment_desc'] = 'Массив, закодированный в JSON для передачи в "markItUp". Подробности тут - http://markitup.jaysalvat.com/documentation/';


$_lang['setting_lms.private_module_page'] = 'Редирект с приватных модулей';
$_lang['setting_lms.private_module_page_desc'] = 'Id существующего ресурса MODX, на который отправлять пользователя, если у него недостаточно прав для просмотра приватного модуля.';
$_lang['setting_lms.unpublished_module_page'] = 'Страница неопубликованных модулей';
$_lang['setting_lms.unpublished_module_page_desc'] = 'Id существующего ресурса MODX, которая будет показана при запросе неопубликованного модуля.';
$_lang['setting_lms.module_max_cut'] = 'Максимальный размер текста без сut';
$_lang['setting_lms.module_max_cut_desc'] = 'Максимальное количество символов без тегов, которые можно сохранить без тега cut.';


//$_lang['setting_lms.course_id_as_alias'] = 'Id раздела как псевдоним';
//$_lang['setting_lms.course_id_as_alias_desc'] = 'Если включено, псевдонимы для дружественных имён разделов не будут генерироваться. Вместо этого будут подставляться их id.';
//$_lang['setting_lms.module_id_as_alias'] = 'Id модуля как псевдоним';
//$_lang['setting_lms.module_id_as_alias_desc'] = 'Если включено, псевдонимы для дружественных имён модулей не будут генерироваться. Вместо этого будут подставляться их id.';

$_lang['setting_mgr_tree_icon_module'] = 'Иконка модуля';
$_lang['setting_mgr_tree_icon_module_desc'] = 'Иконка оформления модуля в дереве ресурсов.';
$_lang['setting_mgr_tree_icon_test'] = 'Иконка модуля';
$_lang['setting_mgr_tree_icon_test_desc'] = 'Иконка оформления модуля в дереве ресурсов.';
$_lang['setting_mgr_tree_icon_course'] = 'Иконка секции модулей';
$_lang['setting_mgr_tree_icon_course_desc'] = 'Иконка оформления секции с модулями в дереве ресурсов.';

$_lang['setting_lms.source_default'] = 'Источник медиа для модулей';
$_lang['setting_lms.source_default_desc'] = 'Выберите источник медиа, который будет использован по умолчанию для загрузки файлов модулей.';

$_lang['lms.source_thumbnail_desc'] = 'Закодированный в JSON массив с параметрами генерации уменьшенной копии изображения.';
$_lang['lms.source_maxUploadWidth_desc'] = 'Максимальная ширина изображения для загрузки. Всё, что больше, будет ужато до этого значения.';
$_lang['lms.source_maxUploadHeight_desc'] = 'Максимальная высота изображения для загрузки. Всё, что больше, будет ужато до этого значения.';
$_lang['lms.source_maxUploadSize_desc'] = 'Максимальный размер загружаемых изображений (в байтах).';
$_lang['lms.source_imageNameType_desc'] = 'Этот параметр указывает, как нужно переименовать файл при загрузке. Hash - это генерация уникального имени, в зависимости от содержимого файла. Friendly - генерация имени по алгоритму дружественных url страниц сайта (они управляются системными настройками).';

