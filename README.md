# Levantamiento de requerimientos

Para esta primera etapa, se agendó una cita con el encargado del edificio, con el objetivo de conocer a detalle sus necesidades y expectativas sobre el software a desarrollar. 
La reunión se llevó a cabo el día 14 de abril de 2026 en sus instalaciones, a las 4:00 de la tarde, horario acordado por conveniencia de ambas partes.

Durante la visita, el encargado mencionó que el problema principal radica en la gestión de los cobros de los departamentos, ya que muchos de los pagos no se realizan en las fechas establecidas. 
Esto genera retrasos, pagos incompletos y saldos pendientes de liquidar, lo que dificulta el control administrativo. Además, indicó que en varios casos los pagos se realizan en abonos, por lo que 
resulta aún más complicado llevar un seguimiento preciso de los adeudos de cada inquilino.

Asimismo, explicó que el cobro de la renta incluye servicios como luz y agua, los cuales pueden variar dependiendo de las características de cada departamento. Por ejemplo, algunos cuentan con
aire acondicionado u otros equipos que incrementan el consumo de energía, por lo que se les asigna un costo adicional en comparación con otros inquilinos.

Debido a esto, surge la necesidad de contar con un sistema que sea capaz de calcular y asignar de manera individual el porcentaje o monto correspondiente a los servicios para cada cliente, en función 
de las condiciones de su vivienda y su consumo.

Finalmente, el encargado expresó la importancia de que el sistema no solo facilite la gestión de los cobros y los abonos, sino que también genere reportes que permitan identificar posibles problemas, 
tanto en los pagos como en el mantenimiento de los departamentos, con el fin de dar seguimiento oportuno y mejorar la administración general del edificio.

Además, el encargado nos mostró y explicó cómo funciona cada una de las áreas dentro del negocio. Es decir, detalló las funciones de cada rol involucrado en la administración del edificio. 
En nuestro caso, estos roles se pueden identificar como administradores, cajeros, cobradores y personal de mantenimiento.

Los administradores se encargan de supervisar el funcionamiento general del sistema y de la gestión del edificio. Por otro lado, los cajeros son responsables de realizar los cobros y registrar los 
abonos, así como de dar seguimiento a las deudas de los inquilinos y generar reportes relacionados con los pagos.

En cuanto a los cobradores, su función principal es visitar las viviendas para realizar el cobro de los adeudos. También se encargan de identificar a las personas con pagos pendientes, consultar 
el monto total a pagar y determinar las acciones pertinentes en caso de morosidad.

Finalmente, el personal de mantenimiento es responsable de atender y reparar los problemas que se presenten en los departamentos. Por ello, es necesario gestionar diversos reportes de mantenimiento, 
así como registrar, dar seguimiento y marcar como resueltas las quejas o solicitudes de los inquilinos. Además, deben poder agregar observaciones sobre las reparaciones realizadas.

# Análisis de la entrevista

En esta etapa se analizan los aspectos mencionados durante la entrevista, así como las observaciones realizadas durante la visita a las instalaciones, con el fin de comprender cómo funciona actualmente 
el sistema y sus procesos.

A partir de esta información, se procederá a desglosar los requerimientos funcionales y los requerimientos no funcionales del sistema, los cuales servirán como base para el desarrollo de la solución propuesta. 

## Requerimientos funcionales

* El sistema debe permitir el registro de inquilinos y sus datos generales.
* El sistema debe permitir el registro y administración de los departamentos.
* El sistema debe gestionar el cobro de rentas por cada inquilino.
* El sistema debe permitir registrar pagos completos y pagos en abonos.
* El sistema debe llevar un control del saldo pendiente de cada inquilino.
* El sistema debe calcular automáticamente el monto total a pagar, incluyendo renta y servicios (agua, luz).
* El sistema debe permitir asignar costos adicionales por consumo extra (por ejemplo, un 15% de luz o un 40%).
* El sistema debe permitir definir el porcentaje o monto de servicios para cada departamento de manera individual.
* El sistema debe generar reportes de pagos realizados, pendientes y atrasados.
* El sistema debe permitir identificar a los inquilinos morosos.
* El sistema debe permitir registrar visitas de cobradores y seguimiento de adeudos.
* El sistema debe permitir registrar y gestionar reportes de mantenimiento.
* El sistema debe permitir dar seguimiento al estado de los reportes (pendiente, en proceso, resuelto).
* El sistema debe permitir agregar observaciones a los reportes de mantenimiento.
* El sistema debe manejar diferentes roles de usuario (administrador, cajero, cobrador y mantenimiento).
* El sistema debe permitir generar reportes generales para la administración del edificio.

