<template>
    <main class="px-2 py-2 mx-5">
        <navbar-component :count="cartCount"/>
        <section class="flex flex-row p-2">
            <category-component :categories="categories" :b-loading="bCategoryLoading" @searchProductByCategory="changeCategory" @searchByNameAndDescription="searchByNameAndDescription"/>
            <product-component
                :key="iSelectedCategory + sSearchByNameAndDesc + cartCount"
                :category="iSelectedCategory"
                :search="sSearchByNameAndDesc"
                :cart-item="cartItem"
                @updateCart="updateCart"
            />
        </section>

    </main>
</template>

<script>
import NavbarComponent from "../components/NavbarComponent";
import CategoryComponent from "../components/CategoryComponent";
import ProductComponent from "../components/ProductComponent";
const CATEGORY_API = '/api/category';
export default {
    name: "StoreContainer",
    components: {ProductComponent, CategoryComponent, NavbarComponent},
    data() {
        return {
            categories: {},
            iSelectedCategory: 0,
            sSearchByNameAndDesc: '',
            bCategoryLoading: true,
            cartItem: [],
            cartCount: 0,
        }
    },

    methods: {
        changeCategory(iCategoryId) {
            this.iSelectedCategory = iCategoryId;
        },
        searchByNameAndDescription(sSearch) {
            this.sSearchByNameAndDesc = sSearch;
        },
        fetchCategories() {
            axios.get(CATEGORY_API)
                .then(oResponse => {
                    this.categories = oResponse.data.data;
                    this.bCategoryLoading = false;
                })
                .catch(oError => {

                });
        },
        fetchCart() {
            axios.get('/cart/items')
                .then(oResponse => {
                    this.cartItem = Object.keys(oResponse.data.data);
                    this.cartCount = this.cartItem.length;
                })
                .catch(oError => {

                });
        },
        updateCart() {
            this.fetchCart();
        }
    },
    mounted() {
        this.fetchCart();
        this.fetchCategories();
    }
}
</script>

<style scoped>

</style>
