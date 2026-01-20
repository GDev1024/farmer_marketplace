-- Migration: Add image support to listings table
-- Version: 2.0
-- Date: 2026-01-19

USE grenada_farmers;

-- Add image columns to listings table
ALTER TABLE listings 
ADD COLUMN image_path VARCHAR(255) NULL AFTER image_url,
ADD COLUMN thumbnail_path VARCHAR(255) NULL AFTER image_path;

-- Update existing records to have NULL for new columns (already default)
-- No data migration needed as this is additive

-- Add index for image queries
CREATE INDEX idx_listings_has_image ON listings(image_path);

-- Rollback SQL (if needed):
-- ALTER TABLE listings DROP COLUMN image_path, DROP COLUMN thumbnail_path;
-- DROP INDEX idx_listings_has_image ON listings;