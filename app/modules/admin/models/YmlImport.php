<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 20.03.17
 * Time: 16:56
 */

namespace app\modules\admin\models;

use yii\base\Model;
use app\modules\admin\helpers\Utils;

class YmlImport extends Model
{
    public $next_step;
    /**
     * @var \app\modules\admin\models\Reference
     */
    public $reference;
    /**
     * @var array
     */
    public $properties = [];

    /**
     * @param $NS array
     */
    public function initialize(&$NS)
    {
        $this->next_step = &$NS;
        $this->reference = Reference::findOne($NS['REFERENCE_ID']);
        $this->properties = $this->reference->getProperties()->all();
    }

    /**
     * @param $xml_root_id
     * @return bool
     */
    public function importMetaData($xml_root_id)
    {
        if ($root = YmlFile::findOne($xml_root_id)) {
            $yml_root_id = $root->id;
        } else {
            return false;
        }

        $yml_elements_root = false;
        $yml_sections_root = false;

        if ($yml_shop_root = YmlFile::findOne(['parent_id' => $yml_root_id, 'name' => 'shop'])) {
            $shop_childrens = YmlFile::find()->where(['parent_id' => $yml_shop_root->id])->all();


            foreach ($shop_childrens as $shop_children) {
                if ($shop_children->name == 'name') {
                    $this->next_step['SHOP_NAME'] = $shop_children->value;
                    $this->next_step['SHOP_CODE'] = Utils::translit($shop_children->value);
                } elseif ($shop_children->name == 'categories') {
                    $yml_sections_root = $shop_children->id;
                } elseif ($shop_children->name == 'offers') {
                    $yml_elements_root = $shop_children->id;
                }
            }

            foreach ($this->getProperties() as $xml_id => $property) {
                $result = $this->checkProperty($xml_id, $property);
                if ($result !== true)
                    return $result;
            }

            $this->next_step["YML_ELEMENTS_ROOT"] = $yml_elements_root;
            $this->next_step["YML_SECTIONS_ROOT"] = $yml_sections_root;

            return true;
        }

        return false;
    }

    public function importElements($start_time, $interval)
    {
        $counter = [
            "ADD" => 0,
            "UPD" => 0,
            "DEL" => 0,
            "DEA" => 0,
            "ERR" => 0,
            "CRC" => 0,
            "NAV" => 0,
        ];

        if($this->next_step["YML_ELEMENTS_ROOT"])
        {
            if (!isset($this->next_step['LAST_YML_ID']))
                $this->next_step['LAST_YML_ID'] = 0;

            $parents = YmlFile::find()
                ->select(['id', 'lft', 'rgt', 'attrs'])
                ->where(['parent_id' => $this->next_step['YML_ELEMENTS_ROOT']])
                ->andWhere(['>', 'id', $this->next_step['LAST_YML_ID']])
                ->orderBy('id')
                ->limit(2000)
                ->all();

            foreach ($parents as $parent)
            {
                $counter["CRC"]++;
                $attributes = $info = unserialize($parent["attrs"]);

                /* check only available items */
                if ($attributes['available'] == "true")
                {
                    $arYMLElement = YmlFile::find()->select(['name', 'value', 'attrs'])->where(['parent_id' => $parent->id])->indexBy('name')->all();

                    $arYMLElement['id'] = $attributes['id'];
                    $arYMLElement["available"] = ($attributes["available"] == "true")?"Y":"N";
                    $arYMLElement["shop"] = $this->next_step['SHOP_NAME'];
                    $arYMLElement["hash"] = $this->next_step['YMLHASH'];

                    $ID = $this->importElement($arYMLElement, $counter, $parent);

                    $this->next_step['LAST_YML_ID'] = $parent['id'];
                }
                else
                {
                    $counter['NAV']++;
                }

                if($interval > 0 && (time()-$start_time) > $interval)
                    break;
            }
        }

        return $counter;
    }

