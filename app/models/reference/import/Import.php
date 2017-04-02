<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 27.03.17
 * Time: 9:59
 */

namespace app\models\reference\import;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\components\helpers\Utils;
use app\components\helpers\FileHelper;
use app\models\reference\Reference;
use app\models\reference\ReferenceElement;
use app\models\reference\ReferenceSection;
use app\models\reference\ReferenceProperty;
use app\models\reference\ReferenceElementProperty;

/**
 * Class Import
 * @package app\models\reference\import
 */
class Import extends Model
{
    /**
     * @var array
     */
    public $next_step;
    /**
     * @var \app\models\reference\Reference
     */
    public $reference;
    /**
     * default path for yml files
     * @var string
     */
    public static $filePath = '@app/yml_files';
    /**
     * @var array
     */
    protected $SECTION_MAP;
    protected $properties;

    /**
     * @param $NS array
     */
    public function initialize(&$NS)
    {
        $this->next_step = &$NS;
        $this->reference = Reference::findOne($NS['REFERENCE_ID']);

        $this->properties = $this->reference->getProperties()->indexBy('xml_id')->all();
    }

    /**
     * @param $xml_root_id
     * @return bool
     */
    public function importMetaData($xml_root_id)
    {
        if (($root = YmlTree::findOne($xml_root_id)) !== null) {
            $yml_root_id = $root->id;
        } else {
            return false;
        }

        $yml_elements_root = false;
        $yml_sections_root = false;

        if ($yml_shop_root = YmlTree::findOne(['parent_id' => $yml_root_id, 'name' => 'shop'])) {
            $shop_childrens = YmlTree::find()->where(['parent_id' => $yml_shop_root->id])->all();

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
                $result = $this->checkReferenceProperty($xml_id, $property);
                if ($result !== true)
                    return $result;
            }

            $this->next_step['YML_ELEMENTS_ROOT'] = $yml_elements_root;
            $this->next_step['YML_SECTIONS_ROOT'] = $yml_sections_root;

            return true;
        }

