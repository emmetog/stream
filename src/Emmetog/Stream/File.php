<?php

namespace Emmetog\Stream;

class File implements \Emmetog\Stream\StreamInterface
{

    public function fileExists($filepath)
    {
        return file_exists($filepath);
    }

    public function dirExists($directoryPath)
    {
        return is_dir($directoryPath);
    }

    /**
     * Opens a file resource
     * 
     * @param resource A resource to a file.
     */
    public function openFile($filepath, $mode, $createFileIfNotExists = true, $createDirIfNotExists = true)
    {
        $fileExists = $this->fileExists($filepath);

        if (!$fileExists)
        {
            if (!$createFileIfNotExists)
            {
                throw new FileDoesNotExistException();
            }

            // Check if the directory exists
            $directory = substr($filepath, 0, strrpos($filepath, DIRECTORY_SEPARATOR));
            $directoryExists = $this->dirExists($directory);

            if (!$directoryExists)
            {
                if (!$createDirIfNotExists)
                {
                    throw new FileDirectoryDoesNotExistException();
                }

                $this->mkdir($directory, '0700');
            }
        }

        return fopen($filepath, $mode);
    }

    public function mkdir($directoryPath, $permissionMode = 0700, $recursive = true)
    {
//        echo "start of mkdir, trying to create $directoryPath\n";

        if ($this->dirExists($directoryPath))
        {
//            echo "directory already exists, skipping\n";
            return true;
        }

        $return = mkdir($directoryPath, 0777, $recursive);

        if (!$return)
        {
            throw new FileCouldntCreateDirectoryException($directoryPath);
        }

//        echo "Directory successfully created: $directoryPath\n";

        $return = chmod($directoryPath, $permissionMode);

        if (!$return)
        {
            throw new FileCouldntChmodDirectoryException($directoryPath);
        }

//        echo "Directory successfully chmodded: $directoryPath\n";

        return true;
    }

    public function rmdir($directoryPath)
    {
        return rmdir($directoryPath);
    }

}

class FileException extends \Exception
{
    
}

class FileFileDoesNotExistException extends FileException
{
    
}

class FileDirectoryDoesNotExistException extends FileException
{
    
}

class FileCouldntCreateDirectoryException extends FileException
{
    
}

class FileCouldntChmodDirectoryException extends FileException
{
    
}

?>
