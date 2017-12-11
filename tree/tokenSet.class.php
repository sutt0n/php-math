<?php

	namespace math\tree;

	class tokenSet extends set {

		const QUOTE_CHARS		= "`'\"";
		const IDENTIFIER_CHARS	= "_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.:";

		/**
		 * Get the first token in the string, and return it and its position.
		 * 
		 * @param string $str
		 * @return array
		 * 
		 * todo: See if this can be optimized.
		 */
		function getFirstIn($str) {
			
			$first 			= [];
			$first["pos"]	= -1;		
			$first["token"]	= null;
			
			$i = 0;
						
			while( $i < strlen($str) ) {
				
				$c = $str{$i};
				
				// inside quoted string, skip ahead
				if( strpos( self::QUOTE_CHARS, $c ) !== false ) {
				
					$in_string		= true;
					$escaped		= false;
					$quote_style	= $c;
					
					$i++;
					
					while( $in_string == true and $i < strlen($str) ) {
						
						$c = $str{$i};
						
						if( $c == "\\" and $escaped == false ) {							
							$escaped = true;
						} elseif( $c == $quote_style and $escaped == false ) {

							if( strlen($str) > $i + 1 and $str{$i + 1} == $quote_style ) {
								$escaped = true;
							} else {						
								$in_string = false;								
							}
						
						} else {						
							$escaped = false;
						}
						
						$i++;						
					}
				}
				
				$s = substr( $str, $i );
			
				// find longest terminator at beginning of string
				$token = null;
				
				for( $j = 0; $j < count($this->items); $j++ ) {
					
					$v = $this->items[$j]->rawValue;
					
					if( strncasecmp( $s, $v, strlen($v) ) != 0 ) {
						continue;
					}
					
					// inside identifier?
					$k = strlen($v) - 1;
						
					if( $k < strlen($s) - 1 ) {
							
						$c = $s{$k};
							
						if( stripos( self::IDENTIFIER_CHARS, $c ) !== false ) {
								
							$c = $s{$k + 1};
								
							if( stripos( self::IDENTIFIER_CHARS, $c ) !== false ) {
								continue;
							}
						}						
					}
					
					// longest found?
					if( $token == null ) {						
						$token = $this->items[$j];						
					} else {
						
						if( strlen($v) > strlen($token->rawValue) ) {								
							$token = $this->items[$j];
						}
					}	
				}
				
				// terminator found, return it				
				if( $token != null ) {					
					$first["pos"]	= $i;			
					$first["token"]	= $token;					
					return $first;
				}
				
				// terminator not found, skip indentifier chars
				if( $i < strlen($str) ) {
					
					if( strpos( self::IDENTIFIER_CHARS, $str{$i} ) !== false ) {
						
						while( $i < strlen($str) and strpos( self::IDENTIFIER_CHARS, $str{$i} ) !== false ) {
							$i++;
						}
						
					} else {
						
						$i++;
					}
				}
				
			}
			
			return $first;
		}

		/**
		 * Return an $item by its $rawValue.
		 * 
		 * @param string $rawValue
		 * @return mixed|null Returns the $item if it's found; null otherwise.
		 */
		function getByRawValue($rawValue) {

			foreach($this->items as $item) {
				if($item->rawValue == $rawValue) {
					return $item;
				}
			}

			return null;
		}

		/**
		 * Checks if a $rawValue is in $this->items.
		 * 
		 * @param string $rawValue
		 * @return bool True if found; false otherwise.
		 */
		function containsRawValue($rawValue) {

			foreach($this->items as $item) {
				if($item->rawValue == $rawValue) {
					return true;
				}
			}

			return false;
		}
	}

?>