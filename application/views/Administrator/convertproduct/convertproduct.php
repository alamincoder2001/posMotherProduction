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
<div id="convert">
    <div class="row">
        <div class="col-md-9 col-xs-12">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;">
                        <legend class="scheduler-border">Out Product</legend>
                        <div class="control-group">
                            <form id="materialForm" v-on:submit.prevent="addToCart">
                                <div class="form-group">
                                    <label class="col-xs-4 control-label no-padding-right"> <span v-html="selectedOutProduct.is_service == 'true' ? 'Service' : 'Product'"></span> </label>
                                    <div class="col-xs-8" style="display: flex;align-items:center;margin-bottom:5px;">
                                        <div style="width: 86%;">
                                            <v-select v-bind:options="outProducts" id="product" style="margin: 0;" v-model="selectedOutProduct" label="display_text" @input="getOutProductStock" @search="onSearchOutProduct"></v-select>
                                        </div>
                                        <div style="width: 13%;margin-left:2px;">
                                            <a href="<?= base_url('product') ?>" class="add-button" target="_blank" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group clearfix">
                                    <label class="col-xs-4">
                                        Quantity <span v-if="selectedOutProduct.Product_SlNo != ''" style="display:none;" v-bind:style="{display: selectedOutProduct.Product_SlNo != '' ? '' : 'none'}">({{ selectedOutProduct.Unit_Name }})</span>
                                    </label>
                                    <div class="col-xs-3">
                                        <input type="text" ref="quantity" required class="form-control" placeholder="Quantity" v-model="selectedOutProduct.quantity" @input="calculateOutProduct" />
                                    </div>
                                    <div class="col-xs-5" style="display: flex;gap:4px;">
                                        <span>Stock</span>
                                        <input type="text" disabled class="form-control" :value="stock_quantity">
                                    </div>
                                </div>


                                <div class="form-group clearfix">
                                    <label class="col-xs-4">Price</label>
                                    <div class="col-xs-3">
                                        <input type="text" required class="form-control" placeholder="Pur. Rate" v-model="selectedOutProduct.Product_Purchase_Rate" @input="calculateOutProduct" />
                                    </div>
                                    <div class="col-xs-5" style="display: flex;gap:4px;">
                                        <span>Total</span>
                                        <input type="text" required class="form-control" placeholder="Total" v-model="selectedOutProduct.total" disabled />
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
                            <tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
                                <tr v-for="(product, sl) in cart">
                                    <td>{{ sl + 1}}</td>
                                    <td style="text-align: left;">{{ product.Product_Name }}</td>
                                    <td>{{ product.quantity }} {{ product.Unit_Name }}</td>
                                    <td>{{ product.total }}</td>
                                    <td><a href="" v-on:click.prevent="removeFromCart(product)"><i class="fa fa-trash"></i></a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6">
                    <fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;">
                        <legend class="scheduler-border">In Product</legend>
                        <div class="control-group">
                            <form id="productForm" v-on:submit.prevent="addToProductCart">
                                <div class="form-group">
                                    <label class="col-xs-4 control-label no-padding-right"> <span v-html="selectedProduct.is_service == 'true' ? 'Service' : 'Product'"></span> </label>
                                    <div class="col-xs-8" style="display: flex;align-items:center;margin-bottom:5px;">
                                        <div style="width: 86%;">
                                            <v-select v-bind:options="products" id="inproduct" style="margin: 0;" v-model="selectedProduct" label="display_text" @input="onChangeProduct" @search="onSearchProduct"></v-select>
                                        </div>
                                        <div style="width: 13%;margin-left:2px;">
                                            <a href="<?= base_url('product') ?>" class="add-button" target="_blank" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <label class="col-xs-4 control-label">Quantity</label>
                                    <div class="col-xs-8">
                                        <input type="text" class="form-control" placeholder="Quantity" ref="productQuantity" v-model="selectedProduct.quantity" required @input="calculateProductTotal">
                                    </div>
                                </div>
                                <div class="form-group clearfix">
                                    <label class="col-xs-4 control-label">Price</label>
                                    <div class="col-xs-4">
                                        <input type="text" class="form-control" v-model="selectedProduct.Product_Purchase_Rate" @input="calculateProductTotal">
                                    </div>
                                    <div class="col-xs-4">
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
        <div class="col-xs-12 col-md-3">
            <fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;">
                <legend class="scheduler-border">Amount Detail</legend>
                <div class="control-group">
                    <form v-on:submit.prevent="saveConvert">
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Convert Invoice</label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control" disabled v-model="convert.invoice">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Date</label>
                            <div class="col-xs-12">
                                <input type="date" class="form-control" v-model="convert.date" required>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Labour Cost</label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control" v-model="convert.labour_cost" v-on:input="calculateTotal">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Product Cost</label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control" v-model="convert.product_cost" disabled>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Other Cost</label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control" v-model="convert.other_cost" v-on:input="calculateTotal">
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label"><strong>Total Cost</strong></label>
                            <div class="col-xs-12">
                                <input type="text" class="form-control" v-model="convert.total_cost" readonly>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <label class="col-xs-12 control-label">Note</label>
                            <div class="col-xs-12">
                                <textarea class="form-control" placeholder="Note" v-model="convert.note"></textarea>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="col-xs-7 col-xs-offset-5">
                                <button type="submit" class="btn btn-sm pull-right" style="background: green !important;border: 0;border-radius: 5px;outline:none;" v-bind:disabled="convertInProgress ? true : false">Save</button>
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
        el: '#convert',
        data() {
            return {
                convert: {
                    id: parseInt('<?php echo $convertId; ?>'),
                    invoice: '<?php echo $invoice; ?>',
                    date: moment().format('YYYY-MM-DD'),
                    note: '',
                    labour_cost: 0.00,
                    product_cost: 0.00,
                    other_cost: 0.00,
                    total_cost: 0.00
                },
                products: [],
                outProducts: [],
                selectedProduct: {
                    Product_SlNo: 0,
                    Product_Name: '',
                    display_text: 'Select Product',
                    quantity: '',
                    Product_Purchase_Rate: 0,
                    total: 0,
                },
                selectedOutProduct: {
                    Product_SlNo: 0,
                    Product_Name: '',
                    display_text: 'Select Product',
                    quantity: '',
                    Product_Purchase_Rate: 0,
                    total: 0,
                },
                cart: [],
                stock_quantity: 0,
                cartProducts: [],
                convertInProgress: false
            }
        },
        created() {
            this.getEmployees();
            this.getProducts();
            this.getOutProducts();

            if (this.convert.id != 0) {
                this.getConvert();
            }
        },
        methods: {
            getEmployees() {
                axios.get('/get_employees')
                    .then(res => {
                        this.employees = res.data;
                    })
            },
            getProducts() {
                axios.post('/get_products', {forSearch: 'yes'})
                    .then(res => {
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
                            this.products = r.filter(item => item.status == 'a');
                            loading(false)
                        })
                } else {
                    loading(false)
                    await this.getProducts();
                }
            },
            getOutProducts() {
                axios.post('/get_products', {forSearch: 'yes'})
                    .then(res => {
                        this.outProducts = res.data;
                    })
            },
            async onSearchOutProduct(val, loading) {
                if (val.length > 2) {
                    loading(true);
                    await axios.post("/get_products", {
                            name: val,
                            isService: 'false'
                        })
                        .then(res => {
                            let r = res.data;
                            this.products = r.filter(item => item.status == 'a');
                            loading(false)
                        })
                } else {
                    loading(false)
                    await this.getOutProducts();
                }
            },
            getOutProductStock() {
                if (this.selectedOutProduct == null) {
                    this.selectedOutProduct = {
                        Product_SlNo: 0,
                        Product_Name: '',
                        display_text: 'Select Product',
                        quantity: '',
                        Product_Purchase_Rate: 0,
                        total: 0,
                    }
                    return;
                }
                if (this.selectedOutProduct.Product_SlNo != 0) {
                    axios.post('/get_product_stock', {
                            productId: this.selectedOutProduct.Product_SlNo
                        })
                        .then(res => {
                            this.stock_quantity = res.data;
                        })
                    this.$refs.quantity.focus();
                }
            },
            calculateOutProduct() {
                this.selectedOutProduct.total = this.selectedOutProduct.quantity * this.selectedOutProduct.Product_Purchase_Rate;
            },
            addToCart() {
                if (parseFloat(this.selectedOutProduct.quantity) > parseFloat(this.stock_quantity)) {
                    swal.fire({
                        icon: 'error',
                        title: 'Stock unavailable'
                    })
                    return;
                }
                let ind = this.cart.findIndex(m => m.Product_SlNo == this.selectedOutProduct.Product_SlNo);
                if (ind > -1) {
                    this.cart[ind].quantity = parseFloat(this.cart[ind].quantity) + parseFloat(this.selectedOutProduct.quantity);
                } else {
                    this.cart.push(this.selectedOutProduct);
                }
                this.clearOutProduct();
                this.calculateTotal();
            },
            removeFromCart(material) {
                let ind = this.cart.findIndex(m => m.Product_SlNo == material.Product_SlNo);
                if (ind > -1) {
                    this.cart.splice(ind, 1);
                    this.calculateTotal();
                }

            },
            clearOutProduct() {
                this.selectedOutProduct = {
                    Product_SlNo: 0,
                    Product_Name: '',
                    display_text: 'Select Product',
                    quantity: '',
                    Product_Purchase_Rate: 0,
                    total: 0,
                }
                this.stock_quantity = 0;
            },
            onChangeProduct() {
                if (this.selectedProduct == null) {
                    this.selectedProduct = {
                        Product_SlNo: 0,
                        Product_Name: '',
                        display_text: 'Select Product',
                        quantity: '',
                        Product_Purchase_Rate: 0.00,
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
                    swal.fire({
                        icon: 'error',
                        title: 'Select product'
                    })
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
                    Product_Purchase_Rate: 0
                }
            },
            calculateTotal() {
                this.convert.product_cost = this.cart.reduce((p, c) => {
                    return +p + +c.total
                }, 0);
                this.convert.total_cost =
                    parseFloat(this.convert.labour_cost) +
                    parseFloat(this.convert.product_cost) +
                    parseFloat(this.convert.other_cost);
            },
            saveConvert() {
                if (this.cart.length == 0) {
                    swal.fire({
                        icon: 'error',
                        title: 'In cart product is empty'
                    })
                    return;
                }
                if (this.cartProducts.length == 0) {
                    swal.fire({
                        icon: 'error',
                        title: 'Out cart product is empty'
                    })
                    return;
                }

                let url = '/add_convertproduct';
                if (this.convert.id != 0) {
                    url = '/update_convertproduct';
                }

                let data = {
                    convert: this.convert,
                    products: this.cartProducts,
                    outProducts: this.cart,
                }

                this.convertInProgress = true;
                axios.post(url, data).then(async res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        let conf = confirm('convert success, Do you want to view invoice?');
                        if (conf) {
                            window.open('/convertproduct_invoice/' + r.convertId, '_blank');
                            await new Promise(r => setTimeout(r, 1000));
                        }
                        window.location = '/convertproduct';
                    }
                })
            },
            async getConvert() {
                await axios.post('/get_convertproduct_master', {
                        convert_id: this.convert.id
                    })
                    .then(res => {
                        this.convert = res.data[0];
                        this.convert.total_cost = res.data[0].total;
                    })
                await axios.post('/get_convertproduct_details', {
                        convert_id: this.convert.id
                    })
                    .then(res => {
                        this.cart = res.data;
                    })

                await axios.post('/get_convert_products', {
                        convert_id: this.convert.id
                    })
                    .then(res => {
                        this.cartProducts = res.data;
                    })
            }
        }
    })
</script>