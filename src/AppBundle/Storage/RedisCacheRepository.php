<?php


namespace AppBundle\Storage;

    use Predis\Client as Redis;
    use Symfony\Component\Form\Extension\Core\Type\FileType;

    /**
 * Class RedisCacheRepository
 * @package App\Storage
 */
class RedisCacheRepository
{
    /**
     * @var Redis
     */
    private $provider;

    /**
     * RedisCacheRepository constructor.
     * @param Redis $redis
     */
    public function __construct(Redis $redis)
    {
        $this->provider = $redis;

    }

    /**
     * @param string $hashName
     * @return array
     */
    public function getAll(string $hashName) : array
    {
        $result = $this->provider->get($hashName);

        if (!empty($result)) {
            $result = unserialize($result);
        }

        return is_array($result) ? $result : [];
    }

    /**
     * @param string $hashName
     * @return mixed
     */
    public function getByHash(string $hashName)
    {
        $result = $this->provider->get($hashName);
        return $result;
    }

    /**
     * @param string $hashName
     * @param string $id
     * @return FileType|null
     */
    public function getById(string $hashName, string $id) : ?FileType
    {
        $result = $this->getAll($hashName);
        $result = $result[$id] ?? null;

        return $result instanceof FileType ? $result : null;
    }

    /**
     * @param string $hashName
     * @param null|string $id
     * @return RedisCacheRepository
     */
    public function delete(string $hashName, ?string $id = null): self
    {
        if ($id) {
            $result = $this->getAll($hashName);
            if (array_key_exists($id, $result)) {
                unset($result[$id]);
                $this->set($hashName, serialize($result));
            }
        } else {
            $this->provider->del([$hashName]);
        }

        return $this;
    }

    /**
     * @param string $hashName
     * @param string $data
     */
    public function set(string $hashName, string $data) : void
    {
        $this->provider->set($hashName, $data);
    }
    
}