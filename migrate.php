<?php
require 'db.php';

// Add detected_person to main memories table (for cover photo)
mysqli_query($conn, "ALTER TABLE memories ADD COLUMN IF NOT EXISTS detected_person VARCHAR(50) DEFAULT 'unknown'");

// Add detected_person to memory_photos table
mysqli_query($conn, "ALTER TABLE memory_photos ADD COLUMN IF NOT EXISTS detected_person VARCHAR(50) DEFAULT 'unknown'");

// Add detected_person to memory_videos table
mysqli_query($conn, "ALTER TABLE memory_videos ADD COLUMN IF NOT EXISTS detected_person VARCHAR(50) DEFAULT 'unknown'");

echo "Migration successful: Columns added to all media tables.\n";
?>
