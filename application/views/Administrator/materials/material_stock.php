<style>
    .v-select {
        background: #fff;
        border-radius: 4px !important;
    }

    .v-select .dropdown-toggle {
        padding: 0px;
        height: 25px;
        border: none;
    }

    .v-select input[type=search],
    .v-select input[type=search]:focus {
        margin: 0px;
    }

    .v-select .vs__selected-options {
        overflow: hidden;
        flex-wrap: nowrap;
    }

    .v-select .selected-tag {
        margin: 2px 0px;
        white-space: nowrap;
        position: absolute;
        left: 0px;
    }

    .v-select .dropdown-menu {
        width: auto;
        overflow-y: auto;
    }
</style>
<div id="materialStock">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12" style="margin: 0;">
            <fieldset class="scheduler-border scheduler-search">
                <legend class="scheduler-border">Stock Report</legend>
                <div class="control-group">
                    <div class="form-group">
                        <label class="col-md-1 control-label no-padding-right" style="font-size: 13px;"> Select Type </label>
                        <div class="col-md-2">
                            <v-select v-bind:options="searchTypes" v-model="selectedSearchType" label="text" v-on:input="onChangeSearchType"></v-select>
                        </div>
                    </div>

                    <div class="form-group" v-if="selectedSearchType.value == 'category'">
                        <div class="col-md-2">
                            <v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name"></v-select>
                        </div>
                    </div>

                    <div class="form-group" v-if="selectedSearchType.value == 'product'">
                        <div class="col-md-2">
                            <v-select v-bind:options="products" v-model="selectedProduct" label="display_text"></v-select>
                        </div>
                    </div>

                    <div class="form-group" v-if="selectedSearchType.value != 'current'">
                        <div class="col-md-2">
                            <input type="date" class="form-control" v-model="date">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-2">
                            <input type="button" style="padding: 1px 15px;" value="Show" v-on:click="getStock">
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="row" v-if="searchType != null" style="display:none" v-bind:style="{display: searchType == null ? 'none' : ''}">
        <div class="col-md-12 text-right">
            <a href="" v-on:click.prevent="print"><i class="fa fa-print"></i> Print</a>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive" id="reportContent">
                <table class="table table-bordered table-hover" v-if="searchType == 'current'" style="display:none" v-bind:style="{display: searchType == 'current' ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Material Name</th>
                            <th>Category</th>
                            <th>Current Quantity</th>
                            <th>Rate</th>
                            <th>Stock Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(product, sl) in stock">
                            <td>{{ sl+1 }}</td>
                            <td>{{ product.name }}</td>
                            <td>{{ product.category_name }}</td>
                            <td>{{ product.stock_quantity }} {{ product.Unit_Name }}</td>
                            <td style="text-align: right;">{{ product.purchase_rate | decimal }}</td>
                            <td style="text-align: right;">{{ product.stock_value | decimal }}</td>
                        </tr>
                        <tr>
                            <th colspan="3" style="text-align:right;">Total</th>
                            <th>{{ stock.reduce((prev, curr) => {return prev + parseFloat(curr.stock_quantity)}, 0) }}</th>
                            <th></th>
                            <th style="text-align: right;">{{ totalStockValue | decimal }}</th>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered" v-if="searchType != 'current' && searchType != null" style="display:none;" v-bind:style="{display: searchType != 'current' && searchType != null ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Sl.</th>
                            <th>Material Name</th>
                            <th>Category</th>
                            <th>Total Purchased</th>
                            <th>Total Sale</th>
                            <th>Used in Production</th>
                            <th>Damaged</th>
                            <th>Current Stock</th>
                            <th>Rate</th>
                            <th>Stock Value</th>
                        </tr>
                    </thead>
                    <tbody style="display:none;" v-bind:style="{display:stock.length > 0 ? '' : 'none'}">
                        <tr v-for="(material, sl) in stock">
                            <td>{{ sl+1 }}</td>
                            <td>{{ material.name }}</td>
                            <td>{{ material.category_name }}</td>
                            <td>{{ material.purchased_quantity }}</td>
                            <td>{{ material.sale_quantity }}</td>
                            <td>{{ material.production_quantity }}</td>
                            <td>{{ material.damage_quantity }}</td>
                            <td>{{ material.stock_quantity }} {{ material.unit_name}}</td>
                            <td style="text-align: right;">{{ material.purchase_rate | decimal }}</td>
                            <td style="text-align: right;">{{ material.stock_value | decimal }}</td>
                        </tr>
                        <tr>
                            <th colspan="3" style="text-align:right;">Total</th>
                            <th>{{ stock.reduce((prev, curr) => {return prev + parseFloat(curr.purchased_quantity)}, 0) }}</th>
                            <th>{{ stock.reduce((prev, curr) => {return prev + parseFloat(curr.sale_quantity)}, 0) }}</th>
                            <th>{{ stock.reduce((prev, curr) => {return prev + parseFloat(curr.production_quantity)}, 0) }}</th>
                            <th>{{ stock.reduce((prev, curr) => {return prev + parseFloat(curr.damage_quantity)}, 0) }}</th>
                            <th>{{ stock.reduce((prev, curr) => {return prev + parseFloat(curr.stock_quantity)}, 0) }}</th>
                            <th></th>
                            <th style="text-align: right;">{{ totalStockValue | decimal }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#materialStock',
        data() {
            return {
                searchTypes: [{
                        text: 'Current Stock',
                        value: 'current'
                    },
                    {
                        text: 'Total Stock',
                        value: 'total'
                    },
                    {
                        text: 'Category Wise Stock',
                        value: 'category'
                    },
                    {
                        text: 'Material Wise Stock',
                        value: 'product'
                    },
                ],
                selectedSearchType: {
                    text: 'Current Stock',
                    value: 'current'
                },
                searchType: null,
                date: moment().format('YYYY-MM-DD'),
                categories: [],
                selectedCategory: null,
                products: [],
                selectedProduct: null,
                stock: [],
                totalStockValue: 0,
            }
        },
        filters: {
            decimal(value) {
                return value == null ? '0.00' : parseFloat(value).toFixed(2);
            }
        },
        methods: {
            getStock() {
                this.searchType = this.selectedSearchType.value;
                let url = '';
                let parameters = {};

                if (this.searchType == 'current') {
                    url = '/get_material_stock';
                } else {
                    url = '/get_material_stock';
                    parameters.date = this.date;
                }

                this.selectionText = "";

                if (this.searchType == 'category' && this.selectedCategory == null) {
                    alert('Select a category');
                    return;
                } else if (this.searchType == 'category' && this.selectedCategory != null) {
                    parameters.categoryId = this.selectedCategory.ProductCategory_SlNo;
                    this.selectionText = "Category: " + this.selectedCategory.ProductCategory_Name;
                }

                if (this.searchType == 'product' && this.selectedProduct == null) {
                    alert('Select a product');
                    return;
                } else if (this.searchType == 'product' && this.selectedProduct != null) {
                    parameters.material_id = this.selectedProduct.material_id;
                    this.selectionText = "product: " + this.selectedProduct.display_text;
                }


                axios.post(url, parameters).then(res => {
                    if (this.searchType == 'current') {
                        this.stock = res.data.filter((pro) => pro.current_quantity != 0);
                    } else {
                        this.stock = res.data;
                    }
                    if (this.stock.length > 0) {
                        this.totalStockValue = this.stock.reduce((prev, curr) => {
                            return prev + parseFloat(curr.stock_quantity * curr.purchase_rate)
                        }, 0);
                    } else {
                        this.totalStockValue = 0
                    }
                })
            },
            onChangeSearchType() {
                if (this.selectedSearchType.value == 'category' && this.categories.length == 0) {
                    this.getCategories();
                } else if (this.selectedSearchType.value == 'product' && this.products.length == 0) {
                    this.getProducts();
                }
            },
            getCategories() {
                axios.get('/get_material_categories').then(res => {
                    this.categories = res.data;
                })
            },
            getProducts() {
                axios.post('/get_materials', {
                    isService: 'false'
                }).then(res => {
                    this.products = res.data;
                })
            },

            async print() {
                let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h3>Material Stock Report</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#reportContent').innerHTML}
							</div>
						</div>
					</div>
				`;

                var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}`);
                reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

                reportWindow.document.head.innerHTML += `
					<style>
						.container{
							width: 100%;
						}
					</style>
				`;
                reportWindow.document.body.innerHTML += reportContent;

                reportWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                reportWindow.print();
                reportWindow.close();
            }
        }
    })
</script>