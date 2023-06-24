export default {
    //forms
    payment: {
        date: "Fecha",
        Ref: "Codigo",
        montant: "A Pagar",
        amount:"Monto Pagado",
        change:"Cambio",
        received_amount: "Deuda",
        Reglement: "Forma de pago",
        notes: "Notas de Pago",
    },
    sale: {
        Ref: "Codigo",
        client_id: "Cliente",
        warehouse_id: "Agencia",
        discount: "Descuento",
        sales_type_id:"Tipo de Orden",
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
        notes:"Notas de Venta",
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

    //others text
    list_category: "Lista de Categorias",
    add_pay:"Agregar pago",
    total_products:"Total de productos",
    submit:"Guardar",
}
