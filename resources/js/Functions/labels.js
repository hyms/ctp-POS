const defaults = {
    name: "Nombre",
    phone: "Telefono",
    email: "Correo",
    code: "Codigo",
    city: "Ciudad",
    date: "Fecha",
    warehouse_text: "Agencia",
};
export default {
    //forms
    client: {
        name: defaults.name + " en Factura",
        company_name: defaults.name + " de Empresa",
        code: defaults.code,
        email: defaults.email,
        phone: defaults.phone,
        city: defaults.city,
        adresse: "Direccion",
        nit_ci: "NIT/CI",
        detail: "Detalle del Cliente",
    },
    warehouse: {
        name: defaults.name,
        mobile: defaults.phone,
        email: defaults.email,
        country: "Pais",
        city: defaults.city,
    },
    payment: {
        date: defaults.date,
        Ref: defaults.code + " de Pago",
        montant: "A Pagar",
        amount: "Monto Pagado",
        change: "Cambio",
        received_amount: "Deuda",
        Reglement: "Forma de pago",
        notes: "Notas de Pago",
    },
    sale: {
        Ref: defaults.code + "de Venta",
        client_id: "Cliente",
        warehouse_id: defaults.warehouse_text,
        discount: "Descuento",
        sales_type_id: "Tipo de Orden",
        taxe: "Impuesto",
        tax_rate: "rango impuesto",
        shipping: "Envio",
        GrandTotal: "Total",
        paid_amount: "Monto a Pagar",
        details: {
            product: "Producto",
            price: "Precio",
            qty: "Cantidad",
            sub_total: "SubTotal",
        },
        notes: "Notas de Orden",
        statut: "Estado de Trabajo",
        due: "Deuda",
        payment_status: "Estado de pago",
        date: defaults.date,
    },
    user: {
        firstname: defaults.name,
        lastname: "Apellido",
        username: "Usuario",
        password: "Contraseña",
        NewPassword: "Nueva Contraseña",
        email: defaults.email,
        phone: defaults.phone,
        ci: "CI",
        statut: "Estado",
        role: "Role",
        is_all_warehouses: "Todas las sucursales",
    },
    sales_type: {
        name: defaults.name,
        code: defaults.code,
    },
    category: {
        name: defaults.name,
        description: "Descripcion",
        code: "Codigo",
    },
    expense: {
        date: defaults.date,
        warehouse_id: defaults.warehouse_text,
        category_id: "Categoria",
        details: "Detalle",
        amount: "Monto",
        ref: defaults.code + " de Gasto",
    },
    product: {
        name: defaults.name + " de Producto",
        code: defaults.code + " de Producto",
        cost: "Precio Compra",
        price: "Precio Venta",
        category_id: "Categoria",
        TaxNet: "Factura",
        tax_method: "Metodo de Factura",
        unit_id: "Unidad",
        unit_sale_id: "Unidad de Venta",
        unit_purchase_id: "Unidad de Compra",
        stock_alert: "Alerta en Stock",
        note: "Notas del producto",
        is_variant: "El Producto tiene multiples variantes",
        not_selling: "Este Producto no es para la venta",
        add: "Añadir Producto",
        total: "Total de productos",
        qty: "Cantidad",
    },
    transfer: {
        date: defaults.date,
        Ref: defaults.code + " de Transferencia",
        from_warehouse: "de " + defaults.warehouse_text,
        to_warehouse: "a " + defaults.warehouse_text,
        items: "Items",
        GrandTotal: "Total",
        statut: "Estado",
        notes: "Notas de tranferencia",
    },
    transfer_detail: {
        product: "Producto",
        code: defaults.code,
        qty: "Cantidad",
        sub_total: "SubTotal",
        stock: "Stock",
    },
    adjustment: {
        notes: "Detalle",
        warehouse_id: "Agencia",
        product_id: "Producto",
        date: defaults.date,
        ref: defaults.code + " de Ajuste",
        items: "Items",
    },
    unit: {
        name: "Nombre",
        ShortName: "Nombre corto",
        base_unit_name: "Unidad base",
        operator: "Operador",
        operator_value: "Valor de Operacion",
    },
    //no data
    no_fill_data: "Debe llenar correctamente los datos",
    no_add_product: "debe adicionar un producto",
    no_add_qty: "debe añadir cantidades",
    no_select_warehouse: "Debe seleccionar una agencia",
    no_select_client: "Debe seleccionar un cliente",
    no_data_table: "No hay datos para mostrar",
    no_data_products: "No existen productos",

    //validations
    low_qty: "Queda poco Stock",

    //messages
    success_message: "Processo exitoso",
    error_message: "Hubo un error en la transaccion",
    warning_message: "Algo salio mal",
    delete_message: "Borrado exitoso",

    //buttons
    add_pay: "Agregar pago",
    list_category: "Lista de Categorias",
    filters: "Filtros",
    search: "Buscar",
    submit: "Guardar",
    cancel: "Cancelar",
    close: "Cerrar",
    add: "Añadir",
    print: "Imprimir",

    //others text
    total_sale_due: "Total deuda",
    filter_by_warehouse: "Filtrar por agencia",
    choose_warehouse: "Elija una agencia",
    actions: "Acciones",
    same_warehouse: "Agencias iguales",
    // this_week_sales_purchases:"Ventas y compras de la semana",
    this_week_sales_purchases: "Ventas de la semana",
    top_selling_products: "Productos más vendidos",
    top_customers: "Top 5 mejores clientes",
    top_expenses: "Top gastos mes",
    stock_alert: "Alerta de stock",
    sales: "Ventas",

    quantity: "Cantidad",
    alert_quantity: "Alerta de Cantidad",
    total_sales: "Ventas totales",
    total_amount: "monto",
    recent_sales: "Ventas Recientes",
    warehouse_assign: "Sucursales Asignadas",
    warehouse_some: "Algunas Sucursales",
    start_date: "Fecha desde",
    end_date: "Fecha hasta",
    payment_mayor_due: "El pago no puede ser mayor a la deuda",

    add_exist_item: "Ya esta agregado",
    no_add_item: "Agregue un producto a la lista",
    no_qty_add_item: "Agregue una cantidad a los productos",
};
