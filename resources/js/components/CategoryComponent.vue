<template>
    <div class="relative flex flex-col flex-wrap bg-white px-2 rounded mt-[0.75rem] w-[20%] min-w-[20%] justify-start rounded">
        <div class="form-control flex rounded">
            <div class="input-group flex-1 rounded">
                <input v-model="sSearch" type="text" placeholder="Search by name, price, or description…" class="input input-bordered flex-1" />
                <button class="btn btn-square btn-outline" @click="defaultSearch">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
        <div class="overflow-auto h-[80vh] mt-2">

            <ul class="menu menu-vertical bg-base-100 flex-1 flex row">
                <li @click="searchProductByCategory(0, $event)" class="justify-center hover:bg-blue-100 cursor-pointer">
                    <a class="active hover:bg-transparent" >All Product</a>
                </li>
                <li v-if="bLoading === true" class="justify-center hover:bg-blue-100 cursor-pointer">
                    <div class="flex justify-center flex-1 my-10">
                        <div class="animate-spin radial-progress text-blue-200" style="--value:70; --size:6rem; --thickness: 1rem;"></div>
                    </div>
                </li>

                <li v-for="category in categories" @click="searchProductByCategory(category.id, $event)" class="justify-center hover:bg-blue-100 cursor-pointer">
                    <a :key="category.id" :id="'category-' + category.id" v-text="category.category" class="hover:bg-transparent"></a>
                </li>
            </ul>
        </div>
    </div>

</template>

<script>
    import { useDebouncedRef } from "../debounceRef";
    export default {
        props: ['categories', 'bLoading'],
        methods: {
            searchProductByCategory(iCategoryId, oEvent) {
                this.$emit('searchProductByCategory', iCategoryId);
                this.iSelectedCategory = iCategoryId;
                document.querySelector('li > a.active').classList.remove('active');
                oEvent.target.classList.add('active');
            },
            searchByNameAndDescription() {
                this.$emit('searchByNameAndDescription', this.sSearch);
            },
            defaultSearch() {
                this.sSearch = useDebouncedRef('', this.searchByNameAndDescription , 1000);
                this.$emit('searchByNameAndDescription', this.sSearch);
            }
        },
        data() {
            return {
                sSearch: useDebouncedRef('', this.searchByNameAndDescription , 1000),
                iSelectedCategory: 0
            }
        }
    }
</script>

<style scoped>
    .active {
        border-bottom: 2px solid rgb(219 234 254);
    }
</style>
