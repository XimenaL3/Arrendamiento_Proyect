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

---

# Modelado de la base de datos

Para el desarrollo de la base de datos, se tomó en cuenta el uso de la tecnología de persistencia MySQL, debido a su facilidad de implementación, mantenimiento y a que es la herramienta con la que se cuenta de mayor experiencia.

Para su diseño, se siguieron buenas prácticas de programación, como la normalización de la base de datos hasta la Tercera Forma Normal (3FN), con el objetivo de evitar redundancias en la información y garantizar que los datos almacenados se mantengan limpios, organizados y consistentes.

Asimismo, se consideraron diferentes herramientas que pueden complementar el funcionamiento del sistema. Además de la estructura de la base de datos, se plantea el uso de vistas y procedimientos almacenados, los cuales permitirán implementar parte de la lógica de negocio directamente en el sistema gestor de base de datos, facilitando la gestión de operaciones como los cobros, abonos y consultas de información.

De igual manera, se contempla el uso de triggers para la automatización de procesos, como la actualización de saldos, el registro de movimientos, lo que contribuirá a mejorar la eficiencia y el control dentro del sistema.

---

# Interfaz de Administradores (Control Total)

### Panel Principal (Dashboard)
* **KPIs en tiempo real:** Gráficos de ingresos mensuales, porcentaje de ocupación y total de adeudos vigentes.
* **Alertas Críticas:** Notificaciones de propiedades en "Aspecto Legal" o mantenimientos urgentes vencidos.
* **Accesos Rápidos:** Botón para autorizar abonos pendientes y registro de nuevos contratos.  

### Gestión de Capital Humano y Clientes
* **Módulo de Trabajadores:** Lista con filtros por rol (Cajero, Cobrador, Mantenimiento) y gestión de credenciales de acceso.
* **Directorio de Inquilinos:** Visualización del Historial Crediticio (Bueno/Malo/Nuevo) para determinar automáticamente el depósito en nuevos contratos.  

### Administración de Propiedades y Servicios
* **Configurador de Inmuebles:** Definir si es Local, Casa o Edificio.
* **Matriz de Servicios:** Configurar si el cobro de agua/luz es por monto fijo (Locales) o por porcentaje de consumo (Edificios).
* **Inventario de Bodega:** Control de stock de insumos de limpieza y alertas de reabastecimiento.  

### Finanzas y Autorizaciones
* **Módulo de Aprobaciones:** Panel exclusivo para que el Admin genere las Autorizaciones de Abono que los cajeros podrán procesar.
* **Reportes Contables:** Generación y descarga (PDF/Excel) de estados de cuenta globales, filtrados por fecha, propiedad o inquilino.  

# Interfaz de Cajeros / Tiendas (Operativa de Cobro)

### Panel de Recepción
* **Buscador Universal:** Localizar inquilinos por nombre, ID o número de propiedad (independientemente del edificio donde estén).
* **Estatus de Pago:** Visualización de adeudos actuales y fechas límite.  

### Módulo de Transacciones
* **Cobro de Renta:** Procesamiento de pagos completos.
* **Validación de Abonos:** Campo para ingresar el ID de Autorización del Administrador; si no existe o expiró, el sistema bloquea la transacción.
* **Emisión de Comprobantes:** Generación de recibos digitales que especifican la tienda donde se realizó el pago.  

# Interfaz de Cobradores (Seguimiento de Campo)

### Ruta del Día
* **Mapa de Visitas:** Lista de inquilinos morosos organizada por ubicación geográfica o edificio.
* **Historial de Gestión:** Consultar observaciones de visitas previas para saber qué se acordó con el inquilino.  

### Registro de Actividad
* **Formulario de Visita:** Registro de fecha, hora y promesas de pago o quejas recibidas.
* **Levantamiento de Reportes:** Creación inmediata de reportes de mantenimiento o incidencias detectadas durante la visita.  

# Interfaz de Mantenimiento (Operación Técnica)

### Gestión de Órdenes de Trabajo
* **Bandeja de Entrada:** Lista de reportes pendientes clasificados por prioridad (Urgente, Medio, Bajo).
* **Control de Estados:** Botones para "Iniciar Atención", "Pausar" (con motivo) y "Finalizar".  

