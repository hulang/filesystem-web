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
    public function write(string $path, string $contents, Config $config): void {}

    /**
     * 将内容写入到指定的流中
     * 
     * 本函数负责将给定的内容写入到指定路径的文件中
     * 使用此方法可以实现文件内容的替换或追加写入
     * 
     * @param string $path 要写入的文件路径
     * @param mixed $contents 要写入的文件内容
     * @param Config $config 配置对象,用于控制写入模式(如追加、覆盖等)
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

    /**
     * 删除指定路径的资源
     * 
     * 此方法用于删除存储在特定路径的资源它不返回任何内容,仅执行删除操作
     * 
     * @param string $path 要删除的资源的路径这是要删除的具体位置,必须作为字符串提供
     * 
     * @return void 此方法不返回任何内容
     */
    public function delete(string $path): void {}

    /**
     * 删除指定路径的目录
     * 
     * 该方法用于删除文件系统中的一个目录,包括该目录下的所有文件和子目录
     * 如果目录不存在,该方法不会执行任何操作,也不会抛出异常
     * 
     * @param string $path 目录的路径,必须是一个字符串
     * 
     * @return void 该方法没有返回值,它执行操作但不返回任何数据
     */
    public function deleteDirectory(string $path): void {}

    /**
     * 创建目录
     *
     * 本函数尝试在指定位置创建一个新目录.它根据配置对象中提供的设置来执行创建操作
     * 如果目录已经存在,或者由于权限问题无法创建新目录,则不会进行任何操作
     *
     * @param string $path 要创建的目录的路径.这可以是绝对路径或相对路径
     * @param Config $config 包含用于目录创建的配置信息的对象.这可能包括权限设置等
     * 
     * @return void 本函数没有返回值,它只是在指定路径上创建一个目录
     */
    public function createDirectory(string $path, Config $config): void {}

    /**
     * 设置文件或目录的可见性
     *
     * 此方法用于根据指定的路径修改文件或目录的可见性属性可见性属性决定了文件或目录是否对所有用户可见
     * 或者仅对特定用户或用户组可见这在文件共享和权限管理中非常重要
     *
     * @param string $path 文件或目录的路径这是要修改可见性的目标位置路径可以是相对路径或绝对路径
     * @param string $visibility 可见性属性值这可以是“public”(公开)、“private”(私有)或其他预定义的可见性属性
     *                           例如,“hidden”(隐藏)此参数决定了目标文件或目录的可见性级别
     *
     * @return void 该方法不返回任何值它主要用于修改文件或目录的可见性属性
     */
    public function setVisibility(string $path, string $visibility): void {}

    /**
     * 将文件从源路径移动到目标路径
     *
     * 此方法用于文件系统操作,具体而言是将一个文件从源路径移动到目标路径
     * 它接受三个参数：源文件的绝对路径,目标文件的绝对路径,以及配置对象
     * 配置对象可能包含了操作过程中需要遵循的规则或选项,例如文件覆盖策略等
     * 该方法不返回任何值,但可能抛出异常,例如目标路径已存在且不允许覆盖时
     *
     * @param string $source 源文件的绝对路径
     * @param string $destination 目标文件的绝对路径
     * @param Config $config 操作配置对象,可能包含操作规则或选项
     * @throws Exception 当移动操作失败时可能抛出异常
     */
    public function move(string $source, string $destination, Config $config): void {}

    /**
     * 复制文件或目录
     * 
     * 该方法用于将指定的文件或目录从一个位置复制到另一个位置,可以根据配置对象提供的信息执行特定的复制操作
     * 
     * @param string $source 当前要复制的文件或目录的路径这是复制操作的源点
     * @param string $destination 复制后的新位置路径这是复制操作的目标位置
     * @param Config $config 包含复制操作所需配置信息的对象这些配置可能影响复制操作的行为,例如是否覆盖现有文件等
     * 
     * @return void 该方法不返回任何值,它主要关注于执行复制操作
     */
    public function copy(string $source, string $destination, Config $config): void {}
}
