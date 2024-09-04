<div id="materialSaleInvoice">
    <div style="display:none;" v-bind:style="{display: sale.sale_id == 'undefined' ? 'none' : ''}">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 text-right" style="margin-bottom: 10px;">
                <a href="" @click.prevent="print"><i class="fa fa-print"></i> Print</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div id="invoiceContent">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="heading">Material Sale Invoice</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6">
                            <table class="info-table">
                                <tr>
                                    <td>Customer Id</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.customer_code }}</td>
                                </tr>
                                <tr>
                                    <td>Customer Name</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.customer_name }}</td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.customer_address }}</td>
                                </tr>
                                <tr>
                                    <td>Mobile</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.customer_mobile }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-xs-4 col-xs-offset-2">
                            <table class="info-table">
                                <tr>
                                    <td style="text-align: right;">Invoice</td>
                                    <td style="text-align: right;">&nbsp;:&nbsp;</td>
                                    <td style="text-align: right;">{{ sale.invoice_no }}</td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Added By</td>
                                    <td style="text-align: right;">&nbsp;:&nbsp;</td>
                                    <td style="text-align: right;">{{ sale.User_Name }}</td>
                                </tr>
                                <tr>
                                    <td style="text-align: right;">Date</td>
                                    <td style="text-align: right;">&nbsp;:&nbsp;</td>
                                    <td style="text-align: right;">{{ sale.sale_date | dateFormat('DD-MM-YYYY') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <table class="details-table">
                                <tr>
                                    <td>Sl</td>
                                    <td>Material Name</td>
                                    <td>Quantity</td>
                                    <td>Price</td>
                                    <td>Total</td>
                                </tr>
                                <tr v-for="(material, sl) in saleDetails">
                                    <td style="text-align:center;">{{ sl + 1 }}</td>
                                    <td style="text-align:left;">{{ material.name }}</td>
                                    <td style="text-align:center;">{{ material.quantity }} {{ material.unit_name }}</td>
                                    <td style="text-align:right;">{{ material.sale_rate }}</td>
                                    <td style="text-align:right;">{{ material.total }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-4">
                            <table class="bottom-table">
                                <tr>
                                    <td>Previous Due</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.previous_due }}</td>
                                </tr>
                                <tr>
                                    <td>Current Due</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.due }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <div class="line"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Due</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ parseFloat(sale.previous_due) + parseFloat(sale.due) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-xs-5 col-xs-offset-3">
                            <table class="bottom-table">
                                <tr>
                                    <td>Sub Total</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.sub_total }}</td>
                                </tr>
                                <tr>
                                    <td>VAT</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.vat }}</td>
                                </tr>
                                <tr>
                                    <td>Transport Cost</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.transport_cost }}</td>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.discount }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <div class="line"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Total</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.total }}</td>
                                </tr>
                                <tr>
                                    <td>Paid</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.paid }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <div class="line"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Due</td>
                                    <td>&nbsp;:&nbsp;</td>
                                    <td>{{ sale.due }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <strong>Total (In Amount): </strong>{{ convertNumberToWords(sale.total) }}
                            <br>
                            <strong>Note: </strong>{{ sale.note }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    new Vue({
        el: '#materialSaleInvoice',
        data() {
            return {
                saleId: parseInt("<?php echo $saleId; ?>"),
                sale: {},
                saleDetails: [],
                style: null
            }
        },
        filters: {
            dateFormat(dt, format) {
                return dt == '' || dt == null ? "" : moment(dt).format(format);
            }
        },
        created() {
            this.setStyle();
            this.getSale();
            this.getSaleDetails();
        },
        methods: {
            setStyle() {
                this.style = `
                    <style>
                    .heading {
                        padding: 5px;
                        font-weight: bold;
                        font-size: 16px;
                        text-align: center;
                        border-top: 1px dotted #454545;
                        border-bottom: 1px dotted #454545;
                    }
                    .info-table, .details-table, .bottom-table {
                        margin:5px 0!important;
                        width: 100%;
                    }
                    .info-table tr td:first-child {
                        font-weight: bold;
                    }
                    .line {
                        border-bottom: 1px dotted #454545;
                    }
                    .details-table td {
                        border: 1px solid #a0a0a0;
                        padding: 3px 8px;
                    }
                    .details-table tr:first-child td{
                        font-weight:bold;
                        text-align:center;
                    }
                    .bottom-table td {
                        padding: 2px 0;
                    }
                    .bottom-table td:last-child {
                        text-align: right;
                        padding-right: 9px;
                    }
                    </style>
                `;

                document.head.innerHTML += this.style;
            },

            getSale() {
                axios.post('/get_material_sale', {
                    sale_id: this.saleId
                }).then(res => {
                    this.sale = res.data[0];
                })
            },

            getSaleDetails() {
                axios.post('/get_material_sale_details', {
                    sale_id: this.saleId
                }).then(res => {
                    this.saleDetails = res.data;
                })
            },

            async print() {
                let invoiceContent = `
					<div class="container">
						<div class="row">
							<div class="col-xs-12">
								${document.querySelector('#invoiceContent').innerHTML}
							</div>
						</div>
					</div>
				`;

                var reportWindow = window.open('', 'PRINT', `height=${screen.height}, width=${screen.width}`);
                reportWindow.document.write(`
					<?php $this->load->view('Administrator/reports/reportHeader.php'); ?>
				`);

                reportWindow.document.head.innerHTML += this.style;

                reportWindow.document.body.innerHTML += invoiceContent;

                reportWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                reportWindow.print();
                reportWindow.close();
            },

            convertNumberToWords(amountToWord) {
                var words = new Array();
                words[0] = '';
                words[1] = 'One';
                words[2] = 'Two';
                words[3] = 'Three';
                words[4] = 'Four';
                words[5] = 'Five';
                words[6] = 'Six';
                words[7] = 'Seven';
                words[8] = 'Eight';
                words[9] = 'Nine';
                words[10] = 'Ten';
                words[11] = 'Eleven';
                words[12] = 'Twelve';
                words[13] = 'Thirteen';
                words[14] = 'Fourteen';
                words[15] = 'Fifteen';
                words[16] = 'Sixteen';
                words[17] = 'Seventeen';
                words[18] = 'Eighteen';
                words[19] = 'Nineteen';
                words[20] = 'Twenty';
                words[30] = 'Thirty';
                words[40] = 'Forty';
                words[50] = 'Fifty';
                words[60] = 'Sixty';
                words[70] = 'Seventy';
                words[80] = 'Eighty';
                words[90] = 'Ninety';
                amount = amountToWord == null ? '0.00' : amountToWord.toString();
                var atemp = amount.split(".");
                var number = atemp[0].split(",").join("");
                var n_length = number.length;
                var words_string = "";
                if (n_length <= 9) {
                    var n_array = new Array(0, 0, 0, 0, 0, 0, 0, 0, 0);
                    var received_n_array = new Array();
                    for (var i = 0; i < n_length; i++) {
                        received_n_array[i] = number.substr(i, 1);
                    }
                    for (var i = 9 - n_length, j = 0; i < 9; i++, j++) {
                        n_array[i] = received_n_array[j];
                    }
                    for (var i = 0, j = 1; i < 9; i++, j++) {
                        if (i == 0 || i == 2 || i == 4 || i == 7) {
                            if (n_array[i] == 1) {
                                n_array[j] = 10 + parseInt(n_array[j]);
                                n_array[i] = 0;
                            }
                        }
                    }
                    value = "";
                    for (var i = 0; i < 9; i++) {
                        if (i == 0 || i == 2 || i == 4 || i == 7) {
                            value = n_array[i] * 10;
                        } else {
                            value = n_array[i];
                        }
                        if (value != 0) {
                            words_string += words[value] + " ";
                        }
                        if ((i == 1 && value != 0) || (i == 0 && value != 0 && n_array[i + 1] == 0)) {
                            words_string += "Crores ";
                        }
                        if ((i == 3 && value != 0) || (i == 2 && value != 0 && n_array[i + 1] == 0)) {
                            words_string += "Lakhs ";
                        }
                        if ((i == 5 && value != 0) || (i == 4 && value != 0 && n_array[i + 1] == 0)) {
                            words_string += "Thousand ";
                        }
                        if (i == 6 && value != 0 && (n_array[i + 1] != 0 && n_array[i + 2] != 0)) {
                            words_string += "Hundred and ";
                        } else if (i == 6 && value != 0) {
                            words_string += "Hundred ";
                        }
                    }
                    words_string = words_string.split("  ").join(" ");
                }
                return words_string + ' only';
            }
        }
    })
</script>