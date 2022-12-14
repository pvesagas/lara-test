require('./bootstrap');

window.Vue = require('vue').default;

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('store-container', require('./container/StoreContainer.vue').default);
Vue.component('cart-container', require('./container/CartContainer.vue').default);
Vue.component('product-container', require('./container/ProductContainer.vue').default);

const app = new Vue({
    el: '#app',
});
