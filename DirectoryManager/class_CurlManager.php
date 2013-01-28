<?php
	/**
	 * @file		CurlManager Class
	 * @author		Ivaylo Enev
	 * @contact		ivailoenev@gmail.com
	 * @license		GPL 2
	 */
 
	class CurlManager
	{
		private $_ch;
		
		public function __construct( $url )
		{
			$this->_ch	= curl_init();
			
			curl_setopt($this->_ch, CURLOPT_AUTOREFERER, TRUE);
			curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($this->_ch, CURLOPT_HEADER, FALSE);
			curl_setopt($this->_ch, CURLOPT_NOBODY, FALSE);
			curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($this->_ch, CURLOPT_TIMEOUT, 15);
			curl_setopt($this->_ch, CURLOPT_MAXREDIRS, 5);
			curl_setopt($this->_ch, CURLOPT_URL, $url);
			curl_setopt($this->_ch, CURLOPT_URL, $url);
			curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, TRUE);
		}
		
		public function addPostFields( $post_fields )
		{
			curl_setopt($this->_ch, CURLOPT_POST, TRUE);
			curl_setopt($this->_ch, CURLOPT_POST, $post_fields);
			
			//format: curl_setopt($this->_ch, CURLOPT_POST, 'field1=value1&field2=value2');
			//format: curl_setopt($this->_ch, CURLOPT_POST, array('field1'=>'value1'));
		}
		
		public function execute()
		{
			$res	= curl_exec($this->_ch);
			curl_close($this->_ch);
			
			if( !$res ){
				throw new Exception('Invalid url provided');
			}
			
			return $res;
		}
	}