import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const rolePermision = (values) => {
    if (values === "all") {
        return true;
    }
    if (values !== undefined && typeof values === "object") {
        const userRoles = computed(() => usePage().props.userRoles);
        for (let value of values) {
            if (userRoles.value.includes(value)) {
                return true;
            }
        }
    }
    return false;
};
const userPermision = (values) => {
    if (values !== undefined && typeof values === "object") {
        const userPermisions = computed(() => usePage().props.userPermisions);
        // console.log(userPermisions);
        for (let value of values) {
            if (userPermisions.value.includes(value)) {
                return true;
            }
        }
    }
    return false;
};
const currency = () => {
    return computed(() => usePage().props.currency);
};
const oldDay = () => {
    return computed(() => usePage().props.day);
};
const rolesP = () => {
    const roles = computed(() => usePage().props.rolesP);
    return roles.value;
};
const fullName = () => {
    const user = computed(() => usePage().props.fullName);
    return user.value;
};
export default {
    rolePermision,
    userPermision,
    rolesP,
    fullName,
    currency,
    oldDay,
};
