<?php
/**
 * Created by PhpStorm.
 * User: VILDERR
 * Date: 27.03.17
 * Time: 9:49
 */

namespace app\components;

use Yii;
use yii\base\Model;
use app\models\reference\import\YmlTree;

/**
 * Class Yml
 * @package app\models\reference\import
 */
class Yml extends Model
{
    /**
     * @var bool
     */
    public $charset = false;
    /**
     * @var array
     */
    public $element_stack = [];
    /**
     * @var int
     */
    public $file_position = 0;
    /**
     * @var int
     */
    public $read_size = 10240;
    /**
     * @var string
     */
    public $buf = "";
    /**
     * @var int
     */
    public $buf_position = 0;
    /**
     * @var int
     */
    public $buf_len = 0;
    /**
     * @var string
     */
    private $_get_xml_chunk_function = "_get_xml_chunk";

    public function init()
    {
        parent::init();

        if (function_exists("mb_orig_strpos") && function_exists("mb_orig_strlen") && function_exists("mb_orig_substr")) {
            $this->_get_xml_chunk_function = "_get_xml_chunk_mb_orig";
        }
    }

    /**
     * check $file exists
     * check $file is YML format
     * @param $file string
     * @return bool
     */
    public static function checkFileIsYml($file, &$NS)
    {
        if (file_exists($file) && is_file($file)) {
            $fp = fopen($file, 'rb');
            if (!is_resource($fp))
                return false;

            $filesize = filesize($file);
            $NS['file_size'] = $filesize;

            $footerPos = $filesize - 1024;
            $header = fread($fp, 1024);
            fseek($fp, $footerPos);
            $footer = fread($fp, 1024);
            fclose($fp);

            if (
                preg_match("/<" . "\\?XML[^>]{1,}encoding=[\"']([^>\"']{1,})[\"'][^>]{0,}\\?" . ">/i", $header, $matches)
                && (strpos($header, "<yml_catalog") !== false)
                && (strpos($footer, "</yml_catalog") !== false)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * create temp tables
     */
    public static function createTables()
    {
        $connection = Yii::$app->db;
        $table_name = YmlTree::tableName();

        $connection->createCommand()
            ->createTable($table_name, [
                'id'        => 'pk',
                'parent_id' => 'integer(11)',
                'lft'       => 'integer(11)',
                'rgt'       => 'integer(11)',
                'depth'     => 'integer(11)',
                'name'      => 'string(255)',
                'value'     => 'text',
                'attrs'     => 'string',
            ])->query();

        $connection->createCommand()
            ->createIndex('idx-yml_import_file-parent', $table_name, 'parent_id')
            ->query();
        $connection->createCommand()
            ->createIndex('idx-yml_import_file-lft', $table_name, 'lft')
            ->query();

        return true;
    }

    /**
     * @param $fp
     * @param $NS
     * @param $time_limit
     * @param int $read_size
     * @return bool
     */
    public function readToBase($fp, &$NS, $time_limit, $read_size = 10240)
    {
        if (!array_key_exists("element_stack", $NS))
            $NS["element_stack"] = [];
        $this->element_stack = &$NS["element_stack"];

        if (!array_key_exists("file_position", $NS))
            $NS["file_position"] = 0;
        $this->file_position = &$NS["file_position"];

        $this->read_size = $read_size;
        $this->buf = "";
        $this->buf_position = 0;
        $this->buf_len = 0;

        if ($time_limit > 0)
            $end_time = time() + $time_limit;
        else
            $end_time = time() + 365 * 24 * 3600; // One year

        $_get_xml_chunk = [
            $this,
            $this->_get_xml_chunk_function,
        ];
        fseek($fp, $this->file_position);
        while (($xmlChunk = call_user_func_array($_get_xml_chunk, [$fp])) !== false) {
            if ($xmlChunk[0] == "/") {
                $this->_end_element($xmlChunk);
                if (time() > $end_time)
                    break;
            } elseif ($xmlChunk[0] == "!" || $xmlChunk[0] == "?") {
                if (substr($xmlChunk, 0, 4) === "?xml") {
                    if (preg_match('#encoding[\s]*=[\s]*"(.*?)"#i', $xmlChunk, $arMatch)) {
                        $this->charset = $arMatch[1];
                    }
                }
            } else {
                $this->_start_element($xmlChunk);
            }
        }

        return feof($fp);
    }

    /**
     * Internal function.
     * Used to read an xml by chunks started with "<" and endex with "<"
     * @param $fp
     * @return mixed
     */
    function _get_xml_chunk_mb_orig($fp)
    {
        if ($this->buf_position >= $this->buf_len) {
            if (!feof($fp)) {
                $this->buf = fread($fp, $this->read_size);
                $this->buf_position = 0;
                $this->buf_len = mb_orig_strlen($this->buf);
            } else
                return false;
        }

        //Skip line delimiters (ltrim)
        $xml_position = mb_orig_strpos($this->buf, "<", $this->buf_position);
        while ($xml_position === $this->buf_position) {
            $this->buf_position++;
            $this->file_position++;
            //Buffer ended with white space so we can refill it
            if ($this->buf_position >= $this->buf_len) {
                if (!feof($fp)) {
                    $this->buf = fread($fp, $this->read_size);
                    $this->buf_position = 0;
                    $this->buf_len = mb_orig_strlen($this->buf);
                } else
                    return false;
            }
            $xml_position = mb_orig_strpos($this->buf, "<", $this->buf_position);
        }

        //Let's find next line delimiter
        while ($xml_position === false) {
            $next_search = $this->buf_len;
            //Delimiter not in buffer so try to add more data to it
            if (!feof($fp)) {
                $this->buf .= fread($fp, $this->read_size);
                $this->buf_len = mb_orig_strlen($this->buf);
            } else
                break;

            //Let's find xml tag start
            $xml_position = mb_orig_strpos($this->buf, "<", $next_search);
        }

        if ($xml_position === false)
            $xml_position = $this->buf_len + 1;

        $len = $xml_position - $this->buf_position;
        $this->file_position += $len;
        $result = mb_orig_substr($this->buf, $this->buf_position, $len);
        $this->buf_position = $xml_position;

        return $result;
    }

    /**
     * Internal function.
     * Used to read an xml by chunks started with "<" and ended with "<"
     * @param $fp
     * @return bool
     */
    public function _get_xml_chunk($fp)
    {
        if ($this->buf_position >= $this->buf_len) {
            if (!feof($fp)) {
                $this->buf = fread($fp, $this->read_size);
                $this->buf_position = 0;
                $this->buf_len = strlen($this->buf);
            } else
                return false;
        }

        //Skip line delimiters (ltrim)
        $xml_position = strpos($this->buf, "<", $this->buf_position);
        while ($xml_position === $this->buf_position) {
            $this->buf_position++;
            $this->file_position++;

            //Buffer ended with white space so we can refill it
            if ($this->buf_position >= $this->buf_len) {
                if (!feof($fp)) {
                    $this->buf = fread($fp, $this->read_size);
                    $this->buf_position = 0;
                    $this->buf_len = strlen($this->buf);
                } else
                    return false;
            }
            $xml_position = strpos($this->buf, "<", $this->buf_position);
        }

        //Let's find next line delimiter
        while ($xml_position === false) {
            $next_search = $this->buf_len;
            //Delimiter not in buffer so try to add more data to it
            if (!feof($fp)) {
                $this->buf .= fread($fp, $this->read_size);
                $this->buf_len = strlen($this->buf);
            } else
                break;

            //Let's find xml tag start
            $xml_position = strpos($this->buf, "<", $next_search);
        }
        if ($xml_position === false)
            $xml_position = $this->buf_len + 1;

        $len = $xml_position - $this->buf_position;
        $this->file_position += $len;
        $result = substr($this->buf, $this->buf_position, $len);
        $this->buf_position = $xml_position;

        return $result;
    }

    /**
     * Internal function.
     * Stores an element into xml database tree.
     * @param $xmlChunk
     */
    public function _start_element($xmlChunk)
    {
        static $search = [
            "'&(quot|#34);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(amp|#38);'i",
        ];

        static $replace = [
            "\"",
            "<",
            ">",
            "&",
        ];

        $p = strpos($xmlChunk, ">");

        if ($p !== false) {
            if (substr($xmlChunk, $p - 1, 1) == "/") {
                $bHaveChildren = false;
                $elementName = substr($xmlChunk, 0, $p - 1);
                $DBelementValue = false;
            } else {
                $bHaveChildren = true;
                $elementName = substr($xmlChunk, 0, $p);
                $elementValue = substr($xmlChunk, $p + 1);
                if (preg_match("/^\s*$/", $elementValue))
                    $DBelementValue = false;
                elseif (strpos($elementValue, "&") === false)
                    $DBelementValue = $elementValue;
                else
                    $DBelementValue = preg_replace($search, $replace, $elementValue);
            }

            if (($ps = strpos($elementName, " ")) !== false) {
                $elementAttrs = substr($elementName, $ps + 1);
                $elementName = substr($elementName, 0, $ps);

                preg_match_all("/(\\S+)\\s*=\\s*[\"](.*?)[\"]/s", $elementAttrs, $attrs_tmp);
                $attrs = [];
                if (strpos($elementAttrs, "&") === false) {
                    foreach ($attrs_tmp[1] as $i => $attrs_tmp_1)
                        $attrs[$attrs_tmp_1] = $attrs_tmp[2][$i];
                } else {
                    foreach ($attrs_tmp[1] as $i => $attrs_tmp_1)
                        $attrs[$attrs_tmp_1] = preg_replace($search, $replace, $attrs_tmp[2][$i]);
                }
                $DBelementAttrs = serialize($attrs);
            } else
                $DBelementAttrs = false;

            if ($c = count($this->element_stack))
                $parent = $this->element_stack[$c - 1];
            else
                $parent = ["ID" => "NULL", "L" => 0, "R" => 1];

            $left = $parent["R"];
            $right = $left + 1;

            $arFields = [
                "parent_id" => $parent["ID"],
                "lft"       => $left,
                "rgt"       => $right,
                "depth"     => $c,
                "name"      => $elementName,
            ];

            if ($DBelementValue !== false) {
                $arFields["value"] = $DBelementValue;
            }
            if ($DBelementAttrs !== false) {
                $arFields["attrs"] = $DBelementAttrs;
            }

            $ID = $this->add($arFields);

            if ($bHaveChildren)
                $this->element_stack[] = ["ID" => $ID, "L" => $left, "R" => $right, "RO" => $right];
            else
                $this->element_stack[$c - 1]["R"] = $right + 1;
        }
    }

    /**
     * Internal function.
     * Winds tree stack back. Modifies (if neccessary) internal tree structure.
     * @param $xmlChunk
     */
    public function _end_element($xmlChunk)
    {
        $child = array_pop($this->element_stack);
        $this->element_stack[count($this->element_stack) - 1]["R"] = $child["R"] + 1;

        if ($child["R"] != $child["RO"]) {
            Yii::$app->db->createCommand(
                'UPDATE ' . YmlTree::tableName() . ' SET rgt = ' . intval($child["R"]) . ' WHERE id = ' . intval($child["ID"]),
                [
                    'enableQueryCache' => false,
                ]
            )->execute();
        }
    }

    /**
     * @param $arFields
     * @return int
     */
    public function add($arFields)
    {
        $element = new YmlTree();

        $element->attributes = $arFields;
        $element->save(false);

        return $element->id;
    }
}