## Requerimientos no funcionales

* El sistema debe ser fácil de usar (interfaz intuitiva y clara).
* El sistema debe ser accesible para diferentes tipos de usuarios según su rol.
* El sistema debe garantizar la seguridad de la información (control de accesos y contraseñas).
* El sistema debe ser confiable, evitando pérdida de datos.
* El sistema debe estar disponible en todo momento para consulta y registro de información.
* El sistema debe ser escalable, permitiendo agregar más departamentos o usuarios en el futuro.
* El sistema debe ser compatible con equipos de uso común (computadoras estándar y tablets).
* El sistema debe mantener integridad en los datos (evitar inconsistencias en pagos y adeudos).


# Modelado de la base de datos

Para el desarrollo de la base de datos, se tomó en cuenta el uso de la tecnología de persistencia MySQL, debido a su facilidad de implementación, mantenimiento y a que es la herramienta con la que se cuenta de mayor experiencia.

Para su diseño, se siguieron buenas prácticas de programación, como la normalización de la base de datos hasta la Tercera Forma Normal (3FN), con el objetivo de evitar redundancias en la información y garantizar que los datos almacenados se mantengan limpios, organizados y consistentes.

Asimismo, se consideraron diferentes herramientas que pueden complementar el funcionamiento del sistema. Además de la estructura de la base de datos, se plantea el uso de vistas y procedimientos almacenados, los cuales permitirán implementar parte de la lógica de negocio directamente en el sistema gestor de base de datos, facilitando la gestión de operaciones como los cobros, abonos y consultas de información.

De igual manera, se contempla el uso de triggers para la automatización de procesos, como la actualización de saldos, el registro de movimientos, lo que contribuirá a mejorar la eficiencia y el control dentro del sistema.

### Tabla: Personas
* idPersona (PK)
* nombre
* apellido
* telefono
* correo

### Tabla: Roles
* idRol (PK)
* nombre_rol (administrador, cajero, cobrador, mantenimiento)

### Tabla: Usuarios
* idUsuario (PK)
* idPersona (FK)
* idRol (FK)
* usuario
* contraseña

### Tabla: Departamentos
* idDepartamento (PK)
* numero
* descripcion
* estado

### Tabla: Inquilinos
* idInquilino (PK)
* idPersona (FK)
* idDepartamento (FK)
* fecha_inicio
* fecha_fin

### Tabla: Servicios
* idServicio (PK)
* nombre_servicio (agua, luz)

### Tabla: Departamento_servicios
* iddepartamento_servicio (PK)
* idDepartamento (FK)
* idServicio (FK)
* porcentaje
* costo_adicional

### Tabla: Pagos
* idPago (PK)
* idInquilino (FK)
* fecha_pago
* monto_pagado
* tipo_pago (completo / abono)

### Tabla: Adeudos
* idAdeudo (PK)
* idInquilino (FK)
* monto_total
* monto_pendiente
* fecha_limite
* estado

### Tabla: Visitas
* idVisita (PK)
* idUsuario (FK) (cobrador)
* idInquilino (FK)
* fecha_visita
* observaciones

### Tabla: Reportes
* idReporte (PK)
* idDepartamento (FK)
* descripcion
* fecha_reporte
* estado

### Tabla: Seguimiento
* idSeguimiento (PK)
* idReporte (FK)
* idUsuario (FK) (mantenimiento)
* fecha
* observaciones
