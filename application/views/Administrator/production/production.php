<style>
    .v-select {
        margin-bottom: 5px;
        background: #fff;
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

    .v-select .vs__actions {
        margin-top: -5px;
    }

    .v-select .dropdown-menu {
        width: auto;
        overflow-y: auto;
    }

    .add-button {
        padding: 2.8px;
        width: 100%;
        background-color: #d15b47;
        display: block;
        text-align: center;
        color: white;
        cursor: pointer;
        border-radius: 3px;
    }

    .add-button:hover {
        color: white;
    }
</style>
<div id="production">
    <div class="row">
        <div class="col-xs-12 col-md-9">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-12">
                            <fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;">
                                <legend class="scheduler-border">Material Information</legend>
                                <div class="control-group">
                                    <form id="materialForm" v-on:submit.prevent="addToCart">
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label no-padding-right"> Material </label>
                                            <div class="col-xs-9" style="display: flex;align-items:center;margin-bottom:5px;">
                                                <div style="width: 86%;">
                                                    <v-select v-bind:options="materials" id="material" style="margin: 0;" v-model="selectedMaterial" label="display_text" placeholder="Select Material" v-on:input="getMaterialStock"></v-select>
                                                </div>
                                                <div style="width: 13%;margin-left:2px;">
                                                    <a href="<?= base_url('materials') ?>" class="add-button" target="_blank" title="Add New Material"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group clearfix">
                                            <label class="col-xs-3">
                                                Quantity <span v-if="selectedMaterial.material_id != ''" style="display:none;" v-bind:style="{display: selectedMaterial.material_id != '' ? '' : 'none'}">({{ selectedMaterial.unit_name }})</span>
                                            </label>
                                            <div class="col-xs-4">
                                                <input type="text" ref="quantity" required class="form-control" placeholder="Quantity" v-model="selectedMaterial.quantity" @input="calculateMaterialTotal" />
                                            </div>
                                            <div class="col-xs-5" style="display: flex;gap:4px;">
                                                <span>Stock</span>
                                                <input type="text" disabled class="form-control" :value="stock_quantity">
                                            </div>
                                        </div>


                                        <div class="form-group clearfix">
                                            <label class="col-xs-3">Price</label>
                                            <div class="col-xs-4">
                                                <input type="text" required class="form-control" placeholder="Pur. Rate" v-model="selectedMaterial.purchase_rate" @input="calculateMaterialTotal" />
                                            </div>
                                            <div class="col-xs-5" style="display: flex;gap:4px;">
                                                <span>Total</span>
                                                <input type="text" required class="form-control" placeholder="Total" v-model="selectedMaterial.total" disabled />
                                            </div>
                                        </div>

                                        <div class="form-group clearfix">
                                            <label class="col-xs-4 control-label"></label>
                                            <div class="col-xs-8">
                                                <button type="submit" class="btn btnCart pull-right">Add to Cart</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </fieldset>
                        </div>

                        <div class="col-xs-12 col-md-12 col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" style="color:#000;margin-bottom: 5px;">
                                    <thead>
                                        <tr>
                                            <th style="width:4%;color:#000;">SL</th>
                                            <th style="width:20%;color:#000;">Material Name</th>
                                            <th style="width:13%;color:#000;">Category</th>
                                            <th style="width:5%;color:#000;">Qty</th>
                                            <th style="width:5%;color:#000;">Amount</th>
                                            <th style="width:10%;color:#000;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
                                        <tr v-for="(material, sl) in cart">
                                            <td>{{ sl + 1}}</td>
                                            <td>{{ material.name }}</td>
                                            <td>{{ material.category_name }}</td>
                                            <td>{{ material.quantity }} {{ material.unit_name }}</td>
                                            <td>{{ material.total }}</td>
                                            <td><a href="" v-on:click.prevent="removeFromCart(material)"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-md-6">
                    <div class="row">
                        <div class="col-xs-12">
                            <fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;">
                                <legend class="scheduler-border">Finished Products</legend>
                                <div class="control-group">
                                    <form id="productForm" v-on:submit.prevent="addToProductCart">
                                        <div class="form-group">
                                            <label class="col-xs-3 control-label no-padding-right"> <span v-html="selectedProduct.is_service == 'true' ? 'Service' : 'Product'"></span> </label>
                                            <div class="col-xs-9" style="display: flex;align-items:center;margin-bottom:5px;">
                                                <div style="width: 86%;">
                                                    <v-select v-bind:options="products" id="product" style="margin: 0;" v-model="selectedProduct" label="display_text" @input="onChangeProduct" @search="onSearchProduct"></v-select>
                                                </div>
                                                <div style="width: 13%;margin-left:2px;">
                                                    <a href="<?= base_url('product') ?>" class="add-button" target="_blank" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group clearfix">
                                            <label class="col-xs-3 control-label">Quantity</label>
                                            <div class="col-xs-9">
                                                <input type="text" class="form-control" placeholder="Quantity" ref="productQuantity" v-model="selectedProduct.quantity" required @input="calculateProductTotal">
                                            </div>
                                        </div>
                                        <div class="form-group clearfix">
                                            <label class="col-xs-3 control-label">Price</label>
                                            <div class="col-xs-4">
                                                <input type="text" class="form-control" v-model="selectedProduct.Product_Purchase_Rate" @input="calculateProductTotal">
                                            </div>
                                            <div class="col-xs-5">
                                                <input type="text" class="form-control" v-model="selectedProduct.total" disabled>
                                            </div>
                                        </div>
                                        <div class="form-group clearfix">
                                            <label class="col-xs-4 control-label"></label>
                                            <div class="col-xs-8">
                                                <button type="submit" class="btn btnCart pull-right">Add to Cart</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-xs-12 col-md-12 col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-bordered" style="color:#000;margin-bottom: 5px;">
                                    <thead>
                                        <tr>
                                            <th style="width:4%;color:#000;">SL</th>
                                            <th style="width:20%;color:#000;">Product Name</th>
                                            <th style="width:5%;color:#000;">Qty</th>
                                            <th style="width:5%;color:#000;">Amount</th>
                                            <th style="width:10%;color:#000;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody style="display:none;" v-bind:style="{display: cartProducts.length > 0 ? '' : 'none'}">
                                        <tr v-for="(product, sl) in cartProducts">
                                            <td>{{ sl + 1}}</td>
                                            <td>{{ product.name }}</td>
                                            <td>{{ product.quantity }} {{ product.unit_name }}</td>
                                            <td>{{ product.total }}</td>
                                            <td><a href="" v-on:click.prevent="removeFromProductCart(product)"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;">
                <legend class="scheduler-border">Amount Detail</legend>
                <div class="control-group">
                    <form v-on:submit.prevent="saveProduction">
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Production Invoice</label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control" disabled placeholder="Production Id" v-model="production.production_sl">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Date</label>
                            <div class="col-xs-12">
                                <input type="date" class="form-control" v-model="production.date" required>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label no-padding-right"> Incharge </label>
                            <div class="col-xs-12" style="display: flex;align-items:center;margin-bottom:5px;">
                                <div style="width: 86%;">
                                    <v-select label="display_name" v-bind:options="employees" style="margin: 0;" v-model="selectedEmployee" placeholder="Select Incharge"></v-select>
                                </div>
                                <div style="width: 13%;margin-left:2px;">
                                    <a href="<?= base_url('employee') ?>" class="add-button" target="_blank" title="Add New Material"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Shift</label>
                            <div class="col-xs-12">
                                <select class="form-control" v-model="production.shift" style="padding:0px 3px;" required>
                                    <option value="">Select Shift</option>
                                    <option v-for="shift in shifts" v-bind:value="shift.name">{{ shift.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Labour Cost</label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control" v-model="production.labour_cost" v-on:input="calculateTotal">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Material Cost</label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control" v-model="production.material_cost" disabled>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Other Cost</label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control" v-model="production.other_cost" v-on:input="calculateTotal">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label"><strong>Total Cost</strong></label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control" v-model="production.total_cost" readonly>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Note</label>
                            <div class="col-xs-12">
                                <textarea class="form-control" placeholder="Note" v-model="production.note"></textarea>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="col-xs-7 col-xs-offset-5">
                                <button type="submit" class="btn btn-sm pull-right" style="background: green !important;border: 0;border-radius: 5px;outline:none;" v-bind:disabled="productionInProgress ? true : false">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </fieldset>
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
        el: '#production',
        data() {
            return {
                production: {
                    production_id: parseInt('<?php echo $production_id; ?>'),
                    production_sl: '<?php echo $productionSl; ?>',
                    date: '',
                    incharge_id: '',
                    shift: '',
                    note: '',
                    labour_cost: 0.00,
                    material_cost: 0.00,
                    other_cost: 0.00,
                    total_cost: 0.00
                },
                employees: [],
                shifts: [],
                products: [],
                materials: [],
                selectedEmployee: null,
                selectedProduct: {
                    Product_SlNo: 0,
                    Product_Name: '',
                    display_text: 'Select Product',
                    quantity: '',
                    Product_Purchase_Rate: 0.00,
                    total: 0,
                },
                selectedMaterial: {
                    material_id: '',
                    name: 'Select Material',
                    purchase_rate: 0.00,
                    quantity: '',
                    total: 0,
                },
                cart: [],
                stock_quantity: 0,
                cartProducts: [],
                productionInProgress: false
            }
        },
        created() {
            this.getEmployees();
            this.getShifts();
            this.getProducts();
            this.getMaterials();
            this.production.date = moment().format('YYYY-MM-DD');

            if (this.production.production_id != 0) {
                this.getProduction();
            }
        },
        methods: {
            getEmployees() {
                axios.get('/get_employees')
                    .then(res => {
                        this.employees = res.data;
                    })
            },
            getShifts() {
                axios.get('/get_shifts')
                    .then(res => {
                        this.shifts = res.data;
                    })
            },
            getProducts() {
                axios.post('/get_products', {
                    isService: 'false',
                    forSearch: 'yes'
                }).then(res => {
                    this.products = res.data;
                })
            },
            async onSearchProduct(val, loading) {
                if (val.length > 2) {
                    loading(true);
                    await axios.post("/get_products", {
                            name: val,
                            isService: 'false'
                        })
                        .then(res => {
                            let r = res.data;
                            this.products = r.filter(item => item.status == 'a' && item.is_service == 'false');
                            loading(false)
                        })
                } else {
                    loading(false)
                    await this.getProducts();
                }
            },
            getMaterials() {
                axios.get('/get_materials')
                    .then(res => {
                        this.materials = res.data;
                    })
            },
            getMaterialStock() {
                if (this.selectedMaterial == null) {
                    this.selectedMaterial = {
                        material_id: '',
                        name: 'Select Material',
                        purchase_rate: 0.00,
                        quantity: '',
                        total: 0,
                    }
                    return;
                }
                if (this.selectedMaterial.material_id != '') {
                    axios.post('/get_material_stock', {
                            material_id: this.selectedMaterial.material_id
                        })
                        .then(res => {
                            this.stock_quantity = res.data[0].stock_quantity;
                        })

                    this.$refs.quantity.focus();
                }
            },
            calculateMaterialTotal() {
                this.selectedMaterial.total = this.selectedMaterial.quantity * this.selectedMaterial.purchase_rate;
            },
            addToCart() {
                if (parseFloat(this.selectedMaterial.quantity) > parseFloat(this.stock_quantity)) {
                    swal.fire({
                        icon: 'error',
                        title: 'Stock unavailable'
                    })
                    return;
                }
                let ind = this.cart.findIndex(m => m.material_id == this.selectedMaterial.material_id);
                if (ind > -1) {
                    this.cart[ind].quantity = parseFloat(this.cart[ind].quantity) + parseFloat(this.selectedMaterial.quantity);
                } else {
                    this.cart.push(this.selectedMaterial);
                }
                this.clearMaterial();
                this.calculateTotal();
            },
            removeFromCart(material) {
                let ind = this.cart.findIndex(m => m.material_id == material.material_id);
                if (ind > -1) {
                    this.cart.splice(ind, 1);
                    this.calculateTotal();
                }

            },
            clearMaterial() {
                this.selectedMaterial = {
                    material_id: '',
                    name: '',
                    purchase_rate: 0.00,
                    quantity: ''
                }
            },
            onChangeProduct() {
                if (this.selectedProduct == null) {
                    this.selectedProduct = {
                        Product_SlNo: 0,
                        Product_Name: '',
                        display_text: 'Select Product',
                        quantity: '',
                        Product_Purchase_Rate: 0.00,
                        total: 0,
                    }
                    return;
                }
                if (this.selectedProduct.Product_SlNo != 0) {
                    this.$refs.productQuantity.focus();
                }
            },
            calculateProductTotal() {
                this.selectedProduct.total = this.selectedProduct.quantity * this.selectedProduct.Product_Purchase_Rate;
            },
            addToProductCart() {
                if (this.selectedProduct == null || this.selectedProduct.Product_SlNo == 0) {
                    alert('Select product');
                    return;
                }

                let ind = this.cartProducts.findIndex(p => p.product_id == this.selectedProduct.Product_SlNo);
                if (ind > -1) {
                    this.cartProducts[ind].quantity = parseFloat(this.cartProducts[ind].quantity) + parseFloat(this.selectedProduct.quantity);
                } else {
                    let product = {
                        product_id: this.selectedProduct.Product_SlNo,
                        name: this.selectedProduct.Product_Name,
                        category_name: this.selectedProduct.ProductCategory_Name,
                        quantity: this.selectedProduct.quantity,
                        price: this.selectedProduct.Product_Purchase_Rate,
                        total: this.selectedProduct.total,
                    }
                    this.cartProducts.push(product);
                }

                this.clearProduct();
            },
            removeFromProductCart(product) {
                let ind = this.cartProducts.findIndex(p => p.product_id == product.product_id);
                if (ind > -1) {
                    this.cartProducts.splice(ind, 1);
                }
            },
            clearProduct() {
                this.selectedProduct = {
                    Product_SlNo: 0,
                    Product_Name: '',
                    display_text: 'Select Product',
                    quantity: '',
                    Product_Purchase_Rate: 0.00
                }
            },
            calculateTotal() {
                this.production.material_cost = this.cart.reduce((p, c) => {
                    return +p + +c.total
                }, 0);
                this.production.total_cost =
                    parseFloat(this.production.labour_cost) +
                    parseFloat(this.production.material_cost) +
                    parseFloat(this.production.other_cost);
            },
            saveProduction() {
                if (this.selectedEmployee == null) {
                    alert('Select production incharge');
                    return;
                }
                if (this.cart.length == 0) {
                    alert('Material cart is empty');
                    return;
                }
                if (this.cartProducts.length == 0) {
                    alert('Product cart is empty');
                    return;
                }

                this.production.incharge_id = this.selectedEmployee.Employee_SlNo;

                let url = '/add_production';
                if (this.production.production_id != 0) {
                    url = '/update_production';
                }

                let data = {
                    production: this.production,
                    materials: this.cart,
                    products: this.cartProducts
                }

                this.productionInProgress = true;
                axios.post(url, data).then(async res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        let conf = confirm('Production success, Do you want to view invoice?');
                        if (conf) {
                            window.open('/production_invoice/' + r.productionId, '_blank');
                            await new Promise(r => setTimeout(r, 1000));
                        }
                        window.location = '/production';
                    }
                })
            },
            async getProduction() {
                await axios.post('/get_productions', {
                        production_id: this.production.production_id
                    })
                    .then(res => {
                        this.production = res.data[0];
                        this.selectedEmployee = {
                            Employee_SlNo: this.production.incharge_id,
                            display_name: this.production.incharge_name + ' - ' + this.production.incharge_id,
                        }
                    })
                await axios.post('/get_production_details', {
                        production_id: this.production.production_id
                    })
                    .then(res => {
                        this.cart = res.data;
                    })

                await axios.post('/get_production_products', {
                        production_id: this.production.production_id
                    })
                    .then(res => {
                        this.cartProducts = res.data;
                    })
            }
        }
    })
</script>