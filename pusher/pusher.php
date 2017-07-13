<?php
require 'vendor/autoload.php';

$lambda_url = "https://w15odbvaf5.execute-api.eu-west-2.amazonaws.com/prod/logSoilMoisture";

/*** 
 * Reads from serial, pushes to amazon
 */
//include 'PhpSerial.php';

// Let's start the class
$serial = new PhpSerial;

// First we must specify the device. This works on both linux and windows (if
// your linux serial device is /dev/ttyS0 for COM1, etc)
//$serial->deviceSet("COM1");
$serial->deviceSet("/dev/ttyUSB0");

// We can change the baud rate, parity, length, stop bits, flow control
$serial->confBaudRate(9600);
$serial->confParity("none");
$serial->confCharacterLength(8);
$serial->confStopBits(1);
$serial->confFlowControl("none");

// Then we need to open it
$serial->deviceOpen();

$client = new GuzzleHttp\Client();

// To write into
while (TRUE) {
  if ($data = $serial->ReadPort()) {
    $data = trim($data);
    try {
    echo "Posting $data...";
    $res = $client->request('POST', $lambda_url, [
      'json' => ['moisture' => $data]
    ]);
      echo $res->getStatusCode();
      echo "\n";
    }
    catch (Exception $e) {
      echo $e->getMessage();
    }

  }
}
