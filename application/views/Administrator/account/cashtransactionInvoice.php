<div id="cashtransactionInvoice">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="row">
                <div class="col-xs-12">
                    <a href="" v-on:click.prevent="print"><i class="fa fa-print"></i> Print</a>
                </div>
            </div>

            <div id="invoiceContent">
                <div class="row">
                    <div class="col-xs-12 text-center">
                        <div _h098asdh>
                            Transaction Invoice
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <strong>TransactionType:</strong> {{ cashtransaction.Tr_Type == 'Out Cash' ? 'Cash Payment' : 'Cash Received' }}<br>
                        <strong>Payment Type:</strong> {{ cashtransaction.paymentType == 'cash' ? 'By Cash' : 'By Bank' }}<br>
                        <span v-if="cashtransaction.paymentType == 'bank'"> <strong>Bank Account:</strong> {{ cashtransaction.account_name }} - {{ cashtransaction.account_number }} - {{ cashtransaction.bank_name }}</span><br>
                    </div>
                    <div class="col-xs-4 text-right">
                        <strong>Invoice No.:</strong> {{ cashtransaction.Tr_Id }}<br>
                        <strong>Added By:</strong> {{ cashtransaction.added_by }}<br>
                        <strong>Date:</strong> {{ cashtransaction.Tr_date | dateFormat('DD-MM-YYYY') }} {{ cashtransaction.AddTime | dateFormat('h:mm a') }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div _d9283dsc></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <table _a584de>
                            <thead>
                                <tr>
                                    <td>Sl.</td>
                                    <td>Account Name</td>
                                    <td>In Amount</td>
                                    <td>Out Amount</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td style="text-align: left;">
                                        {{ cashtransaction.Acc_Name }}
                                        <span></span>
                                    </td>
                                    <td style="text-align: right;">{{ cashtransaction.In_Amount}}</td>
                                    <td style="text-align: right;">{{ cashtransaction.Out_Amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12" style="margin-top: 10px;">
                        <strong>Note: </strong> {{cashtransaction.Tr_Description}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6" style="margin-top:80px;">
                        <span style="text-decoration:overline;">Received by</span>
                    </div>
                    <div class="col-xs-6 text-right" style="margin-top:80px;">
                        <span style="text-decoration:overline;">Authorized by</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    new Vue({
        el: '#cashtransactionInvoice',
        data() {
            return {
                cashtransaction: {
                    Tr_SlNo: parseInt('<?php echo $transactionId; ?>')
                },
                style: null,
                companyProfile: null,
                currentBranch: null
            }
        },
        filters: {
            dateFormat(dt, format) {
                return dt == null || dt == '' ? "" : moment(dt).format(format);
            }
        },
        created() {
            this.setStyle();
            this.getCashTransaction();
            this.getCompanyProfile();
            this.getCurrentBranch();
        },
        methods: {
            getCashTransaction() {
                axios.post('/get_cash_transactions', {
                    transactionId: this.cashtransaction.Tr_SlNo
                }).then(res => {
                    this.cashtransaction = res.data[0];
                })
            },
            getCompanyProfile() {
                axios.get('/get_company_profile').then(res => {
                    this.companyProfile = res.data;
                })
            },
            getCurrentBranch() {
                axios.get('/get_current_branch').then(res => {
                    this.currentBranch = res.data;
                })
            },
            setStyle() {
                this.style = document.createElement('style');
                this.style.innerHTML = `
                div[_h098asdh]{
                    background-color:#e0e0e0;
                    font-weight: bold;
                    font-size:15px;
                    margin-bottom:15px;
                    padding: 5px;
                }
                div[_d9283dsc]{
                    padding-bottom:25px;
                    border-bottom: 1px solid #ccc;
                    margin-bottom: 15px;
                }
                table[_a584de]{
                    width: 100%;
                    text-align:center;
                }
                table[_a584de] thead{
                    font-weight:bold;
                }
                table[_a584de] td{
                    padding: 3px;
                    border: 1px solid #ccc;
                }
                table[_t92sadbc2]{
                    width: 100%;
                }
                table[_t92sadbc2] td{
                    padding: 2px;
                }
                
                @media print{
                    div[_h098asdh]{
                        background-color: #e0e0e0 !important;
                        print-color-adjust: exact; 
                    }
                }
            `;
                document.head.appendChild(this.style);
            },
            async print() {
                let reportContent = `
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

                reportWindow.document.body.innerHTML += reportContent;

                if (this.searchType == '' || this.searchType == 'user') {
                    let rows = reportWindow.document.querySelectorAll('.record-table tr');
                    rows.forEach(row => {
                        row.lastChild.remove();
                    })
                }

                let invoiceStyle = reportWindow.document.createElement('style');
                invoiceStyle.innerHTML = this.style.innerHTML;
                reportWindow.document.head.appendChild(invoiceStyle);

                reportWindow.focus();
                await new Promise(resolve => setTimeout(resolve, 1000));
                reportWindow.print();
                reportWindow.close();
            }
        }
    })
</script>