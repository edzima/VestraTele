import Vue from 'vue'
import App from './MeetAgentCalendar.vue'
import { prettify } from '@/helpers/dateHelper'
import axios, { AxiosInstance } from 'axios'
import VueAxios from 'vue-axios'
import VueSweetalert2 from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

Vue.use(VueSweetalert2)
Vue.filter('prettify', prettify)
Vue.use(VueAxios, axios)
Vue.config.productionTip = false
declare module 'vue/types/vue' {
  interface Vue {
    $axios: AxiosInstance;
  }
}

new Vue({
  render: h => h(App)
}).$mount('#app')
