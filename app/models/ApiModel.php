<?php

/**
 * Class ApiModel
 * 
 * Manage API keys that are required to access the REST API of a workspace
 */ 
class ApiModel extends \Asatru\Database\Model {
    /**
     * @return mixed
     * @throws \Exception
     */
    public static function getKeys()
    {
        try {
            return static::raw('SELECT * FROM `@THIS`');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    public static function addKey()
    {
        try {
            $key = md5(random_bytes(55) . date('Y-m-d H:i:s'));

            static::raw('INSERT INTO `@THIS` (token, active) VALUES(?, 1)', [$key]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $token
     * @return bool
     * @throws \Exception
     */
    public static function isValidKey($token)
    {
        try {
            $result = static::raw('SELECT * FROM `@THIS` WHERE token = ? AND active = 1', [$token])->first();
            if (($result) && ($result->get('token') === $token)) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $token
     * @return void
     * @throws \Exception
     */
    public static function validateKey($token)
    {
        try {
            $result = static::raw('SELECT * FROM `@THIS` WHERE token = ? AND active = 1', [$token])->first();
            if ((!$result) || ($result->get('token') !== $token)) {
                throw new \Exception('Invalid token: ' . $token);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public static function toggleApiKey($id)
    {
        try {
            $item = static::raw('SELECT * FROM `@THIS` WHERE id = ?', [$id])->first();
            if (!$item) {
                throw new \Exception('No item with id ' . $id . ' exists.');
            }

            static::raw('UPDATE `@THIS` SET active = NOT active WHERE id = ?', [$id]);

            return !(bool)$item->get('active');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $token
     * @return void
     * @throws \Exception
     */
    public static function removeKey($token)
    {
        try {
            static::raw('DELETE FROM `@THIS` WHERE token = ?', [$token]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}