        return false;
    }

    public function importSectionsData(array $SECTION_MAP)
    {
        if (empty($SECTION_MAP)) {
            $SECTION_MAP = [];

            if ($this->next_step['YML_SECTIONS_ROOT']) {
                foreach (YmlTree::find()->select(['id', 'name', 'value', 'attrs'])->where(['parent_id' => $this->next_step['YML_SECTIONS_ROOT']])->orderBy('id')->asArray()->all() as $dbSection) {
                    $arSection = [];
                    $info = unserialize($dbSection['attrs']);
                    $arSection['name'] = $dbSection['value'];
                    if (isset($info['parentId']))
                        $arSection['parent_id'] = $info['parentId'];

                    $SECTION_MAP[$info['id']] = $arSection;
                }
            }

            Yii::$app->session->set('SECTION_MAP', $SECTION_MAP);
        }

        $this->SECTION_MAP = $SECTION_MAP;
    }

    public function importElements($start_time, $interval)
    {
        $counter = [
            'ADD' => 0,
            'UPD' => 0,
            'DEL' => 0,
            'ERR' => 0,
            'CRC' => 0,
            'NAV' => 0,
        ];

        if ($this->next_step['YML_ELEMENTS_ROOT']) {
            if (!isset($this->next_step['LAST_YML_ID']))
                $this->next_step['LAST_YML_ID'] = 0;

            $parents = YmlTree::find()
                ->select(['id', 'lft', 'rgt', 'attrs'])
                ->where(['parent_id' => $this->next_step['YML_ELEMENTS_ROOT']])
                ->andWhere(['>', 'id', $this->next_step['LAST_YML_ID']])
                ->orderBy('id')
                ->limit(5000)
                ->all();

            foreach ($parents as $parent) {
                $counter['CRC']++;
                $attributes = unserialize($parent['attrs']);
                if (!isset($attributes['available']))
                    $attributes['available'] = 'true';

                if ($attributes['available'] == 'true') {
                    $arYMLElement = [];
                    $arYML = ArrayHelper::getColumn(YmlTree::find()->select(['name', 'value', 'attrs'])->where(['parent_id' => $parent->id])->indexBy('name')->asArray()->all(), 'value');
                    foreach ($arYML as $name => $value) {
                        $arYMLElement[strtolower($name)] = $value;
                    }

                    $arYMLElement['id'] = $attributes['id'];

                    /**
                     * Для товаров с параметром  'group_id'!!!
                     * ищем во временной базе совпадающие записи,
                     * если записи нет, записываем, если есть используем offer_id найденной записи
                     */
                    if (array_key_exists('group_id', $attributes)) {

                        $arOfferGroupHash = [
                            'type'     => 'group_element',
                            'group_id' => $attributes['group_id'],
                            'shop'     => $this->next_step['SHOP_CODE'],
                            'model'    => (isset($arYMLElement['model'])) ? $arYMLElement['model'] : $arYMLElement['name'],
                        ];

                        $arYMLElement['xml_id'] = $this->getElementMD($arOfferGroupHash);

                        $first_item = false; //если предложение есть в базе, то элемент не первый
                        if (YmlOffers::find()->limit(1)->where(['offer_id' => $arYMLElement['xml_id']])->one() === null) {
                            $first_item = true;
                        }

                        $result = $this->ImportElement($arYMLElement, $counter, $parent, $first_item);

                        // добавим запись в таблицу, если элемент первый и он успешно  сохренен/изменен
                        if ($result && $first_item) {
                            $modelTree = new YmlTree(['parent_id' => 0, 'name' => 'used_id', 'lft' => $result]);
                            $modelTree->save(false);

                            $modelOffer = new YmlOffers(['offer_id' => $arYMLElement['xml_id'], 'sku_id' => $result]);
                            $modelOffer->save(false);
                        }
                    } else {
                        $arElementHash = [
                            'type'  => 'element',
                            'id'    => $arYMLElement['id'],
                            'model' => (isset($arYMLElement['model'])) ? $arYMLElement['model'] : $arYMLElement['name'],
                            'shop'  => $this->next_step['SHOP_CODE'],
                        ];

                        $arYMLElement['xml_id'] = $this->GetElementMD($arElementHash);
                        if ($result = $this->ImportElement($arYMLElement, $counter, $parent, true)) {
                            $model = new YmlTree(['parent_id' => 0, 'name' => 'used_id', 'lft' => $result]);
                            $model->save(false);
                        }
                    }

                } else {
                    $counter['NAV']++;
                }

                $this->next_step['LAST_YML_ID'] = $parent['id'];
                if ($interval > 0 && (time() - $start_time) > $interval)
                    break;
            }
        }

        return $counter;
    }

    /**
     * update or add new element
     * if $first_item = true replace all properties and make $counter++
     * else not make $counter
     *
     * @param $arYMLElement
     * @param $counter
     * @param $parent
     * @param bool $first_item
     * @return int
     */
    protected function importElement($arYMLElement, &$counter, $parent, $first_item = false)
    {
        $arElement = [
            'name'   => Html::decode($arYMLElement['name']),
            'xml_id' => $arYMLElement['xml_id'],
        ];
        $arProperty = [];

        $arDbElement = ReferenceElement::find()->where(['reference_id' => $this->reference->id, 'xml_id' => $arElement['xml_id']])->limit(1)->one();
        $properties = $arDbElement ? $arDbElement->initProperties() : (new ReferenceElement(['reference_id' => $this->reference->id]))->initProperties();

        //устанавливаем значения для новых товаров
        if ($first_item) {
            $arElement['shop'] = $this->next_step['SHOP_NAME'];

            if (array_key_exists('picture', $arYMLElement))
                $arElement['picture_src'] = $arYMLElement['picture'];

            if (array_key_exists('url', $arYMLElement))
                $arElement['item_url'] = $arYMLElement['url'];

            if (array_key_exists('categoryId', $arYMLElement)) {
                $id = $arYMLElement['categoryId'];
                if (isset($this->SECTION_MAP[$id])) {
                    $sectStr = [];
                    for ($i = 0; $i < 10; $i++) {
                        $sectStr[] = $this->SECTION_MAP[$id]['name'];
                        if (isset($this->SECTION_MAP[$id]['parent_id']) && isset($this->SECTION_MAP[$this->SECTION_MAP[$id]['parent_id']])) {
                            $id = $this->SECTION_MAP[$id]['parent_id'];
                        } else {
                            break;
                        }
                    }

                    $arElement['current_section'] = implode('/', array_reverse($sectStr));
                }
            }

            if (array_key_exists('vendor', $arYMLElement))
                $arElement['brand'] = $arYMLElement['vendor'];

            if (array_key_exists('price', $arYMLElement) && intval($arYMLElement['price']) > 0) {
                $arElement['price'] = intval($arYMLElement['price']);

                if (array_key_exists('oldprice', $arYMLElement)
                    && (intval($arYMLElement['oldprice']) > 0)
                    && ($arElement['price'] < intval($arYMLElement['oldprice']))
                ) {
                    $arElement['oldprice'] = intval($arYMLElement['oldprice']);

                    if ($arElement['oldprice'] > 0) {
                        $percent = ($arElement['price'] * 100) / $arElement['oldprice'];
                        $discount = 100 - round($percent);

                        $arElement['discount'] = $discount;
                    }
                } else {
                    $arElement['oldprice'] = null;
                    $arElement['discount'] = null;
                }
            } else {
                $arElement['price'] = null;
                $arElement['oldprice'] = null;
                $arElement['discount'] = null;
            }

            if (!$arElement['price']) {
                $arElement['reference_section_id'] = (ReferenceSection::find()->limit(1)->where(['reference_id' => $this->reference->id, 'code' => 'without_price'])->one())->id;
                $arElement['active'] = 0;
            }
        }

        //устанавливаем свойства товара
        if (array_key_exists('param', $arYMLElement)) {
            $curParams = [];
            $intKey = 0;
            $params = YmlTree::find()->select(['value', 'attrs'])->where(['parent_id' => $parent->id, 'name' => 'param'])->orderBy('id')->all();
            foreach ($params as $param) {
                $attributes = unserialize($param->attrs);

                if (ArrayHelper::isIn(strtolower($attributes['name']), [Yii::t('app/ymlimport', 'yml_prop_color'), Yii::t('app/ymlimport', 'yml_prop_color_lat')])) {
                    $arColors = explode(',', $param->value);
                    $colors = [];
                    foreach ($arColors as $strColor) {
                        $color = $this->checkPropertyValue($this->properties['yml_current_color'], trim($strColor));
                        if ($color && $color->reference_section_id > 0) {
                            $colors[] = $color->reference_section_id;
                        }
                    }

                    $PID = $this->properties['yml_color']->id;
                    $arProperty[$PID] = (isset($arProperty[$PID])) ? $arProperty[$PID] : [];
                    $arProperty[$PID] = ArrayHelper::merge($arProperty[$PID], $this->setPropertyValues($properties[$PID], array_unique($colors)));
                } elseif (ArrayHelper::isIn(strtolower($attributes['name']), [Yii::t('app/ymlimport', 'yml_prop_dimension'), Yii::t('app/ymlimport', 'yml_prop_dimensions'), Yii::t('app/ymlimport', 'yml_prop_dimension_lat')])) {
                    $currentStr = str_replace('|', ',', $param->value);
                    $arDimensions = explode(',', $currentStr);
                    $dimensions = [];
                    foreach ($arDimensions as $strDimension) {
                        $dimension = $this->checkPropertyValue($this->properties['yml_current_dimension'], trim($strDimension));
                        if ($dimension && $dimension->reference_section_id > 0) {
                            $dimensions[] = $dimension->reference_section_id;
                        }
                    }

                    $PID = $this->properties['yml_dimension']->id;
                    $arProperty[$PID] = (isset($arProperty[$PID])) ? $arProperty[$PID] : [];
                    $arProperty[$PID] = ArrayHelper::merge($arProperty[$PID], $this->setPropertyValues($properties[$PID], array_unique($dimensions)));
                } elseif (ArrayHelper::isIn(strtolower($attributes['name']), [Yii::t('app/ymlimport', 'yml_prop_material'), Yii::t('app/ymlimport', 'yml_prop_material_lat')])) {
                    $arMaterial = explode(',', $param->value);
                    $materails = [];
                    foreach ($arMaterial as $strMaterial) {
                        $material = $this->checkPropertyValue($this->properties['yml_current_material'], trim($strMaterial));
                        if ($material && $material->reference_section_id > 0) {
                            $materails[] = $material->reference_section_id;
                        }
                    }
                    $PID = $this->properties['yml_material']->id;
                    $arProperty[$PID] = (isset($arProperty[$PID])) ? $arProperty[$PID] : [];
                    $arProperty[$PID] = ArrayHelper::merge($arProperty[$PID], $this->setPropertyValues($properties[$PID], array_unique($materails)));
                } elseif (ArrayHelper::isIn(strtolower($attributes['name']), [Yii::t('app/ymlimport', 'yml_prop_gender'), Yii::t('app/ymlimport', 'yml_prop_gender_lat')])) {
                    $genders = [];
                    $gender = $this->checkPropertyValue($this->properties['yml_current_gender'], trim($param->value));
                    if ($gender && $gender->reference_section_id > 0) {
                        $genders[] = $gender->reference_section_id;
                    }
                    $PID = $this->properties['yml_gender']->id;
                    $arProperty[$PID] = $this->setPropertyValues($properties[$PID], $genders);
                } elseif (ArrayHelper::isIn(strtolower($attributes['name']), [Yii::t('app/ymlimport', 'yml_prop_season'), Yii::t('app/ymlimport', 'yml_prop_season_ext')])) {
                    $arSesons = explode(',', $param->value);
                    $seasons = [];
                    foreach ($arSesons as $strSeason) {
                        $season = $this->checkPropertyValue($this->properties['yml_current_seazon'], trim($strSeason));
                        if ($season && $season->reference_section_id > 0) {
                            $seasons[] = $season->reference_section_id;
                        }
                    }

                    $PID = $this->properties['yml_seazon']->id;
                    $arProperty[$PID] = (isset($arProperty[$PID])) ? $arProperty[$PID] : [];
                    $arProperty[$PID] = ArrayHelper::merge($arProperty[$PID], $this->setPropertyValues($properties[$PID], array_unique($seasons)));
                } else {
                    $curParams[$intKey] = $attributes['name'] . ': ' . $param->value;
                    $intKey++;
                }
            }

            if ($first_item && !empty($curParams)) {
                $arElement['current_props'] = implode(' | ', $curParams);
            }
        }

        //обновляем элемент
        if ($arDbElement) {
            $arDbElement->attributes = $arElement;
            $arDbElement->arProperty = $arProperty;
            if ($arDbElement->validate()) {
                $arDbElement->save(false);
                if ($first_item)
                    $counter['UPD']++;
            } else {
                $counter['ERR']++;
            }

            $ID = $arDbElement->id;

        } else {
            // создаем новый элемент
            $arElement['active'] = 0;
            $obElement = new ReferenceElement(['reference_id' => $this->reference->id]);
            $obElement->attributes = $arElement;
            $obElement->arProperty = $arProperty;

            if ($obElement->validate()) {
                $obElement->save(false);
                $counter['ADD']++;
            } else {
                $counter['ERR']++;
            }

            $ID = $obElement->id;
        }
        return $ID;
    }

    /**
     * @param ReferenceProperty $property
     * @param $values
     * @return ReferenceElementProperty[]
     */
    public function setPropertyValues(ReferenceProperty $property, $values)
    {
        $result = [];
        $values = (array)$values;
        foreach ($values as $value) {
            $key = array_search($value, ArrayHelper::getColumn($property->elementProperties, 'value'));
            $propertyValue = $key ? $property->elementProperties[$key] : new ReferenceElementProperty(['property_id' => $property->id, 'value' => $value]);
            $propertyValue->setAttribute('marker', $this->next_step['YMLHASH']);
            $result[] = $propertyValue;
        }

        return $result;
    }

    /**
     * @param $start_time
     * @param $interval
     * @return array
     */
    public function deleteElements($start_time, $interval)
    {
        $counter = [
            'DEL' => 0,
        ];

        $dbElements = ReferenceElement::find()
            ->where(['reference_id' => $this->reference->id])
            ->andWhere(['>', 'id', $this->next_step['LAST_YML_ID']])
            ->andWhere(['shop' => $this->next_step['SHOP_NAME']])
            ->orderBy('id')
            ->limit(2000)
            ->all();

        foreach ($dbElements as $element) {
            if (($used = YmlTree::find()->limit(1)->where(['parent_id' => 0, 'name' => 'used_id', 'lft' => $element->id])->one()) === null) {
                if ($element->delete()) {
                    $counter['DEL']++;
                }
            }

            $this->next_step['LAST_YML_ID'] = $element->id;
            if ($interval > 0 && (time() - $start_time) > $interval)
                break;
        }
        return $counter;
    }

    /**
     * @param $start_time
     * @param $interval
     * @return array
     */
    public function deleteProperties($start_time, $interval)
    {
        $counter = [
            'DPR' => 0,
        ];

        $arLoaded = YmlTree::find()->limit(2000)->where(['parent_id' => 0, 'name' => 'used_id'])->andWhere(['>', 'id', $this->next_step['LAST_YML_ID']])->orderBy('id')->all();

        foreach ($arLoaded as $loaded) {
            $arProperty = ReferenceElementProperty::find()->where(['element_id' => $loaded->lft])->all();
            foreach ($arProperty as $property) {
                if ($property->marker != $this->next_step['YMLHASH']) {
                    $property->delete();
                }
            }

            $this->next_step['LAST_YML_ID'] = $loaded->id;
            if ($interval > 0 && (time() - $start_time) > $interval)
                break;
        }

        return $counter;
    }

    /**
     * @param $arYMLElement
     * @return int
     */
    protected function getElementCRC($arYMLElement)
    {
        $c = crc32(print_r($arYMLElement, true));
        if ($c > 0x7FFFFFFF)
            $c = -(0xFFFFFFFF - $c + 1);
        return (string)$c;
    }

    /**
     * @param $arElement
     * @return string
     */
    protected function getElementMD($arElement)
    {
        $c = md5(print_r($arElement, true));
        return $c;
    }

    /**
     * @return array
     */
    public static function getFiles()
    {
        $files = FileHelper::findFiles(Yii::getAlias(static::$filePath), [
            'only' => [
                '*.xml',
            ],
        ]);
        sort($files);
        return $files;
    }

    /**
     * @param $xml_id
     * @param array $arProperty
     * @return bool
     */
    protected function checkReferenceProperty($xml_id, Array $arProperty)
    {
        if (!$property = ReferenceProperty::findOne(['reference_id' => $this->reference->id, 'xml_id' => $xml_id])) {
            $property = new ReferenceProperty;

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
     * @param ReferenceProperty $property
     * @param $value
     * @return ReferenceElement|array|bool|null
     */
    protected function checkPropertyValue(ReferenceProperty $property, $value)
    {
        switch ($property->type) {
            case ReferenceProperty::TYPE_STRING:
            case ReferenceProperty::TYPE_INTEGER:
                break;
            case ReferenceProperty::TYPE_LINK_ELEMENT:
                $xml_id = $property->xml_id . '_' . Utils::translit($value);
                $result = ReferenceElement::find()->limit(1)
                    ->where([
                        'reference_id' => $property->link_reference_id,
                        'xml_id'       => $xml_id,
                    ])->one();
                if ($result !== null)
                    return $result;
                else {
                    $result = new ReferenceElement(['reference_id' => $property->link_reference_id, 'name' => $value, 'xml_id' => $xml_id, 'reference_section_id' => 0, 'active' => 0]);
                    if ($result->save())
                        return $result;
                }

                break;
            case ReferenceProperty::TYPE_LINK_SECTION:
                break;
        }

        return false;
    }

    /**
     * @return array
     */
    protected function getProperties()
    {
        return [
            'yml_current_color'     => [
                'name'              => 'Исходный цвет',
                'type'              => 'LE',
                'code'              => 'current_color',
                'xml_id'            => 'yml_current_color',
                'link_reference_id' => 7,
                'multiple'          => 1,
            ],
            'yml_color'             => [
                'name'              => 'Цвет',
                'type'              => 'LS',
                'code'              => 'color',
                'xml_id'            => 'yml_color',
                'link_reference_id' => 7,
                'multiple'          => 1,
            ],
            'yml_current_dimension' => [
                'name'              => 'Исходный размер',
                'type'              => 'LE',
                'code'              => 'current_dimension',
                'xml_id'            => 'yml_current_dimension',
                'link_reference_id' => 8,
                'multiple'          => 1,
            ],
            'yml_dimension'         => [
                'name'              => 'Размер',
                'type'              => 'LS',
                'code'              => 'dimension',
                'xml_id'            => 'yml_dimension',
                'link_reference_id' => 8,
                'multiple'          => 1,
            ],
            'yml_current_material'  => [
                'name'              => 'Исходный материал',
                'type'              => 'LE',
                'code'              => 'current_material',
                'xml_id'            => 'yml_current_material',
                'link_reference_id' => 9,
                'multiple'          => 1,
            ],
            'yml_material'          => [
                'name'              => 'Maтериал',
                'type'              => 'LS',
                'code'              => 'material',
                'xml_id'            => 'yml_material',
                'link_reference_id' => 9,
                'multiple'          => 1,
            ],
            'yml_current_gender'    => [
                'name'              => 'Исходный пол',
                'type'              => 'LE',
                'code'              => 'current_gender',
                'xml_id'            => 'yml_current_gender',
                'link_reference_id' => 10,
            ],
            'yml_gender'            => [
                'name'              => 'Пол',
                'type'              => 'LS',
                'code'              => 'gender',
                'xml_id'            => 'yml_gender',
                'link_reference_id' => 10,
            ],
            'yml_current_seazon'    => [
                'name'              => 'Исходный сезон',
                'type'              => 'LE',
                'code'              => 'current_seazon',
                'xml_id'            => 'yml_current_seazon',
                'link_reference_id' => 11,
                'multiple'          => 1,
            ],
            'yml_seazon'            => [
                'name'              => 'Сезон',
                'type'              => 'LS',
                'code'              => 'seazon',
                'xml_id'            => 'yml_seazon',
                'link_reference_id' => 11,
                'multiple'          => 1,
            ],
        ];
    }
}