<?php
declare(strict_types=1);
namespace App\Services;

use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Filesystem\FilesystemFactory;

use function Hyperf\Config\config;

class StorageServices extends BaseService
{
    private $disk;
    private $path;

    /**
     * @var FilesystemFactory
     */
    #[Inject]
    protected $filesystemfactory;
    
    protected $fileSystem;

    public function __construct(string $path = 'uploads')
    {
        $this->disk = config('file.default', 'local');
        $this->path = $path;
        $this->fileSystem = $this->filesystemfactory->get($this->disk);
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function store(UploadedFile $file, ?string $filename = null): string
    {
        if (!$filename) {
            $filename = uniqid('files_') . '.' . $file->getExtension();
        }

        $filePath = $this->path . '/' . $filename;

        $stream = fopen($file->getRealPath(), 'r+');
        $this->fileSystem->writeStream($filePath, $stream);
        if(is_resource($stream)) {
            fclose($stream);
        }

        return $filePath;
    }

    public function check_file(string $filePath): bool
    {
        return $this->fileSystem->has($filePath);
    }

    public function delete_file(string $filePath): bool
    {
        if ($this->check_file($filePath)) {
            $this->fileSystem->delete($filePath);
            return true;
        }
        return false;
    }

    public function get_file_url(string $filePath, bool $temporary = false): string
    {
        if ($this->check_file($filePath)) {
            return $temporary ? $this->fileSystem->temporaryUrl($filePath, Carbon::now()->addMinutes(5)) : $this->fileSystem->url($filePath);
        }
        return '';
    }
}