    /**
     * @param $arYMLElement
     * @param $counter
     * @param $parent
     * @return int
     */
    protected function importElement($arYMLElement, &$counter, $parent)
    {
        $arElement = [];
        $arElement['xml_id'] = $this->next_step['SHOP_CODE'].'_'.$arYMLElement['id'];
        $arElement['tmp_id'] = $this->getElementCRC($arYMLElement);
        $bMatch = false;

        $obElement = new ReferenceElement(['reference_id' => $this->reference->id]);
        $obElement->reference = $this->reference;

        $arDbElement = $obElement::find()->where(['xml_id' => $arElement['xml_id']])->limit(1)->one();

        if (!$arDbElement) {
            $arElement['name'] = $arYMLElement['name']['value'];//Название элемента
        } else {
            $arElement['name'] = $arDbElement->name;
        }

        //устанавливаем магазин товара
        if (array_key_exists('shop', $arYMLElement))
        {
            $arElement['shop'] = $arYMLElement['shop'];
        }

        //устанавливаем цены при наличии цены
        //скидку при наличии старой цены
        //удаляем пустые значения
        if (array_key_exists('price', $arYMLElement)) {
            $arElement['price'] = intval($arYMLElement['price']['value']);

            if (array_key_exists('oldprice', $arYMLElement)) {
                $arElement['oldprice'] = intval($arYMLElement['oldprice']['value']);

                $percent = ($arElement['price'] * 100) / $arElement['oldprice'];
                $discount = 100 - round($percent);

                $arElement['discount'] = $discount;
            } else {
                $arElement['oldprice'] = null;
                $arElement['discount'] = null;
            }
        } else {
            $arElement['price'] = null;
            $arElement['oldprice'] = null;
            $arElement['discount'] = null;
        }

        //обновляем элемент
        if ($arDbElement) {
            $arDbElement->attributes = $arElement;
            if ($arDbElement->validate()) {
                $arDbElement->save(false);
                $counter['UPD']++;
            } else {
                $counter['ERR']++;
            }

            return $arDbElement->id;

        } else  {
            // создаем новый элемент
            $arElement['active'] = 0;
            $obElement->attributes = $arElement;

            if ($obElement->validate()) {
                $obElement->save(false);
                $counter['ADD']++;
            } else {
                $counter['ERR']++;
            }

            return $obElement->id;
        }
    }

    /**
     * @param $arYMLElement
     * @return int
     */
    private function getElementCRC($arYMLElement)
    {
        $c = crc32(print_r($arYMLElement, true));
        if ($c > 0x7FFFFFFF)
            $c = -(0xFFFFFFFF - $c + 1);
        return (string)$c;
    }

    /**
     * @param $xml_id
     * @param array $arProperty
     * @return bool
     */
    private function checkProperty($xml_id, Array $arProperty)
    {
        if (!$property = ReferenceProp::findOne(['reference_id' => $this->reference->id, 'xml_id' => $xml_id])) {
            $property = new ReferenceProp;

            $property->loadDefaultValues();
            $property->attributes = $arProperty;
            $property->reference_id = $this->reference->id;

            if ($property->validate()) {
                $property->save(false);
            } else
                return false;
        }

        return true;
    }

    /**
     * @return array
     */
    private function getProperties()
    {
        return [
            'yml_article' => [
                'name'   => 'Артикул',
                'type'   => 'S',
                'code'   => 'article',
                'xml_id' => 'yml_article',
            ],
            'yml_brand'   => [
                'name'              => 'Бренд',
                'type'              => 'LE',
                'code'              => 'brand',
                'xml_id'            => 'yml_brand',
                'link_reference_id' => 89,
            ],
            'yml_shop'    => [
                'name'              => 'Магазин',
                'type'              => 'LE',
                'code'              => 'shop',
                'xml_id'            => 'yml_shop',
                'link_reference_id' => 90,
            ],
        ];
    }
}