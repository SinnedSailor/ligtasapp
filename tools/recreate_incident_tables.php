<?php
$db=new mysqli('127.0.0.1','root','','db_iwas');
if ($db->connect_error) { echo "connect error: " . $db->connect_error . "\n"; exit(1); }

$queries = [];

$queries[] = "CREATE TABLE IF NOT EXISTS `incident_reports` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `n` INT(11) UNSIGNED NOT NULL,
  `month_of_incident` VARCHAR(40) DEFAULT NULL,
  `year_of_incident` INT(4) UNSIGNED DEFAULT NULL,
  `province` VARCHAR(120) DEFAULT NULL,
  `municipality` VARCHAR(160) DEFAULT NULL,
  `name_of_victim_enc` TEXT DEFAULT NULL,
  `name_of_victim_hash` VARCHAR(64) DEFAULT NULL,
  `location_category` VARCHAR(120) DEFAULT NULL,
  `age` INT(11) UNSIGNED DEFAULT NULL,
  `gender` VARCHAR(10) DEFAULT NULL,
  `occasion` VARCHAR(160) DEFAULT NULL,
  `factors` VARCHAR(255) DEFAULT NULL,
  `residence` VARCHAR(160) DEFAULT NULL,
  `region` VARCHAR(120) DEFAULT NULL,
  `occupation` VARCHAR(160) DEFAULT NULL,
  `remarks` TEXT DEFAULT NULL,
  `row_hash` VARCHAR(64) DEFAULT NULL,
  `review_status` VARCHAR(20) DEFAULT NULL,
  `reviewed_by` INT(11) UNSIGNED DEFAULT NULL,
  `reviewed_at` DATETIME DEFAULT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `incident_reports_n_unique` (`n`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

$queries[] = "CREATE TABLE IF NOT EXISTS `incident_report_attachments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `incident_n` INT(11) UNSIGNED NOT NULL,
  `file_kind` VARCHAR(20) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `stored_name` VARCHAR(255) NOT NULL,
  `stored_path` VARCHAR(255) NOT NULL,
  `preview_path` VARCHAR(255) DEFAULT NULL,
  `preview_mime` VARCHAR(120) DEFAULT NULL,
  `mime_type` VARCHAR(120) DEFAULT NULL,
  `size_bytes` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `uploaded_by` INT(11) UNSIGNED NOT NULL,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incident_n` (`incident_n`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

foreach ($queries as $sql) {
    if ($db->query($sql) === false) {
        echo "ERROR: " . $db->error . "\n";
        exit(1);
    }
}

echo "Created missing incident tables (if they were absent).\n";
