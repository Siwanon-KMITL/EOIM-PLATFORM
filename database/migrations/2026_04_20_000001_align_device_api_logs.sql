ALTER TABLE device_api_logs
  ADD COLUMN IF NOT EXISTS user_id int(10) UNSIGNED DEFAULT NULL AFTER device_id,
  ADD COLUMN IF NOT EXISTS endpoint varchar(255) NOT NULL DEFAULT '/api/smartmeter/store' AFTER user_id,
  MODIFY request_method varchar(10) NOT NULL DEFAULT 'POST',
  MODIFY request_path varchar(255) DEFAULT NULL;

CREATE INDEX IF NOT EXISTS idx_device_api_logs_user_id
  ON device_api_logs (user_id);

SET @fk_device_api_logs_user_exists := (
  SELECT COUNT(*)
  FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'device_api_logs'
    AND CONSTRAINT_NAME = 'fk_device_api_logs_user'
);

SET @fk_device_api_logs_user_sql := IF(
  @fk_device_api_logs_user_exists = 0,
  'ALTER TABLE device_api_logs ADD CONSTRAINT fk_device_api_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL',
  'SELECT ''fk_device_api_logs_user already exists'''
);

PREPARE fk_device_api_logs_user_stmt FROM @fk_device_api_logs_user_sql;
EXECUTE fk_device_api_logs_user_stmt;
DEALLOCATE PREPARE fk_device_api_logs_user_stmt;

CREATE TABLE IF NOT EXISTS password_resets (
  id int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  email varchar(150) NOT NULL,
  token varchar(255) NOT NULL,
  expires_at datetime NOT NULL,
  created_at timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY token (token),
  KEY email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
