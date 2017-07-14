<?
	// a = App id
	if (!isset($_GET["a"])) {
		$_GET["a"]="1234";
	}

	// c = client id
	if (!isset($_GET["c"])) {
		$_GET["c"]="ABCD";
	}

	// p = player id
	if (!isset($_GET["p"])) {
		$_GET["p"]="TanzoWings";
	}
?>

<!DOCTYPE html>
<html>
<head>
<title>TanzoWings controller</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- Jquery.mobile -->
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
	<link rel="stylesheet" href="http://demos.jquerymobile.com/1.4.5/theme-classic/theme-classic.css" />
	<script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	
	<!-- Client MQTT -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/paho-mqtt/1.0.1/mqttws31.min.js" type="text/javascript"></script>

	<!-- Funzioni comuni -->
	<script src="common.js" type="text/javascript"></script>
	
	<script>
		var run=false;
		var broker="iot.eclipse.org";
		var port=80;
		var topic = "TW<? echo $_GET["a"]; ?>/<? echo $_GET["c"]; ?>/cmd";
		var client;

		function onSuccess() {
			console.log("onSuccess");
		}

		function onConnect() {
			console.log("onConnect");
			jstring='{"player":"<? echo $_GET["p"]; ?>","cmd":"start"}';
			message = new Paho.MQTT.Message(jstring);
			message.destinationName = topic;
			client.send(message);
			
			setInterval(function() { 
				if (run) { 
					jstring='{"player":"<? echo $_GET["p"]; ?>","cmd":"move", "x":"' + $("#x").val() + '","y":"' + $("#y").val() + '"}';
					message = new Paho.MQTT.Message(jstring);
					message.destinationName = topic;
				} else {
					jstring='{"player":"<? echo $_GET["p"]; ?>","cmd":"move", "x":"' + 0 + '","y":"' + 0 + '"}';
					message = new Paho.MQTT.Message(jstring);
				}
				message.destinationName = topic;
				client.send(message);
			}, 10);
		}
		

		$(document).ready(function() {	
			client = new Paho.MQTT.Client(broker, Number(port), "/ws",randomString(20));
			client.connect({onSuccess:onConnect});

			$("#stop").click(function() {
				run=false
			});
		
			$("#go").click(function() {
				run=true
			});
		
			window.ondevicemotion = function(event) {
				var x = event.accelerationIncludingGravity.x;  
				var y = event.accelerationIncludingGravity.y;  
				//var z = event.accelerationIncludingGravity.z; 
			
				accX = x;  
				accY = Math.round(y*10) / 10;  

				$("#x").val(accX.toFixed(3));
				$("#y").val(accY.toFixed(3));
			}
		});
	</script>
</head>

<body>
	<!-- Prima pagina -->
	<div data-role="page" id="foo">
	
		<div data-role="header">
			<h1><? echo "Player " . $_GET["p"]; ?></h1>
		</div>
	
		<div role="main" class="ui-content">
			<fieldset class="ui-grid-a">
				<div class="ui-block-a">X</div>
				<div class="ui-block-b">Y</div>
				<!--<div class="ui-block-c">Z</div>-->
			</fieldset>
			<fieldset class="ui-grid-a">
				<div class="ui-block-a"><input type="text" id="x" value="0"></div>
				<div class="ui-block-b"><input type="text" id="y" value="0"></div>
				<!--<div class="ui-block-c"><input type="text" id="z" value="0"></div>-->
			</fieldset>
			<fieldset class="ui-grid-a">
				<div class="ui-block-a"><button id="go" class="ui-btn">GO</button></div>
				<div class="ui-block-b"><button  id="stop" class="ui-btn">STOP</button></div>
				<!--<div class="ui-block-c"><button class="ui-btn">-</button></div>-->
			</fieldset>

		</div>
		
		<div data-role="footer">
			<h4>TanzoWings</h4>
		</div>
	</div>
	
	<!-- Seconda pagina -->
	<div data-role="page" id="bar">
	
		<div data-role="header">
			<h1>Bar</h1>
		</div>
	
		<div role="main" class="ui-content">
			<p>I'm the second in the source order so I'm hidden when the page loads. I'm just shown if a link that references my id is beeing clicked.</p>
			<p><a href="#foo">Back to foo</a></p>
		</div><!-- /content -->
	
		<div data-role="footer">
			<h4>Page Footer</h4>
		</div><!-- /footer -->
	</div><!-- /page -->
</body>