import { router } from "@inertiajs/vue3";
import moment from "moment/moment";

const reglaments = [
    { title: "Efectivo", value: "cash" },
    { title: "Cheque", value: "cheque" },
    { title: "Transferencia Bancaria", value: "bank_transfer" },
    { title: "QR", value: "qr" },
    { title: "Otros", value: "other" },
];
const salesStatus = [
    { title: "Completado", value: "completed", color: "primary" },
    { title: "Pendiente", value: "pending", color: "secondary" },
    // { title: "Entregado", value: "ordered", color: "info" },
];
const paymentStatus = [
    { title: "Pagado", value: "paid", color: "success" },
    { title: "Deuda", value: "unpaid", color: "error" },
    { title: "Parcial", value: "partial", color: "warning" },
];
const transferStatus = [
    { title: "Completado", value: "completed", color: "success" },
    { title: "Enviado", value: "sent", color: "warning" },
    { title: "Pendiente", value: "pending", color: "error" },
];

const print_pos = (element_id) => {
    const pos_css = new URL("@/../css/pos_print.css", import.meta.url).href;
    let divContents = document.getElementById(element_id).innerHTML;
    let a = window.open("", "", "height=500, width=500");
    a.document.write(
        '<html lang="es"><link rel="stylesheet" href="' + pos_css + '">'
    );
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
    let url = "/pdf?body=" + encodeURIComponent(divContents);
    let win = window.open(url, "_blank");
    win.focus();
};

const linkVisit = (url, type = "get") => {
    router.visit(url, {
        method: type,
    });
};
const linkBack = () => {
    window.history.back();
};
const newLine = (value) => {
    return value.toString().replaceAll(" ", "<br>");
};

const formatNumber = (number, dec) => {
    const value = (
        typeof number === "string" ? number : (number * 1).toString()
    ).split(".");
    if (dec <= 0) return value[0];
    let formated = value[1] || "";
    if (formated.length > dec) return `${value[0]}.${formated.substr(0, dec)}`;
    while (formated.length < dec) formated += "0";
    return `${value[0]}.${formated}`;
};
const toArray = (object) => {
    let array = [];
    if (typeof object === "array" && object !== null) {
        return object;
    }
    if (typeof object === "object" && object !== null) {
        for (let val of Object.entries(object)) {
            array[val[0]] = val[1];
        }
    }
    return array;
};
const toArraySelect = (object) => {
    let array = [];
    if (typeof object === "object" && object !== null) {
        for (let val of Object.entries(object)) {
            array.push({ value: parseInt(val[0]), title: val[1] });
        }
    }
    return array;
};
const toArraySelectString = (object) => {
    let array = [];
    if (typeof object === "object" && object !== null) {
        for (let val of Object.entries(object)) {
            array.push({ value: val[0], title: val[1] });
        }
    }
    return array;
};
const statutTransfer = (value = null) => {
    if (value == null) {
        return transferStatus;
    }
    return transferStatus.find((item) => item.value === value).title;
};
const statutTransferColor = (value) => {
    return transferStatus.find((item) => item.value === value).color;
};
const statutSale = (value = null) => {
    if (value == null) {
        return salesStatus;
    }
    return salesStatus.find((item) => item.value === value).title;
};
const statutSaleColor = (value) => {
    return salesStatus.find((item) => item.value === value).color;
};
const statusPayment = (value = null) => {
    if (value == null) {
        return paymentStatus;
    }
    return paymentStatus.find((item) => item.value === value).title;
};
const statusPaymentColor = (value) => {
    return paymentStatus.find((item) => item.value === value).color;
};
const getReglamentPayment = (v) => {
    let result = reglaments.filter(
        (i) => i.value.toLowerCase() === v.toLowerCase()
    );
    if (result == null || result.length === 0) {
        result = [{ title: "", value: "" }];
    }
    return result;
};
const reglamentPayment = () => {
    return reglaments;
};
const resetForm = (form) => {
    for (let key of Object.keys(form)) {
        if (typeof form[key] === "object") {
            form[key] = [];
        }
        if (typeof form[key] === "string") {
            form[key] = "";
        }
        if (typeof form[key] === "number") {
            form[key] = 0;
        }
        if (typeof form[key] === "boolean") {
            form[key] = false;
        }
    }
};
const maxEnableButtons = (timeSale, enableDays) => {
    let time = moment(timeSale, "YYYY-MM-DD").add(
        moment.duration(enableDays, "d")
    );
    return moment().isAfter(time);
};
const getOperatorUnit = () => {
    return [
        { title: "Multiplicar (*)", value: "*" },
        { title: "Dividir (/)", value: "/" },
    ];
};
const formatDate = (date) => {
    return moment(date).format("DD-MM-YYYY");
};
export default {
    formatNumber,
    print_pdf,
    linkVisit,
    linkBack,
    newLine,
    toArray,
    toArraySelect,
    toArraySelectString,
    resetForm,
    maxEnableButtons,
    statutTransfer,
    statutTransferColor,
    statutSale,
    statutSaleColor,
    statusPayment,
    statusPaymentColor,
    getReglamentPayment,
    reglamentPayment,
    getOperatorUnit,
    print_pos,
    formatDate,
};
