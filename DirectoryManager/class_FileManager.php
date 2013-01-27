<?php
	
	include 'class_CurlManager.php';
	
	class FileManager
	{
		public function __construct()
		{
			
		}
		
		public function isValidFile( $file_name )
		{
			if( !file_exists($file_name) ){
				throw new Exception('File do not exists('.$file_name.')');
			}
			if( !is_file ( $file_name ) ){
				throw new Exception('Not a valid file('.$file_name.')');
			}
			if( !is_readable($file_name) ){
				throw new Exception('Not a readable file('.$file_name.')');
			}
			
			return TRUE;
		}
		
		public function isValidFileName( $file_name )
		{
			$file_name = (string) $file_name;
				
			if( empty($file_name) ){
				throw new Exception('Invalid file name('.$file_name.')');
			}
			
			return $file_name;
		}
		
		public function readFile( $file_name )
		{
			$file_contents = '';
			
			$file_name = $this->isValidFileName( $file_name );
			
			if (filter_var($file_name, FILTER_VALIDATE_URL) !== FALSE) {
				
				$curl = new CurlManager($file_name);
				$file_contents = $curl->execute();
			}
			else{
				
				if( $this->isValidFile( $file_name ) ){
					$file_contents = @file_get_contents($file_name);
				}
			}
			
			return $file_contents;	
		}
		
		public function writeToFile( $file_name, $file_contents, $append = FALSE )
		{
			$file_name = $this->isValidFileName( $file_name );
			$this->isValidFile( $file_name );
			
			if( !is_writable($file_name) ){
				throw new Exception( 'This file('.$file_name.') is not writable' );
			}
			
			$result = $append? @file_put_contents($file_name, $file_contents, FILE_APPEND) : @file_put_contents($file_name, $file_contents);
			
			return $result;
		}
		
		public function renameFile( $from_name, $to_name )
		{
			$from_name = $this->isValidFileName( $from_name );
			$this->isValidFile( $from_name );
			
			$to_name = (string) $to_name;
			if( empty($to_name) ){
				throw new Exception('Invalid new name('.$to_name.') of a file');
			}
			
			//@TODO: check file permissions
			
			return @rename($from_name, $to_name);
		}
		
		public function deleteFile( $file_name )
		{
			$file_name = $this->isValidFileName( $file_name );
			$this->isValidFile( $file_name );
			
			//@TODO: check file permissions 
			
			return @unlink($file_name);
		}
		
		public function deleteFiles( $files )
		{
			if( is_array($files) ){
				foreach($files as $file_name){
					$this->deleteFile( $file_name );
				}
			}
			else{
				$this->deleteFile( $files );
			}
			
			return TRUE;
		}
	
		protected function _transformFileSize($files_size, $measure_in = 'KB')
		{
			$measure_in = strtoupper($measure_in);
			
			switch( $measure_in ){
				case 'KB': $files_size = $files_size / 1024;
					break;
					
				case 'MB': $files_size = $files_size / (1024*1024);
					break;
					
				case 'GB': $files_size = $files_size / (1024*1024*1024);
					break;
				
				default: $files_size = $files_size;
					break;
			}
			
			return number_format($files_size, 3, '.', '');
		}
		
		public function getFileSize( $file_name, $measure_in = 'KB' )
		{
			$file_name = $this->isValidFileName( $file_name );
			$this->isValidFile( $file_name );
			
			return $this->_transformFileSize( filesize($file_name), $measure_in );
		}
		
		public function moveFile( $source_file, $destination_file )
		{
			$file_name = $this->isValidFileName( $source_file );
			$this->isValidFile( $source_file );
			
			$target_file = $this->isValidFileName( $destination_file );
			if( is_file($destination_file) ){
				throw new Exception('File '.$destination_file.' already exists');
			}
			
			@copy($source_file, $destination_file);
			@unlink($source_file);
			
			return TRUE;
		}
		
		public function createFileLink($file_name, $link_name, $symbolic = TRUE)
		{
			$file_name = $this->isValidFileName( $file_name );
			$this->isValidFile( $file_name );
			
			return ( $symbolic )? symlink($file_name, $link_name) : link($file_name, $link_name);
		}
		
		public function changePermissions($path, $filePerm='0644', $dirPerm='0755')
		{
			if(!file_exists($path)){
				throw new Exception('Invalid Path: '.$path);
			}

			if(is_file($path)){
				chmod($path, $filePerm);
			} 
			elseif(is_dir($path)) {
				
				$foldersAndFiles = scandir($path);
				$entries = array_slice($foldersAndFiles, 2);
				foreach($entries as $entry){
					$this->changePermissions($path."/".$entry, $filePerm, $dirPerm);
				}

				chmod($path, $dirPerm);
			}
			
			return TRUE;
		}
	}