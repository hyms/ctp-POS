export default {
    payment: {
        date: "Fecha",
        Ref: "Codigo",
        montant: "A Pagar",
        received_amount: "Deuda",
        Reglement: "Forma de pago",
        notes: "Detalle",
    },
    sale: {
        Ref: "Codigo",
        client_id: "Cliente",
        warehouse_id: "Agencia",
        discount: "Descuento",
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
        }
    },

    list_category: "Lista de Categorias",
    no_fill_data: "Debe llenar correctamente los datos",
    no_add_product: "debe adicionar un producto",
    no_add_qty: "debe añadir cantidades",
    no_select_warehouse: "Debe seleccionar una agencia",
    no_select_client: "Debe seleccionar un cliente",
    no_data_table: "No hay datos para mostrar",

    low_qty: "stock bajo",

    success_message: "Processo exitoso",
    error_message: "Hubo un error en la transaccion"
}
