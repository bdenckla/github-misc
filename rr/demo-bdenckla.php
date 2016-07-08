<?php

	header ('Content-type: text/html; charset=utf-8');

	$text		= str_replace(array("\r", "\n"), ' ', <<<EOF
Dorothy lived in the midst of the great Kansas prairies, with Uncle
Henry, who was a farmer, and Aunt Em, who was the farmer’s wife.  Their
house was small, for the lumber to build it had to be carried by wagon
many miles.  There were four walls, a floor and a roof, which made one
room; and this room contained a rusty looking cookstove, a cupboard for
the dishes, a table, three or four chairs, and the beds.  Uncle Henry
and Aunt Em had a big bed in one corner, and Dorothy a little bed in
another corner.  There was no garret at all, and no cellar—except a
small hole dug in the ground, called a cyclone cellar, where the family
could go in case one of those great whirlwinds arose, mighty enough to
crush any building in its path.  It was reached by a trap door in the
middle of the floor, from which a ladder led down into the small, dark
hole.
EOF
);
	
	$source		= isset($_REQUEST['source'])? $_REQUEST['source'] : $text;
	$language	= 'en-us';

	// phpSyllable code
	require_once(dirname(__FILE__) . '/classes/autoloader.php');

    $syllable = new Syllable($language);
	$syllable->getCache()->setPath(dirname(__FILE__).'/cache');
	$syllable->getSource()->setPath(dirname(__FILE__).'/languages');
?><html>
	<head>
		<title>phpSyllable</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<style>
html
{
    max-width: 40em;
    margin-left: auto;
    margin-right: auto;
}

p
{
    line-height: 2.5em;
    word-spacing: 150%;
    font-size: 150%;
}
		</style>
	</head>

	<body>

		<form method="POST">
			<div>
				<textarea name="source" cols="80" rows="10"><?php echo $source; ?></textarea>
			</div>
			<div>
				<button>Hyphenate</button>
			</div>
		</form>
		<hr/>
		<p>
			<?php
				$syllable->setHyphen('·');
				echo nl2br($syllable->hyphenateText($source));
			?>
		</p>
	</body>
</html>