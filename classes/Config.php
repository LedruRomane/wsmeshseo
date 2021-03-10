<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Config extends ObjectModel
{
    public $id_wsmeshseo_config;
    public $value;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'ws_seo_configuration',
        'primary' => 'id_category',
        'multilang' => false,
        'fields' => array(
            'id_category' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'configuration' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
        ),
    );


    public function __construct($ws_seo_configuration = null, $id_lang = null, Context $context = null)
    {
        parent::__construct($ws_seo_configuration, $id_lang);

        /*
        $category = new Category($this->id_category, $id_lang);
        $this->performer = $category->name;
        */
    }

    public function update($null_values = false)
    {
        $return = parent::update($null_values);
        return $return;
    }

    public function add($autoDate = true, $nullValues = false)
    {
        $ret = parent::add($autoDate, $nullValues);
        return $ret;
    }

    public static function get($id_seo_configuration){
        $return = null;

        $sql = 'SELECT configuration ';
        $sql .= ' FROM `'._DB_PREFIX_.'ws_seo_configuration`';
        $sql .= ' WHERE id_category = \''.$id_seo_configuration.'\'';

        if ($results = Db::getInstance()->ExecuteS($sql)){
            foreach ($results as $row){
                $return = $row['configuration'];
            }
        }
        return $return;
    }

    public static function updateValue($id_seo_configuration, $value){

        $sql = 'REPLACE into `'._DB_PREFIX_.'ws_seo_configuration`';
        $sql .= ' (id_category, configuration) VALUES (\''.$id_seo_configuration.'\', \''.$value.'\')';

        $results = Db::getInstance()->Execute($sql);

        return $results;
    }
}
