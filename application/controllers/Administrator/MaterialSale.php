<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MaterialSale extends CI_Controller
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

    public function materialSale($sale_id = 0)
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Material Sale";
        $data['sale_id'] = $sale_id;
        $data['invoiceNumber'] = $this->mt->generateMaterialSaleCode();
        $data['content'] = $this->load->view('Administrator/materials/sale/material_sale', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function MaterialSaleRecord()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Material Sale Record";
        $data['content'] = $this->load->view('Administrator/materials/sale/material_sale_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getMaterialSale()
    {
        $options = json_decode($this->input->raw_input_stream);
        $clauses = "";

        if (isset($options->sale_id)) {
            $clauses .= " and p.sale_id = '$options->sale_id'";
        }

        if (isset($options->customer_id) && $options->customer_id != null) {
            $clauses .= " and p.Customer_SlNo = '$options->customer_id'";
        }

        if (isset($options->dateFrom) && isset($options->dateTo) && $options->dateFrom != null && $options->dateTo != null) {
            $clauses .= " and p.sale_date between '$options->dateFrom' and '$options->dateTo'";
        }
        $sales = $this->db->query("
            select 
                p.*,
                ifnull(c.Customer_Name, p.customerName) as customer_name,
                ifnull(c.Customer_Code, '') as customer_code,
                ifnull(c.Customer_Mobile, p.customerMobile) as customer_mobile,
                ifnull(c.Customer_Address, p.customerAddress) as customer_address,
                ifnull(c.Customer_Mobile, p.customerType) as customer_type,
                u.User_Name
            from tbl_material_sale p
            left join tbl_customer c on c.Customer_SlNo = p.Customer_SlNo
            left join tbl_user u on u.User_SlNo = p.AddBy
            where p.status = 'a' $clauses
        ")->result();

        foreach ($sales as $key => $item) {
            $item->saleDetails = $this->db->query("
                                    select
                                        pd.*,
                                        m.name
                                    from tbl_material_sale_details pd
                                    left join tbl_materials m on m.material_id = pd.material_id
                                    where pd.status = 'a'
                                    and pd.sale_id = ?
                                ", $item->sale_id)->result();
        }

        echo json_encode($sales);
    }

    public function getMaterialSaleDetails()
    {
        $options = json_decode($this->input->raw_input_stream);
        $clauses = "";
        if (isset($options->sale_id) && $options->sale_id != '') {
            $clauses .= " and pd.sale_id = '$options->sale_id'";
        }
        if (isset($options->materialId) && $options->materialId != '') {
            $clauses .= " and pd.material_id = '$options->materialId'";
        }
        if (isset($options->categoryId) && $options->categoryId != '') {
            $clauses .= " and m.category_id = '$options->categoryId'";
        }
        $saleDetails = $this->db->query("
            select
                pd.*,
                m.name,
                pc.ProductCategory_Name as category_name,
                u.Unit_Name as unit_name,
                mp.invoice_no,
                mp.sale_date,
                ifnull(c.Customer_Name, mp.customerName) as customer_name,
                ifnull(c.Customer_Code, '') as customer_code
            from tbl_material_sale_details pd
            join tbl_material_sale mp on mp.sale_id = pd.sale_id
            left join tbl_customer c on c.Customer_SlNo = mp.Customer_SlNo
            left join tbl_materials m on m.material_id = pd.material_id
            left join tbl_productcategory pc on pc.ProductCategory_SlNo = m.category_id
            left join tbl_unit u on u.Unit_SlNo = m.unit_id
            where pd.status = 'a'
            $clauses
        ")->result();

        echo json_encode($saleDetails);
    }

    public function addMaterialSale()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $countSaleCode = $this->db->query("select * from tbl_material_sale where invoice_no = ?", $data->sale->invoice_no)->num_rows();
            if ($countSaleCode > 0) {
                $data->sale->invoice_no = $this->mt->generateMaterialSaleCode();
            }

            $customerId = $data->customer->Customer_Type == 'G' ? NULL : $data->customer->Customer_SlNo;
            if ($data->customer->Customer_Type == 'N') {
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                $customer['Customer_Code']  = $this->mt->generateCustomerCode();
                $customer['status']         = 'a';
                $customer['AddBy']          = $this->session->userdata("userId");
                $customer['AddTime']        = date('Y-m-d H:i:s');
                $customer['last_update_ip'] = get_client_ip();
                $customer['branch_id']      = $this->session->userdata('BRANCHid');
                $this->db->insert('tbl_customer', $customer);
                $customerId = $this->db->insert_id();
            }

            $sale = array(
                "Customer_SlNo"  => $customerId,
                "invoice_no"     => $data->sale->invoice_no,
                "sale_date"      => $data->sale->sale_date,
                "sale_for"       => $data->sale->sale_for,
                "sub_total"      => $data->sale->sub_total,
                "vat"            => $data->sale->vat,
                "transport_cost" => $data->sale->transport_cost,
                "discount"       => $data->sale->discount,
                "total"          => $data->sale->total,
                "paid"           => $data->sale->paid,
                "due"            => $data->sale->due,
                "previous_due"   => $data->sale->previous_due,
                "note"           => $data->sale->note,
                'status'         => 'a',
                'AddBy'          => $this->session->userdata("userId"),
                'AddTime'        => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id'      => $this->session->userdata('BRANCHid')
            );
            if ($data->customer->Customer_Code == 'G') {
                $sale['Customer_SlNo']    = Null;
                $sale['customerType']    = "G";
                $sale['customerName']    = $data->customer->Customer_Name;
                $sale['customerMobile']  = $data->customer->Customer_Mobile;
                $sale['customerAddress'] = $data->customer->Customer_Address;
            } else {
                $sale['customerType'] = $data->customer->Customer_Code == 'N' ? "retail" : 'retail';
                $sale['Customer_SlNo'] = $customerId;
            }
            $this->db->insert('tbl_material_sale', $sale);
            $lastId = $this->db->insert_id();

            foreach ($data->saledMaterials as $saledMaterial) {
                $pm = array(
                    "sale_id"        => $lastId,
                    "material_id"    => $saledMaterial->material_id,
                    "sale_rate"      => $saledMaterial->sale_rate,
                    "quantity"       => $saledMaterial->quantity,
                    "total"          => $saledMaterial->total,
                    "status"         => 'a',
                    'AddBy'          => $this->session->userdata("userId"),
                    'AddTime'        => date('Y-m-d H:i:s'),
                    'last_update_ip' => get_client_ip(),
                    'branch_id'      => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_material_sale_details', $pm);
            }

            $res = ['success' => true, 'message' => 'Material Sale Success', 'saleId' => $lastId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateMaterialSale()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $customerId = $data->customer->Customer_Type == 'G' ? NULL : $data->customer->Customer_SlNo;
            if ($data->customer->Customer_Type == 'N') {
                $customer = (array)$data->customer;
                unset($customer['Customer_SlNo']);
                unset($customer['display_name']);
                $customer['Customer_Code']  = $this->mt->generateCustomerCode();
                $customer['status']         = 'a';
                $customer['AddBy']          = $this->session->userdata("userId");
                $customer['AddTime']        = date('Y-m-d H:i:s');
                $customer['last_update_ip'] = get_client_ip();
                $customer['branch_id']      = $this->session->userdata('BRANCHid');
                $this->db->insert('tbl_customer', $customer);
                $customerId = $this->db->insert_id();
            }

            $sale = array(
                "Customer_SlNo"    => $customerId,
                "invoice_no"     => $data->sale->invoice_no,
                "sale_date"      => $data->sale->sale_date,
                "sale_for"       => $data->sale->sale_for,
                "sub_total"      => $data->sale->sub_total,
                "vat"            => $data->sale->vat,
                "transport_cost" => $data->sale->transport_cost,
                "discount"       => $data->sale->discount,
                "total"          => $data->sale->total,
                "paid"           => $data->sale->paid,
                "due"            => $data->sale->due,
                "previous_due"   => $data->sale->previous_due,
                "note"           => $data->sale->note,
                'UpdateBy'       => $this->session->userdata("userId"),
                'UpdateTime'     => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id'      => $this->session->userdata('BRANCHid')
            );
            if ($data->customer->Customer_Type == 'G') {
                $sale['Customer_SlNo']   = Null;
                $sale['CustomerType']    = "G";
                $sale['CustomerName']    = $data->customer->Customer_Name;
                $sale['CustomerMobile']  = $data->customer->Customer_Mobile;
                $sale['CustomerAddress'] = $data->customer->Customer_Address;
            } else {
                $sale['customerType']  = $data->customer->Customer_Type == 'N' ? "retail" : 'retail';
                $sale['Customer_SlNo'] = $customerId;
            }
            $this->db->where('sale_id', $data->sale->sale_id);
            $this->db->set($sale);
            $this->db->update('tbl_material_sale');

            $this->db->delete('tbl_material_sale_details', array('sale_id' => $data->sale->sale_id));
            foreach ($data->saledMaterials as $saledMaterial) {
                $pm = array(
                    "sale_id"        => $data->sale->sale_id,
                    "material_id"    => $saledMaterial->material_id,
                    "sale_rate"      => $saledMaterial->sale_rate,
                    "quantity"       => $saledMaterial->quantity,
                    "total"          => $saledMaterial->total,
                    "status"         => 'a',
                    'UpdateBy'       => $this->session->userdata("userId"),
                    'UpdateTime'     => date('Y-m-d H:i:s'),
                    'last_update_ip' => get_client_ip(),
                    'branch_id'      => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_material_sale_details', $pm);
            }

            $res = ['success' => true, 'message' => 'Updated Successfully', 'saleId' => $data->sale->sale_id];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteMaterialSale()
    {
        $data = json_decode($this->input->raw_input_stream);
        $res = ['success' => false, 'message' => ''];
        try {
            $sale = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip(),
            );

            $this->db->set($sale)->where('sale_id', $data->sale_id)->update('tbl_material_sale');
            $this->db->set($sale)->where('sale_id', $data->sale_id)->update('tbl_material_sale_details');
            $res = ['success' => true, 'message' => 'Material Sale deleted'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function materialSaleInvoice($saleId)
    {
        $data['title'] = "Material Sale Invoice";
        $data['saleId'] = $saleId;
        $data['content'] = $this->load->view("Administrator/materials/sale/material_sale_invoice", $data, true);
        $this->load->view("Administrator/index", $data);
    }
}
