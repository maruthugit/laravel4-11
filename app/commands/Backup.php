<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Backup extends Command {

	protected $name			= 'backup';
	protected $description	= 'Backup database, documents and images';

	private $archive;
	private $backupDirectory;
	private $databaseName;
	private $databaseUsername;
	private $databasePassword;
	private $databaseBackup;
	private $remoteUrl;

	public function __construct()
	{
		parent::__construct();

		$this->backupDirectory	= '../backup/';
		$this->databaseName		= Config::get('database.connections.mysql.database');
		$this->databaseUsername	= Config::get('database.connections.mysql.username');
		$this->databasePassword	= Config::get('database.connections.mysql.password');
		$this->databaseBackup	= $this->backupDirectory.$this->databaseName.'_'.date('Ymd').'.sql';
		$this->archive			= $this->backupDirectory.date('Ymd').'.zip';
		$this->remoteUrl		= Config::get('backup.ftp');
		$this->remoteUsername	= Config::get('backup.username');
		$this->remotePassword	= Config::get('backup.password');

		$this->verifyBackupDirectory();
	}

	public function fire()
	{
		if ( ! file_exists($this->archive))
		{
			$this->backupDatabase();
			$this->createArchive();
		}

		$status = $this->postArchive();

		if ($status == 'success')
		{
			unlink($this->archive); // Remove archive
		}
	}

	/**
	 * Dump database structure and data into a .sql file
	 * @return void
	 */
	private function backupDatabase()
	{
		exec("mysqldump -u{$this->databaseUsername} -p{$this->databasePassword} {$this->databaseName} > {$this->databaseBackup} 2>/dev/null");
	}

	/**
	* Create archive
	* @return void
	*/
	private function createArchive()
	{
		Zipper::make($this->archive)
			->folder('database')->add($this->databaseBackup)
			->folder('public/archive')->add('public/archive')
			->folder('public/images')->add('public/images')
			->folder('public/media')->add('public/media')
			->close();

		// Remove unnecessary .sql files
		foreach (glob($this->backupDirectory.'*.sql') as $file)
		{
			unlink($file);
		}
	}

	/**
	* Create archive
	* @return string 'success' or 'failed'
	*/
	private function postArchive()
	{
		$ftpStream = ftp_connect($this->remoteUrl);

		if (ftp_login($ftpStream, $this->remoteUsername, $this->remotePassword))
		{
			if (ftp_put($ftpStream, date('Ymd').'.zip', $this->archive, FTP_BINARY))
			{
				return 'success';
			}
			else
			{
				return 'failed';
			}
		}
		else
		{
			return 'failed';
		}
	}

	/**
	 * Check existence of backup directory
	 * Create backup directory if not exists
	 * @return void
	 */
	private function verifyBackupDirectory()
	{
		if ( ! is_dir($this->backupDirectory))
		{
			if ( ! mkdir($this->backupDirectory, 0755, true))
			{
				exit("Aborted: Failed to create directory.\n");
			}
		}

		chmod($this->backupDirectory, 0755);
	}

}
