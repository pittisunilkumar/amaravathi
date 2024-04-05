-- Smart School Gmeet Live Classes DB
-- Version 3.0
-- https://smart-school.in
-- https://qdocs.in
-- Tables added: 5

-- --------------------------------------------------------


--
-- Table structure for table `gmeet`
--

CREATE TABLE `gmeet` (
  `id` int(11) NOT NULL,
  `purpose` varchar(20) NOT NULL DEFAULT 'class',
  `staff_id` int(11) DEFAULT NULL,
  `created_id` int(10) NOT NULL,
  `title` text,
  `date` datetime DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'manual',
  `api_data` text,
  `duration` int(11) DEFAULT NULL,
  `subject` varchar(50) DEFAULT NULL,
  `url` text NOT NULL,
  `session_id` int(10) NOT NULL,
  `description` varchar(50) DEFAULT NULL,
  `timezone` varchar(100) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
-- Table structure for table `gmeet_history`
--

CREATE TABLE `gmeet_history` (
  `id` int(11) NOT NULL,
  `gmeet_id` int(11) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `total_hit` int(10) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `gmeet_sections`
--

CREATE TABLE `gmeet_sections` (
  `id` int(11) NOT NULL,
  `gmeet_id` int(11) NOT NULL,
  `cls_section_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
-- Table structure for table `gmeet_settings`
--

CREATE TABLE `gmeet_settings` (
  `id` int(11) NOT NULL,
  `api_key` varchar(200) DEFAULT NULL,
  `api_secret` varchar(200) DEFAULT NULL,
  `use_api` int(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `gmeet_staff`
--

CREATE TABLE `gmeet_staff` (
  `id` int(11) NOT NULL,
  `gmeet_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `notification_setting` (`id`, `type`, `is_mail`, `is_sms`, `is_notification`, `display_notification`, `display_sms`, `subject`, `template_id`, `template`, `variables`, `created_at`) VALUES

(null, 'gmeet_online_classes', '1', '0', 0, 1, 1, 'Gmeet Live Classes', '', 'Dear student, your live class {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute, please do not share the link to any body.', '{{title}} {{date}} {{duration}}', '2021-05-20 09:47:22'),
(null, 'gmeet_online_meeting', '1', '0', 0, 0, 1, 'Gmeet Live Meeting', '', 'Dear staff, your live meeting {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute, please do not share the link to any body.', '{{title}} {{date}} {{duration}} {{employee_id}} {{department}} {{designation}} {{name}} {{contact_no}} {{email}}', '2021-05-20 09:47:09'),
(null, 'gmeet_online_classes_start', '1', '0', 0, 1, 1, 'Gmeet  Live Classes Start', '', 'Dear student, your live class {{title}} has been started  for the duration of {{duration}} minute.', '{{title}} {{date}} {{duration}}', '2021-05-20 09:47:41'),
(null, 'gmeet_online_meeting_start', '1', '0', 0, 0, 1, 'Gmeet Live Meeting Start', '', 'Dear {{name}},  your live meeting {{title}} has been started  for the duration of {{duration}} minute.', '{{title}} {{date}} {{duration}} {{employee_id}} {{department}} {{designation}} {{name}} {{contact_no}} {{email}}', '2021-05-20 09:47:31');

-- --------------------------------------------------------


INSERT INTO `permission_group` (`id`, `name`, `short_code`, `is_active`, `system`, `created_at`) VALUES
(600, 'Gmeet Live Classes', 'gmeet_live_classes', 1, 0, '2020-11-12 13:37:03');

-- --------------------------------------------------------


INSERT INTO `permission_category` (`id`, `perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`, `created_at`) VALUES
(6001, 600, 'Live Classes', 'gmeet_live_classes', 1, 1, 0, 1, '2020-09-22 10:03:29'),
(6002, 600, 'Live Meeting', 'gmeet_live_meeting', 1, 1, 0, 1, '2020-09-22 10:03:44'),
(6003, 600, 'Live Meeting Report', 'gmeet_live_meeting_report', 1, 0, 0, 0, '2020-09-22 10:03:57'),
(6004, 600, 'Live Classes Report', 'gmeet_live_classes_report', 1, 0, 0, 0, '2020-09-22 10:04:08'),
(6005, 600, 'Setting', 'gmeet_setting', '1', '0', '1', '0', '2020-09-22 10:04:08');

INSERT INTO `permission_student` (`id`, `name`, `short_code`, `system`, `student`, `parent`, `group_id`, `created_at`) VALUES
(null, 'Gmeet Live Classes ', 'gmeet_live_classes', 0, 1, 1, 600, '2020-11-12 13:37:03');


INSERT INTO `roles_permissions` (`id`, `role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`, `created_at`) VALUES
(null, 2, 6001, 1, 1, 0, 1, '2021-01-29 09:31:16'),
(null, 1, 6001, 1, 1, 0, 1, '2020-09-23 12:59:22'),
(null, 1, 6002, 1, 1, 0, 1, '2020-09-23 13:01:52'),
(null, 1, 6003, 1, 0, 0, 0, '2020-09-23 13:02:06'),
(null, 1, 6004, 1, 0, 0, 0, '2020-09-23 13:02:33'),
(null, 1, 6005, 1, 0, 1, 0, '2021-01-29 07:45:05'),
(null, 2, 6005, 1, 0, 1, 0, '2021-01-29 11:08:20'),
(null, 2, 6002, 1, 1, 0, 1, '2021-01-29 07:58:48'),
(null, 3, 6002, 1, 1, 0, 1, '2021-01-29 07:26:08'),
(null, 3, 6003, 1, 0, 0, 0, '2021-01-29 07:26:08'),
(null, 3, 6005, 1, 0, 1, 0, '2021-01-29 07:26:08'),
(null, 4, 6001, 1, 1, 0, 1, '2021-01-29 07:26:53'),
(null, 4, 6002, 1, 1, 0, 1, '2021-01-29 07:26:53'),
(null, 6, 6001, 1, 1, 0, 1, '2021-01-29 07:27:32'),
(null, 6, 6002, 1, 1, 0, 1, '2021-01-29 07:27:32'),
(null, 6, 6005, 1, 0, 1, 0, '2021-01-29 07:27:32'),
(null, 2, 6003, 1, 0, 0, 0, '2021-01-29 09:31:16'),
(null, 2, 6004, 1, 0, 0, 0, '2021-01-29 09:31:16');



INSERT INTO `sidebar_menus` (`id`, `permission_group_id`, `icon`, `menu`, `activate_menu`, `lang_key`, `system_level`, `level`, `sidebar_display`, `access_permissions`, `is_active`, `created_at`) VALUES (47, 600, 'fa fa-video-camera', 'Gmeet Live Classes', 'gmeet', 'gmeet_online_classes', 0, 10, 1, '(\'gmeet_setting\', \'can_view\') || (\'gmeet_live_classes\', \'can_view\') || (\'gmeet_live_meeting\', \'can_view\') || (\'gmeet_live_meeting_report\', \'can_view\') || (\'gmeet_live_classes_report\', \'can_view\')', 1, '2023-01-10 12:49:51');

INSERT INTO `sidebar_sub_menus` (`id`, `sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`, `created_at`) VALUES
(451, 47, 'Live Classes', NULL, 'live_class', 'admin/gmeet/timetable', 1, '(\'gmeet_live_classes\', \'can_view\')', NULL, 'gmeet', 'timetable', '', 1, '2022-12-22 05:59:38'),
(452, 47, 'Live Meeting', NULL, 'live_meeting', 'admin/gmeet/meeting', 1, '(\'gmeet_live_meeting\', \'can_view\')', NULL, 'gmeet', 'meeting', '', 1, '2022-11-15 06:05:27'),
(453, 47, 'Live Classes Report', NULL, 'live_meeting_report', 'admin/gmeet/class_report', 1, '(\'gmeet_live_classes_report\', \'can_view\')', NULL, 'gmeet', 'class_report', '', 1, '2022-11-15 06:05:27'),
(454, 47, 'Setting', NULL, 'setting', 'admin/gmeet', 1, '(\'gmeet_setting\', \'can_view\')', NULL, 'gmeet', 'index', '', 1, '2022-11-15 06:05:27'),
(455, 47, 'Live Meeting Report', NULL, 'live_classes_report', 'admin/gmeet/meeting_report', 1, '(\'gmeet_live_meeting_report\', \'can_view\')', NULL, 'gmeet', 'meeting_report', '', 1, '2022-12-22 05:59:38');


--
-- Indexes for table `gmeet`
--
ALTER TABLE `gmeet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `gmeet_history`
--
ALTER TABLE `gmeet_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gmeet_id` (`gmeet_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `gmeet_sections`
--
ALTER TABLE `gmeet_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cls_section_id` (`cls_section_id`),
  ADD KEY `gmeet_id` (`gmeet_id`);

--
-- Indexes for table `gmeet_settings`
--
ALTER TABLE `gmeet_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gmeet_staff`
--
ALTER TABLE `gmeet_staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gmeet_id` (`gmeet_id`),
  ADD KEY `staff_id` (`staff_id`);

ALTER TABLE `gmeet`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `gmeet_history`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `gmeet_sections`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `gmeet_settings`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `gmeet_staff`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `gmeet`
  ADD CONSTRAINT `gmeet_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeet_ibfk_2` FOREIGN KEY (`created_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeet_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gmeet_history`
--

ALTER TABLE `gmeet_history`
  ADD CONSTRAINT `gmeet_history_ibfk_1` FOREIGN KEY (`gmeet_id`) REFERENCES `gmeet` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeet_history_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeet_history_ibfk_3` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gmeet_sections`
--
ALTER TABLE `gmeet_sections`
  ADD CONSTRAINT `gmeet_sections_ibfk_1` FOREIGN KEY (`cls_section_id`) REFERENCES `class_sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeet_sections_ibfk_2` FOREIGN KEY (`gmeet_id`) REFERENCES `gmeet` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gmeet_staff`
--
ALTER TABLE `gmeet_staff`
  ADD CONSTRAINT `gmeet_staff_ibfk_1` FOREIGN KEY (`gmeet_id`) REFERENCES `gmeet` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `gmeet_staff_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;
