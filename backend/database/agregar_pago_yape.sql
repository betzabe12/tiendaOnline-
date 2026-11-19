USE fashioncloud;

ALTER TABLE pedidos
    MODIFY metodo_pago ENUM('por_confirmar', 'transferencia', 'efectivo', 'yape')
    NOT NULL DEFAULT 'por_confirmar';
