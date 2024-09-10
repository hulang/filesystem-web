<?php

declare(strict_types=1);

namespace hulang\web;

use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathPrefixer;
use yzh52521\EasyHttp\Http;

class WebAdapter implements FilesystemAdapter
{
    /**
     * @var mixed|string
     */
    protected $secret_id;

    /**
     * @var mixed|string
     */
    protected $secret_key;

    /**
     * @var mixed|string
     */
    protected $bucket;

    /**
     * @var mixed|string
     */
    protected $domain;

    /**
     * @var mixed|string
     */
    protected $url;

    /**
     * @var mixed|array
     */
    protected $ops = [];

    private PathPrefixer $prefixer;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->secret_id = $config['secret_id'];
        $this->secret_key = $config['secret_key'];
        $this->bucket = $config['bucket'];
        $this->domain = $config['domain'];
        $this->url = $config['url'];
        $this->ops = $config;
        $this->prefixer = new PathPrefixer('');
    }

    /**
     * 上传文件到指定路径
     * 
     * 该方法负责将提供的文件内容上传到指定的路径
     * 它不返回任何值,但确保文件上传符合给定的配置要求
     * 
     * @param string $path 文件将被上传到的路径
     * @param mixed $contents 要上传的文件的内容,它可以是字符串形式的数据或其他类型的文件内容
     * @param Config $config 配置对象,用于控制上传过程的行为,$config的具体结构和要求未在文档中提供
     * 
     * @return void 该方法不返回任何值,但会根据提供的配置执行文件上传操作
     */
    public function upload(string $path, $contents, Config $config): void
    {
        $url = $this->domain;
        $data = [];
        $data['ops'] = $this->ops;
        $data['path'] = $path;
        $data['content'] = $contents;
        $response = Http::post($url, $data);
        $response->body();
    }

    /**
     * 将给定的内容写入到指定的文件中,并应用配置设置
     * 
     * 该方法用于将提供的内容字符串写入到文件系统中的一个文件中
     * 它接受一个文件路径,要写入的内容,以及配置对象作为参数
     * 该方法不返回任何内容,但可能引发异常,如果写入过程中出现问题
     * 
     * @param string $path 要写入的文件的路径
     * @param string $contents 要写入文件的内容
     * @param Config $config 配置对象,包含写入操作的配置
     * @return void
     */
    public function write(string $path, string $contents, Config $config): void
    {
        echo ('write');
        echo (PHP_EOL);
        echo ($path);
        echo (PHP_EOL);
        print_r($contents);
        echo (PHP_EOL);
        print_r($config);
        echo (PHP_EOL);
        die;
    }

    /**
     * 将内容写入到指定的流中
     * 
     * 本函数负责将给定的内容写入到指定路径的文件中
     * 使用此方法可以实现文件内容的替换或追加写入
     * 
     * @param string $path 要写入的文件路径
     * @param mixed $contents 要写入的文件内容
     * @param Config $config 配置对象,用于控制写入模式（如追加、覆盖等）
     * 
     * @return void 该方法没有返回值
     */
    public function writeStream(string $path, $contents, Config $config): void
    {
        if ($contents) {
            // 将资源转换成文件流
            $fileStream = stream_get_contents($contents);
            // 关闭资源
            fclose($contents);
            $key = $this->prefixer->prefixPath($path);
            $this->upload($key, $fileStream, $config);
        }
    }

    /**
     * @return mixed|bool
     */
    public function fileExists(string $path): bool
    {
        return false;
    }

    /**
     * @return mixed|bool
     */
    public function directoryExists(string $path): bool
    {
        return false;
    }

    /**
     * @return mixed|string
     */
    public function read(string $path): string
    {
        $body = '';

        return $body;
    }

    /**
     * @return mixed|resource
     */
    public function readStream(string $path)
    {
        $body = '';

        return $body;
    }

    public function delete(string $path): void {}
    public function deleteDirectory(string $path): void {}
    public function createDirectory(string $path, Config $config): void {}
    public function setVisibility(string $path, string $visibility): void {}
    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path, null, '');
    }
    public function mimeType(string $path): FileAttributes
    {
        return new FileAttributes($path, null, '');
    }
    public function lastModified(string $path): FileAttributes
    {
        return new FileAttributes($path, null, '');
    }
    public function fileSize(string $path): FileAttributes
    {
        return new FileAttributes($path, null, '');
    }
    public function listContents(string $path, bool $deep): iterable
    {
        return [];
    }
    public function move(string $source, string $destination, Config $config): void {}
    public function copy(string $source, string $destination, Config $config): void {}
}
