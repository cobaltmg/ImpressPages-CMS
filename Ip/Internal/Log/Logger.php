<?php

namespace Ip\Internal\Log;

class Logger extends \Psr\Log\AbstractLogger
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if (!is_string($message) && ipDb()->isConnected()) {
            // Probably programmer made a mistake, used Logger::log($message, $context)
            $row = array(
                'level' => \Psr\Log\LogLevel::ERROR,
                'message' => 'Code uses ipLog()->log() without giving $level info.',
                'context' => json_encode(array('args' => func_get_args())),
            );

            ipDb()->insert(ipDb()->tablePrefix() . 'log', $row);
            return;
        }

        $row = array(
            'level' => $level,
            'message' => $message,
            'context' => json_encode($context),
        );

        ipDb()->insert(ipDb()->tablePrefix() . 'log', $row);
    }
}