<?php

/**
 * Class ChatMsgModel
 * 
 * Manages chat messages
 */ 
class ChatMsgModel extends \Asatru\Database\Model {
    /**
     * @param $message
     * @return void
     * @throws \Exception
     */
    public static function addMessage($message)
    {
        try {
            $user = UserModel::getAuthUser();
            if (!$user) {
                throw new \Exception('Invalid user');
            }

            $message = trim($message);

            static::raw('INSERT INTO `@THIS` (userId, message) VALUES(?, ?)', [
                $user->get('id'), $message
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $limit
     * @param $api
     * @return mixed
     * @throws \Exception
     */
    public static function getChat($limit = 50, $api = false)
    {
        try {
            $result = static::raw('SELECT * FROM `@THIS` ORDER BY created_at DESC LIMIT ' . $limit);

            if (!$api) {
                if (count($result) > 0) {
                    UserModel::updateLastSeenMsg($result->get(0)->get('id'));

                    $lastsysmsg = static::raw('SELECT * FROM `@THIS` WHERE sysmsg = 1 ORDER BY created_at DESC')->first();
                    if ($lastsysmsg) {
                        UserModel::updateLastSeenSysMsg($lastsysmsg->get('id'));
                    }
                }
            }

            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function getLatestMessages()
    {
        try {
            $user = UserModel::getAuthUser();
            if (!$user) {
                throw new \Exception('Invalid user');
            }

            $result = static::raw('SELECT * FROM `@THIS` WHERE id > ? ORDER BY created_at DESC', [($user->get('last_seen_msg')) ? $user->get('last_seen_msg') : 0]);

            if (($result) && (count($result) > 0)) {
                UserModel::updateLastSeenMsg($result->get(0)->get('id'));
            }

            return $result;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param $limit
     * @return mixed
     * @throws \Exception
     */
    public static function getLatestSystemMessage($limit = 0)
    {
        try {
            $user = UserModel::getAuthUser();
            if (!$user) {
                throw new \Exception('Invalid user');
            }

            $limit_token = '';
            if ($limit > 0) {
                $limit_token = 'LIMIT ' . strval($limit);
            }

            $result = static::raw('SELECT * FROM `@THIS` WHERE sysmsg = 1 AND id > ? ORDER BY created_at DESC ' . $limit_token, [($user->get('last_seen_sysmsg')) ? $user->get('last_seen_sysmsg') : 0]);
            if (($result) && (count($result) > 0)) {
                $msg = $result->get(count($result) - 1);

                UserModel::updateLastSeenSysMsg($msg->get('id'));
                
                return $msg;
            }

            return null;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @return int
     * @throws \Exception
     */
    public static function getUnreadCount()
    {
        try {
            $user = UserModel::getAuthUser();
            if (!$user) {
                throw new \Exception('Invalid user');
            }

            $data = static::raw('SELECT COUNT(*) AS `count` FROM `@THIS` WHERE userId <> ? AND id > ? ORDER BY id ASC', [
                $user->get('id'), ($user->get('last_seen_msg')) ? $user->get('last_seen_msg') : 0
            ])->first();

            return $data->get('count');
        } catch (\Exception $e) {
            throw $e;
        }
    }
}