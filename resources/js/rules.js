export default {
    required: [(v) => !!v || "Requerido"],
    max30: [
        (v) => {
            if (v !== null && v !== undefined && v.length > 0) {
                return v.length <= 30 || "Tiene que ser menor 30 caracteres";
            }
            return true;
        },
    ],
    min3: [
        (v) => {
            if (v !== null && v !== undefined && v.length > 0) {
                return v.length >= 3 || "Tiene que ser mayor 3 caracteres";
            }
            return true;
        },
    ],
    max14: [
        (v) => {
            if (v !== null && v !== undefined && v.length > 0) {
                return v.length <= 14 || "Tiene que ser menor 14 caracteres";
            }
            return true;
        },
    ],
    min6: [
        (v) => {
            if (v !== null && v !== undefined && v.length > 0) {
                return v.length >= 6 || "Tiene que ser mayor 6 caracteres";
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
    alpha: [
        (v) => {
            if (v !== null && v !== undefined && v.length > 0) {
                return /[a-zA-Z]+$/.test(v) || "Solo letras";
            }
            return true;
        },
    ],
};
