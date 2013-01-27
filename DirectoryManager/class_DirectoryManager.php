<?php
	include 'class_FileManager.php';
	
	class DirectoryManager extends FileManager
	{
		public function __construct()
		{
			
		}
		
		public function isValidDirName( $dir_name )
		{
			$dir_name = (string) $dir_name;
		
			if( empty($dir_name) ){
				throw new Exception('Invalid directory name('.$dir_name.')');
			}
				
			return $dir_name;
		}
		
		public function isValidDir( $dir_name )
		{
			if( !is_dir ( $dir_name ) ){
				throw new Exception('Not a valid directory('.$dir_name.')');
			}
			if( !is_readable($dir_name) ){
				throw new Exception('Not a readable directory('.$dir_name.')');
			}
				
			return TRUE;
		}
		
		public function createDirectory( $dir_name, $dir_permissions = '0777' )
		{
			$dir_name = $this->isValidDirName( $dir_name );
			
			if( is_dir($dir_name) ){
				throw new Exception('Directory('.$dir_name.') already exists');
			}
			
			return @mkdir($dir_name, $dir_permissions);
		}
		
		public function directoryTreeDelete($dir_name)
		{
			$dir_name	= $this->isValidDirName($dir_name);
			
			if( ! is_dir($dir_name) ) {
				$this->deleteFile($dir_name);
				return TRUE;
			}
			
			$dir	= opendir($dir_name);
			while($file = readdir($dir)) {
				if( $file == '.' || $file == '..' ) {
					continue;
				}
				if( !is_dir($file) ) {
					$this->deleteFile($dir_name .DIRECTORY_SEPARATOR. $file);
				}
				else{
					$this->directoryTreeDelete($dir_name .DIRECTORY_SEPARATOR. $file);
				}
				
			}
			
			closedir($dir);
			@rmdir($dir_name);
			return TRUE;
		}
		
		public function ifDirContentsNestedDirs( $dir_name )
		{
			$dir_name	= $this->isValidDirName($dir_name);
			
			if( ! is_dir($dir_name) ) {
				throw new Exception('Not a valid directory: '.$dir_name);
			}
			
			$dir	= opendir($dir_name);
			while($file = readdir($dir)) {
				if( $file == '.' || $file == '..' ) {
					continue;
				}
				if( is_dir($dir_name .DIRECTORY_SEPARATOR. $file) ) {
					return TRUE;
				}
			}
				
			closedir($dir);
			
			return FALSE;
		}
		
		public function directoryDelete($dir_name)
		{
			$dir_name	= $this->isValidDirName($dir_name);
			
			if( $this->ifDirContentsNestedDirs( $dir_name ) ){
				throw new Exception('This directory contents nested directories and could not be deleted');
			}
			
			$dir	= opendir($dir_name);
			while($file = readdir($dir)) {
				if( $file == '.' || $file == '..' ) {
					continue;
				}
			
				$this->deleteFile($dir_name .DIRECTORY_SEPARATOR. $file);
			}
				
			closedir($dir);
			@rmdir($dir_name);
			return TRUE;
		}
		
		public function directoryContents( $dir_name )
		{
			$dir_name	= $this->isValidDirName($dir_name);
			
			if( ! is_dir($dir_name) ) {
				throw('Not a valid directory('.$dir_name.')');
			}
			
			$dir_contents = array(
				'directory_name' => $dir_name,
				'directory_contents' => array(
					'files' => array(),
					'directories' => array(),
					'links' => array()
				)
			);
			
			$dir_contents['directory_name'] = $dir_name;
				
			$dir	= opendir($dir_name);
			while($file = readdir($dir)) {
				if( $file == '.' || $file == '..' ) {
					continue;
				}
				if( !is_dir($dir_name .DIRECTORY_SEPARATOR. $file) ) {
					
					if( is_link($dir_name .DIRECTORY_SEPARATOR. $file) ){
						$dir_contents['directory_contents']['links'] = $file;
					}elseif( is_file($dir_name .DIRECTORY_SEPARATOR. $file) ){
						$dir_contents['directory_contents']['files'] = $file;
					}
				}
				else{
					$dir_contents['directory_contents']['directories'] = $this->directoryContents( $dir_name .DIRECTORY_SEPARATOR. $file );
				}
			
			}
				
			closedir($dir);
			
			return $dir_contents;
		}
		
		public function directorySize( $dir_name, $measure_in = 'KB' )
		{
			$dir_name	= $this->isValidDirName($dir_name);
				
			if( ! is_dir($dir_name) ) {
				throw new Exception('Not a valid directory('.$dir_name.')');
			}
				
			$files_size = 0;
			
			$dir	= opendir($dir_name);
			while($file = readdir($dir)) {
				if( $file == '.' || $file == '..' ) {
					continue;
				}
				if( !is_dir($file) ) {
					$files_size += filesize($dir_name .DIRECTORY_SEPARATOR. $file);
				}
				else{
					$files_size += $this->directorySize( $dir_name .DIRECTORY_SEPARATOR. $file, $measure_in );
				}
					
			}
			
			closedir($dir);
				
			return $this->_transformFileSize($files_size, $measure_in);
		}
		
		public function directoryContentsCount( $dir_name )
		{
			$dir_contents = array(
				'files' => 0,
				'directories' => 0,
				'links' => 0
			);
			
			if( is_dir($dir_name) ) {
				$dir	= opendir($dir_name);
				while($file = readdir($dir)) {
					if( $file == '.' || $file == '..' ) {
						continue;
					}
					if( !is_dir($dir_name .DIRECTORY_SEPARATOR. $file) ) {
						if( is_file($dir_name .DIRECTORY_SEPARATOR. $file) ){
							$dir_contents['files'] ++;
						}elseif(is_link($dir_name .DIRECTORY_SEPARATOR. $file)){
							$dir_contents['links'] ++;
						}
					}
					else{
						$dir_contents['directories']++;
						
						$tmp =  $this->directoryContentsCount( $dir_name .DIRECTORY_SEPARATOR. $file );
						
						$dir_contents['directories'] += $tmp['directories'];
						$dir_contents['files'] += $tmp['files']; 
						$dir_contents['links'] += $tmp['links'];
					}
						
				}
					
				closedir($dir);
			}
		
			return $dir_contents;
		}
		
		protected function _addDirToZip( $target_dir, &$zipArchive )
		{
			if(!empty($target_dir)){ 
				$zipArchive->addEmptyDir($target_dir); 
			}
			
			$dir	= opendir($target_dir);
			while ($file = readdir($dir)) { 
				if( ($file === ".") || ($file === "..")){ 
					continue;
				}
				if(!is_file($target_dir.DIRECTORY_SEPARATOR.$file)){ 
					$this->_addDirToZip($target_dir.DIRECTORY_SEPARATOR.$file, $zipArchive); 
				}else{
					$zipArchive->addFile($target_dir .DIRECTORY_SEPARATOR. $file); 
				} 
			} 
		}
		
		public function directoryToZip( $target_dir, $destination_dir, $zip_archive_name = 'archive.zip' )
		{
			if( !class_exists('ZipArchive') ){
				throw new Exception('Class ZipArchive do not exists, please enable it before you continue.');	
			}
			
			$destination_dir		= $this->isValidDirName($destination_dir);
			$this->isValidDir($destination_dir);
			
			$target_dir				= $this->isValidDirName($target_dir);
			$this->isValidDir($target_dir);
			
			$zip_archive_name 		= $this->isValidFileName($zip_archive_name);
			$zip_file_to_create 	= $destination_dir. DIRECTORY_SEPARATOR . $zip_archive_name;
			
			if( ! is_writable($destination_dir) ) {
				throw new Exception('Not a writable directory('.$destination_dir.')');
			}
			if( is_file($zip_archive_name) ){
				throw new Exception('There is already file named "'.$zip_archive_name.'" in this directory');
			}
			
			$zip = new ZipArchive();
			if($zip->open($zip_file_to_create, ZIPARCHIVE::CREATE) !== TRUE) {
				throw new Exception('Could not create zip archive ('.$zip_file_to_create.')');
			}	
			
			$this->_addDirToZip($target_dir, $zip);

			$zip->close();
			
			return file_exists($zip_file_to_create);
		}
		
		
		public function extractArchive( $archive_file, $extract_to )
		{
			$file_name = $this->isValidFileName( $archive_file );
			$this->isValidFile( $archive_file );
			
			if( !is_dir($extract_to) || !is_writable($extract_to) ){
				throw new Exception('Invalid directory to extract files provided('.$extract_to.')');
			}

			$zip = new ZipArchive();
			if($zip->open($archive_file) === TRUE){
				$zip->extractTo($extract_to);
				$zip->close();
			}
			else{
				throw new Exception("Error: failed to extract the plugin archive");
			}
			
			return TRUE;
		}
		
		protected function _moveDir( $source_dir, $destination_dir )
		{
			$source_dir		= $this->isValidDirName($source_dir);
			$source_dir 	= $this->isValidDir($source_dir);
			
			if (file_exists ( $destination_dir )){
	            @rrmdir ( $destination_dir );
			}
			
	        if (is_dir ( $source_dir )) {
				@mkdir ( $destination_dir );
				$files = scandir ( $source_dir );
				foreach ( $files as $file )
					if ($file != "." && $file != ".."){
						$this->_moveDir ( $source_dir.DIRECTORY_SEPARATOR.$file, $destination_dir.DIRECTORY_SEPARATOR.$file );
					}
			} 
			else if (file_exists ( $source_dir )){
				@copy ( $source_dir, $destination_dir );
			}
		}
		
		public function moveFilesToDirectory( $files_to_move, $to_dir )
		{
			$to_dir				= $this->isValidDirName($to_dir);
			$this->isValidDir($to_dir);
			
			if( is_array($files_to_move) ){
				foreach($files_to_move as $item){
					if( is_file($item) ){
						$this->moveFile($item, $to_dir.DIRECTORY_SEPARATOR.$item);
					}
					elseif( is_dir($item) ){
						$this->_moveDir($item, $to_dir.DIRECTORY_SEPARATOR.$item);
					}
				}
			}else{
				if( is_file($item) ){
					$this->moveFile($files_to_move, $to_dir.DIRECTORY_SEPARATOR.$files_to_move);
				}
				elseif( is_dir($item) ){
					$this->_moveDir($files_to_move, $to_dir);
				}
			}
		}
		
	}