

CREATE TABLE `conferences` (
  `id` int(11) primary key auto_increment NOT NULL,
  `purpose` varchar(20) NOT NULL DEFAULT 'class',
  `staff_id` int(11) DEFAULT NULL,
  `created_id` int(10) NOT NULL,
  `title` text,
  `date` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `class_id` int(10) DEFAULT NULL,
  `section_id` int(10) DEFAULT NULL,
  `session_id` int(10) NOT NULL,
  `host_video` int(1) NOT NULL DEFAULT '1',
  `client_video` int(1) NOT NULL DEFAULT '1',
  `description` varchar(50) DEFAULT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `return_response` text,
  `api_type` varchar(30) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `conference_staff` (
  `id` int(11) primary key auto_increment NOT NULL,
  `conference_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `conferences_history` (
 `id` int(11) primary key auto_increment NOT NULL,
  `conference_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `total_hit` int(10) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `zoom_settings` (
  `id` int(11) primary key auto_increment NOT NULL,
  `zoom_api_key` varchar(200) DEFAULT NULL,
  `zoom_api_secret` varchar(200) DEFAULT NULL,
  `use_teacher_api` int(1) DEFAULT '1',
  `use_zoom_app` int(1) DEFAULT '1',
  `use_zoom_app_user` int(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `conference_sections` (
  `id` int(11) primary key auto_increment NOT NULL,
  `conference_id` int(11) DEFAULT NULL,
  `cls_section_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)  ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `conferences_history`
  ADD CONSTRAINT `conferences_history_ibfk_1` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conferences_history_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conferences_history_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

ALTER TABLE `conference_sections`
  ADD CONSTRAINT `conference_sections_ibfk_1` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conference_sections_ibfk_2` FOREIGN KEY (`cls_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE;

ALTER TABLE `conferences`
  ADD CONSTRAINT `conferences_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conferences_ibfk_2` FOREIGN KEY (`created_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

ALTER TABLE `conference_staff`
  ADD CONSTRAINT `conference_staff_ibfk_1` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conference_staff_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

INSERT INTO `notification_setting` (`id`, `type`, `is_mail`, `is_sms`, `is_notification`, `display_notification`, `display_sms`, `subject`, `template_id`, `template`,  `variables`, `created_at`) VALUES
(null, 'online_classes', '1', '0', 0, 1, 1, 'Zoom Live Classes', '', 'Dear student, your live class {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute, please do not share the link to any body.', '{{title}} {{date}} {{duration}}', '2021-05-20 09:49:16'),
(null, 'online_meeting', '1', '0', 0, 0, 1, 'Zoom Live Meeting', '', 'Dear staff, your live meeting {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute, please do not share the link to any body.', '{{title}} {{date}} {{duration}} {{employee_id}} {{department}} {{designation}} {{name}} {{contact_no}} {{email}}', '2021-05-20 09:49:38'),
(null, 'zoom_online_classes_start', '1', '0', 0, 1, 1, 'Zoom Live Classes Start', '', 'Dear student, your live class {{title}} has been started  for the duration of {{duration}} minute.', '{{title}} {{date}} {{duration}}', '2021-01-08 11:00:47'),
(null, 'zoom_online_meeting_start', '1', '0', 0, 0, 1, 'Zoom Live Meeting Start', '', 'Dear {{name}},  your live meeting {{title}}  has been started  for the duration of {{duration}} minute.', '{{title}} {{date}} {{duration}} {{employee_id}} {{department}} {{designation}} {{name}} {{contact_no}} {{email}}', '2021-01-08 11:00:47');

ALTER TABLE sch_settings
  ADD `zoom_api_key` varchar(100) DEFAULT NULL after app_logo,
  ADD `zoom_api_secret` varchar(100) DEFAULT NULL after zoom_api_key;
 
ALTER TABLE staff
    ADD `zoom_api_key` varchar(100) DEFAULT NULL after verification_code,
  ADD `zoom_api_secret` varchar(100) DEFAULT NULL after zoom_api_key;


INSERT INTO `permission_group` (`id`, `name`, `short_code`, `is_active`, `system`, `created_at`) VALUES
(500, 'Zoom Live Classes', 'zoom_live_classes', 1, 0, '2020-06-10 13:37:23');

INSERT INTO `permission_category` (`id`, `perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`, `created_at`) VALUES
(5001, 500, 'Setting', 'setting', 1, 0, 1, 0, '2020-06-10 13:39:04'),
(5002, 500, 'Live Classes', 'live_classes', 1, 1, 0, 1, '2020-05-31 15:41:32'),
(5003, 500, 'Live Meeting', 'live_meeting', 1, 1, 0, 1, '2020-06-01 12:41:41'),
(5004, 500, 'Live Meeting Report', 'live_meeting_report', 1, 0, 0, 0, '2020-06-10 05:07:40'),
(5005, 500, 'Live Classes Report', 'live_classes_report', 1, 0, 0, 0, '2020-06-10 06:29:53');

INSERT INTO `permission_student` (`id`, `name`, `short_code`, `system`, `student`, `parent`, `group_id`, `created_at`) VALUES (500, 'Zoom Live Classes', 'live_classes', '0', '1', '1', '500', CURRENT_TIMESTAMP);

INSERT INTO `roles_permissions` (`id`, `role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`, `created_at`) VALUES
(null, 1, 5005, 1, 0, 0, 0, '2020-06-14 12:42:11'),
(null, 2, 5005, 1, 0, 0, 0, '2020-06-14 12:59:50'),
(null, 3, 5004, 1, 0, 0, 0, '2020-06-14 13:03:50'),
(null, 2, 5004, 1, 0, 0, 0, '2020-06-14 13:03:50'),
(null, 1, 5004, 1, 0, 0, 0, '2020-06-14 12:42:11'),
(null, 6, 5003, 1, 0, 0, 0, '2020-06-14 13:05:52'),
(null, 4, 5003, 1, 0, 0, 0, '2020-06-14 13:05:28'),
(null, 3, 5003, 1, 1, 0, 1, '2020-06-14 13:03:50'),
(null, 2, 5003, 1, 1, 0, 1, '2020-06-14 12:59:50'),
(null, 1, 5003, 1, 1, 0, 1, '2020-06-14 12:42:11'),
(null, 2, 5002, 1, 1, 0, 1, '2020-06-14 12:59:50'),
(null, 1, 5002, 1, 1, 0, 1, '2020-06-14 12:42:11'),
(null, 1, 5001, 1, 0, 1, 0, '2020-06-14 12:42:11'),
(null, 2, 5001, 1, 0, 1, 0, '2020-06-14 12:42:11');



INSERT INTO `sidebar_menus` (`id`, `permission_group_id`, `icon`, `menu`, `activate_menu`, `lang_key`, `system_level`, `level`, `sidebar_display`, `access_permissions`, `is_active`, `created_at`) VALUES
(45, 500, 'fa fa-video-camera', 'Zoom Live Classes', 'online_classes', 'online_classes', 0, 10, 1, '(\'setting\', \'can_view\') || (\'live_classes\', \'can_view\')|| (\'live_meeting\', \'can_view\')|| (\'live_meeting_report\', \'can_view\')|| (\'live_classes_report\', \'can_view\')', 1, '2023-01-10 12:49:51');




INSERT INTO `sidebar_sub_menus` (`id`, `sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`, `created_at`) VALUES
(401, 45, 'Live Meeting', NULL, 'live_meeting', 'admin/conference/meeting', 1, '(\'live_meeting\', \'can_view\')', NULL, 'conference', 'meeting', '', 1, '2022-12-22 05:59:38'),
(402, 45, 'Live Classes', NULL, 'live_class', 'admin/conference/timetable', 1, '(\'live_classes\', \'can_view\')', NULL, 'conference', 'timetable', '', 1, '2022-11-15 06:05:27'),
(403, 45, 'Live Class Report', NULL, 'live_classes_report', 'admin/conference/class_report', 1, '(\'live_classes_report\', \'can_view\')', NULL, 'conference', 'class_report', '', 1, '2022-12-22 05:59:38'),
(404, 45, 'Live Meeting Report', NULL, 'live_meeting_report', 'admin/conference/meeting_report', 1, '(\'live_meeting_report\', \'can_view\')', NULL, 'conference', 'meeting_report', '', 1, '2022-11-15 06:05:27'),
(405, 45, 'Setting', NULL, 'setting', 'admin/conference', 1, '(\'setting\', \'can_view\')', NULL, 'conference', 'index', '', 1, '2022-11-15 06:05:27');


