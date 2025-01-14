<?php

namespace CSSoft\Cscheckout\Model\Config\Source;

class Layout implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'cscheckout-col1-set',          'label' => __('1 Column (Multistep Wizard)')],
            ['value' => 'cscheckout-col1-set expanded', 'label' => __('1 Column (Expanded)')],
            ['value' => 'cscheckout-col2-set',          'label' => __('2 Columns (Wide Payment and Shipping sections)')],
            ['value' => 'cscheckout-col2-set alt',      'label' => __('2 Columns (Place Payment and Shipping sections side by side)')],
            ['value' => 'cscheckout-col3-set',          'label' => __('3 Columns')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach ($this->toOptionArray() as $item) {
            $result[$item['value']] = $item['label'];
        }
        return $result;
    }
}
