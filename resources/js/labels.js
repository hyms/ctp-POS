import {ref} from "vue";

export default {
    //forms
    client: {
        name: "Nombre",
        company_name: "Nombre de Empresa",
        code: "Codigo",
        email: "Correo",
        phone: "Telefono",
        city: "Ciudad",
        adresse: "Direccion",
        nit_ci: "NIT",
    },
    warehouse: {
        name: "Nombre",
        mobile: "Telefono",
        email: "Correo",
        country: "Pais",
        city: "Ciudad",
    },
    payment: {
        date: "Fecha",
        Ref: "Codigo Pago",
        montant: "A Pagar",
        amount: "Monto Pagado",
        change: "Cambio",
        received_amount: "Deuda",
        Reglement: "Forma de pago",
        notes: "Notas de Pago",
    },
    sale: {
        Ref: "Codigo Venta",
        client_id: "Cliente",
        warehouse_id: "Agencia",
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
        notes: "Notas de Venta",
        statut: "Estado",
        due: "Deuda",
        payment_status: "Estado de pago",
    },
    user: {
        firstname: "Nombre",
        lastname: "Apellido",
        username: "Usuario",
        password: "Contraseña",
        NewPassword: "Nueva Contraseña",
        email: "Correo",
        phone: "Telefono",
        ci: "CI",
        statut: "Estado",
        role: "Role",
        is_all_warehouses: "Todas las sucursales",
    },
    sales_type: {
        name: "Nombre",
        code: "Codigo",
    },
    category:{
        name: "Nombre",
        description: "Descripcion",
    },
    expense:{
        date: "Fecha",
        warehouse_id: "Agencia",
        category_id: "Categoria",
        details: "Detalle",
        amount: "Monto",
        ref:"Codigo"
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
    low_qty: "stock bajo",

    //messages
    success_message: "Processo exitoso",
    error_message: "Hubo un error en la transaccion",
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
    total_products: "Total de productos",
    total_sale_due: "Total deuda",
    filter_by_warehouse: "Filtrar por agencia",
    choose_warehouse: "Elija una agencia",
    actions: "Acciones",
    // this_week_sales_purchases:"Ventas y compras de la semana",
    this_week_sales_purchases: "Ventas de la semana",
    top_selling_products: "Productos más vendidos",
    top_customers: "Top 5 mejores clientes",
    stock_alert: "Alerta de stock",
    sales: "Ventas",
    product_code: "Código de producto",
    product_name: "Nombre de producto",
    warehouse_text: "Agencia",
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
    client_detail:"Detalle del Cliente",
}
