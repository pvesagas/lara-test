<template>
    <section class="bg-gray-50 flex flex-col flex-nowrap w-[80%] h-[90vh] justify-start">
        <section v-if="bProductLoading === true">
            <div class="flex justify-center items-center flex-1 my-10">
                <div class="animate-spin radial-progress text-blue-200" style="--value:70; --size:12rem; --thickness: 2rem;"></div>
            </div>
        </section>
        <section v-if="bProductLoading === false && bEmptyProduct === false" class="flex justify-center mt-[2rem]">
            <nav aria-label="Page navigation example">
                <ul class="flex list-style-none">
                    <li v-for="links in pagination" class="page-item" :class="links.active === true ? 'active' : ''" :disabled="links.url === null">
                        <a v-if="links.active === false"
                           class="page-link relative block py-1.5 px-3 rounded border-0 bg-transparent outline-none transition-all duration-300 rounded text-gray-800 hover:text-gray-800 hover:bg-gray-200 focus:shadow-none"
                        @click="navigatePage(links.url)" href="javascript:;" v-html="links.label">
                            1
                        </a>
                        <a v-else
                            class="page-link relative block py-1.5 px-3 rounded border-0 bg-blue-600 outline-none transition-all duration-300 rounded text-white hover:text-white hover:bg-blue-600 shadow-md focus:shadow-md"
                           @click="navigatePage(links.url)" href="javascript:;" v-html="links.label">
                        </a>
                    </li>
                </ul>
            </nav>
        </section>
        <section class="bg-gray-50 ml-4 flex flex-row flex-wrap justify-start ml-[3.25rem]">
            <div v-if="bProductLoading === false && bEmptyProduct === true" class="hero min-h-screen bg-base-200">
                <div class="hero-content text-center">
                    <div class="max-w-md">
                        <h1 class="text-5xl font-bold">Product not found</h1>
                        <span class="py-6 mt-2" v-if="search.length > 0">
                            <span>
                                Product with
                            </span>
                            <span class="bg-gray-300 font-bold text-red-600 px-2 italic ml-1 rounded">
                               <code>{{ this.search }}</code>
                            </span>
                            <span>
                                as name or description not found
                            </span>
                        </span>

                    </div>
                </div>
            </div>

            <div v-if="bProductLoading === false && bEmptyProduct === false" v-for="product in products" class="card card-compact rounded w-[16rem] bg-base-100 shadow-xl m-3 p-2">
                <figure class="px-1 pt-[1rem]">
                    <img class="rounded h-[200px] w-100" :src="product.image_path === null ? '/img/no-image.jpg' : product.image_path " alt="Shoes" />
                </figure>
                <div  class="card-body justify-evenly">
                    <h2 class="card-title " v-text="product.name">Product Name </h2>
                    <h2 class="card-title flex-1 items-end" v-text="'$ ' + product.display_price"></h2>
                    <div v-if="cartItem.indexOf(product.id.toString()) === -1" @click="addCartItem(product.id, $event)" class="card-actions flex  flex-1 items-end mt-2 " :key="product.id">
                        <button class="btn btn-primary flex-1 px-0" >Add To Cart</button>
                    </div>
                    <div v-else @click="removeCartItem(product.id, $event)" class="card-actions flex  flex-1 items-end mt-2 " :key="product.id">
                        <button class="btn btn-error flex-1 px-0" >Remove To Cart</button>
                    </div>
                </div>
            </div>
        </section>

    </section>

</template>

<script>
const PRODUCT_API = '/api/product';
export default {
    props: ['category', 'search', 'cartItem'],
    name: "ProductComponent",
    data() {
        return {
            products: {},
            bProductLoading: true,
            bEmptyProduct: false,
            pagination: {}
        }
    },
    methods: {
        fetchProducts() {
            this.bProductLoading = true;
             let aParams = {
                 params: {
                     paginate: true,
                     limit: 10
                 }
             }

             if (this.category !== 0) {
                 aParams.params.category = this.category;
             }

             if(this.search.length !== '') {
                 aParams.params.search = this.search;
             }

             axios.get(PRODUCT_API, aParams)
                .then(oResponse => {
                    if (oResponse.data.result === true) {
                        this.products = oResponse.data.data.data;
                        this.pagination = oResponse.data.data.links;
                        this.bProductLoading = false;
                        this.bEmptyProduct = false;
                        return;
                    }

                    this.bEmptyProduct = true;
                    this.bProductLoading = false;
                })
                .catch(oError => {

                });
        },
        navigatePage(sLink) {
            axios.get(sLink)
                .then(oResponse => {
                    if (oResponse.data.result === true) {
                        this.products = oResponse.data.data.data;
                        this.pagination = oResponse.data.data.links;
                        this.bProductLoading = false;
                        this.bEmptyProduct = false;
                        return;
                    }

                    this.bEmptyProduct = true;
                    this.bProductLoading = false;
                })
                .catch(oError => {

                });
        },
        addCartItem(id, event) {
            let data = {
                product: id,
            };
            axios.post('/cart/add', data)
                .then(oResponse => {
                    this.$emit('updateCart');
                })
                .catch(oError => {

                });
        },
        removeCartItem(id, event) {
            axios.delete('/cart', {params: {product: id}})
                .then(oResponse => {
                    this.cartItem.splice(this.cartItem.indexOf(id.toString()), 0);
                    this.$emit('updateCart');
                })
                .catch(oError => {

                });
        },

    },
    beforeMount() {
        this.fetchProducts();
    },
    mounted() {
    }
}
</script>

<style scoped>

</style>
