@extends('layouts.codebase.master')

@section('title')
    @lang('warehouse_inflow.index.title')
@endsection

@section('page_title')
    <span class="fa fa-mail-forward fa-rotate-90 fa-fw"></span>
    @lang('warehouse_inflow.index.page_title')
@endsection

@section('page_title_desc')
    @lang('warehouse_inflow.index.page_title_desc')
@endsection

@section('breadcrumbs')

@endsection

@section('content')
    <div id="inflowVue">
        @include ('layouts.common.error')
        <div class="block block-shadow-on-hover block-mode-loading-refresh" id="inflowListBlock">
            <div class="block-header block-header-default">
                <h3 class="block-title">@lang('warehouse_inflow.index.panel.list_panel.title')</h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
                    <button type="button" class="btn-block-option" v-on:click="renderInflowtData">
                        <i class="si si-refresh"></i>
                    </button>
                    <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
                </div>
            </div>
            <div class="block-content">
                <select id="inputWarehouse"
                        class="form-control"
                        v-model="selectedWarehouse"
                        v-on:change="renderInflowtData(selectedWarehouse)">
                    <option v-bind:value="defaultPleaseSelect">@lang('labels.PLEASE_SELECT')</option>
                    <option v-for="warehouse in warehouseDDL" v-bind:value="warehouse.hId">@{{ warehouse.name }} @{{ warehouse.address != '' ? '- ' + warehouse.address:''}}</option>
                </select>
                <br/>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-vcenter">
                        <thead class="thead-light">
                            <tr>
                                <th>@lang('warehouse_inflow.index.table.po_list.header.code')</th>
                                <th>@lang('warehouse_inflow.index.table.po_list.header.supplier')</th>
                                <th>@lang('warehouse_inflow.index.table.po_list.header.shipping_date')</th>
                                <th>@lang('warehouse_inflow.index.table.po_list.header.receipt')</th>
                                <th class="text-center" width="10%">@lang('labels.ACTION')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="poWAList.length == 0">
                                <td colspan="5" class="text-center">@lang('labels.DATA_NOT_FOUND')</td>
                            </tr>
                            <tr v-for="(p, pIdx) in poWAList">
                                <td>@{{ p.code }}</td>
                                <td>@{{ p.supplier_type == 'SUPPLIERTYPE.WI' ? p.walk_in_supplier : p.supplier.name }}</td>
                                <td>@{{ p.shipping_date }}</td>
                                <td>0</td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-secondary" v-on:click="createNew(pIdx)">
                                            <span class="fa fa-plus fa-fw"></span>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" id="btnEdit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fa fa-pencil fa-fw"></span></button>
                                        <div class="dropdown-menu" aria-labelledby="btnEdit">
                                            <a class="dropdown-item" href="javascript:void(0)">
                                                Receipt 1
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0)">
                                                Receipt 2
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="javascript:void(0)">
                                                Final Receipt
                                            </a>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" id="btnDelete" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fa fa-close fa-fw"></span></button>
                                        <div class="dropdown-menu" aria-labelledby="btnDelete">
                                            <a class="dropdown-item" href="javascript:void(0)">
                                                Receipt 1
                                            </a>
                                            <a class="dropdown-item" href="javascript:void(0)">
                                                Receipt 2
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="javascript:void(0)">
                                                Final Receipt
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row items-push-2x text-center text-sm-left">
                    <div class="col-sm-6 col-xl-4">
                        <button type="button" class="btn btn-primary btn-lg btn-circle" data-toggle="tooltip" data-placement="top" title="{{ Lang::get('buttons.print_preview_button') }}">
                            <i class="fa fa-print fa-fw"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="block block-shadow-on-hover block-mode-loading-refresh" id="inflowCRUDBlock">
            <div class="block-header block-header-default">
                <h3 class="block-title">
                    <template v-if="mode == 'create'">@lang('warehouse_inflow.index.panel.crud_panel.title_create')</template>
                    <template v-if="mode == 'show'">@lang('warehouse_inflow.index.panel.crud_panel.title_show')</template>
                    <template v-if="mode == 'edit'">@lang('warehouse_inflow.index.panel.crud_panel.title_edit')</template>
                </h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" data-toggle="block-option" data-action="fullscreen_toggle"></button>
                    <button type="button" class="btn-block-option" data-toggle="block-option" data-action="content_toggle"></button>
                </div>
            </div>
            <div class="block-content">
                <form id="inflowForm" method="post" v-on:submit.prevent="validateBeforeSubmit">
                    <div v-bind:class="{ 'form-group row':true, 'is-invalid':errors.has('receipt_date') }">
                        <label for="inputReceiptDate" class="col-3 col-form-label">@lang('warehouse_inflow.fields.receipt_date')</label>
                        <div class="col-md-9">
                            <flat-pickr id="inputReceiptDate" class="form-control"
                                        v-model="receipt.receipt_date" v-bind:config="defaultFlatPickrConfig"
                                        v-validate="'required'" data-vv-as="{{ trans('warehouse_inflow.fields.receipt_date') }}"
                                        data-vv-name="{{ trans('warehouse_inflow.fields.receipt_date') }}"
                                        v-on:input="onChangeReceiptDate"></flat-pickr>
                            <span v-show="errors.has('receipt_date')" class="invalid-feedback">@{{ errors.first('receipt_date') }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputVendorTrucking" class="col-3 col-form-label">@lang('warehouse_inflow.fields.vendor_trucking')</label>
                        <div class="col-md-9">
                            <template v-if="mode == 'create' || mode == 'edit'">
                                <select id="inputVendorTrucking" name="vendor_trucking_id" class="form-control"
                                        v-model="receipt.vendorTruckingHId" v-on:change="onChangeVendorTrucking">
                                    <option v-bind:value="defaultPleaseSelect">@lang('labels.PLEASE_SELECT')</option>
                                    <option v-for="(vendorTrucking, vendorTruckingIdx) of vendorTruckingDDL" v-bind:value="vendorTrucking.hId">@{{ vendorTrucking.name }}</option>
                                </select>
                            </template>
                        </div>
                    </div>
                    <div v-bind:class="{ 'form-group row':true, 'is-invalid':errors.has('license_plate') }">
                        <label for="inputLicensePlate" class="col-3 col-form-label">@lang('warehouse_inflow.fields.license_plate')</label>
                        <div class="col-md-9">
                            <select id="selectLicensePlate" class="form-control" name="truck_id"
                                    v-model="receipt.truckHId">
                                <option v-bind:value="defaultPleaseSelect">@lang('labels.PLEASE_SELECT')</option>
                                <option v-for="(truck, truckIdx) of truckDDL" v-bind:value="truck.hId">@{{ truck.license_plate }}</option>
                            </select>
                        </div>
                    </div>
                    <div v-bind:class="{ 'form-group row':true, 'is-invalid':errors.has('driver') }">
                        <label for="inputDriverName" class="col-3 col-form-label">@lang('warehouse_inflow.fields.driver_name')</label>
                        <div class="col-md-9">
                            <input id="inputDriverName" name="driver" v-model="receipt.driver" type="text" class="form-control" placeholder="{{ trans('warehouse_inflow.fields.driver_name') }}">
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <table id="receiptListTable" class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50%">@lang('warehouse_inflow.index.table.item_table.header.product_name')</th>
                                        <th width="15%" class="text-center">@lang('warehouse_inflow.index.table.item_table.header.unit')</th>
                                        <th width="10%" class="text-center">@lang('warehouse_inflow.index.table.item_table.header.brutto')</th>
                                        <th width="10%" class="text-center">@lang('warehouse_inflow.index.table.item_table.header.netto')</th>
                                        <th width="10%" class="text-center">@lang('warehouse_inflow.index.table.item_table.header.tare')</th>
                                        <th width="5%" class="text-center">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(rd, rdIdx) in receipt.receipt_details">
                                        <input type="hidden" name="item_id[]" v-bind:value="rd.item.hId">
                                        <input type="hidden" name="product_id[]" v-bind:value="rd.item.productHId">
                                        <input type="hidden" name="base_product_unit_id[]" v-bind:value="rd.item.baseProductUnitHId">
                                        <td>
                                            @{{ rd.item.product.name }}
                                        </td>
                                        <td v-bind:class="{ 'is-invalid':errors.has('punit_' + rdIdx) }">
                                            <select name="selected_product_unit_id[]"
                                                    class="form-control"
                                                    v-model="rd.selectedProductUnitsHId"
                                                    v-validate="'required'"
                                                    v-bind:disabled="readOnly"
                                                    v-bind:data-vv-as="'{{ trans('warehouse_inflow.index.table.item_table.header.unit') }} ' + (rdIdx + 1)"
                                                    v-bind:data-vv-name="'punit_' + rdIdx">
                                                <option value="">@lang('labels.PLEASE_SELECT')</option>
                                                <option v-for="product_unit in rd.item.product.product_units" v-bind:value="product_unit.unit.hId">@{{ product_unit.unit.name }} (@{{ product_unit.unit.symbol }})</option>
                                            </select>
                                        </td>
                                        <td v-bind:class="{ 'is-invalid':errors.has('brutto_' + rdIdx) }">
                                            <vue-autonumeric v-bind:id="'brutto_' + rd.item.hId" type="text" class="form-control text-right" name="brutto[]"
                                                    v-model="rd.brutto" v-validate="readOnly ? '':'required'"
                                                    v-bind:readonly="readOnly"
                                                    v-bind:data-vv-as="'{{ trans('warehouse_inflow.index.table.item_table.header.brutto') }} ' + (rdIdx + 1)"
                                                    v-bind:data-vv-name="'brutto_' + rdIdx"
                                                    v-on:input="reValidate('brutto', rdIdx)"></vue-autonumeric>
                                        </td>
                                        <td v-bind:class="{ 'is-invalid':errors.has('netto_' + rdIdx) }">
                                            <vue-autonumeric v-bind:id="'netto_' + rd.item.hId" type="text" class="form-control text-right" name="netto[]"
                                                    v-model="rd.netto" v-validate="readOnly ? '':'required'"
                                                    v-bind:readonly="readOnly"
                                                    v-bind:data-vv-as="'{{ trans('warehouse_inflow.index.table.item_table.header.netto') }} ' + (rdIdx + 1)"
                                                    v-bind:data-vv-name="'netto_' + rdIdx"
                                                    v-on:input="reValidate('netto', rdIdx)"></vue-autonumeric>
                                        </td>
                                        <td v-bind:class="{ 'is-invalid':errors.has('tare_' + rdIdx) }">
                                            <vue-autonumeric v-bind:id="'tare_' + rd.item.hId" type="text" class="form-control text-right" name="tare[]"
                                                    v-model="rd.tare" v-validate="readOnly ? '':'required'"
                                                    v-bind:readonly="readOnly"
                                                    v-bind:data-vv-as="'{{ trans('warehouse_inflow.index.table.item_table.header.tare') }} ' + (rdIdx + 1)"
                                                    v-bind:data-vv-name="'tare_' + rdIdx"
                                                    v-on:change="reValidate('tare', rdIdx)"></vue-autonumeric>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-md" v-on:click="removeReceipt(rdIdx)" disabled><span class="fa fa-minus"/></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputRemarks" class="col-3 col-form-label">@lang('warehouse_inflow.fields.remarks')</label>
                        <div class="col-md-9">
                            <template v-if="mode == 'create' || mode == 'edit'">
                                <input type="text" class="form-control" id="inputRemarks" name="remarks" v-model="receipt.remarks" placeholder="@lang('warehouse_inflow.fields.remarks')">
                            </template>
                            <template v-if="mode == 'show'">
                                <div class="form-control-plaintext">@{{ receipt.remarks }}</div>
                            </template>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-3 col-form-label" for="inputButton">&nbsp;</label>
                        <div class="col-9">
                            <template v-if="mode == 'create' || mode == 'edit'">
                                <button type="submit" class="btn btn-primary min-width-125">
                                    @lang('buttons.submit_button')
                                </button>
                                <button type="button" class="btn btn-default min-width-125" v-on:click="backToList">
                                    @lang('buttons.cancel_button')
                                </button>
                            </template>
                            <template v-if="mode == 'show'">
                                <button type="button" class="btn btn-default min-width-125" v-on:click="backToList">
                                    @lang('buttons.back_button')
                                </button>
                            </template>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('ziggy')
    @routes('warehouse_inflow')
@endsection

@section('custom_js')
    <script type="application/javascript">
        var inflowVue = new Vue ({
            el: '#inflowVue',
            data: {
                mode: '',
                warehouseDDL: [],
                vendorTruckingDDL: [],
                truckDDL: [],
                expenseTypeDDL: [],
                selectedWarehouse: '',
                poWAList: [],
                po: { },
                receipt: {
                    hId: '',
                    receipt_date: new Date(),
                    vendorTruckingHId: '',
                    truckHId: '',
                    article_code: '',
                    driver_name: '',
                    receipt_details: [],
                    remarks: ''
                },
                expenses: [

                ]
            },
            mounted: function () {
                this.$validator.extend('checkequal', {
                    getMessage: (field, args) => {
                        return this.$validator.locale == 'id' ?
                            'Nilai bersih dan Tara tidak sama dengan Nilai Kotor':'Netto and Tare value not equal with Bruto';
                    },
                    validate: (value, args) => {
                        var result = false;
                        var itemIdx = args[0];

                        if (this.po == undefined) { result = true; }
                        if (this.po.receipts == undefined) { result = true; }

                        if (this.po.receipts[itemIdx].brutto ==
                            this.po.receipts[itemIdx].netto + this.po.receipts[itemIdx].tare) {
                            result = true;
                        }

                        return result;
                    }
                });

                this.mode = 'list';
                this.renderInflowtData();
            },
            methods: {
                validateBeforeSubmit: function() {
                    this.$validator.validateAll().then(isValid => {
                        if (!isValid) { return; }
                        this.errors.clear();
                        if (this.mode == 'create') {
                            axios.post(route('api.post.warehouse.inflow.save', this.po.hId).url(), new FormData($('#inflowForm')[0])).then(response => {
                            }).catch(e => {
                                this.handleErrors(e);
                            });
                        } else if (this.mode == 'edit') {
                            axios.post(route('api.post.warehouse.inflow.edit', this.po.hId).url(), new FormData($('#inflowForm')[0])).then(response => {
                            }).catch(e => {
                                this.handleErrors(e);
                            });
                        } else { }
                    });
                },
                createNew: function(index) {
                    this.mode = 'create';
                    this.errors.clear();
                    this.po = Object.assign({ }, this.poWAList[index]);

                    for (var i = 0; i < this.po.items.length; i++) {
                        this.receipt.receipt_details.push({
                            item: _.cloneDeep(this.po.items[i]),
                            selected_product_units: {
                                hId: ''
                            },
                            selectedProductUnitsHId: '',
                            base_product_unit: _.cloneDeep(_.find(this.po.items[i].product.product_units, { is_base: 1 })),
                            baseProductUnitHId: _.cloneDeep(_.find(this.po.items[i].product.product_units, { is_base: 1 })).hId,
                            brutto: 0,
                            netto: 0,
                            tare: 0
                        });
                    };
                },
                editSelected: function(idx) {
                    this.mode = 'edit';
                    this.errors.clear();
                },
                deleteSelected: function(idx) {
                },
                renderInflowtData: function() {
                    this.loadingPanel('#inflowListBlock', 'TOGGLE');
                    Promise.all([
                        this.getWarehouse(),
                        this.getVendorTrucking(),
                        this.getExpenseType(),
                        this.getPOWAList(this.selectedWarehouse)
                    ]).then(() => {
                        this.loadingPanel('#inflowListBlock', 'TOGGLE');
                    });
                },
                getWarehouse: function() {
                    return new Promise((resolve, reject) => {
                        axios.get(route('api.get.warehouse.read').url()).then(response => {
                            this.warehouseDDL = response.data;
                            resolve(true);
                        }).catch(e => {
                            this.handleErrors(e);
                            reject(e.response.data.message);
                        });
                    });
                },
                getPOWAList: function(warehouseId) {
                    return new Promise((resolve, reject) => {
                        if (warehouseId == '') {
                            resolve(true);
                            return;
                        }

                        this.poWAList = [];
                        axios.get(route('api.get.po.status.waiting_arrival', warehouseId).url()).then(response => {
                            this.poWAList = response.data;
                            resolve(true);
                        }).catch(e => {
                            this.handleErrors(e);
                            reject(e.response.data.message);
                        });
                    });
                },
                backToList: function() {
                    this.mode = 'list';
                    this.errors.clear();
                    this.renderInflowtData();
                },
                getExpenseType: function() {
                    axios.get(route('api.get.lookup.bycategory', 'EXPENSE_TYPE').url()).then(
                        response => { this.expenseTypeDDL = response.data; }
                    );
                },
                getVendorTrucking: function() {
                    return new Promise((resolve, reject) => {
                        axios.get(route('api.get.truck.vendor_trucking.read').url()).then(response => {
                            this.vendorTruckingDDL = response.data;
                            resolve(true);
                        }).catch(e => {
                            this.handleErrors(e);
                            reject(e.response.data.message);
                        });
                    });
                },
                reValidate: function(field, idx) {
                    if (field == 'brutto') {
                        this.$validator.validate('netto_' + idx);
                        this.$validator.validate('tare_' + idx);
                    } else if (field == 'netto') {
                        this.$validator.validate('brutto_' + idx);
                        this.$validator.validate('tare_' + idx);
                    } else {
                        this.$validator.validate('brutto_' + idx);
                        this.$validator.validate('netto_' + idx);
                    }
                },
                onChangeProductUnit: function(itemIndex) {
                    if (this.po.receipts[itemIndex].selectedProductUnitsHId != '') {
                        var pUnit = _.find(this.po.receipts[itemIndex].item.product.product_units, { hId: this.po.receipts[itemIndex].selectedProductUnitsHId });
                        _.merge(this.po.receipts[itemIndex].selected_product_units, pUnit);
                    }
                },
                onChangeVendorTrucking: function() {
                    this.truckDDL = [];
                    this.receipt.truckHId = '';
                    if (this.receipt.vendorTruckingHId != '') {
                        this.truckDDL = _.find(this.vendorTruckingDDL, { hId: this.receipt.vendorTruckingHId }).trucks;
                    }
                },
                addExpense: function () {
                    if (!this.po.hasOwnProperty('expenses')) {
                        this.po.expenses = [];
                    }

                    this.po.expenses.push({
                        hId: '',
                        name: '',
                        type: 'EXPENSETYPE.ADD',
                        is_internal_expense: true,
                        is_internal_expense_val: 1,
                        amount: 0,
                        remarks: ''
                    });
                },
                removeExpense: function (index) {
                    this.po.expenses.splice(index, 1);
                }
            },
            watch: {
                mode: function() {
                    switch (this.mode) {
                        case 'create':
                        case 'edit':
                        case 'show':
                            this.contentPanel('#inflowListBlock', 'CLOSE')
                            this.contentPanel('#inflowCRUDBlock', 'OPEN')
                            break;
                        case 'list':
                        default:
                            this.contentPanel('#inflowListBlock', 'OPEN')
                            this.contentPanel('#inflowCRUDBlock', 'CLOSE')
                            break;
                    }
                }
            },
            computed: {
                defaultPleaseSelect: function() {
                    return '';
                },
                numericFormatToString: function() {
                    var conf = Object.assign({}, this.defaultNumericConfig);

                    conf.readOnly = true;
                    conf.noEventListeners = true;

                    return conf;
                }
            }
        });
    </script>
    <script type="application/javascript" src="{{ mix('js/apps/warehouse_inflow.js') }}"></script>
@endsection