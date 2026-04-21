-- PrimeBoats migration: extend boats + bookings tables for new React frontend
-- Run once on existing database: mysql -u user -p primeboats < migrate.sql

ALTER TABLE boats
    ADD COLUMN slug              VARCHAR(120) DEFAULT NULL    AFTER name,
    ADD COLUMN type              VARCHAR(50)  DEFAULT NULL,
    ADD COLUMN year              INT          DEFAULT NULL,
    ADD COLUMN condition_state   VARCHAR(20)  NOT NULL DEFAULT 'used',
    ADD COLUMN status            VARCHAR(20)  NOT NULL DEFAULT 'available',
    ADD COLUMN featured          TINYINT(1)   NOT NULL DEFAULT 0,
    ADD COLUMN short_description VARCHAR(500) DEFAULT NULL,
    ADD COLUMN location          VARCHAR(100) DEFAULT NULL;

-- Migrate is_rented -> status
UPDATE boats SET status = CASE WHEN is_rented = 1 THEN 'sold' ELSE 'available' END;

-- Unique index on slug (populated via admin later)
ALTER TABLE boats ADD UNIQUE INDEX idx_boats_slug (slug);

ALTER TABLE bookings
    ADD COLUMN boat_name VARCHAR(100) DEFAULT NULL AFTER boat_id,
    ADD COLUMN status    VARCHAR(20)  NOT NULL DEFAULT 'new';
