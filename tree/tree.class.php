<?php

	class tree {

		/** @var \math\tree\token $token */
		public $token	= null;
		/** @var \math\tree\tree $left */
		private $left	= null;
		/** @var \math\tree\tree $right */
		private $right	= null;

		function __construct($token, $left = null, $right = null) {
			if($token == null) {
				throw new \Exception("[Tree Constructor]: \$token cannot be null.");
			}

			$this->token	= $token;
			$this->left		= $left;
			$this->right	= $right;
		}

		/**
		 * Puts a token inside of a new tree object, essentially making it a leaf
		 * by itself.
		 * 
		 * @param \math\tree\token $token
		 * @return \math\tree\tree 
		 */
		static function makeLeaf($token) {
			return new tree($token);
		}

		/**
		 * Puts a token into a tree - similar to self::makeLeaf(), but
		 * $left and $right are both used, with an emphasis of the $left
		 * branch being required.
		 * 
		 * @param \math\tree\token $token
		 * @param \math\tree\tree $left 
		 * @param \math\tree\tree|null $right 
		 * @return \math\tree\tree 
		 */
		static function makeNode($token, $left, $right = null) {
			return new tree($token, $left, $right);
		}

		/**
		 * Consolidates the tree recursively into a binary tree represented
		 * by an array.
		 * 
		 * @param \math\tree\token $token
		 * @param int $type
		 * @return void
		 */
		function consolidate($token, $type = \math\tree\token::T_UNKNOWN) {

			// consolidate the left
			if($this->left != null) {
				$this->left->consolidate($token, $type);
			}

			// consolidate the right
			if($this->right != null) {
				$this->right->consolidate($token, $type);
			}

			// if the token is null, we'll stop here
			if($this->token == null) {
				return;
			}

			if($this->token->equals($token)) {

				// consolidate $token->value with an array of the left branch node's token value
				// and the right branch node's token value
				$value = implode(
					$token->value,
					[
						$this->left->token->value,
						$this->right->token->value
					]
				);

				$this->token = new \math\tree\token($value, $type);
				$this->left = null;
				$this->right = null;
			}
		}

	}

?>