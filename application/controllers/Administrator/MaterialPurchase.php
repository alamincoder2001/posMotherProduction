<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MaterialPurchase extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Billing_model');
        $this->load->library('cart');
        $this->load->model('Model_table', "mt", TRUE);
        $this->load->helper('form');
    }

    public function materialPurchase($purchase_id = 0)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Material Purchase";
        $data['purchase_id'] = $purchase_id;
        $data['invoiceNumber'] = $this->mt->generateMaterialPurchaseCode();
        $data['content'] = $this->load->view('Administrator/materials/purchase/material_purchase', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function MaterialPurchaseRecord()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Material Purchase Record";
        $data['content'] = $this->load->view('Administrator/materials/purchase/material_purchase_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getMaterialPurchase()
    {
        $options = json_decode($this->input->raw_input_stream);
        $idClause = "";
        $supplierClause = "";
        $dateClause = "";

        if (isset($options->purchase_id)) {
            $idClause = " and p.purchase_id = '$options->purchase_id'";
        }

        if (isset($options->supplier_id) && $options->supplier_id != null) {
            $supplierClause = " and p.Supplier_SlNo = '$options->supplier_id'";
        }

        if (isset($options->dateFrom) && isset($options->dateTo) && $options->dateFrom != null && $options->dateTo != null) {
            $dateClause = " and p.purchase_date between '$options->dateFrom' and '$options->dateTo'";
        }
        $purchases = $this->db->query("
            select 
                p.*,
                ifnull(s.Supplier_Name, p.supplierName) as supplier_name,
                ifnull(s.Supplier_Code, 'Cash Supplier') as supplier_code,
                ifnull(s.Supplier_Mobile, p.supplierMobile) as supplier_mobile,
                ifnull(s.Supplier_Address, p.supplierAddress) as supplier_address,
                ifnull(s.Supplier_Mobile, p.supplierType) as supplier_type,
                u.User_Name
            from tbl_material_purchase p
            left join tbl_supplier s on s.Supplier_SlNo = p.Supplier_SlNo
            left join tbl_user u on u.User_SlNo = p.AddBy
            where p.status = 'a' $idClause $supplierClause $dateClause
        ")->result();

        foreach ($purchases as $key => $item) {
            $item->purchaseDetails = $this->db->query("
                                    select
                                        pd.*,
                                        m.name
                                    from tbl_material_purchase_details pd
                                    left join tbl_materials m on m.material_id = pd.material_id
                                    where pd.status = 'a'
                                    and pd.purchase_id = ?
                                ", $item->purchase_id)->result();
        }

        echo json_encode($purchases);
    }

    public function getMaterialPurchaseDetails()
    {
        $options = json_decode($this->input->raw_input_stream);
        $clauses = "";
        if (isset($options->purchase_id) && $options->purchase_id != '') {
            $clauses .= " and pd.purchase_id = '$options->purchase_id'";
        }
        if (isset($options->materialId) && $options->materialId != '') {
            $clauses .= " and pd.material_id = '$options->materialId'";
        }
        if (isset($options->categoryId) && $options->categoryId != '') {
            $clauses .= " and m.category_id = '$options->categoryId'";
        }
        $purchaseDetails = $this->db->query("
            select
                pd.*,
                m.name,
                c.ProductCategory_Name as category_name,
                u.Unit_Name as unit_name,
                mp.invoice_no,
                mp.purchase_date,
                ifnull(s.Supplier_Name, mp.supplierName) as supplier_name,
                ifnull(s.Supplier_Code, '') as supplier_code
            from tbl_material_purchase_details pd
            join tbl_material_purchase mp on mp.purchase_id = pd.purchase_id
            left join tbl_supplier s on s.Supplier_SlNo = mp.Supplier_SlNo
            left join tbl_materials m on m.material_id = pd.material_id
            left join tbl_materialcategory c on c.ProductCategory_SlNo = m.category_id
            left join tbl_unit u on u.Unit_SlNo = m.unit_id
            where pd.status = 'a'
            $clauses
        ")->result();

        echo json_encode($purchaseDetails);
    }

    public function addMaterialPurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $countPurchaseCode = $this->db->query("select * from tbl_material_purchase where invoice_no = ?", $data->purchase->invoice_no)->num_rows();
            if ($countPurchaseCode > 0) {
                $data->purchase->invoice_no = $this->mt->generateMaterialPurchaseCode();
            }

            $supplierId = $data->supplier->Supplier_Type == 'G' ? NULL : $data->supplier->Supplier_SlNo;
            if ($data->supplier->Supplier_Type == 'N') {
                $supplier = (array)$data->supplier;
                unset($supplier['Supplier_SlNo']);
                unset($supplier['display_name']);
                $supplier['Supplier_Code']  = $this->mt->generateSupplierCode();
                $supplier['status']         = 'a';
                $supplier['AddBy']          = $this->session->userdata("userId");
                $supplier['AddTime']        = date('Y-m-d H:i:s');
                $supplier['last_update_ip'] = get_client_ip();
                $supplier['branch_id']      = $this->session->userdata('BRANCHid');
                $this->db->insert('tbl_supplier', $supplier);
                $supplierId = $this->db->insert_id();
            }

            $purchase = array(
                "Supplier_SlNo"  => $supplierId,
                "invoice_no"     => $data->purchase->invoice_no,
                "purchase_date"  => $data->purchase->purchase_date,
                "purchase_for"   => $data->purchase->purchase_for,
                "sub_total"      => $data->purchase->sub_total,
                "vat"            => $data->purchase->vat,
                "transport_cost" => $data->purchase->transport_cost,
                "discount"       => $data->purchase->discount,
                "total"          => $data->purchase->total,
                "paid"           => $data->purchase->paid,
                "due"            => $data->purchase->due,
                "previous_due"   => $data->purchase->previous_due,
                "note"           => $data->purchase->note,
                'status'         => 'a',
                'AddBy'          => $this->session->userdata("userId"),
                'AddTime'        => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id'      => $this->session->userdata('BRANCHid')
            );
            if ($data->supplier->Supplier_Type == 'G') {
                $purchase['Supplier_SlNo']    = Null;
                $purchase['supplierType']    = "G";
                $purchase['supplierName']    = $data->supplier->Supplier_Name;
                $purchase['supplierMobile']  = $data->supplier->Supplier_Mobile;
                $purchase['supplierAddress'] = $data->supplier->Supplier_Address;
            } else {
                $purchase['supplierType'] = $data->supplier->Supplier_Type == 'N' ? "retail" : 'retail';
                $purchase['Supplier_SlNo'] = $supplierId;
            }
            $this->db->insert('tbl_material_purchase', $purchase);
            $lastId = $this->db->insert_id();

            foreach ($data->purchasedMaterials as $purchasedMaterial) {
                $pm = array(
                    "purchase_id"    => $lastId,
                    "material_id"    => $purchasedMaterial->material_id,
                    "purchase_rate"  => $purchasedMaterial->purchase_rate,
                    "quantity"       => $purchasedMaterial->quantity,
                    "total"          => $purchasedMaterial->total,
                    "status"         => 'a',
                    'AddBy'          => $this->session->userdata("userId"),
                    'AddTime'        => date('Y-m-d H:i:s'),
                    'last_update_ip' => get_client_ip(),
                    'branch_id'      => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_material_purchase_details', $pm);
            }

            $res = ['success' => true, 'message' => 'Material Purchase Success', 'purchaseId' => $lastId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateMaterialPurchase()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $supplierId = $data->supplier->Supplier_Type == 'G' ? NULL : $data->supplier->Supplier_SlNo;
            if ($data->supplier->Supplier_Type == 'N') {
                $supplier = (array)$data->supplier;
                unset($supplier['Supplier_SlNo']);
                unset($supplier['display_name']);
                $supplier['Supplier_Code']  = $this->mt->generateSupplierCode();
                $supplier['status']         = 'a';
                $supplier['AddBy']          = $this->session->userdata("userId");
                $supplier['AddTime']        = date('Y-m-d H:i:s');
                $supplier['last_update_ip'] = get_client_ip();
                $supplier['branch_id']      = $this->session->userdata('BRANCHid');
                $this->db->insert('tbl_supplier', $supplier);
                $supplierId = $this->db->insert_id();
            }

            $purchase = array(
                "Supplier_SlNo"  => $supplierId,
                "invoice_no"     => $data->purchase->invoice_no,
                "purchase_date"  => $data->purchase->purchase_date,
                "purchase_for"   => $data->purchase->purchase_for,
                "sub_total"      => $data->purchase->sub_total,
                "vat"            => $data->purchase->vat,
                "transport_cost" => $data->purchase->transport_cost,
                "discount"       => $data->purchase->discount,
                "total"          => $data->purchase->total,
                "paid"           => $data->purchase->paid,
                "due"            => $data->purchase->due,
                "previous_due"   => $data->purchase->previous_due,
                "note"           => $data->purchase->note,
                'UpdateBy'       => $this->session->userdata("userId"),
                'UpdateTime'     => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id'      => $this->session->userdata('BRANCHid')
            );
            if ($data->supplier->Supplier_Type == 'G') {
                $purchase['Supplier_SlNo']    = Null;
                $purchase['supplierType']    = "G";
                $purchase['supplierName']    = $data->supplier->Supplier_Name;
                $purchase['supplierMobile']  = $data->supplier->Supplier_Mobile;
                $purchase['supplierAddress'] = $data->supplier->Supplier_Address;
            } else {
                $purchase['supplierType'] = $data->supplier->Supplier_Type == 'N' ? "retail" : 'retail';
                $purchase['Supplier_SlNo'] = $supplierId;
            }
            $this->db->where('purchase_id', $data->purchase->purchase_id);
            $this->db->set($purchase);
            $this->db->update('tbl_material_purchase');

            $this->db->delete('tbl_material_purchase_details', array('purchase_id' => $data->purchase->purchase_id));
            foreach ($data->purchasedMaterials as $purchasedMaterial) {
                $pm = array(
                    "purchase_id"    => $data->purchase->purchase_id,
                    "material_id"    => $purchasedMaterial->material_id,
                    "purchase_rate"  => $purchasedMaterial->purchase_rate,
                    "quantity"       => $purchasedMaterial->quantity,
                    "total"          => $purchasedMaterial->total,
                    "status"         => 'a',
                    'UpdateBy'       => $this->session->userdata("userId"),
                    'UpdateTime'     => date('Y-m-d H:i:s'),
                    'last_update_ip' => get_client_ip(),
                    'branch_id'      => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_material_purchase_details', $pm);
            }

            $res = ['success' => true, 'message' => 'Updated Successfully', 'purchaseId' => $data->purchase->purchase_id];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteMaterialPurchase()
    {
        $data = json_decode($this->input->raw_input_stream);
        $res = ['success' => false, 'message' => ''];
        try {
            $purchase = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip(),
            );

            $this->db->set($purchase)->where('purchase_id', $data->purchase_id)->update('tbl_material_purchase');
            $this->db->set($purchase)->where('purchase_id', $data->purchase_id)->update('tbl_material_purchase_details');
            $res = ['success' => true, 'message' => 'Purchase deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function materialPurchaseInvoice($purchaseId)
    {
        $data['title'] = "Material Purchase Invoice";
        $data['purchaseId'] = $purchaseId;
        $data['content'] = $this->load->view("Administrator/materials/purchase/material_purchase_invoice", $data, true);
        $this->load->view("Administrator/index", $data);
    }
}
