<?php
	
	namespace Drakkar\Cript;
	
	
	class DrakkarCrypto
	{
		
		public static function encrypt($string)
		{
			$e = new self();
			return $e->enc($string);
		}
		
		public static function decrypt($string)
		{
			$e = new self();
			return $e->dec($string);
		}
		
		/////////////////////////////////////////////
		/// PRIVATE
		/////////////////////////////////////////////
		
		
		private function __construct()
		{
			$this->output = false;
			$this->encrypt_method = "AES-256-CBC";
			
			$this->key = hash('sha256',
							  'h6tCqi$Mp#n0Fg$15P&fA!$%8#%!f&QP4igf7CEPgI8!dCX#xR70$&5@njQ!4QPo#7J6xS9J2aU3kX#m4@h$$DVvpK!K0@hAx3Nv02RI4@TU!22088@h$X2!@%8$#Kh0k%&%T72DsA4F#b82f&90&0%QU4#!&K&7#Om4T!Go5y$L37n@vk$EPf4fJ4n2h45gKc0V!D5@4Glq$j!i%!&!4&@%5H#jnt9Jq$RuUSk@&9J!@S&IO8Pox4u&$g3T8w!!');
			$this->iv = substr(hash('sha256',
									'I!9KJjl%OO8!!%i3@!fk#5Xvp@M6Qi$ewMF3$jXyF#K@HTnK1rJ2%k&lqX!6d!A65n&1behP@uQT#0Sk$XA$wt%7#ek!JhJUopSDJ$TRlNb5MX8e4&GccfB$#Q7#U%iC$%!5A%RhVy4a@5QK9h8BSfO$2b0lLC#$$Ab!Bh&dM%4FF#!%eX280603Jj&MFJGg@$72!j4d#mG5%f!!C0R4xo7BS3y&yO&ft01##6!#17#1vG6C16jx&!W9#@34#!%Q'),
							   74,
							   16);
			
		}
		
		private function enc($string)
		{
			return $this->encrypt_decrypt('encrypt', $string);
		}
		
		
		private function dec($string)
		{
			return $this->encrypt_decrypt('decrypt', $string);
		}
		
		
		/**
		 * @param string $action : can be 'encrypt' or 'decrypt'
		 * @param string $string : string to encrypt or decrypt
		 *
		 * @return string
		 */
		private function encrypt_decrypt($action, $string)
		{
			if ($action == 'encrypt') {
				$output = openssl_encrypt($string,
										  $this->encrypt_method,
										  $this->key,
										  0,
										  $this->iv
				);
				$output = base64_encode($output);
			}
			else if ($action == 'decrypt') {
				$output = openssl_decrypt(base64_decode($string),
										  $this->encrypt_method,
										  $this->key,
										  0,
										  $this->iv
				);
			}
			return $output;
		}
		
	}
	
	
	
	//	$plain_txt = "This is my plain text";
	//	echo "Plain Text =" .$plain_txt. "\n";
	//	$encrypted_txt = encrypt_decrypt('encrypt', $plain_txt);
	//	echo "Encrypted Text = " .$encrypted_txt. "\n";
	//	$decrypted_txt = encrypt_decrypt('decrypt', $encrypted_txt);
	//	echo "Decrypted Text =" .$decrypted_txt. "\n";
	//	if ($plain_txt === $decrypted_txt) echo "SUCCESS";
	//	else echo "FAILED";
	//	echo "\n";