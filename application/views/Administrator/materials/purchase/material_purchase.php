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
<div id="materialPurchase">
    <div class="row">
        <div class="col-xs-12 col-md-12 col-lg-12">
            <fieldset class="scheduler-border entryFrom">
                <div class="control-group">
                    <div class="row">
                        <div class="form-group">
                            <label class="col-md-1 control-label no-padding-right" for="age"> Invoice no </label>
                            <div class="col-md-2">
                                <input type="text" id="purchInvoice" name="purchInvoice" class="form-control" readonly v-model="purchase.invoice_no" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label no-padding-right" for="PurchaseFor"> Purchase For </label>
                            <div class="col-md-3">
                                <select class="chosen-select form-control" name="PurchaseFor" id="PurchaseFor">
                                    <option value="<?php echo $this->session->userdata('BRANCHid'); ?>"> <?php echo $this->session->userdata('Branch_name'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-1 control-label no-padding-right" for="Purchase_date"> Date </label>
                            <div class="col-md-3">
                                <input class="form-control" id="Purchase_date" name="Purchase_date" type="date"
                                    class="form-control" v-model="purchase.purchase_date" />
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>

        <div class="col-xs-9 col-md-9 col-lg-9">
            <div class="row">
                <div class="col-md-6">
                    <fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;">
                        <legend class="scheduler-border">Supplier Information</legend>
                        <div class="control-group">
                            <div class="form-group">
                                <label class="col-md-4 control-label no-padding-right" for="SupplierID"> Supplier ID
                                </label>
                                <div class="col-md-7">
                                    <v-select label="display_name" v-bind:options="suppliers"
                                        v-model="selectedSupplier" placeholder="Select Supplier" v-on:input="onChangeSupplier"></v-select>
                                </div>
                                <div class="col-md-1" style="padding: 0;">
                                    <a href="supplier" title="Add New Supplier" class="btn btn-xs btn-danger"
                                        style="height: 25px; border: 0; width: 27px; margin-left: -10px;"
                                        target="_blank"><i class="fa fa-plus" aria-hidden="true"
                                            style="margin-top: 5px;"></i></a>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label no-padding-right"> Name </label>
                                <div class="col-md-8">
                                    <input type="text" placeholder="Supplier Name" class="form-control" v-model="selectedSupplier.Supplier_Name" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label no-padding-right"> Mobile No </label>
                                <div class="col-md-8">
                                    <input type="text" placeholder="Mobile No" class="form-control" v-model="selectedSupplier.Supplier_Mobile" v-bind:disabled="selectedSupplier.Supplier_Type == 'G' ? false : true" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label no-padding-right"> Address </label>
                                <div class="col-md-8">
                                    <textarea class="form-control" v-model="selectedSupplier.Supplier_Address" v-bind:disabled="selectedSupplier.Supplier_Type == 'G' ? false : true"></textarea>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="col-md-6">
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
                                        Material Name
                                    </label>
                                    <div class="col-md-8">
                                        <input type="text" id="materialName" name="materialName"
                                            placeholder="Material Name" class="form-control" readonly v-model="selectedMaterial.name" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label no-padding-right" for="MaterialRATE"> Pur.
                                        Rate
                                    </label>
                                    <div class="col-md-3">
                                        <input type="text" id="PurchaseRate" name="PurchaseRate"
                                            class="form-control" placeholder="Pur. Rate" v-model="selectedMaterial.purchase_rate" />
                                    </div>

                                    <label class="col-md-2 control-label no-padding-right" for="PurchaseQTY">
                                        Quantity
                                    </label>
                                    <div class="col-md-3">
                                        <input type="text" id="PurchaseQTY" name="PurchaseQTY" ref="quantity" required
                                            class="form-control" placeholder="Quantity" v-model="selectedMaterial.quantity" v-on:input="materialTotal" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label no-padding-right" for="totalAmount"> Total
                                        Amount </label>
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
            </div>

            <div class="col-xs-12 col-md-12 col-lg-12" style="padding-left: 0px;padding-right: 0px;">
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
                                <td>{{ material.purchase_rate }}</td>
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
                                <td colspan="3"><textarea style="width: 100%;height:100%;" v-model="purchase.note"></textarea></td>
                                <td colspan="4" style="font-size:18px;font-weight: bold;">tk. {{ purchase.sub_total }}</td>
                            </tr>
                        </tbody>
                    </table>
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
                                <form @submit.prevent="savePurchase">
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
                                                            readonly v-model="purchase.sub_total" />
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
                                                        <input type="number" id="purchVat" readonly="" name="purchVat" class="form-control" v-model="purchase.vat" />
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
                                                            class="form-control" v-model="purchase.transport_cost" v-on:input="calculateTotal" />
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
                                                            class="form-control" v-model="purchase.discount" v-on:input="calculateTotal" />
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
                                                            class="form-control" readonly v-model="purchase.total" />
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
                                                            class="form-control" v-model="purchase.paid" v-on:input="calculateTotal" />
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <label class="col-xs-12 control-label" style="margin:0;">Due</label>
                                                    <div class="col-xs-6">
                                                        <input type="number" id="purchaseDue2" name="purchaseDue2" class="form-control" readonly v-model="purchase.due" />
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <input type="number" id="previousDue" name="previousDue" class="form-control" v-model="purchase.previous_due" readonly style="color:red;" />
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <div class="col-xs-6 col-md-6" style="display: block;width: 50%;">
                                                        <input type="submit" class="btn" value="Purchase" style="width:100%;background: green !important;border: 0;border-radius: 5px;outline:none;" v-bind:disabled="purchaseInProgress ? true : false" />
                                                    </div>
                                                    <div class="col-xs-6 col-md-6" style="display: block;width: 50%;">
                                                        <a class="btn" v-bind:href="`/material_purchase`" style="background: #2d1c5a !important;border: 0;width: 100%;display: flex; justify-content: center;border-radius: 5px;">New Purchase</a>
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
        el: '#materialPurchase',
        data() {
            return {
                purchase: {
                    purchase_id: parseInt("<?php echo $purchase_id; ?>"),
                    supplier_id: '',
                    invoice_no: '<?php echo $invoiceNumber; ?>',
                    purchase_date: '',
                    purchase_for: parseInt("<?php echo $this->session->userdata('BRANCHid'); ?>"),
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
                oldSupplierId: null,
                oldPreviousDue: 0,
                vatPercent: 0,
                cart: [],
                materials: [],
                suppliers: [],
                selectedSupplier: {
                    Supplier_SlNo: "",
                    Supplier_Code: '',
                    Supplier_Name: 'Cash Supplier',
                    display_name: 'Cash Supplier',
                    Supplier_Mobile: '',
                    Supplier_Address: '',
                    Supplier_Type: 'G'
                },
                selectedMaterial: {
                    material_id: '',
                    name: '',
                    purchase_rate: 0.00
                },
                purchaseInProgress: false
            }
        },
        created() {
            this.purchase.purchase_date = moment().format('YYYY-MM-DD');
            this.getSuppliers();
            this.getMaterials();

            if (this.purchase.purchase_id != 0) {
                this.getPurchase();
            }
        },
        methods: {
            async getSuppliers() {
                await axios.post('/get_suppliers', {
                    forSearch: 'yes'
                }).then(res => {
                    this.suppliers = res.data;
                    this.suppliers.unshift({
                        Supplier_SlNo: "",
                        Supplier_Code: '',
                        Supplier_Name: 'Cash Supplier',
                        display_name: 'Cash Supplier',
                        Supplier_Mobile: '',
                        Supplier_Address: '',
                        Supplier_Type: 'G'
                    }, {
                        Supplier_SlNo: "",
                        Supplier_Code: '',
                        Supplier_Name: '',
                        display_name: 'New Supplier',
                        Supplier_Mobile: '',
                        Supplier_Address: '',
                        Supplier_Type: 'N'
                    })
                })
            },
            async onSearchSupplier(val, loading) {
                if (val.length > 2) {
                    loading(true);
                    await axios.post("/get_suppliers", {
                            name: val,
                        })
                        .then(res => {
                            let r = res.data;
                            this.suppliers = r.filter(item => item.status == 'a')
                            loading(false)
                        })
                } else {
                    loading(false)
                    await this.getSuppliers();
                }
            },
            onChangeSupplier() {
                if (this.selectedSupplier == null) {
                    this.selectedSupplier = {
                        Supplier_SlNo: "",
                        Supplier_Code: '',
                        Supplier_Name: '',
                        display_name: 'Cash Supplier',
                        Supplier_Mobile: '',
                        Supplier_Address: '',
                        Supplier_Type: 'G'
                    }
                    this.purchase.previousDue = 0;
                    return
                }
                if (this.selectedSupplier.Supplier_SlNo == "") {
                    this.purchase.previousDue = 0;
                    return;
                }

                axios.post('/get_supplier_due', {
                    supplierId: this.selectedSupplier.Supplier_SlNo
                }).then(res => {
                    if (res.data.length > 0) {
                        this.purchase.previousDue = res.data[0].due;
                    } else {
                        this.purchase.previousDue = 0;
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
            setFocus() {
                this.$refs.quantity.focus();
            },
            materialTotal() {
                this.selectedMaterial.total = this.selectedMaterial.purchase_rate * this.selectedMaterial.quantity;
                this.calculateTotal();
            },
            addToCart() {
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
                this.purchase.sub_total = 0;
                this.cart.forEach(m => {
                    this.purchase.sub_total += parseFloat(m.total);
                })

                this.purchase.vat = (this.purchase.sub_total * this.vatPercent / 100);
                this.purchase.total = (this.purchase.sub_total + this.purchase.vat + parseFloat(this.purchase.transport_cost)) - this.purchase.discount;
                if (this.selectedSupplier.Supplier_Type == 'G') {
                    this.purchase.paid = this.purchase.total;
                    this.purchase.due = 0;
                } else {
                    this.purchase.due = this.purchase.total - this.purchase.paid;
                }
            },
            clearMaterial() {
                this.selectedMaterial = {
                    material_id: '',
                    name: '',
                    purchase_rate: 0.00
                }
            },
            savePurchase() {
                if (this.cart.length == 0) {
                    Swal.fire({
                        icon: "error",
                        text: "Cart is empty",
                    });
                    return;
                }
                if (this.selectedSupplier.Supplier_Type == 'G' && this.purchase.due > 0) {
                    Swal.fire({
                        icon: "error",
                        text: "Cash customer does not due",
                    });
                    return;
                }

                let url = '/add_material_purchase';
                if (this.purchase.purchase_id != 0) {
                    url = '/update_material_purchase';
                }

                let data = {
                    purchase: this.purchase,
                    purchasedMaterials: this.cart,
                    supplier: this.selectedSupplier,
                }

                this.purchaseInProgress = true;
                axios.post(url, data)
                    .then(async res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            let invoiceConf = confirm('Do you want to view invoice?');
                            if (invoiceConf) {
                                window.open(`/material_purchase_invoice/${r.purchaseId}`, '_blank');
                                await new Promise(resolve => setTimeout(resolve, 1000));
                            }
                            window.location = '<?php echo base_url(); ?>material_purchase';
                        }
                    })
            },
            async getPurchase() {
                let options = {
                    purchase_id: this.purchase.purchase_id
                }
                await axios.post('/get_material_purchase', options)
                    .then(res => {
                        this.purchase = res.data[0];
                        this.oldSupplierId = res.data[0].Supplier_SlNo;
                        this.oldPreviousDue = res.data[0].previous_due;
                        this.selectedSupplier = {
                            display_name: this.purchase.supplier_type == 'G' ? 'Cash Supplier' : `${this.purchase.supplier_name} - ${this.purchase.supplier_code} - ${this.purchase.supplier_mobile}`,
                            Supplier_SlNo: this.purchase.Supplier_SlNo,
                            Supplier_Code: this.purchase.supplier_Code,
                            Supplier_Name: this.purchase.supplier_name,
                            Supplier_Mobile: this.purchase.supplier_mobile,
                            Supplier_Address: this.purchase.supplier_address,
                            Supplier_Type: this.purchase.supplier_type
                        }
                    });

                await axios.post('/get_material_purchase_details', {
                        purchase_id: this.purchase.purchase_id
                    })
                    .then(res => {
                        this.cart = res.data;
                    })

                await this.calculateTotal();
            }
        }
    })
</script>