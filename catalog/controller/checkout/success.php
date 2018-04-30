<?php
//require_once('vendor\endroid\qrcode\src\Endroid\QrCode\QrCode.php');
require_once('vendor/autoload.php');
use Endroid\QrCode\QrCode;



class ControllerCheckoutSuccess extends Controller
{
	public function index() 
	{
		$this->load->language('checkout/success');
		$this->load->model('catalog/product');
		$this->load->model('catalog/codegen');
		//get product id 
		$data['products'] = array();
		$products = $this->cart->getProducts();
		$qrdate = ""; //holds date of each show
		$id = $this->customer->getId();
		$name = $this->customer->getFirstName()." ".$this->customer->getLastName();
		$orderID = $this->session->data['order_id'];
		$userEmail = $this->customer->getEmail();
		$todayDate = date('Y-m-d');

		$mail = new PHPMailer; 
		$mail->isSMTP(); //set mailer to SMTP 
		$mail->Host ='';
		$mail->SMTPAuth = true;
		$mail->Username = '';
		$mail->Password = '';
		$mail->SMTPSecure = 'ssl';
		$mail->Port = 465;
		$mail->setFrom('');
		$mail->addAddress($userEmail);
		$mail->isHTML(true);

		//generate code for each id
		foreach ($products as $product)
		{
			$quantity = $product['quantity'];
			$itemID = $product['product_id'];
			$product = $this->model_catalog_product->getProduct($itemID);
			$showName = explode("@", $product['name']);
			$showDate = explode(" ", $showName[1]);
			$qrdate = DateTime::createFromFormat('m-d-Y',$showDate[3])->format('Y-m-d');
			
			$code = $this->model_catalog_codegen->createCode();

			$qr = new QrCode(); 
			$qr->setText($code);
			$qr->setSize(200);
	
			if (!file_exists('QrImages/'.$id))
			{
				mkdir('QrImages/'.$id,744,true);
				chmod('QrImages/'.$id, 0755);
			}
			if (!file_exists('QrImages/'.$id.'/'.$orderID))
			{
				mkdir('QrImages/'.$id.'/'.$orderID,744,true);
				chmod('QrImages/'.$id.'/'.$orderID, 0755);

			}
			$fileName = $showName[0];
			$fileName .= "_".$qrdate.".png";

			$path = "QrImages/".$id."/".$orderID.'/'.$fileName;
			$qr->save("QrImages/".$id."/".$orderID.'/'.$fileName);
			$this->db->query("INSERT INTO presale SET PID = '" . $this->db->escape($code) . "', CID = '" . (int)($id) . "', showName = '" . $this->db->escape($showName[0]) . "', order_date = '" . $this->db->escape($todayDate) . "', showDate = '" . $this->db->escape($qrdate) . "', quantity = '" . (int)($quantity) . "', status = '" . (int)(0) . "'");
			$postDate = substr($qrdate,2);
			$mail->Subject =$name.', '.$product['name'];
			$mail->AddEmbeddedImage('logo/focus-logo-500px.png', 'focus_logo');
			$mail->AddEmbeddedImage($path, 'qr_code');


			$mail->Body = '<head><meta name="viewport" content="width=device-width, initial-scale=1"></head><body style="margin: 0 auto; max-width: 800px; max-height: 600px; line-height:15px"><table border="0" cellpadding="0" cellspacing="0"><tr><td><img src="cid:focus_logo" height="80px" width="100%" style="display:block; margin: 0px auto;"></td><td></tr><tr align="center"><td align="center"><h1 style= "font-family:Monaco, Consolas, Lucida Console, monospace; font-size:100%; text-transform:uppercase; letter-spacing:2px"><b>...Focus OC Presents...</b></h1></td></tr><tr><td align="center" bgcolor="#696969"><p style= "font-family:Monaco, Consolas, Lucida Console, monospace; font-size:80%; font-color:white; text-align:center; text-transform:uppercase; letter-spacing:2px"><i><b>On '.date('d F', strtotime($postDate)).'</i> @ The Circle OC</b></p></td><tr><td align="center"><p style= "font-family:Monaco, Consolas, Lucida Console, monospace; font-size:100%; text-align:center; text-transform:uppercase; letter-spacing:4px"><b>.. '.$showName[0].'..</b></p></td></tr><tr><td><hr/></td></tr><tr><td><p style ="font-family:Monaco, Consolas, Lucida Console, monospace; font-size:100%; text-align:center; text-transform:uppercase">Thank you <b>'.$name.'</b> || Order#<b>'.$orderID.'</b> || Ticket for <b>('.$quantity.')</td></tr><tr><td bgcolor="#696969"><p style= "font-family:Monaco, Consolas, Lucida Console, monospace; font-size:80%; text-align:center; text-transform:uppercase; letter-spacing:2px"><b>.Doors open @ 9:30P - 18+.</b></p></td></tr><tr><td><hr style="border: 2px dotted #000"/></td></tr><tr align="center"><td><img src="cid:qr_code" style="display:block; margin: 0 auto;"></td></tr><tr><td><hr style="border: 2px dotted #000"/></td></tr></table></body>';
			if(!$mail->send())
			{
				echo 'Mailer Error: '.$mail->ErrorInfo;
			}
		

			$this->db->query("INSERT INTO oc_download SET filename = '" . $this->db->escape($product['name']) . "', mask = '" . $this->db->escape($product['name']) . "', date_added = '" . $this->db->escape($todayDate) . "'");
			$this->db->query("INSERT INTO oc_customer_ohistory SET CID = '" . (int)($id) . "', PID = '" . (int)$itemID . "', OID ='" . (int)$orderID."', Quantity = '" . (int)$quantity . "', order_date = '" . $this->db->escape($todayDate) . "', show_date = '" . $this->db->escape($qrdate) . "'");
		}
	

		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/download', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['continue'] = $this->url->link('account/download');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	
	}
	

}?>
