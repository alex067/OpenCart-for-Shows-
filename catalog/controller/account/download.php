<?php
class ControllerAccountDownload extends Controller 
{
	public function index() 
	{
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/download', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/download');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_downloads'),
			'href' => $this->url->link('account/download', '', true)
		);

		$this->load->model('account/download');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['downloads'] = array();


		$download_total = $this->model_account_download->getTotalDownloads();
		$id = $this->customer->getId();
		$start = ($page - 1) * $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		$query = $this->db->query("SELECT * FROM oc_customer_ohistory WHERE CID = '$id' ORDER BY OID DESC  LIMIT " . (int)$start . "," . (int)$limit);
		if ($query->num_rows > 0)
		{
				$this->load->model('catalog/product');
				$data['products'] = array();
				$products = $this->cart->getProducts();
				$rowNum = $query->rows;

				for($i=0;$i<$query->num_rows;$i++)
				{
					$row = $rowNum[$i];
					$product=$this->model_catalog_product->getProduct($row['PID']);
					$showName = explode("@", $product['name']);
					$showDate = explode(" ", $showName[1]);
					$qrdate = DateTime::createFromFormat('m-d-Y',$showDate[3])->format('Y-m-d');
					$data['downloads'][] = array(
					'order_id'   => $row['OID'],
					'date_added' => $showDate[3],
					'name'       => $product['name'],
					'size'		 => $row['Quantity'],
					'href'       => $this->url->link('custom/view_code', 'show_date=' .$qrdate. '&orderid=' .$row['OID']. '&quantity=' .$row['Quantity']. '&show_name=' .$showName[0], true));
				}
		}
		
		$pagination = new Pagination();
		$pagination->total = $download_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		$pagination->url = $this->url->link('account/download', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($download_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($download_total - 10)) ? $download_total : ((($page - 1) * 10) + 10), $download_total, ceil($download_total / 10));
		
		$data['continue'] = $this->url->link('account/account', '', true);

			
		

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/download', $data));
	}


	public function download() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/download', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->model('account/download');

		if (isset($this->request->get['download_id'])) {
			$download_id = $this->request->get['download_id'];
		} else {
			$download_id = 0;
		}


		$download_info = $this->model_account_download->getDownload($download_id);
		if ($download_info) {
			$file = DIR_DOWNLOAD . $download_info['filename'];
			$mask = basename($download_info['mask']);

			if (!headers_sent()) {
				if (file_exists($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));

					if (ob_get_level()) {
						ob_end_clean();
					}

					readfile($file, 'rb');

					exit();
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			$this->response->redirect($this->url->link('account/download', '', true));
		}
	}
}