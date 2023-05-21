import moment from "moment";
// import pos_css from "@/../css/pos_print.css";

let debug = true;
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
    statutSale: () => {
        return [
            {title: "Completado", value: "completed"},
            {title: "Pendiente", value: "pending"},
            {title: "Ordenado", value: "ordered"},
        ];
    },
    statusPayment: () => {
        return [
            {title: "Pagado", value: "paid"},
            {title: "Deuda", value: "unpaid"},
            {title: "Parcial", value: "partial"},
        ];
    },
    reglamentPayment: () => {
        return [
            {title: "Efectivo", value: "Cash"},
            {title: "Cheque", value: "cheque"},
            {title: "Transferencia Bancaria", value: "bank transfer"},
            {title: "Otros", value: "other"},
        ];
    },
    print_pos: () => {
        let pos_css = import('@/../css/pos_print.css?url');
        let divContents = document.getElementById("invoice-POS").innerHTML;
        let a = window.open("", "", "height=500, width=500");
        a.document.write('<html><link rel="stylesheet" href="' + pos_css + '">');
        a.document.write("<body >");
        a.document.write(divContents);
        a.document.write("</body></html>");
        a.document.close();

        setTimeout(() => {
            a.print();
        }, 1000);
    }
};
