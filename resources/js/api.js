import {router} from "@inertiajs/vue3";
import labels from "@/labels";

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
    if(params==undefined)
    {
        params={};
    }
    axios({
        method, url, data: params, params: (method == 'get' ? params : {}),
    })
        .then(({data}) => {
            snackbar.value.color = 'success';
            if (data['redirect'] != undefined) {
                snackbar.value.text = labels.success_message;
                if (data['redirect'] != '') {
                    router.get(data['redirect'], {}, {});
                } else {
                    router.reload({});
                }
            }
            if (localSuccess != undefined) {
                localSuccess(data);
            }
        })
        .catch(({response}) => {
            snackbar.value.color = 'error';
            snackbar.value.text = '';
            if (response.status >= 400 && response.status <= 499) {
                snackbar.value.text = response.data.message;
            }
            if (response.status >= 500 && response.status <= 599) {
                snackbar.value.text = labels.error_message;
                console.log(response);
            }
            if (localError != undefined) {
                localError(response.data);
            }
        })
        .finally(() => {
            setTimeout(() => {
                loading.value = false;
                if (snackbar.value.text !== '') {
                    snackbar.value.view = true;
                }
                if (localFinally != undefined) {
                    localFinally();
                }
            }, 1000);
        });
}


export default {
    put: ({
              url, params, loadingItem, snackbar, Success, Error, Finally
          }) => {
        global(
            'put',
            url,
            params,
            loadingItem,
            snackbar,
            Success,
            Error,
            Finally
        )
    }, post: ({
                  url, params, loadingItem, snackbar, Success, Error, Finally
              }) => {
        global(
            'post',
            url,
            params,
            loadingItem,
            snackbar,
            Success,
            Error,
            Finally
        )
    }, get: ({
                 url, params, loadingItem, snackbar, Success, Error, Finally
             }) => {
        global(
            'get',
            url,
            params,
            loadingItem,
            snackbar,
            Success,
            Error,
            Finally
        )
    }, delete: ({
                    url, params, loadingItem, snackbar, Success, Error, Finally
                }) => {
        global(
            'delete',
            url,
            params,
            loadingItem,
            snackbar,
            Success,
            Error,
            Finally
        )
    },

}
