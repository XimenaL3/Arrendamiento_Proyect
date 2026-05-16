INSERT INTO Roles(NombreRol)
VALUES
('Administrador'),
('Cajero'),
('Mantenimiento'),
('Cobrador');

CALL sp_RegistrarTrabajador(

    'Luis',
    'Guzman',
    'Lopez',
    '4181234567',
    'luis@sunlight.com',
    'luis.png',

    'LuisAdmin',
    '123456'

);

INSERT INTO Servicios (NombreServicio)
VALUES 
('Luz'),
('Agua'),
('Internet');