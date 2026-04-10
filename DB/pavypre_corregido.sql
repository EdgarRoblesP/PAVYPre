USE pavypre;

CREATE TABLE CLIENTES (
    id_cliente VARCHAR(6),
    nombre VARCHAR(50) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    direccion VARCHAR(50) NOT NULL,
    email VARCHAR(30) UNIQUE,
    contrasena VARCHAR(20) NOT NULL,
    CONSTRAINT cte_id_pk PRIMARY KEY (id_cliente)
);

CREATE TABLE OBRAS (
    id_obra VARCHAR(6),
    presupuesto_inicial DECIMAL(10,2) NOT NULL,
    utilidad_neta DECIMAL(10,2) NOT NULL,
    gasto_empleados DECIMAL(10,2) NOT NULL,
    gasto_insumos DECIMAL(10,2) NOT NULL,
    gasto_servicios DECIMAL(10,2) NOT NULL,
    gasto_herramientas DECIMAL(10,2) NOT NULL,
    fecha_inicio TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_fin TIMESTAMP,
    ubicacion VARCHAR(50) NOT NULL,
    CONSTRAINT oba_id_pk PRIMARY KEY (id_obra)
);

CREATE TABLE DISPOSICIONES (
    id_cliente VARCHAR(6) NOT NULL,
    id_obra VARCHAR(6) NOT NULL,
    CONSTRAINT dpe_id_pk PRIMARY KEY (id_cliente, id_obra),
    CONSTRAINT dpe_cte_fk FOREIGN KEY (id_cliente) REFERENCES CLIENTES (id_cliente),
    CONSTRAINT dpe_oba_fk FOREIGN KEY (id_obra) REFERENCES OBRAS (id_obra)
);

CREATE TABLE EMPLEADOS (
    id_empleado VARCHAR(6),
    nombre VARCHAR(50) NOT NULL,
    puesto VARCHAR(30) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    direccion VARCHAR(50) NOT NULL,
    email VARCHAR(30) UNIQUE,
    salario DECIMAL(10,2) NOT NULL,
    id_supervisor VARCHAR(6),
    contrasena VARCHAR(20) NOT NULL,
    CONSTRAINT epo_id_pk PRIMARY KEY(id_empleado),
    CONSTRAINT epo_epo_fk FOREIGN KEY (id_supervisor) REFERENCES EMPLEADOS (id_empleado)
);

CREATE TABLE SERVICIOS (
    id_servicio VARCHAR(6),
    costo_kilometro DECIMAL(10,2) NOT NULL,
    proveedor VARCHAR(50) NOT NULL,
    tipo_traslado VARCHAR(30) NOT NULL,
    CONSTRAINT svo_id_pk PRIMARY KEY(id_servicio)
);

CREATE TABLE INSUMOS (
    id_insumo VARCHAR(6),
    costo_unitario DECIMAL(10,2) NOT NULL,
    proveedor VARCHAR(50) NOT NULL,
    tipo_material VARCHAR(50) NOT NULL,
    CONSTRAINT iso_id_pk PRIMARY KEY(id_insumo)
);

CREATE TABLE HERRAMIENTAS (
    id_herramienta VARCHAR(6),
    nombre VARCHAR(50) NOT NULL,
    renta_diaria DECIMAL(10,2) NOT NULL,
    CONSTRAINT hra_id_pk PRIMARY KEY(id_herramienta)
);

CREATE TABLE COBROS (
    id_cobro INT AUTO_INCREMENT PRIMARY KEY,
    fecha_pago TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    monto DECIMAL(10,2) NOT NULL,
    tipo_pago VARCHAR(30) NOT NULL,
    id_cliente VARCHAR(6) NOT NULL,
    id_obra VARCHAR(6) NOT NULL,
    CONSTRAINT cbo_dpe_fk FOREIGN KEY (id_cliente, id_obra) REFERENCES DISPOSICIONES (id_cliente, id_obra),
    INDEX idx_obra (id_obra),
    INDEX idx_cliente (id_cliente)
);

use pavypre;


CREATE TABLE EMPLEOS_INSUMOS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_insumo VARCHAR(6) NOT NULL,
    fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cantidad INT NOT NULL,
    id_cliente VARCHAR(6) NOT NULL,
    id_obra VARCHAR(6) NOT NULL,
    CONSTRAINT empl_iso_fk FOREIGN KEY (id_insumo) REFERENCES INSUMOS (id_insumo),
    CONSTRAINT empl_dpe_fk FOREIGN KEY (id_cliente, id_obra) REFERENCES DISPOSICIONES (id_cliente, id_obra),
    UNIQUE KEY uk_insumo_obra (id_insumo, id_obra),
    INDEX idx_obra (id_obra)
);
 
CREATE TABLE TRABAJOS_EMPLEADOS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_empleado VARCHAR(6) NOT NULL,
    id_cliente VARCHAR(6) NOT NULL,
    id_obra VARCHAR(6) NOT NULL,
    fecha_adicion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_termino TIMESTAMP,
    salario DECIMAL(10,2) NOT NULL,
    CONSTRAINT trab_epo_fk FOREIGN KEY (id_empleado) REFERENCES EMPLEADOS (id_empleado),
    CONSTRAINT trab_dpe_fk FOREIGN KEY (id_cliente, id_obra) REFERENCES DISPOSICIONES (id_cliente, id_obra),
    UNIQUE KEY uk_empleado_obra (id_empleado, id_obra),
    INDEX idx_obra (id_obra),
    INDEX idx_empleado (id_empleado)
);
 
CREATE TABLE REQUERIMIENTOS_SERVICIOS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_servicio VARCHAR(6) NOT NULL,
    id_cliente VARCHAR(6) NOT NULL,
    id_obra VARCHAR(6) NOT NULL,
    kilometraje DECIMAL(10,2) NOT NULL,
    fecha TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT req_svo_fk FOREIGN KEY (id_servicio) REFERENCES SERVICIOS (id_servicio),
    CONSTRAINT req_dpe_fk FOREIGN KEY (id_cliente, id_obra) REFERENCES DISPOSICIONES (id_cliente, id_obra),
    INDEX idx_obra (id_obra),
    INDEX idx_servicio (id_servicio)
);
 
CREATE TABLE USOS_HERRAMIENTAS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_herramienta VARCHAR(6) NOT NULL,
    id_cliente VARCHAR(6) NOT NULL,
    id_obra VARCHAR(6) NOT NULL,
    fecha_adicion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_termino TIMESTAMP,
    cantidad INT NOT NULL,
    CONSTRAINT usos_dpe_fk FOREIGN KEY (id_cliente, id_obra) REFERENCES DISPOSICIONES (id_cliente, id_obra),
    CONSTRAINT usos_hra_fk FOREIGN KEY (id_herramienta) REFERENCES HERRAMIENTAS (id_herramienta),
    UNIQUE KEY uk_herramienta_obra (id_herramienta, id_obra),
    INDEX idx_obra (id_obra),
    INDEX idx_herramienta (id_herramienta)
);
 