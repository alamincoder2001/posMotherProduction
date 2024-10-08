<style>
    .v-select {
        margin-bottom: 5px;
        background: #fff;
        border-radius: 4px;
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

    input[type="date"] {
        margin-bottom: 4px !important;
    }
</style>
<div id="materialDamages">
    <div class="row" style="margin-top: 15px;">
        <fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;">
            <legend class="scheduler-border">Material Damage Entry Form</legend>
            <div class="control-group">
                <div class="col-md-6 col-md-offset-2">
                    <form class="form-horizontal" @submit.prevent="addDamage">
                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right"> Code </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-8">
                                <input type="text" placeholder="Code" class="form-control" v-model="damage.invoice" required readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right"> Date </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-8">
                                <input type="date" placeholder="Date" class="form-control" v-model="damage.damage_date" required />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right"> Raw Material </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-8">
                                <v-select v-bind:options="materials" label="display_text" v-model="selectedMaterial" placeholder="Select Material" v-on:input="materialChange"></v-select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right"> Quantity </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-3">
                                <input type="number" placeholder="Quantity" class="form-control" v-model="damage.damage_quantity" required v-on:input="calculateAmount" ref="qty" />
                            </div>

                            <label class="col-sm-2 control-label no-padding-right"> Rate: </label>
                            <div class="col-sm-3">
                                <input type="number" placeholder="Rate" class="form-control" v-model="damage.damage_rate" required v-on:input="calculateAmount" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right"> Damage Amount </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-8">
                                <input type="number" placeholder="Amount" class="form-control" v-model="damage.damage_amount" required readonly />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right"> Description </label>
                            <label class="col-sm-1 control-label no-padding-right">:</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" placeholder="Description" v-model="damage.description"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label no-padding-right"></label>
                            <label class="col-sm-1 control-label no-padding-right"></label>
                            <div class="col-sm-8">
                                <button type="submit" class="btn btn-sm btn-success">
                                    Submit
                                    <i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </fieldset>
    </div>
    <div class="row">
        <div class="col-sm-12 form-inline">
            <div class="form-group">
                <label for="filter" class="sr-only">Filter</label>
                <input type="text" class="form-control" v-model="filter" placeholder="Filter">
            </div>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="damages" :filter-by="filter">
                    <template scope="{ row }">
                        <tr>
                            <td>{{ row.invoice }}</td>
                            <td>{{ row.damage_date }}</td>
                            <td>{{ row.material_code }}</td>
                            <td>{{ row.material_name }}</td>
                            <td>{{ row.damage_quantity }}</td>
                            <td>{{ row.damage_rate }}</td>
                            <td>{{ row.damage_amount }}</td>
                            <td>{{ row.description }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <i class="fa fa-pencil btnEdit" @click="editDamage(row)"></i>
                                    <i class="fa fa-trash btnDelete" @click="deleteDamage(row.damage_id)"></i>
                                <?php } ?>
                            </td>
                        </tr>
                    </template>
                </datatable>
                <datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/moment.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#materialDamages',
        data() {
            return {
                damage: {
                    damage_id: 0,
                    invoice: '<?php echo $damageCode; ?>',
                    damage_date: moment().format('YYYY-MM-DD'),
                    description: '',
                    material_id: '',
                    damage_quantity: '',
                    damage_rate: '',
                    damage_amount: ''
                },
                materials: [],
                selectedMaterial: null,
                damages: [],

                columns: [{
                        label: 'Code',
                        field: 'invoice',
                        align: 'center',
                        filterable: false
                    },
                    {
                        label: 'Date',
                        field: 'damage_date',
                        align: 'center'
                    },
                    {
                        label: 'Material Code',
                        field: 'material_code',
                        align: 'center'
                    },
                    {
                        label: 'Material Name',
                        field: 'material_name',
                        align: 'center'
                    },
                    {
                        label: 'Quantity',
                        field: 'damage_quantity',
                        align: 'center'
                    },
                    {
                        label: 'Damage Rate',
                        field: 'damage_rate',
                        align: 'center'
                    },
                    {
                        label: 'Damage Amount',
                        field: 'damage_amount',
                        align: 'center'
                    },
                    {
                        label: 'Description',
                        field: 'description',
                        align: 'center'
                    },
                    {
                        label: 'Action',
                        align: 'center',
                        filterable: false
                    }
                ],
                page: 1,
                per_page: 10,
                filter: ''
            }
        },
        created() {
            this.getMaterials();
            this.getDamages();
        },
        methods: {
            materialChange() {
                if (this.selectedMaterial == null) {
                    this.damage.damage_rate = '';
                }
                if (this.selectedMaterial != null && this.damage.damage_id == 0) {
                    this.damage.damage_rate = this.selectedMaterial.purchase_rate;
                    this.$refs.qty.focus();
                }
            },
            calculateAmount() {
                this.damage.damage_amount = (this.damage.damage_quantity * this.damage.damage_rate);
            },
            getMaterials() {
                axios.get('/get_materials').then(res => {
                    this.materials = res.data.filter(m => m.status == 1);
                })
            },
            addDamage() {
                if (this.selectedMaterial == null) {
                    alert('Select material');
                    return;
                }

                this.damage.material_id = this.selectedMaterial.material_id;

                let url = '/add_material_damage';
                if (this.damage.damage_id != 0) {
                    url = '/update_material_damage'
                }
                axios.post(url, this.damage).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.resetForm();
                        this.damage.invoice = r.newCode;
                        this.getDamages();
                    }
                })
            },

            editDamage(damage) {
                let keys = Object.keys(this.damage);
                keys.forEach(key => this.damage[key] = damage[key]);

                this.selectedMaterial = {
                    material_id: damage.material_id,
                    display_text: `${damage.material_name} - ${damage.material_code}`
                }
            },

            deleteDamage(damageId) {
                let deleteConfirm = confirm('Are you sure?');
                if (deleteConfirm == false) {
                    return;
                }
                axios.post('/delete_material_damage', {
                    damageId: damageId
                }).then(res => {
                    let r = res.data;
                    alert(r.message);
                    if (r.success) {
                        this.getDamages();
                    }
                })
            },

            getDamages() {
                axios.get('/get_material_damage').then(res => {
                    this.damages = res.data;
                })
            },

            resetForm() {
                this.damage.damage_id = 0;
                this.damage.description = '';
                this.damage.material_id = '';
                this.damage.damage_quantity = '';
                this.damage.damage_rate = '';
                this.damage.damage_amount = '';
                this.selectedMaterial = null;
            }
        }
    })
</script>