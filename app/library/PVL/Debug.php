<?php
namespace PVL;

class Debug
{
    static $echo_debug = false;
    static $debug_log = array();
    static $timers = array();

    static function setEchoMode($new_value = true)
    {
        self::$echo_debug = $new_value;
    }

    static function showErrors()
    {
        error_reporting(E_ALL & ~E_STRICT);
        ini_set('display_errors', 1);
    }

    // Logging
    static function log($entry)
    {
        $row = array('type' => 'log', 'message' => $entry);

        if (self::$echo_debug)
            self::display($row);

        self::$debug_log[] = $row;
    }

    static function print_r($item)
    {
        $row = array('type' => 'array', 'message' => $item);

        if (self::$echo_debug)
            self::display($row);

        self::$debug_log[] = $row;
    }

    static function divider()
    {
        $row = array('type' => 'divider');

        if (self::$echo_debug)
            self::display($row);

        self::$debug_log[] = $row;
    }

    static function display($info)
    {
        switch($info['type'])
        {
            case 'divider':
                if (DF_IS_COMMAND_LINE)
                {
                    echo '---------------------------------------------'."\n";
                }
                else
                {
                    echo '<div style="
                        padding: 3px;
                        background: #DDD;
                        border-left: 4px solid #DDD;
                        border-bottom: 1px solid #DDD;
                        margin: 0;"></div>';
                }
            break;

            case 'array':
                if (DF_IS_COMMAND_LINE)
                {
                    echo print_r($info['message'], TRUE);
                    echo "\n";
                }
                else
                {
                    echo '<pre style="
                        padding: 3px; 
                        font-family: Consolas, Courier New, Courier, monospace; 
                        font-size: 12px; 
                        background: #EEE; 
                        border-left: 4px solid #FFD24D; 
                        border-bottom: 1px solid #DDD;
                        margin: 0;">';

                    $message = print_r($info['message'], TRUE);
                    if ($message)
                        echo $message;
                    else
                        echo '&nbsp;';

                    echo '</pre>';
                }
            break;

            case 'log':
            default:
                if (DF_IS_COMMAND_LINE)
                {
                    echo $info['message']."\n";
                }
                else
                {
                    echo '<div style="
                        padding: 3px; 
                        font-family: Consolas, Courier New, Courier, monospace; 
                        font-size: 12px; 
                        background: #EEE; 
                        border-left: 4px solid #4DA6FF; 
                        border-bottom: 1px solid #DDD;
                        margin: 0;">';
                    echo $info['message'];
                    echo '</div>';
                }
            break;
        }
    }

    // Retrieval
    static function getLog()
    {
        return self::$debug_log;
    }

    static function printLog()
    {
        foreach(self::$debug_log as $log_row)
            self::display($log_row);
    }

    // Timers
    static function startTimer($timer_name)
    {
        self::$timers[$timer_name] = microtime(true);
    }
    static function endTimer($timer_name)
    {
        $start_time = (isset(self::$timers[$timer_name])) ? self::$timers[$timer_name] : microtime(true);
        $end_time = microtime(true);

        $time_diff = $end_time - $start_time;
        self::log('Timer "'.$timer_name.'" completed in '.round($time_diff, 3).' second(s).');

        unset(self::$timers[$timer_name]);
    }


}