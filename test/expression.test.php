<?php

	namespace math\test;

	include("../tree/operator.class.php");
	include("../tree/operatorTable.class.php");
	include("../tree/parser.class.php");
	include("../tree/token.class.php");
	include("../calculator.class.php");

	$calculator = new \math\math(
		"%x + %y",
		[
			"x" => 5,
			"y" => 6
		],
		"%",
		true
	);

?>