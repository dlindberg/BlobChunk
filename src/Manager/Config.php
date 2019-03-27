<?php

declare(strict_types=1);

namespace dlindberg\BlobChunk\Manager;

class Config
{
    public $parentTags                  = [];
    public $parentAttributes            = [];
    public $specialTags                 = [];
    public $specialAttributes           = [];
    public $recursionParentTags         = [];
    public $recursionParentAttributes   = [];
    public $recursionChildrenTags       = [];
    public $recursionChildrenAttributes = [];
    public $splitWhatTags               = [];
    public $splitWhatAttributes         = [];
    public $splitOn                     = [];
    public $pairParentTags              = [];
    public $pairParentAttributes        = [];
    public $pairSets                    = [];
    public $rowColParentTags            = [];
    public $rowColParentAttributes      = [];
    public $rowColSets                  = [];

    public function __construct(array $config)
    {
        \array_walk($config, function ($config): void {
            switch ($config['group']) {
                case ('parents'):
                case ('specials'):
                    $this->addSetting($config, $config['group']);
                    break;
                case ('recursive'):
                    $this->addSetting($config['parents'], 'recursionParents');
                    $this->addSetting($config['children'], 'recursionChildren');
                    break;
                case ('splits'):
                    $this->addSetting($config['what'], 'splitWhat');
                    $this->splitOn = \array_merge($config['on'], $this->splitOn);
                    break;
                case ('rowCol'):
                case ('pairs'):
                    $this->defineCompoundSection($config);
            }
        });
    }

    public function hasAttribute(string $in, array $attribute): bool
    {
        if (!\property_exists($this, $in)) {
            $in = $this->convertKey($in, 'attributes');
        }

        return \property_exists($this, $in) ? $this->attributeExists($this->$in, $attribute) : false;
    }

    public function convertKey(string $to, string $as): string
    {
        return \rtrim($to, 's').\mb_convert_case($as, MB_CASE_TITLE);
    }

    private function defineCompoundSection(array $config): void
    {
        $group = \array_shift($config);
        \array_walk($config, function ($setting) use ($group): void {
            if ($this->isSettableGroup($setting, 'parent')) {
                switch ($group) {
                    case ('rowCol'):
                        $this->addSetting($setting['parent'], 'rowColParents');
                        $this->rowColSets[] = $setting;
                        break;
                    case ('pairs'):
                        $this->addSetting($setting['parent'], 'pairParents');
                        $this->pairSets[] = $setting;
                        break;
                }
            }
        });
    }

    private function addSetting(?array $what, string $to): void
    {
        if (\is_array($what)) {
            if ($this->isSettableGroup($what, 'tags')) {
                $this->addValues($what['tags'], $to, 'tags');
            }
            if ($this->isSettableGroup($what, 'attributes')) {
                $this->addValues($what['attributes'], $to, 'attributes');
            }
            if (\array_key_exists('type', $what)) {
                $this->addValues($this->expandSingle($what), $to, $what['type'].'s');
            }
        }
    }

    private function isSettableGroup(array $what, string $test): bool
    {
        return \array_key_exists($test, $what) && \is_array($what[$test]) && 0 !== \count($what[$test]);
    }

    private function addValues(array $what, string $to, string $as): void
    {
        $to = $this->convertKey($to, $as);
        if (\is_array($this->$to)) {
            $this->$to = $this->addValue($what, $this->$to, $as);
        }
    }

    private function addValue(array $new, array $existing, string $as): array
    {
        if ('attributes' === $as) {
            $existing = \array_reduce($new, function ($existing, $attribute): array {

                $existing = $this->mergeAttributes($attribute, $existing);

                return $existing;
            }, $existing);
        }

        return 'tags' === $as ? \array_merge($new, $existing) : $existing;
    }

    private function mergeAttributes(array $new, array $existing): array
    {
        if ($this->validAttribute($new) && !$this->attributeExists($existing, $new)) {
            $existing[] = ['name' => $new['name'], 'value' => $new['value']];
        }

        return $existing;
    }

    private function attributeExists(array $attributes, array $test): bool
    {
        return \array_reduce($attributes, function ($carry, $attribute) use ($test): bool {
            return ($carry || ($attribute['name'] === $test['name'] && $attribute['value'] === $test['value']));
        }, false);
    }

    private function validAttribute(array $attribute): bool
    {
        return \array_key_exists('name', $attribute) &&
               \array_key_exists('value', $attribute) &&
               \is_string($attribute['name']) &&
               \is_string($attribute['value']);
    }

    private function expandSingle(array $what): array
    {
        if ('tag' === $what['type']) {
            $what = [$what['value'],];
        } elseif ('attribute' === $what['type']) {
            $what = [['name' => $what['name'], 'value' => $what['value']],];
        }

        return $what;
    }
}
