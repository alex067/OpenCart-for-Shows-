# OpenCart-for-Shows-
OpenCart modification where each product is treated as an event Ticket, with a QR code attached. 

The purpose of this OpenCart mod is to optimize it for selling event tickets. 
The products are not being physically shipped to the customer, each ticket is customly designed by me and a QR code is attached to each ticket.
The customer then receives an email with the QR code, and can view the custom design code from their account / download page. 

The two most important components are the controller files:
checkout / success.php
- generates the QR code based on each product, a 9 digit code that is scannable by a custom app. 
- saves the QR code on a local folder, where each child folder is the customer ID, and each sub folder is the product number.
- sends out an email with the QR code for each product. 

account / download.php
- working with the language & view equivalent, displays a link for each product.

custom / viewcode.php 
- each link from the download.php brings the user to a custom designed ticket.
- the ticket displays the show name, the address of the venue, along with the qr code.

These are the core components of the file, along with a php file in the root folder which the scanner app has to communicate with.
The app is responsible for decoding the qr code, and querying the database to see if the ticket is valid for entry.

This project was made for a client, who has unfortunately gone a different direction with the launch of his website.

If you would like to implement this type of OpenCart, please contact me and I will be glad to help you out :)
