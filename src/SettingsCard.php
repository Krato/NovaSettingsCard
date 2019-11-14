<?php

namespace EricLagarda\SettingsCard;

use Illuminate\Support\Str;
use Laravel\Nova\Card;

class SettingsCard extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = 'full';

    /**
     * @var array
     */
    protected $disks = [];

    /**
     * Set the card fields
     *
     * @param array $fields
     * @return mixed
     */
    public function fields(array $fields)
    {
        return $this->withMeta([
            'fields' => $this->fillFieldValues($fields),
            'disks'  => $this->disks,
        ]);
    }

    /**
     * Set Name of the card
     *
     * @param string $name
     * @return mixed
     */
    public function name(string $name)
    {
        return $this->withMeta(['name' => $name]);
    }

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'settings-card';
    }

    /**
     * @param array $fields
     * @return mixed
     */
    private function fillFieldValues(array $fieldsTabbed): array
    {
        foreach ($fieldsTabbed as $tab => $fields) {
            foreach ($fields as $field) {
                if ($value = setting($field->attribute, null)) {
                    $field->value = $this->getNovaValue($field->component, $value);
                }
                if ($field->component == 'file-field') {
                    $this->disks[$field->attribute] = $field->disk;
                }
            }
        }

        return $this->parseFieldsToTabs($fieldsTabbed);
    }

    /**
     * @param $type
     * @param $value
     */
    private function getNovaValue($type, $value)
    {
        if ($value == null) {
            return null;
        }
        if ($type == 'file-field') {
            $data = json_decode($value);

            return $data->path;
        }

        if ($type == 'key-value-field') {
            return json_decode($value);
        }

        return $value;
    }

    /**
     * @param array $fields
     * @return mixed
     */
    private function parseFieldsToTabs(array $fields): array
    {
        $tabs = [];
        foreach ($fields as $tab => $fields) {
            $tabs[] = [
                'name'   => $tab,
                'key'    => Str::slug($tab),
                'fields' => $fields,
                'init'   => false,
            ];
        }

        return $tabs;
    }
}
