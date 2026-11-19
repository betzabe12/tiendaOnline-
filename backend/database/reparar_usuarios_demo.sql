-- Ejecutar en phpMyAdmin si ya importaste fashioncloud.sql anteriormente.
-- Estas claves son exclusivamente para pruebas locales. Cámbialas después.
USE fashioncloud;
UPDATE usuarios SET password = '$2y$12$lU86juLP586nWlKzYV0bT.eoEigns7ADhTA5Ii5w1ox7Qft8/MLJS' WHERE correo = 'admin@fashioncloud.tienda';
UPDATE usuarios SET password = '$2y$12$7G2xA0tSupGW7EGO4zbiMO3RqgAkSH1rgQvdFskS8m2UI0ARmbrPe' WHERE correo = 'cliente@fashioncloud.tienda';
