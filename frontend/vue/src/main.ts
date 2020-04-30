import Vue from 'vue';
import App from './MeetAgentCalendar.vue';
import {prettify} from '@/helpers/dateHelper';
import axios from 'axios';
import VueAxios from 'vue-axios';
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import {getCSRFToken} from '@/helpers/CSRFtoken';
import {setAxiosErrorHandler} from "@/helpers/axiosErrorHandler";

Vue.use(VueSweetalert2);
Vue.filter('prettify', prettify);
Vue.use(VueAxios, axios);
Vue.axios.defaults.headers.common['X-CSRF-TOKEN'] = getCSRFToken();
Vue.config.productionTip = false;
setAxiosErrorHandler(Vue);



const rootElement = document.getElementById('app');
if (rootElement) {
    const rootComponent = Vue.extend(App);
    const propsData = rootElement.dataset.props ? JSON.parse(rootElement.dataset.props) : {};
    new rootComponent({el: rootElement, propsData: propsData});
}
