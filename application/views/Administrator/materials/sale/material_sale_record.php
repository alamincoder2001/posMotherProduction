<style>
    .v-select {
        float: right;
        min-width: 200px;
        background: #fff;
        margin-left: 5px;
        border-radius: 4px !important;
        margin-top: -2px;
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

    #searchForm select {
        padding: 0;
        border-radius: 4px;
    }

    #searchForm .form-group {
        margin-right: 5px;
    }

    #searchForm * {
        font-size: 13px;
    }

    .record-table {
        width: 100%;
        border-collapse: collapse;
    }

    .record-table thead {
        background-color: #0097df;
        color: white;
    }

    .record-table th,
    .record-table td {
        padding: 3px;
        border: 1px solid #454545;
    }

    .record-table th {
        text-align: center;
    }
</style>
<div id="saleRecord">
    <div class="row" style="margin:0;">
        <fieldset class="scheduler-border scheduler-search">
            <legend class="scheduler-border">Search Sale Record</legend>
            <div class="control-group">
                <div class="col-md-12">
                    <form class="form-inline" id="searchForm" @submit.prevent="getSearchResult">
                        <div class="form-group">
                            <label>Search Type</label>
                            <select class="form-select" style="margin: 0;width:150px;height:26px;" v-model="searchType" @change="onChangeSearchType">
                                <option value="">All</option>
                                <option value="customer">By Customer</option>
                                <option value="category">By Category</option>
                                <option value="quantity">By Quantity</option>
                                <option value="user">By User</option>
                            </select>
                        </div>

                        <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'customer' && customers.length > 0 ? '' : 'none'}">
                            <label>Customer</label>
                            <v-select v-bind:options="customers" v-model="selectedCustomer" label="display_name"></v-select>
                        </div>

                        <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'quantity' && materials.length > 0 ? '' : 'none'}">
                            <label>Material</label>
                            <v-select v-bind:options="materials" v-model="selectedMaterial" label="display_text"></v-select>
                        </div>

                        <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'category' && categories.length > 0 ? '' : 'none'}">
                            <label>Category</label>
                            <v-select v-bind:options="categories" v-model="selectedCategory" label="ProductCategory_Name"></v-select>
                        </div>

                        <div class="form-group" style="display:none;" v-bind:style="{display: searchType == 'user' && users.length > 0 ? '' : 'none'}">
                            <label>User</label>
                            <v-select v-bind:options="users" v-model="selectedUser" label="FullName"></v-select>
                        </div>

                        <div class="form-group" v-bind:style="{display: searchTypesForRecord.includes(searchType) ? '' : 'none'}">
                            <label>Record Type</label>
                            <select class="form-control" v-model="recordType" @change="sales = []">
                                <option value="without_details">Without Details</option>
                                <option value="with_details">With Details</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">From</label>
                            <input type="date" class="form-control" v-model="dateFrom">
                        </div>

                        <div class="form-group">
                            <label for="">To</label>
                            <input type="date" class="form-control" v-model="dateTo">
                        </div>

                        <div class="form-group" style="margin-top: -1px;">
                            <input type="submit" value="Search">
                        </div>
                    </form>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="row" style="display:none;" v-bind:style="{display: sales.length > 0 ? '' : 'none'}">
        <div class="col-md-12 text-right">
            <a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
        </div>
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">
                <table class="table record-table table-hover table-bordered" v-if="(searchTypesForRecord.includes(searchType)) && recordType == 'with_details'" style="display:none" v-bind:style="{display: (searchTypesForRecord.includes(searchType)) && recordType == 'with_details' ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Material Name</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="sale in sales">
                            <tr>
                                <td>{{ sale.invoice_no }}</td>
                                <td>{{ sale.sale_date }}</td>
                                <td>{{ sale.customer_name }}</td>
                                <td style="text-align: left;">{{ sale.saleDetails[0].name }}</td>
                                <td style="text-align:center;">{{ sale.saleDetails[0].quantity }}</td>
                                <td style="text-align:right;">{{ sale.saleDetails[0].sale_rate }}</td>
                                <td style="text-align:right;">{{ sale.saleDetails[0].total }}</td>
                                <td style="text-align:center;">
                                    <a href="" title="sale Invoice" v-bind:href="`/material_sale_invoice/${sale.sale_id}`" target="_blank"><i class="fa fa-file-text"></i></a>
                                    <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                        <a href="javascript:" title="Edit sale" @click="checkReturnAndEdit(sale)"><i class="fa fa-edit"></i></a>
                                        <a href="" title="Delete sale" @click.prevent="deleteSale(sale.sale_id)"><i class="fa fa-trash"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr v-for="(product, sl) in sale.saleDetails.slice(1)">
                                <td colspan="3" v-bind:rowspan="sale.saleDetails.length - 1" v-if="sl == 0"></td>
                                <td style="text-align: left;">{{ product.name }}</td>
                                <td style="text-align:center;">{{ product.quantity }}</td>
                                <td style="text-align:right;">{{ product.sale_rate }}</td>
                                <td style="text-align:right;">{{ product.total }}</td>
                                <td></td>
                            </tr>
                            <tr style="font-weight:bold;">
                                <td colspan="4" style="font-weight:normal;"><strong>Note: </strong>{{ sale.note }}</td>
                                <td style="text-align:center;">Total Quantity<br>{{ sale.saleDetails.reduce((prev, curr) => {return prev + parseFloat(curr.quantity)}, 0) }}</td>
                                <td></td>
                                <td style="text-align:right;">
                                    Total: {{ sale.total }}<br>
                                    Paid: {{ sale.paid }}<br>
                                    Due: {{ sale.due }}
                                </td>
                                <td></td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <table class="table record-table table-hover table-bordered" v-if="(searchTypesForRecord.includes(searchType)) && recordType == 'without_details'" style="display:none" v-bind:style="{display: (searchTypesForRecord.includes(searchType)) && recordType == 'without_details' ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Sub Total</th>
                            <th>VAT</th>
                            <th>Discount</th>
                            <th>Transport Cost</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sale in sales">
                            <td>{{ sale.invoice_no }}</td>
                            <td>{{ sale.sale_date }}</td>
                            <td style="text-align: left;">{{ sale.customer_name }}</td>
                            <td style="text-align:right;">{{ sale.sub_total }}</td>
                            <td style="text-align:right;">{{ sale.vat }}</td>
                            <td style="text-align:right;">{{ sale.discount }}</td>
                            <td style="text-align:right;">{{ sale.transport_cost }}</td>
                            <td style="text-align:right;">{{ sale.total }}</td>
                            <td style="text-align:right;">{{ sale.paid }}</td>
                            <td style="text-align:right;">{{ sale.due }}</td>
                            <td style="text-align:left;">{{ sale.note }}</td>
                            <td style="text-align:center;">
                                <a href="" title="Sale Invoice" v-bind:href="`/material_sale_invoice/${sale.sale_id}`" target="_blank"><i class="fa fa-file-text"></i></a>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <a href="javascript:" title="Edit Sale" @click="checkReturnAndEdit(sale)"><i class="fa fa-edit"></i></a>
                                    <a href="" title="Delete sale" @click.prevent="deleteSale(sale.sale_id)"><i class="fa fa-trash"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td colspan="3" style="text-align:right;">Total</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.sub_total)}, 0) }}</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.vat)}, 0) }}</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.discount)}, 0) }}</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.transport_cost)}, 0) }}</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.total)}, 0) }}</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.paid)}, 0) }}</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr)=>{return prev + parseFloat(curr.due)}, 0) }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table record-table table-hover table-bordered" v-if="searchTypesForDetails.includes(searchType)" style="display:none;" v-bind:style="{display: searchTypesForDetails.includes(searchType) ? '' : 'none'}">
                    <thead>
                        <tr>
                            <th>Invoice No.</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Product Name</th>
                            <th>sales Rate</th>
                            <th>Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="sale in sales">
                            <td>{{ sale.invoice_no }}</td>
                            <td>{{ sale.sale_date }}</td>
                            <td style="text-align: left;">{{ sale.customer_name }}</td>
                            <td style="text-align: left;">{{ sale.name }}</td>
                            <td style="text-align:right;">{{ sale.sale_rate }}</td>
                            <td style="text-align:right;">{{ sale.quantity }}</td>
                        </tr>
                        <tr style="font-weight:bold;">
                            <td colspan="5" style="text-align:right;">Total Quantity</td>
                            <td style="text-align:right;">{{ sales.reduce((prev, curr) => { return prev + parseFloat(curr.quantity)}, 0) }}</td>
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
        el: '#saleRecord',
        data() {
            return {
                searchType: '',
                recordType: 'without_details',
                dateFrom: moment().format('YYYY-MM-DD'),
                dateTo: moment().format('YYYY-MM-DD'),
                customers: [],
                selectedCustomer: null,
                materials: [],
                selectedMaterial: null,
                users: [],
                selectedUser: null,
                categories: [],
                selectedCategory: null,
                sales: [],
                searchTypesForRecord: ['', 'user', 'customer'],
                searchTypesForDetails: ['quantity', 'category']
            }
        },
        methods: {
            checkReturnAndEdit(sale) {
                location.replace('/material_sale/' + sale.sale_id);
            },
            onChangeSearchType() {
                this.sales = [];
                if (this.searchType == 'quantity') {
                    this.getMaterials();
                } else if (this.searchType == 'user') {
                    this.getUsers();
                } else if (this.searchType == 'category') {
                    this.getCategories();
                } else if (this.searchType == 'customer') {
                    this.getcustomers();
                }
            },
            getMaterials() {
                axios.get('/get_materials').then(res => {
                    this.materials = res.data;
                })
            },
            getcustomers() {
                axios.get('/get_customers').then(res => {
                    this.customers = res.data;
                })
            },
            getUsers() {
                axios.get('/get_users').then(res => {
                    this.users = res.data;
                })
            },
            getCategories() {
                axios.get('/get_material_categories').then(res => {
                    this.categories = res.data;
                })
            },
            getSearchResult() {
                if (this.searchType != 'user') {
                    this.selectedUser = null;
                }

                if (this.searchType != 'quantity') {
                    this.selectedMaterial = null;
                }

                if (this.searchType != 'category') {
                    this.selectedCategory = null;
                }

                if (this.searchType != 'customer') {
                    this.selectedCustomer = null;
                }

                if (this.searchTypesForRecord.includes(this.searchType)) {
                    this.getSaleRecord();
                } else {
                    this.getSaleDetails();
                }
            },
            getSaleRecord() {
                let filter = {
                    userFullName: this.selectedUser == null || this.selectedUser.FullName == '' ? '' : this.selectedUser.FullName,
                    customerId: this.selectedCustomer == null ? '' : this.selectedCustomer.customer_SlNo,
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }

                let url = '/get_material_sale';
                if (this.recordType == 'with_details') {
                    url = '/get_material_sale';
                }

                axios.post(url, filter)
                    .then(res => {
                        if (this.recordType == 'with_details') {
                            this.sales = res.data;
                        } else {
                            this.sales = res.data;
                        }
                    })
            },

            getSaleDetails() {
                let filter = {
                    categoryId: this.selectedCategory == null || this.selectedCategory.ProductCategory_SlNo == '' ? '' : this.selectedCategory.ProductCategory_SlNo,
                    materialId: this.selectedMaterial == null || this.selectedMaterial.material_id == '' ? '' : this.selectedMaterial.material_id,
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }

                axios.post('/get_material_sale_details', filter)
                    .then(res => {
                        this.sales = res.data;
                    })
            },
            deleteSale(saleId) {
                let deleteConf = confirm('Are you sure?');
                if (deleteConf == false) {
                    return;
                }
                axios.post('/delete_material_sale', {
                        sale_id: saleId
                    })
                    .then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.getSaleRecord();
                        }
                    })
            },
            async print() {
                let dateText = '';
                if (this.dateFrom != '' && this.dateTo != '') {
                    dateText = `Statement from <strong>${this.dateFrom}</strong> to <strong>${this.dateTo}</strong>`;
                }

                let userText = '';
                if (this.selectedUser != null && this.selectedUser.FullName != '' && this.searchType == 'user') {
                    userText = `<strong>Sold by: </strong> ${this.selectedUser.FullName}`;
                }

                let customerText = '';
                if (this.selectedCustomer != null && this.selectedCustomer.customer_SlNo != '' && this.searchType == 'quantity') {
                    customerText = `<strong>customer: </strong> ${this.selectedCustomer.customer_Name}<br>`;
                }

                let productText = '';
                if (this.selectedMaterial != null && this.selectedMaterial.material_id != '' && this.searchType == 'quantity') {
                    productText = `<strong>Product: </strong> ${this.selectedMaterial.Material_Name}`;
                }

                let categoryText = '';
                if (this.selectedCategory != null && this.selectedCategory.ProductCategory_SlNo != '' && this.searchType == 'category') {
                    categoryText = `<strong>Category: </strong> ${this.selectedCategory.ProductCategory_Name}`;
                }


                let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h3>Sale Record</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-6">
								${userText} ${customerText} ${productText} ${categoryText}
							</div>
							<div class="col-xs-6 text-right">
								${dateText}
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
					<link rel="stylesheet" href="/assets/css/bootstrap.min.css" />
					<style>
						.record-table{
							width: 100%;
							border-collapse: collapse;
						}
						.record-table th{
							text-align: center;
						}
					</style>
				`;
                reportWindow.document.body.innerHTML += reportContent;

                if (this.searchType == '' || this.searchType == 'user') {
                    let rows = reportWindow.document.querySelectorAll('.record-table tr');
                    rows.forEach(row => {
                        row.lastChild.remove();
                    })
                }


                reportWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                reportWindow.print();
                reportWindow.close();
            }
        }
    })
</script>