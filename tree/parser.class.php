<?php

	namespace math\tree;

	use \math\tree\operator;
	use \math\tree\token;

	class parser {

		private $operatorTable	= null;
		private $parseStr		= "";

		/**
		 * Allocate and define operator definitions in the $operatorTable.
		 * 
		 * @param \math\tree\operatorTable $operatorTable
		 */
		function __construct($operatorTable = null) {

			if($operatorTable == null) {
				$operatorTable = new \math\tree\operatorTable();
			}

			$operatorTable->add(",",	0,	operator::LEFT);
			$operatorTable->add("OR",	1,	operator::LEFT);
			$operatorTable->add("XOR",	2,	operator::LEFT);
			$operatorTable->add("AND",	3,	operator::LEFT);
			$operatorTable->add("||",	4,	operator::LEFT);
			$operatorTable->add("&&",	5,	operator::LEFT);

			$operatorTable->add("NOT",	5,	operator::UNARY);	// negate

			$operatorTable->add("=",	6,	operator::LEFT);	// assignment
			$operatorTable->add("LIKE",6,	operator::LEFT);
			$operatorTable->add("IN",	6,	operator::LEFT);
			$operatorTable->add("IS",	6,	operator::LEFT);
			$operatorTable->add("!=",	6,	operator::LEFT);	// not equal
			$operatorTable->add("<",	7,	operator::LEFT);	// less than
			$operatorTable->add("<=",	7,	operator::LEFT);	// less than or equal to
			$operatorTable->add(">",	7,	operator::LEFT);	// greater than
			$operatorTable->add(">=",	7,	operator::LEFT);	// greater than or equal to
			$operatorTable->add("+",	8,	operator::LEFT);	// addition
			$operatorTable->add("-",	8,	operator::LEFT);	// subtract
			$operatorTable->add("*",	9,	operator::LEFT);	// multiply
			$operatorTable->add("/",	9,	operator::LEFT);	// divide
			$operatorTable->add("!",	10,	operator::UNARY);	// negate
			$operatorTable->add("-",	10,	operator::UNARY);	// negative

			$this->operatorTable = $operatorTable;
		}

		/**
		 * Sets $this->parseStr property.
		 * 
		 * @param string $parseString
		 * @return void
		 */
		function setParseString($parseString) {
			$this->parseStr = $parseString;
		}

		/**
		 * Returns $this->parseStr property.
		 * 
		 * @return string
		 */
		function getParseString() {
			return $this->parseStr;
		}

		/**
		 * Parse $this->parseStr.
		 * 
		 * @param string $parseString
		 * @return \math\tree\tree 
		 */
		function parse($parseString) {
			$this->setParseString($parseString);

			$tree = $this->exp(0);
			$this->expect(token::T_NONE);

			return $tree;
		}

		/**
		 * Returns the next token.
		 * 
		 * @return \math\tree\token
		 */
		function nextToken() {
			$this->parseStr = ltrim($this->parseStr);

			// stop if there's nothing else to parse
			if($this->parseStr == "") {
				return token::T_NONE;
			}

			// decimal
			if(preg_match(token::REGEX_DECIMAL, $this->parseStr, $matches)) {
				$token = new token($matches[0], token::T_NUMBER);
				return $token;
			}

			// quoted string or identifier
			if(in_array($this->parseStr[0], ["'", "\"", "`"])) {

				$s				= $this->parseStr[0];
				$style			= $s;
				$isEscaped		= false;
				$isTerminated	= false;

				for( $i = 1; $i < strlen($this->parseStr); $i++ ) {

					$char = $this->parseStr[$i];

					$s .= $char;

					if( $char == "\\" && $isEscaped == false ) {
						$isEscaped = true;

					} elseif( $char == $style && $isEscaped == false ) {

						if( strlen($this->parseStr) > $i + 1 && $this->parseStr[$i + 1] == $style ) {
							$isEscaped = true;
						} else {
							$isTerminated = true;
							break;
						}

					} else {
						$isEscaped = false;
					}
				}

				if( $isTerminated == false ) {
					$this->error("There is an unterminated string.");
				}

				$type = token::T_STRING;

				if($style == "`") {
					$type = token::T_ID;
				}

				$token = new token($s, $type);
				return $token;
			}

			$first = $this->operatorTable->terminatorSet->getFirstIn($this->parseStr);

			// terminator or operator
			if($first["pos"] == 0) {
				return $first["token"];
			}

			// identifier
			if($first["pos"] == -1) {
				$str = $this->parseStr;
			} else {
				$str = substr($this->parseStr, 0, $first["pos"]);
			}

			$str = trim($str);
			
			return new token($str, token::T_ID);
		}

		/**
		 * The first token in $this->parseStr is removed.
		 * 
		 * @param \math\tree\token $token
		 */
		function consume($token) {

			// no token?
			if($token == token::T_NONE) {
				return;
			}

			// if the token's $rawValue is in $this->parseStr, then remove it from $this->parseStr
			if(stripos($this->parseStr, $token->rawValue) === 0) {
				$this->parseStr = substr($this->parseStr, strlen($token->rawValue));
			}
		}

		/**
		 * Constructs a tree for an expression (with $precedence).
		 * 
		 * @param int $precedence
		 * @return \math\tree\tree
		 */
		function exp($precedence) {

			$tree		= $this->_exp();
			$nextToken	= $this->next();

			// functions
			while($nextToken != token::T_NONE && $nextToken->equals()) {
				$this->consume($nextToken);
				$treeLeft = $this->exp(0);
				$this->expect(token::$T_CLOSE_PAREN);

				$token = $tree->token;
				$token->type = token::T_FUNCTION;
				$tree = \math\tree\tree::makeNode($token, $treeLeft);

				$nextToken = $this->next();
			}

			// binary operators
			while(
				$nextToken != token::T_NONE && 
				$nextToken->type == token::T_OPERAND && 
				!$nextToken->isUnary() && 
				$nextToken->precedence >= $precedence
			) {

				$this->consume($nextToken);
				$treeRight = $this->exp(
					($nextToken->associativity == operator::LEFT) ? $nextToken->precedence + 1 : $nextToken->precedence
				);
				$tree = \math\tree\tree::makeNode($nextToken, $tree, $treeRight);
				$nextToken = $this->next();

			}

			return $tree;

		}

		private function _exp() {

			$tree		= null;
			$nextToken	= $this->next();

			if( $nextToken == token::T_NONE ) {
				return null;
			}

			$unaryToken = $this->operatorTable->unaryOpSet->getByRawValue( $nextToken->rawValue );

			if( $unaryToken != null ) {
				$nextToken = $unaryToken;
			}

			if(
				$nextToken != token::T_NONE &&
				$nextToken->type == token::T_OPERAND &&
				$nextToken->isUnary()
			) {

				$this->consume($nextToken);
				$tree = $this->exp($nextToken->precedence);
				$tree = Tree::makeNode($nextToken, $tree, null);
				return $tree;
			}

			if($nextToken->equals(token::$T_OPEN_PAREN)) {

				$this->consume($nextToken);
				$tree = $this->exp(0);
				$this->expect(token::$T_CLOSE_PAREN);
				return $tree;
			}

			if($nextToken->equals(token::$T_CLOSE_PAREN)) {
				return null;
			}

			$tree = Tree::makeLeaf($nextToken);
			$this->consume($nextToken);
			return $tree;
		}

		function expect($token) {

			$nextToken = $this->next();

			if( $nextToken == token::T_NONE and $token == Token::T_NONE ) {
				return;
			}

			if( $nextToken != Token::T_NONE and $nextToken->equals($token) ) {
				$this->consume($token);
				return;
			}

			$this->error("Was expecting [" . ($token == Token::T_NONE ? "T_NONE" : $token) . "].");
		}

		function error($message) {
			throw new \Exception("PARSE ERROR: " . $message);
		}

	}

?>