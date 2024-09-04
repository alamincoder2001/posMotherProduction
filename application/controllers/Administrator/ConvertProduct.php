<?php
class ConvertProduct extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->sbrunch = $this->session->userdata('BRANCHid');
        $access = $this->session->userdata('userId');
        if ($access == '') {
            redirect("Login");
        }
        $this->load->model('Model_table', "mt", TRUE);
    }
    public function index()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Convert Product Entry";
        $data['convertId'] = 0;
        $data['invoice'] = $this->mt->generateConvertProductCode();
        $data['content'] = $this->load->view('Administrator/convertproduct/convertproduct', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function edit($convertId = 0)
    {
        $data['title'] = "Convert Product Edit";
        $data['convertId'] = $convertId;
        $convert = $this->db->query("
            select * from tbl_convert_master
            where id = '$convertId'
        ")->row();
        $data['invoice'] = $convert->invoice;
        $data['content'] = $this->load->view('Administrator/convertproduct/convertproduct', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function addConvertProduct()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $this->db->trans_begin();
            $data = json_decode($this->input->raw_input_stream);

            $invoice = $this->db->query("select * from tbl_convert_master where invoice = ?", $data->convert->invoice)->num_rows();
            if ($invoice > 0) {
                $data->convert->invoice = $this->mt->generateConvertProductCode();
            }
            $convert = array(
                'invoice'        => $data->convert->invoice,
                'date'           => $data->convert->date,
                'note'           => $data->convert->note,
                'labour_cost'    => $data->convert->labour_cost,
                'product_cost'   => $data->convert->product_cost,
                'other_cost'     => $data->convert->other_cost,
                'total'          => $data->convert->total_cost,
                'status'         => 'a',
                'AddBy'          => $this->session->userdata("userId"),
                'AddTime'        => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id'      => $this->session->userdata('BRANCHid')
            );

            $this->db->insert('tbl_convert_master', $convert);
            $convertId = $this->db->insert_id();


            foreach ($data->outProducts as $item) {
                $product = array(
                    'convert_id'     => $convertId,
                    'product_id'     => $item->Product_SlNo,
                    'quantity'       => $item->quantity,
                    'price'          => $item->Product_Purchase_Rate,
                    'total'          => $item->total,
                    'status'         => 'a',
                    'AddBy'          => $this->session->userdata("userId"),
                    'AddTime'        => date('Y-m-d H:i:s'),
                    'last_update_ip' => get_client_ip(),
                    'branch_id'      => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_convert_product_out', $product);

                $productInventoryCount = $this->db->query("select * from tbl_currentinventory ci where ci.product_id = ? and ci.branch_id = ?", [$item->Product_SlNo, $this->session->userdata('BRANCHid')])->num_rows();
                if ($productInventoryCount == 0) {
                    $inventory = array(
                        'product_id'          => $item->Product_SlNo,
                        'convert_quantity_out' => $item->quantity,
                        'branch_id'           => $this->session->userdata('BRANCHid')
                    );
                    $this->db->insert('tbl_currentinventory', $inventory);
                } else {
                    $this->db->query("update tbl_currentinventory set convert_quantity_out = convert_quantity_out + ? where product_id = ? and branch_id = ?", [$item->quantity, $item->Product_SlNo, $this->session->userdata('BRANCHid')]);
                }
            }

            foreach ($data->products as $product) {
                $convertProduct = array(
                    'convert_id'  => $convertId,
                    'product_id'     => $product->product_id,
                    'quantity'       => $product->quantity,
                    'price'          => $product->price,
                    'total'          => $product->total,
                    'status'         => 'a',
                    'AddBy'          => $this->session->userdata("userId"),
                    'AddTime'        => date('Y-m-d H:i:s'),
                    'last_update_ip' => get_client_ip(),
                    'branch_id'      => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_convert_product_in', $convertProduct);

                $productInventoryCount = $this->db->query("select * from tbl_currentinventory ci where ci.product_id = ? and ci.branch_id = ?", [$product->product_id, $this->session->userdata('BRANCHid')])->num_rows();
                if ($productInventoryCount == 0) {
                    $inventory = array(
                        'product_id'           => $product->product_id,
                        'convert_quantity_in' => $product->quantity,
                        'branch_id'            => $this->session->userdata('BRANCHid')
                    );

                    $this->db->insert('tbl_currentinventory', $inventory);
                } else {
                    $this->db->query("update tbl_currentinventory set convert_quantity_in = convert_quantity_in + ? where product_id = ? and branch_id = ?", [$product->quantity, $product->product_id, $this->session->userdata('BRANCHid')]);
                }
            }

            $this->db->trans_commit();
            $res = ['success' => true, 'message' => 'convert entry success', 'convertId' => $convertId];
        } catch (Exception $ex) {
            $this->db->trans_rollback();
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function updateConvertProduct()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $convertId = $data->convert->id;
            $convert = array(
                'invoice'        => $data->convert->invoice,
                'date'           => $data->convert->date,
                'note'           => $data->convert->note,
                'labour_cost'    => $data->convert->labour_cost,
                'product_cost'   => $data->convert->product_cost,
                'other_cost'     => $data->convert->other_cost,
                'total'          => $data->convert->total_cost,
                'AddBy'          => $this->session->userdata("userId"),
                'AddTime'        => date('Y-m-d H:i:s'),
                'last_update_ip' => get_client_ip(),
                'branch_id'      => $this->session->userdata('BRANCHid')
            );

            $this->db->where('id', $convertId)->update('tbl_convert_master', $convert);

            $oldProductOut = $this->db->query("select * from tbl_convert_product_out where convert_id = ?", $convertId)->result();
            $this->db->delete('tbl_convert_product_out', array('convert_id' => $convertId));
            foreach ($oldProductOut as $oldProduct) {
                $this->db->query("update tbl_currentinventory set convert_quantity_out = convert_quantity_out - ? where product_id = ? and branch_id = ?", [$oldProduct->quantity, $oldProduct->product_id, $this->session->userdata('BRANCHid')]);
            }
            foreach ($data->outProducts as $item) {
                $product = array(
                    'convert_id'     => $convertId,
                    'product_id'     => $item->Product_SlNo,
                    'quantity'       => $item->quantity,
                    'price'          => $item->Product_Purchase_Rate,
                    'total'          => $item->total,
                    'status'         => 'a',
                    'AddBy'          => $this->session->userdata("userId"),
                    'AddTime'        => date('Y-m-d H:i:s'),
                    'last_update_ip' => get_client_ip(),
                    'branch_id'      => $this->session->userdata('BRANCHid')
                );
                $this->db->insert('tbl_convert_product_out', $product);

                $productInventoryCount = $this->db->query("select * from tbl_currentinventory ci where ci.product_id = ? and ci.branch_id = ?", [$item->Product_SlNo, $this->session->userdata('BRANCHid')])->num_rows();
                if ($productInventoryCount == 0) {
                    $inventory = array(
                        'product_id'          => $item->Product_SlNo,
                        'convert_quantity_out' => $item->quantity,
                        'branch_id'           => $this->session->userdata('BRANCHid')
                    );
                    $this->db->insert('tbl_currentinventory', $inventory);
                } else {
                    $this->db->query("update tbl_currentinventory set convert_quantity_out = convert_quantity_out + ? where product_id = ? and branch_id = ?", [$item->quantity, $item->Product_SlNo, $this->session->userdata('BRANCHid')]);
                }
            }

            $oldProductIn = $this->db->query("select * from tbl_convert_product_in where convert_id = ?", $convertId)->result();
            $this->db->delete('tbl_convert_product_in', array('convert_id' => $convertId));
            foreach ($oldProductIn as $oldProduct) {
                $this->db->query("update tbl_currentinventory set convert_quantity_in = convert_quantity_in - ? where product_id = ? and branch_id = ?", [$oldProduct->quantity, $oldProduct->product_id, $this->session->userdata('BRANCHid')]);
            }
            foreach ($data->products as $product) {
                $convertProduct = array(
                    'convert_id'     => $convertId,
                    'product_id'     => $product->product_id,
                    'quantity'       => $product->quantity,
                    'price'          => $product->price,
                    'total'          => $product->total,
                    'status'         => 'a',
                    'AddBy'          => $this->session->userdata("userId"),
                    'AddTime'        => date('Y-m-d H:i:s'),
                    'last_update_ip' => get_client_ip(),
                    'branch_id'      => $this->session->userdata('BRANCHid')
                );

                $this->db->insert('tbl_convert_product_in', $convertProduct);

                $productInventoryCount = $this->db->query("select * from tbl_currentinventory ci where ci.product_id = ? and ci.branch_id = ?", [$product->product_id, $this->session->userdata('BRANCHid')])->num_rows();
                if ($productInventoryCount == 0) {
                    $inventory = array(
                        'product_id' => $product->product_id,
                        'convert_quantity_in' => $product->quantity,
                        'branch_id' => $this->session->userdata('BRANCHid')
                    );

                    $this->db->insert('tbl_currentinventory', $inventory);
                } else {
                    $this->db->query("update tbl_currentinventory set convert_quantity_in = convert_quantity_in + ? where product_id = ? and branch_id = ?", [$product->quantity, $product->product_id, $this->session->userdata('BRANCHid')]);
                }
            }

            $res = ['success' => true, 'message' => 'convert update success', 'convertId' => $convertId];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function convertproductRecord()
    {
        $access = $this->mt->userAccess();
        if (!$access) {
            redirect(base_url());
        }
        $data['title'] = "Convert Product Record";
        $data['content'] = $this->load->view('Administrator/convertproduct/convertproduct_record', $data, TRUE);
        $this->load->view('Administrator/index', $data);
    }

    public function getConvertMaster()
    {
        $options = json_decode($this->input->raw_input_stream);

        $idClause = '';
        $dateClause = '';


        if (isset($options->convert_id) && $options->convert_id != 0) {
            $idClause = " and pr.id = '$options->convert_id'";
        }

        if (isset($options->dateFrom) && isset($options->dateTo) && $options->dateFrom != null && $options->dateTo != null) {
            $dateClause = " and pr.date between '$options->dateFrom' and '$options->dateTo'";
        }
        $converts = $this->db->query("
            select 
            pr.*
            from tbl_convert_master pr
            where pr.status = 'a' $idClause $dateClause
        ")->result();
        echo json_encode($converts);
    }

    public function getConvertProductRecord()
    {
        $options = json_decode($this->input->raw_input_stream);

        $dateClause = '';

        if (isset($options->dateFrom) && isset($options->dateTo) && $options->dateFrom != null && $options->dateTo != null) {
            $dateClause = " and pr.date between '$options->dateFrom' and '$options->dateTo'";
        }
        $converts = $this->db->query("
            select 
            pr.*
            from tbl_convert_master pr
            where pr.status = 'a' $dateClause
        ")->result();

        foreach ($converts as $convert) {
            $convert->products = $this->db->query("
                select
                    pp.*,
                    p.Product_Code as product_code,
                    p.Product_Name as name,
                    p.ProductCategory_ID as category_id,
                    pc.ProductCategory_Name as category_name,
                    u.Unit_Name as unit_name
                from tbl_convert_product_in pp
                join tbl_product p on p.Product_SlNo = pp.product_id
                join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
                join tbl_unit u on u.Unit_SlNo = p.unit_id
                where pp.status = 'a'
                and pp.convert_id = ?
            ", $convert->id)->result();
        }
        echo json_encode($converts);
    }

    public function getConvertProductDetails()
    {
        $options = json_decode($this->input->raw_input_stream);
        $convertDetails = $this->db->query("
            select
            pd.*,
            m.Product_SlNo,
            m.Product_Name,
            m.Product_Purchase_Rate,
            u.Unit_Name as unit_name
            from tbl_convert_product_out pd
            join tbl_product m on m.Product_SlNo = pd.product_id
            join tbl_unit u on u.Unit_SlNo = m.Unit_ID
            where pd.status = 'a' 
            and pd.convert_id = '$options->convert_id'
        ")->result();

        echo json_encode($convertDetails);
    }

    public function getconvertProducts()
    {
        $options = json_decode($this->input->raw_input_stream);
        $convertProducts = $this->db->query("
            select
                pp.*,
                p.Product_Code as product_code,
                p.Product_Name as name,
                p.ProductCategory_ID as category_id,
                pc.ProductCategory_Name as category_name,
                u.Unit_Name as unit_name
            from tbl_convert_product_in pp
            join tbl_product p on p.Product_SlNo = pp.product_id
            join tbl_productcategory pc on pc.ProductCategory_SlNo = p.ProductCategory_ID
            join tbl_unit u on u.Unit_SlNo = p.unit_id
            where pp.status = 'a'
            and pp.convert_id = '$options->convert_id'
        ")->result();

        echo json_encode($convertProducts);
    }

    public function deleteConvertProduct()
    {
        $res = ['success' => false, 'message' => ''];
        try {
            $data = json_decode($this->input->raw_input_stream);
            $convert = array(
                'status' => 'd',
                'DeletedBy' => $this->session->userdata('userId'),
                'DeletedTime' => date("Y-m-d H:i:s"),
                'last_update_ip' => get_client_ip(),
            );

            $oldOutProduct = $this->db->query("select * from tbl_convert_product_out where status = 'a' and convert_id = ?", $data->convert_id)->result();
            $oldInProduct = $this->db->query("select * from tbl_convert_product_in where status = 'a' and convert_id = ?", $data->convert_id)->result();
            foreach ($oldOutProduct as $key => $item) {
                $this->db->query("update tbl_currentinventory set convert_quantity_out = convert_quantity_out - ? where product_id = ? and branch_id = ?", [$item->quantity, $item->product_id, $this->session->userdata('BRANCHid')]);
            }
            foreach ($oldInProduct as $key => $item) {
                $this->db->query("update tbl_currentinventory set convert_quantity_in = convert_quantity_in - ? where product_id = ? and branch_id = ?", [$item->quantity, $item->product_id, $this->session->userdata('BRANCHid')]);
            }
            $this->db->set($convert)->where('id', $data->convert_id)->update('tbl_convert_master');
            $this->db->set($convert)->where('convert_id', $data->convert_id)->update('tbl_convert_product_out');
            $this->db->set($convert)->where('convert_id', $data->convert_id)->update('tbl_convert_product_in');
            $res = ['success' => true, 'message' => 'convert deleted successfully'];
        } catch (Exception $ex) {
            $res = ['success' => false, 'message' => $ex->getMessage()];
        }

        echo json_encode($res);
    }

    public function convertproductInvoice($convertId)
    {
        $data['title'] = "convert Invoice";
        $data['convertId'] = $convertId;
        $data['content'] = $this->load->view("Administrator/convertproduct/convertproduct_invoice", $data, true);
        $this->load->view("Administrator/index", $data);
    }
}
