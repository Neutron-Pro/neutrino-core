<?php
namespace NeutronStars\Neutrino\Core;

class Configuration
{
    private array $json;

    public function __construct($path)
    {
        $this->json = json_decode(file_get_contents($path), true);
    }

    /**
     * @param string $key
     * @param ?mixed $def
     * @return ?mixed
     */
    public function get(string $key, $def = null)
    {
        $keys = explode('.', $key);
        $obj = $this->json;
        for ($i = 0; $i < count($keys)-1; $i++) {
            if(!isset($obj[$keys[$i]]) || !is_array($obj[$keys[$i]])) {
                return $def;
            }
            $obj = $obj[$keys[$i]];
        }
        return $obj[$keys[count($keys)-1]] ?? $def;
    }

    public function forEach($callback): void
    {
        foreach ($this->json as $key => $value) {
            $callback($key, $value);
        }
    }
}
