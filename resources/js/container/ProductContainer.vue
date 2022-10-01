<template>
    <section class="flex flex-row p-2 container mx-auto mt-10">
        <category-component
            :categories="categories"
            :b-loading="bCategoryLoading"
            @searchProductByCategory="changeCategory"
            @searchByNameAndDescription="searchByNameAndDescription"/>
        <section class="bg-gray-50 ml-4 flex-1 flex flex-row flex-wrap justify-start ml-[3.25rem]">

            <div class="flex-1 justify-center p-5">
                <!-- The button to open modal -->
                <label for="" class="btn btn-success modal-button mb-2" @click="openAddProduct">Add product</label>
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="py-3 px-6">
                            Product name
                        </th>
                        <th scope="col" class="py-3 px-6">
                            Category
                        </th>
                        <th scope="col" class="py-3 px-6">
                            Price
                        </th>
                        <th scope="col" class="py-3 px-6">
                            Image
                        </th>
                        <th scope="col" class="py-3 px-6">
                            Date Added
                        </th>

                        <th scope="col" class="py-3 px-6">
                            Last Update
                        </th>

                        <th scope="col" class="py-3 px-6">
                            Action
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(product, index) in products" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th scope="row" class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap dark:text-white" v-text="product.name"></th>
                        <td class="py-4 px-6" v-text="product.category_name"></td>
                        <td class="py-4 px-6" v-text="product.price"></td>
                        <td class="py-4 px-6" >
                            <a v-if="product.image_path !== null" class="link link-primary" target="_blank" :href="product.image_path">View Image</a>
                            <p v-else>No Image available</p>
                        </td>
                        <td class="py-4 px-6" v-text="product.created_at"></td>
                        <td class="py-4 px-6" v-text="product.updated_at"></td>
                        <td class="py-4 px-6">
                            <div class="flex flex-row">
                                <button @click="openEditModal(index)" class="btn btn-success btn-xs mr-2">Edit</button>
                                <button @click="deleteProduct(index)" class="btn btn-error btn-xs">Delete</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <nav v-if="pagination.length > 0 && (pagination[pagination.length - 1].url !== null || pagination[0].url !== null)" aria-label="Page navigation example" class="mt-5">
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
            </div>
        </section>
        <!-- Put this part before </body> tag -->
        <input type="checkbox" id="my-modal-3" class="modal-toggle" />
        <div class="modal">
            <div class="modal-box relative">
                <label for="my-modal-3" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
                <!-- Modal header -->
                <div class="flex justify-between items-start p-5 rounded-t border-b">
                    <h3 class="text-xl font-semibold">
                        Add product
                    </h3>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <form>
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-full">
                                <label for="product-category" class="block mb-2 text-sm font-medium text-gray-900">Category</label>
                                <select class="select select-bordered w-full " v-model="addedCategory">
                                    <option value="0" selected>Select category </option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id" v-text="category.category"></option>
                                </select>
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label for="product-name" class="block mb-2 text-sm font-medium text-gray-900">Product Name</label>
                                <input v-model="addedProductName" type="text" name="product-name" id="product-name" class="shadow-lg-sm border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-2 focus:ring-fuchsia-50 focus:border-fuchsia-300 block w-full p-2.5" placeholder="Product name" required>
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Price</label>
                                <input v-model="addedProductPrice" type="number" name="price" id="price" class="shadow-lg-sm border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-2 focus:ring-fuchsia-50 focus:border-fuchsia-300 block w-full p-2.5" placeholder="0.00" required>
                            </div>
                            <div class="col-span-full">
                                <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Image Upload</label>
                                <label class="flex flex-col w-full h-32 rounded border-2 border-gray-300 border-dashed cursor-pointer hover:bg-gray-50">
                                    <div class="flex flex-col justify-center items-center pt-5 pb-6">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <p class="py-1 text-sm text-gray-600">Upload a file or drag and drop</p>
                                        <p class="text-md text-gray-500" v-text="filename"></p>
                                    </div>
                                    <input id="fileUpload" type="file" class="hidden" @change="onFileChange('#fileUpload')"/>
                                </label>
                            </div>
                            <div class="col-span-full">
                                <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Image Preview</label>
                                <figure class="px-1 pt-[1rem]">
                                    <img id="addImgPreview" class="rounded h-[200px] w-100"  src="/img/no-image.jpg" alt="Shoes" />
                                </figure>
                            </div>

                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="p-6 rounded-b border-t border-gray-200" @click="submitForm">
                    <button class="btn btn-success" type="button">Add product</button>
                </div>
            </div>
        </div>

        <!-- Put this part before </body> tag -->
        <input type="checkbox" id="edit-modal" class="modal-toggle" />
        <div class="modal">
            <div class="modal-box relative">
                <label for="edit-modal" class="btn btn-sm btn-circle absolute right-2 top-2">✕</label>
                <!-- Modal header -->
                <div class="flex justify-between items-start p-5 rounded-t border-b">
                    <h3 class="text-xl font-semibold">
                        Edit product
                    </h3>
                </div>
                <!-- Modal body -->
                <div class="p-6 space-y-6">
                    <form>
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-full">
                                <label for="product-category" class="block mb-2 text-sm font-medium text-gray-900">Category</label>
                                <select class="select select-bordered w-full " v-model="editCategory">
                                    <option v-for="category in categories" :key="category.id" :value="category.id" v-text="category.category"></option>
                                </select>
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label for="product-name" class="block mb-2 text-sm font-medium text-gray-900">Product Name</label>
                                <input v-model="editProductName" type="text" name="product-name" id="product-name" class="shadow-lg-sm border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-2 focus:ring-fuchsia-50 focus:border-fuchsia-300 block w-full p-2.5" placeholder="Product name" required>
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Price</label>
                                <input v-model="editProductPrice" type="number" name="price" id="price" class="shadow-lg-sm border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-2 focus:ring-fuchsia-50 focus:border-fuchsia-300 block w-full p-2.5" placeholder="0.00" required>
                            </div>
                            <div class="col-span-full">
                                <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Image Upload</label>
                                <label class="flex flex-col w-full h-32 rounded border-2 border-gray-300 border-dashed cursor-pointer hover:bg-gray-50">
                                    <div class="flex flex-col justify-center items-center pt-5 pb-6">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <p class="py-1 text-sm text-gray-600">Upload a file or drag and drop</p>
                                        <p class="text-md text-gray-500" v-text="filename"></p>
                                    </div>
                                    <input id="fileUploadEdit" type="file" class="hidden"  @change="onFileChange('#fileUploadEdit')" name="editImgPreview"/>
                                </label>
                            </div>
                            <div class="col-span-full">
                                <label for="price" class="block mb-2 text-sm font-medium text-gray-900">Image Preview</label>
                                <figure class="px-1 pt-[1rem]">
                                    <img id="editImgPreview" class="rounded h-[200px] w-100"  :src="this.editSrc === null ? '/img/no-image.jpg' : this.editSrc" alt="Shoes" />
                                </figure>
                            </div>

                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div @click="editProduct()" class="p-6 rounded-b border-t border-gray-200">
                    <button class="btn btn-success" type="button">Edit product</button>
                </div>
            </div>
        </div>
    </section>

