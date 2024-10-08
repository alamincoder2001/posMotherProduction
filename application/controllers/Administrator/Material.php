<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Material extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->brunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Model_table', "mt", TRUE);
    }
    public function materials()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = 'Materials';
        $data['materialCode'] = $this->mt->generateMaterialCode();
        $data['content'] = $this->load->view('Administrator/materials/materials', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getMaterials()
    {
        $materials = $this->db->query("
            select 
            m.*, 
            concat(m.name, ' - ', m.code) as display_text,
            c.ProductCategory_Name as category_name, 
            u.Unit_Name as unit_name,
            case
                when m.status = 1 then 'Active'
                when m.status = 0 then 'Inactive'
            end as status_text
            from tbl_materials m
            left join tbl_materialcategory c on c.ProductCategory_SlNo = m.category_id
            left join tbl_unit u on u.Unit_SlNo = m.unit_id
        ")->result();
        echo json_encode($materials);
    }

    public function addMaterial()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $nameQuery = $this->db->query("select * from tbl_materials where name = '$data->name'");
            $nameCount = $nameQuery->num_rows();
            if ($nameCount != 0) {
                $res = ['success' => false, 'message' => 'Duplicate material name ' . $data->name];
                echo json_encode($res);
                exit;
            }

            $codeQuery = $this->db->query("select * from tbl_materials where code = '$data->code'");
            $codeCount = $codeQuery->num_rows();
            if ($codeCount != 0) {
                $res = ['success' => false, 'message' => 'Duplicate material code ' . $data->code];
                echo json_encode($res);
                exit;
            }

            $material = array(
                "code"           => $data->code,
                "name"           => $data->name,
                "category_id"    => $data->category_id,
                "reorder_level"  => $data->reorder_level,
                "purchase_rate"  => $data->purchase_rate,
                "sale_rate"  => $data->sale_rate,
                "unit_id"        => $data->unit_id,
                "AddBy"          => $this->session->userdata("userId"),
                'AddTime'        => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip(),
                'branch_id'      => $this->session->userdata("BRANCHid")
            );
            $this->db->insert('tbl_materials', $material);
            $res = ['success' => true, 'message' => 'Material added successfully', 'code' => $this->mt->generateMaterialCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }
    public function updateMaterial()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $nameQuery = $this->db->query("select * from tbl_materials where name = '$data->name' and material_id != '$data->material_id'");
            $nameCount = $nameQuery->num_rows();
            if ($nameCount != 0) {
                $res = ['success' => false, 'message' => 'Duplicate material name ' . $data->name];
                echo json_encode($res);
                exit;
            }

            $codeQuery = $this->db->query("select * from tbl_materials where code = '$data->code' and material_id != '$data->material_id'");
            $codeCount = $codeQuery->num_rows();
            if ($codeCount != 0) {
                $res = ['success' => false, 'message' => 'Duplicate material code ' . $data->code];
                echo json_encode($res);
                exit;
            }

            $material = array(
                "code"           => $data->code,
                "name"           => $data->name,
                "category_id"    => $data->category_id,
                "reorder_level"  => $data->reorder_level,
                "purchase_rate"  => $data->purchase_rate,
                "sale_rate"      => $data->sale_rate,
                "unit_id"        => $data->unit_id,
                "UpdateBy"       => $this->session->userdata("userId"),
                'UpdateTime'     => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip(),
                "branch_id"      => $this->session->userdata("BRANCHid")
            );
            $this->db->where('material_id', $data->material_id);
            $this->db->set($material);
            $this->db->update('tbl_materials');
            $res = ['success' => true, 'message' => 'Material updated successfully', 'code' => $this->mt->generateMaterialCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function changeMaterialStatus()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $material = array(
                'status' => $data->status == 1 ? 0 : 1,
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip(),
            );
            $this->db->where('material_id', $data->material_id);
            $this->db->set($material);
            $this->db->update('tbl_materials');
            $res = ['success' => true, 'message' => 'Status changed successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function materialStock()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = 'Material Stock';
        $data['content'] = $this->load->view('Administrator/materials/material_stock', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getMaterialStock()
    {
        $data = json_decode($this->input->raw_input_stream);
        $materialClause = "";
        if (isset($data->material_id) && $data->material_id != null) {
            $materialClause = "where m.material_id = $data->material_id";
        }
        if (isset($data->categoryId) && $data->categoryId != null) {
            $materialClause = "where m.category_id = $data->categoryId";
        }
        $stock = $this->db->query("
            select
                m.*,
                pc.ProductCategory_Name as category_name,
                u.Unit_Name as unit_name,
                ifnull((select sum(quantity) from tbl_material_purchase_details where material_id = m.material_id and status = 'a'), 0.00) as purchased_quantity,
                ifnull((select sum(quantity) from tbl_material_sale_details where material_id = m.material_id and status = 'a'), 0.00) as sale_quantity,
                ifnull((select sum(quantity) from tbl_production_details where material_id = m.material_id and status = 'a'), 0.00) as production_quantity,
                ifnull((select sum(damage_quantity) from tbl_material_damage_details where material_id = m.material_id and status = 'a'), 0.00) as damage_quantity,
                (select purchased_quantity - (sale_quantity + production_quantity + damage_quantity)) as stock_quantity,
                (select m.purchase_rate * stock_quantity) as stock_value

            from
            tbl_materials m
            join tbl_materialcategory pc on pc.ProductCategory_SlNo = m.category_id
            join tbl_unit u on u.Unit_SlNo = m.unit_id
            $materialClause
        ")->result();

        echo json_encode($stock);
    }

    public function materialDamage()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = 'Material Damage Entry';
        $data['damageCode'] = $this->mt->generateMaterialDamageCode();
        $data['content'] = $this->load->view('Administrator/materials/material_damage', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addMaterialDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $damage = array(
                'invoice' => $data->invoice,
                'damage_date' => $data->damage_date,
                'description' => $data->description,
                'status' => 'a',
                'AddBy' => $this->session->userdata("userId"),
                'AddTime' => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id' => $this->session->userdata('BRANCHid')
            );

            $this->db->insert('tbl_material_damage', $damage);
            $damageId = $this->db->insert_id();

            $damageDetails = array(
                'damage_id'       => $damageId,
                'material_id'     => $data->material_id,
                'damage_quantity' => $data->damage_quantity,
                'damage_rate'     => $data->damage_rate,
                'damage_amount'   => $data->damage_amount,
                'status'          => 'a',
                'AddBy'           => $this->session->userdata("userId"),
                'AddTime'         => date('Y-m-d H:i:s'),
                'last_update_ip'  => get_client_ip(),
                'branch_id'       => $this->session->userdata('BRANCHid')
            );

            $this->db->insert('tbl_material_damage_details', $damageDetails);

            $res = ['success' => true, 'message' => 'Damage added successfully', 'newCode' => $this->mt->generateMaterialDamageCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateMaterialDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $damage = array(
                'invoice' => $data->invoice,
                'damage_date' => $data->damage_date,
                'description' => $data->description,
                'UpdateBy' => $this->session->userdata("userId"),
                'UpdateTime' => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id' => $this->session->userdata('BRANCHid')
            );

            $this->db->where('damage_id', $data->damage_id)->update('tbl_material_damage', $damage);

            $damageDetails = array(
                'material_id'     => $data->material_id,
                'damage_quantity' => $data->damage_quantity,
                'damage_rate'     => $data->damage_rate,
                'damage_amount'   => $data->damage_amount,
                'UpdateBy'        => $this->session->userdata("userId"),
                'UpdateTime'      => date('Y-m-d H:i:s'),
                'last_update_ip'  => get_client_ip(),
                'branch_id'       => $this->session->userdata('BRANCHid')
            );

            $this->db->where('damage_id', $data->damage_id)->update('tbl_material_damage_details', $damageDetails);

            $res = ['success' => true, 'message' => 'Damage updated successfully', 'newCode' => $this->mt->generateMaterialDamageCode()];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function deleteMaterialDamage()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);

            $damage = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip(),
            );

            $this->db->set($damage)->where('damage_id', $data->damageId)->update('tbl_material_damage');
            $this->db->set($damage)->where('damage_id', $data->damageId)->update('tbl_material_damage_details');

            $res = ['success' => true, 'message' => 'Damage deleted successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function getMaterialDamage()
    {
        $damages = $this->db->query("
            select
                mdd.*,
                md.invoice,
                md.damage_date,
                md.description,
                m.code as material_code,
                m.name as material_name
            from tbl_material_damage_details mdd
            join tbl_material_damage md on md.damage_id = mdd.damage_id
            join tbl_materials m on m.material_id = mdd.material_id
            where mdd.status = 'a'
        ")->result();

        echo json_encode($damages);
    }

    public function add_category()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Add Material Category";
        $data['content'] = $this->load->view('Administrator/materials/add_materialcategory', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }
    public function getCategories()
    {
        $categories = $this->db->query("select * from tbl_materialcategory where status = 'a' order by ProductCategory_SlNo desc")->result();
        echo json_encode($categories);
    }

    public function insert_category()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $query = $this->db->query("SELECT * from tbl_materialcategory where branch_id = '$this->brunch' AND ProductCategory_Name = '$data->ProductCategory_Name'")->row();
            if (!empty($query)) {
                $category = array(
                    'status'     => 'a',
                    "UpdateBy"   => $this->session->userdata("userId"),
                    "UpdateTime" => date("Y-m-d H:i:s"),
                    "last_update_ip" => get_client_ip()
                );
                $this->db->where('ProductCategory_SlNo', $query->ProductCategory_SlNo);
                $this->db->update('tbl_materialcategory', $category);
            } else {
                $category = array(
                    "ProductCategory_Name"        => $data->ProductCategory_Name,
                    "ProductCategory_Description" => $data->ProductCategory_Description,
                    "status"                      => 'a',
                    "AddBy"                       => $this->session->userdata("userId"),
                    "AddTime"                     => date("Y-m-d H:i:s"),
                    "last_update_ip"              => get_client_ip(),
                    "branch_id"                   => $this->brunch
                );
                $this->db->insert('tbl_materialcategory', $category);
            }

            $res = ['status' => true, 'message' => 'Category added successfully'];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }

        echo json_encode($res);
    }
    public function update_category()
    {
        $res = ['status' => false];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $category = array(
                "ProductCategory_Name"        => $data->ProductCategory_Name,
                "ProductCategory_Description" => $data->ProductCategory_Description,
                "UpdateBy"                    => $this->session->userdata("userId"),
                "UpdateTime"                  => date("Y-m-d H:i:s"),
                "last_update_ip" => get_client_ip()
            );
            $this->db->where('ProductCategory_SlNo', $data->ProductCategory_SlNo);
            $this->db->update('tbl_materialcategory', $category);

            $res = ['status' => true, 'message' => 'Category update successfully'];
        } catch (\Throwable $th) {
            $res = ['status' => false, 'message' => $th->getMessage()];
        }
        echo json_encode($res);
    }
    public function catdelete()
    {
        $data = json_decode($this->input->raw_input_stream);
        $category = array(
            'status'         => 'd',
            "DeletedBy"      => $this->session->userdata("userId"),
            "DeletedTime"    => date("Y-m-d H:i:s"),
            "last_update_ip" => get_client_ip()
        );
        $this->db->where('ProductCategory_SlNo', $data->categoryId);
        $this->db->update('tbl_materialcategory', $category);
        echo json_encode(['status' => true, 'message' => 'Category delete successfully']);
    }

    public function materialList()
    {
        $data['title']  = 'Materials List';
        $data['materials'] = $this->db->query("
            select
            m.*,
            c.ProductCategory_Name as category_name
            from tbl_materials m
            left join tbl_materialcategory c on c.ProductCategory_SlNo = m.category_id
            where m.branch_id = '$this->brunch'
        ")->result();
        $data['content'] = $this->load->view('Administrator/materials/materialList', $data, true);
        $this->load->view('Administrator/index', $data);
    }


    public function materialLedger(){
        $access = $this->mt->userAccess();
        if(!$access){
            redirect(base_url());
        }
		$data['title']  = 'Material Ledger';
      
		$data['content'] = $this->load->view('Administrator/materials/material_ledger', $data, true);
        $this->load->view('Administrator/index', $data);
    }
    
    public function getMaterialLedger(){
        $data = json_decode($this->input->raw_input_stream);
        $result = $this->db->query("
            select
                'a' as sequence,
                pd.purchase_detail_id as id,
                pm.purchase_date as date,
                concat('Purchase - ', pm.invoice_no, ' - ', ifnull(s.Supplier_Name, pm.supplierName)) as description,
                pd.purchase_rate as rate,
                pd.quantity as in_quantity,
                0 as out_quantity
            from tbl_material_purchase_details pd
            join tbl_material_purchase pm on pm.purchase_id = pd.purchase_id
            left join tbl_supplier s on s.Supplier_SlNo = pm.Supplier_SlNo
            where pd.status = 'a'
            and pd.material_id = " . $data->materialId . "
            and pd.branch_id = " . $this->brunch . "
            
            UNION
            select
                'b' as sequence,
                sd.sale_detail_id as id,
                sm.sale_date as date,
                concat('Sale - ', sm.invoice_no, ' - ', ifnull(c.Customer_Name, sm.customerName)) as description,
                sd.sale_rate as rate,
                0 as in_quantity,
                sd.quantity as out_quantity
            from tbl_material_sale_details sd
            join tbl_material_sale sm on sm.sale_id = sd.sale_id
            left join tbl_customer c on c.Customer_SlNo = sm.Customer_SlNo
            where sd.status = 'a'
            and sd.material_id = " . $data->materialId . "
            and sd.branch_id = " . $this->brunch . "

            UNION

            select 
                'c' as sequence,
                pd.production_id as id,
                p.date,
                concat('Production Expense- ', p.production_sl, ' - ', p.note) as description,
                pd.purchase_rate as rate,
                0.00 as in_quantity,
                pd.quantity as out_quantity    
            from tbl_production_details pd
            join tbl_productions p on p.production_id = pd.production_id
            where p.status = 'a'
            and pd.material_id = " . $data->materialId . "
            and pd.branch_id = " . $this->brunch . "
            
            UNION
            select 
                'd' as sequence,
                dmd.damage_details_id as id,
                d.damage_date as date,
                concat('Damaged - ', d.description) as description,
                0 as rate,
                0 as in_quantity,
                dmd.damage_quantity as out_quantity
            from tbl_material_damage_details dmd
            join tbl_material_damage d on d.damage_id = dmd.damage_id
            where dmd.material_id = " . $data->materialId . "
            and d.branch_id = " . $this->brunch . "

            order by date, sequence, id
        ")->result();

        $ledger = array_map(function($key, $row) use ($result){
            $row->stock = $key == 0 ? $row->in_quantity - $row->out_quantity : ($result[$key - 1]->stock + ($row->in_quantity - $row->out_quantity));
            return $row;
        }, array_keys($result), $result);

        $previousRows = array_filter($ledger, function($row) use ($data){
            return $row->date < $data->dateFrom;
        });

        $previousStock = empty($previousRows) ? 0 : end($previousRows)->stock;

        $ledger = array_filter($ledger, function($row) use ($data){
            return $row->date >= $data->dateFrom && $row->date <= $data->dateTo;
        });

        echo json_encode(['ledger' => $ledger, 'previousStock' => $previousStock]);

    }
}
