<template>
    <div class="container mx-auto mt-10">
        <div class="flex shadow-md my-10">
            <div class="w-3/4 bg-white px-10 py-10">
                <div class="flex justify-between border-b pb-8">
                    <h1 class="font-semibold text-2xl">Shopping Cart</h1>
                    <h2 class="font-semibold text-2xl">3 Items</h2>
                </div>
                <div class="flex mt-10 mb-5">
                    <h3 class="font-semibold text-gray-600 text-xs uppercase w-2/5">Product Details</h3>
                    <h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5 text-center">Quantity</h3>
                    <h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5 text-center">Price</h3>
                    <h3 class="font-semibold text-center text-gray-600 text-xs uppercase w-1/5 text-center">Total</h3>
                </div>
                <div v-for="(item, index) in cart" class="flex items-center hover:bg-gray-100 -mx-8 px-6 py-5" :key="index">
                    <div class="flex w-2/5"> <!-- product -->
                        <div class="w-20">
                            <img class="h-24" :src="item.img === null ? '/img/no-image.jpg' : item.img "  alt="">
                        </div>
                        <div class="flex flex-col justify-between ml-4 flex-grow">
                            <span class="font-bold text-sm" v-text="item.name">Item Name</span>
                            <a @click="removeCartItem(item.id)" href="javascript:;" class="font-semibold hover:text-red-500 text-gray-500 text-xs">Remove</a>
                        </div>
                    </div>
                    <div class="flex justify-center w-1/5">
                        <button @click="minusCount(item.id)" class="btn btn-outline btn-error btn-circle btn-xs">
                            <svg class="fill-current w-3" viewBox="0 0 448 512"><path d="M416 208H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h384c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/>
                            </svg>
                        </button>
                        <span class="mx-2 text-center w-8" type="text" v-text="item.count"></span>

                        <button @click="addCount(item.id)" class="btn btn-outline btn-success btn-circle btn-xs">
                            <svg class="fill-current w-3" viewBox="0 0 448 512">
                                <path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/>
                            </svg>
                        </button>
                    </div>
                    <span class="text-center w-1/5 font-semibold text-sm" v-text="'$ ' + item.price"></span>
                    <span class="text-center w-1/5 font-semibold text-sm" v-text="'$ ' + computeItemTotal(item.price,  item.count)">$400.00</span>
                </div>


                <a href="/" class="flex font-semibold text-indigo-600 text-sm mt-10">
                    <svg class="fill-current mr-2 text-indigo-600 w-4" viewBox="0 0 448 512"><path d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"/></svg>
                    Continue Shopping
                </a>
            </div>

            <div id="summary" class="w-1/4 px-8 py-10">
                <h1 class="font-semibold text-2xl border-b pb-8">Order Summary</h1>
                <div class="flex justify-between mt-10 mb-5">
                    <span class="font-semibold text-sm uppercase">Items <span v-text="getTotalItemCount"></span></span>
                    <span class="font-semibold text-sm">$ <span v-text="getTotalItemCost"></span></span>
                </div>
                <button class="bg-indigo-500 font-semibold hover:bg-indigo-600 py-3 text-sm text-white uppercase w-full">Checkout</button>
            </div>

        </div>
    </div>
</template>

<script>
export default {
    name: "CartContainer",
    data() {
       return {
           cart: {},
       }
    },
    computed: {
        getTotalItemCount() {
            if (this.cart.length === 0) return 0;
            let totalCount = 0;
            for (const id in this.cart) {
                totalCount += parseInt(this.cart[id].count);
            }

            return totalCount;
        },
        getTotalItemCost() {
            if (this.cart.length === 0) return 0;
            let totalCost = 0;
            for (const id in this.cart) {
                totalCost += this.cart[id].count * this.cart[id].price
            }

            return parseFloat((totalCost).toString()).toFixed(2);
        }
    },
    methods: {
        fetchCart() {
            axios.get('/cart/items')
                .then(oResponse => {
                    this.cart = oResponse.data.data;
                })
                .catch(oError => {

                });
        },
        addCount(id) {
            this.cart[id].count++;
            let data = {
                product: id,
                count: this.cart[id].count
            };

            this.updateCart(data);
        },
        minusCount(id) {
            if (this.cart[id].count === 1) {
                return false;
            }
            this.cart[id].count--;
            let data = {
                product: id,
                count: this.cart[id].count
            };

            this.updateCart(data);
        },
        updateCart(data) {
            axios.put('/cart', data)
                .then(oResponse => {
                })
                .catch(oError => {

                });
        },
        removeCartItem(id) {
            axios.delete('/cart', {params: {product: id}})
                .then(oResponse => {
                    this.$delete(this.cart, id)
                })
                .catch(oError => {

                });
        },
        computeItemTotal(price, qty) {
            return parseFloat((price * qty).toString()).toFixed(2);
        }
    },
    beforeMount() {
        this.fetchCart();
    }
}
</script>

<style scoped>

</style>
