import Vue from 'vue'
import App from './MeetAgentCalendar.vue'
import { prettify } from '@/helpers/dateHelper'
Vue.filter('prettify', prettify)

Vue.config.productionTip = false
new Vue({
  render: h => h(App)
}).$mount('#app')