### Consumo y Evidencias
* **Uso de Insumos:** Selector para descontar del Inventario de Bodega los productos de limpieza o materiales utilizados en la reparación.
* **Registro de Evidencias:** Cámara integrada para subir fotos del "Antes" y "Después", marcando si el daño amerita seguimiento legal.
* **Historial de Propiedad:** Consultar mantenimientos pasados de un local o casa específica para identificar problemas recurrentes.

---

### Tabla: Personas
* idPersona (PK)
* Nombre
* ApellidoP
* ApellidoM
* Telefono
* Correo

### Tabla: Roles
* idRol (PK)
* NombreRol (Administrador, Cajero, Cobrador, Mantenimiento)

### Tabla: Usuarios
* idUsuario (PK)
* idPersona (FK)
* idRol (FK)
* Usuario
* Contraseña

### Tabla: Propiedades
* idPropiedad (PK)
* idEdificioPadre (FK) (Para agrupar locales/cuartos dentro de un edificio)
* TipoPropiedad (Local comercial / Casa / Edificio)
* NumeroIdentificador (Número de casa o local)
* Descripcion
* EstadoFisico (Buenas condiciones / Malas condiciones / En mantenimiento)
* EstadoDisponibilidad (Disponible / Rentado / Aspecto Legal)

### Tabla: Inquilinos
* idInquilino (PK)
* idPersona (FK)
* HistorialCrediticio (Bueno / Malo / Nuevo)
* RegistroDeudasPrevias (Si ha fallado en pagos antes)

### Tabla: Contratos_Arrendamiento
* idContrato (PK)
* idInquilino (FK)
* idPropiedad (FK)
* FechaInicio
* FechaFin
* MontoRenta
* MontoDeposito (Calculado según el historial del inquilino)

### Tabla: Servicios
* idServicio (PK)
* NombreServicio (Agua / Luz / Internet)

### Tabla: Propiedad_Servicios
* idPropiedadServicio (PK)
* idPropiedad (FK)
* idServicio (FK)
* ManejoPorPorcentaje (Booleano: Sí/No para consumo de luz en edificios)
* PorcentajeAsignado
* CostoFijo (Para servicios básicos incluidos en locales)

### Tabla: Tiendas_Cobro
* idTienda (PK)
* NombreTienda
* idPropiedad (FK) (Referencia al edificio donde se encuentra la tienda física)

### Tabla: Autorizaciones_Abono
* idAutorizacion (PK)
* idUsuario (FK) (Admin que autoriza)
* idInquilino (FK)
* MontoMinimoAceptado
* FechaExpiracionAutorizacion

### Tabla: Pagos
* idPago (PK)
* idContrato (FK)
* idTienda (FK) (Lugar donde se realizó el pago)
* idUsuario (FK) (Cajero que recibió el dinero)
* idAutorizacion (FK) (Obligatorio si es un abono)
* FechaPago
* MontoPagado
* TipoPago (Completo / Abono)

### Tabla: Adeudos
* idAdeudo (PK)
* idContrato (FK)
* MontoTotal
* MontoPendiente
* FechaLimite
* Estado (Pendiente / Liquidado)

### Tabla: Bodega_Inventario
* idProducto (PK)
* NombreProducto (Insumos de limpieza)
* CantidadDisponible
* Descripcion

### Tabla: Mantenimiento_Detalle
* idMantenimiento (PK)
* idPropiedad (FK)
* idUsuario (FK) (Encargado del mantenimiento)
* idProducto (FK) (Producto de limpieza/bodega utilizado)
* TareaRealizada
* FechaInicio
* FechaFin

### Tabla: Evidencias_Legales
* idEvidencia (PK)
* idPropiedad (FK)
* idInquilino (FK) (En caso de daños causados)
* DescripcionDano
* Fotografia (Ruta del archivo)
* FechaRegistro
* EsCasoLegal (Booleano)

### Tabla: Visitas_Cobranza
* idVisita (PK)
* idUsuario (FK) (Cobrador)
* idInquilino (FK)
* FechaVisita
* Observaciones
