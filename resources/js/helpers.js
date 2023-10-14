import moment from "moment";
import {router, usePage} from "@inertiajs/vue3";
import {computed} from "vue";
// import pos_css from "@/../css/pos_print.css";

let debug = true;
const reglaments = [
    {title: "Efectivo", value: "cash"},
    {title: "Cheque", value: "cheque"},
    {title: "Transferencia Bancaria", value: "bank_transfer"},
    {title: "QR", value: "qr"},
    {title: "Otros", value: "other"},
];
const salesStatus = [
    {title: "Completado", value: "completed", color: "primary"},
    {title: "Pendiente", value: "pending", color: "secondary"},
    {title: "Ordenado", value: "ordered", color: "info"},
];
const paymentStatus = [
    {title: "Pagado", value: "paid", color: "success"},
    {title: "Deuda", value: "unpaid", color: "error"},
    {title: "Parcial", value: "partial", color: "warning"},
];
const transferStatus = [
    {title: "Completado", value: "completed", color: "success"},
    {title: "Enviado", value: "sent", color: "warning"},
    {title: "Pendiente", value: "pending", color: "error"},
];

const toggleFullScreen = () => {
    let doc = window.document;
    let docEl = doc.documentElement;

    let requestFullScreen =
        docEl.requestFullscreen ||
        docEl.mozRequestFullScreen ||
        docEl.webkitRequestFullScreen ||
        docEl.msRequestFullscreen;
    let cancelFullScreen =
        doc.exitFullscreen ||
        doc.mozCancelFullScreen ||
        doc.webkitExitFullscreen ||
        doc.msExitFullscreen;

    if (
        !doc.fullscreenElement &&
        !doc.mozFullScreenElement &&
        !doc.webkitFullscreenElement &&
        !doc.msFullscreenElement
    ) {
        requestFullScreen.call(docEl);
    } else {
        cancelFullScreen.call(doc);
    }
};
const print_pos = (element_id) => {
    const pos_css = new URL('@/../css/pos_print.css', import.meta.url).href
    let divContents = document.getElementById(element_id).innerHTML;
    let a = window.open("", "", "height=500, width=500");
    a.document.write('<html><link rel="stylesheet" href="' + pos_css + '">');
    a.document.write("<body >");
    a.document.write(divContents);
    a.document.write("</body></html>");
    a.document.close();

    setTimeout(() => {
        a.print();
    }, 1000);
};
const print_pdf = (element_id, title) => {
    event.preventDefault();
    let divContents = document.getElementById(element_id).innerHTML;
    let url = "/pdf?body=" + divContents;
    let win = window.open(url, '_blank');
    win.focus();
};
//------------------------------Get Month -------------------------\\
const GetMonth = () => {
    let months = [
        "Enero",
        "Febrero",
        "Marzo",
        "Abril",
        "Mayo",
        "Junio",
        "Julio",
        "Agosto",
        "Septiembre",
        "Octubre",
        "Noviembre",
        "Diciembre"
    ];
    const now = new Date();
    return months[now.getMonth()];
}
const linkVisit = (url, type = "get") => {
    router.visit(url, {
        method: type,
        preserveState: true,
    });
}
const newLine = (value) => {
    return value.toString().replaceAll(' ', "<br>");
}

const verifyPermisions = () =>{
    const roles = computed(() => usePage().props.rolesP);
    const user = computed(() => usePage().props.user);
}
const maxEnableButtons = (timeSale,enableDays) =>{
    let time = moment(timeSale,"YYYY-MM-DD").add(moment.duration(enableDays,'d'));
    const rest = moment().isAfter(time);
    return rest;
}
export default {
    required: [(v) => !!v || "Requerido"],
    max: (max) => [
        (v) => {
            if (v !== null && v !== undefined && v.length > 0) {
                return (
                    v.length <= max ||
                    "Tiene que ser menor " + max + " caracteres"
                );
            }
            return true;
        },
    ],
    min: (min) => [
        (v) => {
            if (v !== null && v !== undefined && v.length > 0) {
                return (
                    v.length >= min ||
                    "Tiene que ser mayor " + min + " caracteres"
                );
            }
            return true;
        },
    ],
    number: [
        (v) => {
            if (v !== null && v !== undefined && v.length > 0) {
                return /[0-9]+$/.test(v) || "Solo numeros";
            }
            return true;
        },
    ],
    numberWithDecimal: [
        (v) => {
            if (v !== null && v !== undefined && v.length > 0) {
                return /^\d*\.?\d*$/.test(v) || "Solo numeros";
            }
            return true;
        },
    ],
    alpha: [
        (v) => {
            if (v !== null && v !== undefined && v.length > 0) {
                return /[a-zA-Z]+$/.test(v) || "Solo letras";
            }
            return true;
        },
    ],
    enableDay: (date) => {
        if (debug) {
            return false;
        }
        let diff = moment(date, "YYYY-MM-DD").add(2, "d");
        diff = moment(diff).diff(moment(), "days");
        return diff <= 0;
    },
    formatNumber: (number, dec) => {
        const value = (
            typeof number === "string" ? number : (number * 1).toString()
        ).split(".");
        if (dec <= 0) return value[0];
        let formated = value[1] || "";
        if (formated.length > dec)
            return `${value[0]}.${formated.substr(0, dec)}`;
        while (formated.length < dec) formated += "0";
        return `${value[0]}.${formated}`;
    },
    formatDate: (date) => {
        return moment(date).format("DD-MM-YYYY");
    },
    statutTransfer: (value = null) => {
        if (value == null) {
            return transferStatus;
        }
        return transferStatus.find(item => item.value == value).title
    },
    statutTransferColor: (value) => {
        return transferStatus.find(item => item.value == value).color;
    },
    statutSale: (value = null) => {
        if (value == null) {
            return salesStatus;
        }
        return salesStatus.find(item => item.value == value).title
    },
    statutSaleColor: (value) => {
        return salesStatus.find(item => item.value == value).color;
    },
    statusPayment: (value = null) => {
        if (value == null) {
            return paymentStatus;
        }
        return paymentStatus.find(item => item.value == value).title
    },
    statusPaymentColor: (value) => {
        return paymentStatus.find(item => item.value == value).color;
    },
    getReglamentPayment: (v) => {
        let result = reglaments.filter((i) => i.value.toLowerCase() == v.toLowerCase());
        if (result == null || result.length == 0) {
            result = [{title: "", value: ""}];
        }
        return result;
    },
    reglamentPayment: () => {
        return reglaments;
    },
    print_pos,
    print_pdf,
    toggleFullScreen,
    linkVisit,
    GetMonth,
    maxEnableButtons,
    getObjectValue: (value, object) => {
        return object.find(item => item.id == value)
    },
    getValueObject: (object) => {
        if (typeof object === 'object' && object !== null)
            return object.id
        return object;
    },
    newLine,
    getOperatorUnit() {
        return [
            {title: 'Multiplicar (*)', value: '*',},
            {title: 'Dividir (/)', value: '/'},
        ];
    },
    toExport(fields)
    {
        const res = fields.reduce((acc, curr) => (acc[curr].title = acc[curr].key, acc), {});
        return Object.assign({}, ...res);
    },

};
