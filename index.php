<html>
<head>
	<script src="jquery.min.js"></script>
	<script src="bootstrap/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="bootstrap/bootstrap.min.css">
	<style>
		.body{
			margin:0;
			border: 0px solid gainsboro;
			background-color: rgb(240, 240, 240);
			-webkit-touch-callout: none;
			-webkit-user-select: none;
			-khtml-user-select: none;
			-moz-user-select: none;
			-ms-user-select: none;
			user-select: none;
		}
		.inside{
			position:relative;
			left:0;
			right:0;
			top:0;
			bottom:0;

			border-top: 1px solid gray;
		}
		.framed{
			height:100%;
			overflow:hidden;
		}
		.infobar{
			//background: linear-gradient(0deg, black, dimGray);
			//color: white;
			color: rgb(187, 187, 187);
			padding: 5px;
		}
		#personal{
			position:relative;
			height:35px;
		}
		#theme{
			position:absolute;
			top:0;
			right:10%;
			padding:13px;
			background-color: lightblue;
			color:dimGray;
			font-weight:bold;
		}
		#tablet{
			margin: 0 auto;
		}
		td:hover{
			color:red;
			//background-color: lightgray;

	        //box-shadow: 0 8px 6px -6px black;
	        box-shadow: 0px 8px 25px -6px black;
		}
		td:active{
			background-color: gray;
		}
		td.col{
			text-align: center;
		}
		.marked{
			background-color: rgb(224, 224, 224);
			color:gray;
		}
		.done{
			background-color: lightgreen;
			color: lightgreen;
		}
		#cloak{
			//position:relative;
			top:0;
			left:0;
			right:0;
			bottom:0;
			z-index:2;
		}
		#score{
			font-size:30px;
			margin-bottom:15px;
		}
		.win{
			text-align: center;
			position: absolute;
			background-color: white;
			color:dimGray;
			
			font-weight: bolder;
			font-size: 120px;
			width:100%;
			height:200px;
			padding-top: 15px;
			opacity: 0;
		}
		canvas{
			position: fixed;
			top: 0px;
			z-index:-1;
		}
	</style>
	
</head>

