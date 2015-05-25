<?php
/***********************************************
Simple Taxi 簡易計程車叫車服務系統 DEMO
by Patrick Lin
2015-05-16
================================================
Functions:
1. 駕駛上傳 GPS 坐標，取得最近客人編號
[input]
f	: function ID = 1
p1	: driver ID (to simplify, use phone number for demo)
p2	: driver latitude
p3	: driver longitude

[return] (json format)
txnid 		: transaction ID of first user within 5KM
address		: user address

2. 駕駛確認接客
[input]
f	: function ID = 2
p1	: driver ID
p2	: transaction ID to pickup
p3	: driver latitude
p4	: driver longitude

[return] (json format)
result:	true / false

3. 客人叫車
[input]
f	: function ID = 3
p1	: user mobile phone number
p2	: address to pickup

[return] (json format)
txnid	:	transaction ID 

4. 客人查詢叫車狀態
[input]
f	: function ID = 4
p1	: user mobile phone number
p2	: transaction ID to query

[return] (json format)
status		:	1 if a driver has chosen to pickup this ride, 0 if not yet
distance	:	distance to the driver's initial position when he/she chose to pickup

***********************************************/
include_once("inc_db.php");

// 最遠派送5公里以內的車子給乘客
$PICK_UP_DISTANCE_MAX = 5; 

// handle request according to function ID
$_function = $_REQUEST['f'];
$_uid = $_REQUEST['p1'];

// 駕駛上傳 GPS 坐標，取得最近客人編號
if($_function == 1) {
	$lat = $_REQUEST['p2'];
	$lon = $_REQUEST['p3'];	
	echo json_encode(driver_upload_location($_uid, $lat, $lon));
}
// 駕駛確認接客
else if ($_function == 2) {
	$txnid = $_REQUEST['p2'];
	$lat = $_REQUEST['p3'];
	$lon = $_REQUEST['p4'];	
	echo(json_encode(array('result'=>driver_pickup($_uid, $txnid,$lat,$lon))));
}
// 客人叫車
else if ($_function == 3) {
	$address = $_REQUEST['p2'];
	$decoded_address = urldecode($address);
	$txnid = user_call_car($_uid, $decoded_address);
	echo(json_encode(array('txnid'=>$txnid)));
}
// 客人查詢叫車狀態
else if($_function == 4) {
	$txnid = $_REQUEST['p2'];
	echo(json_encode(user_call_result($_uid, $txnid)));
}

////// DRIVER FUNCTIONS ////////
// 1. 駕駛上傳 GPS 坐標，並取回是否有附近等待乘客
function driver_upload_location($driver_id, $lat, $lon) {
	global $PICK_UP_DISTANCE_MAX;
	$sql = "select * from orders where status=0";
	$result = query($sql);
	while($row = mysql_fetch_array($result)) {
		$user_lat = $row['lat'];
		$user_lon = $row['lon'];
		$address = $row['user_address'];
		$distance = calcDistance($user_lat, $user_lon, $lat, $lon);
		if($distance <= $PICK_UP_DISTANCE_MAX) {
			return (array('txnid'=> $row['txnid'], 'address'=> $address));
		}
	}
	return array('txnid'=>"",'distance'=>999999);
}

// 2. 駕駛要求接下乘客，只有第一個請求的駕駛會成功得到這個單，其他會失敗。
function driver_pickup($driver_id, $txn_id, $lat, $lon) {
	$sql = "update orders set status=1, driver_id='$driver_id',driver_lat='$lat',driver_lon='$lon' where txnid='$txn_id' and status=0";
	query($sql);
	return (mysql_affected_rows() > 0);
}

////// USER FUNCTIONS /////////////////
// 3. 使用者輸入地址，系統排入派車需求清單中，回傳交易代碼
function user_call_car($mobile_phone, $address) {
	$coords = getCoordinates($address);
	$lat = $coords[0];
	$lon = $coords[1];
	$sql = "INSERT INTO orders (`user_mobile`, `user_address`, `lat`, `lon`) VALUES ('$mobile_phone', '$address', '$lat', '$lon')";
	query($sql);
	return  mysql_insert_id();
}

// 4. 使用者查詢派車結果
function user_call_result($mobile_phone, $txn_id) {
	$status = 0;
	$distance = 999999;
	$sql = "select * from orders where txnid='$txn_id' and user_mobile='$mobile_phone'";
	$result = query($sql);
	if($row = mysql_fetch_array($result)) {
		$status = $row['status'];
		if($status > 0) {
			$user_lat = $row['lat'];
			$user_lon = $row['lon'];
			$driver_lat = $row['driver_lat'];
			$driver_lon = $row['driver_lon'];
			$distance = calcDistance($user_lat, $user_lon, $driver_lat, $driver_lon);
		}
	}
	return array('status'=>$status,'distance'=>$distance);
}

// 使用 Google Map 轉換地址為 GPS 坐標
function getCoordinates($address){
    $address = urlencode($address);
    $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=" . $address;
    $response = file_get_contents($url);
    $json = json_decode($response,true);
 
    $lat = $json['results'][0]['geometry']['location']['lat'];
    $lng = $json['results'][0]['geometry']['location']['lng'];
 
    return array($lat, $lng);
}

// 計算兩點距離
function calcDistance($lat1, $lon1, $lat2, $lon2, $unit) {
  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  // miles
  if ($unit == "M") {
	  return $miles;
  } 
  // n miles
  else if ($unit == "N") {
      return ($miles * 0.8684);
  } 
  // KM: default to return KM
  else {
	  return round($miles * 1.609344,3);
  }
}
?>