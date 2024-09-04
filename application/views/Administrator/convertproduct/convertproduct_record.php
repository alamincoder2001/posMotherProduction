<style>
    .form-group {
        margin-right: 15px;
    }

    .v-select {
        width: 200px;
        height: 30px;
    }

    .v-select .dropdown-toggle {
        height: 29px;
        border-radius: 0;
    }

    .v-select input[type=search] {
        margin: 0;
    }
</style>
<div id="productions">
    <div class="row">
        <div class="col-md-12">
            <fieldset class="scheduler-border scheduler-search">
                <legend class="scheduler-border">Search Convert Product Record</legend>
                <div class="control-group">
                    <form class="form-inline" v-on:submit.prevent="getProductions">
                        <div class="form-group">
                            <label>Date From</label>
                            <input type="date" class="form-control" v-model="dateFrom">
                        </div>
                        <div class="form-group">
                            <label>Date To</label>
                            <input type="date" class="form-control" v-model="dateTo">
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Search">
                        </div>
                    </form>
                </div>
            </fieldset>
        </div>
    </div>

    <div class="row" style="display:none;" v-bind:style="{display: productions.length > 0 ? '' : 'none'}">
        <div class="col-md-12 text-right">
            <a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
        </div>
        <div class="col-md-12">
            <div class="table-responsive" id="reportContent">
                <table class="table table-bordered record-table">
                    <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Production Id</th>
                            <th>Date</th>
                            <th>Total Cost</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="(convert, sl) in productions">
                            <tr>
                                <td>{{ sl + 1 }}</td>
                                <td>{{ convert.invoice }}</td>
                                <td>{{ convert.date }}</td>
                                <td style="text-align:right;">{{ convert.total }}</td>
                                <td style="text-align:left;">{{ convert.products[0].name }}</td>
                                <td style="text-align:right;">{{ convert.products[0].quantity }}</td>
                                <td>
                                    <a href="" v-bind:href="`/convertproduct_invoice/${convert.id}`" target="_blank"><i class="fa fa-file-text"></i></a>
                                    <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                        <a href="" v-bind:href="`/convertproduct/edit/${convert.id}`"><i class="fa fa-pencil-square"></i></a>
                                        <a href="" v-on:click.prevent="deleteConvertProduct(convert.id)"><i class="fa fa-trash"></i></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr v-for="(product, sl) in convert.products.slice(1)">
                                <td colspan="5" v-bind:rowspan="convert.products.length - 1" v-if="sl == 0"></td>
                                <td style="text-align:left;">{{ product.name }}</td>
                                <td style="text-align:right;">{{ product.quantity }}</td>
                                <td></td>
                            </tr>
                        </template>
                        <tr>
                            <td colspan="3" style="text-align: right">Total</td>
                            <td style="text-align:right;">{{ productions.reduce((p, c) => { return p + parseFloat(c.total)}, 0).toFixed(2) }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
        el: '#productions',
        data() {
            return {
                dateFrom: moment().format('YYYY-MM-DD'),
                dateTo: moment().format('YYYY-MM-DD'),
                productions: []
            }
        },
        created() {
            this.getProductions();
        },
        methods: {
            getProductions() {
                let options = {
                    dateFrom: this.dateFrom,
                    dateTo: this.dateTo
                }
                axios.post('get_convertproduct_record', options)
                    .then(res => {
                        this.productions = res.data.filter(production => production.products.length > 0);
                    })
            },
            deleteConvertProduct(producton_id) {
                let deleteConfirm = confirm('Are you sure?');
                if (deleteConfirm == false) {
                    return;
                }

                axios.post('/delete_production', {
                    productionId: producton_id
                }).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getProductions();
                    }
                })
            },
            async print() {
                let dateText = '';
                if (this.dateFrom != '' && this.dateTo != '') {
                    dateText = `Statemenet from <strong>${this.dateFrom}</strong> to <strong>${this.dateTo}</strong>`;
                }

                let reportContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12 text-center">
								<h3>Production Record</h3>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12 text-right">
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

                reportWindow.document.body.innerHTML += reportContent;

                let rows = reportWindow.document.querySelectorAll('.record-table tr');
                rows.forEach(row => {
                    row.lastChild.remove();
                })

                reportWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                reportWindow.print();
                reportWindow.close();
            }
        }
    })
</script>