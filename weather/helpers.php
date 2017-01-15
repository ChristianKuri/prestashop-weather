<?php

trait Helpers
{
    /**
     * This method installs the database.
     * Creates a table called weather_data
     * with an id, zip, data and date using
     * the mysql engine and the utf8 charset.
     * 
     * @return boolean
     */
    protected function installDb()
    {
        return (Db::getInstance()
            ->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'weather_data` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `zip` VARCHAR(5) NOT NULL,
                `data` TEXT NOT NULL,
                `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE = ' . _MYSQL_ENGINE_ . ' CHARACTER SET utf8 COLLATE utf8_general_ci;'));
    }

    /**
     * This method uninstalls the database.
     * Deletes a table called weather_data.
     * 
     * @return boolean
     */
    protected function uninstallDb()
    {
        return (Db::getInstance()
            ->execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'weather_data`'));
    }

    /**
     * This method gets data stored in the database for
     * certain zip code, but it takes in account the cache
     * value defined by the store admin to retrieve results.
     * 
     * @param  int $zip
     * @return object $data
     */
    protected function getData($zip)
    {
        $weather = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'weather_data WHERE date >= DATE_ADD(CURRENT_TIMESTAMP, INTERVAL -' . $this->getValue('cache') . ' MINUTE) AND zip = ' . $zip . ' ORDER BY date DESC');

        if ($weather && $weather['data'])
            return unserialize($weather['data']);
    }

    /**
     * This method serializes and stores the
     * data in the database for certain zip code.
     * 
     * @param int $zip
     * @param object $data
     */
    protected function setData($zip, $data)
    {
        Db::getInstance()->insert('weather_data', array(
            'zip' => $zip, 
            'data' => serialize($data),
        ));
    }

    /**
     * This method removes the old data from the database, to
     * determine which data is old, the store admin have defined
     * the amount of time that the data should be stored in the cache.
     * 
     * @return [type] [description]
     */
    protected function removeOldData()
    {
        Db::getInstance()->delete('weather_data', 'date < DATE_ADD(CURRENT_TIMESTAMP, INTERVAL -' . $this->getValue('cache') . ' MINUTE)');
    }

    /**
     * This method unserialices and gets the  
     * value of a key in the configuration class.
     * 
     * @param  string $key
     * @return string $weather_config[$key]
     */
    protected function getValue($key)
    {
        $weather_config = unserialize(Configuration::get('WEATHER'));
        return $weather_config[$key];
    }

    /**
     * This method unserialices the configutation values
     * and then stores the new value in the configuration class.
     * 
     * @param string $key
     * @param string $value
     */
    protected function setValue($key, $value)
    {
        $weather_config = unserialize(Configuration::get('WEATHER'));
        $weather_config[$key] = $value;
        return Configuration::updateValue('WEATHER', serialize($weather_config));
    }

    /**
     * This method sets the default 
     * values using the setValue method.
     * 
     * @return boolean
     */
    protected function defaultValues()
    {
        if ($this->setValue('cache', '30') && $this->setValue('zip', '15206'))
            return true;
        return false;
    }
}