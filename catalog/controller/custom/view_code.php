<?php
class ControllerCustomViewCode extends Controller 
{
	public function index() 
	{
		if (!$this->customer->isLogged()) 
		{
			$this->session->data['redirect'] = $this->url->link('account/download', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}
		$id = $this->customer->getId();
		$name = $this->customer->getFirstName()." ".$this->customer->getLastName();
		$postDate = substr($_GET['show_date'],2);
		$orderID = $_GET['orderid'];
		$showName = $_GET['show_name'];
		$quantity = $_GET['quantity'];
		$dir = glob('QrImages/'.$id.'/'.$orderID.'/'.$showName.'*.png');
		$title = explode("_",substr($dir[0],23));

?>
		<!DOCTYPE html>
		<html>
		<title>Focus OC</title>
		<head>
			<link rel="stylesheet" href="catalog/controller/custom/view_code.css">
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
		</head>
			<body>
				<div class="header">
					<div class="address">
						<h2><?php echo"The Circle OC";?></h2>
						<p id="address_info"><?php echo"<i>8901 Warner Ave,</i>"."<br>"."<i>Huntington Beach,</i>"."<br>"."<i>CA 92647</i><br><i>(714) 375-1961</i>";?></p>
					</div>
					<div class="fontlogo cf">
						<div class="fontlogo-item">
							<img src="logo/Focus-Logo-White.png" height="80" width="500" class="imgW">
							<img src="logo/Focus-logo-500px.png" height="80" width="500" class="imgB">
						</div>
					</div>
				</div>
				<div class="right">
							<div class="codelogo cf">
								<div class="codelogo-item">
									<?php echo'<img src="'.$dir[0].'"/>';?>
								</div>
							</div>
					</div>

					<div class="left">
					<h1><?php echo "...Focus OC Presents...<br>..".$showName."..";?></h1>
						<div class="info">
							<?php echo"Thank you <b>".$name." +</b><br>Order #<b>".$orderID." +</b> ";
							echo "<br>Your ticket for <b>(".$quantity.")</b> has been <b>confirmed +</b><br>Ticket guarantees a one-time <b>entry +</b><br>Once <b>scanned</b> and permitted entry, your ticket is no longer <b>valid +</b><br> ";?>
							<p id="show_info"><?php echo"<b>.Doors open @ 9:30P - 18+.</b><br>".date('d F',strtotime($postDate));?></p>
						</div>
						<div class="dividors">
							<hr/>
						</div>
					</div>

				</div>
			</body> 
		</html>
		<?php
	}
}
?>