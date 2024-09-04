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

    #materials .add-button {
        padding: 2.5px;
        width: 100%;
        background-color: #298db4;
        display: block;
        text-align: center;
        color: white;
        cursor: pointer;
        border-radius: 3px;
    }

    #materials .add-button:hover {
        background-color: #41add6;
        color: white;
    }
</style>

<div id="materials">
    <div class="row">
        <div class="col-xs-12">
            <fieldset class="scheduler-border" style="margin-bottom: 5px;padding: 0 4px 3px 0;">
                <legend class="scheduler-border">Material Entry Form</legend>
                <div class="control-group">
                    <form id="materialForm" class="form-horizontal" v-on:submit.prevent="saveMaterial">
                        <div class="col-xs-12 col-md-6">
                            <div class="form-group">
                                <label class="control-label col-md-4">Material Id</label>
                                <label class="control-label col-md-1"> : </label>
                                <div class="col-md-6">
                                    <input type="text" name="code" id="code" v-model="material.code"
                                        class="form-control" placeholder="Material Id" disabled="disabled">
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="control-label col-md-4">Category</label>
                                <label class="control-label col-md-1"> : </label>
                                <div class="col-md-6" style="display: flex;align-items:center;margin-bottom:5px;">
                                    <div style="width: 88%;">
                                        <v-select v-bind:options="categories" style="margin:0;" v-model="selectedCategory" label="ProductCategory_Name"></v-select>
                                    </div>
                                    <div style="width:11%;margin-left:2px;">
                                        <span class="add-button" @click.prevent="modalOpen('/add_material_category', 'Add Material Category', 'ProductCategory_Name')"><i class="fa fa-plus"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">Name</label>
                                <label class="control-label col-md-1"> : </label>
                                <div class="col-md-6">
                                    <input type="text" name="name" id="name" v-model="material.name" class="form-control"
                                        placeholder="Material Name" required>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <label class="control-label col-md-4">Unit</label>
                                <label class="control-label col-md-1">:</label>
                                <div class="col-md-6" style="display: flex;align-items:center;margin-bottom:5px;">
                                    <div style="width: 88%;">
                                        <v-select v-bind:options="units" style="margin:0;" v-model="selectedUnit" label="Unit_Name"></v-select>
                                    </div>
                                    <div style="width:11%;margin-left:2px;">
                                        <span class="add-button" @click.prevent="modalOpen('/add_unit', 'Add Unit', 'Unit_Name')"><i class="fa fa-plus"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label col-md-4">Re-Order Level</label>
                                <label class="control-label col-md-1"> : </label>
                                <div class="col-md-6">
                                    <input type="text" name="reorder_level" id="reorder_level" v-model="material.reorder_level"
                                        class="form-control" placeholder="Re-Order Level">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">Purchase Rate</label>
                                <label class="control-label col-md-1"> : </label>
                                <div class="col-md-6">
                                    <input type="text" name="purchase_rate" id="purchase_rate" v-model="material.purchase_rate"
                                        class="form-control" placeholder="Purchase Rate" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-4">Sale Rate</label>
                                <label class="control-label col-md-1"> : </label>
                                <div class="col-md-6">
                                    <input type="text" name="sale_rate" id="sale_rate" v-model="material.sale_rate"
                                        class="form-control" placeholder="Sale Rate" required>
                                </div>
                            </div>

                            <div class="form-group" style="margin-top: 15px;">
                                <div class="col-md-offset-5 col-md-6">
                                    <button type="submit" name="btnSubmit" title="Save" class="btn btn-xs btn-success pull-right">
                                        <span v-if="material.material_id == 0">Save</span>
                                        <span v-if="material.material_id != 0">Update</span>
                                        <i class="ace-icon fa fa-arrow-right icon-on-right bigger-110"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </fieldset>
        </div>
    </div>


    <div class="row">
        <div class="col-xs-12 form-inline">
            <div class="form-group">
                <label for="filter" class="sr-only">Filter</label>
                <input type="text" class="form-control" v-model="filter" placeholder="Filter">
            </div>
        </div>
        <div class="col-xs-12">
            <div class="table-responsive">
                <datatable :columns="columns" :data="materials" :filter-by="filter">
                    <template scope="{ row }">
                        <tr :style="{background: row.status == 1 ? '' : '#ffc356'}">
                            <td>{{ row.code }}</td>
                            <td>{{ row.name }}</td>
                            <td>{{ row.category_name }}</td>
                            <td>{{ row.reorder_level }}</td>
                            <td>{{ row.purchase_rate }}</td>
                            <td>{{ row.sale_rate }}</td>
                            <td>{{ row.unit_name }}</td>
                            <td>{{ row.status_text }}</td>
                            <td>
                                <?php if ($this->session->userdata('accountType') != 'u') { ?>
                                    <i v-if="row.status == 1" class="btnEdit fa fa-pencil" @click="editMaterial(row)"></i>
                                    <i v-bind:class="row.status == 1 ? 'btnDelete fa fa-trash' : 'btnEdit fa fa-check'" @click="changeStatus(row)"></i>
                                <?php } ?>
                            </td>
                        </tr>
                    </template>
                </datatable>
                <datatable-pager v-model="page" type="abbreviated" :per-page="per_page"></datatable-pager>
            </div>
        </div>
    </div>

    <!-- modal form -->
    <div class="modal formModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <form @submit.prevent="saveModalData($event)">
                <div class="modal-content">
                    <div class="modal-header" style="display: flex;align-items: center;justify-content: space-between;">
                        <h5 class="modal-title" v-html="modalTitle"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="padding-top: 0;">
                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" :name="formInput" v-model="fieldValue" class="form-control" autocomplete="off" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btnReset" data-dismiss="modal">Close</button>
                        <button type="submit" class="btnSave">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/vue/vue.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/axios.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vuejs-datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/js/vue/vue-select.min.js"></script>

