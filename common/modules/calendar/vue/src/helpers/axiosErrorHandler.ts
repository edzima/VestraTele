import {Vue, VueConstructor} from "vue/types/vue";

export function setAxiosErrorHandler(vueInstance: VueConstructor<Vue>): void {
    vueInstance.axios.interceptors.response.use(res => {
        // is ok
        return res;
    }, () => {
        // error
        handleAxiosError(vueInstance);
        return {};
    });
}

function handleAxiosError(vueInstance: VueConstructor<Vue>): void {
    vueInstance.swal({
        icon: 'error',
        title: 'Ups...',
        text: 'coś poszło nie tak!'
    });
}