<body class="body"><div class="inside">

	<div class="infobar">
		<div id="personal">
			<div style="position:absolute; left:15%;">
				<div id="date">
					<h4>saturday | me</h4>
				</div>
				<!--<div onClick="showHelp()">
					<h5>help?</h5>
				</div>-->
			</div>
		</div>
		<div id="theme">light as day</div>
	</div>

	<div id="score" class="text-center"></div>
	<table class="table" id="tablet">
	</table>
	
	<div id="cloak">
		<div class="win">
			<p id="winTitle">mod five</p>
			<br><br>
			<div id="infoTitle">
				<h5>make all the numbers into a multiple of 5<br>clicking a number will add itself to it's entire row and column</h5>
			</div>
			<br>

			<span style="font-size:15px; background-color:lightblue; color:dimGray; padding:10px 80px 10px 80px; margin-top:10px" onClick="nextLevel()" id="nextButton">my mind is ready!</span>
		</div>

		
	</div>
	<canvas id="canvas"></canvas>

	<script>
		window.onload = function(){
			$('#tablet').hide();
			$('.win').css('top', .32*window.innerHeight).animate({
				top: .35*window.innerHeight,
				opacity: 0.95
			});
		}

	//theme changing
		$('#theme').click(function(){
			var curr = $(this).text().trim();
			if (curr != "light as day"){
				$(this).css('background-color', 'lightblue');
				$(this).text('light as day');
				$('body').css('background-color', 'rgb(240, 240, 240)');
				$('body').css('color', '#333');
				$('#score').css('color', 'black');
				$('#nextButton').css('background-color', 'lightblue');
				$('.marked:not(.done)').css('background-color', 'rgb(224, 224, 224)');
			} else {
				$(this).css('background-color', 'orange');
				$(this).text('dark as night');
				$('body').css('background-color', 'rgb(20, 20, 20)');
				$('body').css('color', 'lightgray');
				$('#score').css('color', 'gray');
				$('#nextButton').css('background-color', 'orange');
				$('.marked:not(.done)').css('background-color', 'rgb(80, 80, 80)');
			}
		});

	//setup and zoomout
		var duration = 500;
		var outside = false;
		$(document).on('keyup',function(e){
			if (e.keyCode == 32){
				if ( outside == false ){
					$("body").animate({borderWidth:'+=100'}, duration);
					$(".inside").toggleClass("framed");
					outside = true;
				} else {
					$("body").animate({borderWidth:'-=100'}, duration, function(){
						$(".inside").toggleClass("framed");
					});
					outside = false;
				}
			}
		});

	//date
		var days = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];
		var day = days[ (new Date).getDay() ];
		var dayN = (new Date).getDate();
		$('#date').html("<h4>"+day+" | "+dayN+"</h4>");

		var winMessages = ["You Win!", "Great Job!", "Radical Win!", "Touchdown!", "Home Run!"]
		var level = 2;
		//newGame(3);

	//make bubbles
		var bloom;
		bubbles();


		function newGame(level){
		//formatting
			clearInterval(bloom);
			$('#canvas').fadeOut(500);

			$('#tablet').fadeIn();
			var rowN = colN = level;
			if (rowN <= 3){
				$('#tablet').css('width', '50%');
			}
			if (rowN <= 4){
				$('#tablet').css('margin-top', '10%');
			} else if (rowN <= 6) {
				$('#tablet').css('margin-top', '5%');
			} else {
				$('#tablet').css('margin-top', '0');
			}

			var goal = Math.floor(0.75*rowN*colN);
			$('#score').text("0 | goal: " + goal);

		//setting up the grid

			var table = "";
			var id;

			for (var r=0; r<rowN; r++){
				table += "<tr id='row"+r+"'>";
				for (var c=0; c<colN; c++){
					id = "r"+r+"c"+c;
					table += "<td class='col' id='"+id+"'><h1>"+(Math.floor(Math.random()*4)+1)+"</h1></td>";
				}
				table += "</tr>";
			}
			$('#tablet').html(table);
			table = null;
			id = null;


			var total = 0;
			var score = 0;
			var moves = 0;
			$('.col').click(function(){
				var id = $(this).attr('id');
				var r = id.substring(1, id.search("c"));
				var c = id.substring(id.search("c")+1);

				//set row and col to black
				$('.marked').removeClass('marked')
				getCol(c).toggleClass('marked');
				getRow(r).toggleClass('marked');
				$(this).toggleClass('marked');

				//don't do anything if it's done
				if ($(this).hasClass('done'))
					return;

				total += parseInt($(this).text(),10);
				moves += 1;
				
				var v = parseInt($(this).text(),10);
				
				//increment the row and column
				getRow(r).each(function(i, e){
					if (good(parseInt($(e).text(),10)) == false)
					{
						var changed = (parseInt($(e).text(),10) + v)%10;
						$(e).html("<h1>"+changed+"</h1>")

						if (good(parseInt($(e).text(),10))){
							$(e).addClass('done');
							score++;
						}
							
					} else {
						$(e).addClass('done');
					}
				});
				getCol(c).each(function(i, e){
					if (good(parseInt($(e).text(),10)) == false)
					{
						var changed = (parseInt($(e).text(),10) + v)%10;
						$(e).html("<h1>"+changed+"</h1>")

						if (good(parseInt($(e).text(),10))){
							$(e).addClass('done');
							score++;
						}
					} else {
						$(e).addClass('done');
					}
				});

				//update score
				$('#score').text(score + " | goal: " + goal);
				var color = "rgb("+120+","+Math.floor((256*score/goal))+","+50+")";
				$('.inside').css('border-color', color);
				if (score >= goal){
					$('#cloak').fadeIn();
					//win message
					$('#winTitle').text(winMessages[Math.floor(Math.random()*winMessages.length)]);
					$('#infoTitle').html("<i><h5>completed in " + moves + " moves!</h5></i>");

					//make bubbles
					bubbles();
					$('#canvas').show();
				}
			});
		}

	//determines whether the value is done
		function good(n){
			return (n % 5 == 0);
		}

	//utility functions
		function getCol(n){
			n = parseInt(n,10)+1;
			return $("#tablet>tbody>tr>td:nth-child("+n+")");
		}
		function getRow(n){
			return $("table tr:eq("+n+") td");
		}
		function nextLevel(){
			level += 1;
			moves = 0;
			newGame(level);
			$('#cloak').fadeOut(function(){
				$('#nextButton').text("let's try something harder...");
			});
		}




	//floaty circles
		function bubbles(){
			var canvas = document.getElementById('canvas'),
				context = canvas.getContext("2d");
			W = canvas.width = document.width;
			H = canvas.height = document.height;

			var circles = new Array();
			for (var i=0; i<5; i++)
			{
				var c = new Array();	//x, y, r
				c.push(W*Math.random());
				//c.push(H*Math.random());
				c.push(H-Math.random()*500);
				c.push(200*Math.random());

				circles.push(c);
			}

			var dr = 0.4;

			bloom = setInterval(function(){
				//clear
				context.clearRect(0,0,W,H);
				//canvas.width = canvas.width;

				if (dr > 0.05)
					dr *= 0.995;
				else
					dr = 0.05;

				for (c in circles)
				{
					circles[c][2] += dr;
					context.beginPath();
					context.arc(circles[c][0], circles[c][1], circles[c][2], 0, 2*Math.PI);

					context.strokeStyle = "#1B87E0";
					context.stroke();
				}	
			}, 1);
		}
	</script>
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-40796917-2']);
	  _gaq.push(['_trackPageview']);

	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</div></body>
</html>