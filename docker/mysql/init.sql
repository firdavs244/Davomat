-- MySQL initialization script
-- This file is executed when the container first starts

-- Grant permissions
GRANT ALL PRIVILEGES ON davomat.* TO 'davomat_user'@'%';
FLUSH PRIVILEGES;

-- You can add initial database setup here if needed
-- For now, Laravel migrations will handle the schema
