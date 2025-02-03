<?php

/**
 * Class ChatViewModel
 * 
 * Handles the "seen" status of chat messages by users
 */ 
class ChatViewModel extends \Asatru\Database\Model {
    /**
     * @param $userId
     * @param $messageId
     * @return bool
     * @throws \Exception
     */
    public static function handleNewMessage($userId, $messageId)
    {
        try {
            $row = static::raw('SELECT * FROM `@THIS` WHERE userId = ? AND messageId = ?', [$userId, $messageId])->first();
            if (!$row) {
                static::raw('INSERT INTO `@THIS` (userId, messageId) VALUES(?, ?)', [
                    $userId, $messageId
                ]);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}