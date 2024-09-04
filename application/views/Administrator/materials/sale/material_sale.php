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
</style>
<div id="materialSale">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <fieldset class="scheduler-border entryFrom">
                <div class="control-group">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-1 control-label no-padding-right" for="age"> Invoice no </label>
                            <div class="col-md-2">
                                <input type="text" id="purchInvoice" name="purchInvoice" class="form-control" readonly v-model="sale.invoice_no" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label no-padding-right" for="saleFor"> Sale For </label>
                            <div class="col-md-3">
                                <select class="chosen-select form-control" name="saleFor" id="saleFor">
                                    <option value="<?php echo $this->session->userdata('BRANCHid'); ?>">
                                        <?php echo $this->session->userdata('Brunch_name'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label no-padding-right" for="sale_date"> Date </label>
                            <div class="col-md-3">
                                <input class="form-control" id="sale_date" name="sale_date" type="date"
                                    class="form-control" v-model="sale.sale_date" />
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="col-xs-9 col-md-9 col-lg-9">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;">
                        <legend class="scheduler-border">Customer Information</legend>
                        <div class="control-group">
                            <div class="form-group">
                                <label class="col-md-4 control-label no-padding-right" for="CustomerId"> Customer ID
                                </label>
                                <div class="col-md-7">
                                    <v-select label="display_name" v-bind:options="customers"
                                        v-model="selectedCustomer" placeholder="Select Customer" v-on:input="onChangeCustomer"></v-select>
                                </div>
                                <div class="col-md-1" style="padding: 0;">
                                    <a href="customer" title="Add New Customer" class="btn btn-xs btn-danger"
                                        style="height: 25px; border: 0; width: 27px; margin-left: -10px;"
                                        target="_blank"><i class="fa fa-plus" aria-hidden="true"
                                            style="margin-top: 5px;"></i></a>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label no-padding-right"> Name </label>
                                <div class="col-md-8">
                                    <input type="text" placeholder="Customer Name" class="form-control" v-model="selectedCustomer.Customer_Name" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label no-padding-right"> Mobile No </label>
                                <div class="col-md-8">
                                    <input type="text" placeholder="Mobile No" class="form-control" v-model="selectedCustomer.Customer_Mobile" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? false : true" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label no-padding-right"> Address </label>
                                <div class="col-md-8">
                                    <textarea class="form-control" v-model="selectedCustomer.Customer_Address" v-bind:disabled="selectedCustomer.Customer_Type == 'G' ? false : true"></textarea>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="col-xs-12 col-md-5">
                    <fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;">
                        <legend class="scheduler-border">Material Information</legend>
                        <div class="control-group">
                            <div class="form-group">
                                <label class="col-md-4 control-label no-padding-right" for="patient_id"> Material
                                </label>
                                <div class="col-md-7">
                                    <v-select label="name" v-bind:options="materials" v-on:input="setFocus"
                                        v-model="selectedMaterial" placeholder="Select Material"></v-select>
                                </div>
                                <div class="col-md-1" style="padding: 0;">
                                    <a href="materials" title="Add New Material" class="btn btn-xs btn-danger"
                                        style="height: 25px; border: 0; width: 27px; margin-left: -10px;"
                                        target="_blank"><i class="fa fa-plus" aria-hidden="true"
                                            style="margin-top: 5px;"></i></a>
                                </div>
                            </div>

                            <form id="MaterialsResult" v-on:submit.prevent="addToCart">
                                <div class="form-group">
                                    <label class="col-md-4 control-label no-padding-right" for="materialName">
                                        Name
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="materialName" name="materialName"
                                            placeholder="Material Name" class="form-control" readonly v-model="selectedMaterial.name" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label no-padding-right" for="MaterialRATE"> Sale Rate
                                    </label>
                                    <div class="col-md-3">
                                        <input type="text" id="saleRate" name="saleRate"
                                            class="form-control" placeholder="Sale Rate" v-model="selectedMaterial.sale_rate" />
                                    </div>

                                    <label class="col-md-2 control-label no-padding-right" for="saleQTY">
                                        Qty
                                    </label>
                                    <div class="col-md-3">
                                        <input type="text" id="saleQTY" name="saleQTY" ref="quantity" required
                                            class="form-control" placeholder="Quantity" v-model="selectedMaterial.quantity" v-on:input="materialTotal" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label no-padding-right" for="totalAmount"> Total </label>
                                    <div class="col-md-8">
                                        <input type="text" id="totalAmount" name="totalAmount"
                                            class="form-control" readonly v-model="selectedMaterial.total" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label no-padding-right"> </label>
                                    <div class="col-md-8">
                                        <button type="submit" class="btn btnCart pull-right">Add Cart</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </fieldset>

                </div>

                <div class="col-xs-12 col-md-1 no-padding" style="height: 164px;background: #93d2f5;border: 1px solid gray;margin-top: 10px;border-radius: 5px;">
                    <div style="height: 169px;display:flex;flex-direction:column;justify-content:center;">
                        <div class="text-center" style="display:none;font-size: 10px;line-height: 1;margin-bottom: 3px;" v-bind:style="{color: productStock > 0 ? 'green' : 'red', display: selectedMaterial.Product_SlNo == '' ? 'none' : ''}">{{ productStockText }}</div class="text-center">

                        <input type="text" id="productStock" v-model="productStock" readonly style="border:none;font-size:13px;width:100%;text-align:center;color:green"><br>
                        <input type="text" id="stockUnit" v-model="selectedMaterial.Unit_Name" readonly style="border:none;font-size:12px;width:100%;text-align: center;margin-bottom:2px;"><br>
                        <input type="password" ref="productPurchaseRate" v-model="selectedMaterial.purchase_rate" v-on:mousedown="toggleProductPurchaseRate" v-on:mouseup="toggleProductPurchaseRate" readonly title="Purchase rate (click & hold)" style="font-size:12px;width:100%;text-align: center;">
                    </div>
                </div>
                <div class="col-xs-12 col-md-12 col-lg-12 no-padding-right">
                    <div class="table-responsive">
                        <table class="table table-bordered" cellspacing="0" cellpadding="0"
                            style="color:#000;margin-bottom: 5px;">
                            <thead>
                                <tr>
                                    <th style="width:4%;color:#000;">Sl.</th>
                                    <th style="width:20%;color:#000;">Material Name</th>
                                    <th style="width:13%;color:#000;">Category</th>
                                    <th style="width:8%;color:#000;">Rate</th>
                                    <th style="width:5%;color:#000;">Qty</th>
                                    <th style="width:13%;color:#000;">Total</th>
                                    <th style="width:10%;color:#000;">Action</th>
                                </tr>
                            </thead>
                            <tbody style="display:none;" v-bind:style="{display: cart.length > 0 ? '' : 'none'}">
                                <tr v-for="(material, sl) in cart">
                                    <td>{{ sl+1 }}</td>
                                    <td>{{ material.name }}</td>
                                    <td>{{ material.category_name }}</td>
                                    <td>{{ material.sale_rate }}</td>
                                    <td>{{ material.quantity }}</td>
                                    <td>{{ material.total }}</td>
                                    <td><i class="fa fa-trash text-danger" style="cursor: pointer;" v-on:click="removeFromCart(material)"></i></button></td>
                                </tr>

                                <tr v-if="cart.length > 0">
                                    <td colspan="7"></td>
                                </tr>
                                <tr v-if="cart.length > 0">
                                    <td colspan="3">Notes</td>
                                    <td colspan="4">Total</td>
                                </tr>
                                <tr v-if="cart.length > 0">
                                    <td colspan="3"><textarea style="width: 100%;height:100%;" v-model="sale.note"></textarea></td>
                                    <td colspan="4" style="font-size:18px;font-weight: bold;">tk. {{ sale.sub_total }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-3 col-md-3 col-md-3 col-lg-3">
            <fieldset class="scheduler-border" style="margin-bottom: 5px;padding-bottom: 5px">
                <legend class="scheduler-border">Amount Details</legend>
                <div class="control-group">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <form @submit.prevent="saveSale">
                                    <table class="" cellspacing="0" cellpadding="0"
                                        style="color:#000;margin-bottom: 0px;">
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <label class="col-md-12 control-label no-padding-right"
                                                        for="subTotalDisabled">Sub Total</label>
                                                    <div class="col-md-12">
                                                        <input type="number" id="subTotalDisabled"
                                                            name="subTotalDisabled" class="form-control"
                                                            readonly v-model="sale.sub_total" />
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <label class="col-xs-12 control-label no-padding-right" style="margin:0;">Vat</label>
                                                    <div class="col-xs-4 no-padding-right">
                                                        <input type="number" id="vatPersent" name="vatPersent"
                                                            class="form-control" style="width: 100%;" v-model="vatPercent" v-on:input="calculateTotal" />
                                                    </div>
                                                    <label class="col-xs-1 control-label no-padding-right">%</label>
                                                    <div class="col-xs-7">
                                                        <input type="number" id="purchVat" readonly="" name="purchVat" class="form-control" v-model="sale.vat" />
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <label class="col-md-12 control-label no-padding-right"
                                                        for="subTotalDisabled">Transport / Labour Cost</label>
                                                    <div class="col-md-12">
                                                        <input type="number" id="purchFreight" name="purchFreight"
                                                            class="form-control" v-model="sale.transport_cost" v-on:input="calculateTotal" />
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <label class="col-md-12 control-label no-padding-right"
                                                        for="subTotalDisabled">Discount</label>
                                                    <div class="col-md-12">
                                                        <input type="number" id="purchDiscount" name="purchDiscount"
                                                            class="form-control" v-model="sale.discount" v-on:input="calculateTotal" />
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <label class="col-md-12 control-label no-padding-right"
                                                        for="subTotalDisabled">Total</label>
                                                    <div class="col-md-12">
                                                        <input type="number" id="purchTotaldisabled"
                                                            class="form-control" readonly v-model="sale.total" />
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <label class="col-md-12 control-label no-padding-right"
                                                        for="subTotalDisabled">Paid</label>
                                                    <div class="col-md-12">
                                                        <input type="number" id="PurchPaid"
                                                            class="form-control" v-model="sale.paid" v-on:input="calculateTotal" />
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <label class="col-xs-12 control-label" style="margin:0;">Due</label>
                                                    <div class="col-xs-6">
                                                        <input type="number" id="saleDue2" name="saleDue2" class="form-control" readonly v-model="sale.due" />
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <input type="number" id="previousDue" name="previousDue" class="form-control" v-model="sale.previous_due" readonly style="color:red;" />
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <div class="col-xs-6 col-md-6" style="display: block;width: 50%;">
                                                        <input type="submit" class="btn" value="Sale" style="width:100%;background: green !important;border: 0;border-radius: 5px;outline:none;" v-bind:disabled="saleInProgress ? true : false" />
                                                    </div>
                                                    <div class="col-xs-6 col-md-6" style="display: block;width: 50%;">
                                                        <a class="btn" v-bind:href="`/material_sale`" style="background: #2d1c5a !important;border: 0;width: 100%;display: flex; justify-content: center;border-radius: 5px;">New Sale</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>
<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#materialSale',
        data() {
            return {
                sale: {
                    sale_id: parseInt("<?php echo $sale_id; ?>"),
                    customer_id: '',
                    invoice_no: '<?php echo $invoiceNumber; ?>',
                    sale_date: '',
                    sale_for: parseInt("<?php echo $this->session->userdata('BRANCHid'); ?>"),
                    sub_total: 0,
                    vat: 0,
                    transport_cost: 0,
                    discount: 0,
                    total: 0,
                    paid: 0,
                    due: 0,
                    previous_due: 0,
                    note: ''
                },
                oldCustomerId: null,
                oldPreviousDue: 0,
                vatPercent: 0,
                cart: [],
                materials: [],
                customers: [],
                selectedCustomer: {
                    Customer_SlNo: "",
                    Customer_Code: '',
                    Customer_Name: 'Cash Customer',
                    display_name: 'Cash Customer',
                    Customer_Mobile: '',
                    Customer_Address: '',
                    Customer_Type: 'G'
                },
                selectedMaterial: {
                    material_id: '',
                    name: '',
                    sale_rate: 0.00
                },
                productStock: "",
                productStockText: "",
                saleInProgress: false
            }
        },
        created() {
            this.sale.sale_date = moment().format('YYYY-MM-DD');
            this.getCustomers();
            this.getMaterials();

            if (this.sale.sale_id != 0) {
                this.getSale();
            }
        },
        methods: {
            async getCustomers() {
                await axios.post('/get_customers', {
                    forSearch: 'yes'
                }).then(res => {
                    this.customers = res.data;
                    this.customers.unshift({
                        Customer_SlNo: "",
                        Customer_Code: '',
                        Customer_Name: 'Cash Customer',
                        display_name: 'Cash Customer',
                        Customer_Mobile: '',
                        Customer_Address: '',
                        Customer_Type: 'G'
                    }, {
                        Customer_SlNo: "",
                        Customer_Code: '',
                        Customer_Name: '',
                        display_name: 'New Customer',
                        Customer_Mobile: '',
                        Customer_Address: '',
                        Customer_Type: 'N'
                    })
                })
            },
            async onSearchCustomer(val, loading) {
                if (val.length > 2) {
                    loading(true);
                    await axios.post("/get_customers", {
                            name: val,
                        })
                        .then(res => {
                            let r = res.data;
                            this.customers = r.filter(item => item.status == 'a')
                            loading(false)
                        })
                } else {
                    loading(false)
                    await this.getCustomers();
                }
            },
            onChangeCustomer() {
                if (this.selectedCustomer == null) {
                    this.selectedCustomer = {
                        Customer_SlNo: "",
                        Customer_Code: '',
                        Customer_Name: '',
                        display_name: 'Cash Customer',
                        Customer_Mobile: '',
                        Customer_Address: '',
                        Customer_Type: 'G'
                    }
                    this.sale.previousDue = 0;
                    return
                }
                if (this.selectedCustomer.Customer_SlNo == "") {
                    this.sale.previous_due = 0;
                    return;
                }

                axios.post('/get_customer_due', {
                    customerId: this.selectedCustomer.Customer_SlNo
                }).then(res => {
                    if (res.data.length > 0) {
                        this.sale.previous_due = res.data[0].dueAmount;
                    } else {
                        this.sale.previous_due = 0;
                    }
                })

                this.calculateTotal();
            },
            getMaterials() {
                axios.get('/get_materials')
                    .then(res => {
                        this.materials = res.data.filter(m => m.status == 1);
                    })
            },
            async setFocus() {
                if ((this.selectedMaterial.material_id != '' || this.selectedMaterial.material_id != 0)) {
                    this.productStock = await axios.post('/get_material_stock', {
                        material_id: this.selectedMaterial.material_id
                    }).then(res => {
                        return res.data[0].stock_quantity;
                    })
                    this.productStockText = this.productStock > 0 ? "Available Stock" : "Stock Unavailable";

                    this.$refs.quantity.focus();
                }
            },
            toggleProductPurchaseRate() {
                this.$refs.productPurchaseRate.type = this.$refs.productPurchaseRate.type == 'text' ? 'password' : 'text';
            },
            materialTotal() {
                this.selectedMaterial.total = this.selectedMaterial.sale_rate * this.selectedMaterial.quantity;
                this.calculateTotal();
            },
            addToCart() {
                if (parseFloat(this.selectedMaterial.quantity) > parseFloat(this.productStock)) {
                    Swal.fire({
                        icon: "error",
                        text: "Stock Unavailable",
                    });
                    return;
                }
                let ind = this.cart.findIndex(m => m.material_id == this.selectedMaterial.material_id);
                if (ind > -1) {
                    this.clearMaterial();
                    return;
                }
                this.cart.push(this.selectedMaterial);
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
            calculateTotal() {
                this.sale.sub_total = 0;
                this.cart.forEach(m => {
                    this.sale.sub_total += parseFloat(m.total);
                })

                this.sale.vat = (this.sale.sub_total * this.vatPercent / 100);
                this.sale.total = (this.sale.sub_total + this.sale.vat + parseFloat(this.sale.transport_cost)) - this.sale.discount;
                this.sale.due = this.sale.total - this.sale.paid;
            },
            clearMaterial() {
                this.selectedMaterial = {
                    material_id: '',
                    name: '',
                    sale_rate: 0.00
                }
            },
            saveSale() {
                if (this.cart.length == 0) {
                    Swal.fire({
                        icon: "error",
                        text: "Cart is empty",
                    });
                    return;
                }

                let url = '/add_material_sale';
                if (this.sale.sale_id != 0) {
                    url = '/update_material_sale';
                }

                let data = {
                    sale: this.sale,
                    saledMaterials: this.cart,
                    customer: this.selectedCustomer,
                }

                this.saleInProgress = true;
                axios.post(url, data)
                    .then(async res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            let invoiceConf = confirm('Do you want to view invoice?');
                            if (invoiceConf) {
                                window.open(`/material_sale_invoice/${r.saleId}`, '_blank');
                                await new Promise(resolve => setTimeout(resolve, 1000));
                            }
                            window.location = '<?php echo base_url(); ?>material_sale';
                        }
                    })
            },
            async getSale() {
                let options = {
                    sale_id: this.sale.sale_id
                }
                await axios.post('/get_material_sale', options)
                    .then(res => {
                        this.sale = res.data[0];
                        this.oldCustomerId = res.data[0].Customer_SlNo;
                        this.oldPreviousDue = res.data[0].previous_due;
                        this.selectedCustomer = {
                            display_name: this.sale.Customer_type == 'G' ? 'Cash Customer' : `${this.sale.customer_name} - ${this.sale.customer_code} - ${this.sale.customer_mobile}`,
                            Customer_SlNo: this.sale.Customer_SlNo,
                            Customer_Code: this.sale.customer_code,
                            Customer_Name: this.sale.customer_name,
                            Customer_Mobile: this.sale.customer_mobile,
                            Customer_Address: this.sale.customer_address,
                            Customer_Type: this.sale.customer_type
                        }                        
                    });

                await axios.post('/get_material_sale_details', {
                        sale_id: this.sale.sale_id
                    })
                    .then(res => {
                        this.cart = res.data;
                    })

                await this.calculateTotal();

            }
        }
    })
</script>