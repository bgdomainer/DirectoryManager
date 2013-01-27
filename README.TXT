This is a file manager class for PHP which provides the following operations

File Operations

readFile( $file_name );
writeToFile( $file_name, $file_contents, $append = FALSE );
renameFile( $from_name, $to_name );
deleteFile( $file_name );
deleteFiles( $files );
createFileLink($file_name, $link_name, $link_type = 'symbolic');
calculateFileSize($size, $measure_in = 'KB');
moveFile( $source_file, $destination_file );
changePermissions();

Directory operations

createDirectory( $dir_name, $dir_permissions = '0777' );
directoryTreeDelete($dir_name);
ifDirContentsNestedDirs( $dir_name );
directoryDelete(dir_name);
directoryContents( $dir_name );
directorySize( $dir_name, $measure_in = 'KB' );
directoryContentsCount( $dir_name );
directoryToZip( $target_dir, $destination_dir, $zip_archive_name = 'archive.zip' );
extractArchive( $archive_file, $extract_to );
moveFilesToDirectory( $files_to_move, $to_dir );