</template>

<script>
import CategoryComponent from "../components/CategoryComponent";

const PRODUCT_API = '/api/product';
const CATEGORY_API = '/api/category';
export default {
    name: "ProductContainer",
    components: {CategoryComponent},
    data() {
        return {
            categories: {},
            search: '',
            limit: 15,
            iSelectedCategory: 0,
            sSearchByNameAndDesc: '',
            bCategoryLoading: true,
            products: {},
            bProductLoading: true,
            bEmptyProduct: false,
            pagination: [],
            filename: '',
            file: '',
            success: '',
            src: '',
            addedProductName: '',
            addedProductPrice: '',
            addedCategory: 0,
            editProductName: '',
            editProductPrice: 0.00,
            editCategory: 0,
            editSrc: null,
            editProductId: 0,
            editFile: '',
            editFilename: '',
        }
    },
    methods: {
        changeCategory(iCategoryId) {
            this.iSelectedCategory = iCategoryId;
            this.fetchProducts();
        },
        searchByNameAndDescription(sSearch) {
            this.sSearchByNameAndDesc = sSearch;
            this.fetchProducts();
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
        fetchProducts() {
            this.bProductLoading = true;
            let aParams = {
                params: {
                    paginate: true,
                    limit: this.limit
                }
            }

            if (this.iSelectedCategory !== 0) {
                aParams.params.category = this.iSelectedCategory;
            }

            if(this.sSearchByNameAndDesc !== '') {
                aParams.params.search = this.sSearchByNameAndDesc;
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
        onFileChange(target) {
            let e = document.querySelector(target);
            if (target === '#fileUpload') {
                this.filename = "Selected File: " + e.files[0].name;
                this.file = e.files[0];
            } else {
                this.editFilename = "Selected File: " + e.files[0].name;
                this.editFile = e.files[0];
            }

            if (e.files && e.files[0]) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    document.querySelector(target === '#fileUpload' ? '#addImgPreview' : '#editImgPreview').src = e.target.result;
                }
                reader.readAsDataURL(e.files[0]);
            }
        },
        submitForm() {
            let data = {
                name: this.addedProductName,
                price: this.addedProductPrice,
                category_no: this.addedCategory,
            }

            if (this.file) {
                let formData = new FormData();
                const config = {
                    headers: {
                        'content-type': 'multipart/form-data',
                    }
                }
                formData.append('image', this.file);
                axios.post('/imageUpload', formData, config)
                    .then(oResponse => {
                        data.image_path = oResponse.data.url;
                        this.addProduct(data);
                    })
                    .catch(oError => {

                    });
            } else {
                this.addProduct(data);
            }
        },
        addProduct(data) {
            axios.post('/api/product', data)
                .then(oResponse => {
                    if (oResponse.data.result === true) {
                        alert('Product added');
                    } else {
                        alert('Failed to add product')
                    }

                    this.addedProductName = '';
                    this.addedProductPrice = 0.00;
                    this.addedCategory = 0;
                    this.filename = '';
                    this.file = '';
                    document.querySelector('#my-modal-3').checked = false;
                    this.fetchProducts();
                })
                .catch(oError => {

                });
        },
        editProduct() {
            let data = {
                name: this.editProductName,
                price: this.editProductPrice,
                category_no : this.editCategory
            };

            if (this.editFile) {
                let formData = new FormData();
                const config = {
                    headers: {
                        'content-type': 'multipart/form-data',
                    }
                }
                formData.append('image', this.editFile);
                axios.post('/imageUpload', formData, config)
                    .then(oResponse => {
                        data.image_path = oResponse.data.url;

                        this.saveUpdate(data);
                    })
                    .catch(oError => {

                    });
            } else {
                this.saveUpdate(data);
            }

        },
        saveUpdate(data) {
            axios.put('/api/product/' + this.editProductId, data)
                .then(oResponse => {
                    if (oResponse.data.result === true) {
                        alert('Product updated');
                    } else {
                        alert('Failed to update product')
                    }
                    this.editFilename = '';
                    this.editFile = '';
                    this.editSrc = null;
                    document.querySelector('#edit-modal').checked = false;
                    this.fetchProducts();
                })
                .catch(oError => {

                });
        },
        openEditModal(index) {
            document.getElementById('editImgPreview').src = '/img/no-image.jpg';
            this.editFilename = '';
            this.editFile = '';
            this.editSrc = null;
            let product = this.products[index];
            this.editProductName = product.name;
            this.editProductPrice = product.price;
            this.editCategory = product.category_no;
            this.editSrc = product.image_path;
            this.editProductId = product.id;
            document.querySelector('#edit-modal').checked = true;
        },
        openAddProduct() {
            document.getElementById('addImgPreview').src = '/img/no-image.jpg';
            this.filename = '';
            this.file = '';
            this.addedProductName = '';
            this.addedProductPrice = '';
            this.addedCategory = 0;
            document.querySelector('#my-modal-3').checked = true;
        },
        deleteProduct(index) {
            let product = this.products[index];
            if(confirm('Delete product ' + product.name + '?')) {
                axios.delete('/api/product/' + product.id)
                    .then(oResponse => {
                        if (oResponse.data.result === true) {
                            alert('Product deleted');
                            this.$delete(this.products, index);
                        } else {
                            alert('Failed to delete product')
                        }
                    })
                    .catch(oError => {

                    });
            }
        }
    },
    beforeMount() {
        this.fetchCategories();
        this.fetchProducts();
    }
}
</script>

<style scoped>

</style>
