<?php
/**
 * Default English Lexicon Entries for LMS
 */

include_once('setting.inc.php');

$_lang['lms'] = 'LMS';
$_lang['comments'] = 'Comments';
$_lang['threads'] = 'Comments threads';
$_lang['authors'] = 'Authors';
$_lang['lms_course'] = 'Cource with lms';
$_lang['ticket'] = 'Ticket';
$_lang['module_all'] = 'All lms';
$_lang['lms_menu_desc'] = 'Learning management system';
$_lang['comments_all'] = 'All comments';

$_lang['lms_course_create_here'] = 'Cource with lms';
$_lang['lms_course_new'] = 'New course';
$_lang['lms_course_management'] = 'Cource management';
$_lang['lms_course_duplicate'] = 'Duplicate course';
$_lang['lms_course_unpublish'] = 'Unpublish course';
$_lang['lms_course_publish'] = 'Publish course';
$_lang['lms_course_undelete'] = 'Undelete course';
$_lang['lms_course_delete'] = 'Delete course';
$_lang['lms_course_view'] = 'View on site';

$_lang['lms_course_settings'] = 'Settings';
$_lang['lms_course_tab_main'] = 'Main';

$_lang['lms_course_tab_lms'] = 'Children lms';
$_lang['lms_course_tab_lms_intro'] = 'All settings on this page apply only to new lms.';
$_lang['lms_course_settings_template'] = 'The template of children';
$_lang['lms_course_settings_template_desc'] = 'Select the template that will be assigned to all new lms that are created in this course. If template is not specified, it will be taken from the system settings <b>lms.default_module_template</b>.';
$_lang['lms_course_settings_uri'] = 'URI scheme';
$_lang['lms_course_settings_uri_desc'] = 'You can use <b>%y</b> - the year in two digits, <b>%m</b> is the month <b>%d</b> - the day <b>%alias</b> - alias <b>%id</b> - the identifier and <b>%ext</b> - the document extension.';
$_lang['lms_course_settings_show_in_tree'] = 'Display in the tree';
$_lang['lms_course_settings_show_in_tree_desc'] = 'default lms are not shown in the document tree, to reduce the load on the admin panel, but you can enable it for new documents.';
$_lang['lms_course_settings_hidemenu'] = 'Hide in menu';
$_lang['lms_course_settings_hidemenu_desc'] = 'You can specify configuration display the new ticket in the menu.';

$_lang['course_err_publish'] = 'Курс можно будет опубликовать после создания теста.';

$_lang['module_create_here'] = 'Create module';

$_lang['lms_message_close_all'] = 'close all';
$_lang['lms_err_unknown'] = 'An unknown error occurred.';
$_lang['module_err_id'] = 'The ticket with specified id = [[+id]] not found.';
$_lang['module_err_wrong_user'] = 'You trying to update a ticket that is not yours.';
$_lang['module_err_no_auth'] = 'You need to login to create a ticket.';
$_lang['module_err_wrong_parent'] = 'Invalid course for this ticket was specified.';
$_lang['module_err_wrong_resource'] = 'Wrong ticket specified.';
$_lang['module_err_wrong_thread'] = 'Wrong comments thread specified.';
$_lang['module_err_wrong_course'] = 'Wrong lms course specified.';
$_lang['module_err_access_denied'] = 'Access denied';
$_lang['module_err_form'] = 'Form contains errors. Please, fix it.';
$_lang['module_err_deleted_comment'] = 'You trying to edit the deleted comment.';
$_lang['module_err_unpublished_comment'] = 'This comment was not published.';
$_lang['module_err_ticket'] = 'The specified ticket does not exist.';
$_lang['module_err_vote_own'] = 'You cannot vote for your ticket.';
$_lang['module_err_vote_already'] = 'You have already voted for this ticket.';
$_lang['module_err_empty'] = 'You forgot to write the text of the ticket.';
$_lang['module_err_publish'] = 'You are not allowed to publish lms.';
$_lang['module_err_cut'] = 'The length of text is [[+length]] symbols. You must specify tag &lt;cut/&gt if text longer than [[+max_cut]] symbols.';
$_lang['module_unpublished_comment'] = 'Your comment will be published after moderation.';
$_lang['permission_denied'] = 'You do not have permissions for this action.';
$_lang['field_required'] = 'This field is required.';
$_lang['module_clear'] = 'Clear';

