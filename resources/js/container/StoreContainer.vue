<template>
    <main class="px-2 py-2 mx-5">
        <navbar-component/>
        <section class="flex flex-row p-2">
            <category-component :categories="categories" :b-loading="bCategoryLoading" @searchProductByCategory="changeCategory" @searchByNameAndDescription="searchByNameAndDescription"/>
            <product-component :key="iSelectedCategory + sSearchByNameAndDesc" :category="iSelectedCategory" :search="sSearchByNameAndDesc"/>
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
        }
    },
    mounted() {
        this.fetchCategories();
    }
}
</script>

<style scoped>

</style>
