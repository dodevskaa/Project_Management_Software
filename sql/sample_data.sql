USE pm_app;
-- Create an admin
INSERT INTO users (name,email,password,level,approved) VALUES
('Admin','admin@example.com','REPLACE_WITH_HASH','Admin',1);

-- Example users (passwords should be hashed if inserted directly)
-- You can register via UI and approve via admin panel after starting the app.
