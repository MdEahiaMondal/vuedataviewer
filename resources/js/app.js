
require('./bootstrap');
import Vue from 'vue'

import VueProgressBar from 'vue-progressbar'
const VueProgressBarOptions = {
    color: '#5df599',
    failedColor: '#ff3939',
    thickness: '2px',
    transition: {
        speed: '0.2s',
        opacity: '0.6s',
        termination: 300
    },
    autoRevert: true,
    location: 'top',
    inverse: false
}
import Snotify, { SnotifyPosition } from 'vue-snotify'
const SnotifyOptions = {
    toast: {
        position: SnotifyPosition.rightTop
    }
}
Vue.use(Snotify, SnotifyOptions)


import { Form, HasError, AlertError } from 'vform'
window.Form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)



Vue.component('customer-component', require('./components/CustomersComponent').default);
Vue.component('paginate-component', require('./components/partials/PaginateComponent').default);



Vue.use(VueProgressBar, VueProgressBarOptions)

const app = new Vue({
    el: '#app',
});
