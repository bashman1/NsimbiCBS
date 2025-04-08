<?php
class Handler{

    	/*
	* This will stores the Encryption Length value
	*/
	private $encrypt_len = 5;

    
//extra
	/*
	* This is used for Encode a String for Security
	*  Parameter;
	*		@str => The Text you want to Encode
	*/
	public function Encoding($str ="") {  return $this->Encrypt($str, true); }

	/*
	* This is used for Decode a String
	*  Parameter;
	*		@str => The Encoded Text you want to Encode
	*/
	public function Decoding($str ="") {  return $this->Encrypt($str, false); }


	/*
	* This is used to change the data format 
	*  Parameter;
	*		@str => The text you want to change
	*		@encypt => This is used to tell the format process
	*			true => for Encrypting
	*			false => for decrypting
	*/
	private function Encrypt($str ="", $encypt = true ) { 
		$output = "";
		$key = $encypt ? $this->encrypt_len : 26 - $this->encrypt_len;
		$inputArr = str_split($str);
		foreach($inputArr as $ch){
			$output .= $this->Cipher($ch,  $key);
		}
		$output = strrev($output);
		$output = str_ireplace("", "", $output);
		return $output;
	}
	
	/*
	* This is used to generate the  Encryption AND Decryption Algorithm
	*  Parameter;
	*		@ch => The text you want to change
	*		@key => The secret ket to use for the process
	*/
	private function Cipher($ch = "", $key = 1){
		$string = "";
		if(!ctype_alpha($ch)){
			$string = $ch;
		}else{
			$offset = ord(ctype_upper($ch) ? 'A' : 'a');
			$string = chr(fmod(((ord($ch) + $key) - $offset), 26) + $offset);
		}
		return $string;
	}
}

?>