$_lang['module_comments_intro'] = 'Here are comments from the entire site.';
$_lang['module_comment_deleted_text'] = 'This comment was deleted.';
$_lang['module_comment_remove_confirm'] = 'Are you sure you want to permanently remove <b>comments thread</b>, starting with this? This operation is irreversible!';

$_lang['module_lms_intro'] = 'Here you can find the lms from the whole site.';
$_lang['module_publishedon'] = 'Published On';
$_lang['module_pagetitle'] = 'Title';
$_lang['module_parent'] = 'Cource';
$_lang['module_author'] = 'Author';
$_lang['module_delete'] = 'Delete ticket';
$_lang['module_delete_text'] = 'Are you sure you want to delete this ticket?';
$_lang['module_create'] = 'Create ticket?';
$_lang['module_show_in_tree'] = 'Show in the tree';
$_lang['module_show_in_tree_help'] = 'default they are not displayed in the resource tree MODX, so as not to burden him.';
$_lang['module_createdon'] = 'Created On';
$_lang['module_publishedon'] = 'Published On';
$_lang['module_content'] = 'Content';
$_lang['module_publish'] = 'Publish';
$_lang['module_preview'] = 'Preview';
$_lang['module_comments'] = 'Comments';
$_lang['module_actions'] = 'Actions';
$_lang['module_save'] = 'Save';
$_lang['module_draft'] = 'Into drafts';
$_lang['module_open'] = 'Open';
$_lang['module_read_more'] = 'Read more';
$_lang['module_saved'] = 'Saved!';

$_lang['module_date_now'] = 'Just now';
$_lang['module_date_today'] = 'Today at';
$_lang['module_date_yesterday'] = 'Yesterday at';
$_lang['module_date_tomorrow'] = 'Tomorrow at';
$_lang['module_date_minutes_back'] = '["[[+minutes]] minutes ago","[[+minutes]] minutes ago","[[+minutes]] minutes ago"]';
$_lang['module_date_minutes_back_less'] = 'Less than a minute ago';
$_lang['module_date_hours_back'] = '["[[+hours]] hours ago","[[+hours]] hours ago","[[+hours]] hours ago"]';
$_lang['module_date_hours_back_less'] = 'Less than an hour ago';
$_lang['module_date_months'] = '["january","february","march","april","may","june","july","august","september","october","november","december"]';

$_lang['module_file_select'] = 'Select files';
$_lang['module_file_delete'] = 'Delete';
$_lang['module_file_restore'] = 'Restore';
$_lang['module_file_insert'] = 'Insert link';
$_lang['module_err_source_initialize'] = 'Could not initialize media source';
$_lang['module_err_file_ns'] = 'Could not process specified file';
$_lang['module_err_file_ext'] = 'Wrong file extension';
$_lang['module_err_file_save'] = 'Could not upload file';
$_lang['module_err_file_owner'] = 'This file not belongs to you';
$_lang['module_err_file_exists'] = 'File with the same name or content is already exists: "[[+file]]"';
$_lang['module_uploaded_files'] = 'Uploaded files';

$_lang['lms_action_view'] = 'View';
$_lang['lms_action_edit'] = 'Edit';
$_lang['lms_action_publish'] = 'Publish';
$_lang['lms_action_unpublish'] = 'UnPublish';
$_lang['lms_action_delete'] = 'Delete';
$_lang['lms_action_undelete'] = 'Restore';
$_lang['lms_action_remove'] = 'Remove';
$_lang['lms_action_duplicate'] = 'Duplicate';
$_lang['lms_action_open'] = 'Open';
$_lang['lms_action_close'] = 'Close';