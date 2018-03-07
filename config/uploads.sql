-- uploads.sql
-- author: Pierre Quang Linh To
-- Database schema

DROP TABLE IF EXISTS `uploads`;

CREATE TABLE IF NOT EXISTS `uploads` (
  `id` CHAR(30) NOT NULL,
  `file_name` VARCHAR(256) NOT NULL,
  `size` BIGINT NOT NULL,
  `type` VARCHAR(256) NOT NULL,
  `share_code` CHAR(6) NOT NULL,
  `save_file_name` CHAR(40) NOT NULL,
  `upload_time` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;