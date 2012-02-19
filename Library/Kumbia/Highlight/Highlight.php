<?php

class Highlight {

	private static function _isReservedWord($token){
		switch($token){
			case T_NEW;
			case T_IF:
			case T_WHILE:
			case T_FOR:
			case T_FOREACH:
			case T_STATIC:
			case T_PUBLIC:
			case T_PRINT:
			case T_REQUIRE:
			case T_REQUIRE_ONCE:
			case T_RETURN:
			case T_ARRAY:
			case T_FINAL:
			case T_CATCH:
			case T_TRY:
			case T_FUNCTION:
			case T_THROW:
				return true;
			default:
				return false;
		}
	}

	public static function getString($phpCode){
		$tokens = token_get_all('<?php '.$phpCode);
		$numberTokens = count($tokens);
		$highString = '';
		for($i=1;$i<$numberTokens;++$i){
			$token = $tokens[$i];
			if(isset($token[1])){
				$token[1] = htmlspecialchars($token[1]);
			} else {
				$token[0] = htmlspecialchars($token[0]);
			}
			if($token[0]==T_COMMENT){
				$highString.= '<span class="tComment">'.$token[1].'</span>';
			} else {
				if($token[0]==T_CONSTANT_ENCAPSED_STRING){
					$highString.= '<span class="tString">'.$token[1].'</span>';
				} else {
					if($token[0]==T_LNUMBER){
						$highString.= '<span class="tNumber">'.$token[1].'</span>';
					} else {
						if($token[0]==T_VARIABLE){
							$highString.= '<span class="tVariable">'.$token[1].'</span>';
						} else {
							if(self::_isReservedWord($token[0])){
								$highString.= '<span class="tReserved">'.$token[1].'</span>';
							} else {
								if(isset($token[1])){
									$highString.= '<span class="tOtherSentence">'.$token[1].'</span>';
								} else {
									if($token[0]=="\t"){
										$token[0] = '&nbsp;';
									}
									$highString.= '<span class="tOther">'.$token[0].'</span>';
								}
							}
						}
					}
				}
			}

		}
		return $highString;
	}

}