<script>
    Vue.component('v-select', VueSelect.VueSelect);
    new Vue({
        el: '#materials',
        data() {
            return {
                columns: [{
                        label: 'Material Id',
                        field: 'code',
                        align: 'center',
                        filterable: false
                    },
                    {
                        label: 'Material Name',
                        field: 'name'
                    },
                    {
                        label: 'Category',
                        field: 'category_name'
                    },
                    {
                        label: 'Reorder Level',
                        field: 'reorder_level'
                    },
                    {
                        label: 'Purchase Rate',
                        field: 'purchase_rate'
                    },
                    {
                        label: 'Sale Rate',
                        field: 'sale_rate'
                    },
                    {
                        label: 'Unit',
                        field: 'unit_name'
                    },
                    {
                        label: 'Status',
                        field: 'status_text'
                    },
                    {
                        label: 'Action',
                        filterable: false
                    }
                ],
                page: 1,
                per_page: 10,
                filter: '',
                material: {
                    material_id: 0,
                    code: '<?php echo $materialCode; ?>',
                    name: '',
                    category_id: '',
                    reorder_level: '',
                    sale_rate: '',
                    purchase_rate: '',
                    unit_id: ''
                },
                materials: [],
                units: [],
                categories: [],
                selectedUnit: null,
                selectedCategory: null,

                formInput: '',
                url: '',
                modalTitle: '',
                fieldValue: ''
            }
        },
        created() {
            this.getUnits();
            this.getCategories();
            this.getMaterials();
        },
        methods: {
            getUnits() {
                axios.get('/get_units')
                    .then(res => {
                        this.units = res.data;
                    })
            },
            getCategories() {
                axios.get('/get_material_categories')
                    .then(res => {
                        this.categories = res.data;
                    })
            },
            getMaterials() {
                axios.get('/get_materials')
                    .then(res => {
                        this.materials = res.data;
                    })
            },
            saveMaterial() {
                if (this.selectedCategory == null) {
                    alert('select a category');
                    return;
                }

                if (this.selectedUnit == null) {
                    alert('select a unit');
                    return;
                }


                this.material.unit_id = this.selectedUnit.Unit_SlNo;
                this.material.category_id = this.selectedCategory.ProductCategory_SlNo;

                let url = '/add_material';
                if (this.material.material_id != 0) {
                    url = '/update_material';
                }

                axios.post(url, this.material)
                    .then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.getMaterials();
                            this.resetForm();
                            this.material.code = r.code;
                        }
                    })
            },
            editMaterial(material) {
                let objKey = Object.keys(this.material)
                objKey.forEach(item => {
                    this.material[item] = material[item];
                })
                this.selectedCategory = {
                    ProductCategory_SlNo: material.category_id,
                    ProductCategory_Name: material.category_name
                };
                this.selectedUnit = {
                    Unit_SlNo: material.unit_id,
                    Unit_Name: material.unit_name
                };
            },
            changeStatus(material) {
                axios.post('/change_material_status', material)
                    .then(res => {
                        let r = res.data;
                        alert(r.message);
                        if (r.success) {
                            this.getMaterials();
                        }
                    })
            },
            resetForm() {
                this.material = {
                    material_id: 0,
                    code: '',
                    name: '',
                    category_id: '',
                    reorder_level: '',
                    purchase_rate: '',
                    sale_rate: '',
                    unit_id: ''
                }
                this.selectedUnit = null;
                this.selectedCategory = null;
            },

            // modal data store
            modalOpen(url, title, txt) {
                $(".formModal").modal("show");
                this.formInput = txt;
                this.url = url;
                this.modalTitle = title;
            },

            saveModalData(event) {
                let filter = {}
                if (this.formInput == "ProductCategory_Name") {
                    filter.ProductCategory_Name = this.fieldValue;
                    filter.ProductCategory_Description = "";
                }
                if (this.formInput == "Unit_Name") {
                    filter.Unit_Name = this.fieldValue;
                }

                axios.post(this.url, filter)
                    .then(res => {
                        if (this.formInput == "ProductCategory_Name") {
                            this.getCategories();
                        }
                        if (this.formInput == "Unit_Name") {
                            this.getUnits();
                        }

                        $(".formModal").modal('hide');
                        this.formInput = '';
                        this.url = "";
                        this.modalTitle = '';
                        this.fieldValue = '';
                    })
            },
        }
    })
</script>