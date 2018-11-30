# OpenCart-for-Shows-
## OpenCart V3 Mod. for digital tickets & QR Codes. 
 
eCommerce store which sells online tickets, and each tickets comes with a unique QR code. The QR code is then scanned at the day/night of the event, using a simple android app which decodes the QR code.
There is no physical products, after each successful purchase, a QR code is generated and linked to the ticket and the customer.
The customer then receives an email with the QR code, which they print out or show via mobile at the day of the event. 

## WARNING 
In order for this to work, YOU MUST **USE PAYPAL EXPRESS!** PayPal Express guarantees the customer to re-direct to the success.php page, which runs the script to generate the QR code. If success.php never lands, then no ticket & QR code will be generated.

This works and can be used live, however there are better solutions than using OpenCart. I personally think OpenCart isn't the best platform for this kind of products.
-------------------------------------------------------------------------------------------------------------------------------




The two most important components are the controller files:
checkout / success.php
- generates the QR code based on each product, a 9 digit code that is scannable by a custom android app. 
- saves the QR code on a local folder, where each child folder is the customer ID, and each sub folder is the product number.
- sends out an email with the QR code for each product, and a link to view the ticket. 


account / download.php
- working with the language & view equivalent, displays a link for each product.

custom / viewcode.php 
- each link from the download.php brings the user to a custom designed ticket.
- the ticket displays the show name, the address of the venue, along with the qr code.

root / searchQR.php
- android app connects to searchQR.php and sends the de-coded QR code
- searchQR.php connects to the database, and attempts to find a match with the given QR code
- if a match is found, sends back to the android app a valid confirmation, to allow the customer into the event

These are the core components of the file, I probably missed something but oh well.

The app is responsible for decoding the qr code, and outputting either a valid or invalid message, depending on what the searchQR.php send back.

If you would like to implement this type of OpenCart, please contact me and I will be glad to help you out :)
