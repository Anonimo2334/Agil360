-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-04-2026 a las 02:16:58
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `agil365`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alerts`
--

CREATE TABLE `alerts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('riesgo','vencido','sin_tareas','sin_actualizacion','tareas_atrasadas') NOT NULL DEFAULT 'riesgo',
  `message` varchar(255) NOT NULL,
  `severity` enum('warning','error') NOT NULL DEFAULT 'error',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('activa','resuelta','ignorada') NOT NULL DEFAULT 'activa',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `alerts`
--

INSERT INTO `alerts` (`id`, `project_id`, `type`, `message`, `severity`, `is_read`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'riesgo', 'Automatización CRM: <50% avance con <30% tiempo restante.', 'error', 1, 'resuelta', '2026-03-12 06:15:27', '2026-03-18 18:44:03'),
(2, 4, 'riesgo', 'TechBot Pro: <50% avance con <30% tiempo restante.', 'error', 1, 'resuelta', '2026-03-12 06:15:27', '2026-03-18 22:20:24'),
(4, 4, 'vencido', 'TechBot Pro: proyecto vencido.', 'error', 1, 'resuelta', '2026-03-18 18:32:55', '2026-03-18 22:19:58'),
(5, 4, 'vencido', 'TechBot Pro: proyecto vencido.', 'error', 0, 'resuelta', '2026-03-18 22:29:52', '2026-03-18 22:30:42'),
(6, 1, 'vencido', 'Bot WhatsApp Business: proyecto vencido desde 15/04/2025.', 'error', 0, 'resuelta', '2026-03-18 22:30:42', '2026-03-18 22:51:21'),
(7, 2, 'riesgo', 'Automatización CRM: menos del 50% de avance con menos del 30% de tiempo restante.', 'error', 0, 'resuelta', '2026-03-18 22:30:42', '2026-03-18 22:53:31'),
(8, 2, 'vencido', 'Automatización CRM: proyecto vencido desde 28/03/2025.', 'error', 0, 'resuelta', '2026-03-18 22:30:42', '2026-03-18 22:53:31'),
(9, 3, 'vencido', 'Plataforma E-Commerce: proyecto vencido desde 10/05/2025.', 'error', 0, 'activa', '2026-03-18 22:30:42', '2026-03-18 22:30:42'),
(12, 1, 'vencido', 'Bot WhatsApp Business: proyecto vencido desde 15/04/2025.', 'error', 0, 'resuelta', '2026-03-18 22:51:29', '2026-03-18 23:52:38'),
(13, 1, 'vencido', 'Bot WhatsApp Business: proyecto vencido desde 15/04/2025.', 'error', 0, 'activa', '2026-03-18 23:54:35', '2026-03-18 23:54:35'),
(14, 4, 'vencido', 'TechBot Pro: proyecto vencido desde 20/03/2026.', 'error', 0, 'resuelta', '2026-03-23 21:58:09', '2026-03-24 05:16:16'),
(15, 4, 'vencido', 'TechBot Pro: proyecto vencido desde 20/03/2026.', 'error', 0, 'activa', '2026-03-24 05:16:33', '2026-03-24 05:16:33'),
(16, 1, 'tareas_atrasadas', 'Bot WhatsApp Business: 2 tarea(s) vencida(s) sin completar.', 'warning', 0, 'resuelta', '2026-03-28 18:08:25', '2026-03-30 00:17:06'),
(17, 2, 'vencido', 'Automatización CRM: proyecto vencido desde 28/03/2026.', 'error', 0, 'activa', '2026-03-28 18:08:25', '2026-03-28 18:08:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bonuses`
--

CREATE TABLE `bonuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `engineer_id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(8,2) NOT NULL DEFAULT 50.00,
  `status` enum('pendiente','aprobado','pagado','rechazado') NOT NULL DEFAULT 'pendiente',
  `reason` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `companies`
--

CREATE TABLE `companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `whatsapp` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `companies`
--

INSERT INTO `companies` (`id`, `name`, `contact_name`, `phone`, `whatsapp`, `email`, `website`, `country`, `address`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Nexo Corp', 'Roberto Nexo', NULL, NULL, 'info@nexocorp.com', NULL, 'México', NULL, NULL, 1, '2026-03-12 06:15:27', '2026-03-12 06:15:27'),
(2, 'TechVision SA', 'Diana Visión', NULL, NULL, 'info@techvision.com', NULL, 'Colombia', NULL, NULL, 1, '2026-03-12 06:15:27', '2026-03-12 06:15:27'),
(3, 'Innova Digital', 'Jorge Innovate', NULL, NULL, 'info@innovadig.com', NULL, 'Argentina', NULL, NULL, 1, '2026-03-12 06:15:27', '2026-03-12 06:15:27'),
(4, 'StartUp Pro', 'Laura Startup', NULL, NULL, 'info@startuppro.com', NULL, 'Chile', NULL, NULL, 1, '2026-03-12 06:15:27', '2026-03-12 06:15:27'),
(6, 'FinTech Group', 'Natalia Fintech', NULL, NULL, 'info@fintech.com', NULL, 'Brasil', NULL, NULL, 1, '2026-03-12 06:15:27', '2026-03-12 06:15:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `google_calendar_integrations`
--

CREATE TABLE `google_calendar_integrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `access_token` text NOT NULL,
  `refresh_token` text DEFAULT NULL,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `google_calendar_integrations`
--

INSERT INTO `google_calendar_integrations` (`id`, `user_id`, `google_id`, `email`, `access_token`, `refresh_token`, `token_expires_at`, `created_at`, `updated_at`) VALUES
(1, 1, '100226277222113735020', 'darielmonterodeoleo12@gmail.com', 'ya29.a0Aa7MYioqOS7vUJcsYZ53NFUtx1vgPnZiviPGY1cliqTsFeVESuSVM81l9baOzxt0LgOyWBlUDntk7zCttUg_El3GR0W0MLljMQl30vSb5W1kIjsy_6-fblt652YRAhZZmBdjmAUiheK5mX7dl6109iGnKTgJYf8OdSiyv7QSod0lI2cMvMiORDjGnWstzWeHbJ3Cry8TaCgYKAdwSARMSFQHGX2Mi8naLNz-xQQiYWZfTl1oIpw0207', '1//05c6igLOIvgcOCgYIARAAGAUSNwF-L9Ir8uT61VniClGCHaDR5jQ541iTELP2Y_hH2i-j-MPJVX7VYpC7EcjjiY07eVg5vM2h87U', '2026-03-30 00:57:57', '2026-03-28 23:46:46', '2026-03-29 23:57:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `google_integrations`
--

CREATE TABLE `google_integrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `google_email` varchar(255) DEFAULT NULL,
  `access_token` text NOT NULL,
  `refresh_token` text DEFAULT NULL,
  `token_expires_at` timestamp NULL DEFAULT NULL,
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `meetings`
--

CREATE TABLE `meetings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `project_id` bigint(20) UNSIGNED DEFAULT NULL,
  `meeting_date` date NOT NULL,
  `meeting_time` time NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` enum('programada','completada','cancelada') NOT NULL DEFAULT 'programada',
  `google_event_id` varchar(255) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `meetings`
--

INSERT INTO `meetings` (`id`, `title`, `project_id`, `meeting_date`, `meeting_time`, `description`, `location`, `status`, `google_event_id`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Revisión Sprint Nexo Corp', 1, '2026-03-12', '10:00:00', 'Revisión del sprint quincenal', NULL, 'programada', NULL, 1, '2026-03-12 06:15:27', '2026-03-12 06:15:27'),
(2, 'Demo cliente TechVision', 2, '2026-03-12', '15:30:00', 'Demo del CRM al cliente', NULL, 'programada', NULL, 1, '2026-03-12 06:15:27', '2026-03-12 06:15:27'),
(3, 'Reunion para avance', 2, '2026-03-18', '18:23:00', NULL, 'https://meet.google.com/landing?pli=1', 'programada', NULL, 1, '2026-03-18 18:50:49', '2026-03-18 18:50:49'),
(4, 'Estado de proyecto', 2, '2026-03-24', '09:23:00', NULL, 'https://meet.google.com/landing?pli=1', 'programada', NULL, 1, '2026-03-24 05:13:00', '2026-03-24 05:13:00'),
(5, 'Revision de proyecto', 1, '2026-03-24', '10:03:00', 'Necesito que tenga todo actualizado del proyecto', 'https://meet.google.com/landing?pli=1', 'programada', NULL, 1, '2026-03-24 08:57:54', '2026-03-24 08:57:54'),
(7, 'Automatización', 1, '2026-03-25', '12:00:00', 'ddddddddddddddddd', 'https://meet.google.com/landing?pli=1', 'programada', NULL, 1, '2026-03-25 19:20:39', '2026-03-25 19:20:39'),
(8, 'Automatizacion', 2, '2026-03-26', '14:20:00', 'Reunion para ver proyecto', NULL, 'programada', NULL, 1, '2026-03-25 20:14:06', '2026-03-25 20:14:06'),
(9, 'Automatizacion', 2, '2026-03-26', '14:20:00', 'Reunion para ver proyecto', NULL, 'programada', NULL, 1, '2026-03-25 20:14:06', '2026-03-25 20:14:06'),
(10, 'Ver Estado del proyecto', 3, '2026-03-29', '13:45:00', 'Revisión de avances del proyecto, para ver lo que han hechos los ingeniero', 'https://meet.google.com/landing?pli=1', 'programada', NULL, 1, '2026-03-28 23:48:11', '2026-03-30 00:04:20'),
(11, 'Ver Estado del proyecto', 3, '2026-03-29', '13:45:00', 'Revisión de avances del proyecto', 'https://meet.google.com/landing?pli=1', 'programada', 'ha9antb4uk8248uih9be1gcjlk', 1, '2026-03-28 23:52:15', '2026-03-29 23:57:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `meeting_logs`
--

CREATE TABLE `meeting_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `action` enum('creada','editada','eliminada','estado_cambiado','fecha_cambiada') NOT NULL,
  `field_changed` varchar(255) DEFAULT NULL,
  `old_value` text DEFAULT NULL,
  `new_value` text DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `meeting_logs`
--

INSERT INTO `meeting_logs` (`id`, `meeting_id`, `user_id`, `action`, `field_changed`, `old_value`, `new_value`, `reason`, `created_at`, `updated_at`) VALUES
(4, 7, 1, 'creada', NULL, NULL, NULL, NULL, '2026-03-25 19:20:39', '2026-03-25 19:20:39'),
(6, 9, 1, 'creada', NULL, NULL, NULL, NULL, '2026-03-25 20:14:06', '2026-03-25 20:14:06'),
(7, 8, 1, 'creada', NULL, NULL, NULL, NULL, '2026-03-25 20:14:06', '2026-03-25 20:14:06'),
(8, 10, 1, 'creada', NULL, NULL, NULL, NULL, '2026-03-28 23:48:11', '2026-03-28 23:48:11'),
(9, 11, 1, 'creada', NULL, NULL, NULL, NULL, '2026-03-28 23:52:15', '2026-03-28 23:52:15'),
(10, 11, 1, 'editada', 'meeting_time', '13:45:00', '13:45', 'Avances del proyecto, tiene que estar a un 90%', '2026-03-28 23:55:21', '2026-03-28 23:55:21'),
(11, 11, 1, 'editada', 'description', 'Revisión de avances del proyecto', NULL, 'Avances del proyecto, tiene que estar a un 90%', '2026-03-28 23:55:21', '2026-03-28 23:55:21'),
(12, 11, 1, 'editada', 'location', 'https://meet.google.com/landing?pli=1', NULL, 'Avances del proyecto, tiene que estar a un 90%', '2026-03-28 23:55:21', '2026-03-28 23:55:21'),
(13, 11, 1, 'editada', 'meeting_time', '13:45:00', '13:45', 'Revisión de avances del proyecto, nota, tiene que estar aunque sea un 90% de avances', '2026-03-28 23:59:01', '2026-03-28 23:59:01'),
(14, 11, 1, 'editada', 'description', 'Revisión de avances del proyecto', NULL, 'Revisión de avances del proyecto, nota, tiene que estar aunque sea un 90% de avances', '2026-03-28 23:59:01', '2026-03-28 23:59:01'),
(15, 11, 1, 'editada', 'location', 'https://meet.google.com/landing?pli=1', NULL, 'Revisión de avances del proyecto, nota, tiene que estar aunque sea un 90% de avances', '2026-03-28 23:59:01', '2026-03-28 23:59:01'),
(16, 10, 1, 'editada', 'meeting_time', '13:45:00', '13:45', 'Revisión de avances del proyecto el avance tiene que estar a un 80%', '2026-03-29 23:54:32', '2026-03-29 23:54:32'),
(17, 10, 1, 'editada', 'description', 'Revisión de avances del proyecto', NULL, 'Revisión de avances del proyecto el avance tiene que estar a un 80%', '2026-03-29 23:54:32', '2026-03-29 23:54:32'),
(18, 10, 1, 'editada', 'location', 'https://meet.google.com/landing?pli=1', NULL, 'Revisión de avances del proyecto el avance tiene que estar a un 80%', '2026-03-29 23:54:32', '2026-03-29 23:54:32'),
(19, 11, 1, 'editada', 'meeting_time', '13:45:00', '13:45', 'Revisión', '2026-03-29 23:57:54', '2026-03-29 23:57:54'),
(20, 11, 1, 'editada', 'description', 'Revisión de avances del proyecto', NULL, 'Revisión', '2026-03-29 23:57:54', '2026-03-29 23:57:54'),
(21, 11, 1, 'editada', 'location', 'https://meet.google.com/landing?pli=1', NULL, 'Revisión', '2026-03-29 23:57:54', '2026-03-29 23:57:54'),
(22, 10, 1, 'editada', 'meeting_time', '13:45:00', '13:45', 'Se modifico para mas claridad', '2026-03-30 00:04:20', '2026-03-30 00:04:20'),
(23, 10, 1, 'editada', 'description', 'Revisión de avances del proyecto', 'Revisión de avances del proyecto, para ver lo que han hechos los ingeniero', 'Se modifico para mas claridad', '2026-03-30 00:04:20', '2026-03-30 00:04:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `meeting_participants`
--

CREATE TABLE `meeting_participants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `meeting_participants`
--

INSERT INTO `meeting_participants` (`id`, `meeting_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 5, 7, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000001_create_roles_table', 1),
(5, '2024_01_01_000002_update_users_table', 1),
(6, '2024_01_01_000003_create_companies_table', 1),
(7, '2024_01_01_000004_create_projects_table', 1),
(8, '2024_01_01_000005_create_tasks_table', 1),
(9, '2024_01_01_000006_create_project_notes_table', 1),
(10, '2024_01_01_000007_create_meetings_table', 1),
(11, '2024_01_01_000008_create_pending_items_table', 1),
(12, '2024_01_01_000009_create_bonuses_table', 1),
(13, '2024_01_01_000010_create_alerts_table', 1),
(14, '2024_01_01_000011_create_activity_logs_table', 1),
(15, '2026_03_17_234641_add_permissions_to_roles_table', 2),
(16, '2026_03_18_003017_create_settings_table', 3),
(17, '2026_03_23_185308_add_resolution_note_to_pending_items_table', 4),
(18, '2026_03_25_000001_create_meeting_logs_table', 5),
(19, '2026_03_25_184315_create_google_integrations_table', 6),
(20, '2026_03_25_200000_update_google_integrations_table', 6),
(21, '2026_03_25_200001_add_google_event_id_to_meetings', 6),
(22, '2026_03_28_125800_create_google_calendar_integrations_table', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pending_items`
--

CREATE TABLE `pending_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('cliente','ingeniero') NOT NULL,
  `description` text NOT NULL,
  `status` enum('pendiente','completado','cancelado') NOT NULL DEFAULT 'pendiente',
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `resolution_note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `pending_items`
--

INSERT INTO `pending_items` (`id`, `project_id`, `type`, `description`, `status`, `assigned_to`, `created_at`, `updated_at`, `resolution_note`) VALUES
(1, 2, 'cliente', 'Faltan credenciales de acceso al CRM actual', 'completado', NULL, '2026-03-12 06:15:27', '2026-03-18 23:03:09', NULL),
(2, 4, 'cliente', 'Datos de entrenamiento pendientes de envío', 'pendiente', NULL, '2026-03-12 06:15:27', '2026-03-12 06:15:27', NULL),
(3, 2, 'ingeniero', 'Completar integración con Salesforce', 'pendiente', 3, '2026-03-12 06:15:27', '2026-03-12 06:15:27', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `ceo` varchar(255) DEFAULT NULL,
  `primary_engineer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `backup_engineer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `progress_percentage` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('iniciado','en_proceso','soporte','completado','cancelado') NOT NULL DEFAULT 'iniciado',
  `platform` varchar(255) DEFAULT NULL,
  `bot_name` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `server_hosting` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_at_risk` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `projects`
--

INSERT INTO `projects` (`id`, `company_id`, `project_name`, `ceo`, `primary_engineer_id`, `backup_engineer_id`, `start_date`, `end_date`, `progress_percentage`, `status`, `platform`, `bot_name`, `website_url`, `server_hosting`, `notes`, `is_at_risk`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bot WhatsApp Business', 'Roberto Nexo', 2, 3, '2025-01-15', '2025-04-15', 100, 'en_proceso', 'Agil365', 'NexoBot', NULL, NULL, NULL, 0, '2026-03-12 06:15:27', '2026-03-30 00:20:13'),
(2, 2, 'Automatización CRM', 'Diana Visión', 3, 4, '2025-01-01', '2026-03-28', 100, 'en_proceso', 'Agil365', NULL, NULL, NULL, NULL, 0, '2026-03-12 06:15:27', '2026-03-30 00:20:30'),
(3, 3, 'Plataforma E-Commerce', 'Jorge Innovate', 4, 2, '2024-11-01', '2025-05-10', 91, 'soporte', 'Agil365', NULL, NULL, NULL, NULL, 0, '2026-03-12 06:15:27', '2026-03-12 06:15:27'),
(4, 4, 'TechBot Pro', 'Laura Startup', 5, 6, '2026-01-10', '2026-03-20', 100, 'en_proceso', 'Agil365', 'Bot Daniel', NULL, NULL, NULL, 0, '2026-03-12 06:15:27', '2026-03-18 22:30:31'),
(6, 6, 'Dashboard Analytics', 'Natalia Fintech', 7, 2, '2024-12-01', '2025-03-01', 100, 'completado', 'Agil365', NULL, NULL, NULL, NULL, 0, '2026-03-12 06:15:27', '2026-03-12 06:15:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `project_notes`
--

CREATE TABLE `project_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `note` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `permissions`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrador', 'super_admin', 'Acceso total al sistema', '[]', '2026-03-12 06:15:14', '2026-03-18 04:20:33'),
(2, 'Administrador', 'admin', 'Gestión del sistema', '[\"clientes.ver\",\"clientes.crear\",\"clientes.editar\",\"clientes.eliminar\",\"proyectos.ver\",\"proyectos.crear\",\"proyectos.editar\",\"proyectos.eliminar\",\"tareas.ver\",\"tareas.crear\",\"tareas.editar\",\"tareas.eliminar\",\"usuarios.gestionar\",\"roles.gestionar\",\"reportes.ver\"]', '2026-03-12 06:15:14', '2026-03-18 19:49:11'),
(3, 'Gerente', 'gerente', 'Gerencia operativa', '[\"clientes.ver\",\"proyectos.ver\",\"proyectos.crear\",\"proyectos.editar\",\"tareas.ver\",\"tareas.crear\",\"tareas.editar\",\"reportes.ver\"]', '2026-03-12 06:15:14', '2026-03-18 19:49:11'),
(4, 'Ingeniero', 'ingeniero', 'Desarrollador / Técnico', '[\"proyectos.ver\",\"tareas.ver\",\"tareas.crear\",\"tareas.editar\"]', '2026-03-12 06:15:14', '2026-03-18 19:49:11'),
(5, 'Soporte', 'soporte', 'Soporte técnico', '[\"clientes.ver\",\"proyectos.ver\",\"tareas.ver\",\"tareas.crear\",\"tareas.editar\"]', '2026-03-12 06:15:14', '2026-03-18 19:49:11'),
(6, 'Visualizador', 'visualizador', 'Solo lectura', '[\"clientes.ver\",\"proyectos.ver\",\"tareas.ver\",\"reportes.ver\"]', '2026-03-12 06:15:15', '2026-03-18 04:20:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('7Pa5x43HQKVrzQYn8187OuRTeO654V4coZCIlCUI', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZmJRbTgwV3N5R0FmbGpMcVdudTdQTW02RURUQklTTmxmRWNCMTk4UCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1NToiaHR0cDovL2xvY2FsaG9zdC9BZ2lsMzYwL3B1YmxpYy9nb29nbGUtY2FsZW5kYXIvY29ubmVjdCI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjU1OiJodHRwOi8vbG9jYWxob3N0L0FnaWwzNjAvcHVibGljL2dvb2dsZS1jYWxlbmRhci9jb25uZWN0IjtzOjU6InJvdXRlIjtzOjIzOiJnb29nbGUuY2FsZW5kYXIuY29ubmVjdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1774725341),
('cBHuqIDq5sp2Zzotcedjrj92FmpELml5QNekSPcJ', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibnZadVp3cGpFZ1REeGFpRXlEUGJhdUs1SVRpOG16eTdySlpLMmxwdyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQxOiJodHRwOi8vbG9jYWxob3N0L0FnaWwzNjAvcHVibGljL3JldW5pb25lcyI7czo1OiJyb3V0ZSI7czo5OiJyZXVuaW9uZXMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1774727943),
('Qmmu15dlrTAT0LcVLPK36ZtsEApErQAZ56Jf7yLp', 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNU1JSWxuR3dEcVBYSTZYR3Nqb3lEcU5XazNWQ0VlUWxaSG5aS2J1NCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3QvQWdpbDM2MC9wdWJsaWMiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1774816618);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_engineer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `priority` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media',
  `status` enum('pendiente','en_progreso','completada','bloqueada') NOT NULL DEFAULT 'pendiente',
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `progress` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tasks`
--

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `assigned_engineer_id`, `priority`, `status`, `start_date`, `due_date`, `progress`, `created_at`, `updated_at`) VALUES
(1, 1, 'Integración API WhatsApp', NULL, 2, 'alta', 'completada', NULL, NULL, 100, '2026-03-12 06:15:27', '2026-03-30 00:20:13'),
(2, 1, 'Panel de mensajes', NULL, 3, 'media', 'completada', NULL, NULL, 100, '2026-03-12 06:15:27', '2026-03-19 00:05:11'),
(3, 2, 'Sincronización de contactos', NULL, 3, 'critica', 'completada', NULL, NULL, 100, '2026-03-12 06:15:27', '2026-03-18 22:53:27'),
(4, 2, 'Dashboard de reportes CRM', NULL, 3, 'alta', 'completada', NULL, NULL, 100, '2026-03-12 06:15:27', '2026-03-30 00:20:30'),
(5, 3, 'Módulo de pagos', NULL, 4, 'alta', 'completada', NULL, NULL, 100, '2026-03-12 06:15:27', '2026-03-12 06:15:27'),
(6, 4, 'Entrenamiento del modelo NLP', NULL, 5, 'critica', 'completada', NULL, NULL, 100, '2026-03-12 06:15:27', '2026-03-18 20:06:21'),
(8, 6, 'Gráficas de conversión', NULL, 7, 'media', 'completada', NULL, NULL, 100, '2026-03-12 06:15:27', '2026-03-12 06:15:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `bio` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `avatar`, `bio`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role_id`, `phone`, `department`, `position`, `is_active`, `last_login_at`) VALUES
(1, 'Admin Agil365', 'admin@agil365.com', NULL, NULL, NULL, '$2y$12$3e2.7oI112e76pXaRfWai.BqCtM5OPPIV8ABqXNVdJl.xfNBU0mGe', NULL, '2026-03-12 06:15:20', '2026-03-30 00:36:57', 1, NULL, 'Administración', NULL, 1, '2026-03-30 00:36:57'),
(2, 'Ana López', 'ana@agil365.com', NULL, NULL, NULL, '$2y$12$pQ5IbvY7sQQhrQX5paA.2.gH.Ji3fzobvusytNvjbTHSd98Gh9XGO', NULL, '2026-03-12 06:15:21', '2026-03-30 00:19:48', 4, NULL, 'Desarrollo', NULL, 1, '2026-03-30 00:19:48'),
(3, 'Carlos Ruiz', 'carlos@agil365.com', NULL, NULL, NULL, '$2y$12$5245O3LTM33R0oDZnXeQ3eCiX1TI/tKXFYdG41E5vEJFDBKF70zDC', NULL, '2026-03-12 06:15:22', '2026-03-12 06:15:22', 4, NULL, 'Desarrollo', NULL, 1, NULL),
(4, 'María Torres', 'maria@agil365.com', NULL, NULL, NULL, '$2y$12$VoHZO5Wx9CIvQHjX71upIeNGu2pXeuYLjqpJWl58qvye9iFrSxPgC', NULL, '2026-03-12 06:15:23', '2026-03-17 23:55:51', 4, NULL, 'Desarrollo', NULL, 1, '2026-03-17 23:55:51'),
(5, 'Luis García', 'luis@agil365.com', NULL, NULL, NULL, '$2y$12$i1daLmZ0cgHiLPobpLac0unjY1QGdUkAs00HDovb5Jk0vEjZisnj6', NULL, '2026-03-12 06:15:25', '2026-03-12 06:15:25', 4, NULL, 'Desarrollo', NULL, 1, NULL),
(6, 'Sara Méndez', 'sara@agil365.com', NULL, NULL, NULL, '$2y$12$X5Ajk195ClLhXThEdyezIuWobhbbB7FgOdztaXKtkjAyDtqa88R1G', NULL, '2026-03-12 06:15:25', '2026-03-18 20:58:56', 5, NULL, 'Soporte', NULL, 1, '2026-03-18 20:58:56'),
(7, 'Pedro Vega', 'pedro@agil365.com', NULL, NULL, NULL, '$2y$12$r/7wWQTaUQd48w8s51EjW.Z7WKuliS9632/QbREA9/Q7QVa0nob2e', NULL, '2026-03-12 06:15:27', '2026-03-12 06:15:27', 4, NULL, 'Desarrollo', NULL, 1, NULL),
(8, 'Dariel Montero', 'dari@agil360.com', NULL, NULL, NULL, '$2y$12$S1WKsBcfPi4bcstoP8az.uZnv05Q..rbaqvHZ3Y4yBQERdRtEtvH6', NULL, '2026-03-18 18:22:31', '2026-03-18 23:56:18', 4, '8294557178', 'Inteligencia artificial', NULL, 1, '2026-03-18 23:56:18'),
(9, 'Elian', 'elian12@gmail.com', NULL, NULL, NULL, '$2y$12$qGhXMEODhoB8LO.H6fmhHOJ2IEFjaeeTQH2kzFMScqG8DHz3eBo2G', NULL, '2026-03-25 19:23:54', '2026-03-25 19:23:54', 3, '+1 8492074206', 'Gerencia', NULL, 1, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `alerts`
--
ALTER TABLE `alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `alerts_project_id_foreign` (`project_id`);

--
-- Indices de la tabla `bonuses`
--
ALTER TABLE `bonuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bonuses_engineer_id_foreign` (`engineer_id`),
  ADD KEY `bonuses_project_id_foreign` (`project_id`),
  ADD KEY `bonuses_approved_by_foreign` (`approved_by`);

--
-- Indices de la tabla `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indices de la tabla `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `google_calendar_integrations`
--
ALTER TABLE `google_calendar_integrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `google_calendar_integrations_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `google_integrations`
--
ALTER TABLE `google_integrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `google_integrations_user_id_unique` (`user_id`);

--
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indices de la tabla `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meetings_project_id_foreign` (`project_id`),
  ADD KEY `meetings_created_by_foreign` (`created_by`);

--
-- Indices de la tabla `meeting_logs`
--
ALTER TABLE `meeting_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meeting_logs_meeting_id_foreign` (`meeting_id`),
  ADD KEY `meeting_logs_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `meeting_participants`
--
ALTER TABLE `meeting_participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meeting_participants_meeting_id_foreign` (`meeting_id`),
  ADD KEY `meeting_participants_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `pending_items`
--
ALTER TABLE `pending_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pending_items_project_id_foreign` (`project_id`),
  ADD KEY `pending_items_assigned_to_foreign` (`assigned_to`);

--
-- Indices de la tabla `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projects_company_id_foreign` (`company_id`),
  ADD KEY `projects_primary_engineer_id_foreign` (`primary_engineer_id`),
  ADD KEY `projects_backup_engineer_id_foreign` (`backup_engineer_id`);

--
-- Indices de la tabla `project_notes`
--
ALTER TABLE `project_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_notes_project_id_foreign` (`project_id`),
  ADD KEY `project_notes_user_id_foreign` (`user_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indices de la tabla `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indices de la tabla `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indices de la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_project_id_foreign` (`project_id`),
  ADD KEY `tasks_assigned_engineer_id_foreign` (`assigned_engineer_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `alerts`
--
ALTER TABLE `alerts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `bonuses`
--
ALTER TABLE `bonuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `companies`
--
ALTER TABLE `companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `google_calendar_integrations`
--
ALTER TABLE `google_calendar_integrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `google_integrations`
--
ALTER TABLE `google_integrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `meeting_logs`
--
ALTER TABLE `meeting_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `meeting_participants`
--
ALTER TABLE `meeting_participants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `pending_items`
--
ALTER TABLE `pending_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `project_notes`
--
ALTER TABLE `project_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `alerts`
--
ALTER TABLE `alerts`
  ADD CONSTRAINT `alerts_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `bonuses`
--
ALTER TABLE `bonuses`
  ADD CONSTRAINT `bonuses_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bonuses_engineer_id_foreign` FOREIGN KEY (`engineer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bonuses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `google_calendar_integrations`
--
ALTER TABLE `google_calendar_integrations`
  ADD CONSTRAINT `google_calendar_integrations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `google_integrations`
--
ALTER TABLE `google_integrations`
  ADD CONSTRAINT `google_integrations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `meetings`
--
ALTER TABLE `meetings`
  ADD CONSTRAINT `meetings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meetings_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `meeting_logs`
--
ALTER TABLE `meeting_logs`
  ADD CONSTRAINT `meeting_logs_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `meeting_participants`
--
ALTER TABLE `meeting_participants`
  ADD CONSTRAINT `meeting_participants_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `pending_items`
--
ALTER TABLE `pending_items`
  ADD CONSTRAINT `pending_items_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pending_items_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_backup_engineer_id_foreign` FOREIGN KEY (`backup_engineer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `projects_primary_engineer_id_foreign` FOREIGN KEY (`primary_engineer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `project_notes`
--
ALTER TABLE `project_notes`
  ADD CONSTRAINT `project_notes_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_assigned_engineer_id_foreign` FOREIGN KEY (`assigned_engineer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
