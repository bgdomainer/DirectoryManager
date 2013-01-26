This is a file manager class for PHP which provides the following operations

1. $PHPFileManager->directoryTreeDelete();	//deletes directory all directories and files in it. 
2. $PHPFileManager->directoryDelete(); 		//deletes directory and all the files in a directory, if there are some subdirectory throws error.
3. $PHPFileManager->directorySize();		//size in bytes, KB, MB, GB of a directory 
4. $PHPFileManager->directoryToZip();		//archives directory
5. $PHPFileManager->moveFilesToDirectory();	//move files to specific directory
6. $PHPFileManager->itemsCount();			//how many files, directories, links are there in a specific directory
7. $PHPFileManager->readFile();
8. $PHPFileManager->createFile();	
9. $PHPFileManager->createDirectory();	
10. $PHPFileManager->createLink();	
11. $PHPFileManager->itemFullPath();
12. $PHPFileManager->getMimeType();
13. $PHPFileManager->getModifiedTime();
14. $PHPFileManager->rename();
15. $PHPFileManager->pathExists();
16. $PHPFileManager->itemReadable();
17. $PHPFileManager->itemWritable();
18. $PHPFileManager->appendFile();
19. $PHPFileManager->listDirectory();
20. $PHPFileManager->changePermissions();