import { router } from "@inertiajs/vue3";
import { labels, labelsNew } from "@/helpers";
import { ref } from "vue";

const snackbarDefault = ref({
    color: "",
    view: "",
    text: "",
});

const global = (
    method,
    url,
    params,
    loading,
    snackbar,
    localSuccess,
    localError,
    localFinally
) => {
    loading.value = true;
    if (params === undefined) {
        params = {};
    }
    if (snackbar === undefined) {
        snackbar = snackbarDefault;
    }
    axios({
        method,
        url,
        data: params,
        params: method === "get" ? params : {},
    })
        .then(({ data }) => {
            snackbar.value.color = "success";
            if (data["redirect"] !== undefined) {
                snackbar.value.text = labels.success_message;
                if (data["redirect"] !== "") {
                    router.get(data["redirect"], {}, {});
                } else {
                    router.reload();
                }
            }
            if (localSuccess !== undefined) {
                localSuccess(data);
            }
        })
        .catch((data) => {
            console.log(data);
            let response = data.response;
            snackbar.value.color = "error";
            snackbar.value.text = "";
            if (response.status >= 400 && response.status <= 499) {
                snackbar.value.text = response.data.message;
            }
            if (response.status >= 500 && response.status <= 599) {
                snackbar.value.text = labelsNew.InvalidData;
            }
            if (response.data["errors"]) {
                let errors = "";
                for (let err in response.data["errors"]) {
                    errors +=
                        "<li><strong>" +
                        err +
                        ":</strong> " +
                        response.data["errors"][err] +
                        "</li>";
                }
                snackbar.value.text = errors + "";
            }
            if (localError !== undefined) {
                localError(response.data);
            }
        })
        .finally(() => {
            setTimeout(() => {
                loading.value = false;
                if (snackbar.value.text !== "") {
                    snackbar.value.view = true;
                }
                if (localFinally !== undefined) {
                    localFinally();
                }
            }, 750);
        });
};

export default {
    put: ({
        url,
        params,
        loadingItem,
        snackbar,
        onSuccess,
        onError,
        onFinally,
    }) => {
        global(
            "put",
            url,
            params,
            loadingItem,
            snackbar,
            onSuccess,
            onError,
            onFinally
        );
    },
    post: ({
        url,
        params,
        loadingItem,
        snackbar,
        onSuccess,
        onError,
        onFinally,
    }) => {
        global(
            "post",
            url,
            params,
            loadingItem,
            snackbar,
            onSuccess,
            onError,
            onFinally
        );
    },
    get: ({
        url,
        params,
        loadingItem,
        snackbar,
        onSuccess,
        onError,
        onFinally,
    }) => {
        global(
            "get",
            url,
            params,
            loadingItem,
            snackbar,
            onSuccess,
            onError,
            onFinally
        );
    },
    delete: ({
        url,
        params,
        loadingItem,
        snackbar,
        onSuccess,
        onError,
        onFinally,
    }) => {
        global(
            "delete",
            url,
            params,
            loadingItem,
            snackbar,
            onSuccess,
            onError,
            onFinally
        );
    },
};
